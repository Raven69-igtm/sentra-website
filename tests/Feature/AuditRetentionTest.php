<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Akun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuditRetentionTest extends TestCase
{
    use DatabaseTransactions;

    private function akun(): Akun
    {
        return Akun::create([
            'nama' => 'Akun Uji Retensi',
            'email' => 'retensi.' . Str::random(10) . '@sentra.test',
            'notelp' => '0812' . random_int(10000000, 99999999),
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    private function log(Akun $akun, string $type, string $reference, int $daysAgo): ActivityLog
    {
        $log = ActivityLog::create([
            'akun_id' => $akun->id_akun,
            'nama' => $akun->nama,
            'role' => $akun->role,
            'instansi' => 'Superadmin',
            'activity_type' => $type,
            'module' => $type === 'login' ? 'auth' : 'akun',
            'reference' => $reference,
            'description' => 'Aktivitas uji retensi.',
            'status' => 'berhasil',
        ]);

        ActivityLog::whereKey($log->id)->update([
            'created_at' => now()->subDays($daysAgo),
            'updated_at' => now()->subDays($daysAgo),
        ]);

        return $log->refresh();
    }

    public function test_retensi_memakai_batas_berbeda_untuk_login_dan_audit_umum(): void
    {
        Storage::fake('local');

        $akun = $this->akun();

        $loginLama = $this->log($akun, 'login', 'RETENSI-LOGIN-LAMA', 100);
        $dataLama = $this->log($akun, 'data', 'RETENSI-DATA-LAMA', 100);
        $dataSangatLama = $this->log($akun, 'data', 'RETENSI-DATA-SANGAT-LAMA', 400);
        $dataBaru = $this->log($akun, 'data', 'RETENSI-DATA-BARU', 5);

        $this->artisan('audit:prune')->assertSuccessful();

        $this->assertDatabaseMissing('activity_logs', ['id' => $loginLama->id]);
        $this->assertDatabaseMissing('activity_logs', ['id' => $dataSangatLama->id]);
        $this->assertDatabaseHas('activity_logs', ['id' => $dataLama->id]);
        $this->assertDatabaseHas('activity_logs', ['id' => $dataBaru->id]);
    }

    public function test_audit_diarsipkan_ke_jsonl_sebelum_dihapus(): void
    {
        Storage::fake('local');

        $akun = $this->akun();
        $lama = $this->log($akun, 'data', 'RETENSI-ARSIP-LAMA', 400);
        $baru = $this->log($akun, 'data', 'RETENSI-ARSIP-BARU', 5);

        $this->artisan('audit:prune')->assertSuccessful();

        $files = Storage::disk('local')->files('audit-archive');

        $this->assertNotEmpty($files, 'Arsip JSONL harus dibuat sebelum penghapusan.');

        $isi = Storage::disk('local')->get($files[0]);

        $this->assertStringContainsString('RETENSI-ARSIP-LAMA', $isi);
        $this->assertStringNotContainsString('RETENSI-ARSIP-BARU', $isi);

        $this->assertDatabaseMissing('activity_logs', ['id' => $lama->id]);
        $this->assertDatabaseHas('activity_logs', ['id' => $baru->id]);
    }

    public function test_dry_run_tidak_menghapus_dan_tidak_mengarsipkan(): void
    {
        Storage::fake('local');

        $akun = $this->akun();
        $lama = $this->log($akun, 'data', 'RETENSI-DRY-RUN', 400);

        $this->artisan('audit:prune', ['--dry-run' => true])->assertSuccessful();

        $this->assertDatabaseHas('activity_logs', ['id' => $lama->id]);
        $this->assertSame([], Storage::disk('local')->files('audit-archive'));
    }

    public function test_no_archive_menghapus_tanpa_menulis_berkas(): void
    {
        Storage::fake('local');

        $akun = $this->akun();
        $lama = $this->log($akun, 'data', 'RETENSI-NO-ARCHIVE', 400);

        $this->artisan('audit:prune', ['--no-archive' => true])->assertSuccessful();

        $this->assertDatabaseMissing('activity_logs', ['id' => $lama->id]);
        $this->assertSame([], Storage::disk('local')->files('audit-archive'));
    }
}
