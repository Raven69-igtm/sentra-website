<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Laporan;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class SuperadminAkunCrudTest extends TestCase
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
            'nama' => 'Superadmin Uji Crud',
            'email' => 'sa.crud.' . Str::random(8) . '@sentra.test',
            'notelp' => '081200000003',
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->actingAs($this->superadmin);
    }

    public function test_checkbox_status_menyimpan_aktif_dan_nonaktif_dengan_benar(): void
    {
        $inactive = 'NonaktifBaru' . Str::random(5);
        $active = 'AktifBaru' . Str::random(5);

        $this->post(route('superadmin.akun.store'), [
            'nama' => $inactive,
            'email' => 'nonaktif.' . Str::random(8) . '@sentra.test',
            'notelp' => '081211111111',
            'password' => 'RahasiaUji123!',
            'role' => 'guest',
            'jenis_kelamin' => 'Pria',
            'alamat' => 'Alamat uji',
            'is_active' => '0',
        ])->assertRedirect(route('superadmin.akun.index'));

        $this->assertDatabaseHas('akun', ['nama' => $inactive, 'is_active' => false]);

        $this->post(route('superadmin.akun.store'), [
            'nama' => $active,
            'email' => 'aktif.' . Str::random(8) . '@sentra.test',
            'notelp' => '081222222222',
            'password' => 'RahasiaUji123!',
            'role' => 'guest',
            'is_active' => '1',
        ])->assertRedirect(route('superadmin.akun.index'));

        $this->assertDatabaseHas('akun', ['nama' => $active, 'is_active' => true]);
    }

    public function test_role_guest_dapat_dibuat_dan_status_bisa_diubah_lewat_edit(): void
    {
        $akun = Akun::create([
            'nama' => 'Akun Guest ' . Str::random(5),
            'email' => 'guest.' . Str::random(8) . '@sentra.test',
            'notelp' => '081233333333',
            'password' => 'RahasiaUji123!',
            'role' => 'guest',
            'is_active' => true,
        ]);

        $this->put(route('superadmin.akun.update', $akun->id_akun), [
            'nama' => $akun->nama,
            'email' => $akun->email,
            'notelp' => '081233333333',
            'role' => 'guest',
            'is_active' => '0',
        ])->assertRedirect(route('superadmin.akun.index'));

        $this->assertDatabaseHas('akun', [
            'id_akun' => $akun->id_akun,
            'is_active' => false,
        ]);

        $this->post(route('superadmin.akun.toggle-status', $akun->id_akun))
            ->assertRedirect();

        $this->assertDatabaseHas('akun', [
            'id_akun' => $akun->id_akun,
            'is_active' => true,
        ]);
    }

    public function test_akun_tanpa_laporan_dapat_dihapus(): void
    {
        $akun = Akun::create([
            'nama' => 'Akun Hapus ' . Str::random(5),
            'email' => 'hapus.' . Str::random(8) . '@sentra.test',
            'notelp' => '081244444444',
            'password' => 'RahasiaUji123!',
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->delete(route('superadmin.akun.destroy', $akun->id_akun))
            ->assertRedirect(route('superadmin.akun.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('akun', ['id_akun' => $akun->id_akun]);
    }

    public function test_akun_dengan_laporan_tidak_dapat_dihapus(): void
    {
        $akun = Akun::create([
            'nama' => 'Akun Berlaporan ' . Str::random(5),
            'email' => 'laporan.' . Str::random(8) . '@sentra.test',
            'notelp' => '081255555555',
            'password' => 'RahasiaUji123!',
            'role' => 'user',
            'is_active' => true,
        ]);

        Laporan::create([
            'id_laporan' => 'LAP-UJI-' . Str::random(8),
            'id_akun' => $akun->id_akun,
            'kategori' => 'Kekerasan Fisik',
            'status' => 'dikirim',
        ]);

        $this->delete(route('superadmin.akun.destroy', $akun->id_akun))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('akun', ['id_akun' => $akun->id_akun]);
    }
}
