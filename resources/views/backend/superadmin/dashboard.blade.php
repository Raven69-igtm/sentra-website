@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-uppercase fw-bold"><i class="fa fa-shield me-2 text-primary"></i> Dashboard Superadmin</h4>
                <p class="mb-0 text-muted">Panel kendali akun, hak akses peran (RBAC), dan parameter sistem Sentra</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Superadmin</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('superadmin.dashboard') }}">Dashboard</a></li>
            </ol>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-custom-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- STAT WIDGETS -->
    <div class="row">
        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fs-14 mb-1 text-white opacity-75">Total Akun</p>
                            <h3 class="fs-28 font-w700 text-white mb-0">{{ $totalAkun }}</h3>
                        </div>
                        <span class="p-3 bg-white text-primary rounded-circle">
                            <i class="fa fa-users fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fs-14 mb-1 text-white opacity-75">Admin & Petugas</p>
                            <h3 class="fs-28 font-w700 text-white mb-0">{{ $totalAdmin }}</h3>
                        </div>
                        <span class="p-3 bg-white text-success rounded-circle">
                            <i class="fa fa-user-secret fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fs-14 mb-1 text-white opacity-75">Masyarakat / User</p>
                            <h3 class="fs-28 font-w700 text-white mb-0">{{ $totalUser }}</h3>
                        </div>
                        <span class="p-3 bg-white text-info rounded-circle">
                            <i class="fa fa-user fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-xxl-3 col-lg-6 col-sm-6">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="fs-14 mb-1 text-white opacity-75">Role & Permission</p>
                            <h3 class="fs-28 font-w700 text-white mb-0">{{ $totalRoles }} / {{ $totalPermissions }}</h3>
                        </div>
                        <span class="p-3 bg-white text-warning rounded-circle">
                            <i class="fa fa-key fa-2x"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN SECTION: RINGKASAN AKUN TERBARU & STATUS KONFIGURASI -->
    <div class="row">
        <!-- Akun Pendaftar Terakhir -->
        <div class="col-xl-8 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="fa fa-user-plus text-primary me-2"></i> Pendaftaran Akun Terkini</h5>
                    <a href="{{ route('superadmin.akun.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua Akun</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-responsive-sm">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Tanggal Daftar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($latestAccounts as $acc)
                                    <tr>
                                        <td><strong>{{ $acc->nama }}</strong></td>
                                        <td>{{ $acc->email ?? '-' }}</td>
                                        <td>
                                            @if ($acc->role === 'superadmin')
                                                <span class="badge badge-danger">Superadmin</span>
                                            @elseif ($acc->role === 'admin_ppa' || $acc->role === 'admin')
                                                <span class="badge badge-primary">Admin PPA</span>
                                            @elseif ($acc->role === 'admin_dinsos')
                                                <span class="badge badge-warning text-white">Admin Dinsos</span>
                                            @elseif ($acc->role === 'user')
                                                <span class="badge badge-info">User</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $acc->role }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($acc->is_active ?? true)
                                                <span class="badge badge-outline-success"><i class="fa fa-check-circle"></i> Aktif</span>
                                            @else
                                                <span class="badge badge-outline-danger"><i class="fa fa-times-circle"></i> Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>{{ $acc->created_at ? $acc->created_at->format('d M Y H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data akun</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Konfigurasi & Quick Links -->
        <div class="col-xl-4 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fa fa-cogs text-warning me-2"></i> Ringkasan Sistem</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Aplikasi</span>
                            <span class="fw-bold">{{ $appName }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Hotline</span>
                            <span class="fw-bold text-success">{{ $hotline }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">SLA Laporan</span>
                            <span class="badge badge-info">{{ $reportSla }} Hari</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Session Timeout</span>
                            <span>{{ $sessionLifetime }} Menit</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span class="text-muted">Akun Nonaktif</span>
                            <span class="badge badge-{{ $totalNonAktif > 0 ? 'danger' : 'light' }}">{{ $totalNonAktif }} Akun</span>
                        </li>
                    </ul>

                    <div class="d-grid gap-2">
                        <a href="{{ route('superadmin.akun.create') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fa fa-user-plus me-1"></i> Tambah Pengguna Baru
                        </a>
                        <a href="{{ route('superadmin.roles.index') }}" class="btn btn-outline-info btn-block mb-2">
                            <i class="fa fa-key me-1"></i> Kelola Role & Hak Akses
                        </a>
                        <a href="{{ route('superadmin.laporan.index') }}" class="btn btn-outline-success btn-block mb-2">
                            <i class="fa fa-file-text-o me-1"></i> Audit Laporan Global
                        </a>
                        <a href="{{ route('superadmin.settings.index') }}" class="btn btn-outline-secondary btn-block">
                            <i class="fa fa-sliders me-1"></i> Konfigurasi Sistem
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
