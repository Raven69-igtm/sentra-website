<?php

namespace Tests\Feature;

use App\Models\Akun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

/**
 * Verifikasi panel Admin PPA & Admin Dinsos berjalan dengan Livewire
 * seperti panel Superadmin:
 *   - runtime Livewire dimuat oleh layout bersama,
 *   - pustaka inti memakai data-navigate-once (tidak dieksekusi ulang),
 *   - tautan navigasi memakai wire:navigate (soft navigation),
 *   - daftar laporan memakai form filter data-livewire-navigate dan
 *     area hasil data-live-region (filter parsial tanpa reload).
 */
class LivewirePanelTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Ambil akun admin yang sudah ada, atau buat sementara di dalam
     * transaksi agar tidak mengubah data aplikasi.
     */
    private function admin(string $role, string $email): Akun
    {
        $akun = Akun::where('email', $email)->first();

        if (! $akun) {
            $akun = Akun::create([
                'nama' => 'Uji ' . $role,
                'email' => $email,
                'notelp' => '0812000' . random_int(1000, 9999),
                'password' => 'RahasiaUji123!',
                'role' => $role,
                'is_active' => true,
            ]);
        }

        return $akun;
    }

    public function test_layout_instansi_memuat_runtime_livewire(): void
    {
        $this->actingAs($this->admin('admin_ppa', 'admin.ppa@sentra.com'));

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        // Runtime Livewire (pengganti reload penuh) ikut terpasang.
        // Nama berkasnya livewire.js dengan endpoint ber-hash, mis.
        // /livewire-78f560e2/livewire.js.
        $response->assertSee('livewire.js', false);
        // Pustaka inti hanya dieksekusi sekali per sesi browser.
        $response->assertSee('data-navigate-once', false);
        // Lonceng notifikasi adalah komponen Livewire dengan polling.
        $response->assertSee('wire:poll', false);
    }

    public function test_halaman_ppa_memakai_soft_navigation_dan_live_filter(): void
    {
        $this->actingAs($this->admin('admin_ppa', 'admin.ppa@sentra.com'));

        // Daftar laporan diproses: filter server-side + area live.
        $this->get(route('laporan_proses'))
            ->assertOk()
            ->assertSee('wire:navigate', false)
            ->assertSee('data-livewire-navigate', false)
            ->assertSee('data-live-region="ppa-laporan-proses"', false)
            ->assertSee('data-live-reset', false);

        // Arsip laporan selesai.
        $this->get(route('selesai'))
            ->assertOk()
            ->assertSee('data-livewire-navigate', false)
            ->assertSee('data-live-region="ppa-laporan-selesai"', false);

        // Laporan cepat PPA.
        $this->get(route('laporan_cepat'))
            ->assertOk()
            ->assertSee('data-livewire-navigate', false)
            ->assertSee('data-live-region="ppa-laporan-cepat"', false);

        // Daftar percakapan memakai soft navigation.
        $this->get(route('chat.index'))
            ->assertOk()
            ->assertSee('wire:navigate', false);
    }

    public function test_halaman_dinsos_memakai_soft_navigation_dan_live_filter(): void
    {
        $this->actingAs($this->admin('admin_dinsos', 'admin.dinsos@sentra.com'));

        $this->get(route('dinsos.dashboard'))
            ->assertOk()
            ->assertSee('wire:navigate', false);

        // Laporan PJSRK: filter (search/status/kecamatan/layanan) + area live.
        $this->get(route('dinsos.laporan.index'))
            ->assertOk()
            ->assertSee('data-livewire-navigate', false)
            ->assertSee('data-live-region="dinsos-laporan"', false)
            ->assertSee('data-live-reset', false);

        // Laporan cepat PJSRK.
        $this->get(route('dinsos.laporan_cepat'))
            ->assertOk()
            ->assertSee('data-livewire-navigate', false)
            ->assertSee('data-live-region="dinsos-laporan-cepat"', false);

        // Filter diterapkan server-side: respons parsial masih membawa
        // area live yang sama sehingga bisa ditukar tanpa reload.
        $this->get(route('dinsos.laporan.index', ['status' => 'selesai', 'search' => 'uji']))
            ->assertOk()
            ->assertSee('data-live-region="dinsos-laporan"', false);
    }

    public function test_perubahan_status_masih_tersedia_di_daftar_laporan(): void
    {
        $this->actingAs($this->admin('admin_dinsos', 'admin.dinsos@sentra.com'));

        // Tombol aksi tetap berupa form POST/PUT meski halaman memakai
        // soft navigation — aksi tidak boleh berubah menjadi tautan GET.
        $this->get(route('dinsos.laporan.index'))
            ->assertOk()
            ->assertSee('method="POST"', false)
            ->assertSee('_method', false);
    }
}
