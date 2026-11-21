@extends('backend.layout.admin_layout')
@section('admin')
    {{-- Halaman Admin PPA → Laporan Cepat.
         Data $laporan berasal dari LaporanCepatController@index
         (scope forUser + laporanCepat), bukan data dummy. --}}
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Laporan Cepat',
            'subtitle' => 'Laporan yang dikirim melalui fitur Laporan Cepat aplikasi mobile',
            'icon' => 'fa-bolt',
        ])

        {{-- Filter server-side: otomatis diterapkan lewat live filter. --}}
        <div class="filter-bar mb-4">
            <form method="GET" action="{{ route('laporan_cepat') }}" data-livewire-navigate data-live-debounce="300">
                <div class="filter-grid">
                    <div class="filter-field" style="flex: 2 1 280px;">
                        <label for="searchNama">Cari Pelapor / No. Tiket</label>
                        <input type="text" id="searchNama" name="search" value="{{ request('search') }}"
                            class="form-control" placeholder="Cari nama pelapor, tiket, atau lokasi...">
                    </div>
                    <div class="filter-field">
                        <label for="filterStatus">Status</label>
                        <select id="filterStatus" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach (['dikirim' => 'Dikirim', 'diterima' => 'Diterima', 'diproses' => 'Diproses', 'selesai' => 'Selesai', 'dirujuk' => 'Dirujuk', 'ditolak' => 'Ditolak'] as $value => $label)
                                <option value="{{ $value }}"
                                    {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field" style="flex: 1 1 100%; min-width: 0;">
                        {{-- Area live terpisah agar tombol Reset ikut diperbarui
                             setelah filter parsial tanpa reload. --}}
                        <div class="filter-actions" data-live-region="ppa-laporan-cepat-actions"
                            style="flex-wrap: wrap; justify-content: flex-end;">
                            @if (request()->hasAny(['search', 'status']))
                                <a href="{{ route('laporan_cepat') }}" data-live-reset class="btn btn-light btn-pill"
                                    style="white-space: nowrap;">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Area live: filter menukar bagian ini saja (tanpa reload) --}}
        <div class="card" data-live-region="ppa-laporan-cepat">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Daftar Laporan Cepat</h4>
                <span class="text-muted" style="font-size: .8rem;">{{ $laporan->count() }} laporan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="laporanCepatTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Laporan</th>
                                <th>Nama Pelapor</th>
                                <th>Lokasi</th>
                                <th>Kronologi</th>
                                <th>Status</th>
                                <th>Tanggal Laporan</th>
                                <th>Waktu</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $index => $data)
                                @php
                                    // Laporan Cepat tidak mengisi kecamatan/tempat kejadian
                                    // (quickAccess menulis '-'), jadi lokasi jatuh ke alamat pelapor.
                                    $tempat = trim((string) optional($data->detail_kasus)->tempat_kejadian);
                                    $lokasi = $data->kecamatan
                                        ?: ($tempat !== '' && $tempat !== '-' ? $tempat : optional($data->detail_pelapor)->alamat)
                                        ?: '-';
                                    // Tanggal laporan = tanggal pada detail_kasus; fallback '-'
                                    // bila data lama belum punya detail kasus.
                                    $tanggalLaporan = optional($data->detail_kasus)->tanggal
                                        ? \Illuminate\Support\Carbon::parse($data->detail_kasus->tanggal)->format('d-m-Y')
                                        : '-';
                                @endphp
                                <tr>
                                    <th>{{ $index + 1 }}</th>
                                    <td>{{ $data->id_laporan }}</td>
                                    <td>{{ optional($data->detail_pelapor)->nama ?: '-' }}</td>
                                    <td>{{ $lokasi }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit(optional($data->detail_kasus)->kronologi, 70) ?: '-' }}
                                    </td>
                                    <td>
                                        @include('backend.components.status-pill', ['status' => $data->status])
                                    </td>
                                    <td>{{ $tanggalLaporan }}</td>
                                    <td>{{ $data->created_at->format('H:i') }}</td>
                                    <td>{{ $data->created_at->format('d-m-Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('laporan_cepat.show', $data->id_laporan) }}"
                                            wire:navigate class="btn btn-instansi-primary btn-pill btn-sm" title="Detail">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        Belum ada laporan cepat yang dikirim.
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
