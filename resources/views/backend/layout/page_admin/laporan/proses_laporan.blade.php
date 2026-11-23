@extends('backend.layout.admin_layout')
@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Laporan Belum Selesai',
            'subtitle' => 'Laporan masuk (dikirim), diterima, diproses, dan dirujuk yang menunggu penyelesaian',
            'icon' => 'fa-clipboard-list',
        ])

        {{-- Filter server-side: otomatis diterapkan lewat live filter. --}}
        <div class="filter-bar mb-4">
            <form method="GET" action="{{ route('laporan_proses') }}" data-livewire-navigate data-live-debounce="300">
                <div class="filter-grid">
                    <div class="filter-field" style="flex: 2 1 280px;">
                        <label for="searchNama">Cari Pelapor / No. Tiket</label>
                        <input type="text" id="searchNama" name="search" value="{{ request('search') }}"
                            class="form-control" placeholder="Cari nama pelapor atau nomor tiket...">
                    </div>
                    <div class="filter-field">
                        <label for="filterStatus">Status</label>
                        <select id="filterStatus" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="dikirim" {{ request('status') === 'dikirim' ? 'selected' : '' }}>
                                Dikirim
                            </option>
                            <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>
                                Diterima
                            </option>
                            <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>
                                Diproses
                            </option>
                            <option value="dirujuk" {{ request('status') === 'dirujuk' ? 'selected' : '' }}>
                                Dirujuk
                            </option>
                        </select>
                    </div>
                    <div class="filter-field" style="flex: 1 1 100%; min-width: 0;">
                        {{-- Area live terpisah agar tombol Reset ikut diperbarui
                             setelah filter parsial tanpa reload. --}}
                        <div class="filter-actions" data-live-region="ppa-laporan-proses-actions"
                            style="flex-wrap: wrap; justify-content: flex-end;">
                            @if (request()->hasAny(['search', 'status']))
                                <a href="{{ route('laporan_proses') }}" data-live-reset class="btn btn-light btn-pill"
                                    style="white-space: nowrap;">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Area live: filter & polling menukar bagian ini saja (tanpa reload) --}}
        <div class="card" data-live-region="ppa-laporan-proses">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">Daftar Laporan</h4>
                    <p class="mb-0 text-muted" style="font-size: .8rem;">Diperbarui otomatis tiap 10 detik</p>
                </div>
                <span class="badge badge-primary">{{ $laporan->count() }} laporan</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="laporanTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Laporan</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Tanggal Laporan</th>
                                <th>Nama Pelapor</th>
                                <th>Nama Penerima</th>
                                <th>Nama Terlapor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $index => $data)
                                <tr>
                                    <th>{{ $index + 1 }}</th>
                                    <td class="fw-semibold">{{ $data->id_laporan }}</td>
                                    <td>{{ $data->kategori != 'unset' ? $data->kategori : '-' }}</td>
                                    <td>
                                        @include('backend.components.status-pill', ['status' => $data->status])
                                    </td>
                                    <td>{{ $data->created_at->format('d-m-Y H:i') }}</td>
                                    <td>{{ $data->detail_pelapor->nama ?? '-' }}</td>
                                    <td>{{ $data->detail_penerima_manfaat->nama ?? '-' }}</td>
                                    <td>{{ $data->detail_terlapor->nama ?? '-' }}</td>
                                    <td>
                                        <div class="action-group">
                                            {{-- Preview cepat via modal AJAX --}}
                                            <button type="button" class="btn btn-light btn-pill btn-sm preview-btn"
                                                data-id="{{ $data->id_laporan }}" title="Preview Laporan">
                                                Preview
                                            </button>

                                            {{-- Terima laporan baru (khusus status dikirim) --}}
                                            @if ($data->status === 'dikirim')
                                                <form id="terima-form-{{ $data->id_laporan }}"
                                                    action="{{ route('laporan.terima', $data->id_laporan) }}"
                                                    method="POST" style="display:inline;" class="terima-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="button" class="btn btn-warning btn-pill btn-sm terima-btn"
                                                        data-id="{{ $data->id_laporan }}"
                                                        data-nama="{{ $data->detail_pelapor->nama ?? 'Tanpa Nama' }}"
                                                        title="Terima Laporan">
                                                        Terima
                                                    </button>
                                                </form>
                                            @endif

                                            {{-- Form Proses / Lengkapi Data --}}
                                            <form action="{{ route('laporan.proseskan', $data->id_laporan) }}"
                                                method="POST" style="display:inline;" class="proses-form">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-instansi-primary btn-pill btn-sm"
                                                    title="Proses & Edit Laporan">Proses</button>
                                            </form>

                                            @unless (Auth::user()->isAdminDinsos())
                                                {{-- Form Rujuk (aktif saat status diproses; nonaktif jika sudah dirujuk) --}}
                                                <form id="rujuk-form-{{ $data->id_laporan }}"
                                                    action="{{ route('laporan.rujuk', $data->id_laporan) }}"
                                                    method="POST" style="display:inline;" class="rujuk-form">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="button" class="btn btn-instansi-soft btn-pill btn-sm rujuk-btn"
                                                        {{ $data->status !== 'diproses' ? 'disabled' : '' }}
                                                        title="{{ $data->status === 'dirujuk' ? 'Sudah Dirujuk' : ($data->status !== 'diproses' ? 'Proses Laporan Dulu' : 'Rujuk Laporan') }}"
                                                        data-id="{{ $data->id_laporan }}"
                                                        data-nama="{{ $data->detail_pelapor->nama ?? 'Tidak Diketahui' }}">
                                                        Rujuk
                                                    </button>
                                                </form>
                                            @endunless

                                            {{-- Form Selesai (aktif setelah diproses atau dirujuk) --}}
                                            <form id="selesai-form-{{ $data->id_laporan }}"
                                                action="{{ route('laporan.selesai', $data->id_laporan) }}"
                                                method="POST" style="display:inline;" class="selesai-form">
                                                @csrf
                                                @method('PUT')
                                                <button type="button" class="btn btn-hijau btn-pill btn-sm selesai-btn"
                                                    {{ !in_array($data->status, ['diproses', 'dirujuk'], true) ? 'disabled title=Proses-Laporan-Dulu' : '' }}
                                                    data-id="{{ $data->id_laporan }}"
                                                    data-nama="{{ $data->detail_pelapor->nama ?? 'Tanpa Nama' }}">
                                                    Selesai
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9">
                                        <div class="empty-state">
                                            <div class="empty-icon"><i class="fa fa-folder-open"></i></div>
                                            <p class="mb-0">Tidak ada laporan yang sesuai dengan filter.</p>
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

    {{-- Modal Preview Laporan --}}
    <div class="modal fade" id="previewModalProses" tabindex="-1" role="dialog"
        aria-labelledby="previewModalProsesLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="previewModalProsesLabel">Preview Laporan</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContentProses">
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0">Memuat data laporan...</p>
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

    <script>
        // Skrip halaman dapat dieksekusi ulang oleh Livewire (soft navigation),
        // jadi deklarasi dibungkus IIFE dan handler delegasi diberi namespace
        // agar tidak menumpuk. Baris tabel juga ditukar oleh live filter/polling,
        // sehingga aksi baris memakai delegasi di document.
        (function() {
            'use strict';

            var $ = window.jQuery;
            if (!$) return;

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

            // Fungsi helper
            function updateRowNumbers() {
                $('#laporanTable tbody tr').each(function(index) {
                    $(this).find('th:first').text(index + 1);
                });
            }

            // Fungsi konfirmasi rujuk dengan AJAX
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
                                    // Laporan dirujuk tetap di daftar aktif agar bisa ditandai Selesai
                                    showMessage(response.message, 'success');
                                    if (window.sentraLiveRefresh) {
                                        window.sentraLiveRefresh();
                                    } else {
                                        window.location.reload();
                                    }
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

            // Fungsi konfirmasi selesai dengan AJAX
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

            // Event listener untuk form proses - hanya redirect, tidak update status di tabel
            $(document).off('submit.ppaLaporanProses').on('submit.ppaLaporanProses', '.proses-form', function(e) {
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
                            }, 1000);
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

            // Event listener untuk button preview cepat
            $(document).off('click.ppaLaporanPreview').on('click.ppaLaporanPreview', '.preview-btn', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#previewModalProses').modal('show');
                $('#modalContentProses').html(
                    '<div class="text-center py-4"><div class="spinner-border" role="status"></div><p class="mt-2 mb-0">Memuat data laporan...</p></div>'
                );
                $.ajax({
                    url: '/laporan/preview/' + id,
                    type: 'GET',
                    success: function(response) {
                        $('#modalContentProses').html(response);
                    },
                    error: function() {
                        $('#modalContentProses').html(
                            '<div class="alert alert-danger mb-0">Gagal memuat detail laporan. Silakan coba lagi.</div>'
                        );
                    }
                });
            });

            // Event listener untuk button terima (dikirim -> diterima)
            $(document).off('click.ppaLaporanTerima').on('click.ppaLaporanTerima', '.terima-btn', function(e) {
                e.preventDefault();
                if ($(this).prop('disabled')) return;

                var button = $(this);
                var id = button.data('id');
                var nama = button.data('nama');

                Swal.fire({
                    title: 'Terima Laporan',
                    text: `Terima laporan dari ${nama} (#${id})?`,
                    icon: 'question',
                    iconColor: '#059652',
                    showCancelButton: true,
                    confirmButtonColor: '#059652',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Terima',
                    cancelButtonText: 'Batal',
                    buttonsStyling: true,
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    var form = $('#terima-form-' + id);
                    var formData = new FormData(form[0]);
                    button.prop('disabled', true).text('Menerima..');

                    $.ajax({
                        url: form.attr('action'),
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
                                showMessage(response.message, 'success');
                                if (window.sentraLiveRefresh) {
                                    window.sentraLiveRefresh();
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                button.prop('disabled', false).text('Terima');
                                showMessage(response.message || 'Gagal menerima laporan', 'error');
                            }
                        },
                        error: function(xhr) {
                            button.prop('disabled', false).text('Terima');
                            var errorMessage = (xhr.responseJSON && xhr.responseJSON.message)
                                ? xhr.responseJSON.message
                                : 'Terjadi kesalahan. Silakan coba lagi.';
                            showMessage(errorMessage, 'error');
                        }
                    });
                });
            });

            // Event listener untuk button rujuk
            $(document).off('click.ppaLaporanRujuk').on('click.ppaLaporanRujuk', '.rujuk-btn', function(e) {
                e.preventDefault();

                if ($(this).prop('disabled')) return;

                var button = $(this);
                var id = button.data('id');
                var nama = button.data('nama');

                confirmRujukAjax(id, nama, button);
            });

            // Event listener untuk button selesai
            $(document).off('click.ppaLaporanSelesai').on('click.ppaLaporanSelesai', '.selesai-btn', function(e) {
                e.preventDefault();

                if ($(this).prop('disabled')) return;

                var button = $(this);
                var id = button.data('id');
                var nama = button.data('nama');

                confirmSelesaiAjax(id, nama, button);
            });

            // Polling ringan: cukup tukar area hasil lewat live filter (tanpa
            // reload). Timer lama dibersihkan agar tidak menumpuk saat navigasi.
            if (window.__ppaProsesPollTimer) {
                clearInterval(window.__ppaProsesPollTimer);
            }

            window.__ppaProsesPollTimer = setInterval(function() {
                if (!document.querySelector('[data-live-region="ppa-laporan-proses"]')) {
                    clearInterval(window.__ppaProsesPollTimer);
                    window.__ppaProsesPollTimer = null;
                    return;
                }

                if (window.sentraLiveRefresh) {
                    window.sentraLiveRefresh();
                }
            }, 10000);
        })();
    </script>
@endpush
