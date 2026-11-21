@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Audit Aktivitas Sistem',
        'subtitle' => 'Pemantauan metadata aktivitas administrator: login, perubahan status laporan, unggah dokumen, dan perubahan data administratif.',
        'icon' => 'fa-history',
    ])

    @include('backend.superadmin.components.alerts')

    <div class="card p-2">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" data-live-region="audit-meta">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Daftar Aktivitas Administrator</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;" aria-live="polite">
                        Menampilkan {{ $activities->count() }} dari {{ $activities->total() }} aktivitas tercatat.
                    </p>
                </div>
                <div class="d-flex flex-wrap align-items-center gap-2 mt-2 mt-sm-0">
                    <span class="live-search-loading badge badge-soft-amber badge-pill-soft" style="font-size: 0.76rem;">
                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Mencari...
                    </span>
                    <span class="badge badge-soft-blue badge-pill-soft" style="font-size: 0.76rem;">
                        <i class="fa fa-history me-1"></i> Total: {{ $activities->total() }} Aktivitas
                    </span>
                </div>
            </div>

            {{-- Tab Audit --}}
            @php
                $activeTab = $filters['tab'] ?? 'penting';
                $tabBase = request()->except(['tab', 'page']);
                $auditTabs = [
                    'penting' => ['label' => 'Penting', 'icon' => 'fa-shield-halved'],
                    'login' => ['label' => 'Login', 'icon' => 'fa-key'],
                    'semua' => ['label' => 'Semua', 'icon' => 'fa-list'],
                ];
            @endphp
            <div class="d-flex flex-wrap align-items-center gap-2 mb-2" role="group" aria-label="Tab audit aktivitas" data-live-region="audit-tabs">
                <span class="text-muted small fw-bold me-1"><i class="fa fa-layer-group me-1"></i> Tampilkan:</span>
                @foreach ($auditTabs as $tabKey => $tab)
                    <a href="{{ route('superadmin.audit.index', array_merge($tabBase, ['tab' => $tabKey])) }}"
                        wire:navigate
                        class="btn btn-sm {{ $activeTab === $tabKey ? 'btn-oripio-primary' : 'btn-oripio-light' }} btn-pill"
                        @if ($activeTab === $tabKey) aria-current="page" @endif>
                        <i class="fa {{ $tab['icon'] }} me-1"></i> {{ $tab['label'] }}
                    </a>
                @endforeach
            </div>
            <p class="text-muted mb-3" style="font-size: 0.78rem;">
                <i class="fa fa-circle-info me-1"></i>
                <strong>Penting</strong> menampilkan perubahan data, keamanan, laporan, dan dokumen tanpa login/logout rutin.
                <strong>Login</strong> khusus riwayat autentikasi (termasuk login gagal).
                <strong>Semua</strong> menampilkan seluruh catatan.
            </p>

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('superadmin.audit.index') }}" data-livewire-navigate data-live-debounce="300" class="mb-3">
                <input type="hidden" name="tab" value="{{ $filters['tab'] ?? 'penting' }}">
                <div class="row g-2 align-items-end">
                    <div class="col-xl-4 col-lg-6 col-md-12">
                        <label class="small fw-bold text-muted mb-1" for="auditQ">Pencarian Global</label>
                        <div class="search-pill-wrapper w-100" style="height: 44px;">
                            <i class="fa fa-search text-muted"></i>
                            <input id="auditQ" type="search" name="q" autocomplete="off"
                                placeholder="Nama, role, referensi, 19 Nov 2026, nov 2026, 13:30..."
                                value="{{ $filters['q'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditInstansi">Instansi</label>
                        <select id="auditInstansi" name="instansi" class="form-control">
                            <option value="">Semua Instansi</option>
                            @foreach ($instansiList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['instansi'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditJenis">Jenis Aktivitas</label>
                        <select id="auditJenis" name="jenis" class="form-control">
                            <option value="">Semua Jenis</option>
                            @foreach ($jenisList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['jenis'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditRole">Role</label>
                        <select id="auditRole" name="role" class="form-control">
                            <option value="">Semua Role</option>
                            @foreach ($roleList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['role'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditStatus">Status</label>
                        <select id="auditStatus" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach ($statusList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditModule">Modul</label>
                        <select id="auditModule" name="module" class="form-control">
                            <option value="">Semua Modul</option>
                            @foreach ($moduleList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['module'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditBulan">Bulan Aktivitas</label>
                        <select id="auditBulan" name="bulan" class="form-control">
                            <option value="">Semua Bulan</option>
                            @foreach ($bulanList as $value => $label)
                                <option value="{{ $value }}" {{ (string) ($filters['bulan'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditTahun">Tahun Aktivitas</label>
                        <select id="auditTahun" name="tahun" class="form-control">
                            <option value="">Semua Tahun</option>
                            @foreach ($tahunList as $value => $label)
                                <option value="{{ $value }}" {{ (string) ($filters['tahun'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditAkunId">ID Akun</label>
                        <input id="auditAkunId" type="text" name="akun_id" class="form-control"
                            placeholder="Contoh: spadmin111" value="{{ $filters['akun_id'] ?? '' }}">
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditDari">Dari Tanggal</label>
                        <input id="auditDari" type="date" name="dari" class="form-control" value="{{ $filters['dari'] ?? '' }}">
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditSampai">Sampai Tanggal</label>
                        <input id="auditSampai" type="date" name="sampai" class="form-control" value="{{ $filters['sampai'] ?? '' }}">
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="auditSort">Urutkan</label>
                        <select id="auditSort" name="sort" class="form-control">
                            <option value="terbaru" {{ ($filters['sort'] ?? 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ ($filters['sort'] ?? '') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                    <a href="{{ route('superadmin.audit.index', ['tab' => $activeTab]) }}" wire:navigate class="btn btn-oripio-light btn-pill" style="height: 44px;">
                        <i class="fa fa-rotate-left me-1"></i> Reset Filter
                    </a>
                    <div class="d-inline-flex align-items-center gap-1 ms-sm-2">
                        <span class="text-muted small fw-bold me-1">Rentang cepat:</span>
                        @php
                            $quickBase = request()->except(['dari', 'sampai', 'tanggal']);
                        @endphp
                        <a href="{{ route('superadmin.audit.index', array_merge($quickBase, ['dari' => now()->toDateString(), 'sampai' => now()->toDateString()])) }}"
                            class="btn btn-sm btn-oripio-light btn-pill">Hari ini</a>
                        <a href="{{ route('superadmin.audit.index', array_merge($quickBase, ['dari' => now()->subDays(6)->toDateString(), 'sampai' => now()->toDateString()])) }}"
                            class="btn btn-sm btn-oripio-light btn-pill">7 hari</a>
                        <a href="{{ route('superadmin.audit.index', array_merge($quickBase, ['dari' => now()->subDays(29)->toDateString(), 'sampai' => now()->toDateString()])) }}"
                            class="btn btn-sm btn-oripio-light btn-pill">30 hari</a>
                    </div>
                    <span class="text-muted small ms-sm-auto">
                        <i class="fa fa-info-circle me-1"></i> Pencarian global mencakup ID aktivitas, ID akun, identitas, jenis, modul, referensi, deskripsi, status, tanggal, bulan (termasuk potongan nama bulan seperti <strong>no</strong> untuk November), tahun, dan jam.
                    </span>
                </div>
            </form>

            @php
                $activeFilterLabels = [
                    'q' => 'Kata kunci',
                    'instansi' => 'Instansi',
                    'jenis' => 'Jenis',
                    'role' => 'Role',
                    'status' => 'Status',
                    'module' => 'Modul',
                    'akun_id' => 'ID Akun',
                    'bulan' => 'Bulan',
                    'tahun' => 'Tahun',
                    'dari' => 'Dari',
                    'sampai' => 'Sampai',
                ];
                $activeFilters = collect($filters)
                    ->except(['sort', 'tanggal', 'tab'])
                    ->filter(fn ($value) => filled($value));
                $hasActiveFilters = $activeFilters->isNotEmpty();
            @endphp

            <div data-live-region="audit-chips">
            @if ($hasActiveFilters)
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="text-muted small fw-bold">Filter aktif:</span>
                    @foreach ($activeFilters as $key => $value)
                        @php
                            $displayValue = match ($key) {
                                'jenis' => \App\Models\ActivityLog::typeLabel($value),
                                'role' => \App\Models\ActivityLog::roleLabel($value),
                                'status' => \App\Models\ActivityLog::statusLabel($value),
                                'module' => \App\Models\ActivityLog::moduleLabel($value),
                                'bulan' => $bulanList[(int) $value] ?? $value,
                                default => $value,
                            };
                        @endphp
                        <a href="{{ route('superadmin.audit.index', request()->except($key)) }}"
                            data-live-link
                            class="badge badge-soft-blue badge-pill-soft text-decoration-none"
                            title="Hapus filter {{ $activeFilterLabels[$key] ?? $key }}">
                            {{ $activeFilterLabels[$key] ?? ucfirst($key) }}: {{ $displayValue }}
                            <i class="fa fa-times ms-1"></i>
                        </a>
                    @endforeach
                </div>
            @endif
            </div><!-- /data-live-region: audit-chips -->

            {{-- Area hasil filter: ditukar parsial oleh live filter (tanpa reload) --}}
            <div data-live-region="audit-results">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th style="width: 100px;">ID Aktivitas</th>
                            <th style="width: 80px;">ID Akun</th>
                            <th>Nama Administrator</th>
                            <th>Role</th>
                            <th>Instansi</th>
                            <th>Jenis Aktivitas</th>
                            <th>Waktu Aktivitas</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $index => $item)
                            <tr>
                                <td class="text-muted fw-bold">
                                    {{ $activities->firstItem() + $index }}
                                </td>
                                <td class="text-muted" style="font-size: 0.82rem;">{{ $item->id }}</td>
                                <td class="text-muted" style="font-size: 0.82rem;">{{ $item->akun?->kode_akun ?: ($item->akun_id ?? '-') }}</td>
                                <td><span class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $item->nama }}</span></td>
                                <td><span class="badge-pill-soft badge-soft-gray" style="font-size: 0.74rem;">{{ \App\Models\ActivityLog::roleLabel($item->role) }}</span></td>
                                <td>
                                    @if ($item->instansi === 'UPTD PPA')
                                        <span class="badge-pill-soft badge-soft-blue">UPTD PPA</span>
                                    @elseif ($item->instansi === 'Dinas Sosial')
                                        <span class="badge-pill-soft badge-soft-amber">Dinas Sosial</span>
                                    @elseif ($item->instansi === 'Superadmin')
                                        <span class="badge-pill-soft badge-soft-green">Superadmin</span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-gray">{{ $item->instansi }}</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.84rem;">{{ \App\Models\ActivityLog::typeLabel($item->activity_type) }}</td>
                                <td class="text-muted text-nowrap" style="font-size: 0.82rem;">
                                    {{ $item->created_at ? $item->created_at->format('d M Y H:i:s') : '-' }}
                                </td>
                                <td>
                                    @if ($item->status === 'berhasil')
                                        <span class="badge-pill-soft badge-soft-green"><i class="fa fa-check me-1"></i> Berhasil</span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-red"><i class="fa fa-times me-1"></i> {{ \App\Models\ActivityLog::statusLabel($item->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('superadmin.audit.show', $item->id) }}" wire:navigate class="btn btn-oripio-light btn-pill" title="Lihat Detail Aktivitas" style="font-size: 0.78rem; padding: 6px 14px;">
                                        <i class="fa fa-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    @if ($hasActiveFilters)
                                        <i class="fa fa-filter-circle-xmark fa-3x mb-2 text-muted opacity-50"></i>
                                        <p class="mb-1">Tidak ada aktivitas yang sesuai filter.</p>
                                        <a href="{{ route('superadmin.audit.index', ['tab' => $activeTab]) }}" wire:navigate class="btn btn-oripio-light btn-pill mt-2" style="font-size: 0.8rem;">
                                            <i class="fa fa-rotate-left me-1"></i> Reset Filter
                                        </a>
                                    @else
                                        <i class="fa fa-history fa-3x mb-2 text-muted opacity-50"></i>
                                        <p class="mb-0">Belum ada aktivitas yang tercatat.</p>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{ $activities->links('pagination.superadmin') }}
            </div>
            </div><!-- /data-live-region: audit-results -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function () {
        if (window.__auditRealtimeTimer) {
            window.clearInterval(window.__auditRealtimeTimer);
        }

        var pollToken = 0;
        var pollInFlight = false;

        window.__auditRealtimeTimer = window.setInterval(function () {
            if (document.hidden) return;
            if (document.documentElement.classList.contains('live-filter-loading')) return;

            var form = document.querySelector(
                'form[action="{{ route("superadmin.audit.index") }}"][data-livewire-navigate]'
            );
            if (!form || !window.location.pathname.startsWith('/superadmin/audit')) return;

            // Jangan menimpa hasil live search saat pengguna masih mengetik.
            var active = document.activeElement;
            if (active && form.contains(active) && /^(INPUT|SELECT|TEXTAREA)$/.test(active.tagName)) {
                return;
            }

            if (pollInFlight) return;

            var token = ++pollToken;
            var url = new URL(form.action, window.location.origin);
            var data = new FormData(form);

            data.forEach(function (value, key) {
                if (value !== '') url.searchParams.set(key, value);
                else url.searchParams.delete(key);
            });

            var currentPage = new URL(window.location.href).searchParams.get('page');
            if (currentPage) url.searchParams.set('page', currentPage);

            pollInFlight = true;

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(function (response) {
                if (response.status === 419 || response.status === 401) {
                    window.location.href = '{{ route("login") }}';
                    return null;
                }
                if (!response.ok) throw new Error('HTTP ' + response.status);
                return response.text();
            }).then(function (html) {
                if (!html || token !== pollToken) return;

                var doc = new DOMParser().parseFromString(html, 'text/html');

                document.querySelectorAll('[data-live-region]').forEach(function (el) {
                    var fresh = doc.querySelector(
                        '[data-live-region="' + el.getAttribute('data-live-region') + '"]'
                    );
                    if (fresh) el.innerHTML = fresh.innerHTML;
                });
            }).catch(function () {
                // Realtime polling mencoba lagi pada interval berikutnya.
            }).finally(function () {
                if (token === pollToken) pollInFlight = false;
            });
        }, 3000);
    })();
</script>
@endpush
