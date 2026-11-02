@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-uppercase fw-bold"><i class="fa fa-users me-2 text-primary"></i> Pengelolaan Akun</h4>
                <p class="mb-0 text-muted">Daftar pengguna terdaftar, pengaturan peran, status aktif, dan reset kata sandi</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Superadmin</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('superadmin.akun.index') }}">Akun</a></li>
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
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
            <h5 class="card-title mb-2 mb-sm-0">Daftar Akun Pengguna</h5>
            <a href="{{ route('superadmin.akun.create') }}" class="btn btn-primary btn-sm">
                <i class="fa fa-plus me-1"></i> Tambah Akun Baru
            </a>
        </div>
        <div class="card-body">
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('superadmin.akun.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4 col-sm-12">
                        <input type="text" name="q" class="form-control" placeholder="Cari nama atau email..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <select name="role" class="form-control">
                            <option value="">-- Semua Role --</option>
                            <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            <option value="admin_ppa" {{ request('role') === 'admin_ppa' ? 'selected' : '' }}>Admin PPA</option>
                            <option value="admin_dinsos" {{ request('role') === 'admin_dinsos' ? 'selected' : '' }}>Admin Dinsos</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="guest" {{ request('role') === 'guest' ? 'selected' : '' }}>Guest</option>
                            @foreach ($roles as $r)
                                @if (!in_array($r->name, ['superadmin', 'admin', 'admin_ppa', 'admin_dinsos', 'user', 'guest']))
                                    <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ $r->display_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <select name="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-12 d-flex">
                        <button type="submit" class="btn btn-info me-2 w-100">
                            <i class="fa fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('superadmin.akun.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($akunList as $index => $akun)
                            <tr>
                                <td>{{ $akunList->firstItem() + $index }}</td>
                                <td>
                                    <strong>{{ $akun->nama }}</strong>
                                    @if (Auth::id() == $akun->id_akun)
                                        <span class="badge badge-xs badge-info ms-1">Anda</span>
                                    @endif
                                </td>
                                <td>{{ $akun->email ?? '-' }}</td>
                                <td>{{ $akun->notelp ?? '-' }}</td>
                                <td>
                                    @if ($akun->role === 'superadmin')
                                        <span class="badge badge-danger">Superadmin</span>
                                    @elseif ($akun->role === 'admin_ppa' || $akun->role === 'admin')
                                        <span class="badge badge-primary">Admin PPA</span>
                                    @elseif ($akun->role === 'admin_dinsos')
                                        <span class="badge badge-warning text-white">Admin Dinsos</span>
                                    @elseif ($akun->role === 'user')
                                        <span class="badge badge-info">User</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $akun->role }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($akun->is_active ?? true)
                                        <span class="badge badge-success"><i class="fa fa-check-circle"></i> Aktif</span>
                                    @else
                                        <span class="badge badge-danger"><i class="fa fa-times-circle"></i> Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('superadmin.akun.edit', $akun->id_akun) }}" class="btn btn-sm btn-primary" title="Edit Akun">
                                            <i class="fa fa-pencil"></i>
                                        </a>

                                        <!-- Tombol Modal Reset Password -->
                                        <button type="button" class="btn btn-sm btn-warning text-white" data-toggle="modal" data-target="#modalResetPw{{ $akun->id_akun }}" title="Reset Password">
                                            <i class="fa fa-key"></i>
                                        </button>

                                        @if (Auth::id() != $akun->id_akun)
                                            <!-- Toggle Status -->
                                            <form action="{{ route('superadmin.akun.toggle-status', $akun->id_akun) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-{{ $akun->is_active ? 'secondary' : 'success' }}" title="{{ $akun->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                    <i class="fa fa-{{ $akun->is_active ? 'ban' : 'check' }}"></i>
                                                </button>
                                            </form>

                                            <!-- Hapus Akun -->
                                            <form action="{{ route('superadmin.akun.destroy', $akun->id_akun) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $akun->nama }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus Akun">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    <!-- Modal Reset Password -->
                                    <div class="modal fade" id="modalResetPw{{ $akun->id_akun }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('superadmin.akun.reset-password', $akun->id_akun) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reset Password - {{ $akun->nama }}</h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-muted">Masukkan password baru untuk akun <strong>{{ $akun->email }}</strong>.</p>
                                                        <div class="form-group">
                                                            <label class="fw-bold">Password Baru (min. 6 karakter)</label>
                                                            <input type="password" name="new_password" class="form-control" required minlength="6" placeholder="Masukkan password baru">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-warning text-white">Simpan Password Baru</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Data akun tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $akunList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
