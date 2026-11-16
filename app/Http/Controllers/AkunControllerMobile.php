<?php

namespace App\Http\Controllers;

use App\Models\UserMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use Log;

class AkunControllerMobile extends Controller
{
    // Ambil semua akun
    public function index()
    {
        $data = DB::table('akun')->get();
        return response()->json($data);
    }

    // Ambil akun berdasarkan ID 
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'notelp' => 'required',
            'password' => 'required'
        ]);

        $user = UserMobile::where('notelp', $credentials['notelp'])->first();
        Log::info($credentials);  // Log data credentials untuk pengecekan

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            Log::info($user); // Log user yang ditemukan untuk pengecekan

            return response()->json(['message' => 'No Telp atau password salah'], 401);

        }

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $user->createToken('mobile')->plainTextToken,
        ]);
    }
    public function emergencyCheck(Request $request)
    {
        $data = $request->validate([
            'notelp' => 'required',
            'answer' => 'required'
        ]);

        $user = UserMobile::where('notelp', $data['notelp'])
            ->where('answquest', $data['answer'])
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Jawaban tidak sesuai'], 400);
        }

        return response()->json(['message' => 'Verifikasi berhasil'],200);
    }
    public function getEmergencyQuestion(Request $request)
    {
        $request->validate([
            'notelp' => 'required|string|max:15'
        ]);

        $akun = DB::table('akun')
            ->where('notelp', $request->notelp)
            ->select('emerquest')
            ->first();

        if (!$akun) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor telepon tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'emerquest' => $akun->emerquest
        ]);
    }
    //Login google
    public function loginWithEmail(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
        ]);

        Log::info('Email login attempt:', ['email' => $credentials['email']]);
        $user = UserMobile::where('email', $credentials['email'])->first();
        if (!$user) {
            Log::info('User not found for email:', ['email' => $credentials['email']]);
            return response()->json(['message' => 'Email tidak ditemukan'], 404);
        }
        Log::info('User found:', ['user' => $user]);
        return response()->json([
            'message' => 'Login dengan email berhasil',
            'user' => $user,
            'token' => $user->createToken('mobile')->plainTextToken,
        ], 200);
    }
    //Login Biometric
    public function loginBiometric(Request $request)
    {
        $credentials = $request->validate([
            'notelp' => 'required|string',
        ]);

        Log::info('Biometric login attempt:', ['notelp' => $credentials['notelp']]);

        $user = UserMobile::where('notelp', $credentials['notelp'])->first();

        if (!$user) {
            Log::info('User not found for notelp:', ['notelp' => $credentials['notelp']]);
            return response()->json(['message' => 'Nomor telepon tidak ditemukan'], 404);
        }

        Log::info('User found:', ['user' => $user]);

        return response()->json([
            'message' => 'Login biometrik berhasil',
            'user' => $user,
            'token' => $user->createToken('mobile')->plainTextToken,
        ], 200);
    }

    // Tambah akun baru
    public function register(Request $request)
    {
        $request->validate([
            'notelp' => 'required|string|max:15',
            'nama' => 'required|string|max:50',
            'password' => 'required|string|min:6',
            'role' => 'in:user,admin,guest',
            'emerquest' => 'nullable|string|max:100',
            'answquest' => 'nullable|string|max:100',
            'gender' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();

        try {
            // Insert akun baru dan dapatkan ID
            $newAccountId = DB::table('akun')->insertGetId([
                'notelp' => $request->notelp,
                'nama' => $request->nama,
                'password' => Hash::make($request->password),
                'role' => $request->role ?? 'guest',
                'emerquest' => $request->emerquest,
                'answquest' => $request->answquest,
                'jenis_kelamin' => $request->gender,
                'alamat' => $request->alamat,
                'otp' => rand(100000, 999999),
                'otp_expiry' => Carbon::now()->addMinutes(5),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Cari laporan yang terkait dengan nomor telepon dan id_akun = 3 (Guest)
            $laporan = DB::table('laporan')
                ->join('detail_pelapor', 'laporan.id_laporan', '=', 'detail_pelapor.id_laporan')
                ->where('detail_pelapor.no_telp', $request->notelp)
                ->where('laporan.id_akun', 3)
                ->select('laporan.id_laporan')
                ->first();

            // Jika ditemukan laporan, update id_akun
            if ($laporan) {
                DB::table('laporan')
                    ->where('id_laporan', $laporan->id_laporan)
                    ->update([
                        'id_akun' => $newAccountId,
                        'updated_at' => now(),
                    ]);

                // Opsional: Tambahkan log atau notifikasi
                Log::info("Laporan dengan ID {$laporan->id_laporan} diperbarui dari Guest (3) ke akun baru ({$newAccountId}).");
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Akun berhasil ditambahkan',
                'data' => [
                    'id_akun' => $newAccountId,
                    'laporan_updated' => $laporan ? true : false,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat akun.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update akun
    public function update(Request $request, $id)
    {
        $akun = DB::table('akun')->where('id_akun', $id)->first();
        if (!$akun) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        $request->validate([
            'notelp' => 'nullable|string|max:15',
            'nama' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'jenis_kelamin' => 'nullable|string|max:100',
            'alamat' => 'nullable|string|max:100',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|in:user,admin,guest',
            'emerquest' => 'nullable|string|max:100',
            'answquest' => 'nullable|string|max:100',
        ]);

        DB::table('akun')->where('id_akun', $id)->update([
            'notelp' => $request->notelp ?? $akun->notelp,
            'nama' => $request->nama ?? $akun->nama,
            'email' => $request->email ?? $akun->email,
            'jenis_kelamin' => $request->jenis_kelamin ?? $akun->jenis_kelamin,
            'alamat' => $request->alamat ?? $akun->alamat,
            'password' => $request->password ? Hash::make($request->password) : $akun->password,
            'role' => $request->role ?? $akun->role,
            'emerquest' => $request->emerquest ?? $akun->emerquest,
            'answquest' => $request->answquest ?? $akun->answquest,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Akun berhasil diperbarui']);
    }

    // Hapus akun
    public function delete($id)
    {
        $akun = DB::table('akun')->where('id_akun', $id)->first();
        if (!$akun) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        DB::table('akun')->where('id_akun', $id)->delete();
        return response()->json(['message' => 'Akun berhasil dihapus']);
    }
    // Fungsi untuk update nomor telepon setelah verifikasi jawabannya
    public function updatenomor(Request $request)
    {
        // Validasi input
        $request->validate([
            'notelp_lama' => 'required|string|max:15', 
            'notelp_baru' => 'required|string|max:15',
            'answquest' => 'required|string|max:100', 
        ]);

        $akun = DB::table('akun')->where('notelp', $request->notelp_lama)->first();

        if (!$akun) {
            return response()->json(['message' => 'Nomor telepon lama tidak ditemukan'], 404);
        }

        if ($akun->answquest !== $request->answquest) {
            return response()->json(['message' => 'Jawaban pertanyaan darurat salah'], 401);
        }

        
        DB::table('akun')->where('id_akun', $akun->id_akun)->update([
            'notelp' => $request->notelp_baru,
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Nomor telepon berhasil diperbarui']);
    }
    // Fungsi untuk update password
    public function updatepassword(Request $request)
    {
        // Validasi input
        $request->validate([
            'notelp' => 'required|string|max:15',        // Nomor telepon yang digunakan untuk update password
            'password' => 'required|string|min:6',       // Password baru
            'password2' => 'required|string|min:6|same:password', // Konfirmasi password
        ]);

        // Cari akun berdasarkan nomor telepon
        $akun = DB::table('akun')->where('notelp', $request->notelp)->first();

        // Cek apakah akun ditemukan
        if (!$akun) {
            return response()->json(['message' => 'Nomor telepon tidak ditemukan'], 404);
        }

        // Update password akun dengan hash yang baru
        DB::table('akun')->where('id_akun', $akun->id_akun)->update([
            'password' => Hash::make($request->password), // Menggunakan Hash untuk mengamankan password
            'updated_at' => now(), // Waktu update
        ]);

        return response()->json(['message' => 'Password berhasil diperbarui']);
    }


}
