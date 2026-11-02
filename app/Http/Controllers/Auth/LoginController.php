<?php

namespace App\Http\Controllers\Auth;

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

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Cek status aktif
            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Akun Anda dinonaktifkan. Silakan hubungi Superadmin.',
                ]);
            }

            // Cek role: hanya admin (admin, admin_ppa, admin_dinsos) dan superadmin yang boleh masuk web panel
            if (!in_array($user->role, ['admin', 'admin_ppa', 'admin_dinsos', 'superadmin'], true)) {
                Auth::logout(); // Logout langsung
                return back()->withErrors([
                    'login' => 'Akses hanya khusus staf dan admin.',
                ]);
            }

            $request->session()->regenerate();

            if ($user->role === 'superadmin') {
                return redirect()->route('superadmin.dashboard');
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'login' => 'Email atau password Anda salah.',
        ]);
    }
}
