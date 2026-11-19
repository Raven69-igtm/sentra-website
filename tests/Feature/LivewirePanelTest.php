<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\ChatMobile;
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

        // Daftar percakapan: klik dirender instan dari cache yang dikirim
        // bersama halaman, bukan lewat request/soft navigation.
        $this->get(route('chat.index'))
            ->assertOk()
            ->assertSee('data-live-region="ppa-chat-list"', false)
            ->assertSee('id="chat-prefetch"', false);
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

    public function test_chat_show_tetap_mengembalikan_html_untuk_request_xhr(): void
    {
        $admin = $this->admin('admin_ppa', 'admin.ppa@sentra.com');
        $this->actingAs($admin);

        $warga = Akun::create([
            'nama' => 'Warga Uji Chat',
            'email' => 'warga.chat.' . random_int(1000, 9999) . '@sentra.test',
            'notelp' => '0813000' . random_int(1000, 9999),
            'password' => 'RahasiaUji123!',
            'role' => 'user',
            'is_active' => true,
        ]);

        ChatMobile::create([
            'sender_id' => $warga->id_akun,
            'receiver_id' => $admin->id_akun,
            'message' => 'Halo admin, saya ingin konsultasi.',
        ]);

        // Klien chat memakai XHR, tetapi halaman show harus tetap HTML.
        $this->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            ->get(route('chat.show', $warga->id_akun))
            ->assertOk()
            ->assertSee('data-live-region="ppa-chat-list"', false)
            ->assertSee('id="chat-prefetch"', false)
            ->assertSee('Percakapan konsultasi', false)
            ->assertSee('Halo admin, saya ingin konsultasi.', false);
    }

    public function test_chat_tidak_tersedia_untuk_admin_dinsos(): void
    {
        $dinsos = $this->admin('admin_dinsos', 'admin.dinsos@sentra.com');

        // Akses langsung ke modul chat dialihkan ke dashboard Dinsos.
        $this->actingAs($dinsos)
            ->get(route('chat.index'))
            ->assertRedirect(route('dinsos.dashboard'))
            ->assertSessionHas('error');

        // Menu dan badge chat tidak dirender di sidebar Dinsos.
        $this->actingAs($dinsos)
            ->get(route('dinsos.dashboard'))
            ->assertOk()
            ->assertDontSee('id="sidebar-chat-badge"', false)
            ->assertDontSee(route('chat.index'), false);

        // Kirim pesan dari akun Dinsos tidak membuat baris chat baru.
        $sebelum = ChatMobile::count();

        $this->actingAs($dinsos)
            ->post(route('chat.send'), [
                'receiver_id' => $dinsos->id_akun,
                'message' => 'Pesan uji dari Admin Dinsos',
            ])
            ->assertRedirect(route('dinsos.dashboard'));

        $this->assertSame($sebelum, ChatMobile::count());
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
