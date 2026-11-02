@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-uppercase fw-bold"><i class="fa fa-search me-2 text-primary"></i> Detail Audit Laporan #{{ $laporan->id_laporan }}</h4>
                <p class="mb-0 text-muted">Informasi terperinci kasus dan entitas terkait untuk audit kepatuhan</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Superadmin</a></li>
                <li class="breadcrumb-item"><a href="{{ route('superadmin.laporan.index') }}">Audit Laporan</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Detail</a></li>
            </ol>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card bg-white border p-3 d-flex flex-row justify-content-between align-items-center">
                <div>
                    <span class="text-muted d-block small">Status Saat Ini:</span>
                    <span class="badge badge-lg badge-{{ $laporan->status === 'selesai' ? 'success' : ($laporan->status === 'dirujuk' ? 'warning' : 'primary') }} text-uppercase">
                        {{ $laporan->status }}
                    </span>
                    <span class="badge badge-outline-secondary ms-2">{{ $laporan->kategori }}</span>
                </div>
                <div>
                    <a href="{{ route('superadmin.laporan.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- DETAIL PELAPOR -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fa fa-user me-2 text-primary"></i> Data Pelapor</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 35%;">Nama</td>
                            <td>: <strong>{{ $laporan->detail_pelapor->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIK</td>
                            <td>: {{ $laporan->detail_pelapor->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">No. Telepon</td>
                            <td>: {{ $laporan->detail_pelapor->no_telp ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Hubungan</td>
                            <td>: {{ $laporan->detail_pelapor->hubungan_dengan_korban ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>: {{ $laporan->detail_pelapor->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- DETAIL TERLAPOR -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fa fa-user-times me-2 text-danger"></i> Data Terlapor / Terduga</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 35%;">Nama</td>
                            <td>: <strong>{{ $laporan->detail_terlapor->nama ?? '-' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">NIK</td>
                            <td>: {{ $laporan->detail_terlapor->nik ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jenis Kelamin</td>
                            <td>: {{ $laporan->detail_terlapor->jenis_kelamin ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Umur</td>
                            <td>: {{ $laporan->detail_terlapor->umur ?? '-' }} Tahun</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Alamat</td>
                            <td>: {{ $laporan->detail_terlapor->alamat ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- DETAIL KASUS & KRONOLOGI -->
    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold"><i class="fa fa-info-circle me-2 text-info"></i> Deskripsi & Kronologi Kasus</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <span class="text-muted small d-block">Tanggal Kejadian:</span>
                            <strong>{{ $laporan->detail_kasus->tanggal ?? '-' }}</strong>
                        </div>
                        <div class="col-md-8">
                            <span class="text-muted small d-block">Tempat Kejadian:</span>
                            <strong>{{ $laporan->detail_kasus->tempat_kejadian ?? '-' }}</strong>
                        </div>
                    </div>
                    <hr>
                    <label class="fw-bold small text-muted text-uppercase">Kronologi Lengkap:</label>
                    <div class="p-3 bg-light rounded border" style="line-height: 1.7;">
                        {{ $laporan->detail_kasus->kronologi ?? 'Tidak ada data kronologi yang dicatat.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
