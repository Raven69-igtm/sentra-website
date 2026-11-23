<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\UserMobile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PpaLaporanSyncTest extends TestCase
{
    use DatabaseTransactions;

    private function buatAdminPpa(): Akun
    {
        return Akun::create([
            'notelp' => '0811' . random_int(10000000, 99999999),
            'nama' => 'Admin PPA Uji',
            'email' => 'admin.ppa.' . uniqid() . '@contoh.test',
            'password' => Hash::make('rahasia123'),
            'role' => 'admin_ppa',
            'is_active' => true,
        ]);
    }

    private function buatUserMobile(): UserMobile
    {
        return UserMobile::create([
            'notelp' => '0812' . random_int(10000000, 99999999),
            'nama' => 'Pelapor PPA Uji',
            'email' => 'pelapor.ppa.' . uniqid() . '@contoh.test',
            'password' => Hash::make('rahasia123'),
            'role' => 'user',
            'is_active' => true,
        ]);
    }

    public function test_siklus_penuh_laporan_mobile_ke_web_admin_ppa_sinkron_dan_kirim_notifikasi(): void
    {
        $user = $this->buatUserMobile();
        $admin = $this->buatAdminPpa();

        // 1. Mobile mengirim laporan PPA lengkap (status dikirim, penerima berbeda tanpa NIK,
        //    telp 10 digit, agama 'Lainnya', jenis_kelamin 'Perempuan', anak parsial hanya Nama).
        Sanctum::actingAs($user, ['mobile']);
        $postResp = $this->postJson('/api/post-report', [
            'id_akun' => $user->id_akun,
            'kategori' => 'unset',
            'status' => 'dikirim',
            'detail_pelapor' => [
                'nik' => '3518000000001234',
                'nama' => 'Budi Pelapor',
                'alamat' => 'Jl. Melati No. 1',
                'hubungan_dengan_korban' => 'Orang tua',
                'no_telp' => '0812345678', // 10 digit
            ],
            'detail_penerima_manfaat' => [
                'nik' => null,
                'nama' => 'Siti Korban',
                'Tempat_lahir' => 'Nganjuk',
                'tanggal_lahir' => null,
                'umur' => null,
                'jenis_kelamin' => 'Perempuan',
                'pekerjaan' => 'Pelajar',
                'agama' => 'Lainnya',
                'alamat' => 'Jl. Melati No. 1',
                'pendidikan' => 'SMA',
                'hubungan_dengan_terlapor' => 'Tetangga',
                'notelp' => '0819876543', // 10 digit
                'informasi_tambahan' => null,
            ],
            'detail_terlapor' => [
                'nik' => null,
                'nama' => 'Andi Terlapor',
                'umur' => '35',
                'alamat' => 'Desa Sebelah',
                'jenis_kelamin' => 'Laki-laki',
                'hubungan_dengan_korban' => 'Tetangga',
                'informasi_tambahan' => null,
            ],
            'detail_kasus' => [
                'tanggal' => '2026-11-23',
                'waktu_kejadian' => '2026-11-23T14:30:00.000',
                'tempat_kejadian' => 'Desa Sukorejo, Nganjuk',
                'kronologi' => 'Kronologi uji sinkronisasi PPA mobile ke web admin.',
            ],
            'informasi_anak' => [
                [
                    'nama' => 'Citra Anak Parsial',
                    'tanggal_lahir' => null,
                    'umur' => null,
                    'jenis_kelamin' => null,
                    'pendidikan' => null,
                    'agama' => null,
                    'status' => null,
                ],
            ],
        ])->assertStatus(200)->assertJsonPath('success', true);

        $idLaporan = $postResp->json('data.laporan.id_laporan');
        $this->assertNotEmpty($idLaporan);
        // Pastikan notelp penerima manfaat terdekripsi (bukan ciphertext)
        $this->assertSame('0819876543', $postResp->json('data.detail_penerima.notelp'));

        // 2. Web Admin PPA melihat laporan berstatus 'dikirim' di /laporan dan /laporan/{id}/edit
        $this->actingAs($admin);
        $this->get('/laporan?status=dikirim')
            ->assertOk()
            ->assertSee($idLaporan)
            ->assertSee('Terima');

        $this->get("/laporan/{$idLaporan}/edit")
            ->assertOk()
            ->assertSee('0819876543') // notelp terdekripsi
            ->assertSee('Citra Anak Parsial')
            ->assertSee('value="Perempuan"', false)
            ->assertSee('value="Lainnya"', false);

        // 3. Admin PPA klik Terima (dikirim -> diterima) -> Notifikasi user tercipta
        $this->putJson("/laporan/{$idLaporan}/terima", [], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('status', 'diterima');

        $this->assertDatabaseHas('laporan', [
            'id_laporan' => $idLaporan,
            'status' => 'diterima',
        ]);
        $this->assertTrue(
            Notifikasi::where('id_akun', $user->id_akun)
                ->where('tipe', 'user')
                ->where('judul', "{$idLaporan} - Laporan Diterima")
                ->exists()
        );

        // 4. Admin PPA menolak kategori Dinsos ('Penelantaran') pada update PPA
        $this->put("/laporan/{$idLaporan}", [
            'kategori' => 'Penelantaran',
            'pelapor' => [
                'nik' => '3518000000001234',
                'nama' => 'Budi Pelapor',
                'alamat' => 'Jl. Melati No. 1',
                'hubungan' => 'Orang tua',
                'telepon' => '0812345678',
            ],
            'kasus' => [
                'tanggal' => '2026-11-23',
                'waktu' => '14:30',
                'tempat' => 'Desa Sukorejo, Nganjuk',
                'kronologi' => 'Kronologi uji',
            ],
        ])->assertSessionHasErrors('kategori');

        // 5. Admin PPA proses & simpan edit dengan anak parsial -> status diproses, anak TIDAK terhapus
        $laporanModel = Laporan::with('detail_penerima_manfaat.informasi_anak')->findOrFail($idLaporan);
        $idAnak = $laporanModel->detail_penerima_manfaat->informasi_anak->first()->id_anak;

        $this->put("/laporan/{$idLaporan}", [
            'anak_sync' => '1',
            'kategori' => 'Kekerasan Psikis',
            'pelapor' => [
                'nik' => '3518000000001234',
                'nama' => 'Budi Pelapor',
                'alamat' => 'Jl. Melati No. 1',
                'hubungan' => 'Orang tua',
                'telepon' => '0812345678',
            ],
            'penerima' => [
                'nik' => '',
                'nama' => 'Siti Korban',
                'ttl' => 'Nganjuk',
                'tanggal' => '',
                'umur' => '',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Pelajar',
                'agama' => 'Lainnya',
                'pendidikan' => 'SMA',
                'alamat' => 'Jl. Melati No. 1',
                'hubungan_terlapor' => 'Tetangga',
                'telepon' => '0819876543',
                'informasi_tambahan' => '',
            ],
            'terlapor' => [
                'nik' => '',
                'nama' => 'Andi Terlapor',
                'umur' => '35',
                'alamat' => 'Desa Sebelah',
                'jk' => 'Laki-laki',
                'hubungan' => 'Tetangga',
                'informasi_tambahan' => '',
            ],
            'anak' => [
                [
                    'id' => $idAnak,
                    'nama' => 'Citra Anak Parsial',
                    'tanggal' => '',
                    'umur' => '',
                    'jenis_kelamin' => '',
                    'pendidikan' => '',
                    'agama' => '',
                    'status' => '',
                ],
            ],
            'kasus' => [
                'tanggal' => '2026-11-23',
                'waktu' => '14:30',
                'tempat' => 'Desa Sukorejo, Nganjuk',
                'kronologi' => 'Kronologi uji sinkronisasi PPA mobile ke web admin.',
            ],
        ])->assertRedirect(route('laporan_proses'));

        $laporanModel->refresh()->load('detail_penerima_manfaat.informasi_anak');
        $this->assertSame('diproses', $laporanModel->status);
        $this->assertSame('Kekerasan Psikis', $laporanModel->kategori);
        $this->assertCount(1, $laporanModel->detail_penerima_manfaat->informasi_anak);
        $this->assertSame('Citra Anak Parsial', $laporanModel->detail_penerima_manfaat->informasi_anak->first()->nama);
        $this->assertTrue(
            Notifikasi::where('id_akun', $user->id_akun)
                ->where('tipe', 'user')
                ->where('judul', "{$idLaporan} - Laporan Dalam Proses")
                ->exists()
        );

        // 6. Admin PPA klik Rujuk (diproses -> dirujuk) -> tetap di /laporan, belum di /laporan_selesai, notif user tercipta
        $this->putJson("/laporan/{$idLaporan}/rujuk", [], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('laporan', [
            'id_laporan' => $idLaporan,
            'status' => 'dirujuk',
        ]);
        $this->assertTrue(
            Notifikasi::where('id_akun', $user->id_akun)
                ->where('tipe', 'user')
                ->where('judul', "{$idLaporan} - Laporan Dirujuk")
                ->exists()
        );

        // Laporan dirujuk tetap tampil di daftar aktif /laporan & belum masuk koleksi /laporan_selesai
        $this->get('/laporan?status=dirujuk')
            ->assertOk()
            ->assertViewHas('laporan', fn ($items) => $items->contains('id_laporan', $idLaporan))
            ->assertSee($idLaporan);
        $this->get('/laporan_selesai')
            ->assertOk()
            ->assertViewHas('laporan', fn ($items) => ! $items->contains('id_laporan', $idLaporan));

        // 7. Admin PPA menyelesaikan laporan yang sudah dirujuk (dirujuk -> selesai) -> pindah ke /laporan_selesai + notif
        $this->putJson("/laporan/{$idLaporan}/selesai", [], ['X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('laporan', [
            'id_laporan' => $idLaporan,
            'status' => 'selesai',
        ]);
        $this->assertTrue(
            Notifikasi::where('id_akun', $user->id_akun)
                ->where('tipe', 'user')
                ->where('judul', "{$idLaporan} - Laporan Selesai")
                ->exists()
        );
        $this->get('/laporan')
            ->assertOk()
            ->assertViewHas('laporan', fn ($items) => ! $items->contains('id_laporan', $idLaporan));
        $this->get('/laporan_selesai')
            ->assertOk()
            ->assertViewHas('laporan', fn ($items) => $items->contains('id_laporan', $idLaporan))
            ->assertSee($idLaporan);
    }

    public function test_laporan_cepat_anonim_bisa_diterima_tanpa_kirim_notifikasi_ke_guest(): void
    {
        $admin = $this->buatAdminPpa();

        $resp = $this->postJson('/api/laporan-cepat', [
            'nik' => '3518000000009999',
            'nama' => 'Pelapor Cepat Anonim',
            'no_telp' => '0812345678',
            'alamat' => 'Jl. Sudirman Nganjuk',
            'deskripsi' => 'Laporan cepat darurat tanpa login untuk uji tindak lanjut admin PPA.',
        ])->assertStatus(201)->assertJsonPath('success', true);

        $idLaporan = $resp->json('data.id_laporan');
        $laporan = Laporan::with('akun')->findOrFail($idLaporan);
        $this->assertTrue($laporan->isAnonim());

        $this->actingAs($admin);

        // Detail laporan cepat menampilkan label anonim & tombol Terima
        $this->get("/laporan-cepat/{$idLaporan}")
            ->assertOk()
            ->assertSee('Pelapor Anonim')
            ->assertSee('Terima Laporan');

        $notifBefore = Notifikasi::where('id_akun', $laporan->id_akun)->where('tipe', 'user')->count();

        $this->put("/laporan/{$idLaporan}/terima")->assertRedirect();

        $this->assertDatabaseHas('laporan', [
            'id_laporan' => $idLaporan,
            'status' => 'diterima',
        ]);

        // Akun guest anonim tidak menerima notifikasi user
        $notifAfter = Notifikasi::where('id_akun', $laporan->id_akun)->where('tipe', 'user')->count();
        $this->assertSame($notifBefore, $notifAfter);
    }

    public function test_riwayat_dan_pencarian_mobile_memisahkan_scope_ppa_dan_pjsrk(): void
    {
        $user = $this->buatUserMobile();
        Sanctum::actingAs($user, ['mobile']);

        // 1. Buat 1 laporan PPA (layanan_pjsrk = null)
        $ppaResp = $this->postJson('/api/post-report', [
            'id_akun' => $user->id_akun,
            'kategori' => 'Kekerasan Fisik',
            'status' => 'dikirim',
            'detail_pelapor' => [
                'nik' => '3518000000001111',
                'nama' => 'Pelapor Scope PPA',
                'alamat' => 'Jl. Mawar No. 2',
                'hubungan_dengan_korban' => 'Orang tua',
                'no_telp' => '0812345678',
            ],
            'detail_penerima_manfaat' => [
                'nama' => 'Korban Scope PPA',
                'Tempat_lahir' => 'Nganjuk',
                'jenis_kelamin' => 'Perempuan',
            ],
            'detail_kasus' => [
                'tanggal' => '2026-11-23',
                'waktu_kejadian' => '10:00:00',
                'tempat_kejadian' => 'Kecamatan Nganjuk',
                'kronologi' => 'Uji pemisahan riwayat PPA.',
            ],
        ])->assertStatus(200);
        $idPpa = $ppaResp->json('data.laporan.id_laporan');

        // 2. Buat 1 laporan PJSRK (layanan_pjsrk = rehabilitasi_sosial)
        $pjsrkResp = $this->postJson('/api/pjsrk/cepat', [
            'id_akun' => $user->id_akun,
            'layanan' => 'rehabilitasi_sosial',
            'nama' => 'Pelapor Scope PJSRK',
            'telepon' => '081234567890',
            'lokasi' => 'Desa Bagor, Nganjuk',
            'jenis' => 'Tunawisma',
            'deskripsi' => 'Uji pemisahan riwayat PJSRK agar tidak masuk ke PPA.',
        ])->assertStatus(201);
        $idPjsrk = $pjsrkResp->json('data.id_laporan');
        $statusPjsrk = Laporan::where('id_laporan', $idPjsrk)->value('status');

        // 3. GET /api/user-reports/{id}?scope=ppa hanya kembalikan laporan PPA
        $ppaList = collect(
            $this->getJson("/api/user-reports/{$user->id_akun}?scope=ppa")
                ->assertOk()
                ->json('laporan')
        )->pluck('id_laporan')->all();
        $this->assertContains($idPpa, $ppaList);
        $this->assertNotContains($idPjsrk, $ppaList);

        // 4. GET /api/user-reports/{id}?scope=pjsrk hanya kembalikan laporan PJSRK
        $pjsrkList = collect(
            $this->getJson("/api/user-reports/{$user->id_akun}?scope=pjsrk")
                ->assertOk()
                ->json('laporan')
        )->pluck('id_laporan')->all();
        $this->assertContains($idPjsrk, $pjsrkList);
        $this->assertNotContains($idPpa, $pjsrkList);

        // 5. GET /api/user-reports-by-category/{id}/{status}?scope=ppa|pjsrk
        $ppaByStatus = collect(
            $this->getJson("/api/user-reports-by-category/{$user->id_akun}/dikirim?scope=ppa")
                ->assertOk()
                ->json('laporan')
        )->pluck('id_laporan')->all();
        $this->assertContains($idPpa, $ppaByStatus);
        $this->assertNotContains($idPjsrk, $ppaByStatus);

        $pjsrkByStatus = collect(
            $this->getJson("/api/user-reports-by-category/{$user->id_akun}/{$statusPjsrk}?scope=pjsrk")
                ->assertOk()
                ->json('laporan')
        )->pluck('id_laporan')->all();
        $this->assertContains($idPjsrk, $pjsrkByStatus);
        $this->assertNotContains($idPpa, $pjsrkByStatus);

        // 6. GET /api/search dengan ?scope=ppa tidak mengembalikan ID laporan PJSRK
        $searchPpa = collect(
            $this->getJson("/api/search?keyword={$idPjsrk}&id_akun={$user->id_akun}&scope=ppa")
                ->assertOk()
                ->json()
        )->pluck('id_laporan')->all();
        $this->assertNotContains($idPjsrk, $searchPpa);

        $searchPjsrk = collect(
            $this->getJson("/api/search?keyword={$idPjsrk}&id_akun={$user->id_akun}&scope=pjsrk")
                ->assertOk()
                ->json()
        )->pluck('id_laporan')->all();
        $this->assertContains($idPjsrk, $searchPjsrk);

        // 7. Tanpa ?scope tetap backward-compatible (mengembalikan keduanya)
        $allList = collect(
            $this->getJson("/api/user-reports/{$user->id_akun}")
                ->assertOk()
                ->json('laporan')
        )->pluck('id_laporan')->all();
        $this->assertContains($idPpa, $allList);
        $this->assertContains($idPjsrk, $allList);
    }
}
