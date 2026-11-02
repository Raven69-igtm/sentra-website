<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperadminDashboardController extends Controller
{
    public function index()
    {
        // Statistik Akun
        $totalAkun = Akun::count();
        $totalSuperadmin = Akun::where('role', 'superadmin')->count();
        $totalAdmin = Akun::whereIn('role', ['admin', 'admin_ppa', 'admin_dinsos'])->count();
        $totalAdminPPA = Akun::whereIn('role', ['admin', 'admin_ppa'])->count();
        $totalAdminDinsos = Akun::where('role', 'admin_dinsos')->count();
        $totalUser = Akun::where('role', 'user')->count();
        $totalGuest = Akun::where('role', 'guest')->count();
        $totalNonAktif = Akun::where('is_active', false)->count();

        // Statistik Role & Permission
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        // 5 Akun Pendaftar Terakhir
        $latestAccounts = Akun::latest('created_at')->limit(5)->get();

        // Ringkasan Pengaturan Sistem Utama
        $appName = SystemSetting::get('app_name', 'SENTRA');
        $instansi = SystemSetting::get('instansi_name', 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak');
        $hotline = SystemSetting::get('hotline_number', '0812-3456-7890');
        $reportSla = SystemSetting::get('report_sla_days', '3');
        $sessionLifetime = config('session.lifetime');

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
            'latestAccounts',
            'appName',
            'instansi',
            'hotline',
            'reportSla',
            'sessionLifetime'
        ));
    }
}
