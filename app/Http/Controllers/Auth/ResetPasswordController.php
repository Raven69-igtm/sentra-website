<?php

namespace App\Http\Controllers\Auth;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    /**
     * Email yang sudah terverifikasi OTP, atau null bila sesinya tidak ada
     * maupun sudah lewat masa berlaku (mengikuti setting otp_expiry_minutes).
     */
    private function verifiedEmail(): ?string
    {
        $email = session('verified_email');
        $verifiedAt = session('verified_at');

        if (!$email || !$verifiedAt) {
            return null;
        }

        $minutes = (int) SystemSetting::get('otp_expiry_minutes', 5);

        if (Carbon::parse($verifiedAt)->addMinutes($minutes)->isPast()) {
            return null;
        }

        return $email;
    }

    // Tampilkan form untuk mengisi password baru
    public function showNewPasswordForm()
    {
        $email = $this->verifiedEmail();

        if (!$email) {
            return redirect()->route('otp.form')->withErrors([
                'otp' => 'Sesi verifikasi kedaluwarsa, silakan minta kode OTP baru.'
            ]);
        }

        return view('backend.login.new_password', ['email' => $email]);
    }

    // Simpan password baru ke database
    public function reset(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixed' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers' => 'Password harus mengandung angka.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $email = $this->verifiedEmail();

        // Email yang diproses harus sama dengan yang diverifikasi OTP.
        if (!$email || strcasecmp($email, (string) $request->email) !== 0) {
            return redirect()->route('otp.form')->withErrors([
                'otp' => 'Sesi verifikasi tidak valid, silakan ulangi proses.'
            ]);
        }

        // Update password di tabel akun
        DB::table('akun')->where('email', $email)->update([
            'password' => Hash::make($request->password),
            'otp' => null,
            'otp_expiry' => null,
        ]);

        // Hapus session setelah berhasil reset
        session()->forget(['verified_email', 'verified_at', 'email', 'otp_attempts']);

        return redirect()->route('login')->with('message', 'Password berhasil direset. Silakan login.');
    }
}
