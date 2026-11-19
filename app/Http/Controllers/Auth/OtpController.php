<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Models\SystemSetting;

class OtpController extends Controller
{
    /** Batas percobaan verifikasi OTP untuk satu sesi. */
    private const MAX_VERIFY_ATTEMPTS = 5;

    /** Batas permintaan OTP dan jendela waktunya (detik). */
    private const MAX_SEND_ATTEMPTS = 3;
    private const SEND_DECAY_SECONDS = 300;

    // Kirim OTP ke email
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = DB::table('akun')->where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        if (!in_array($user->role, ['admin', 'admin_ppa', 'admin_dinsos', 'superadmin'], true)) {
            return redirect()->back()->withErrors(['email' => 'Hanya admin yang dapat melakukan reset password.']);
        }

        // Batasi permintaan OTP agar kode tidak dikirim berulang tanpa jeda.
        $throttleKey = 'otp-send:' . Str::transliterate(Str::lower($request->email)) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_SEND_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return redirect()->back()->withErrors([
                'email' => "Terlalu banyak permintaan kode OTP. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $minutes = (int) SystemSetting::get('otp_expiry_minutes', 5);
        $otp = (string) rand(100000, 999999);
        $expiry = Carbon::now()->addMinutes($minutes);

        DB::table('akun')->where('email', $request->email)->update([
            'otp' => $otp,
            'otp_expiry' => $expiry
        ]);

        try {
            Mail::raw("Kode OTP Anda adalah: {$otp}. Berlaku selama {$minutes} menit.", function ($message) use ($request) {
                $message->to($request->email)->subject('Reset Password - Kode OTP');
            });
        } catch (\Throwable $e) {
            // Jangan tinggalkan OTP aktif kalau emailnya tidak terkirim.
            DB::table('akun')->where('email', $request->email)->update([
                'otp' => null,
                'otp_expiry' => null,
            ]);

            Log::error('Gagal mengirim email OTP reset password.', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'email' => 'Gagal mengirim kode OTP ke email Anda. Silakan coba lagi atau hubungi administrator.',
            ]);
        }

        RateLimiter::hit($throttleKey, self::SEND_DECAY_SECONDS);

        // Sesi verifikasi lama tidak boleh dipakai ulang untuk OTP baru.
        session()->forget(['verified_email', 'verified_at', 'otp_attempts']);
        session(['email' => $request->email]);

        $message = "Kode OTP telah dikirim ke email Anda (berlaku {$minutes} menit).";

        // Tanpa konfigurasi MAIL_*, mailer default 'log' sehingga kode hanya
        // masuk berkas log. Beri petunjuk agar tidak dianggap gagal kirim.
        if (app()->environment('local') && config('mail.default') === 'log') {
            $message .= ' Mode pengembangan: kode OTP tercatat di storage/logs/laravel.log.';
        }

        return redirect()->route('otp.form')->with('message', $message);
    }



    // Tampilkan form OTP
    public function showOtpForm(Request $request)
    {
        
        $email = session('email') ?? $request->old('email');

        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Silakan masukkan email terlebih dahulu.']);
        }

        return view('backend.login.otp', ['email' => $email]);
    }

    // Verifikasi OTP dan arahkan ke form password baru
    public function verifyOtp(Request $request)
    {
        $otp = is_array($request->otp) ? implode('', $request->otp) : (string) $request->otp;
        $request->merge(['otp' => $otp]);

        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.digits' => 'Kode OTP harus 6 angka.',
        ]);

        $attempts = (int) session('otp_attempts', 0);

        if ($attempts >= self::MAX_VERIFY_ATTEMPTS) {
            return back()->withErrors([
                'otp' => 'Terlalu banyak percobaan. Silakan minta kode OTP baru.'
            ])->withInput();
        }

        $user = DB::table('akun')->where('email', $request->email)->first();

        // Bandingkan sebagai string: kolom otp varchar, input form selalu string.
        if (!$user || $user->otp === null || !hash_equals((string) $user->otp, $otp)) {
            session(['otp_attempts' => $attempts + 1]);

            return back()->withErrors(['otp' => 'Kode OTP salah.'])->withInput();
        }

        if (!$user->otp_expiry || Carbon::now()->greaterThan(Carbon::parse($user->otp_expiry))) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa.'])->withInput();
        }

        // Bersihkan OTP setelah berhasil (opsional tapi aman)
        DB::table('akun')->where('email', $request->email)->update([
            'otp' => null,
            'otp_expiry' => null,
        ]);

        // Simpan verifikasi ke session beserta waktunya agar bisa kedaluwarsa.
        session([
            'verified_email' => $request->email,
            'verified_at' => Carbon::now()->toIso8601String(),
            'email' => $request->email,
        ]);
        session()->forget('otp_attempts');

        return redirect()->route('password.new');
    }
}
