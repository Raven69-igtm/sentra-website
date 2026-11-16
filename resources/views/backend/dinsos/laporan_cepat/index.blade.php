@extends('backend.layout.admin_layout')

@section('title', 'Laporan Cepat PJSRK')

@section('admin')
    {{-- Halaman Admin Dinsos → Laporan Cepat PJSRK.
         Menampung kiriman form ringkas menu "Pelaporan Cepat" mobile
         (rehabilitasi sosial, bencana alam, permasalahan sosial) yang
         masuk lewat POST /api/pjsrk/cepat. --}}
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Laporan Cepat PJSRK',
            'subtitle' => 'Laporan ringkas dari menu Pelaporan Cepat aplikasi mobile',
            'icon' => 'fa-bolt',
        ])

        {{-- FILTER: pencarian teks + layanan + status (server-side) --}}
        <div class="filter-bar">
            <form method="GET" action="{{ route('dinsos.laporan_cepat') }}">
                <div class="filter-grid">
                    <div class="filter-field" style="flex: 2 1 260px;">
                        <label for="filterSearch">Pencarian</label>
                        <input type="text" id="filterSearch" name="search" value="{{ request('search') }}"
                            class="form-control" placeholder="Cari tiket, nama pelapor, atau lokasi...">
                    </div>
                    <div class="filter-field">
                        <label for="filterLayanan">Layanan</label>
                        <select id="filterLayanan" name="layanan" class="form-control">
                            <option value="">Semua Layanan</option>
                            @foreach ($layananList as $value => $label)
                                <option value="{{ $value }}"
                                    {{ (string) request('layanan') === (string) $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="filterStatus">Status</label>
                        <select id="filterStatus" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach ($statusList as $value => $label)
                                <option value="{{ $value }}"
                                    {{ (string) request('status') === (string) $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field" style="flex: 1 1 100%; min-width: 0;">
                        <div class="filter-actions" style="flex-wrap: wrap; justify-content: flex-end;">
                            <button type="submit" class="btn btn-instansi-primary btn-pill"
                                style="white-space: nowrap;">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            @if (request()->hasAny(['search', 'layanan', 'status']))
                                <a href="{{ route('dinsos.laporan_cepat') }}" class="btn btn-light btn-pill"
                                    style="white-space: nowrap;">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Daftar Laporan Cepat</h4>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        {{ $laporan->total() }} laporan ditemukan
                    </p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Tiket</th>
                                <th>Layanan</th>
                                <th>Nama Pelapor</th>
                                <th>Lokasi</th>
                                <th>Kronologi</th>
                                <th>Status</th>
                                <th>Tanggal Masuk</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $index => $item)
                                @php
                                    // Laporan cepat tidak mengisi kecamatan/judul lengkap,
                                    // jadi lokasi diambil dari tempat kejadian lalu alamat pelapor.
                                    $pelapor = $item->detail_pelapor;
                                    $kasus = $item->detail_kasus;
                                    $tempat = trim((string) optional($kasus)->tempat_kejadian);
                                    $lokasi = $item->kecamatan
                                        ?: ($tempat !== '' && $tempat !== '-' ? $tempat : optional($pelapor)->alamat)
                                        ?: '-';

                                    // created_at tersimpan UTC; tampilkan dalam WIB.
                                    $masuk = $item->created_at
                                        ? \Illuminate\Support\Carbon::parse($item->created_at)->timezone('Asia/Jakarta')
                                        : null;
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $laporan->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('dinsos.laporan_cepat.show', $item->id_laporan) }}"
                                            class="fw-semibold">
                                            {{ $item->id_laporan }}
                                        </a>
                                    </td>
                                    <td>{{ $item->layanan_label }}</td>
                                    <td>{{ optional($pelapor)->nama ?: '-' }}</td>
                                    <td>
                                        <span title="{{ $lokasi }}">{{ \Illuminate\Support\Str::limit($lokasi, 45) }}</span>
                                    </td>
                                    <td>
                                        <span title="{{ optional($kasus)->kronologi }}">
                                            {{ \Illuminate\Support\Str::limit((string) optional($kasus)->kronologi, 60) ?: '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        @include('backend.components.status-pill', ['status' => $item->status])
                                    </td>
                                    <td class="text-muted">
                                        <div>{{ optional($masuk)->format('d-m-Y') ?? '-' }}</div>
                                        <small>{{ optional($masuk)->format('H:i') ?? '-' }} WIB</small>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('dinsos.laporan_cepat.show', $item->id_laporan) }}"
                                                class="btn btn-instansi-soft btn-sm btn-pill">Detail</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fa fa-folder-open"></i></div>
                                            <p class="mb-0">Belum ada laporan cepat PJSRK yang dikirim.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($laporan->hasPages())
                    <div class="d-flex justify-content-center p-3">
                        {{ $laporan->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
