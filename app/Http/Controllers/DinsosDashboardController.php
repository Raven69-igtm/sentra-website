<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;

class DinsosDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $base = Laporan::query()->forUser($user);

        $totalLaporan = (clone $base)->count();
        $menungguVerifikasi = (clone $base)
            ->whereIn('status', ['dikirim', 'diterima'])
            ->count();
        $sedangDiproses = (clone $base)
            ->where('status', 'diproses')
            ->count();
        $selesai = (clone $base)
            ->where('status', 'selesai')
            ->count();
        $ditolak = (clone $base)
            ->where('status', 'ditolak')
            ->count();

        $layananCounts = [];
        foreach (array_keys(Laporan::LAYANAN_PJSRK) as $layanan) {
            $layananCounts[$layanan] = (clone $base)
                ->where('layanan_pjsrk', $layanan)
                ->count();
        }

        // Laporan lama/rujukan PPA yang belum punya layanan tetap ditampilkan
        // pada kelompok Permasalahan Sosial agar tidak hilang dari dashboard.
        $tanpaLayanan = (clone $base)->whereNull('layanan_pjsrk')->count();
        $layananCounts['permasalahan_sosial'] += $tanpaLayanan;

        $layananMaks = max($layananCounts) ?: 1;

        $laporanTerbaru = (clone $base)
            ->with(['detail_pelapor', 'detail_kasus'])
            ->latest('created_at')
            ->limit(8)
            ->get();

        return view('backend.dinsos.dashboard', compact(
            'totalLaporan',
            'menungguVerifikasi',
            'sedangDiproses',
            'selesai',
            'ditolak',
            'layananCounts',
            'layananMaks',
            'laporanTerbaru'
        ));
    }
}
