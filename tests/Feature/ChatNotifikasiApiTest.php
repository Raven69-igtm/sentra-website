<?php

namespace Tests\Feature;

use App\Models\Akun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatNotifikasiApiTest extends TestCase
{
    use DatabaseTransactions;

    private function buatAkun(?string $nama = null): Akun
    {
        return Akun::create([
            'nama' => $nama ?? 'Uji Api ' . uniqid(),
            'notelp' => '08' . random_int(1000000000, 9999999999),
            'password' => 'rahasia-uji-123',
            'role' => 'user',
            'is_active' => true,
        ]);
    }

    private function adminPpa(): Akun
    {
        return Akun::findOrFail(15);
    }

    /**
     * Trigger laporan membuat id_laporan dan notifikasi otomatis, jadi id
     * dibaca ulang setelah insert.
     */
    private function buatLaporan(Akun $akun, ?string $layananPjsrk): string
    {
        $createdAt = now();
        $penanda = 'Kecamatan Uji ' . uniqid('', true);

        DB::table('laporan')->insert([
            'id_akun' => $akun->id_akun,
            'judul' => null,
            'kecamatan' => $penanda,
            'kategori' => 'Kekerasan Fisik',
            'layanan_pjsrk' => $layananPjsrk,
            'status' => 'dikirim',
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        return DB::table('laporan')
            ->where('kecamatan', $penanda)
            ->value('id_laporan');
    }

    private function notifUserDariLaporan(string $idLaporan, Akun $akun)
    {
        return DB::table('notifikasi')
            ->where('judul', 'like', $idLaporan . ' - %')
            ->where('id_akun', $akun->id_akun)
            ->first();
    }

    private function notifDariLaporan(string $idLaporan)
    {
        return DB::table('notifikasi')
            ->where('judul', 'like', $idLaporan . ' - %')
            ->get();
    }

    public function test_notifikasi_ppa_dan_pjsrk_tampil_dalam_satu_daftar(): void
    {
        $user = $this->buatAkun();
        $idPpa = $this->buatLaporan($user, null);
        $idPjsrk = $this->buatLaporan($user, 'permasalahan_sosial');

        DB::table('chats')->insert([
            'sender_id' => $this->adminPpa()->id_akun,
            'receiver_id' => $user->id_akun,
            'message' => 'Halo, ada yang bisa dibantu?',
            'sent_at' => now(),
        ]);

        Sanctum::actingAs($user, ['mobile']);

        $response = $this->getJson('/api/notifications?user_id=' . $user->id_akun);
        $response->assertOk();
        $response->assertJsonPath('success', true);

        $data = collect($response->json('data'));

        $ppa = $data->firstWhere('id_laporan', $idPpa);
        $pjsrk = $data->firstWhere('id_laporan', $idPjsrk);
        $pesan = $data->firstWhere('judul', 'Pesan Baru');

        $this->assertNotNull($ppa, 'Notifikasi PPA tidak ada di daftar.');
        $this->assertSame('PPA', $ppa['source']);
        $this->assertNotNull($pjsrk, 'Notifikasi PJSRK tidak ada di daftar.');
        $this->assertSame('PJSRK', $pjsrk['source']);
        $this->assertNotNull($pesan, 'Notifikasi pesan chat tidak ada di daftar.');
        $this->assertSame('Pesan', $pesan['source']);
    }

    public function test_mark_read_dan_restore_notifikasi(): void
    {
        $user = $this->buatAkun();
        $idLaporan = $this->buatLaporan($user, null);
        $notif = $this->notifUserDariLaporan($idLaporan, $user);

        Sanctum::actingAs($user, ['mobile']);

        $this->patchJson("/api/notifications/{$notif->id_notif}/read")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame('dibaca', DB::table('notifikasi')
            ->where('id_notif', $notif->id_notif)
            ->value('status'));
    }

    public function test_hapus_lalu_pulihkan_notifikasi_dengan_id_sama(): void
    {
        $user = $this->buatAkun();
        $idLaporan = $this->buatLaporan($user, null);
        $notif = $this->notifUserDariLaporan($idLaporan, $user);

        Sanctum::actingAs($user, ['mobile']);

        $this->deleteJson("/api/notifications/{$notif->id_notif}")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertNull(DB::table('notifikasi')->where('id_notif', $notif->id_notif)->first());

        $this->postJson('/api/notifications/restore', [
            'id_notif' => $notif->id_notif,
            'id_akun' => $user->id_akun,
            'judul' => $notif->judul,
            'pesan' => $notif->pesan,
            'tipe' => $notif->tipe,
            'status' => 'dibaca',
        ])->assertOk()->assertJsonPath('success', true);

        $dipulihkan = DB::table('notifikasi')->where('id_notif', $notif->id_notif)->first();
        $this->assertNotNull($dipulihkan);
        $this->assertSame('dibaca', $dipulihkan->status);
        $this->assertSame($notif->judul, $dipulihkan->judul);
    }

    public function test_tidak_bisa_melihat_notifikasi_akun_lain(): void
    {
        $pemilik = $this->buatAkun();
        $lain = $this->buatAkun();

        Sanctum::actingAs($lain, ['mobile']);

        $this->getJson('/api/notifications?user_id=' . $pemilik->id_akun)
            ->assertStatus(403);
    }

    public function test_chat_memakai_identitas_dari_token(): void
    {
        $pengirim = $this->buatAkun();
        $admin = $this->adminPpa();

        Sanctum::actingAs($pengirim, ['mobile']);

        $response = $this->postJson('/api/chats', [
            'sender_id' => $admin->id_akun,
            'receiver_id' => $admin->id_akun,
            'message' => 'Halo Admin PPA',
        ]);

        $response->assertStatus(201);

        $chat = DB::table('chats')
            ->where('message', 'Halo Admin PPA')
            ->first();

        $this->assertNotNull($chat);
        $this->assertSame($pengirim->id_akun, (int) $chat->sender_id);
        $this->assertSame($admin->id_akun, (int) $chat->receiver_id);

        $this->assertDatabaseHas('notifikasi', [
            'id_akun' => $admin->id_akun,
            'judul' => 'Pesan Baru',
            // Penerima akun admin → notifikasi harus bertipe 'admin' agar
            // muncul di lonceng panel admin.
            'tipe' => 'admin',
        ]);
    }

    public function test_chat_tanpa_token_dan_tanpa_sender_ditolak(): void
    {
        $this->postJson('/api/chats', [
            'receiver_id' => $this->adminPpa()->id_akun,
            'message' => 'Pesan tanpa identitas',
        ])->assertStatus(401);
    }

    public function test_login_mobile_mengembalikan_token_sanctum(): void
    {
        $notelp = '0812' . random_int(1000000, 9999999);

        $this->postJson('/api/auth/register', [
            'notelp' => $notelp,
            'nama' => 'Uji Login Token',
            'password' => 'rahasia-uji',
        ])->assertStatus(201);

        $login = $this->postJson('/api/auth/login', [
            'notelp' => $notelp,
            'password' => 'rahasia-uji',
        ]);

        $login->assertOk();
        $login->assertJsonPath('message', 'Login berhasil');

        $token = $login->json('token');
        $idAkun = $login->json('user.id_akun');

        $this->assertIsString($token);
        $this->assertStringContainsString('|', $token);

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id_akun', $idAkun);
    }

    public function test_report_update_menyimpan_notifikasi_status(): void
    {
        $user = $this->buatAkun();
        $idLaporan = $this->buatLaporan($user, null);

        Sanctum::actingAs($this->adminPpa());

        $response = $this->postJson('/api/report-update', [
            'id_laporan' => $idLaporan,
            'user_id' => $user->id_akun,
            'status' => 'diproses',
        ]);

        $response->assertOk();
        $response->assertJsonPath('message', 'Notifikasi laporan dikirim');

        $this->assertDatabaseHas('notifikasi', [
            'id_akun' => $user->id_akun,
            'judul' => $idLaporan . ' - Laporan Dalam Proses',
            'tipe' => 'user',
            'status' => 'terkirim',
        ]);
    }
}
