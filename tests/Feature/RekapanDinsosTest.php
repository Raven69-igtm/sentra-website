<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Laporan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

/**
 * Memastikan Rekapan Admin Dinsos hanya memuat laporan milik Dinsos dan
 * tidak lagi menarik laporan PPA yang berstatus 'dirujuk'.
 *
 * Rujukan PPA tetap harus terlihat pada daftar operasional Dinsos, jadi
 * tes ini juga mengunci perilaku Laporan::forUser() agar tidak ikut berubah.
 */
class RekapanDinsosTest extends TestCase
{
    use DatabaseTransactions;

    private Akun $ppa;

    private Akun $dinsos;

    /** @var array<string, string> */
    private array $ids = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->ppa = $this->admin('admin_ppa', 'admin.ppa@sentra.com');
        $this->dinsos = $this->admin('admin_dinsos', 'admin.dinsos@sentra.com');

        $this->ids = [
            'ppa_dirujuk' => $this->buatLaporan('PPA-DIRUJUK', 'Kekerasan Seksual', null, 'dirujuk'),
            'ppa_selesai' => $this->buatLaporan('PPA-SELESAI', 'Kekerasan Fisik', null, 'selesai'),
            'ppa_unset' => $this->buatLaporan('PPA-UNSET', 'unset', null, 'dirujuk'),
            'dinsos_rehab' => $this->buatLaporan('DINSOS-REHAB', 'unset', 'rehabilitasi_sosial', 'selesai'),
            'dinsos_legacy' => $this->buatLaporan('DINSOS-LEGACY', 'Penelantaran', null, 'selesai'),
            'dinsos_tolak' => $this->buatLaporan('DINSOS-TOLAK', 'unset', 'permasalahan_sosial', 'ditolak'),
        ];
    }

    private function admin(string $role, string $email): Akun
    {
        $akun = Akun::where('email', $email)->first();

        if (! $akun) {
            $akun = Akun::create([
                'nama' => 'Uji ' . $role,
                'email' => $email,
                'notelp' => '0813000' . random_int(1000, 9999),
                'password' => 'RahasiaUji123!',
                'role' => $role,
                'is_active' => true,
            ]);
        }

        return $akun;
    }

    private function buatLaporan(string $id, string $kategori, ?string $layanan, string $status): string
    {
        // Trigger MySQL `before_laporan_insert_id` selalu menimpa id_laporan,
        // jadi simpan penanda unik di kolom judul lalu baca ID hasil trigger.
        $penanda = $id . '-' . strtoupper(bin2hex(random_bytes(5)));

        Laporan::create([
            'id_laporan' => $id . '-TEMP',
            'id_akun' => $this->ppa->id_akun,
            'judul' => $penanda,
            'kategori' => $kategori,
            'layanan_pjsrk' => $layanan,
            'status' => $status,
        ]);

        $idLaporan = DB::table('laporan')->where('judul', $penanda)->value('id_laporan');

        $this->assertNotNull($idLaporan, 'Laporan uji gagal dibuat.');

        return (string) $idLaporan;
    }

    public function test_rekapan_dinsos_hanya_memuat_laporan_dinsos(): void
    {
        $response = $this->actingAs($this->dinsos)->get(route('rekapan'));

        $response->assertOk();
        $response->assertSee('ditolak layanan Dinsos', false);
        $response->assertDontSee('value="dirujuk"', false);

        foreach (['dinsos_rehab', 'dinsos_legacy', 'dinsos_tolak'] as $key) {
            $response->assertSee($this->ids[$key], false);
        }

        foreach (['ppa_dirujuk', 'ppa_selesai', 'ppa_unset'] as $key) {
            $response->assertDontSee($this->ids[$key], false);
        }
    }

    public function test_rekapan_ppa_tetap_memuat_laporan_ppa_dan_tidak_memuat_laporan_dinsos(): void
    {
        $response = $this->actingAs($this->ppa)->get(route('rekapan'));

        $response->assertOk();
        $response->assertSee('value="dirujuk"', false);
        $response->assertDontSee('value="ditolak"', false);

        foreach (['ppa_dirujuk', 'ppa_selesai', 'ppa_unset'] as $key) {
            $response->assertSee($this->ids[$key], false);
        }

        foreach (['dinsos_rehab', 'dinsos_legacy', 'dinsos_tolak'] as $key) {
            $response->assertDontSee($this->ids[$key], false);
        }
    }

    public function test_filter_status_dirujuk_tidak_membocorkan_laporan_ppa_ke_rekapan_dinsos(): void
    {
        $response = $this->actingAs($this->dinsos)
            ->get(route('rekapan', ['status' => 'dirujuk']));

        $response->assertOk();
        $response->assertDontSee($this->ids['ppa_dirujuk'], false);
        $response->assertDontSee($this->ids['ppa_unset'], false);

        // Status di luar daftar role diabaikan → kembali ke status default.
        $response->assertSee($this->ids['dinsos_rehab'], false);
        $response->assertSee($this->ids['dinsos_tolak'], false);
    }

    public function test_export_dinsos_konsisten_dengan_tabel_tanpa_data_ppa(): void
    {
        $response = $this->actingAs($this->dinsos)
            ->post(route('rekapan.export'), []);

        $response->assertOk();
        $this->assertTrue(
            $response->baseResponse instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse,
            'Respons export harus berupa unduhan file.'
        );

        $sheet = IOFactory::load($response->baseResponse->getFile()->getPathname())
            ->getActiveSheet();

        $ids = collect($sheet->rangeToArray('A2:A' . $sheet->getHighestDataRow()))
            ->flatten()
            ->filter()
            ->map(fn ($value) => (string) $value)
            ->values()
            ->all();

        foreach (['dinsos_rehab', 'dinsos_legacy', 'dinsos_tolak'] as $key) {
            $this->assertContains($this->ids[$key], $ids, "Export harus memuat {$key}.");
        }

        foreach (['ppa_dirujuk', 'ppa_selesai', 'ppa_unset'] as $key) {
            $this->assertNotContains($this->ids[$key], $ids, "Export tidak boleh memuat {$key}.");
        }
    }

    public function test_rujukan_ppa_tetap_terlihat_di_daftar_operasional_dinsos(): void
    {
        // forUser() sengaja tidak diubah: rujukan PPA tetap masuk daftar
        // operasional Dinsos.
        $this->assertTrue(
            Laporan::forUser($this->dinsos)
                ->whereKey($this->ids['ppa_dirujuk'])
                ->exists(),
            'Rujukan PPA harus tetap terlihat lewat forUser().'
        );

        // Scope rekap Dinsos yang baru justru harus mengecualikannya.
        $this->assertFalse(
            Laporan::forUser($this->dinsos)
                ->laporanDinsos()
                ->whereKey($this->ids['ppa_dirujuk'])
                ->exists(),
            'Rujukan PPA tidak boleh masuk scope rekap Dinsos.'
        );

        $this->actingAs($this->dinsos)
            ->get(route('dinsos.laporan.index'))
            ->assertOk()
            ->assertSee($this->ids['ppa_dirujuk'], false);
    }
}
