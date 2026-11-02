@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-uppercase fw-bold"><i class="fa fa-key me-2 text-primary"></i> Peran & Hak Akses (Role & Permission)</h4>
                <p class="mb-0 text-muted">Pengaturan peran pengguna dan alokasi matriks hak akses per modul fitur</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Superadmin</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('superadmin.roles.index') }}">Role</a></li>
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
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Peran Pengguna</h5>
            <a href="{{ route('superadmin.roles.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus me-1"></i> Tambah Role Baru
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Slug</th>
                            <th>Nama Tampilan</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Total Pengguna</th>
                            <th class="text-center">Hak Akses</th>
                            <th class="text-center">Tipe</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $index => $role)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td><code>{{ $role->name }}</code></td>
                                <td><strong>{{ $role->display_name }}</strong></td>
                                <td>{{ $role->description ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $role->users_count }} Pengguna</span>
                                </td>
                                <td class="text-center">
                                    @if ($role->name === 'superadmin')
                                        <span class="badge badge-success">Semua Akses (Wildcard)</span>
                                    @else
                                        <span class="badge badge-primary">{{ $role->permissions_count }} Izin</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($role->is_system)
                                        <span class="badge badge-secondary" title="Role bawaan sistem tidak dapat dihapus">Sistem</span>
                                    @else
                                        <span class="badge badge-outline-primary">Kustom</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('superadmin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary" title="Edit Hak Akses">
                                            <i class="fa fa-sliders"></i> Atur Akses
                                        </a>

                                        @if (!$role->is_system)
                                            <form action="{{ route('superadmin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role {{ $role->display_name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger ms-1" title="Hapus Role">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada role terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
