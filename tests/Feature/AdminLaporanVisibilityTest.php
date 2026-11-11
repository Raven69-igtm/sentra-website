<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Laporan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminLaporanVisibilityTest extends TestCase
{
    use DatabaseTransactions;

    private function adminPPA(): Akun
    {
        return Akun::findOrFail(15);
    }

    private function adminDinsos(): Akun
    {
        return Akun::findOrFail(18);
    }

    private function buatLaporan(array $atribut): string
    {
        $atribut = array_merge([
            'id_akun' => 21,
            'judul' => 'TEST-'.uniqid(),
            'kecamatan' => 'Kecamatan Uji',
            'kategori' => 'unset',
            'layanan_pjsrk' => null,
            'status' => 'dikirim',
            'created_at' => now(),
            'updated_at' => now(),
        ], $atribut);

        DB::table('laporan')->insert($atribut);

        $id = DB::table('laporan')->where('judul', $atribut['judul'])->value('id_laporan');

        DB::table('detail_kasus')->insert([
            'id_laporan' => $id,
            'tanggal' => '2026-11-11',
            'waktu_kejadian' => '09:15:00',
            'tempat_kejadian' => 'Kecamatan Uji',
            'kronologi' => 'Kronologi uji coba.',
        ]);

        return $id;
    }

    public function test_scope_ppa_mencakup_laporan_mobile_dan_tidak_bocor_ke_dinsos(): void
    {
        $this->assertTrue(
            Laporan::forUser($this->adminPPA())->where('id_laporan', 'RKN370258556')->exists()
        );

        $this->assertFalse(
            Laporan::forUser($this->adminDinsos())->where('id_laporan', 'RKN370258556')->exists()
        );
    }

    public function test_scope_ppa_tidak_mengambil_laporan_pjsrk(): void
    {
        $id = $this->buatLaporan([
            'judul' => 'TEST-PJSRK-'.uniqid(),
            'kategori' => 'Penelantaran',
            'layanan_pjsrk' => 'rehabilitasi_sosial',
        ]);

        $this->assertFalse(
            Laporan::forUser($this->adminPPA())->where('id_laporan', $id)->exists()
        );

        $this->actingAs($this->adminPPA())
            ->get('/laporan')
            ->assertOk()
            ->assertDontSee($id);

        $this->assertTrue(
            Laporan::forUser($this->adminDinsos())->where('id_laporan', $id)->exists()
        );
    }

    public function test_laporan_mobile_muncul_di_daftar_tanpa_mengubah_status(): void
    {
        $response = $this->actingAs($this->adminPPA())->get('/laporan');

        $response->assertOk();
        $response->assertSee('RKN370258556');
        $this->assertSame('dikirim', Laporan::find('RKN370258556')->status);
    }

    public function test_detail_laporan_mobile_menampilkan_waktu_kejadian(): void
    {
        $response = $this->actingAs($this->adminPPA())->get('/laporan/RKN370258556');

        $response->assertOk();
        $response->assertSee('Waktu Kejadian');
        $response->assertSee('11:10');
        $response->assertSee('DIKI DI KEROYOK');
    }

    public function test_laporan_mobile_dapat_diproses_tanpa_data_pelapor(): void
    {
        $judul = 'TEST-MOBILE-'.uniqid();
        $id = $this->buatLaporan(['id_akun' => 21, 'judul' => $judul]);

        $this->actingAs($this->adminPPA())
            ->get("/laporan/{$id}/edit")
            ->assertOk()
            ->assertSee('Laporan mobile tidak menyertakan data pelapor');

        $response = $this->actingAs($this->adminPPA())->put("/laporan/{$id}", [
            'kategori' => 'unset',
            'judul' => $judul,
            'kasus' => [
                'tanggal' => '2026-11-11',
                'tempat' => 'Kecamatan Uji',
                'kronologi' => 'Kronologi uji coba tanpa pelapor.',
            ],
        ]);

        $response->assertRedirect(route('laporan_proses'));
        $response->assertSessionHas('success');
        $this->assertSame('diproses', Laporan::find($id)->status);
        $this->assertDatabaseMissing('detail_pelapor', ['id_laporan' => $id]);
    }

    public function test_laporan_website_normal_tetap_wajib_pelapor_dan_dapat_diproses(): void
    {
        $judul = 'TEST-WEBSITE-'.uniqid();
        $id = $this->buatLaporan([
            'judul' => $judul,
            'kategori' => 'Kekerasan Fisik',
            'layanan_pjsrk' => null,
        ]);

        $this->actingAs($this->adminPPA())
            ->get('/laporan')
            ->assertOk()
            ->assertSee($id);

        $this->actingAs($this->adminPPA())
            ->put("/laporan/{$id}", [
                'kategori' => 'Kekerasan Fisik',
                'judul' => $judul,
                'kasus' => [
                    'tanggal' => '2026-11-11',
                    'tempat' => 'Kecamatan Uji',
                    'kronologi' => 'Kronologi uji coba.',
                ],
            ])
            ->assertSessionHas('error');

        $this->assertSame('dikirim', Laporan::find($id)->status);

        $this->actingAs($this->adminPPA())
            ->put("/laporan/{$id}", [
                'kategori' => 'Kekerasan Fisik',
                'judul' => $judul,
                'pelapor' => [
                    'nik' => '3523000000000001',
                    'nama' => 'Pelapor Uji',
                    'alamat' => 'Jl. Uji',
                    'hubungan' => 'Orang Tua',
                    'telepon' => '081200000000',
                ],
                'kasus' => [
                    'tanggal' => '2026-11-11',
                    'tempat' => 'Kecamatan Uji',
                    'kronologi' => 'Kronologi uji coba.',
                ],
            ])
            ->assertRedirect(route('laporan_proses'))
            ->assertSessionHas('success');

        $this->assertSame('diproses', Laporan::find($id)->status);
        $this->assertDatabaseHas('detail_pelapor', [
            'id_laporan' => $id,
            'nama' => 'Pelapor Uji',
        ]);
    }
}
