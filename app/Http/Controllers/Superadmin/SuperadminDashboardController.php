<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemSetting;
use App\Models\ActivityLog;
use App\Support\SettingSchema;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard Superadmin.
 *
 * Ruang lingkup sengaja dibatasi pada pengelolaan akun, konfigurasi,
 * keamanan, dan kesehatan sistem. Dashboard TIDAK menampilkan statistik,
 * grafik, SLA, maupun isi laporan PPA/Dinsos.
 */
class SuperadminDashboardController extends Controller
{
    public function index(Request $request)
    {
        // ============================================================
        // 1. STATISTIK AKUN & USER
        // ============================================================
        $totalAkun = Akun::count();
        $totalSuperadmin = Akun::where('role', 'superadmin')->count();
        $totalAdmin = Akun::whereIn('role', ['admin', 'admin_ppa', 'admin_dinsos'])->count();
        $totalAdminPPA = Akun::whereIn('role', ['admin', 'admin_ppa'])->count();
        $totalAdminDinsos = Akun::where('role', 'admin_dinsos')->count();
        $totalUser = Akun::where('role', 'user')->count();
        $totalGuest = Akun::where('role', 'guest')->count();
        $totalNonAktif = Akun::where('is_active', false)->count();
        $totalAkunAktif = $totalAkun - $totalNonAktif;

        // Pengguna baru terdaftar
        $newUsersThisWeek = Akun::where('created_at', '>=', Carbon::now()->subDays(7))->count();
        $newUsersToday = Akun::whereDate('created_at', Carbon::today())->count();

        // ============================================================
        // 2. STATISTIK ROLE & PERMISSION
        // ============================================================
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();
        $systemRoles = Role::where('is_system', true)->count();

        // Distribusi akun per role — urutan tetap agar donut & legend tidak acak.
        $roleLabelMap = [
            'superadmin' => 'Superadmin',
            'admin' => 'Admin',
            'admin_ppa' => 'Admin PPA',
            'admin_dinsos' => 'Admin Dinsos',
            'user' => 'User / Pelapor',
            'guest' => 'Guest',
            'Pelapor Cepat' => 'Guest / Pelapor Cepat',
        ];
        $roleCounts = Akun::selectRaw('role, COUNT(*) as total')
            ->groupBy('role')
            ->pluck('total', 'role');

        $roleDistribution = collect($roleLabelMap)
            ->map(fn (string $label, string $role) => [
                'label' => $label,
                'value' => (int) ($roleCounts[$role] ?? 0),
            ])
            ->filter(fn (array $item) => $item['value'] > 0)
            ->values();

        $otherRolesTotal = (int) $roleCounts->sum() - (int) $roleDistribution->sum('value');
        if ($otherRolesTotal > 0) {
            $roleDistribution->push(['label' => 'Lainnya', 'value' => $otherRolesTotal]);
        }

        $roleDistributionLabels = $roleDistribution->pluck('label')->all();
        $roleDistributionValues = $roleDistribution->pluck('value')->all();

        // Satu-satunya sumber warna untuk legend dot, donut depan, dan depth.
        $roleChartColors = [
            '#14532D', '#15803D', '#16A34A', '#22C55E',
            '#4ADE80', '#86EFAC', '#A7F3D0', '#BBF7D0',
        ];
        $roleDepthColors = [
            '#064E3B', '#065F46', '#047857', '#15803D',
            '#16A34A', '#22C55E', '#4ADE80', '#86EFAC',
        ];

        // ============================================================
        // 3 - 5. KESEHATAN SISTEM, KEAMANAN, KONFIGURASI
        // ============================================================
        $systemHealth = $this->getSystemHealth();
        $securitySummary = $this->getSecuritySummary();
        $configStatus = $this->getConfigurationStatus();

        // Panel pengganti tren: ringkasan kesehatan & aktivitas keamanan.
        $healthOverview = [
            'db_status' => $systemHealth['db_status_label'],
            'db_status_class' => $systemHealth['db_status_class'],
            'storage_used' => $systemHealth['storage_used'],
            'storage_capacity' => $systemHealth['storage_capacity'],
            'storage_percent' => $systemHealth['storage_percent'],
            'storage_percent_label' => $systemHealth['storage_percent_label'],
            'storage_level' => $systemHealth['storage_level'],
            'uptime' => $systemHealth['uptime'],
            'last_backup' => $systemHealth['last_backup_display'],
        ];

        $securityActivityOverview = [
            'failed_logins_24h' => $securitySummary['failed_logins_24h'],
            'failed_logins_level' => $securitySummary['failed_logins_level'],
            'deactivated_accounts' => $securitySummary['deactivated_accounts'],
            'deactivated_accounts_level' => $securitySummary['deactivated_accounts_level'],
            'active_sessions' => $securitySummary['active_sessions'],
            'active_sessions_level' => $securitySummary['active_sessions_level'],
            'expiring_tokens' => $securitySummary['expiring_tokens_available']
                ? $securitySummary['expiring_tokens']
                : null,
            'expiring_tokens_level' => $securitySummary['expiring_tokens_level'],
            'recent_logins' => $this->getRecentLoginActivity(),
        ];

        // ============================================================
        // 6. METADATA AKTIVITAS SISTEM (tanpa isi laporan/data kasus)
        // ============================================================
        $recentActivities = $this->getRecentActivityMetadata();

        return view('backend.superadmin.dashboard', compact(
            'totalAkun',
            'totalSuperadmin',
            'totalAdmin',
            'totalAdminPPA',
            'totalAdminDinsos',
            'totalUser',
            'totalGuest',
            'totalNonAktif',
            'totalAkunAktif',
            'newUsersThisWeek',
            'newUsersToday',
            'totalRoles',
            'totalPermissions',
            'systemRoles',
            'roleDistributionLabels',
            'roleDistributionValues',
            'roleChartColors',
            'roleDepthColors',
            'healthOverview',
            'securityActivityOverview',
            'systemHealth',
            'securitySummary',
            'configStatus',
            'recentActivities'
        ));
    }

    /**
     * Monitoring kesehatan sistem.
     *
     * Catatan: aplikasi ini tidak memiliki job backup terkonfigurasi, jadi
     * waktu backup hanya diambil jika memang tercatat di setting atau berkas
     * backup tersedia. Jika tidak ada, ditampilkan sebagai "Belum tercatat"
     * dan tidak diarang-ngarang.
     */
    private function getSystemHealth(): array
    {
        try {
            DB::connection()->getPdo();
            $dbStatus = 'Terhubung';
            $dbStatusLabel = 'Connected';
            $dbStatusClass = 'badge-soft-green';
        } catch (\Throwable $e) {
            $dbStatus = 'Terputus';
            $dbStatusLabel = 'Disconnected';
            $dbStatusClass = 'badge-soft-rose';
        }

        $storageBytes = $this->getDirectorySize(storage_path())
            + $this->getDirectorySize(public_path('uploads'));

        $diskTotal = @disk_total_space(base_path());
        $capacityBytes = is_numeric($diskTotal) && $diskTotal > 0 ? (int) $diskTotal : 0;
        $storagePercent = null;
        $storageCapacity = 'Belum tersedia';

        if ($capacityBytes > 0) {
            $storageCapacity = $this->formatBytes($capacityBytes);
            $storagePercent = max(0, min(100, (int) round(($storageBytes / $capacityBytes) * 100)));
        }

        $storagePercentLabel = $storagePercent === null ? 'Belum tersedia' : $storagePercent . '%';
        $storageLevel = 'unknown';

        if ($storagePercent !== null) {
            $storageLevel = $storagePercent >= 95
                ? 'critical'
                : ($storagePercent >= 85 ? 'warning' : 'normal');
        }

        $lastBackupAt = $this->getLastBackupAt();

        // Uptime aplikasi dihitung sejak pemantauan dashboard pertama kali
        // dijalankan. Tidak bergantung pada perintah `uptime` sistem operasi.
        $monitoredSince = (int) Cache::rememberForever(
            'sentra.dashboard.monitored_since',
            fn () => Carbon::now()->timestamp
        );
        $uptimeSeconds = max(0, Carbon::now()->timestamp - $monitoredSince);

        return [
            'db_status' => $dbStatus,
            'db_status_label' => $dbStatusLabel,
            'db_status_class' => $dbStatusClass,
            'storage_used' => $this->formatBytes($storageBytes),
            'storage_capacity' => $storageCapacity,
            'storage_percent' => $storagePercent,
            'storage_percent_label' => $storagePercentLabel,
            'storage_level' => $storageLevel,
            'last_backup' => $lastBackupAt
                ? $lastBackupAt->translatedFormat('d M Y, H:i')
                : 'Belum tercatat',
            'last_backup_display' => $lastBackupAt
                ? $lastBackupAt->translatedFormat('d M Y, H:i')
                : 'Belum tersedia',
            'last_backup_class' => $lastBackupAt ? 'text-dark' : 'text-muted',
            'uptime' => $this->formatUptime($uptimeSeconds),
        ];
    }

    /**
     * Ambil waktu backup terakhir dari setting atau direktori backup.
     */
    private function getLastBackupAt(): ?Carbon
    {
        $configured = SystemSetting::get('last_backup_at');

        if (!empty($configured)) {
            try {
                return Carbon::parse($configured);
            } catch (\Throwable $e) {
                // Abaikan nilai setting yang tidak valid.
            }
        }

        foreach ([storage_path('backups'), database_path('backups')] as $directory) {
            if (!is_dir($directory)) {
                continue;
            }

            $latest = 0;

            try {
                foreach (new \FilesystemIterator($directory) as $file) {
                    if ($file->isFile()) {
                        $latest = max($latest, $file->getMTime());
                    }
                }
            } catch (\Throwable $e) {
                continue;
            }

            if ($latest > 0) {
                return Carbon::createFromTimestamp($latest);
            }
        }

        return null;
    }

    /**
     * Ringkasan keamanan.
     */
    private function getSecuritySummary(): array
    {
        $failedLogins24h = ActivityLog::where('activity_type', 'login')
            ->where('status', 'gagal')
            ->where('created_at', '>=', Carbon::now()->subDay())
            ->count();

        $deactivatedAccounts = Akun::where('is_active', false)
            ->where('updated_at', '>=', Carbon::now()->subDays(7))
            ->count();
        $deactivatedAccountsTotal = Akun::where('is_active', false)->count();

        // Sesi aktif: sesi web Laravel yang masih dalam ambang aktivitas.
        try {
            $activeSessions = DB::table('sessions')
                ->where('last_activity', '>=', Carbon::now()->subMinutes(15)->getTimestamp())
                ->count();
        } catch (\Throwable $e) {
            $activeSessions = 0;
        }

        // Token API yang akan kedaluwarsa dalam 7 hari ke depan.
        $tokensAvailable = true;

        try {
            $expiringTokens = DB::table('personal_access_tokens')
                ->whereNotNull('expires_at')
                ->where('expires_at', '>=', Carbon::now())
                ->where('expires_at', '<=', Carbon::now()->addDays(7))
                ->count();
        } catch (\Throwable $e) {
            $expiringTokens = 0;
            $tokensAvailable = false;
        }

        // Ambang indikator: normal → perhatian → kritis.
        $level = fn (int $value, int $warning, int $critical): string => $value >= $critical
            ? 'critical'
            : ($value >= $warning ? 'warning' : 'normal');

        return [
            'failed_logins_24h' => $failedLogins24h,
            'deactivated_accounts_week' => $deactivatedAccounts,
            'deactivated_accounts' => $deactivatedAccountsTotal,
            'active_sessions' => $activeSessions,
            'expiring_tokens' => $expiringTokens,
            'expiring_tokens_available' => $tokensAvailable,
            'failed_logins_level' => $level($failedLogins24h, 1, 5),
            'deactivated_level' => $level($deactivatedAccounts, 1, 3),
            'deactivated_accounts_level' => $level($deactivatedAccountsTotal, 1, 3),
            'active_sessions_level' => 'normal',
            'expiring_tokens_level' => $tokensAvailable ? $level($expiringTokens, 1, 5) : 'unknown',
        ];
    }

    /**
     * Status konfigurasi sistem.
     */
    private function getConfigurationStatus(): array
    {
        $warnings = [];
        $completed = 0;
        $total = 0;

        // Setting yang benar-benar berdampak pada identitas & operasional.
        $requiredSettings = [
            'app_name' => 'Nama Aplikasi',
            'instansi_name' => 'Nama Instansi',
            'app_description' => 'Deskripsi Aplikasi',
            'hotline_number' => 'Nomor Hotline',
            'emergency_call' => 'Panggilan Darurat',
            'office_email' => 'Email Layanan',
            'office_address' => 'Alamat Kantor',
            'report_sla_days' => 'Target Waktu Layanan',
            'session_lifetime_minutes' => 'Masa Aktif Sesi',
            'max_login_attempts' => 'Batas Percobaan Login',
            'otp_expiry_minutes' => 'Masa Berlaku OTP',
        ];

        foreach ($requiredSettings as $key => $label) {
            $total++;
            $value = SystemSetting::get($key);

            if (empty($value) || $value === SettingSchema::default($key)) {
                $warnings[] = [
                    'icon' => 'fa-exclamation-triangle',
                    'message' => "Konfigurasi \"{$label}\" masih kosong atau menggunakan nilai bawaan.",
                ];
            } else {
                $completed++;
            }
        }

        // Status logo & branding.
        $total++;
        $logo = SystemSetting::get('app_logo');
        if (empty($logo)) {
            $logoStatus = 'Logo bawaan';
            $logoStatusClass = 'badge-soft-amber';
            $logoBadge = 'Needs Attention';
            $warnings[] = [
                'icon' => 'fa-image',
                'message' => 'Logo aplikasi belum diunggah, masih menggunakan logo bawaan.',
            ];
        } else {
            $logoStatus = 'Sudah diunggah';
            $logoStatusClass = 'badge-soft-green';
            $logoBadge = 'Complete';
            $completed++;
        }

        // Status kontak darurat.
        $total++;
        $contactKeys = [
            'hotline_number' => 'hotline',
            'emergency_call' => 'panggilan darurat',
            'office_email' => 'email layanan',
            'office_address' => 'alamat kantor',
        ];
        $missingContact = [];

        foreach ($contactKeys as $key => $label) {
            if (empty(SystemSetting::get($key))) {
                $missingContact[] = $label;
            }
        }

        if ($missingContact !== []) {
            $contactStatus = 'Belum lengkap';
            $contactStatusClass = count($missingContact) === count($contactKeys)
                ? 'badge-soft-rose'
                : 'badge-soft-amber';
            $contactBadge = count($missingContact) === count($contactKeys)
                ? 'Incomplete'
                : 'Needs Attention';
            $warnings[] = [
                'icon' => 'fa-phone',
                'message' => 'Kontak darurat belum lengkap: ' . implode(', ', $missingContact) . '.',
            ];
        } else {
            $contactStatus = 'Lengkap';
            $contactStatusClass = 'badge-soft-green';
            $contactBadge = 'Complete';
            $completed++;
        }

        $completionPercent = $total > 0 ? (int) round(($completed / $total) * 100) : 0;

        if ($completionPercent >= 80) {
            $completionBadge = 'Complete';
            $completionBadgeClass = 'badge-soft-green';
        } elseif ($completionPercent >= 50) {
            $completionBadge = 'Needs Attention';
            $completionBadgeClass = 'badge-soft-amber';
        } else {
            $completionBadge = 'Incomplete';
            $completionBadgeClass = 'badge-soft-rose';
        }

        return [
            'warnings' => $warnings,
            'completed' => $completed,
            'total' => $total,
            'completion_percent' => $completionPercent,
            'is_complete' => $completionPercent >= 80,
            'completion_badge' => $completionBadge,
            'completion_badge_class' => $completionBadgeClass,
            'logo_status' => $logoStatus,
            'logo_status_class' => $logoStatusClass,
            'logo_badge' => $logoBadge,
            'contact_status' => $contactStatus,
            'contact_status_class' => $contactStatusClass,
            'contact_badge' => $contactBadge,
        ];
    }

    /**
     * Login terbaru untuk panel keamanan — metadata minimal saja.
     *
     * Sengaja tidak mengambil ip_address, user_agent, token, session ID,
     * maupun referensi ke laporan/dokumen.
     */
    private function getRecentLoginActivity(): \Illuminate\Support\Collection
    {
        // Login gagal tercatat di activity_logs, sedangkan login berhasil
        // tercatat pada last_login_at akun. Keduanya digabung agar daftar
        // tetap mencerminkan aktivitas nyata tanpa menambah data baru.
        $fromLogs = ActivityLog::where('activity_type', 'login')
            ->latest('created_at')
            ->limit(3)
            ->get()
            ->map(function ($log) {
                return [
                    'actor' => $log->nama ?: 'Tidak Dikenal',
                    'identity' => $log->instansi ?: ActivityLog::roleLabel($log->role),
                    'time' => $log->created_at
                        ? $log->created_at->translatedFormat('d M, H:i')
                        : 'Belum tersedia',
                    'status' => ActivityLog::statusLabel($log->status),
                    'status_class' => $log->status === 'berhasil'
                        ? 'badge-soft-green'
                        : 'badge-soft-rose',
                    'sort_at' => $log->created_at,
                ];
            });

        $fromAccounts = Akun::whereNotNull('last_login_at')
            ->latest('last_login_at')
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'actor' => $user->nama ?: 'Tidak Dikenal',
                    'identity' => ActivityLog::instansiForRole($user->role),
                    'time' => $user->last_login_at
                        ? $user->last_login_at->translatedFormat('d M, H:i')
                        : 'Belum tersedia',
                    'status' => 'Berhasil',
                    'status_class' => 'badge-soft-green',
                    'sort_at' => $user->last_login_at,
                ];
            });

        return $fromLogs->concat($fromAccounts)
            ->sortByDesc('sort_at')
            ->take(3)
            ->values()
            ->map(function (array $item) {
                return [
                    'actor' => $item['actor'],
                    'identity' => $item['identity'],
                    'time' => $item['time'],
                    'status' => $item['status'],
                    'status_class' => $item['status_class'],
                ];
            });
    }

    /**
     * Riwayat aktivitas pengguna & sistem — metadata saja.
     *
     * Aktivitas bertema laporan/dokumen sengaja tidak diambil sama sekali,
     * bukan sekadar disembunyikan deskripsinya.
     */
    private function getRecentActivityMetadata(): \Illuminate\Support\Collection
    {
        $excludedTypes = ['laporan_status', 'dokumen'];

        $activities = ActivityLog::whereNotIn('activity_type', $excludedTypes)
            ->latest('created_at')
            ->limit(6)
            ->get()
            ->map(function ($log) {
                [$icon, $iconColor, $bgColor] = match ($log->activity_type) {
                    'login', 'logout' => ['fa-sign-in', 'sa-activity-icon-green', 'badge-soft-green'],
                    'data' => ['fa-edit', 'sa-activity-icon-green', 'badge-soft-green'],
                    default => ['fa-shield-alt', 'sa-activity-icon-gray', 'badge-soft-gray'],
                };

                return [
                    'type' => 'aktivitas',
                    'icon' => $icon,
                    'icon_color' => $iconColor,
                    'bg_color' => $bgColor,
                    'title' => ActivityLog::typeLabel($log->activity_type),
                    'subtitle' => $log->description ?: ActivityLog::moduleLabel($log->module),
                    'instansi' => $log->instansi ?: 'Sistem',
                    'actor' => $log->nama ?: 'Tidak Dikenal',
                    'status' => ActivityLog::statusLabel($log->status),
                    'status_class' => $log->status === 'berhasil' ? 'badge-soft-green' : 'badge-soft-rose',
                    'date' => $log->created_at
                        ? $log->created_at->translatedFormat('d M Y, H:i')
                        : '-',
                    'created_at' => $log->created_at,
                ];
            });

        $newAccounts = Akun::latest('created_at')
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'type' => 'akun',
                    'icon' => 'fa-user-plus',
                    'icon_color' => 'sa-activity-icon-green',
                    'bg_color' => 'badge-soft-green',
                    'title' => 'Akun Baru',
                    'subtitle' => $user->nama,
                    'instansi' => ActivityLog::instansiForRole($user->role),
                    'actor' => $user->nama ?: 'Tidak Dikenal',
                    'status' => ($user->is_active ?? true) ? 'Aktif' : 'Nonaktif',
                    'status_class' => ($user->is_active ?? true) ? 'badge-soft-green' : 'badge-soft-rose',
                    'date' => $user->created_at
                        ? $user->created_at->translatedFormat('d M Y, H:i')
                        : '-',
                    'created_at' => $user->created_at,
                ];
            });

        return $activities->concat($newAccounts)
            ->sortByDesc('created_at')
            ->values()
            ->take(6);
    }

    /**
     * Hitung ukuran direktori secara rekursif.
     */
    private function getDirectorySize(string $path): int
    {
        if (!is_dir($path)) {
            return 0;
        }

        $size = 0;

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        } catch (\Throwable $e) {
            return 0;
        }

        return $size;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function formatUptime(int $seconds): string
    {
        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($days > 0) {
            return "{$days} hari {$hours} jam";
        }

        if ($hours > 0) {
            return "{$hours} jam {$minutes} menit";
        }

        return "{$minutes} menit";
    }
}
