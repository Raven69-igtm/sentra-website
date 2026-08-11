<?php

namespace App\Http\Controllers\Auth;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    function login()
    {
        return view('backend.login.login');
    }

    function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Proteksi brute force berbasis konfigurasi max_login_attempts
        $maxAttempts = (int) (\App\Models\SystemSetting::get('max_login_attempts', 5));
        $throttleKey = \Illuminate\Support\Str::transliterate(
            \Illuminate\Support\Str::lower($request->input('email')) . '|' . $request->ip()
        );

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            ActivityLog::record('login', "Percobaan login ditolak: terlalu banyak percobaan gagal (email: {$credentials['email']}).", null, null, 'gagal');
            return back()->withErrors([
                'login' => "Terlalu banyak percobaan login salah. Akun ditangguhkan sementara selama {$seconds} detik.",
            ]);
        }

        if (Auth::attempt($credentials)) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);

            $user = Auth::user();

            // Cek status aktif
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                ActivityLog::record('login', "Percobaan login ditolak: akun dinonaktifkan ({$user->nama}).", null, null, 'gagal', $user);
                return back()->withErrors([
                    'login' => 'Akun Anda dinonaktifkan. Silakan hubungi Superadmin.',
                ]);
            }

            // Cek role: hanya admin (admin, admin_ppa, admin_dinsos) dan superadmin yang boleh masuk web panel
            if (!in_array($user->role, ['admin', 'admin_ppa', 'admin_dinsos', 'superadmin'], true)) {
                Auth::logout(); // Logout langsung
                ActivityLog::record('login', "Percobaan login ditolak: role tidak diizinkan masuk panel web ({$user->role}).", null, null, 'gagal', $user);
                return back()->withErrors([
                    'login' => 'Akses hanya khusus staf dan admin.',
                ]);
            }

            $request->session()->regenerate();

            ActivityLog::record('login', 'Login berhasil ke panel ' . ActivityLog::instansiForRole($user->role) . '.', null, null, 'berhasil', $user);

            if ($user->role === 'superadmin') {
                // Penanda sekali-pakai: animasi pembuka brand (mask reveal)
                // hanya diputar pada halaman pertama setelah login, sehingga
                // perpindahan halaman berikutnya tidak dibebani animasi.
                $request->session()->put('superadmin_brand_intro', true);

                return redirect()->route('superadmin.dashboard');
            }

            if ($user->role === 'admin_dinsos') {
                return redirect()->route('dinsos.dashboard');
            }

            return redirect()->route('dashboard');
        }

        \Illuminate\Support\Facades\RateLimiter::hit($throttleKey, 60);

        ActivityLog::record('login', "Percobaan login gagal: email atau password salah (email: {$request->email}).", null, null, 'gagal');

        return back()->withErrors([
            'login' => 'Email atau password Anda salah.',
        ]);
    }
}
