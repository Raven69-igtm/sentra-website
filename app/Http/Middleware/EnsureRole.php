<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Cek status aktif
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'login' => 'Akun Anda dinonaktifkan. Silakan hubungi Superadmin.',
            ]);
        }

        // Superadmin TIDAK lagi bypass: akses modul operasional mengikuti
        // role eksplisit (superadmin hanya untuk modul superadmin/*).
        // Expanded check: jika role yang diminta adalah 'admin', izinkan admin_ppa dan admin_dinsos
        if (in_array('admin', $roles, true) && in_array($user->role, ['admin', 'admin_ppa', 'admin_dinsos'], true)) {
            return $next($request);
        }

        if (in_array($user->role, $roles, true)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
    }
}
