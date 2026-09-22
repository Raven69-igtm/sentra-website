<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Models\Notifikasi;
use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('id_akun', Auth::id())
                                ->where('tipe', 'admin')
                                ->where('status', 'terkirim')
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        return view('backend.layout.page_admin.notifikasi', compact('notifikasi'));
    }
    
    public function showNotifikasi()
    {
        $notifikasi = Notifikasi::where('id_akun', Auth::id())
                                ->where('tipe', 'admin')
                                ->where('status', 'terkirim')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
        
        $count = Notifikasi::where('id_akun', Auth::id())
                          ->where('tipe', 'admin')
                          ->where('status', 'terkirim')
                          ->count();
        
        return view('backend.components.notifikasi_dropdown', compact('notifikasi', 'count'));
    }
    
    // Mengubah status notifikasi menjadi dibaca (dengan AJAX support)
    public function markAsRead(Request $request, $id)
    {
        try {
            $notifikasi = Notifikasi::find($id);
            
            if ($notifikasi) {
                $notifikasi->status = 'dibaca';
                $notifikasi->save();
                
                // Jika request adalah AJAX, return JSON response
                if ($request->ajax()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Notifikasi telah ditandai sebagai dibaca!'
                    ]);
                }
                
                // Fallback untuk non-AJAX request
                return redirect()->back()->with('success', 'Notifikasi telah dibaca');
            }
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }
            
            return redirect()->back()->with('error', 'Notifikasi tidak ditemukan');
            
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Terjadi kesalahan!');
        }
    }
    
    // Menerima laporan dari notifikasi (dengan AJAX support)
public function terimaLaporan(Request $request, $id_notif, $id_laporan)
{
    try {
        \Log::info('Menerima laporan', ['id_notif' => $id_notif, 'id_laporan' => $id_laporan]); // Debugging

        // Update status notifikasi
        $notifikasi = Notifikasi::find($id_notif);
        if (!$notifikasi) {
            \Log::warning('Notifikasi tidak ditemukan', ['id_notif' => $id_notif]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }
            return redirect()->back()->with('error', 'Notifikasi tidak ditemukan');
        }
        $notifikasi->status = 'dibaca';
        $notifikasi->save();
        \Log::info('Notifikasi updated', ['id_notif' => $id_notif, 'status' => 'dibaca']);

        // Update status laporan
        $laporan = Laporan::find($id_laporan);
        if (!$laporan) {
            \Log::warning('Laporan tidak ditemukan', ['id_laporan' => $id_laporan]);
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan'
                ], 404);
            }
            return redirect()->back()->with('error', 'Laporan tidak ditemukan');
        }
        $laporan->status = 'diterima';
        $laporan->save();
        \Log::info('Laporan updated', ['id_laporan' => $id_laporan, 'status' => 'diterima']);

        ActivityLog::record('laporan_status', "Menerima laporan #{$laporan->id_laporan} melalui notifikasi (→ diterima).", 'laporan', $laporan->id_laporan);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil diterima!',
                'data' => [
                    'id_notif' => $id_notif,
                    'id_laporan' => $id_laporan
                ]
            ]);
        }
        
        return redirect()->back()->with('success', 'Laporan berhasil diterima');
    } catch (\Exception $e) {
        \Log::error('Error menerima laporan', ['error' => $e->getMessage(), 'id_notif' => $id_notif, 'id_laporan' => $id_laporan]);
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
        return redirect()->back()->with('error', 'Terjadi kesalahan!');
    }
}
    
    // Fungsi untuk mengambil jumlah notifikasi yang belum dibaca (untuk ajax minimal)
    public function getCount()
    {
        $count = Notifikasi::where('id_akun', Auth::id())
                          ->where('tipe', 'admin')
                          ->where('status', 'terkirim')
                          ->count();
        
        return response()->json(['count' => $count]);
    }
    
    // Method baru untuk mendapatkan notifikasi terbaru (untuk update dropdown via AJAX)
    public function getNotifikasi(Request $request)
    {
        $notifikasi = Notifikasi::where('id_akun', Auth::id())
                                ->where('tipe', 'admin')
                                ->where('status', 'terkirim')
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get();
        
        $count = Notifikasi::where('id_akun', Auth::id())
                          ->where('tipe', 'admin')
                          ->where('status', 'terkirim')
                          ->count();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'notifikasi' => $notifikasi,
                'count' => $count
            ]);
        }
        
        return compact('notifikasi', 'count');
    }
}