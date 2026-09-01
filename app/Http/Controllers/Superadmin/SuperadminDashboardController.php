<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SuperadminDashboardController extends Controller
{
    public function index(Request $request)
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

        // 4. Data Chart 12 Bulan (Tahun Terpilih via filter ?tahun=)
        $now = Carbon::now();
        $currentYear = (int) $now->year;
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Daftar tahun yang memiliki data laporan + tahun berjalan
        $availableYears = Laporan::query()
            ->selectRaw('YEAR(created_at) as tahun')
            ->groupBy(DB::raw('YEAR(created_at)'))
            ->pluck('tahun')
            ->map(fn ($y) => (int) $y)
            ->push($currentYear)
            ->unique()
            ->sortDesc()
            ->values();

        // Validasi tahun dari query string; fallback ke tahun berjalan
        $requestedYear = (int) $request->query('tahun', $currentYear);
        $tahun = $availableYears->contains($requestedYear) ? $requestedYear : $currentYear;

        // Agregasi 1 query: total kasus masuk & jumlah dirujuk per bulan
        $monthlyRows = Laporan::query()
            ->selectRaw("MONTH(created_at) AS bulan, COUNT(*) AS total_masuk, SUM(CASE WHEN status = 'dirujuk' THEN 1 ELSE 0 END) AS total_dirujuk")
            ->whereYear('created_at', $tahun)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy('bulan');

        $chartDataMasuk = [];
        $chartDataRujukan = [];
        for ($m = 1; $m <= 12; $m++) {
            $row = $monthlyRows->get($m);
            $chartDataMasuk[] = $row ? (int) $row->total_masuk : 0;
            $chartDataRujukan[] = $row ? (int) $row->total_dirujuk : 0;
        }

        // Bulan yang disorot: bulan berjalan untuk tahun berjalan,
        // sedangkan untuk tahun lampau = bulan terakhir yang memiliki data.
        if ($tahun === $currentYear) {
            $highlightIndex = (int) $now->month - 1;
        } else {
            $highlightIndex = 0;
            for ($m = 11; $m >= 0; $m--) {
                if ($chartDataMasuk[$m] > 0 || $chartDataRujukan[$m] > 0) {
                    $highlightIndex = $m;
                    break;
                }
            }
        }
        $highlightName = $months[$highlightIndex];
        $highlightMasuk = $chartDataMasuk[$highlightIndex];
        $highlightRujukan = $chartDataRujukan[$highlightIndex];

        // 5. Aktivitas Terkini (metadata aktivitas sistem — tanpa isi laporan / identitas pelapor)
        $latestActivities = ActivityLog::latest('created_at')
            ->limit(4)
            ->get()
            ->map(function ($a) {
                [$icon, $iconColor, $bgColor] = match ($a->activity_type) {
                    'login', 'logout' => ['fa-sign-in', 'text-primary', 'bg-primary-light'],
                    'laporan_status' => ['fa-exchange', 'text-warning', 'bg-warning-light'],
                    'dokumen' => ['fa-upload', 'text-success', 'bg-success-light'],
                    default => ['fa-edit', 'text-success', 'bg-success-light'],
                };

                return (object) [
                    'type' => 'aktivitas',
                    'icon' => $icon,
                    'icon_color' => $iconColor,
                    'bg_color' => $bgColor,
                    'title' => ActivityLog::TYPES[$a->activity_type] ?? ucfirst($a->activity_type),
                    'subtitle' => $a->description,
                    'date' => $a->created_at ? $a->created_at->translatedFormat('d M Y, H:i') : '-',
                    'instansi' => $a->instansi,
                    'status' => $a->status === 'berhasil' ? 'Berhasil' : ucfirst($a->status),
                    'status_class' => $a->status === 'berhasil' ? 'badge-success' : 'badge-danger',
                    'created_at' => $a->created_at,
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

        $recentActivities = $latestActivities->concat($latestUsers)
            ->sortByDesc('created_at')
            ->values()
            ->take(5);

        // 6. Ringkasan Pengaturan Sistem Utama (ditampilkan pada widget Standar Layanan & SLA)
        $reportSla = SystemSetting::get('report_sla_days', '3');

        // 7. Kalkulasi SLA dinamis
        $slaTargetDays = (int) $reportSla;

        // SLA Respon PPA: % laporan PPA yang direspons (status != dikirim) dalam target hari
        $totalPPAForSla = Laporan::whereIn('kategori', Laporan::PPA_CATEGORIES)->count();
        $ppaRespondedInSla = 0;
        if ($totalPPAForSla > 0) {
            $ppaRespondedInSla = Laporan::whereIn('kategori', Laporan::PPA_CATEGORIES)
                ->where('status', '!=', 'dikirim')
                ->whereRaw('DATEDIFF(updated_at, created_at) <= ?', [$slaTargetDays])
                ->count();
        }
        $slaPercentPPA = $totalPPAForSla > 0 ? round(($ppaRespondedInSla / $totalPPAForSla) * 100) : 0;

        // Penyelesaian Rujukan Dinsos: % laporan dirujuk yang sudah selesai
        $totalDirujukForSla = Laporan::where('status', 'dirujuk')
            ->orWhere(function ($q) {
                $q->whereIn('kategori', Laporan::DINSOS_CATEGORIES)
                    ->where('status', 'selesai');
            })->count();
        $dinsosSelesai = Laporan::whereIn('kategori', Laporan::DINSOS_CATEGORIES)
            ->where('status', 'selesai')
            ->count();
        $slaPercentDinsos = $totalDirujukForSla > 0 ? round(($dinsosSelesai / $totalDirujukForSla) * 100) : 0;

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
            'chartDataMasuk',
            'chartDataRujukan',
            'tahun',
            'availableYears',
            'highlightName',
            'highlightMasuk',
            'highlightRujukan',
            'recentActivities',
            'reportSla',
            'slaPercentPPA',
            'slaPercentDinsos'
        ));
    }
}
