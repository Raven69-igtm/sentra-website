@extends('backend.layout.admin_layout')
@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Laporan Belum Selesai',
            'subtitle' => 'Laporan yang sudah diterima dan sedang ditangani petugas',
            'icon' => 'fa-clipboard-list',
        ])

        {{-- Filter & pencarian (kolom tabel difilter lewat DataTables) --}}
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
                        <option value="DITERIMA">Diterima</option>
                        <option value="DIPROSES">Diproses</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Daftar Laporan</h4>
                <span class="text-muted" style="font-size: .8rem;">Diperbarui otomatis tiap 10 detik</span>
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
                                            {{-- Form Proses --}}
                                            <form action="{{ route('laporan.proseskan', $data->id_laporan) }}"
                                                method="POST" style="display:inline;" class="proses-form">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-instansi-primary btn-pill btn-sm"
                                                    title="Proses">Proses</button>
                                            </form>

                                            @unless (Auth::user()->isAdminDinsos())
                                                {{-- Form Rujuk --}}
                                                <form id="rujuk-form-{{ $data->id_laporan }}"
                                                    action="{{ route('laporan.rujuk', $data->id_laporan) }}"
                                                    method="POST" style="display:inline;" class="rujuk-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="button" class="btn btn-instansi-soft btn-pill btn-sm rujuk-btn"
                                                        {{ $data->status == 'diterima' ? 'disabled title=Proses-Laporan-Dulu' : '' }}
                                                        data-id="{{ $data->id_laporan }}"
                                                        data-nama="{{ $data->detail_pelapor->nama ?? 'Tidak Diketahui' }}">
                                                        Rujuk
                                                    </button>
                                                </form>
                                            @endunless

                                            {{-- Form Selesai --}}
                                            <form id="selesai-form-{{ $data->id_laporan }}"
                                                action="{{ route('laporan.selesai', $data->id_laporan) }}"
                                                method="POST" style="display:inline;" class="selesai-form">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" class="btn btn-hijau btn-pill btn-sm selesai-btn"
                                                    {{ $data->status == 'diterima' ? 'disabled title=Proses-Laporan-Dulu' : '' }}
                                                    data-id="{{ $data->id_laporan }}"
                                                    data-nama="{{ $data->detail_pelapor->nama ?? 'Tanpa Nama' }}">
                                                    Selesai
                                                </button>
                                            </form>
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
@endsection

@push('scripts')
    @include('backend.components.style-confirm')

    <script>
        let table;

        $(document).ready(function() {

            initializeDataTable();


            $('#searchNama').on('keyup', function() {
                if (table) {
                    table.column(1).search(this.value).draw();
                }
            });

            // Filter berdasarkan status (kolom ke-2)
            $('#filterStatus').on('change', function() {
                if (table) {
                    table.column(2).search(this.value).draw();
                }
            });


            setInterval(function() {
                refreshLaporanData();
            }, 10000);

            // Event listener untuk form proses - hanya redirect, tidak update status di tabel
            $(document).on('submit', '.proses-form', function(e) {
                e.preventDefault();

                var form = $(this);
                var url = form.attr('action');
                var formData = new FormData(form[0]);
                var submitButton = form.find('button[type="submit"]');

                // Disable button dan ubah text
                submitButton.prop('disabled', true).text('Processing..');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        if (response.success && response.redirect_url) {
                            // Langsung redirect ke halaman edit tanpa update status di tabel
                            showMessage(response.message, 'success');
                            setTimeout(function() {
                                window.location.href = response.redirect_url;
                            }, 1000); // Kurangi delay jadi 1 detik
                        } else {
                            submitButton.prop('disabled', false).text('Proses');
                            showMessage(response.message || 'Terjadi kesalahan', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        submitButton.prop('disabled', false).text('Proses');

                        var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }

                        showMessage(errorMessage, 'error');
                        console.error('Error:', error);
                    }
                });
            });

            // Event listener untuk button rujuk - Perbaiki selector
            $(document).on('click', '.rujuk-btn', function(e) {
                e.preventDefault();

                if ($(this).prop('disabled')) return;

                var button = $(this);
                var id = button.data('id');
                var nama = button.data('nama');

                confirmRujukAjax(id, nama, button);
            });

            // Event listener untuk button selesai - Perbaiki selector
            $(document).on('click', '.selesai-btn', function(e) {
                e.preventDefault();

                if ($(this).prop('disabled')) return;

                var button = $(this);
                var id = button.data('id');
                var nama = button.data('nama');

                confirmSelesaiAjax(id, nama, button);
            });
        });

        // Fungsi untuk initialize DataTable
        function initializeDataTable() {
            if ($.fn.DataTable.isDataTable('#laporanTable')) {
                table = $('#laporanTable').DataTable();
            } else {
                table = $('#laporanTable').DataTable({
                    dom: 'rt',
                    ordering: false,
                    destroy: true,
                    paging: false,
                });
            }
        }

        // Fungsi untuk refresh data laporan
        function refreshLaporanData() {
            $.ajax({
                url: window.location.href,
                type: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data) {
                    var newTableContent = $(data).find('#laporanTable tbody').html();

                    if (newTableContent && newTableContent !== $('#laporanTable tbody').html()) {
                        var searchValue = $('#searchNama').val();
                        var filterValue = $('#filterStatus').val();

                        if ($.fn.DataTable.isDataTable('#laporanTable')) {
                            table.destroy();
                        }

                        // Update isi tabel
                        $('#laporanTable tbody').html(newTableContent);

                        // Re-initialize DataTable
                        initializeDataTable();

                        // Restore filter values
                        if (searchValue) {
                            $('#searchNama').val(searchValue);
                            table.column(1).search(searchValue).draw();
                        }
                        if (filterValue) {
                            $('#filterStatus').val(filterValue);
                            table.column(2).search(filterValue).draw();
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error refreshing data:', error);
                }
            });
        }

        // Fungsi konfirmasi rujuk dengan AJAX - Perbaiki AJAX call
        function confirmRujukAjax(id, nama, button) {
            Swal.fire({
                title: 'Konfirmasi Rujuk',
                text: `Apakah laporan ${nama} akan dirujuk?`,
                icon: 'question',
                iconColor: '#593bdb',
                showCancelButton: true,
                confirmButtonColor: '#593bdb',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Rujuk',
                cancelButtonText: 'Batal',
                buttonsStyling: true,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('#rujuk-form-' + id);
                    var url = form.attr('action');
                    var row = button.closest('tr');
                    var formData = new FormData(form[0]);

                    button.prop('disabled', true).text('Processing..');

                    $.ajax({
                        url: url,
                        type: 'POST', // Gunakan POST
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Hapus row dari tabel karena laporan sudah dirujuk
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                    updateRowNumbers();
                                });

                                showMessage(response.message, 'success');
                            } else {
                                button.prop('disabled', false).text('Rujuk');
                                showMessage(response.message || 'Terjadi kesalahan', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            button.prop('disabled', false).text('Rujuk');

                            var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            showMessage(errorMessage, 'error');
                            console.error('Error:', error);
                        }
                    });
                }
            });
        }

        // Fungsi konfirmasi selesai dengan AJAX - Perbaiki AJAX call
        function confirmSelesaiAjax(id, nama, button) {
            Swal.fire({
                title: 'Konfirmasi Selesai',
                text: `Apakah laporan ${nama} sudah selesai?`,
                icon: 'question',
                iconColor: '#059652',
                showCancelButton: true,
                confirmButtonColor: '#059652',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal',
                buttonsStyling: true,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    var form = $('#selesai-form-' + id);
                    var url = form.attr('action');
                    var row = button.closest('tr');
                    var formData = new FormData(form[0]);

                    button.prop('disabled', true).text('Processing..');

                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        success: function(response) {
                            if (response.success) {
                                // Hapus row dari tabel karena laporan sudah selesai
                                row.fadeOut(300, function() {
                                    $(this).remove();
                                    updateRowNumbers();
                                });

                                showMessage(response.message, 'success');
                            } else {
                                button.prop('disabled', false).text('Selesai');
                                showMessage(response.message || 'Terjadi kesalahan', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            button.prop('disabled', false).text('Selesai');

                            var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }

                            showMessage(errorMessage, 'error');
                            console.error('Error:', error);
                        }
                    });
                }
            });
        }

        // Fungsi helper
        function updateRowNumbers() {
            $('#laporanTable tbody tr').each(function(index) {
                $(this).find('th:first').text(index + 1);
            });
        }

        function showMessage(message, type) {
            var alertClass = type === 'success' ? 'alert-custom-success' : 'alert-danger';
            var alert = $('<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
                message +
                '<button type="button" class="close" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span>' +
                '</button>' +
                '</div>');

            // Remove existing alerts
            $('.alert').remove();

            // Add new alert at the top
            $('.container-fluid').prepend(alert);

            // Auto hide after 5 seconds
            setTimeout(function() {
                alert.alert('close');
            }, 5000);
        }
    </script>
@endpush
