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

    <div class="card border-0 shadow-sm p-2" style="border-radius: 18px; border: 1px solid #F1F5F9 !important;">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3" data-live-region="akun-meta">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem; color: #0F172A;">Daftar Akun</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Menampilkan {{ $akunList->count() }} dari {{ $akunList->total() }} akun terdaftar.
                    </p>
                </div>
                <span class="jigate-tag jigate-tag-green" style="font-size: 0.76rem;">
                    <i class="fa fa-shield me-1"></i> {{ $akunList->total() }} Total Akun
                </span>
            </div>

            {{-- FILTER BAR --}}
            <form method="GET" action="{{ route('superadmin.akun.index') }}" data-livewire-navigate data-live-debounce="300" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-xl-4 col-lg-6 col-md-12">
                        <label class="small fw-bold text-muted mb-1" for="akunQ">Pencarian</label>
                        <div class="search-pill-wrapper w-100" style="height: 44px;">
                            <i class="fa fa-search text-muted"></i>
                            <input id="akunQ" type="search" name="q" autocomplete="off"
                                placeholder="Cari nama, email, telepon, alamat, role, status, tanggal daftar, atau ID akun..."
                                value="{{ $filters['q'] ?? '' }}">
                        </div>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="akunRole">Role</label>
                        <select id="akunRole" name="role" class="form-control">
                            <option value="">Semua Role</option>
                            @foreach ($roleList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['role'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="akunStatus">Status</label>
                        <select id="akunStatus" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach ($statusList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['status'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="akunGender">Jenis Kelamin</label>
                        <select id="akunGender" name="jenis_kelamin" class="form-control">
                            <option value="">Semua</option>
                            @foreach ($genderList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['jenis_kelamin'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="akunInstansi">Instansi</label>
                        <select id="akunInstansi" name="instansi" class="form-control">
                            <option value="">Semua Instansi</option>
                            @foreach ($instansiList as $value => $label)
                                <option value="{{ $value }}" {{ ($filters['instansi'] ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 align-items-end mt-2">
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="akunDari">Dari Tanggal</label>
                        <input id="akunDari" type="date" name="dari" class="form-control" value="{{ $filters['dari'] ?? '' }}">
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="akunSampai">Sampai Tanggal</label>
                        <input id="akunSampai" type="date" name="sampai" class="form-control" value="{{ $filters['sampai'] ?? '' }}">
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="akunSort">Urutkan</label>
                        <select id="akunSort" name="sort" class="form-control">
                            <option value="terbaru" {{ ($filters['sort'] ?? 'terbaru') === 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                            <option value="terlama" {{ ($filters['sort'] ?? '') === 'terlama' ? 'selected' : '' }}>Terlama</option>
                            <option value="nama_az" {{ ($filters['sort'] ?? '') === 'nama_az' ? 'selected' : '' }}>Nama A-Z</option>
                            <option value="nama_za" {{ ($filters['sort'] ?? '') === 'nama_za' ? 'selected' : '' }}>Nama Z-A</option>
                        </select>
                    </div>
                    <div class="col-xl-2 col-lg-3 col-md-6">
                        <label class="small fw-bold text-muted mb-1" for="akunPerPage">Per Halaman</label>
                        <select id="akunPerPage" name="per_page" class="form-control">
                            @foreach ([15, 25, 50, 100] as $size)
                                <option value="{{ $size }}" {{ (int) ($filters['per_page'] ?? 15) === $size ? 'selected' : '' }}>{{ $size }} akun</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-12 d-flex">
                        <a href="{{ route('superadmin.akun.index') }}" wire:navigate class="btn btn-oripio-light btn-pill" style="height: 44px;">
                            <i class="fa fa-rotate-left me-1"></i> Reset
                        </a>
                    </div>
                </div>

                <div class="text-muted small mt-2">
                    <i class="fa fa-info-circle me-1"></i> Pencarian mencakup ID, nama, email, nomor telepon, alamat, role, status, jenis kelamin, instansi, dan tanggal daftar. Potongan nama bulan juga dikenali, misalnya <strong>no</strong> untuk November.
                </div>
            </form>

            @php
                $activeFilterLabels = [
                    'q' => 'Kata kunci',
                    'role' => 'Role',
                    'status' => 'Status',
                    'jenis_kelamin' => 'Jenis Kelamin',
                    'instansi' => 'Instansi',
                    'dari' => 'Dari',
                    'sampai' => 'Sampai',
                ];
                $activeFilters = collect($filters)
                    ->except(['sort', 'per_page'])
                    ->filter(fn ($value) => filled($value));
                $hasActiveFilters = $activeFilters->isNotEmpty();
            @endphp

            @if ($hasActiveFilters)
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="text-muted small fw-bold">Filter aktif:</span>
                    @foreach ($activeFilters as $key => $value)
                        @php
                            $displayValue = match ($key) {
                                'role' => $roleList[$value] ?? $value,
                                'status' => $statusList[$value] ?? $value,
                                'instansi' => $instansiList[$value] ?? $value,
                                default => $value,
                            };
                        @endphp
                        <a href="{{ route('superadmin.akun.index', request()->except($key)) }}"
                            class="badge badge-soft-blue badge-pill-soft text-decoration-none"
                            title="Hapus filter {{ $activeFilterLabels[$key] ?? $key }}">
                            {{ $activeFilterLabels[$key] ?? ucfirst($key) }}: {{ $displayValue }}
                            <i class="fa fa-times ms-1"></i>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Area hasil filter: ditukar parsial oleh live filter (tanpa reload) --}}
            <div data-live-region="akun-results">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-jigate">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Nama Lengkap</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Role & Wewenang</th>
                            <th>Status</th>
                            <th>Tanggal Dibuat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($akunList as $index => $akun)
                            @php
                                $roleClass = match ($akun->role) {
                                    'superadmin' => 'badge-soft-green',
                                    'admin_ppa', 'admin' => 'badge-soft-blue',
                                    'admin_dinsos' => 'badge-soft-amber',
                                    default => 'badge-soft-gray',
                                };
                                $isActive = $akun->is_active ?? true;
                            @endphp
                            <tr>
                                <td class="text-muted fw-bold">{{ $akunList->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-3 d-flex align-items-center justify-content-center rounded-circle text-white fw-bold" style="width: 36px; height: 36px; background: {{ $akun->role === 'superadmin' ? '#006039' : (in_array($akun->role, ['admin_ppa', 'admin'], true) ? '#0284C7' : ($akun->role === 'admin_dinsos' ? '#D97706' : '#64748B')) }}; font-size: 0.85rem;">
                                            {{ strtoupper(substr($akun->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.9rem;">{{ $akun->nama }}</h6>
                                            <span class="text-muted" style="font-size: 0.74rem;">ID #{{ $akun->displayId() }}</span>
                                            @if (Auth::id() == $akun->id_akun)
                                                <span class="badge bg-light text-primary border rounded-pill" style="font-size: 0.68rem;">Sesi Anda</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-muted" style="font-size: 0.86rem;">{{ $akun->email ?? '-' }}</td>
                                <td class="text-muted" style="font-size: 0.86rem;">{{ $akun->notelp ?? '-' }}</td>
                                <td>
                                    <span class="badge-pill-soft {{ $roleClass }}">{{ \App\Models\Akun::roleLabel($akun->role) }}</span>
                                    <div class="text-muted" style="font-size: 0.72rem;">{{ \App\Models\Akun::instansiLabel($akun->role) }}</div>
                                </td>
                                <td>
                                    @if ($isActive)
                                        <span class="badge-pill-soft badge-soft-green" style="font-size: 0.74rem;">
                                            <i class="fa fa-circle me-1" style="font-size: 6px;"></i> Aktif
                                        </span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-rose" style="font-size: 0.74rem;">
                                            <i class="fa fa-circle me-1" style="font-size: 6px;"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted" style="font-size: 0.86rem;">
                                    {{ $akun->created_at ? $akun->created_at->format('d M Y') : '-' }}
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
                                                <button type="submit" class="btn-circle-action me-1" title="{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}" style="width: 34px; height: 34px;">
                                                    <i class="fa fa-{{ $isActive ? 'ban text-warning' : 'check text-success' }}" style="font-size: 0.82rem;"></i>
                                                </button>
                                            </form>

                                            {{-- Delete Akun --}}
                                            <form action="{{ route('superadmin.akun.destroy', $akun->id_akun) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $akun->nama }}? Akun yang memiliki laporan atau percakapan tidak dapat dihapus.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-circle-action text-danger" title="Hapus Akun" style="width: 34px; height: 34px;">
                                                    <i class="fa fa-trash" style="font-size: 0.82rem;"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    @if ($hasActiveFilters)
                                        <i class="fa fa-filter-circle-xmark fa-3x mb-2 text-muted opacity-50"></i>
                                        <p class="mb-1">Tidak ada akun yang sesuai filter.</p>
                                        <a href="{{ route('superadmin.akun.index') }}" wire:navigate class="btn btn-oripio-light btn-pill mt-2" style="font-size: 0.8rem;">
                                            <i class="fa fa-rotate-left me-1"></i> Reset Filter
                                        </a>
                                    @else
                                        <i class="fa fa-users fa-3x mb-2 text-muted opacity-50"></i>
                                        <p class="mb-0">Belum ada akun yang terdaftar.</p>
                                    @endif
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

{{-- Modal reset password diletakkan di luar tabel & main wrapper agar
     backdrop Bootstrap tidak menutupi modal. Wrapper-nya juga menjadi
     live region agar isi modal ikut diperbarui saat filter/pagination
     ditukar parsial tanpa reload. --}}
@section('modals')
    <div data-live-region="akun-modals">
        @foreach ($akunList as $akun)
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
        @endforeach
    </div>
@endsection
