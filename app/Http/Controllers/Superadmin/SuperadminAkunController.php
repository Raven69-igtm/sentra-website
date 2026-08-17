<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperadminAkunController extends Controller
{
    public function index(Request $request)
    {
        $query = Akun::query();

        // Filter role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter status aktif
        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('is_active', true);
            } elseif ($request->status === 'nonaktif') {
                $query->where('is_active', false);
            }
        }

        // Search nama atau email
        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $akunList = $query->orderBy('id_akun', 'desc')->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('backend.superadmin.akun.index', compact('akunList', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('backend.superadmin.akun.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'email' => 'required|email|max:100|unique:akun,email',
            'notelp' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'role' => 'required|string|max:30|exists:roles,name',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'alamat' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $akun = Akun::create($validated);

        ActivityLog::record('data', "Membuat akun baru '{$akun->nama}' ({$akun->email}) dengan role {$akun->role}.", 'akun', "akun-{$akun->id_akun}");

        return redirect()->route('superadmin.akun.index')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function edit($id)
    {
        $akun = Akun::findOrFail($id);
        $roles = Role::all();
        return view('backend.superadmin.akun.edit', compact('akun', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $akun = Akun::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:50',
            'email' => ['required', 'email', 'max:100', Rule::unique('akun', 'email')->ignore($akun->id_akun, 'id_akun')],
            'notelp' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|max:30|exists:roles,name',
            'jenis_kelamin' => 'nullable|in:Pria,Wanita',
            'alamat' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = $request->boolean('is_active', true);

        // Proteksi: jangan sampai tidak ada superadmin aktif
        if ($akun->role === 'superadmin' && ($validated['role'] !== 'superadmin' || !$isActive)) {
            $totalSuperadmin = Akun::where('role', 'superadmin')->where('is_active', true)->count();
            if ($totalSuperadmin <= 1) {
                return back()->with('error', 'Gagal: Sistem harus memiliki setidaknya satu Superadmin aktif.');
            }
        }

        $akun->nama = $validated['nama'];
        $akun->email = $validated['email'];
        $akun->notelp = $validated['notelp'];
        $akun->role = $validated['role'];
        $akun->jenis_kelamin = $validated['jenis_kelamin'] ?? null;
        $akun->alamat = $validated['alamat'] ?? null;
        $akun->is_active = $isActive;

        if (!empty($validated['password'])) {
            $akun->password = $validated['password']; // Mutator akan hash otomatis
        }

        $akun->save();

        ActivityLog::record('data', "Memperbarui data akun '{$akun->nama}' ({$akun->email}, role {$akun->role}).", 'akun', "akun-{$akun->id_akun}");

        return redirect()->route('superadmin.akun.index')
            ->with('success', 'Data akun berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $akun = Akun::findOrFail($id);

        // Proteksi 1: Tidak boleh menghapus akun sendiri
        if (Auth::id() == $akun->id_akun) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Proteksi 2: Tidak boleh menghapus satu-satunya superadmin
        if ($akun->role === 'superadmin') {
            $totalSuperadmin = Akun::where('role', 'superadmin')->count();
            if ($totalSuperadmin <= 1) {
                return back()->with('error', 'Superadmin terakhir tidak dapat dihapus.');
            }
        }

        $akun->delete();

        ActivityLog::record('data', "Menghapus akun '{$akun->nama}' ({$akun->email}, role {$akun->role}).", 'akun', "akun-{$akun->id_akun}");

        return redirect()->route('superadmin.akun.index')
            ->with('success', 'Akun berhasil dihapus.');
    }

    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $akun = Akun::findOrFail($id);
        $akun->password = $request->new_password; // Mutator otomatis hash
        $akun->save();

        ActivityLog::record('data', "Me-reset password akun '{$akun->nama}' ({$akun->email}).", 'akun', "akun-{$akun->id_akun}");

        return back()->with('success', "Password untuk akun {$akun->nama} berhasil direset.");
    }

    public function toggleStatus($id)
    {
        $akun = Akun::findOrFail($id);

        if (Auth::id() == $akun->id_akun) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        if ($akun->role === 'superadmin' && $akun->is_active) {
            $totalSuperadmin = Akun::where('role', 'superadmin')->where('is_active', true)->count();
            if ($totalSuperadmin <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan Superadmin aktif terakhir.');
            }
        }

        $akun->is_active = !$akun->is_active;
        $akun->save();

        $statusText = $akun->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLog::record('data', "Akun '{$akun->nama}' ({$akun->email}) {$statusText}.", 'akun', "akun-{$akun->id_akun}");

        return back()->with('success', "Akun {$akun->nama} berhasil {$statusText}.");
    }
}
