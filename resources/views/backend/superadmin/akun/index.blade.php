@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Pengelolaan Akun Pengguna',
        'subtitle' => 'Kelola hak akses Superadmin, Admin PPA, Admin Dinsos, dan masyarakat terdaftar.',
        'icon' => 'fa-users',
        'actionUrl' => route('superadmin.akun.create'),
        'actionLabel' => 'Tambah Akun Baru',
        'actionIcon' => 'fa-user-plus',
    ])

    @include('backend.superadmin.components.alerts')

    <div class="card p-2">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" data-live-region="akun-meta">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Daftar Akun</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Menampilkan {{ $akunList->count() }} dari {{ $akunList->total() }} akun terdaftar.
                    </p>
                </div>
                <span class="badge badge-soft-green badge-pill-soft mt-2 mt-sm-0" style="font-size: 0.76rem;">
                    <i class="fa fa-shield me-1"></i> {{ $akunList->total() }} Total Akun
                </span>
            </div>

            {{-- FILTER BAR --}}
            <form method="GET" action="{{ route('superadmin.akun.index') }}" data-livewire-navigate class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-md-4 col-sm-12">
                        <div class="search-pill-wrapper w-100" style="height: 44px;">
                            <i class="fa fa-search text-muted"></i>
                            <input type="text" name="q" placeholder="Cari nama atau email..." value="{{ request('q') }}">
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <select name="role" class="form-select rounded-pill px-3" style="height: 44px; border: 1px solid #E2E8F0; font-size: 0.88rem;">
                            <option value=""> Semua Role </option>
                            <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                            <option value="admin_ppa" {{ request('role') === 'admin_ppa' ? 'selected' : '' }}>Admin PPA</option>
                            <option value="admin_dinsos" {{ request('role') === 'admin_dinsos' ? 'selected' : '' }}>Admin Dinsos</option>
                            <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User Pelapor</option>
                            <option value="Pelapor Cepat" {{ request('role') === 'Pelapor Cepat' ? 'selected' : '' }}>Guest</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <select name="status" class="form-select rounded-pill px-3" style="height: 44px; border: 1px solid #E2E8F0; font-size: 0.88rem;">
                            <option value=""> Semua Status </option>
                            <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-sm-12 d-flex">
                        <button type="submit" class="btn btn-oripio-dark btn-pill me-2 flex-grow-1" style="height: 44px;">
                            <i class="fa fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('superadmin.akun.index') }}" wire:navigate class="btn btn-oripio-light btn-pill" style="height: 44px;">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- Area hasil filter: ditukar parsial oleh live filter (tanpa reload) --}}
            <div data-live-region="akun-results">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Role & Wewenang</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($akunList as $index => $akun)
                            <tr>
                                <td class="text-muted fw-bold">{{ $akunList->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width: 36px; height: 36px; background: {{ $akun->role === 'superadmin' ? '#006039' : ($akun->role === 'admin_ppa' ? '#0284C7' : ($akun->role === 'admin_dinsos' ? '#D97706' : '#64748B')) }}; font-size: 0.85rem;">
                                            {{ strtoupper(substr($akun->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">{{ $akun->nama }}</h6>
                                            @if (Auth::id() == $akun->id_akun)
                                                <span class="badge bg-light text-primary border rounded-pill" style="font-size: 0.68rem;">Sesi Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted" style="font-size: 0.86rem;">{{ $akun->email ?? '-' }}</td>
                                <td class="text-muted" style="font-size: 0.86rem;">{{ $akun->notelp ?? '-' }}</td>
                                <td>
                                    @if ($akun->role === 'superadmin')
                                        <span class="badge-pill-soft badge-soft-green">Superadmin</span>
                                    @elseif ($akun->role === 'admin_ppa' || $akun->role === 'admin')
                                        <span class="badge-pill-soft badge-soft-blue">Admin PPA</span>
                                    @elseif ($akun->role === 'admin_dinsos')
                                        <span class="badge-pill-soft badge-soft-amber">Admin Dinsos</span>
                                    @elseif ($akun->role === 'user')
                                        <span class="badge-pill-soft badge-soft-gray">User Pelapor</span>
                                    @else
                                        <span class="badge bg-light text-dark border rounded-pill px-2 py-1">{{ $akun->role }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($akun->is_active ?? true)
                                        <span class="badge-pill-soft badge-soft-green" style="font-size: 0.74rem;">
                                            <i class="fa fa-circle me-1" style="font-size: 6px;"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-rose" style="font-size: 0.74rem;">
                                            <i class="fa fa-circle me-1" style="font-size: 6px;"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center">
                                        <a href="{{ route('superadmin.akun.edit', $akun->id_akun) }}" wire:navigate class="btn-circle-action me-1" title="Edit Akun" style="width: 34px; height: 34px;">
                                            <i class="fa fa-pencil" style="font-size: 0.82rem;"></i>
                                        </a>

                                        {{-- Reset Password Trigger --}}
                                        <button type="button" class="btn-circle-action me-1" data-toggle="modal" data-target="#modalResetPw{{ $akun->id_akun }}" title="Reset Password" style="width: 34px; height: 34px;">
                                            <i class="fa fa-key" style="font-size: 0.82rem;"></i>
                                        </button>

                                        @if (Auth::id() != $akun->id_akun)
                                            {{-- Toggle Status --}}
                                            <form action="{{ route('superadmin.akun.toggle-status', $akun->id_akun) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn-circle-action me-1" title="{{ $akun->is_active ? 'Nonaktifkan' : 'Aktifkan' }}" style="width: 34px; height: 34px;">
                                                    <i class="fa fa-{{ $akun->is_active ? 'ban text-warning' : 'check text-success' }}" style="font-size: 0.82rem;"></i>
                                                </button>
                                            </form>

                                            {{-- Delete Akun --}}
                                            <form action="{{ route('superadmin.akun.destroy', $akun->id_akun) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $akun->nama }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-circle-action text-danger" title="Hapus Akun" style="width: 34px; height: 34px;">
                                                    <i class="fa fa-trash" style="font-size: 0.82rem;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- MODAL RESET PASSWORD --}}
                                    <div class="modal fade" id="modalResetPw{{ $akun->id_akun }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                                <form action="{{ route('superadmin.akun.reset-password', $akun->id_akun) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                                                        <h5 class="fw-bold text-dark mb-0">Reset Kata Sandi</h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                    </div>
                                                    <div class="modal-body px-4 py-3 text-start">
                                                        <p class="text-muted" style="font-size: 0.88rem;">
                                                            Masukkan kata sandi baru untuk <strong>{{ $akun->nama }}</strong> (<code>{{ $akun->email }}</code>).
                                                        </p>
                                                        <div class="form-group mb-0">
                                                            <label class="fw-bold text-dark" style="font-size: 0.85rem;">Password Baru (Min. 6 Karakter)</label>
                                                            <input type="password" name="new_password" class="form-control rounded-pill px-3" required minlength="6" placeholder="Ketik password baru">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 px-4 pb-4">
                                                        <button type="button" class="btn btn-oripio-light btn-pill" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-oripio-primary btn-pill">Simpan Password</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fa fa-users fa-3x mb-2 text-muted opacity-50"></i>
                                    <p class="mb-0">Tidak ada akun yang sesuai kriteria pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{ $akunList->links('pagination.superadmin') }}
            </div>
            </div><!-- /data-live-region: akun-results -->
        </div>
    </div>
</div>
@endsection
