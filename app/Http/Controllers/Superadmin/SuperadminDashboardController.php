<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemSetting;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperadminDashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Akun
        $totalAkun = Akun::count();
        $totalSuperadmin = Akun::where('role', 'superadmin')->count();
        $totalAdmin = Akun::whereIn('role', ['admin', 'admin_ppa', 'admin_dinsos'])->count();
        $totalAdminPPA = Akun::whereIn('role', ['admin', 'admin_ppa'])->count();
        $totalAdminDinsos = Akun::where('role', 'admin_dinsos')->count();
        $totalUser = Akun::where('role', 'user')->count();
        $totalGuest = Akun::where('role', 'guest')->count();
        $totalNonAktif = Akun::where('is_active', false)->count();

        // 2. Statistik Role & Permission
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        // 3. Statistik Laporan Berdasarkan Instansi
        $totalLaporan = Laporan::count();
        $totalLaporanPPA = Laporan::whereIn('kategori', [
            'Kekerasan Fisik',
            'Kekerasan Psikis',
            'Kekerasan Seksual',
            'Eksploitasi',
            'TPPO'
        ])->count();

        $totalLaporanDinsos = Laporan::where(function ($q) {
            $q->whereIn('layanan_pjsrk', array_keys(Laporan::LAYANAN_PJSRK))
                ->orWhere('status', 'dirujuk')
                ->orWhere(function ($legacy) {
                    $legacy->whereNull('layanan_pjsrk')
                        ->whereIn('kategori', Laporan::DINSOS_CATEGORIES);
                });
        })->count();

        $totalLaporanSelesai = Laporan::where('status', 'selesai')->count();
        $totalLaporanDirujuk = Laporan::where('status', 'dirujuk')->count();

        // 4. Data Chart 12 Bulan (Tahun Berjalan)
        $currentYear = Carbon::now()->year;
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $chartDataPPA = [];
        $chartDataDinsos = [];
        $chartDataTotal = [];

        for ($m = 1; $m <= 12; $m++) {
            $countPPA = Laporan::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $m)
                ->whereIn('kategori', ['Kekerasan Fisik', 'Kekerasan Psikis', 'Kekerasan Seksual', 'Eksploitasi', 'TPPO'])
                ->count();

            $countDinsos = Laporan::whereYear('created_at', $currentYear)
                ->whereMonth('created_at', $m)
                ->where(function ($q) {
                    $q->whereIn('layanan_pjsrk', array_keys(Laporan::LAYANAN_PJSRK))
                        ->orWhere('status', 'dirujuk')
                        ->orWhere(function ($legacy) {
                            $legacy->whereNull('layanan_pjsrk')
                                ->whereIn('kategori', Laporan::DINSOS_CATEGORIES);
                        });
                })
                ->count();

            $chartDataPPA[] = $countPPA;
            $chartDataDinsos[] = $countDinsos;
            $chartDataTotal[] = $countPPA + $countDinsos;
        }

        $currentMonthIndex = Carbon::now()->month - 1;
        $currentMonthName = $months[$currentMonthIndex] ?? 'Aug';
        $currentMonthTotal = $chartDataTotal[$currentMonthIndex] ?? 0;

        // 5. Aktivitas Terkini (Kombinasi Laporan Terbaru & Akun Pendaftar)
        $latestReports = Laporan::with('detail_pelapor')
            ->latest('created_at')
            ->limit(4)
            ->get()
            ->map(function ($l) {
                $isPPA = in_array($l->kategori, ['Kekerasan Fisik', 'Kekerasan Psikis', 'Kekerasan Seksual', 'Eksploitasi', 'TPPO']);
                return (object) [
                    'type' => 'laporan',
                    'icon' => $isPPA ? 'fa-shield' : 'fa-handshake-o',
                    'icon_color' => $isPPA ? 'text-primary' : 'text-warning',
                    'bg_color' => $isPPA ? 'bg-primary-light' : 'bg-warning-light',
                    'title' => "Laporan: {$l->kategori}",
                    'subtitle' => 'Pelapor: ' . ($l->detail_pelapor->nama ?? 'Anonim') . " (#{$l->id_laporan})",
                    'date' => $l->created_at ? $l->created_at->translatedFormat('d M Y, H:i') : '-',
                    'instansi' => $isPPA ? 'UPTD PPA' : 'Dinas Sosial',
                    'status' => ucfirst($l->status),
                    'status_class' => match ($l->status) {
                        'selesai' => 'badge-success',
                        'dirujuk' => 'badge-warning',
                        'diproses' => 'badge-info',
                        default => 'badge-secondary',
                    },
                    'created_at' => $l->created_at,
                ];
            });

        $latestUsers = Akun::latest('created_at')
            ->limit(3)
            ->get()
            ->map(function ($u) {
                return (object) [
                    'type' => 'akun',
                    'icon' => 'fa-user-plus',
                    'icon_color' => 'text-success',
                    'bg_color' => 'bg-success-light',
                    'title' => "Akun Baru: {$u->nama}",
                    'subtitle' => $u->email ?? $u->notelp,
                    'date' => $u->created_at ? $u->created_at->translatedFormat('d M Y, H:i') : '-',
                    'instansi' => match ($u->role) {
                        'superadmin' => 'Root System',
                        'admin_ppa', 'admin' => 'Admin PPA',
                        'admin_dinsos' => 'Admin Dinsos',
                        default => 'Masyarakat',
                    },
                    'status' => ($u->is_active ?? true) ? 'Aktif' : 'Nonaktif',
                    'status_class' => ($u->is_active ?? true) ? 'badge-success' : 'badge-danger',
                    'created_at' => $u->created_at,
                ];
            });

        $recentActivities = $latestReports->concat($latestUsers)
            ->sortByDesc('created_at')
            ->values()
            ->take(5);

        // 6. Ringkasan Pengaturan Sistem Utama
        $appName = SystemSetting::get('app_name', 'SENTRA');
        $instansi = SystemSetting::get('instansi_name', 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak');
        $hotline = SystemSetting::get('hotline_number', '0812-3456-7890');
        $reportSla = SystemSetting::get('report_sla_days', '3');
        $sessionLifetime = config('session.lifetime', 120);

        return view('backend.superadmin.dashboard', compact(
            'totalAkun',
            'totalSuperadmin',
            'totalAdmin',
            'totalAdminPPA',
            'totalAdminDinsos',
            'totalUser',
            'totalGuest',
            'totalNonAktif',
            'totalRoles',
            'totalPermissions',
            'totalLaporan',
            'totalLaporanPPA',
            'totalLaporanDinsos',
            'totalLaporanSelesai',
            'totalLaporanDirujuk',
            'months',
            'chartDataPPA',
            'chartDataDinsos',
            'chartDataTotal',
            'currentMonthName',
            'currentMonthTotal',
            'recentActivities',
            'appName',
            'instansi',
            'hotline',
            'reportSla',
            'sessionLifetime'
        ));
    }
}
