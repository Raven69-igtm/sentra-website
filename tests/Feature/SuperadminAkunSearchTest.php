<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class SuperadminAkunSearchTest extends TestCase
{
    use DatabaseTransactions;

    private Akun $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(
            ['name' => 'guest'],
            ['display_name' => 'Guest', 'description' => 'Pengguna tamu', 'is_system' => true]
        );

        $this->superadmin = Akun::create([
            'nama' => 'Superadmin Uji Akun',
            'email' => 'sa.akun.' . Str::random(8) . '@sentra.test',
            'notelp' => '081200000002',
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    private function akun(array $attributes = []): Akun
    {
        return Akun::create(array_merge([
            'nama' => 'Akun Uji ' . Str::random(6),
            'email' => 'akun.' . Str::random(8) . '@sentra.test',
            'notelp' => '0812' . random_int(10000000, 99999999),
            'password' => 'RahasiaUji123!',
            'role' => 'user',
            'is_active' => true,
        ], $attributes));
    }

    public function test_pencarian_akun_mencakup_id_nama_email_alamat_dan_telepon_terenkripsi(): void
    {
        $marker = 'Penanda' . Str::random(6);

        $target = $this->akun([
            'nama' => 'Budi ' . $marker,
            'email' => strtolower($marker) . '@sentra.test',
            'notelp' => '081234567890',
            'alamat' => 'Jl. Melati No. 10 ' . $marker,
            'role' => 'user',
        ]);

        $this->actingAs($this->superadmin);

        // Nama
        $this->get(route('superadmin.akun.index', ['q' => $marker]))
            ->assertOk()
            ->assertSee($target->nama);

        // Email
        $this->get(route('superadmin.akun.index', ['q' => strtolower($marker)]))
            ->assertOk()
            ->assertSee($target->email);

        // ID akun
        $this->get(route('superadmin.akun.index', ['q' => (string) $target->id_akun]))
            ->assertOk()
            ->assertSee($target->nama);

        // Telepon terenkripsi: variasi format harus tetap menemukan akun.
        foreach (['0812345', '6281234567890', '+62 812-3456-7890'] as $phone) {
            $this->get(route('superadmin.akun.index', ['q' => $phone]))
                ->assertOk()
                ->assertSee($target->nama);
        }
    }

    public function test_filter_status_role_legacy_jenis_kelamin_dan_instansi(): void
    {
        $aktif = $this->akun(['nama' => 'AkunAktif' . Str::random(5), 'is_active' => true, 'role' => 'user']);
        $nonaktif = $this->akun(['nama' => 'AkunNonaktif' . Str::random(5), 'is_active' => false, 'role' => 'user']);

        $legacyAdmin = $this->akun([
            'nama' => 'AkunLegacy' . Str::random(5),
            'role' => 'admin',
            'jenis_kelamin' => 'Pria',
        ]);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.akun.index', ['status' => 'aktif']))
            ->assertOk()
            ->assertSee($aktif->nama)
            ->assertDontSee($nonaktif->nama);

        $this->get(route('superadmin.akun.index', ['status' => 'nonaktif']))
            ->assertOk()
            ->assertSee($nonaktif->nama)
            ->assertDontSee($aktif->nama);

        // Role legacy 'admin' harus muncul saat memfilter Admin PPA.
        $this->get(route('superadmin.akun.index', ['role' => 'admin_ppa']))
            ->assertOk()
            ->assertSee($legacyAdmin->nama);

        $this->get(route('superadmin.akun.index', ['jenis_kelamin' => 'Pria']))
            ->assertOk()
            ->assertSee($legacyAdmin->nama);

        // Instansi UPTD PPA mencakup role admin legacy.
        $this->get(route('superadmin.akun.index', ['instansi' => 'uptd_ppa']))
            ->assertOk()
            ->assertSee($legacyAdmin->nama);
    }

    public function test_pencarian_akun_berdasarkan_status_dan_label_role(): void
    {
        $marker = 'StatusCari' . Str::random(5);
        $aktif = $this->akun(['nama' => $marker, 'is_active' => true]);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.akun.index', ['q' => 'aktif']))
            ->assertOk()
            ->assertSee($aktif->nama);

        $this->get(route('superadmin.akun.index', ['q' => 'Superadmin']))
            ->assertOk()
            ->assertSee($this->superadmin->nama);

        // Istilah instansi "Masyarakat" harus memetakan ke role user/guest.
        $warga = $this->akun(['nama' => 'WargaMasyarakat' . Str::random(5), 'role' => 'user']);

        $this->get(route('superadmin.akun.index', ['q' => 'Masyarakat']))
            ->assertOk()
            ->assertSee($warga->nama);
    }

    public function test_pencarian_potongan_nama_bulan_menemukan_akun_bulan_tersebut(): void
    {
        $role = 'uji_bulan';

        $november = $this->akun([
            'nama' => 'Akun Bulan Sebelas',
            'email' => Str::uuid() . '@sentra.test',
            'role' => $role,
        ]);
        Akun::whereKey($november->id_akun)->update(['created_at' => '2026-11-05 10:00:00']);

        $desember = $this->akun([
            'nama' => 'Akun Bulan Dua Belas',
            'email' => Str::uuid() . '@sentra.test',
            'role' => $role,
        ]);
        Akun::whereKey($desember->id_akun)->update(['created_at' => '2026-12-05 10:00:00']);

        $teksNo = $this->akun([
            'nama' => 'Akun Nomor',
            'email' => Str::uuid() . '@sentra.test',
            'role' => $role,
        ]);
        Akun::whereKey($teksNo->id_akun)->update(['created_at' => '2026-10-05 10:00:00']);

        $this->actingAs($this->superadmin);

        $response = $this->get(route('superadmin.akun.index', [
            'q' => 'no',
            'role' => $role,
            'per_page' => 100,
        ]));

        $response->assertOk()
            ->assertSee($november->nama)
            ->assertSee($teksNo->nama)
            ->assertDontSee($desember->nama);
    }

    public function test_pencarian_status_secara_parsial(): void
    {
        $role = 'uji_status';

        $aktif = $this->akun([
            'nama' => 'Akun Tersedia',
            'email' => Str::uuid() . '@sentra.test',
            'role' => $role,
            'is_active' => true,
        ]);

        $nonaktif = $this->akun([
            'nama' => 'Akun Terhenti',
            'email' => Str::uuid() . '@sentra.test',
            'role' => $role,
            'is_active' => false,
        ]);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.akun.index', [
            'q' => 'akt',
            'role' => $role,
            'per_page' => 100,
        ]))
            ->assertOk()
            ->assertSee($aktif->nama)
            ->assertDontSee($nonaktif->nama);

        $this->get(route('superadmin.akun.index', [
            'q' => 'non',
            'role' => $role,
            'per_page' => 100,
        ]))
            ->assertOk()
            ->assertSee($nonaktif->nama)
            ->assertDontSee($aktif->nama);
    }

    public function test_pencarian_tanggal_dan_bulan_daftar_akun(): void
    {
        $role = 'uji_tanggal';

        $target = $this->akun([
            'nama' => 'Akun Tanggal Uji',
            'email' => Str::uuid() . '@sentra.test',
            'role' => $role,
        ]);
        Akun::whereKey($target->id_akun)->update(['created_at' => '2026-11-19 10:00:00']);

        $lain = $this->akun([
            'nama' => 'Akun Tanggal Lain',
            'email' => Str::uuid() . '@sentra.test',
            'role' => $role,
        ]);
        Akun::whereKey($lain->id_akun)->update(['created_at' => '2025-12-18 10:00:00']);

        $this->actingAs($this->superadmin);

        foreach (['2026-11-19', '19 November 2026', '19-11-2026', 'no 2026'] as $keyword) {
            $this->get(route('superadmin.akun.index', [
                'q' => $keyword,
                'role' => $role,
                'per_page' => 100,
            ]))
                ->assertOk()
                ->assertSee($target->nama)
                ->assertSee('19 Nov 2026')
                ->assertDontSee($lain->nama);
        }
    }

    public function test_ui_pencarian_akun_memakai_live_search_dan_debounce(): void
    {
        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.akun.index'))
            ->assertOk()
            ->assertSee('data-live-debounce="300"', false)
            ->assertSee('id="akunQ"', false)
            ->assertSee('type="search"', false)
            ->assertSee('autocomplete="off"', false)
            ->assertSee('Potongan nama bulan juga dikenali', false);
    }

    public function test_keyword_wildcard_dan_pagination_mempertahankan_query(): void
    {
        $marker = 'WildcardAkun' . Str::random(5);
        $target = $this->akun(['nama' => $marker, 'role' => 'user']);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.akun.index', ['q' => '%']))
            ->assertOk()
            ->assertDontSee($target->nama);

        $this->get(route('superadmin.akun.index', ['q' => '_']))
            ->assertOk()
            ->assertDontSee($target->nama);

        $response = $this->get(route('superadmin.akun.index', ['q' => $marker, 'per_page' => 25]));
        $response->assertOk();
        $this->assertSame($marker, $response->viewData('filters')['q']);
    }
}
