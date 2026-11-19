<?php

namespace Tests\Feature;

use App\Models\Akun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Alur lupa password berbasis OTP untuk ketiga akun admin
 * (superadmin, Admin PPA, Admin Dinsos):
 *   - OTP tersimpan dan dikirim lewat mailer aktif,
 *   - verifikasi memakai perbandingan string dan punya batas percobaan,
 *   - sesi verifikasi kedaluwarsa mengikuti setting otp_expiry_minutes,
 *   - reset password hanya untuk email yang benar-benar terverifikasi.
 */
class LupaPasswordOtpTest extends TestCase
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

    private function otpOf(string $email): ?string
    {
        return DB::table('akun')->where('email', $email)->value('otp');
    }

    private function passwordOf(string $email): string
    {
        return (string) DB::table('akun')->where('email', $email)->value('password');
    }

    public function test_otp_dikirim_untuk_ketiga_role_admin(): void
    {
        $akunAdmin = [
            ['superadmin', 'superadmin@sentra.com'],
            ['admin_ppa', 'admin.ppa@sentra.com'],
            ['admin_dinsos', 'admin.dinsos@sentra.com'],
        ];

        foreach ($akunAdmin as [$role, $email]) {
            $this->admin($role, $email);

            $this->post(route('send.otp'), ['email' => $email])
                ->assertRedirect(route('otp.form'))
                ->assertSessionHas('message');

            $this->assertNotNull($this->otpOf($email), "OTP untuk {$email} tidak tersimpan.");
            $this->assertSame($email, session('email'));
        }
    }

    public function test_role_selain_admin_tidak_bisa_meminta_otp(): void
    {
        $user = $this->admin('user', 'pelapor.uji@sentra.com');

        $this->from(route('resetpw'))
            ->post(route('send.otp'), ['email' => $user->email])
            ->assertRedirect(route('resetpw'))
            ->assertSessionHasErrors('email');

        $this->assertNull($this->otpOf($user->email));
    }

    public function test_otp_benar_membuka_form_password_baru(): void
    {
        $akun = $this->admin('admin_ppa', 'admin.ppa@sentra.com');

        $this->post(route('send.otp'), ['email' => $akun->email]);
        $otp = $this->otpOf($akun->email);

        $this->assertNotNull($otp);

        $this->post(route('otp.verify'), [
            'email' => $akun->email,
            'otp' => $otp,
        ])->assertRedirect(route('password.new'));

        $this->assertSame($akun->email, session('verified_email'));
        $this->assertNotNull(session('verified_at'));
        $this->assertNull($this->otpOf($akun->email), 'OTP harus dikosongkan setelah verifikasi.');
    }

    public function test_otp_salah_dibatasi_lima_percobaan(): void
    {
        $akun = $this->admin('admin_dinsos', 'admin.dinsos@sentra.com');

        $this->post(route('send.otp'), ['email' => $akun->email]);
        $otp = $this->otpOf($akun->email);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('otp.verify'), [
                'email' => $akun->email,
                'otp' => '000000',
            ])->assertSessionHasErrors('otp');
        }

        // Percobaan keenam ditolak walau kodenya benar, sesi tidak terverifikasi.
        $this->post(route('otp.verify'), [
            'email' => $akun->email,
            'otp' => $otp,
        ])->assertSessionHasErrors('otp');

        $this->assertNull(session('verified_email'));
    }

    public function test_reset_password_menolak_tanpa_sesi_verifikasi(): void
    {
        $akun = $this->admin('admin_ppa', 'admin.ppa@sentra.com');
        $passwordLama = $this->passwordOf($akun->email);

        $this->post(route('otp.reset'), [
            'email' => $akun->email,
            'password' => 'RahasiaBaru123',
            'password_confirmation' => 'RahasiaBaru123',
        ])->assertRedirect(route('otp.form'));

        $this->assertSame($passwordLama, $this->passwordOf($akun->email));
    }

    public function test_reset_password_menolak_email_berbeda_dari_sesi(): void
    {
        $akun = $this->admin('admin_ppa', 'admin.ppa@sentra.com');
        $akunLain = $this->admin('admin_dinsos', 'admin.dinsos@sentra.com');
        $passwordLama = $this->passwordOf($akunLain->email);

        $this->withSession([
            'verified_email' => $akun->email,
            'verified_at' => Carbon::now()->toIso8601String(),
        ])->post(route('otp.reset'), [
            'email' => $akunLain->email,
            'password' => 'RahasiaBaru123',
            'password_confirmation' => 'RahasiaBaru123',
        ])->assertRedirect(route('otp.form'));

        $this->assertSame($passwordLama, $this->passwordOf($akunLain->email));
    }

    public function test_sesi_verifikasi_kedaluwarsa_mengikuti_setting_otp(): void
    {
        $akun = $this->admin('superadmin', 'superadmin@sentra.com');

        $this->withSession([
            'verified_email' => $akun->email,
            'verified_at' => Carbon::now()->subMinutes(10)->toIso8601String(),
        ])->get(route('password.new'))
            ->assertRedirect(route('otp.form'))
            ->assertSessionHasErrors('otp');
    }

    public function test_reset_password_berhasil_lalu_bisa_login(): void
    {
        $akun = $this->admin('admin_dinsos', 'admin.dinsos@sentra.com');

        $this->withSession([
            'verified_email' => $akun->email,
            'verified_at' => Carbon::now()->toIso8601String(),
        ])->post(route('otp.reset'), [
            'email' => $akun->email,
            'password' => 'RahasiaBaru123',
            'password_confirmation' => 'RahasiaBaru123',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('message');

        $this->assertTrue(Hash::check('RahasiaBaru123', $this->passwordOf($akun->email)));
        $this->assertNull(session('verified_email'));
        $this->assertTrue(auth()->attempt(['email' => $akun->email, 'password' => 'RahasiaBaru123']));
    }
}
