<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpController extends Controller
{
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

    $minutes = (int) (\App\Models\SystemSetting::get('otp_expiry_minutes', 5));
    $otp = rand(100000, 999999);
    $expiry = Carbon::now()->addMinutes($minutes);

    DB::table('akun')->where('email', $request->email)->update([
        'otp' => $otp,
        'otp_expiry' => $expiry
    ]);

    Mail::raw("Kode OTP Anda adalah: $otp. Berlaku selama {$minutes} menit.", function ($message) use ($request) {
        $message->to($request->email)->subject('Reset Password - Kode OTP');
    });

    session(['email' => $request->email]);

    return redirect()->route('otp.form')->with('message', "Kode OTP telah dikirim ke email Anda (berlaku {$minutes} menit).");
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
    $otp = is_array($request->otp) ? implode('', $request->otp) : $request->otp;
    $request->merge(['otp' => $otp]);

    $request->validate([
        'email' => 'required|email',
        'otp' => 'required|digits:6',
    ]);

    $user = DB::table('akun')->where('email', $request->email)->first();

    if (!$user || $user->otp !== $request->otp) {
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

    // Simpan verifikasi ke session
    session([
        'verified_email' => $request->email,
        'email' => $request->email,
    ]);

    return redirect()->route('newpassword');
}
 }
