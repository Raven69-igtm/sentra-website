@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Detail Aktivitas #' . $activity->id,
        'subtitle' => 'Metadata aktivitas administratif — tanpa isi laporan maupun identitas pelapor.',
        'icon' => 'fa-search',
        'actionUrl' => route('superadmin.audit.index'),
        'actionLabel' => 'Kembali ke Daftar',
        'actionIcon' => 'fa-arrow-left',
        'actionClass' => 'btn-oripio-light',
    ])

    @include('backend.superadmin.components.alerts')

    {{-- NOTIS BATASAN AUDIT --}}
    <div class="card p-2 mb-4" style="border-left: 4px solid #D97706;">
        <div class="card-body p-3 d-flex align-items-start">
            <i class="fa fa-shield me-3 mt-1" style="color: #D97706; font-size: 1.1rem;"></i>
            <div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.9rem;">Halaman Metadata Aktivitas</h6>
                <p class="text-muted mb-0" style="font-size: 0.82rem;">
                    Informasi yang ditampilkan terbatas pada metadata aktivitas administratif. Isi laporan,
                    identitas pelapor/korban, dokumen, dan lampiran tidak ditampilkan pada halaman audit.
                </p>
            </div>
        </div>
    </div>

    {{-- STATUS BAR --}}
    <div class="card p-2 mb-4">
        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div>
                    <span class="text-muted d-block mb-1" style="font-size: 0.76rem; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase;">Jenis Aktivitas</span>
                    <span class="badge-pill-soft badge-soft-blue badge-lg">{{ \App\Models\ActivityLog::TYPES[$activity->activity_type] ?? ucfirst($activity->activity_type) }}</span>
                </div>
                <div>
                    <span class="text-muted d-block mb-1" style="font-size: 0.76rem; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase;">Status Aktivitas</span>
                    @if ($activity->status === 'berhasil')
                        <span class="badge-pill-soft badge-soft-green badge-lg"><i class="fa fa-check me-1"></i> Berhasil</span>
                    @else
                        <span class="badge-pill-soft badge-soft-red badge-lg"><i class="fa fa-times me-1"></i> {{ ucfirst($activity->status) }}</span>
                    @endif
                </div>
            </div>
            <div class="text-muted mt-3 mt-sm-0" style="font-size: 0.82rem;">
                <i class="fa fa-calendar me-1"></i>
                {{ $activity->created_at ? $activity->created_at->format('d M Y H:i') : '-' }}
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- PELAKU AKTIVITAS --}}
        <div class="col-md-6">
            <div class="card h-100 p-2">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="page-head-icon" style="width: 38px; height: 38px; flex-basis: 38px; font-size: 0.9rem; margin-right: 10px;">
                            <i class="fa fa-user"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Pelaku Aktivitas</h6>
                    </div>

                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Nama Administrator</span>
                            <span class="info-value">{{ $activity->nama }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Role</span>
                            <span class="info-value">{{ $activity->role }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Instansi</span>
                            <span class="info-value">{{ $activity->instansi }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">ID Akun</span>
                            <span class="info-value">{{ $activity->akun_id ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- DETAIL AKTIVITAS --}}
        <div class="col-md-6">
            <div class="card h-100 p-2">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="page-head-icon" style="width: 38px; height: 38px; flex-basis: 38px; font-size: 0.9rem; margin-right: 10px; background: #FEF3C7; color: #D97706;">
                            <i class="fa fa-clipboard-list"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Deskripsi Aktivitas</h6>
                    </div>

                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Modul</span>
                            <span class="info-value">{{ $activity->module ? ucfirst($activity->module) : '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Referensi</span>
                            <span class="info-value"><code class="text-muted" style="font-size: 0.8rem;">{{ $activity->reference ?? '-' }}</code></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Waktu Aktivitas</span>
                            <span class="info-value">{{ $activity->created_at ? $activity->created_at->translatedFormat('d F Y, H:i:s') : '-' }}</span>
                        </div>
                    </div>

                    <div class="p-3 bg-light mt-2" style="border-radius: 14px;">
                        <span class="text-muted d-block mb-1" style="font-size: 0.76rem; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase;">Keterangan</span>
                        <p class="text-dark mb-0" style="font-size: 0.86rem;">{{ $activity->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- METADATA TEKNIS --}}
        <div class="col-12">
            <div class="card p-2">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="page-head-icon" style="width: 38px; height: 38px; flex-basis: 38px; font-size: 0.9rem; margin-right: 10px; background: #E0F2FE; color: #0284C7;">
                            <i class="fa fa-globe"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Metadata Teknis</h6>
                    </div>

                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Alamat IP</span>
                            <span class="info-value">{{ $activity->ip_address ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">User Agent</span>
                            <span class="info-value" style="font-size: 0.78rem; word-break: break-all;">{{ $activity->user_agent ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Dicatat Pada</span>
                            <span class="info-value">{{ $activity->created_at ? $activity->created_at->translatedFormat('d F Y, H:i:s') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
