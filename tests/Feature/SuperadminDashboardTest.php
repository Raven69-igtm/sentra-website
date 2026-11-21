<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Dashboard Superadmin hanya boleh memantau akun, konfigurasi, keamanan,
 * dan kesehatan sistem — bukan statistik/isi laporan PPA maupun Dinsos.
 */
class SuperadminDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dashboard_menampilkan_tujuh_komponen_yang_diizinkan(): void
    {
        $superadmin = Akun::create([
            'nama' => 'Superadmin Dashboard Uji',
            'email' => 'sa.dash.' . Str::random(8) . '@sentra.test',
            'notelp' => '081277777777',
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.dashboard'));

        $response->assertOk();

        foreach ([
            'Total Akun',
            'Akun Aktif',
            'Akun Nonaktif',
            'Total Role',
            'Distribusi Akun berdasarkan Role',
            'System Health Overview',
            'Database Status',
            'Storage Usage',
            'System Uptime',
            'Last Backup',
            'Security Activity Overview',
            'Failed Login Attempts',
            'Deactivated Accounts',
            'Active Sessions',
            'Recent Login Activity',
            'Expiring Tokens',
            'Role & Permission Overview',
            'Configuration Status',
            'Recent System Activities',
        ] as $label) {
            $response->assertSee($label);
        }

        foreach (['saRoleDonut', 'saRoleDonutDepth', 'sa-health-progress', 'sa-login-list', 'sa-checklist', 'sa-timeline-item', 'ApexCharts'] as $needle) {
            $response->assertSee($needle, false);
        }

        $response->assertDontSee('Tren Pendaftaran Pengguna', false)
            ->assertDontSee('saRegistrationTrend', false)
            ->assertDontSee('System Health Monitoring', false)
            ->assertDontSee('Security Overview', false)
            ->assertDontSee('Statistik Lanjutan', false)
            ->assertDontSee('Pengguna Baru Terdaftar', false);

        $text = strip_tags(html_entity_decode($response->getContent(), ENT_QUOTES, 'UTF-8'));
        $positions = array_map(function (string $label) use ($text) {
            $position = strpos($text, $label);

            return $position === false ? PHP_INT_MAX : $position;
        }, [
            'Total Akun',
            'Distribusi Akun berdasarkan Role',
            'System Health Overview',
            'Security Activity Overview',
            'Role & Permission Overview',
            'Configuration Status',
            'Recent System Activities',
        ]);

        $this->assertSame($positions, array_values(array_unique($positions)), 'Urutan komponen dashboard tidak sesuai.');
        $this->assertTrue(
            $positions[0] < $positions[1]
            && $positions[1] < $positions[2]
            && $positions[2] < $positions[3]
            && $positions[3] < $positions[4]
            && $positions[4] < $positions[5]
            && $positions[5] < $positions[6],
            'Urutan komponen dashboard tidak sesuai.'
        );

        foreach ([
            'systemHealth',
            'securitySummary',
            'configStatus',
            'recentActivities',
            'newUsersThisWeek',
            'newUsersToday',
            'totalRoles',
            'totalPermissions',
            'roleDistributionLabels',
            'roleDistributionValues',
            'roleChartColors',
            'roleDepthColors',
            'healthOverview',
            'securityActivityOverview',
        ] as $key) {
            $response->assertViewHas($key);
        }

        $response->assertViewMissing('registrationTrendLabels')
            ->assertViewMissing('registrationTrendValues');

        foreach ([
            route('superadmin.akun.index'),
            route('superadmin.akun.index', ['status' => 'aktif']),
            route('superadmin.akun.index', ['status' => 'nonaktif']),
            route('superadmin.roles.index'),
        ] as $reviewUrl) {
            $response->assertSee($reviewUrl, false);
        }

        $response->assertSee('sa-review-link', false)
            ->assertSee('Tinjau Akun', false)
            ->assertSee('Tinjau Role', false);
    }

    public function test_dashboard_tidak_menampilkan_statistik_atau_isi_laporan(): void
    {
        $superadmin = Akun::create([
            'nama' => 'Superadmin Privasi Uji',
            'email' => 'sa.privacy.' . Str::random(8) . '@sentra.test',
            'notelp' => '081288888888',
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.dashboard'));

        $response->assertOk();

        foreach ([
            'Laporan UPTD PPA',
            'Laporan & Rujukan Dinsos',
            'Lihat Aktivitas PPA',
            'Lihat Aktivitas Dinsos',
            'Grafik Kasus',
            'Statistik SLA',
            'slaPercentDinsos',
            'totalLaporanPPA',
            'chartDataMasuk',
            'ip_address',
            'user_agent',
            'session_id',
            'last_login_ip',
            'raw_token',
        ] as $label) {
            $response->assertDontSee($label, false);
            $response->assertViewMissing($label);
        }
    }

    public function test_panel_statistik_menggunakan_data_akun_aktual(): void
    {
        $superadmin = Akun::create([
            'nama' => 'Superadmin Grafik Uji',
            'email' => 'sa.chart.' . Str::random(8) . '@sentra.test',
            'notelp' => '081211111111',
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        Akun::create([
            'nama' => 'Admin PPA Grafik Uji',
            'email' => 'ppa.chart.' . Str::random(8) . '@sentra.test',
            'notelp' => '081222222222',
            'password' => 'RahasiaUji123!',
            'role' => 'admin_ppa',
            'is_active' => true,
        ]);

        Akun::create([
            'nama' => 'Pelapor Cepat Grafik Uji',
            'email' => 'guest.chart.' . Str::random(8) . '@sentra.test',
            'notelp' => '081233333333',
            'password' => 'RahasiaUji123!',
            'role' => 'Pelapor Cepat',
            'is_active' => true,
        ]);

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.dashboard'));

        $response->assertOk()
            ->assertViewHas('roleDistributionLabels', function ($labels) {
                return in_array('Admin PPA', $labels, true)
                    && in_array('Guest / Pelapor Cepat', $labels, true)
                    && array_search('Superadmin', $labels, true) < array_search('Admin PPA', $labels, true)
                    && array_search('Admin PPA', $labels, true) < array_search('Guest / Pelapor Cepat', $labels, true);
            })
            ->assertViewHas('roleDistributionValues', fn ($values) => array_sum($values) >= 3)
            ->assertViewHas('roleChartColors', fn ($colors) => count($colors) >= 8)
            ->assertViewHas('roleDepthColors', fn ($colors) => count($colors) >= 8)
            ->assertViewHas('healthOverview', function ($health) {
                return in_array($health['db_status'], ['Connected', 'Disconnected'], true)
                    && !empty($health['storage_used'])
                    && !empty($health['storage_capacity'])
                    && !empty($health['uptime'])
                    && !empty($health['last_backup'])
                    && (is_null($health['storage_percent']) || is_int($health['storage_percent']));
            })
            ->assertViewHas('securityActivityOverview', function ($security) {
                return is_int($security['failed_logins_24h'])
                    && is_int($security['deactivated_accounts'])
                    && is_int($security['active_sessions'])
                    && $security['recent_logins'] instanceof \Illuminate\Support\Collection;
            });
    }

    public function test_aktivitas_login_hanya_menampilkan_metadata_yang_diizinkan(): void
    {
        $superadmin = Akun::create([
            'nama' => 'Superadmin Login Uji',
            'email' => 'sa.login.' . Str::random(8) . '@sentra.test',
            'notelp' => '081244444444',
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        ActivityLog::create([
            'akun_id' => $superadmin->id_akun,
            'nama' => 'Admin Login Metadata',
            'role' => 'admin_dinsos',
            'instansi' => 'Dinas Sosial Uji',
            'activity_type' => 'login',
            'module' => 'auth',
            'description' => 'Login berhasil',
            'status' => 'berhasil',
            'ip_address' => '203.0.113.77',
            'user_agent' => 'SecretAgent/9.9',
        ]);

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.dashboard'));

        $response->assertOk()
            ->assertSee('Admin Login Metadata')
            ->assertSee('Dinas Sosial Uji')
            ->assertSee('Berhasil')
            ->assertDontSee('203.0.113.77', false)
            ->assertDontSee('SecretAgent/9.9', false)
            ->assertViewHas('securityActivityOverview', function ($security) {
                $login = $security['recent_logins']->first();

                return $login !== null
                    && $login['actor'] === 'Admin Login Metadata'
                    && $login['identity'] === 'Dinas Sosial Uji'
                    && $login['status'] === 'Berhasil'
                    && array_keys($login) === ['actor', 'identity', 'time', 'status', 'status_class'];
            });
    }
}
