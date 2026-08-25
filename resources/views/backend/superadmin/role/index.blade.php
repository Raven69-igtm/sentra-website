@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Peran & Hak Akses',
        'subtitle' => 'Pengaturan peran pengguna dan alokasi matriks hak akses per modul fitur.',
        'icon' => 'fa-key',
        'actionUrl' => route('superadmin.roles.create'),
        'actionLabel' => 'Tambah Role Baru',
        'actionIcon' => 'fa-plus',
    ])

    @include('backend.superadmin.components.alerts')

    <div class="card p-2">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Daftar Peran Pengguna</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Total {{ $roles->count() }} peran terdaftar pada sistem.
                    </p>
                </div>
                <span class="badge badge-soft-green badge-pill-soft mt-2 mt-sm-0" style="font-size: 0.76rem;">
                    <i class="fa fa-shield me-1"></i> {{ $roles->count() }} Role
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Slug</th>
                            <th>Nama Tampilan</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Total Pengguna</th>
                            <th class="text-center">Hak Akses</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($roles as $index => $role)
                            <tr>
                                <td class="text-muted fw-bold">{{ $index + 1 }}</td>
                                <td><code class="text-muted" style="font-size: 0.8rem;">{{ $role->name }}</code></td>
                                <td><span class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $role->display_name }}</span></td>
                                <td class="text-muted" style="font-size: 0.84rem;">{{ $role->description ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-soft-blue badge-pill-soft">{{ $role->users_count }} Pengguna</span>
                                </td>
                                <td class="text-center">
                                    @if ($role->name === 'superadmin')
                                        <span class="badge-pill-soft badge-soft-green">Semua Akses</span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-blue">{{ $role->permissions_count }} Izin</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($role->is_system)
                                        <span class="badge-pill-soft badge-soft-gray" title="Role bawaan sistem tidak dapat dihapus">Sistem</span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-amber">Kustom</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center">
                                        <a href="{{ route('superadmin.roles.edit', $role->id) }}" wire:navigate class="btn btn-oripio-light btn-pill" title="Edit Hak Akses" style="font-size: 0.78rem; padding: 6px 14px;">
                                            <i class="fa fa-sliders me-1"></i> Atur Akses
                                        </a>

                                        @if (!$role->is_system)
                                            <form action="{{ route('superadmin.roles.destroy', $role->id) }}" method="POST" class="d-inline ms-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role {{ $role->display_name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-circle-action text-danger" title="Hapus Role" style="width: 34px; height: 34px;">
                                                    <i class="fa fa-trash" style="font-size: 0.8rem;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fa fa-key fa-3x mb-2 text-muted opacity-50"></i>
                                    <p class="mb-0">Belum ada role terdaftar.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
