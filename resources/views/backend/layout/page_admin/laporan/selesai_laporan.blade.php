@extends('backend.layout.admin_layout')
@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Laporan Selesai',
            'subtitle' => 'Arsip laporan yang telah selesai ditangani atau dirujuk',
            'icon' => 'fa-circle-check',
        ])

        {{-- Filter & pencarian (kolom tabel difilter lewat DataTables) --}}
        <div class="filter-bar mb-4">
            <div class="filter-grid">
                <div class="filter-field" style="flex: 2 1 280px;">
                    <label for="searchNama">Cari Pelapor</label>
                    <input type="text" id="searchNama" class="form-control" placeholder="Cari nama pelapor...">
                </div>
                <div class="filter-field">
                    <label for="filterKategori">Kategori</label>
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
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Arsip Laporan</h4>
                <span class="badge badge-primary">{{ $laporan->count() }} laporan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="laporanTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Pelapor</th>
                                <th>Status</th>
                                <th>Kategori</th>
                                <th>Tanggal Laporan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($laporan as $index => $data)
                                <tr>
                                    <th>{{ $index + 1 }}</th>
                                    <td>{{ $data->detail_pelapor->nama ?? '-' }}</td>
                                    <td>
                                        @include('backend.components.status-pill', ['status' => $data->status])
                                    </td>
                                    <td>{{ $data->kategori }}</td>
                                    <td>{{ $data->created_at->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="action-group">
                                            <button type="button" class="btn btn-instansi-soft btn-pill btn-sm"
                                                title="Preview" onclick="showPreview('{{ $data->id_laporan }}')">
                                                Preview
                                            </button>

                                            @if (Auth::user()->isSuperAdmin())
                                                <form id="delete-form-{{ $data->id_laporan }}"
                                                    action="{{ route('laporan.destroy', $data->id_laporan) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-pill btn-sm"
                                                        title="Hapus"
                                                        onclick="confirmDelete('{{ $data->id_laporan }}', '{{ $data->detail_pelapor->nama ?? 'Tanpa Nama' }}')">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Preview Laporan --}}
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="previewModalLabel">Preview Laporan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    {{-- Content akan dimuat via AJAX --}}
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fa fa-spinner"></i></div>
                        <p>Memuat data laporan...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-pill" data-dismiss="modal">Tutup</button>
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
