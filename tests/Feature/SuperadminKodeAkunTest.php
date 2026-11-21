<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Akun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SuperadminKodeAkunTest extends TestCase
{
    use DatabaseTransactions;

    private function superadmin(): Akun
    {
        return Akun::where('role', 'superadmin')->firstOrFail();
    }

    private function adminPpa(): Akun
    {
        return Akun::where('email', 'admin.ppa@sentra.com')->firstOrFail();
    }

    private function adminDinsos(): Akun
    {
        return Akun::where('email', 'admin.dinsos@sentra.com')->firstOrFail();
    }

    private function buatAktivitas(Akun $akun, string $reference): ActivityLog
    {
        return ActivityLog::create([
            'akun_id' => $akun->id_akun,
            'nama' => $akun->nama,
            'role' => $akun->role,
            'instansi' => Akun::instansiLabel($akun->role),
            'activity_type' => 'data',
            'module' => 'akun',
            'reference' => $reference,
            'description' => 'Aktivitas uji kode akun.',
            'status' => 'berhasil',
        ]);
    }

    public function test_tiga_akun_admin_memiliki_kode_unik(): void
    {
        $this->assertSame('spadmin111', $this->superadmin()->kode_akun);
        $this->assertSame('adminppa233', $this->adminPpa()->kode_akun);
        $this->assertSame('admindinsos433', $this->adminDinsos()->kode_akun);
    }

    public function test_kelola_akun_menampilkan_kode_akun(): void
    {
        $this->actingAs($this->superadmin())
            ->get(route('superadmin.akun.index'))
            ->assertOk()
            ->assertSee('spadmin111')
            ->assertSee('adminppa233')
            ->assertSee('admindinsos433');
    }

    public function test_audit_menampilkan_kode_akun(): void
    {
        $activity = $this->buatAktivitas($this->superadmin(), 'uji-audit-kode');

        $this->actingAs($this->superadmin())
            ->get(route('superadmin.audit.index'))
            ->assertOk()
            ->assertSee('spadmin111');

        $this->actingAs($this->superadmin())
            ->get(route('superadmin.audit.show', $activity->id))
            ->assertOk()
            ->assertSee('spadmin111');
    }

    public function test_pencarian_kelola_akun_memakai_kode(): void
    {
        $response = $this->actingAs($this->superadmin())
            ->get(route('superadmin.akun.index', ['q' => 'adminppa233']));

        $response->assertOk()->assertSee('Admin PPA (Sentra)');

        $this->assertSame(
            ['admin.ppa@sentra.com'],
            $response->viewData('akunList')->pluck('email')->all()
        );
    }

    public function test_filter_audit_memakai_kode_akun(): void
    {
        $target = $this->buatAktivitas($this->superadmin(), 'ref-target-spadmin');
        $lain = $this->buatAktivitas($this->adminPpa(), 'ref-lain-adminppa');

        $response = $this->actingAs($this->superadmin())
            ->get(route('superadmin.audit.index', ['akun_id' => 'spadmin111']));

        $response->assertOk();

        $ids = $response->viewData('activities')->pluck('id')->all();

        $this->assertContains($target->id, $ids);
        $this->assertNotContains($lain->id, $ids);
    }

    public function test_global_search_audit_memakai_kode_akun(): void
    {
        $activity = $this->buatAktivitas($this->adminDinsos(), 'ref-global-admindinsos');

        $response = $this->actingAs($this->superadmin())
            ->get(route('superadmin.audit.index', ['q' => 'admindinsos433']));

        $response->assertOk();

        $this->assertContains($activity->id, $response->viewData('activities')->pluck('id')->all());
    }

    public function test_akun_tanpa_kode_tetap_memakai_id_angka(): void
    {
        $akun = Akun::create([
            'nama' => 'Akun Tanpa Kode Uji',
            'notelp' => '0812' . random_int(10000000, 99999999),
            'email' => 'tanpa.kode.' . uniqid() . '@contoh.test',
            'password' => 'rahasia-uji',
            'role' => 'user',
            'is_active' => true,
        ]);

        $this->assertNull($akun->kode_akun);
        $this->assertSame((string) $akun->id_akun, $akun->displayId());

        $this->actingAs($this->superadmin())
            ->get(route('superadmin.akun.index'))
            ->assertOk()
            ->assertSee('ID #' . $akun->id_akun);
    }
}
