<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Akun;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class SuperadminAuditSearchTest extends TestCase
{
    use DatabaseTransactions;

    private Akun $superadmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superadmin = Akun::create([
            'nama' => 'Superadmin Uji Audit',
            'email' => 'sa.audit.' . Str::random(8) . '@sentra.test',
            'notelp' => '081200000001',
            'password' => 'RahasiaUji123!',
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    private function log(array $attributes = []): ActivityLog
    {
        return ActivityLog::create(array_merge([
            'akun_id' => $this->superadmin->id_akun,
            'nama' => 'Superadmin Uji Audit',
            'role' => 'superadmin',
            'instansi' => 'Superadmin',
            'activity_type' => 'data',
            'module' => 'akun',
            'reference' => 'uji-ref-' . Str::random(8),
            'description' => 'Aktivitas pengujian audit.',
            'status' => 'berhasil',
            'ip_address' => '192.168.10.5',
            'user_agent' => 'Mozilla/5.0 Chrome/120',
        ], $attributes));
    }

    private function logAt(string $createdAt, array $attributes = []): ActivityLog
    {
        $log = $this->log($attributes);
        ActivityLog::whereKey($log->id)->update(['created_at' => $createdAt]);

        return $log->refresh();
    }

    public function test_pencarian_audit_mencakup_seluruh_aspek(): void
    {
        $alphaRef = 'audit-alpha-' . Str::random(6);
        $betaRef = 'audit-beta-' . Str::random(6);

        $this->log([
            'nama' => 'Auditor Alpha ' . $alphaRef,
            'role' => 'admin_ppa',
            'instansi' => 'UPTD PPA',
            'activity_type' => 'login',
            'module' => 'auth',
            'reference' => $alphaRef,
            'description' => 'Login dari perangkat Chrome.',
            'status' => 'berhasil',
            'ip_address' => '192.168.10.5',
            'user_agent' => 'Mozilla/5.0 Chrome/120',
        ]);

        $this->log([
            'nama' => 'Auditor Beta ' . $betaRef,
            'role' => 'admin_dinsos',
            'instansi' => 'Dinas Sosial',
            'activity_type' => 'data',
            'module' => 'akun',
            'reference' => $betaRef,
            'description' => 'Perubahan data administratif.',
            'status' => 'gagal',
            'ip_address' => '10.0.0.9',
            'user_agent' => 'Safari/17',
        ]);

        $this->actingAs($this->superadmin);

        // Nama
        $this->get(route('superadmin.audit.index', ['q' => $alphaRef]))
            ->assertOk()
            ->assertSee($alphaRef);

        // Label jenis aktivitas ("Login Sistem") harus dipetakan ke key login.
        $this->get(route('superadmin.audit.index', [
            'q' => 'Login Sistem',
            'akun_id' => $this->superadmin->id_akun,
            'tab' => 'login',
        ]))
            ->assertOk()
            ->assertSee($alphaRef);

        // Label jenis aktivitas "Perubahan Data Administratif" -> key data.
        $response = $this->get(route('superadmin.audit.index', [
            'q' => 'Perubahan Data Administratif',
            'akun_id' => $this->superadmin->id_akun,
        ]));
        $response->assertOk();
        $this->assertContains($betaRef, $response->viewData('activities')->getCollection()->pluck('reference')->all());

        // Role label "Admin Dinsos".
        $response = $this->get(route('superadmin.audit.index', [
            'q' => 'Admin Dinsos',
            'akun_id' => $this->superadmin->id_akun,
        ]));
        $response->assertOk();
        $this->assertContains($betaRef, $response->viewData('activities')->getCollection()->pluck('reference')->all());

        // Modul
        $this->get(route('superadmin.audit.index', [
            'q' => 'akun',
            'akun_id' => $this->superadmin->id_akun,
        ]))
            ->assertOk()
            ->assertSee($betaRef);

        // Status
        $this->get(route('superadmin.audit.index', [
            'q' => 'gagal',
            'akun_id' => $this->superadmin->id_akun,
        ]))
            ->assertOk()
            ->assertSee($betaRef);

        // IP dan user agent sengaja tidak dicakup Global Search.

        // ID akun (memakai filter khusus, bukan kolom pencarian)
        $this->get(route('superadmin.audit.index', [
            'akun_id' => (string) $this->superadmin->id_akun,
            'tab' => 'semua',
        ]))
            ->assertOk()
            ->assertSee($alphaRef);
    }

    public function test_keyword_angka_mencari_dalam_atribut_data_bukan_nomor_urut(): void
    {
        $markerNine = 'AuditAngkaSembilan';
        $withoutNine = 'AuditTanpaDigit';

        $this->logAt('2026-11-19 13:30:00', [
            'nama' => $markerNine,
            'akun_id' => $this->superadmin->id_akun,
            'reference' => 'DOC-009',
            'description' => 'Dokumen nomor 009 berhasil diproses.',
            'ip_address' => '192.168.1.9',
        ]);

        $this->logAt('2026-11-18 13:30:00', [
            'nama' => $withoutNine,
            'akun_id' => $this->superadmin->id_akun,
            'reference' => 'DOC-ABC',
            'description' => 'Dokumen tanpa angka.',
            'ip_address' => '192.168.1.2',
        ]);

        $this->actingAs($this->superadmin);

        $response = $this->get(route('superadmin.audit.index', [
            'q' => '9',
            'akun_id' => $this->superadmin->id_akun,
        ]));
        $response->assertOk();
        $names = $response->viewData('activities')->getCollection()->pluck('nama')->all();

        $this->assertContains($markerNine, $names);
        $this->assertNotContains($withoutNine, $names);
    }

    public function test_filter_audit_bisa_dikombinasikan_dan_rentang_tanggal_dihormati(): void
    {
        $token = 'auditrange-' . Str::random(6);

        $oldName = 'Audit Lama ' . Str::random(5);
        $recentName = 'Audit Baru ' . Str::random(5);

        $old = $this->log([
            'nama' => $oldName,
            'reference' => $token . '-lama',
            'activity_type' => 'dokumen',
            'instansi' => 'UPTD PPA',
            'status' => 'berhasil',
        ]);

        ActivityLog::where('id', $old->id)->update(['created_at' => now()->subDays(10)]);

        $recent = $this->log([
            'nama' => $recentName,
            'reference' => $token . '-baru',
            'activity_type' => 'data',
            'instansi' => 'Superadmin',
            'status' => 'gagal',
        ]);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.audit.index', [
            'q' => $old->reference,
            'instansi' => 'UPTD PPA',
            'jenis' => 'dokumen',
            'status' => 'berhasil',
        ]))
            ->assertOk()
            ->assertSee($oldName)
            ->assertDontSee($recentName);

        $response = $this->get(route('superadmin.audit.index', [
            'q' => $token,
            'dari' => now()->subDay()->toDateString(),
            'sampai' => now()->toDateString(),
        ]));

        $response
            ->assertOk()
            ->assertSee($recentName)
            ->assertDontSee($oldName);
    }

    public function test_keyword_wildcard_like_tidak_diperlakukan_sebagai_wildcard(): void
    {
        $marker = 'AuditWildcard' . Str::random(5);
        $this->log(['nama' => $marker, 'reference' => 'ref-' . $marker]);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.audit.index', ['q' => '%']))
            ->assertOk()
            ->assertDontSee($marker);

        $this->get(route('superadmin.audit.index', ['q' => '_']))
            ->assertOk()
            ->assertDontSee($marker);
    }

    public function test_empty_state_membedakan_filter_kosong_dan_data_kosong(): void
    {
        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.audit.index', ['q' => 'tidak-ada-hasil-' . Str::random(10)]))
            ->assertOk()
            ->assertSee('Tidak ada aktivitas yang sesuai filter.');
    }

    public function test_kata_kunci_tanda_kurung_dicocokkan_secara_literal(): void
    {
        $markerWithParentheses = 'Aktivitas (sentra) ' . Str::random(5);
        $markerWithoutParentheses = 'Aktivitas sentra ' . Str::random(5);

        $this->log([
            'nama' => $markerWithParentheses,
            'description' => 'Aktivitas dari portal (sentra).',
        ]);

        $this->log([
            'nama' => $markerWithoutParentheses,
            'description' => 'Aktivitas dari portal sentra.',
        ]);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.audit.index', ['q' => '(sentra)']))
            ->assertOk()
            ->assertSee($markerWithParentheses)
            ->assertDontSee($markerWithoutParentheses);

        $this->get(route('superadmin.audit.index', ['q' => '(tidak-ada)']))
            ->assertOk()
            ->assertSee('Tidak ada aktivitas yang sesuai filter.')
            ->assertDontSee($markerWithParentheses)
            ->assertDontSee($markerWithoutParentheses);
    }

    public function test_kurung_buka_dan_kurung_tutup_dicari_secara_literal(): void
    {
        $openParenthesis = 'Aktivitas buka ( ' . Str::random(5);
        $closeParenthesis = 'Aktivitas tutup ) ' . Str::random(5);
        $withoutParenthesis = 'Aktivitas tanpa kurung ' . Str::random(5);

        $this->log([
            'nama' => $openParenthesis,
            'description' => 'Referensi memiliki kurung buka (',
        ]);

        $this->log([
            'nama' => $closeParenthesis,
            'description' => 'Referensi memiliki kurung tutup )',
        ]);

        $this->log([
            'nama' => $withoutParenthesis,
            'description' => 'Referensi tidak memiliki tanda kurung',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0)',
        ]);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.audit.index', ['q' => '(']))
            ->assertOk()
            ->assertSee($openParenthesis)
            ->assertDontSee($closeParenthesis)
            ->assertDontSee($withoutParenthesis);

        $this->get(route('superadmin.audit.index', ['q' => ')']))
            ->assertOk()
            ->assertSee($closeParenthesis)
            ->assertDontSee($openParenthesis)
            ->assertDontSee($withoutParenthesis);
    }

    public function test_filter_instansi_audit_hanya_tiga_instansi_resmi(): void
    {
        $this->actingAs($this->superadmin);

        $response = $this->get(route('superadmin.audit.index'));
        $response->assertOk();

        $this->assertSame(
            ['Dinas Sosial', 'Superadmin', 'UPTD PPA'],
            array_values($response->viewData('instansiList'))
        );
    }

    public function test_global_search_mengenali_bulan_tanggal_dan_tahun(): void
    {
        $nov26 = $this->logAt('2026-11-19 13:30:00', ['nama' => 'Audit Nov26 ' . Str::random(5)]);
        $nov25 = $this->logAt('2025-11-19 09:00:00', ['nama' => 'Audit Nov25 ' . Str::random(5)]);
        $okt25 = $this->logAt('2025-10-19 13:30:00', ['nama' => 'Audit Okt25 ' . Str::random(5)]);
        $dec26 = $this->logAt('2026-12-19 13:30:00', ['nama' => 'Audit Dec26 ' . Str::random(5)]);

        $this->actingAs($this->superadmin);

        // Batasi ke akun uji agar data aktivitas lain di database tidak
        // memenuhi halaman pertama pagination.
        $cari = fn (string $q) => $this->get(route('superadmin.audit.index', [
            'q' => $q,
            'akun_id' => $this->superadmin->id_akun,
        ]))->assertOk();

        // Nama bulan lengkap dan singkatan.
        $cari('nov')
            ->assertSee($nov26->nama)
            ->assertSee($nov25->nama)
            ->assertDontSee($okt25->nama)
            ->assertDontSee($dec26->nama);

        $cari('November')
            ->assertSee($nov26->nama)
            ->assertSee($nov25->nama)
            ->assertDontSee($okt25->nama);

        // Bulan + tahun.
        $cari('nov 2026')
            ->assertSee($nov26->nama)
            ->assertDontSee($nov25->nama)
            ->assertDontSee($dec26->nama);

        $cari('okt 2025')
            ->assertSee($okt25->nama)
            ->assertDontSee($nov25->nama)
            ->assertDontSee($nov26->nama);

        $cari('nov 2026 13:30')
            ->assertSee($nov26->nama)
            ->assertDontSee($nov25->nama)
            ->assertDontSee($dec26->nama);

        // Tahun.
        $cari('2026')
            ->assertSee($nov26->nama)
            ->assertSee($dec26->nama)
            ->assertDontSee($nov25->nama)
            ->assertDontSee($okt25->nama);

        // Tanggal lengkap dalam tiga format.
        foreach (['19 November 2026', '19-11-2026', '2026-11-19'] as $format) {
            $cari($format)
                ->assertSee($nov26->nama)
                ->assertDontSee($nov25->nama)
                ->assertDontSee($dec26->nama);
        }

        // Waktu (jam:menit).
        $cari('13:30')
            ->assertSee($nov26->nama)
            ->assertSee($okt25->nama)
            ->assertSee($dec26->nama)
            ->assertDontSee($nov25->nama);
    }

    public function test_pencarian_potongan_nama_bulan_no_menemukan_november(): void
    {
        $akunId = $this->superadmin->id_akun;

        $november = $this->logAt('2026-11-05 10:00:00', [
            'nama' => 'Audit Bulan Sebelas',
            'akun_id' => $akunId,
            'reference' => 'REF-SEBELAS',
            'description' => 'Catatan periode.',
        ]);

        $desember = $this->logAt('2026-12-05 10:00:00', [
            'nama' => 'Audit Bulan Dua Belas',
            'akun_id' => $akunId,
            'reference' => 'REF-DUA-BELAS',
            'description' => 'Catatan periode.',
        ]);

        $teksNo = $this->logAt('2026-10-05 10:00:00', [
            'nama' => 'Catatan Nomor',
            'akun_id' => $akunId,
            'reference' => 'REF-NOMOR',
            'description' => 'Catatan teks.',
        ]);

        $this->actingAs($this->superadmin);

        $response = $this->get(route('superadmin.audit.index', [
            'q' => 'no',
            'akun_id' => $akunId,
        ]));

        $response->assertOk();
        $names = $response->viewData('activities')->getCollection()->pluck('nama')->all();

        $this->assertContains($november->nama, $names);
        $this->assertContains($teksNo->nama, $names);
        $this->assertNotContains($desember->nama, $names);
    }

    public function test_kata_kunci_bulan_persis_menggabungkan_teks_dan_bulan(): void
    {
        $akunId = $this->superadmin->id_akun;

        $november = $this->logAt('2026-11-10 10:00:00', [
            'nama' => 'Audit Bulan Sebelas',
            'akun_id' => $akunId,
            'reference' => 'REF-SEBELAS',
            'description' => 'Catatan periode.',
        ]);

        $teksNov = $this->logAt('2026-10-10 10:00:00', [
            'nama' => 'Catatan Nov',
            'akun_id' => $akunId,
            'reference' => 'REF-TEKS',
            'description' => 'Catatan lain.',
        ]);

        $desember = $this->logAt('2026-12-10 10:00:00', [
            'nama' => 'Audit Bulan Dua Belas',
            'akun_id' => $akunId,
            'reference' => 'REF-DUA-BELAS',
            'description' => 'Catatan periode.',
        ]);

        $this->actingAs($this->superadmin);

        $response = $this->get(route('superadmin.audit.index', [
            'q' => 'nov',
            'akun_id' => $akunId,
        ]));

        $response->assertOk();
        $names = $response->viewData('activities')->getCollection()->pluck('nama')->all();

        $this->assertContains($november->nama, $names);
        $this->assertContains($teksNov->nama, $names);
        $this->assertNotContains($desember->nama, $names);
    }

    public function test_pencarian_potongan_bulan_dengan_tahun_dan_tanggal(): void
    {
        $akunId = $this->superadmin->id_akun;

        $nov26 = $this->logAt('2026-11-19 10:00:00', [
            'nama' => 'Audit Tahun Dua Ribu Dua Puluh Enam',
            'akun_id' => $akunId,
            'reference' => 'REF-2026',
            'description' => 'Catatan periode.',
        ]);

        $nov25 = $this->logAt('2025-11-19 10:00:00', [
            'nama' => 'Audit Tahun Dua Ribu Dua Puluh Lima',
            'akun_id' => $akunId,
            'reference' => 'REF-2025',
            'description' => 'Catatan periode.',
        ]);

        $des26 = $this->logAt('2026-12-19 10:00:00', [
            'nama' => 'Audit Bulan Dua Belas',
            'akun_id' => $akunId,
            'reference' => 'REF-DUA-BELAS',
            'description' => 'Catatan periode.',
        ]);

        $this->actingAs($this->superadmin);

        $bulanTahun = $this->get(route('superadmin.audit.index', [
            'q' => 'no 2026',
            'akun_id' => $akunId,
        ]));
        $bulanTahun->assertOk();
        $bulanTahunNames = $bulanTahun->viewData('activities')->getCollection()->pluck('nama')->all();

        $this->assertContains($nov26->nama, $bulanTahunNames);
        $this->assertNotContains($nov25->nama, $bulanTahunNames);
        $this->assertNotContains($des26->nama, $bulanTahunNames);

        $tanggal = $this->get(route('superadmin.audit.index', [
            'q' => '19 no 2026',
            'akun_id' => $akunId,
        ]));
        $tanggal->assertOk();
        $tanggalNames = $tanggal->viewData('activities')->getCollection()->pluck('nama')->all();

        $this->assertContains($nov26->nama, $tanggalNames);
        $this->assertNotContains($nov25->nama, $tanggalNames);
        $this->assertNotContains($des26->nama, $tanggalNames);
    }

    public function test_angka_satu_digit_mencari_hari_secara_parsial(): void
    {
        $tanggal9 = 'AuditHariSembilan';
        $tanggal19 = 'AuditHariSembilanBelas';
        $tanggal29 = 'AuditHariDuaSembilan';
        $tanggal8 = 'AuditHariDelapan';

        foreach ([
            ['2026-11-09 13:30:00', $tanggal9, 'REF-A'],
            ['2026-11-19 13:30:00', $tanggal19, 'REF-B'],
            ['2026-11-29 13:30:00', $tanggal29, 'REF-C'],
            ['2026-11-08 13:30:00', $tanggal8, 'REF-D'],
        ] as [$createdAt, $nama, $reference]) {
            $this->logAt($createdAt, [
                'nama' => $nama,
                'akun_id' => $this->superadmin->id_akun,
                'reference' => $reference,
                'description' => 'Catatan tanpa angka.',
                'ip_address' => null,
            ]);
        }

        $this->actingAs($this->superadmin);

        $response = $this->get(route('superadmin.audit.index', [
            'q' => '9',
            'akun_id' => $this->superadmin->id_akun,
        ]));
        $response->assertOk();
        $names = $response->viewData('activities')->getCollection()->pluck('nama')->all();

        $this->assertContains($tanggal9, $names);
        $this->assertContains($tanggal19, $names);
        $this->assertContains($tanggal29, $names);
        $this->assertNotContains($tanggal8, $names);
    }

    public function test_angka_bilangan_dua_digit_mencari_hari_tanggal(): void
    {
        $tanggal19 = 'AuditHariSembilanBelas';
        $tanggal18 = 'AuditHariDelapanBelas';

        $this->logAt('2026-11-19 13:30:00', [
            'nama' => $tanggal19,
            'akun_id' => $this->superadmin->id_akun,
            'reference' => 'REF-E',
            'ip_address' => null,
        ]);

        $this->logAt('2026-11-18 13:30:00', [
            'nama' => $tanggal18,
            'akun_id' => $this->superadmin->id_akun,
            'reference' => 'REF-F',
            'ip_address' => null,
        ]);

        $this->actingAs($this->superadmin);

        $response = $this->get(route('superadmin.audit.index', [
            'q' => '19',
            'akun_id' => $this->superadmin->id_akun,
        ]));
        $response->assertOk();
        $names = $response->viewData('activities')->getCollection()->pluck('nama')->all();

        $this->assertContains($tanggal19, $names);
        $this->assertNotContains($tanggal18, $names);
    }

    public function test_filter_id_akun_dan_referensi_tanpa_ip(): void
    {
        $marker = 'AuditIdentitasNoRandom';
        $reference = 'DOC-009-' . Str::random(5);

        $this->logAt('2026-11-19 10:00:00', [
            'nama' => $marker,
            'akun_id' => $this->superadmin->id_akun,
            'role' => 'superadmin',
            'instansi' => 'Superadmin',
            'reference' => $reference,
            'ip_address' => '192.168.1.9',
        ]);

        $this->actingAs($this->superadmin);

        // ID akun memakai filter khusus: angka 1900–2100 di pencarian global
        // diperlakukan sebagai tahun, sehingga filter akun lebih tepat.
        $this->get(route('superadmin.audit.index', [
            'akun_id' => (string) $this->superadmin->id_akun,
        ]))
            ->assertOk()
            ->assertSee($marker);

        $this->get(route('superadmin.audit.index', ['q' => '192.168.1.9']))
            ->assertOk()
            ->assertDontSee($marker);

        $this->get(route('superadmin.audit.index', [
            'q' => $reference,
            'akun_id' => $this->superadmin->id_akun,
        ]))
            ->assertOk()
            ->assertSee($marker);

        $this->get(route('superadmin.audit.index', [
            'q' => 'Superadmin',
            'akun_id' => $this->superadmin->id_akun,
        ]))
            ->assertOk()
            ->assertSee($marker);
    }

    public function test_global_search_dan_filter_manual_bulan_tahun_berlaku_and(): void
    {
        $ppaNov = $this->logAt('2026-11-19 13:30:00', [
            'nama' => 'PPA Nov ' . Str::random(5),
            'instansi' => 'UPTD PPA',
            'role' => 'admin_ppa',
        ]);

        $dinsosNov = $this->logAt('2026-11-20 13:30:00', [
            'nama' => 'Dinsos Nov ' . Str::random(5),
            'instansi' => 'Dinas Sosial',
            'role' => 'admin_dinsos',
        ]);

        $ppaDec = $this->logAt('2026-12-19 13:30:00', [
            'nama' => 'PPA Dec ' . Str::random(5),
            'instansi' => 'UPTD PPA',
            'role' => 'admin_ppa',
        ]);

        $this->actingAs($this->superadmin);

        $akunId = $this->superadmin->id_akun;

        // Pencarian global bekerja pada halaman Aktivitas Admin PPA.
        $this->get(route('superadmin.audit.index', ['q' => 'nov', 'instansi' => 'UPTD PPA', 'akun_id' => $akunId]))
            ->assertOk()
            ->assertSee($ppaNov->nama)
            ->assertDontSee($dinsosNov->nama)
            ->assertDontSee($ppaDec->nama);

        // ... dan pada halaman Aktivitas Admin Dinsos.
        $this->get(route('superadmin.audit.index', ['q' => 'nov', 'instansi' => 'Dinas Sosial', 'akun_id' => $akunId]))
            ->assertOk()
            ->assertSee($dinsosNov->nama)
            ->assertDontSee($ppaNov->nama)
            ->assertDontSee($ppaDec->nama);

        // Bulan/tahun manual tetap menjadi AND dengan pencarian global.
        $this->get(route('superadmin.audit.index', ['q' => 'nov', 'bulan' => 11, 'tahun' => 2026, 'akun_id' => $akunId]))
            ->assertOk()
            ->assertSee($ppaNov->nama)
            ->assertSee($dinsosNov->nama)
            ->assertDontSee($ppaDec->nama);

        $this->get(route('superadmin.audit.index', ['q' => 'nov', 'bulan' => 12, 'tahun' => 2026, 'akun_id' => $akunId]))
            ->assertOk()
            ->assertDontSee($ppaNov->nama)
            ->assertDontSee($dinsosNov->nama);

        $this->get(route('superadmin.audit.index', ['q' => 'nov', 'tahun' => 2025, 'akun_id' => $akunId]))
            ->assertOk()
            ->assertDontSee($ppaNov->nama)
            ->assertDontSee($dinsosNov->nama);
    }

    public function test_pagination_pencarian_mencakup_seluruh_dataset(): void
    {
        $token = 'PaginateAudit' . Str::random(6);

        for ($i = 0; $i < 17; $i++) {
            $this->logAt('2026-11-19 10:' . str_pad((string) $i, 2, '0', STR_PAD_LEFT) . ':00', [
                'nama' => $token . ' nomor ' . $i,
            ]);
        }

        $this->actingAs($this->superadmin);

        $response = $this->get(route('superadmin.audit.index', ['q' => $token]));

        $response->assertOk();
        $this->assertSame(17, $response->viewData('activities')->total());
        $this->assertSame(15, $response->viewData('activities')->count());
    }

    public function test_ui_realtime_search_debounce_dan_loading_state(): void
    {
        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.audit.index'))
            ->assertOk()
            ->assertSee('data-live-debounce="300"', false)
            ->assertSee('live-search-loading', false)
            ->assertSee('data-live-region="audit-chips"', false)
            ->assertSee('data-live-region="audit-tabs"', false)
            ->assertSee('Penting')
            ->assertSee('Semua')
            ->assertSee('<input type="hidden" name="tab" value="penting">', false)
            ->assertSee('Bulan Aktivitas')
            ->assertSee('Tahun Aktivitas')
            ->assertSee('<th style="width: 50px;">No</th>', false)
            ->assertSee('<th style="width: 100px;">ID Aktivitas</th>', false)
            ->assertSee('<th style="width: 80px;">ID Akun</th>', false)
            ->assertSee('ID aktivitas', false)
            ->assertSee('__auditRealtimeTimer', false)
            ->assertSee('3000', false);
    }

    public function test_kolom_id_aktivitas_dan_id_akun_ditampilkan(): void
    {
        $log = $this->logAt('2026-11-20 10:00:00', [
            'nama' => 'Pelaku Uji Kolom',
            'akun_id' => $this->superadmin->id_akun,
            'reference' => 'REF-KOLOM-ID',
        ]);

        $this->actingAs($this->superadmin);

        $this->get(route('superadmin.audit.index', [
            'q' => (string) $log->id,
            'akun_id' => $this->superadmin->id_akun,
        ]))
            ->assertOk()
            ->assertSee('<th style="width: 100px;">ID Aktivitas</th>', false)
            ->assertSee('<th style="width: 80px;">ID Akun</th>', false)
            ->assertSee((string) $log->id)
            ->assertSee($this->superadmin->kode_akun ?: (string) $this->superadmin->id_akun);
    }

    public function test_satu_karakter_v_dan_z_tetap_dicari(): void
    {
        $akunId = $this->superadmin->id_akun;

        $markerV = 'Audit Huruf V';
        $markerZ = 'Audit Huruf Z';
        $markerNone = 'Audit Tanpa Huruf';
        $markerNovember = 'Aktivitas Bulan Sebelas';

        $this->logAt('2026-10-12 10:00:00', [
            'nama' => $markerV,
            'akun_id' => $akunId,
            'reference' => 'ALPHA-V',
            'description' => 'Catatan huruf v.',
        ]);

        $this->logAt('2026-10-12 10:00:00', [
            'nama' => $markerZ,
            'akun_id' => $akunId,
            'reference' => 'CHARLIE-Z',
            'description' => 'Catatan huruf z.',
        ]);

        $this->logAt('2026-12-12 10:00:00', [
            'nama' => $markerNone,
            'akun_id' => $akunId,
            'reference' => 'DELTA-123',
            'description' => 'Catatan tanpa huruf.',
        ]);

        $this->logAt('2026-11-12 15:57:00', [
            'nama' => $markerNovember,
            'akun_id' => $akunId,
            'reference' => 'BULAN-SEBELAS',
            'description' => 'Catatan bulan sebelas.',
        ]);

        $this->actingAs($this->superadmin);

        $forV = $this->get(route('superadmin.audit.index', ['q' => 'v', 'akun_id' => $akunId]));
        $forV->assertOk();
        $vNames = $forV->viewData('activities')->getCollection()->pluck('nama')->all();
        $this->assertContains($markerV, $vNames);
        $this->assertContains($markerNovember, $vNames);
        $this->assertNotContains($markerZ, $vNames);
        $this->assertNotContains($markerNone, $vNames);

        $forZ = $this->get(route('superadmin.audit.index', ['q' => 'z', 'akun_id' => $akunId]));
        $forZ->assertOk();
        $zNames = $forZ->viewData('activities')->getCollection()->pluck('nama')->all();
        $this->assertContains($markerZ, $zNames);
        $this->assertNotContains($markerV, $zNames);
        $this->assertNotContains($markerNone, $zNames);
    }
}
