<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Akun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Tests\TestCase;

class SuperadminAuditBoundaryTest extends TestCase
{
    use DatabaseTransactions;

    private function buatAkun(string $role = 'admin_ppa'): Akun
    {
        return Akun::create([
            'nama' => 'Akun Uji Boundary',
            'email' => 'boundary.' . Str::random(10) . '@sentra.test',
            'notelp' => '0812' . random_int(10000000, 99999999),
            'password' => 'RahasiaUji123!',
            'role' => $role,
            'is_active' => true,
        ]);
    }

    private function log(Akun $akun, array $attributes = []): ActivityLog
    {
        return ActivityLog::create(array_merge([
            'akun_id' => $akun->id_akun,
            'nama' => $akun->nama,
            'role' => $akun->role,
            'instansi' => Akun::instansiLabel($akun->role),
            'activity_type' => 'data',
            'module' => 'akun',
            'reference' => 'BOUND-' . Str::random(8),
            'description' => 'Aktivitas uji boundary audit.',
            'status' => 'berhasil',
        ], $attributes));
    }

    public function test_tab_penting_default_menyembunyikan_login_berhasil_dan_logout(): void
    {
        $superadmin = $this->buatAkun('superadmin');

        $loginOk = $this->log($superadmin, [
            'activity_type' => 'login',
            'status' => 'berhasil',
            'reference' => 'BOUND-LOGIN-OK',
        ]);
        $loginFail = $this->log($superadmin, [
            'activity_type' => 'login',
            'status' => 'gagal',
            'reference' => 'BOUND-LOGIN-FAIL',
        ]);
        $logout = $this->log($superadmin, [
            'activity_type' => 'logout',
            'status' => 'berhasil',
            'reference' => 'BOUND-LOGOUT',
        ]);
        $data = $this->log($superadmin, [
            'activity_type' => 'data',
            'status' => 'berhasil',
            'reference' => 'BOUND-DATA',
        ]);

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.audit.index', ['q' => 'BOUND-']));

        $response->assertOk();
        $ids = $response->viewData('activities')->pluck('id')->all();

        $this->assertContains($loginFail->id, $ids);
        $this->assertContains($data->id, $ids);
        $this->assertNotContains($loginOk->id, $ids);
        $this->assertNotContains($logout->id, $ids);
    }

    public function test_tab_login_hanya_menampilkan_aktivitas_autentikasi(): void
    {
        $superadmin = $this->buatAkun('superadmin');

        $loginOk = $this->log($superadmin, [
            'activity_type' => 'login',
            'status' => 'berhasil',
            'reference' => 'BOUND-LOGIN-OK',
        ]);
        $loginFail = $this->log($superadmin, [
            'activity_type' => 'login',
            'status' => 'gagal',
            'reference' => 'BOUND-LOGIN-FAIL',
        ]);
        $logout = $this->log($superadmin, [
            'activity_type' => 'logout',
            'status' => 'berhasil',
            'reference' => 'BOUND-LOGOUT',
        ]);
        $data = $this->log($superadmin, [
            'activity_type' => 'data',
            'status' => 'berhasil',
            'reference' => 'BOUND-DATA',
        ]);

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.audit.index', ['q' => 'BOUND-', 'tab' => 'login']));

        $response->assertOk();
        $ids = $response->viewData('activities')->pluck('id')->all();

        $this->assertContains($loginOk->id, $ids);
        $this->assertContains($loginFail->id, $ids);
        $this->assertContains($logout->id, $ids);
        $this->assertNotContains($data->id, $ids);
    }

    public function test_tab_semua_menampilkan_seluruh_aktivitas(): void
    {
        $superadmin = $this->buatAkun('superadmin');

        $logs = [
            $this->log($superadmin, ['activity_type' => 'login', 'status' => 'berhasil', 'reference' => 'BOUND-LOGIN-OK']),
            $this->log($superadmin, ['activity_type' => 'logout', 'status' => 'berhasil', 'reference' => 'BOUND-LOGOUT']),
            $this->log($superadmin, ['activity_type' => 'data', 'status' => 'berhasil', 'reference' => 'BOUND-DATA']),
        ];

        $response = $this->actingAs($superadmin)
            ->get(route('superadmin.audit.index', ['q' => 'BOUND-', 'tab' => 'semua']));

        $response->assertOk();
        $ids = $response->viewData('activities')->pluck('id')->all();

        foreach ($logs as $log) {
            $this->assertContains($log->id, $ids);
        }
    }

    public function test_login_berhasil_tidak_membuat_activity_log_dan_menyimpan_metadata(): void
    {
        $admin = $this->buatAkun('admin_ppa');

        $this->post('/login', [
            'email' => $admin->email,
            'password' => 'RahasiaUji123!',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);

        $this->assertDatabaseMissing('activity_logs', [
            'akun_id' => $admin->id_akun,
            'activity_type' => 'login',
        ]);

        $admin->refresh();

        $this->assertNotNull($admin->last_login_at);
        $this->assertSame('127.0.0.1', $admin->last_login_ip);
    }

    public function test_login_gagal_berulang_dideduplikasi_dalam_lima_menit(): void
    {
        $admin = $this->buatAkun('admin_ppa');

        $gagal = fn () => ActivityLog::where('activity_type', 'login')
            ->where('status', 'gagal')
            ->where('description', 'like', '%' . $admin->email . '%')
            ->count();

        $credentials = [
            'email' => $admin->email,
            'password' => 'password-salah',
        ];

        $this->post('/login', $credentials)->assertSessionHasErrors('login');
        $this->post('/login', $credentials)->assertSessionHasErrors('login');

        $this->assertSame(1, $gagal(), 'Dua percobaan gagal dalam 5 menit harus menjadi satu baris audit.');

        $this->travel(6)->minutes();

        // Pastikan throttle tidak menghalangi skenario kedua.
        RateLimiter::clear(Str::transliterate(Str::lower($admin->email) . '|127.0.0.1'));

        $this->post('/login', $credentials)->assertSessionHasErrors('login');

        $this->assertSame(2, $gagal(), 'Setelah 5 menit, percobaan gagal baru dicatat lagi.');
    }
}
