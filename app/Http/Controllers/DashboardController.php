<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Mendapatkan tahun dari request atau menggunakan tahun saat ini
        $tahun_aktif = $request->input('tahun', Carbon::now()->year);
        
        // Mendapatkan list tahun untuk filter (5 tahun ke belakang)
        $tahun_list = [];
        $tahun_sekarang = Carbon::now()->year;
        for ($i = 0; $i <= 5; $i++) {
            $tahun_list[] = $tahun_sekarang - $i;
        }
        
        // Mengambil data bulan untuk chart
        $bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        // Inisialisasi array data untuk setiap kategori (untuk chart bulanan)
        $data_kekerasan_fisik = [];
        $data_kekerasan_psikis = [];
        $data_kekerasan_seksual = [];
        $data_penelantaran = [];
        $data_eksploitasi = [];
        $data_tppo = [];
        
        // Mengambil data laporan untuk setiap bulan dan setiap kategori dengan status selesai
        for ($i = 0; $i < 12; $i++) {
            // Kekerasan Fisik
            $data_kekerasan_fisik[$i] = DB::table('laporan')
                ->where('kategori', 'Kekerasan Fisik')
                ->whereIn('status', ['selesai', 'dirujuk'])
                ->whereMonth('created_at', $i + 1)
                ->whereYear('created_at', $tahun_aktif)
                ->count();
            
            // Kekerasan Psikis
            $data_kekerasan_psikis[$i] = DB::table('laporan')
                ->where('kategori', 'Kekerasan Psikis')
                ->whereIn('status', ['selesai', 'dirujuk'])
                ->whereMonth('created_at', $i + 1)
                ->whereYear('created_at', $tahun_aktif)
                ->count();
            
            // Kekerasan Seksual
            $data_kekerasan_seksual[$i] = DB::table('laporan')
                ->where('kategori', 'Kekerasan Seksual')
                ->whereIn('status', ['selesai', 'dirujuk'])
                ->whereMonth('created_at', $i + 1)
                ->whereYear('created_at', $tahun_aktif)
                ->count();
            
            // Penelantaran
            $data_penelantaran[$i] = DB::table('laporan')
                ->where('kategori', 'Penelantaran')
                ->whereIn('status', ['selesai', 'dirujuk'])
                ->whereMonth('created_at', $i + 1)
                ->whereYear('created_at', $tahun_aktif)
                ->count();
            
            // Eksploitasi
            $data_eksploitasi[$i] = DB::table('laporan')
                ->where('kategori', 'Eksploitasi')
                ->whereIn('status', ['selesai', 'dirujuk'])
                ->whereMonth('created_at', $i + 1)
                ->whereYear('created_at', $tahun_aktif)
                ->count();
        
            // TPPO
            $data_tppo[$i] = DB::table('laporan')
                ->where('kategori', 'TPPO')
                ->whereIn('status', ['selesai', 'dirujuk'])
                ->whereMonth('created_at', $i + 1)
                ->whereYear('created_at', $tahun_aktif)
                ->count();
        }
        
        // Menghitung total laporan dengan status selesai untuk setiap kategori dalam tahun aktif
        $total_kekerasan_fisik = DB::table('laporan')
            ->where('kategori', 'Kekerasan Fisik')
            ->whereIn('status', ['selesai', 'dirujuk'])
            ->whereYear('created_at', $tahun_aktif)
            ->count();
            
        $kekerasan_psikis = DB::table('laporan')
            ->where('kategori', 'Kekerasan Psikis')
            ->whereIn('status', ['selesai', 'dirujuk'])
            ->whereYear('created_at', $tahun_aktif)
            ->count();
            
        $kekerasan_seksual = DB::table('laporan')
            ->where('kategori', 'Kekerasan Seksual')
            ->whereIn('status', ['selesai', 'dirujuk'])
            ->whereYear('created_at', $tahun_aktif)
            ->count();
            
        $penelantaran = DB::table('laporan')
            ->where('kategori', 'Penelantaran')
            ->whereIn('status', ['selesai', 'dirujuk'])
            ->whereYear('created_at', $tahun_aktif)
            ->count();
            
        $eksploitasi = DB::table('laporan')
            ->where('kategori', 'Eksploitasi')
            ->whereIn('status', ['selesai', 'dirujuk'])
            ->whereYear('created_at', $tahun_aktif)
            ->count();
            
        $tppo = DB::table('laporan')
            ->where('kategori', 'TPPO')
            ->whereIn('status', ['selesai', 'dirujuk'])
            ->whereYear('created_at', $tahun_aktif)
            ->count();
            
        // Menghitung total semua laporan selesai untuk persentase
        $total_laporan = $total_kekerasan_fisik + $kekerasan_psikis + $kekerasan_seksual + 
                         $penelantaran + $eksploitasi + $tppo;
        
        // Menghitung persentase untuk setiap kategori
        $persen_kekerasan_fisik = $total_laporan > 0 ? ($total_kekerasan_fisik / $total_laporan) * 100 : 0;
        $persen_kekerasan_psikis = $total_laporan > 0 ? ($kekerasan_psikis / $total_laporan) * 100 : 0;
        $persen_kekerasan_seksual = $total_laporan > 0 ? ($kekerasan_seksual / $total_laporan) * 100 : 0;
        $persen_penelantaran = $total_laporan > 0 ? ($penelantaran / $total_laporan) * 100 : 0;
        $persen_eksploitasi = $total_laporan > 0 ? ($eksploitasi / $total_laporan) * 100 : 0;
        $persen_tppo = $total_laporan > 0 ? ($tppo / $total_laporan) * 100 : 0;
        
        return view('backend.layout.page_admin.dashboard', [
            'tahun_aktif' => $tahun_aktif,
            'tahun_list' => $tahun_list,
            'bulan' => $bulan,
            // Data untuk chart bulanan
            'data_kekerasan_fisik' => $data_kekerasan_fisik,
            'data_kekerasan_psikis' => $data_kekerasan_psikis,
            'data_kekerasan_seksual' => $data_kekerasan_seksual,
            'data_penelantaran' => $data_penelantaran,
            'data_eksploitasi' => $data_eksploitasi,
            'data_tppo' => $data_tppo,
            // Data untuk widget panel (menggunakan nama yang berbeda untuk total_kekerasan_fisik)
            'data_kekerasan_fisik_total' => $total_kekerasan_fisik, // Mengubah key agar tidak konflik
            'kekerasan_psikis' => $kekerasan_psikis,
            'kekerasan_seksual' => $kekerasan_seksual,
            'penelantaran' => $penelantaran,
            'eksploitasi' => $eksploitasi,
            'tppo' => $tppo,
            'persen_kekerasan_fisik' => round($persen_kekerasan_fisik, 1),
            'persen_kekerasan_psikis' => round($persen_kekerasan_psikis, 1),
            'persen_kekerasan_seksual' => round($persen_kekerasan_seksual, 1),
            'persen_penelantaran' => round($persen_penelantaran, 1),
            'persen_eksploitasi' => round($persen_eksploitasi, 1),
            'persen_tppo' => round($persen_tppo, 1),
        ]);
    }
}