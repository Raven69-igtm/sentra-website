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
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>LAPORAN SELESAI</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Laporan</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('selesai') }}">Selesai</a></li>
                </ol>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="searchNama" class="form-control" placeholder="Cari Nama Pelapor...">
            </div>
            <div class="col-md-2">
                <select id="filterKategori" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="Kekerasan Fisik">Kekerasan Fisik</option>
                    <option value="Kekerasan Psikis">Kekerasan Psikis</option>
                    <option value="Kekerasan Seksual">Kekerasan Seksual</option>
                    <option value="Penelantaran">Penelantaran</option>
                    <option value="Eksploitasi">Eksploitasi</option>
                    <option value="TPPO">TPPO</option>
                    <option value="unset">Tidak Disetting</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="laporanTable" class="table table-striped text-center table-responsive-sm">
                                <thead class="custom-font-sidebar bg-ijo">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Pelapor</th>
                                        <th>Status</th>
                                        <th>Kategori</th>
                                        <th>Tanggal Laporan</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($laporan as $index => $data)
                                        <tr>
                                            <th>{{ $index + 1 }}</th>
                                            <td>{{ $data->detail_pelapor->nama ?? '-' }}</td>
                                            <td>
                                                @if ($data->status == 'selesai')
                                                    <span class="badge badge-success">SELESAI</span>
                                                @elseif ($data->status == 'dirujuk')
                                                    <span class="badge badge-primary">DIRUJUK</span>
                                                @endif
                                            </td>
                                            <td>{{ $data->kategori }}</td>
                                            <td>{{ $data->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                <!-- Tombol Preview -->
                                                <button type="button" class="btn btn-hijau btn-sm" title="Preview"
                                                    onclick="showPreview('{{ $data->id_laporan }}')">
                                                    Preview
                                                </button>

                                                @if (Auth::user()->isSuperAdmin())
                                                    <form id="delete-form-{{ $data->id_laporan }}"
                                                        action="{{ route('laporan.destroy', $data->id_laporan) }}"
                                                        method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm" title="Hapus"
                                                            onclick="confirmDelete('{{ $data->id_laporan }}', '{{ $data->detail_pelapor->nama ?? 'Tanpa Nama' }}')">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Modal Preview Laporan --}}
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header bg-ijo">
                    <h4 class="modal-title text-white" id="previewModalLabel">PREVIEW LAPORAN</h4>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    {{-- Content akan dimuat via AJAX --}}
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p>Memuat data laporan...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('backend.components.style-confirm')
    {{-- JavaScript untuk Modal --}}
    <script>
        function showPreview(laporanId) {
            // Tampilkan modal
            $('#previewModal').modal('show');

            // Reset content dengan loading
            $('#modalContent').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p>Memuat data laporan...</p>
        </div>
    `);

            // Load content via AJAX
            $.ajax({
                url: '/laporan/preview/' + laporanId,
                type: 'GET',
                success: function(response) {
                    $('#modalContent').html(response);
                },
                error: function(xhr, status, error) {
                    $('#modalContent').html(`
                <div class="alert alert-danger">
                    <h5>Error!</h5>
                    <p>Gagal memuat data laporan. Silakan coba lagi.</p>
                </div>
            `);
                }
            });
        }
    </script>
    <script>
        function confirmDelete(id, nama = 'data ini') {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `Apakah Anda yakin ingin menghapus laporan ${nama}?`,
                icon: 'warning',
                iconColor: '#ff4d4f',
                showCancelButton: true,
                confirmButtonColor: '#059652',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                buttonsStyling: true,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
    <script>
        $(document).ready(function() {
            const table = $('#laporanTable').DataTable({
                dom: 'rt',
                ordering: false,
            });

            // Search berdasarkan nama pelapor (kolom ke-1)
            $('#searchNama').on('keyup', function() {
                table.column(1).search(this.value).draw();
            });

            // Filter berdasarkan status (kolom ke-2)
            $('#filterKategori').on('change', function() {
                table.column(3).search(this.value).draw();
            });
        });
    </script>
@endpush
