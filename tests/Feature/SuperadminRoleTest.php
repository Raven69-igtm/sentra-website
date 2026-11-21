<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Role;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class SuperadminRoleTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $superadmin = Akun::create([
            'nama' => 'Superadmin Uji Role',
            'email' => 'sa.role.' . Str::random(8) . '@sentra.test',
            'notelp' => '081200000004',
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $this->actingAs($superadmin);
    }

    public function test_nama_role_dinormalisasi_sebelum_validasi_unique(): void
    {
        $this->post(route('superadmin.roles.store'), [
            'name' => 'Konselor Ahli',
            'display_name' => 'Konselor Ahli',
            'description' => 'Role uji',
        ])->assertRedirect(route('superadmin.roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'konselor_ahli']);

        // Variasi penulisan yang menghasilkan slug sama harus ditolak.
        $this->from(route('superadmin.roles.create'))
            ->post(route('superadmin.roles.store'), [
                'name' => 'Konselor-Ahli',
                'display_name' => 'Konselor Ahli Duplikat',
            ])
            ->assertRedirect(route('superadmin.roles.create'))
            ->assertSessionHasErrors('name');

        $this->assertSame(1, Role::where('name', 'konselor_ahli')->count());
    }

    public function test_nama_role_yang_tidak_menghasilkan_slug_ditolak(): void
    {
        $this->from(route('superadmin.roles.create'))
            ->post(route('superadmin.roles.store'), [
                'name' => '!!!',
                'display_name' => 'Role Tidak Valid',
            ])
            ->assertRedirect(route('superadmin.roles.create'))
            ->assertSessionHasErrors('name');
    }

    public function test_role_mobile_only_diblokir_dari_pengaturan_superadmin(): void
    {
        $userRole = Role::where('name', 'user')->first();
        $guestRole = Role::where('name', 'guest')->first();

        $this->assertNotNull($userRole);
        $this->assertNotNull($guestRole);

        $this->get(route('superadmin.roles.edit', $userRole->id))
            ->assertForbidden();

        $this->put(route('superadmin.roles.update', $guestRole->id), [
            'display_name' => 'Guest Diubah',
            'description' => 'Tidak boleh diubah',
        ])
            ->assertForbidden();

        $this->delete(route('superadmin.roles.destroy', $userRole->id))
            ->assertForbidden();
    }

    public function test_role_admin_ppa_tetap_dapat_diatur(): void
    {
        $adminPpa = Role::where('name', 'admin_ppa')->first();

        if (! $adminPpa) {
            $adminPpa = Role::create([
                'name' => 'admin_ppa',
                'display_name' => 'Admin PPA',
                'description' => 'Administrator UPTD PPA',
                'is_system' => true,
            ]);
        }

        $this->get(route('superadmin.roles.edit', $adminPpa->id))
            ->assertOk();
    }

    public function test_daftar_peran_hanya_menampilkan_tiga_role_admin_dan_tanpa_admin_operasional(): void
    {
        $response = $this->get(route('superadmin.roles.index'));
        $response->assertOk();
        $response->assertSee('Superadmin');
        $response->assertSee('Admin PPA');
        $response->assertSee('Admin Dinsos');
        $response->assertDontSee('Admin Operasional');
    }
}
