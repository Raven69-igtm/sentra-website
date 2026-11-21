<?php

namespace Tests\Feature;

use App\Models\Akun;
use App\Models\Laporan;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Mengunci batas akses dan transisi status panel Admin Dinsos.
 *
 * Fokus pengujian:
 * - rute mutasi PPA tidak bisa dipakai Admin Dinsos;
 * - rujukan PPA bersifat read-only di panel Dinsos;
 * - laporan harus melewati verifikasi/proses sebelum selesai;
 * - wildcard LIKE pada pencarian Dinsos dibaca sebagai karakter literal.
 */
class DinsosAksesDanStatusTest extends TestCase
{
    use DatabaseTransactions;

    private Akun $dinsos;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dinsos = Akun::where('email', 'admin.dinsos@sentra.com')->first();

        if (! $this->dinsos) {
            $this->dinsos = Akun::create([
                'nama' => 'Admin Dinsos Uji',
                'email' => 'admin.dinsos@sentra.com',
                'notelp' => '0813000' . random_int(1000, 9999),
                'password' => bcrypt('RahasiaUji123!'),
                'role' => 'admin_dinsos',
                'is_active' => true,
            ]);
        } else {
            $this->dinsos->update([
                'role' => 'admin_dinsos',
                'is_active' => true,
            ]);
        }
    }

    private function buatLaporan(
        string $kategori,
        ?string $layanan,
        string $status,
        string $penanda
    ): Laporan {
        // Trigger MySQL `before_laporan_insert_id` menimpa id_laporan,
        // jadi penanda unik disimpan di judul lalu dibaca kembali.
        Laporan::create([
            'id_laporan' => 'TEMP-' . Str::upper(Str::random(8)),
            'id_akun' => $this->dinsos->id_akun,
            'judul' => $penanda,
            'kategori' => $kategori,
            'layanan_pjsrk' => $layanan,
            'status' => $status,
        ]);

        $id = DB::table('laporan')->where('judul', $penanda)->value('id_laporan');

        $this->assertNotNull($id, 'Laporan uji gagal dibuat.');

        return Laporan::findOrFail($id);
    }

    public function test_dinsos_tidak_dapat_mengubah_laporan_ppa_lewat_rute_ppa(): void
    {
        $penanda = 'UJI-PPA-UPDATE-' . Str::upper(Str::random(8));
        $laporan = $this->buatLaporan('Kekerasan Fisik', null, 'diterima', $penanda);

        $this->actingAs($this->dinsos)
            ->put(route('laporan.update', $laporan->id_laporan), [])
            ->assertForbidden();

        $this->assertSame('diterima', $laporan->fresh()->status);
    }

    public function test_dinsos_tidak_dapat_menyelesaikan_laporan_lewat_rute_ppa(): void
    {
        $penanda = 'UJI-PPA-SELESAI-' . Str::upper(Str::random(8));
        $laporan = $this->buatLaporan('Kekerasan Fisik', null, 'diproses', $penanda);

        $this->actingAs($this->dinsos)
            ->put(route('laporan.selesai', $laporan->id_laporan))
            ->assertForbidden();

        $this->assertSame('diproses', $laporan->fresh()->status);
    }

    public function test_dinsos_tidak_dapat_menghapus_laporan_ppa_lewat_rute_ppa(): void
    {
        $penanda = 'UJI-PPA-DELETE-' . Str::upper(Str::random(8));
        $laporan = $this->buatLaporan('Kekerasan Fisik', null, 'diterima', $penanda);

        $this->actingAs($this->dinsos)
            ->delete(route('laporan.destroy', $laporan->id_laporan))
            ->assertForbidden();

        $this->assertNotNull($laporan->fresh(), 'Laporan PPA tidak boleh terhapus oleh Admin Dinsos.');
    }

    public function test_rujukan_ppa_tidak_bisa_diproses_atau_ditolak_dari_panel_dinsos(): void
    {
        $penanda = 'UJI-RUJUKAN-' . Str::upper(Str::random(8));
        $laporan = $this->buatLaporan('Kekerasan Fisik', null, 'dirujuk', $penanda);

        $this->actingAs($this->dinsos)
            ->from(route('dinsos.laporan.index'))
            ->put(route('dinsos.laporan.proses', $laporan->id_laporan))
            ->assertSessionHas('error');

        $this->assertSame('dirujuk', $laporan->fresh()->status);

        $this->actingAs($this->dinsos)
            ->from(route('dinsos.laporan.index'))
            ->put(route('dinsos.laporan.tolak', $laporan->id_laporan), [
                'alasan_penolakan' => 'Uji penolakan rujukan.',
            ])
            ->assertSessionHas('error');

        $this->assertSame('dirujuk', $laporan->fresh()->status);
    }

    public function test_laporan_harus_diproses_dulu_sebelum_diselesaikan(): void
    {
        $penanda = 'UJI-ALUR-' . Str::upper(Str::random(8));
        $laporan = $this->buatLaporan('unset', 'rehabilitasi_sosial', 'diterima', $penanda);

        $this->actingAs($this->dinsos)
            ->from(route('dinsos.laporan.index'))
            ->put(route('dinsos.laporan.selesai', $laporan->id_laporan))
            ->assertSessionHas('error');

        $this->assertSame('diterima', $laporan->fresh()->status);

        $this->actingAs($this->dinsos)
            ->from(route('dinsos.laporan.index'))
            ->put(route('dinsos.laporan.verifikasi', $laporan->id_laporan))
            ->assertSessionHas('success');

        $this->assertSame('diproses', $laporan->fresh()->status);

        $this->actingAs($this->dinsos)
            ->from(route('dinsos.laporan.index'))
            ->put(route('dinsos.laporan.selesai', $laporan->id_laporan))
            ->assertSessionHas('success');

        $this->assertSame('selesai', $laporan->fresh()->status);
    }

    public function test_wildcard_like_tidak_menjadi_wildcard_pada_pencarian_laporan_cepat(): void
    {
        $penanda = 'UJI-CEPAT-' . Str::upper(Str::random(8));
        $this->buatLaporan('unset', 'rehabilitasi_sosial', 'dikirim', $penanda);

        $this->actingAs($this->dinsos)
            ->get(route('dinsos.laporan_cepat', ['search' => '%']))
            ->assertOk()
            ->assertDontSee($penanda, false);

        $this->actingAs($this->dinsos)
            ->get(route('dinsos.laporan_cepat', ['search' => $penanda]))
            ->assertOk()
            ->assertSee($penanda, false);
    }

    public function test_wildcard_like_tidak_menjadi_wildcard_pada_pencarian_dinsos(): void
    {
        $penanda = 'UJI-CARI-' . Str::upper(Str::random(8));
        $this->buatLaporan('unset', 'permasalahan_sosial', 'dikirim', $penanda);

        $this->actingAs($this->dinsos)
            ->get(route('dinsos.laporan.index', ['search' => '%']))
            ->assertOk()
            ->assertDontSee($penanda, false);

        $this->actingAs($this->dinsos)
            ->get(route('dinsos.laporan.index', ['search' => $penanda]))
            ->assertOk()
            ->assertSee($penanda, false);
    }
}
