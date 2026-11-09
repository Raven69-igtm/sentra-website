@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Detail Audit Laporan',
        'subtitle' => 'Laporan #' . $laporan->id_laporan . ' — informasi terperinci kasus dan entitas terkait.',
        'icon' => 'fa-search',
        'actionUrl' => route('superadmin.laporan.index'),
        'actionLabel' => 'Kembali ke Daftar',
        'actionIcon' => 'fa-arrow-left',
        'actionClass' => 'btn-oripio-light',
    ])

    {{-- STATUS BAR --}}
    <div class="card p-2 mb-4">
        <div class="card-body p-3 d-flex flex-wrap justify-content-between align-items-center">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div>
                    <span class="text-muted d-block mb-1" style="font-size: 0.76rem; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase;">Status Saat Ini</span>
                    @if ($laporan->status === 'selesai')
                        <span class="badge-pill-soft badge-soft-green badge-lg"><i class="fa fa-check me-1"></i> Selesai</span>
                    @elseif ($laporan->status === 'dirujuk')
                        <span class="badge-pill-soft badge-soft-amber badge-lg"><i class="fa fa-share me-1"></i> Dirujuk</span>
                    @elseif ($laporan->status === 'diproses')
                        <span class="badge-pill-soft badge-soft-blue badge-lg"><i class="fa fa-refresh me-1"></i> Diproses</span>
                    @else
                        <span class="badge-pill-soft badge-soft-gray badge-lg"><i class="fa fa-clock me-1"></i> {{ ucfirst($laporan->status) }}</span>
                    @endif
                </div>
                <div>
                    <span class="text-muted d-block mb-1" style="font-size: 0.76rem; font-weight: 600; letter-spacing: 0.04em; text-transform: uppercase;">Kategori Kasus</span>
                    <span class="badge-pill-soft badge-soft-blue badge-lg">{{ $laporan->kategori }}</span>
                </div>
            </div>
            <div class="text-muted mt-3 mt-sm-0" style="font-size: 0.82rem;">
                <i class="fa fa-calendar me-1"></i>
                {{ $laporan->created_at ? $laporan->created_at->format('d M Y H:i') : '-' }}
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- DETAIL PELAPOR -->
        <div class="col-md-6">
            <div class="card h-100 p-2">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="page-head-icon" style="width: 38px; height: 38px; flex-basis: 38px; font-size: 0.9rem; margin-right: 10px;">
                            <i class="fa fa-user"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Data Pelapor</h6>
                    </div>

                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Nama</span>
                            <span class="info-value">{{ $laporan->detail_pelapor->nama ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">NIK</span>
                            <span class="info-value">{{ $laporan->detail_pelapor->nik ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">No. Telepon</span>
                            <span class="info-value">{{ $laporan->detail_pelapor->no_telp ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Hubungan</span>
                            <span class="info-value">{{ $laporan->detail_pelapor->hubungan_dengan_korban ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Alamat</span>
                            <span class="info-value">{{ $laporan->detail_pelapor->alamat ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAIL TERLAPOR -->
        <div class="col-md-6">
            <div class="card h-100 p-2">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="page-head-icon" style="width: 38px; height: 38px; flex-basis: 38px; font-size: 0.9rem; margin-right: 10px; background: #FEE2E2; color: #DC2626;">
                            <i class="fa fa-user-times"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Data Terlapor / Terduga</h6>
                    </div>

                    <div class="info-list">
                        <div class="info-row">
                            <span class="info-label">Nama</span>
                            <span class="info-value">{{ $laporan->detail_terlapor->nama ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">NIK</span>
                            <span class="info-value">{{ $laporan->detail_terlapor->nik ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Jenis Kelamin</span>
                            <span class="info-value">{{ $laporan->detail_terlapor->jenis_kelamin ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Umur</span>
                            <span class="info-value">{{ $laporan->detail_terlapor->umur ?? '-' }} Tahun</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Alamat</span>
                            <span class="info-value">{{ $laporan->detail_terlapor->alamat ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DETAIL KASUS & KRONOLOGI -->
    <div class="row g-4 mt-0">
        <div class="col-12">
            <div class="card p-2">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-3">
                        <div class="page-head-icon" style="width: 38px; height: 38px; flex-basis: 38px; font-size: 0.9rem; margin-right: 10px; background: #E0F2FE; color: #0284C7;">
                            <i class="fa fa-info-circle"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Deskripsi & Kronologi Kasus</h6>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <div class="p-3 rounded-4" style="background: #FAFBFD; border: 1px solid var(--oripio-border-subtle);">
                                <span class="text-muted small d-block mb-1">Tanggal Kejadian</span>
                                <strong class="text-dark" style="font-size: 0.88rem;">{{ $laporan->detail_kasus->tanggal ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="p-3 rounded-4" style="background: #FAFBFD; border: 1px solid var(--oripio-border-subtle);">
                                <span class="text-muted small d-block mb-1">Tempat Kejadian</span>
                                <strong class="text-dark" style="font-size: 0.88rem;">{{ $laporan->detail_kasus->tempat_kejadian ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="p-3" style="background: #FAFBFD; border: 1px solid var(--oripio-border-subtle); border-radius: 14px; line-height: 1.7; font-size: 0.88rem; color: #334155;">
                        {{ $laporan->detail_kasus->kronologi ?? 'Tidak ada data kronologi yang dicatat.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-list {
        display: flex;
        flex-direction: column;
    }

    .info-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 10px 0;
        border-bottom: 1px solid var(--oripio-border-subtle);
        font-size: 0.86rem;
    }

    .info-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        flex: 0 0 38%;
        color: var(--oripio-text-muted);
    }

    .info-value {
        flex: 1;
        text-align: right;
        color: var(--oripio-text-main);
        font-weight: 600;
        word-break: break-word;
    }
</style>
@endsection
