<?php

namespace App\Http\Controllers\Auth;

use App\Models\ActivityLog;
use App\Models\Akun;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
            $this->recordFailedLogin('throttled');
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
                $this->recordFailedLogin('inactive', $user);
                return back()->withErrors([
                    'login' => 'Akun Anda dinonaktifkan. Silakan hubungi Superadmin.',
                ]);
            }

            // Cek role: hanya admin (admin, admin_ppa, admin_dinsos) dan superadmin yang boleh masuk web panel
            if (! $user->canAccessWeb()) {
                Auth::logout(); // Logout langsung
                $this->recordFailedLogin('role', $user);
                return back()->withErrors([
                    'login' => 'Akses hanya khusus staf dan admin.',
                ]);
            }

            $request->session()->regenerate();

            // Login berhasil tidak dicatat ke activity_logs (terlalu bising);
            // metadata login disimpan langsung pada akun.
            $user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ])->save();

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

        $this->recordFailedLogin('credentials');

        return back()->withErrors([
            'login' => 'Email atau password Anda salah.',
        ]);
    }

    /**
     * Catat login gagal dengan deduplikasi 5 menit per email + IP + alasan.
     */
    private function recordFailedLogin(string $reason, ?Akun $user = null): void
    {
        $email = mb_strtolower((string) ($user->email ?? request()->input('email')));
        $ip = (string) request()->ip();
        $cacheKey = 'audit:login-failed:' . sha1($reason . '|' . $email . '|' . $ip);

        if (! Cache::add($cacheKey, true, now()->addMinutes(5))) {
            return;
        }

        $description = match ($reason) {
            'throttled' => "Percobaan login ditolak: terlalu banyak percobaan gagal (email: {$email}).",
            'inactive'  => "Percobaan login ditolak: akun dinonaktifkan ({$user->nama}).",
            'role'      => "Percobaan login ditolak: role tidak diizinkan masuk panel web ({$user->role}).",
            default     => "Percobaan login gagal: email atau password salah (email: {$email}).",
        };

        ActivityLog::record('login', $description, null, null, 'gagal', $user);
    }
}
