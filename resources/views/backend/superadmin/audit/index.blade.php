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
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Menampilkan {{ $activities->count() }} dari {{ $activities->total() }} aktivitas tercatat.
                    </p>
                </div>
                <span class="badge badge-soft-blue badge-pill-soft mt-2 mt-sm-0" style="font-size: 0.76rem;">
                    <i class="fa fa-history me-1"></i> Total: {{ $activities->total() }} Aktivitas
                </span>
            </div>

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('superadmin.audit.index') }}" data-livewire-navigate class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="search-pill-wrapper w-100" style="height: 44px;">
                            <i class="fa fa-search text-muted"></i>
                            <input type="text" name="q" placeholder="Cari nama admin atau referensi..." value="{{ request('q') }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="instansi" class="form-control">
                            <option value=""> Semua Instansi </option>
                            @foreach ($instansiList as $value => $label)
                                <option value="{{ $value }}" {{ request('instansi') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="jenis" class="form-control">
                            <option value=""> Jenis Aktivitas </option>
                            @foreach ($jenisList as $value => $label)
                                <option value="{{ $value }}" {{ request('jenis') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}" title="Filter tanggal aktivitas">
                    </div>
                    <div class="col-lg-2 col-md-12 d-flex">
                        <button type="submit" class="btn btn-oripio-dark btn-pill me-2 flex-grow-1" style="height: 44px;">
                            <i class="fa fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('superadmin.audit.index') }}" wire:navigate class="btn btn-oripio-light btn-pill" style="height: 44px;">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- Area hasil filter: ditukar parsial oleh live filter (tanpa reload) --}}
            <div data-live-region="audit-results">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
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
                                <td class="text-muted fw-bold">{{ $activities->firstItem() + $index }}</td>
                                <td><span class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $item->nama }}</span></td>
                                <td><span class="badge-pill-soft badge-soft-gray" style="font-size: 0.74rem;">{{ $item->role }}</span></td>
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
                                <td style="font-size: 0.84rem;">{{ \App\Models\ActivityLog::TYPES[$item->activity_type] ?? ucfirst($item->activity_type) }}</td>
                                <td class="text-muted text-nowrap" style="font-size: 0.82rem;">{{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}</td>
                                <td>
                                    @if ($item->status === 'berhasil')
                                        <span class="badge-pill-soft badge-soft-green"><i class="fa fa-check me-1"></i> Berhasil</span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-red"><i class="fa fa-times me-1"></i> {{ ucfirst($item->status) }}</span>
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
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fa fa-history fa-3x mb-2 text-muted opacity-50"></i>
                                    <p class="mb-0">Belum ada aktivitas yang tercatat.</p>
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
