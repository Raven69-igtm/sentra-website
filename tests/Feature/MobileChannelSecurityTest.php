<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\UserMobile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MobileChannelSecurityTest extends TestCase
{
    use DatabaseTransactions;

    private function buatAkunMobile(array $atribut = []): UserMobile
    {
        return UserMobile::create(array_merge([
            'notelp' => '0812' . random_int(10000000, 99999999),
            'nama' => 'Akun Mobile Uji',
            'email' => 'mobile.' . uniqid() . '@contoh.test',
            'password' => Hash::make('rahasia-uji'),
            'role' => 'user',
            'is_active' => true,
        ], $atribut));
    }

    private function tokenMobile(UserMobile $akun, array $abilities = ['mobile'])
    {
        return $akun->createToken('mobile', $abilities);
    }

    private function superadmin(): Akun
    {
        $superadmin = Akun::where('role', 'superadmin')->first();

        if (! $superadmin) {
            $superadmin = Akun::create([
                'nama' => 'Superadmin Uji',
                'email' => 'superadmin.uji.' . uniqid() . '@contoh.test',
                'notelp' => '0812' . random_int(10000000, 99999999),
                'password' => 'rahasia-uji',
                'role' => 'superadmin',
                'is_active' => true,
            ]);
        }

        return $superadmin;
    }

    public function test_akun_admin_tidak_bisa_login_mobile_via_nomor_telepon(): void
    {
        $admin = $this->buatAkunMobile(['role' => 'admin_ppa']);

        $this->postJson('/api/auth/login', [
            'notelp' => $admin->notelp,
            'password' => 'rahasia-uji',
        ])->assertStatus(401);
    }

    public function test_akun_admin_tidak_bisa_login_mobile_via_email(): void
    {
        $admin = $this->buatAkunMobile(['role' => 'admin_dinsos']);

        $this->postJson('/api/auth/email', [
            'email' => $admin->email,
        ])->assertStatus(401);
    }

    public function test_akun_admin_tidak_bisa_login_mobile_via_biometrik(): void
    {
        $admin = $this->buatAkunMobile(['role' => 'superadmin']);

        $this->postJson('/api/auth/biometric', [
            'notelp' => $admin->notelp,
        ])->assertStatus(401);
    }

    public function test_akun_mobile_tidak_bisa_login_panel_web(): void
    {
        $user = $this->buatAkunMobile();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'rahasia-uji',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_akun_mobile_nonaktif_tidak_bisa_login_mobile(): void
    {
        $user = $this->buatAkunMobile(['is_active' => false]);

        $this->postJson('/api/auth/login', [
            'notelp' => $user->notelp,
            'password' => 'rahasia-uji',
        ])->assertStatus(401);
    }

    public function test_akun_admin_nonaktif_tidak_bisa_login_panel_web(): void
    {
        $admin = $this->buatAkunMobile([
            'role' => 'admin_ppa',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'rahasia-uji',
        ])->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    public function test_registrasi_mobile_selalu_menyimpan_role_user(): void
    {
        $notelp = '0812' . random_int(10000000, 99999999);

        $this->postJson('/api/auth/register', [
            'notelp' => $notelp,
            'nama' => 'Pendaftar Mobile',
            'password' => 'rahasia-uji',
            'role' => 'admin',
        ])->assertStatus(201);

        $this->assertDatabaseHas('akun', [
            'notelp' => $notelp,
            'role' => 'user',
        ]);
    }

    public function test_registrasi_mobile_auth_selalu_menyimpan_role_user(): void
    {
        $notelp = '0812' . random_int(10000000, 99999999);

        $this->postJson('/api/register', [
            'notelp' => $notelp,
            'nama' => 'Pendaftar Mobile Dua',
            'password' => 'rahasia-uji',
            'role' => 'admin',
        ])->assertOk();

        $this->assertDatabaseHas('akun', [
            'notelp' => $notelp,
            'role' => 'user',
        ]);
    }

    public function test_endpoint_edit_mobile_wajib_autentikasi(): void
    {
        $user = $this->buatAkunMobile();

        $this->putJson('/api/auth/edit/' . $user->getKey(), [
            'nama' => 'Nama Baru',
        ])->assertStatus(401);
    }

    public function test_mobile_tidak_bisa_mengubah_akun_lain(): void
    {
        $pemilik = $this->buatAkunMobile();
        $lain = $this->buatAkunMobile();

        $token = $this->tokenMobile($pemilik)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/auth/edit/' . $lain->getKey(), [
                'nama' => 'Diubah Orang Lain',
            ])
            ->assertStatus(403);
    }

    public function test_mobile_tidak_bisa_menaikkan_role_lewat_edit(): void
    {
        $user = $this->buatAkunMobile();
        $token = $this->tokenMobile($user)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/auth/edit/' . $user->getKey(), [
                'nama' => 'Nama Diperbarui',
                'role' => 'admin',
            ])
            ->assertOk();

        $this->assertDatabaseHas('akun', [
            'id_akun' => $user->getKey(),
            'role' => 'user',
            'nama' => 'Nama Diperbarui',
        ]);
    }

    public function test_token_admin_ditolak_dan_dicabut_di_endpoint_mobile(): void
    {
        $admin = $this->buatAkunMobile(['role' => 'admin_ppa']);
        $token = $this->tokenMobile($admin);

        $this->withHeader('Authorization', 'Bearer ' . $token->plainTextToken)
            ->getJson('/api/user')
            ->assertStatus(403);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $token->accessToken->id,
        ]);
    }

    public function test_token_tanpa_ability_mobile_ditolak(): void
    {
        $user = $this->buatAkunMobile();
        $token = $this->tokenMobile($user, ['read'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user')
            ->assertStatus(403);
    }

    public function test_mobile_valid_bisa_mengakses_endpoint_user(): void
    {
        $user = $this->buatAkunMobile();
        $token = $this->tokenMobile($user)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id_akun', $user->getKey());
    }

    public function test_superadmin_reset_password_mencabut_token_mobile(): void
    {
        $user = $this->buatAkunMobile();
        $this->tokenMobile($user);

        $this->actingAs($this->superadmin())
            ->post(route('superadmin.akun.reset-password', $user->getKey()), [
                'new_password' => 'password-baru-uji',
            ])
            ->assertRedirect();

        $this->assertSame(0, DB::table('personal_access_tokens')
            ->whereIn('tokenable_type', [UserMobile::class, Akun::class])
            ->where('tokenable_id', $user->getKey())
            ->count());
    }

    public function test_superadmin_menonaktifkan_akun_mencabut_token_mobile(): void
    {
        $user = $this->buatAkunMobile();
        $this->tokenMobile($user);

        $this->actingAs($this->superadmin())
            ->post(route('superadmin.akun.toggle-status', $user->getKey()))
            ->assertRedirect();

        $this->assertDatabaseHas('akun', [
            'id_akun' => $user->getKey(),
            'is_active' => false,
        ]);

        $this->assertSame(0, DB::table('personal_access_tokens')
            ->whereIn('tokenable_type', [UserMobile::class, Akun::class])
            ->where('tokenable_id', $user->getKey())
            ->count());
    }
}
