<?php

namespace Tests\Feature;

use App\Models\Akun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LaporanPpaRestoreVerifyTest extends TestCase
{
    use DatabaseTransactions;

    private function buatAkun(): Akun
    {
        return Akun::create([
            'nama' => 'Uji Restore ' . uniqid(),
            'notelp' => '08' . random_int(1000000000, 9999999999),
            'password' => 'rahasia-uji-123',
            'role' => 'user',
            'is_active' => true,
        ]);
    }

    private function payloadAsli(
        string $penanda,
        string $hubungan = 'Pelapor / Korban'
    ): array {
        return [
            'id_akun' => $this->buatAkun()->id_akun,
            'kategori' => 'unset',
            'status' => 'diterima',
            'detail_pelapor' => [
                'nik' => '3201010101010001',
                'nama' => 'Pelapor Uji',
                'alamat' => 'Alamat Uji',
                'hubungan_dengan_korban' => $hubungan,
                'no_telp' => '081234567890',
            ],
            'detail_terlapor' => [
                'nik' => '-',
                'nama' => 'Dalam Penyelidikan',
                'umur' => '0',
                'alamat' => '-',
                'jenis_kelamin' => 'Laki-laki',
                'hubungan_dengan_korban' => '-',
                'informasi_tambahan' => '-',
            ],
            'detail_penerima_manfaat' => [
                'nik' => '3201010101010001',
                'nama' => 'Pelapor Uji',
                'Tempat_lahir' => '-',
                'tanggal_lahir' => '2000-01-01',
                'umur' => '0',
                'jenis_kelamin' => 'Laki-laki',
                'pekerjaan' => '-',
                'agama' => 'Islam',
                'alamat' => 'Alamat Uji',
                'pendidikan' => 'Tidak Sekolah',
                'hubungan_dengan_terlapor' => '-',
                'notelp' => '081234567890',
                'informasi_tambahan' => '-',
            ],
            'detail_kasus' => [
                'tanggal' => '2026-11-16',
                'waktu_kejadian' => '2026-11-16T10:30:00',
                'tempat_kejadian' => $penanda,
                'kronologi' => 'Kronologi uji pemulihan.',
            ],
            'informasi_anak' => [],
        ];
    }

    public function test_payload_form_ppa_asli_tersimpan_seperti_sebelum_qoder(): void
    {
        $penanda = 'Kecamatan Restore ' . uniqid();

        $response = $this->postJson('/api/post-report', $this->payloadAsli($penanda));
        $response->assertStatus(200)->assertJsonPath('success', true);

        $laporan = DB::table('laporan')->where('kecamatan', $penanda)->first();
        $this->assertNotNull($laporan, 'Laporan tidak tersimpan');
        $this->assertSame('diterima', $laporan->status, 'Status laporan harus mengikuti payload asli');
        $this->assertSame('unset', $laporan->kategori);

        $pelapor = DB::table('detail_pelapor')->where('id_laporan', $laporan->id_laporan)->first();
        $this->assertNotNull($pelapor, 'detail_pelapor tidak tersimpan');
        $this->assertSame('Pelapor / Korban', $pelapor->hubungan_dengan_korban);

        $penerima = DB::table('detail_penerima_manfaat')->where('id_laporan', $laporan->id_laporan)->first();
        $this->assertNotNull($penerima, 'detail_penerima_manfaat tidak tersimpan');

        $kasus = DB::table('detail_kasus')->where('id_laporan', $laporan->id_laporan)->first();
        $this->assertNotNull($kasus, 'detail_kasus tidak tersimpan');
        $this->assertSame('2026-11-16', $kasus->tanggal);
        $this->assertNull($kasus->waktu_kejadian, 'Kolom waktu_kejadian dibiarkan kosong seperti perilaku awal');
    }

    public function test_hubungan_dengan_korban_dari_pilihan_pengguna_tersimpan(): void
    {
        $penanda = 'Kecamatan Hubungan ' . uniqid();

        $this->postJson('/api/post-report', $this->payloadAsli($penanda, 'Orang tua'))
            ->assertStatus(200)
            ->assertJsonPath('success', true);

        $laporan = DB::table('laporan')->where('kecamatan', $penanda)->first();
        $this->assertNotNull($laporan, 'Laporan tidak tersimpan');

        $hubungan = DB::table('detail_pelapor')
            ->where('id_laporan', $laporan->id_laporan)
            ->value('hubungan_dengan_korban');
        $this->assertSame('Orang tua', $hubungan);
    }

    public function test_admin_ppa_masih_bisa_membuka_daftar_dan_preview(): void
    {
        $admin = Akun::findOrFail(15);

        $this->actingAs($admin)->get('/laporan')->assertOk();

        $idLaporan = DB::table('laporan')
            ->where('kategori', 'Kekerasan Seksual')
            ->orderByDesc('created_at')
            ->value('id_laporan');

        $this->assertNotNull($idLaporan, 'Butuh satu laporan PPA untuk uji preview');
        $this->actingAs($admin)->get('/laporan/preview/' . $idLaporan)->assertOk();
    }
}
