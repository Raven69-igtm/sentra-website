<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SuperadminProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return view('backend.superadmin.profile.edit', compact('user'));
    }

    /**
     * Perbarui data profil milik sendiri (nama, email, nomor telepon).
     * Role & status akun sengaja TIDAK dapat diubah dari halaman ini agar
     * superadmin tidak bisa menurunkan wewenangnya sendiri secara tidak sengaja.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:100', Rule::unique('akun', 'email')->ignore($user->id_akun, 'id_akun')],
            'notelp' => 'required|string|max:20',
        ], [
            'nama.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh akun lain.',
            'notelp.required' => 'Nomor telepon wajib diisi.',
            'notelp.max' => 'Nomor telepon maksimal 20 karakter.',
        ]);

        $user->nama = $validated['nama'];
        $user->email = $validated['email'];
        $user->notelp = $validated['notelp'];
        $user->save();

        ActivityLog::record('data', "Memperbarui profil sendiri ({$user->email}).", 'akun', "akun-{$user->id_akun}");

        return redirect()->route('superadmin.profile.edit')
            ->with('success', 'Data profil berhasil diperbarui.');
    }

    /**
     * Ganti password milik sendiri — wajib verifikasi password saat ini.
     * Form berbasis halaman (bukan modal) karena alur modal lama redirect
     * ke route('dashboard') yang tidak dapat diakses superadmin.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.letters' => 'Password harus mengandung huruf.',
            'password.mixed' => 'Password harus mengandung huruf besar dan kecil.',
            'password.numbers' => 'Password harus mengandung angka.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai.'])
                ->withInput();
        }

        $user->password = $request->password; // Mutator Akun akan hash otomatis
        $user->save();

        ActivityLog::record('data', 'Mengubah password akun sendiri.', 'akun', "akun-{$user->id_akun}");

        return redirect()->route('superadmin.profile.edit')
            ->with('success', 'Password berhasil diubah.');
    }
}
