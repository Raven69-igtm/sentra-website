@extends('backend.layout.admin_layout')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Rekapan',
            'subtitle' => 'Rekapitulasi laporan selesai & dirujuk, siap diekspor ke Excel',
            'icon' => 'fa-folder-open',
        ])

        {{-- FILTER & EXPORT --}}
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="card-title mb-1">Filter &amp; Ekspor</h4>
                <p class="mb-0 text-muted" style="font-size: .82rem;">
                    Pilih rentang tanggal lalu unduh rekap dalam format Excel
                </p>
            </div>
            <div class="card-body">
                <form id="exportForm" method="POST" action="{{ route('rekapan.export') }}">
                    @csrf

                    <div class="filter-bar" style="box-shadow: none; border: 0; padding: 0;">
                        <div class="filter-grid">
                            <div class="filter-field" style="flex: 2 1 260px;">
                                <label for="searchInput">Cari Pelapor</label>
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Cari nama / NIK pelapor...">
                            </div>
                            <div class="filter-field">
                                <label for="filterStatus">Status</label>
                                <select id="filterStatus" name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="dirujuk">Dirujuk</option>
                                    @if (Auth::user()->isAdminDinsos())
                                        <option value="ditolak">Ditolak</option>
                                    @endif
                                </select>
                            </div>
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

        {{-- TABEL REKAPAN --}}
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Data Rekapan</h4>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        Data yang ditampilkan: <span id="dataCount" class="fw-semibold">0</span>
                    </p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="rekapanTable" class="table table-hover nowrap mb-0" style="width:100%">
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
                                    <td class="text-muted">{{ optional($p->created_at)->format('Y-m-d') }}</td>

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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTable
            const table = $('#rekapanTable').DataTable({
                responsive: true,
                paging: true,
                dom: 'rt',
                ordering: false,
                info: true
            });

            // Filter pencarian di kolom ke-4 dan 5 (NIK & Nama Pelapor)
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const keyword = $('#searchInput').val().toLowerCase();
                const col4 = (data[4] || '').toLowerCase();
                const col5 = (data[5] || '').toLowerCase();

                return col4.includes(keyword) || col5.includes(keyword);
            });

            // Trigger filter saat mengetik
            $('#searchInput').on('keyup', function() {
                table.draw();
            });

            // Update jumlah data
            const updateCount = () => {
                const info = table.page.info();
                $('#dataCount').text(info.recordsDisplay);
            };
            $('#rekapanTable').on('draw.dt', updateCount);
            updateCount();

            // Filter berdasarkan status (kolom ke-2)
            $('#filterStatus').on('change', function() {
                table.column(2).search(this.value).draw();
            });

            // Validasi rentang tanggal sebelum ekspor
            document.getElementById('exportForm').addEventListener('submit', function(e) {
                const startDate = document.getElementById('exportStartDate').value;
                const endDate = document.getElementById('exportEndDate').value;

                if (startDate && endDate) {
                    if (new Date(startDate) > new Date(endDate)) {
                        e.preventDefault();
                        alert('Tanggal akhir harus lebih besar atau sama dengan tanggal awal!');
                        return false;
                    }
                }
            });
        });
    </script>
@endpush
