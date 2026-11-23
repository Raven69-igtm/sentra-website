<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\UserMobile;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MobileApiAuthorizationTest extends TestCase
{
    use DatabaseTransactions;

    private function buatUserMobile(array $overrides = []): UserMobile
    {
        return UserMobile::create(array_merge([
            'notelp' => '0812' . random_int(10000000, 99999999),
            'nama' => 'Pelapor Uji ' . uniqid(),
            'email' => 'pelapor.' . uniqid() . '@contoh.test',
            'password' => Hash::make('rahasia-uji'),
            'role' => 'user',
            'is_active' => true,
        ], $overrides));
    }

    public function test_endpoint_getdata_publik_sudah_dihapus(): void
    {
        $this->getJson('/api/auth/getdata')->assertStatus(404);
    }

    public function test_login_email_tanpa_verifikasi_ditolak(): void
    {
        $user = $this->buatUserMobile();

        $this->postJson('/api/auth/email', [
            'email' => $user->email,
        ])->assertStatus(401);
    }

    public function test_biometric_tanpa_token_sanctum_ditolak(): void
    {
        $user = $this->buatUserMobile();

        $this->postJson('/api/auth/biometric', [
            'notelp' => $user->notelp,
        ])->assertStatus(401);
    }

    public function test_endpoint_sensitif_wajib_autentikasi_sanctum(): void
    {
        $user = $this->buatUserMobile();

        $this->postJson('/api/post-report', [])->assertStatus(401);
        $this->getJson('/api/user-reports/' . $user->id_akun)->assertStatus(401);
        $this->getJson('/api/user-reports-by-category/' . $user->id_akun . '/dikirim')->assertStatus(401);
        $this->getJson('/api/search?keyword=test')->assertStatus(401);
        $this->postJson('/api/pjsrk/bencana', [])->assertStatus(401);
        $this->postJson('/api/pjsrk/rehabilitasi', [])->assertStatus(401);
        $this->postJson('/api/pjsrk/permasalahan-sosial', [])->assertStatus(401);
        $this->getJson('/api/notifications')->assertStatus(401);
        $this->postJson('/api/update-fcm-token', ['fcm_token' => 'abc'])->assertStatus(401);
        $this->postJson('/api/report-update', [])->assertStatus(401);
    }

    public function test_user_tidak_bisa_mengakses_riwayat_atau_fcm_akun_lain(): void
    {
        $userA = $this->buatUserMobile();
        $userB = $this->buatUserMobile();

        Sanctum::actingAs($userA, ['mobile']);

        $this->getJson('/api/user-reports/' . $userB->id_akun)->assertStatus(403);
        $this->getJson('/api/user-reports-by-category/' . $userB->id_akun . '/dikirim')->assertStatus(403);
        $this->getJson('/api/search?keyword=uji&id_akun=' . $userB->id_akun)->assertStatus(403);
        $this->postJson('/api/update-fcm-token', [
            'user_id' => $userB->id_akun,
            'fcm_token' => 'token-palsu',
        ])->assertStatus(403);
    }

    public function test_registrasi_nomor_duplikat_ditolak_422(): void
    {
        $existing = $this->buatUserMobile();

        $this->postJson('/api/auth/register', [
            'notelp' => $existing->notelp,
            'nama' => 'Duplikat',
            'password' => 'rahasia123',
        ])->assertStatus(422)->assertJsonValidationErrors('notelp');
    }
}
