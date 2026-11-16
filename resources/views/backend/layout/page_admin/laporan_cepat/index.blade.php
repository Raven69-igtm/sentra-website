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

        {{-- Filter & pencarian dijalankan di sisi klien lewat DataTables --}}
        <div class="filter-bar mb-4">
            <div class="filter-grid">
                <div class="filter-field" style="flex: 2 1 280px;">
                    <label for="searchNama">Cari Pelapor</label>
                    <input type="text" id="searchNama" class="form-control" placeholder="Cari nama pelapor...">
                </div>
                <div class="filter-field">
                    <label for="filterStatus">Status</label>
                    <select id="filterStatus" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="Dikirim">Dikirim</option>
                        <option value="Diterima">Diterima</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                        <option value="Dirujuk">Dirujuk</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
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
                                            class="btn btn-instansi-primary btn-pill btn-sm" title="Detail">
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

@push('scripts')
    <script>
        $(document).ready(function() {
            // Semua baris sudah dirender server; DataTables hanya untuk filter.
            var table = $('#laporanCepatTable').DataTable({
                dom: 'rt',
                ordering: false,
                paging: false,
            });

            $('#searchNama').on('keyup', function() {
                table.column(2).search(this.value).draw(); // kolom "Nama Pelapor"
            });

            $('#filterStatus').on('change', function() {
                table.column(5).search(this.value).draw(); // kolom "Status"
            });
        });
    </script>
@endpush
