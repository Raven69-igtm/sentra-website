@extends('backend.layout.admin_layout')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Rekapan',
            'subtitle' => Auth::user()->isAdminDinsos()
                ? 'Rekapitulasi laporan selesai & ditolak layanan Dinsos, siap diekspor ke Excel'
                : 'Rekapitulasi laporan selesai & dirujuk, siap diekspor ke Excel',
            'icon' => 'fa-folder-open',
        ])

        {{-- FILTER PENCARIAN (server-side, realtime via live-region) --}}
        <div class="filter-bar">
            <form method="GET" action="{{ route('rekapan') }}" data-livewire-navigate data-live-debounce="300">
                <div class="filter-grid">
                    <div class="filter-field" style="flex: 2 1 260px;">
                        <label for="searchInput">Pencarian</label>
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}"
                            class="form-control" placeholder="Cari ID laporan, nama, NIK, kategori...">
                    </div>
                    <div class="filter-field">
                        <label for="filterStatus">Status</label>
                        <select id="filterStatus" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            @unless (Auth::user()->isAdminDinsos())
                                <option value="dirujuk" {{ request('status') === 'dirujuk' ? 'selected' : '' }}>Dirujuk</option>
                            @endunless
                            @if (Auth::user()->isAdminDinsos())
                                <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                            @endif
                        </select>
                    </div>
                    <div class="filter-field" style="flex: 1 1 100%; min-width: 0;">
                        <div class="filter-actions" data-live-region="rekapan-actions"
                            style="flex-wrap: wrap; justify-content: flex-end;">
                            @if (request()->hasAny(['search', 'status']))
                                <a href="{{ route('rekapan') }}" data-live-reset class="btn btn-light btn-pill"
                                    style="white-space: nowrap;">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- EXPORT EXCEL (form terpisah, tidak ikut live-filter) --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-1">Ekspor Excel</h4>
                <p class="mb-0 text-muted" style="font-size: .82rem;">
                    Pilih rentang tanggal lalu unduh rekap dalam format Excel
                </p>
            </div>
            <div class="card-body">
                <form id="exportForm" method="POST" action="{{ route('rekapan.export') }}">
                    @csrf
                    <div class="filter-bar" style="box-shadow: none; border: 0; padding: 0;">
                        <div class="filter-grid">
                            <div class="filter-field">
                                <label for="exportStartDate">Tanggal Awal</label>
                                <input type="date" id="exportStartDate" name="start_date" class="form-control">
                            </div>
                            <div class="filter-field">
                                <label for="exportEndDate">Tanggal Akhir</label>
                                <input type="date" id="exportEndDate" name="end_date" class="form-control">
                            </div>
                            <div class="filter-field" style="flex: 0 0 auto;">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-hijau btn-pill">
                                    <i class="fa fa-file-excel me-2"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL REKAPAN — area live: filter menukar bagian ini saja --}}
        <div class="card" data-live-region="rekapan-data">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Data Rekapan</h4>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        {{ $dataLaporan->count() }} data ditemukan
                    </p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover nowrap mb-0" style="width:100%">
                        <thead>
                            <tr>
                                <th>ID Laporan</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th>NIK Pelapor</th>
                                <th>Nama Pelapor</th>
                                <th>NIK Penerima</th>
                                <th>Nama Penerima</th>
                                <th>NIK Terlapor</th>
                                <th>Nama Terlapor</th>
                                <th>Nama Anak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dataLaporan as $p)
                                <tr>
                                    <td class="fw-semibold">{{ $p->id_laporan }}</td>
                                    <td>{{ $p->kategori }}</td>
                                    <td>@include('backend.components.status-pill', ['status' => $p->status])</td>
                                    <td class="text-muted">{{ optional($p->created_at)->format('d-m-Y') }}</td>

                                    <td>{{ $p->detail_pelapor->nik ?? '-' }}</td>
                                    <td>{{ $p->detail_pelapor->nama ?? '-' }}</td>

                                    <td>{{ $p->detail_penerima_manfaat->nik ?? '-' }}</td>
                                    <td>{{ $p->detail_penerima_manfaat->nama ?? '-' }}</td>

                                    <td>{{ $p->detail_terlapor->nik ?? '-' }}</td>
                                    <td>{{ $p->detail_terlapor->nama ?? '-' }}</td>

                                    <td>
                                        {{ optional($p->detail_penerima_manfaat->informasi_anak ?? collect())->pluck('nama')->implode(', ') ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fa fa-folder-open"></i></div>
                                            <p class="mb-0">Belum ada data rekapan.</p>
                                        </div>
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
        // Skrip ini ikut dieksekusi ulang setiap kali Livewire memasang halaman
        // baru (soft navigation), sedangkan DOMContentLoaded tidak terpicu lagi.
        // Karena itu inisialisasi dijalankan langsung dan fungsi filter global
        // DataTables hanya didaftarkan sekali.
        (function() {
            'use strict';

            var $ = window.jQuery;
            if (!$ || !$.fn || !$.fn.dataTable) return;

            // Pencarian gabungan kolom NIK & Nama Pelapor. Didaftarkan sekali
            // ke $.fn.dataTable.ext.search agar tidak menumpuk antar halaman.
            if (!window.__rekapanSearchFilter) {
                window.__rekapanSearchFilter = true;

                $.fn.dataTable.ext.search.push(function(settings, data) {
                    var table = $(settings.nTable);
                    if (!table.length || table.attr('id') !== 'rekapanTable') return true;

                    var keyword = ($('#searchInput').val() || '').toLowerCase();
                    if (!keyword) return true;

                    var col4 = (data[4] || '').toLowerCase();
                    var col5 = (data[5] || '').toLowerCase();

                    return col4.indexOf(keyword) !== -1 || col5.indexOf(keyword) !== -1;
                });
            }

            var tableEl = document.getElementById('rekapanTable');
            if (!tableEl) return;

            // Instans lama bisa tertinggal saat halaman dipasang ulang.
            if ($.fn.dataTable.isDataTable(tableEl)) {
                $(tableEl).DataTable().destroy();
            }

            var table = $(tableEl).DataTable({
                responsive: true,
                paging: true,
                dom: 'rt',
                ordering: false,
                info: true
            });

            // Trigger filter saat mengetik
            $('#searchInput').on('keyup.rekapan', function() {
                table.draw();
            });

            // Update jumlah data
            function updateCount() {
                var info = table.page.info();
                $('#dataCount').text(info.recordsDisplay);
            }

            $(tableEl).on('draw.dt.rekapan', updateCount);
            updateCount();

            // Filter berdasarkan status (kolom ke-2)
            $('#filterStatus').on('change.rekapan', function() {
                table.column(2).search(this.value).draw();
            });

            // Validasi rentang tanggal sebelum ekspor
            var exportForm = document.getElementById('exportForm');
            if (exportForm) {
                exportForm.addEventListener('submit', function(e) {
                    var startDate = document.getElementById('exportStartDate').value;
                    var endDate = document.getElementById('exportEndDate').value;

                    if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                        e.preventDefault();
                        alert('Tanggal akhir harus lebih besar atau sama dengan tanggal awal!');
                    }
                });
            }
        })();
    </script>
@endpush
