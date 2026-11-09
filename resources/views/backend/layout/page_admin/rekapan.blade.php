@extends('backend.layout.admin_layout')
@section('admin')
    @if (session('success'))
        <div class="alert alert-custom-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    <div class="container-fluid">
        <!-- Breadcrumb -->
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>REKAPAN</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Menu</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('rekapan') }}">Rekapan</a></li>
                </ol>
            </div>
        </div>

        <form id="exportForm" method="POST" action="{{ route('rekapan.export') }}">
            @csrf
        <div class="row mb-3">
            <div class="col-md-6 mb-2">
                <input type="text" id="searchInput" class="form-control" placeholder="Cari Nama/NIK Pelapor...">
            </div>
            <div class="col-md-2">
                <select id="filterStatus" name="status" class="form-control">
                    <option value="">Semua Status</option>
                    <option value="selesai">Selesai</option>
                    <option value="dirujuk">Dirujuk</option>
                    @if (Auth::user()->isAdminDinsos())
                        <option value="ditolak">Ditolak</option>
                    @endif
                </select>
            </div>
        </div>
            <div class="row mb-3">
                <div class="col-md-2 mb-3 mb-md-0">
                    <label for="exportStartDate" class="form-label fw-bold">Tanggal Awal</label>
                    <input type="date" id="exportStartDate" name="start_date" class="form-control">
                </div>
                <div class="col-md-2 mb-3 mb-md-0">
                    <label for="exportEndDate" class="form-label fw-bold">Tanggal Akhir</label>
                    <input type="date" id="exportEndDate" name="end_date" class="form-control">
                </div>
                <div class="col-md-3 mb-3 mb-md-0">
                    <label class="form-label fw-bold d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-file-excel me-2"></i> Export to Excel
                    </button>
                </div>
            </div>
        </form>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-light">
                <div class="card-body">
                    <div class="table-responsive mt-3">
                        <table id="rekapanTable" class="table table-striped table-bordered nowrap" style="width:100%">
                            <thead class="custom-font-sidebar bg-ijo">
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
                                        <td>{{ $p->id_laporan }}</td>
                                        <td>{{ $p->kategori }}</td>
                                        <td>{{ $p->status_label }}</td>
                                        <td>{{ optional($p->created_at)->format('Y-m-d') }}</td>

                                        <td>{{ $p->detail_pelapor->nik ?? '-' }}</td>
                                        <td>{{ $p->detail_pelapor->nama ?? '-' }}</td>

                                        <td>{{ $p->detail_penerima_manfaat->nik ?? '-' }}</td>
                                        <td>{{ $p->detail_penerima_manfaat->nama ?? '-' }}</td>

                                        <td>{{ $p->detail_terlapor->nik ?? '-' }}</td>
                                        <td>{{ $p->detail_terlapor->nama ?? '-' }}</td>

                                        <td>
                                            {{ optional($p->detail_penerima_manfaat->informasi_anak ?? collect())->pluck('nama')->implode(', ') ?:'-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        @for ($i = 0; $i < 11; $i++)
                                            <td class="{{ $i === 0 ? 'text-center text-warning' : '' }}">
                                                {{ $i === 0 ? 'Data Kosong' : '' }}
                                            </td>
                                        @endfor
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <p class="mt-3">Total Data Ditampilkan: <span id="dataCount"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

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

                // Filter pencarian di kolom ke-4 dan 5
                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    const keyword = $('#searchInput').val().toLowerCase();
                    const col4 = (data[4] || '').toLowerCase(); // Kolom ke-4
                    const col5 = (data[5] || '').toLowerCase(); // Kolom ke-5

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
                // Reset filter
                // $('#resetFilter').on('click', function() {
                //     $('#searchInput').val('');
                //     $('#startDate').val('');
                //     $('#endDate').val('');
                //     $('#kategoriFilter').val('');
                //     table.search('').draw();
                // });
                // Filter berdasarkan status (kolom ke-2)
                $('#filterStatus').on('change', function() {
                    table.column(2).search(this.value).draw();
                });
                // Update nilai pencarian jika diperlukan
                document.getElementById('exportForm').addEventListener('submit', function(e) {
                    // Validasi tanggal
                    const startDate = document.getElementById('exportStartDate').value;
                    const endDate = document.getElementById('exportEndDate').value;

                    if (startDate && endDate) {
                        // Periksa apakah tanggal akhir lebih besar dari tanggal awal
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
@endsection
