<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Support\ActivitySearchParser;
use App\Support\ActivitySearchService;
use Illuminate\Http\Request;

/**
 * Audit Aktivitas Sistem.
 *
 * Superadmin hanya dapat melihat metadata aktivitas administratif
 * (login, perubahan status laporan, unggah dokumen, perubahan data).
 * Isi laporan dan identitas pelapor/korban tidak pernah ditampilkan di sini.
 */
class SuperadminAuditController extends Controller
{
    public function index(Request $request, ActivitySearchService $search)
    {
        $filters = $request->validate([
            'q'       => ['nullable', 'string', 'max:100'],
            'instansi'=> ['nullable', 'string', 'max:50'],
            'jenis'   => ['nullable', 'string', 'max:40'],
            'role'    => ['nullable', 'string', 'max:30'],
            'status'  => ['nullable', 'string', 'max:20'],
            'module'  => ['nullable', 'string', 'max:40'],
            'akun_id' => ['nullable', 'string', 'max:32'],
            'bulan'   => ['nullable', 'integer', 'between:1,12'],
            'tahun'   => ['nullable', 'integer', 'between:1900,2100'],
            'dari'    => ['nullable', 'date'],
            'sampai'  => ['nullable', 'date'],
            'tanggal' => ['nullable', 'date'],
            'tab'     => ['nullable', 'in:penting,login,semua'],
            'sort'    => ['nullable', 'in:terbaru,terlama'],
        ]);

        // Tab default: "Penting" agar audit tidak penuh login/logout rutin.
        $filters['tab'] = $filters['tab'] ?? 'penting';

        // Kompatibilitas filter lama: ?tanggal=YYYY-MM-DD.
        if (! empty($filters['tanggal'])) {
            $filters['dari'] = $filters['dari'] ?? $filters['tanggal'];
            $filters['sampai'] = $filters['sampai'] ?? $filters['tanggal'];
        }

        // Jika rentang terbalik, tukar agar hasil tetap masuk akal.
        if (! empty($filters['dari']) && ! empty($filters['sampai'])
            && strtotime((string) $filters['dari']) > strtotime((string) $filters['sampai'])) {
            [$filters['dari'], $filters['sampai']] = [$filters['sampai'], $filters['dari']];
        }

        $query = ActivityLog::with('akun');

        // Filter manual selalu diterapkan lebih dulu (logika AND), lalu
        // Global Search menambahkan kondisi OR antar atribut di atasnya.
        $search->applyFilters($query, $filters);

        $keyword = trim((string) ($filters['q'] ?? ''));
        $sort = ($filters['sort'] ?? 'terbaru') === 'terlama' ? 'asc' : 'desc';
        $parsed = $keyword === '' ? null : ActivitySearchParser::parse($keyword);
        if ($parsed !== null && $parsed['type'] !== 'empty') {
            $search->parseAndApply($query, $keyword);
        }

        $search->applyRelevance($query, $keyword);

        $activities = $query->orderBy('created_at', $sort)
            ->orderBy('id', $sort)
            ->paginate(15)
            ->withQueryString();

        $options = $this->filterOptions();

        return view('backend.superadmin.audit.index', array_merge(
            compact('activities', 'filters'),
            $options
        ));
    }

    public function show($id)
    {
        // Hanya metadata aktivitas — tanpa isi laporan / identitas pelapor.
        $activity = ActivityLog::with('akun')->findOrFail($id);

        return view('backend.superadmin.audit.show', compact('activity'));
    }

    /**
     * @return array{instansiList: array<string, string>, jenisList: array<string, string>, roleList: array<string, string>, moduleList: array<string, string>, statusList: array<string, string>, bulanList: array<int, string>, tahunList: array<string, string>}
     */
    private function filterOptions(): array
    {
        // Filter instansi audit dibatasi hanya untuk tiga instansi resmi.
        $instansiList = [
            'UPTD PPA'     => 'UPTD PPA',
            'Dinas Sosial' => 'Dinas Sosial',
            'Superadmin'   => 'Superadmin',
        ];

        $jenisValues = ActivityLog::query()
            ->whereNotNull('activity_type')
            ->distinct()
            ->orderBy('activity_type')
            ->pluck('activity_type')
            ->filter()
            ->values();

        $jenisList = $jenisValues
            ->mapWithKeys(fn (string $value) => [$value => ActivityLog::typeLabel($value)])
            ->all();

        $roleValues = ActivityLog::query()
            ->whereNotNull('role')
            ->distinct()
            ->orderBy('role')
            ->pluck('role')
            ->filter()
            ->values();

        $roleList = $roleValues
            ->mapWithKeys(fn (string $value) => [$value => ActivityLog::roleLabel($value)])
            ->all();

        $moduleValues = ActivityLog::query()
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module')
            ->filter()
            ->values();

        $moduleList = $moduleValues
            ->mapWithKeys(fn (string $value) => [$value => ActivityLog::moduleLabel($value)])
            ->all();

        $statusValues = ActivityLog::query()
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->filter()
            ->values();

        $statusList = $statusValues
            ->mapWithKeys(fn (string $value) => [$value => ActivityLog::statusLabel($value)])
            ->all();

        $jenisList = array_merge(ActivityLog::TYPES, $jenisList);
        $roleList = array_merge(ActivityLog::ROLE_LABELS, $roleList);
        $statusList = array_merge(ActivityLog::STATUSES, $statusList);

        if (isset($roleList['admin'])) {
            $roleList['admin'] = 'Admin PPA (Legacy)';
        }

        $bulanList = ActivitySearchParser::MONTH_LABELS;

        $tahunValues = ActivityLog::query()
            ->selectRaw('YEAR(created_at) as tahun')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->filter(fn ($value) => $value !== null)
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        $tahunList = $tahunValues
            ->mapWithKeys(fn (int $value) => [(string) $value => (string) $value])
            ->all();

        // Pastikan tahun berjalan tetap tersedia sebagai pilihan filter.
        $tahunList[(string) now()->year] = (string) now()->year;
        krsort($tahunList);

        asort($instansiList);
        asort($jenisList);
        asort($roleList);
        asort($moduleList);
        asort($statusList);

        return compact('instansiList', 'jenisList', 'roleList', 'moduleList', 'statusList', 'bulanList', 'tahunList');
    }
}
