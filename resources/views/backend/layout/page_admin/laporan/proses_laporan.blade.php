@extends('backend.layout.admin_layout')
@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Laporan Belum Selesai',
            'subtitle' => 'Laporan yang sudah diterima dan sedang ditangani petugas',
            'icon' => 'fa-clipboard-list',
        ])

        {{-- Filter server-side: otomatis diterapkan lewat live filter. --}}
        <div class="filter-bar mb-4">
            <form method="GET" action="{{ route('laporan_proses') }}" data-livewire-navigate>
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
                            <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>
                                Diterima
                            </option>
                            <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>
                                Diproses
                            </option>
                        </select>
                    </div>
                    <div class="filter-field" style="flex: 1 1 100%; min-width: 0;">
                        {{-- Area live terpisah agar tombol Reset ikut diperbarui
                             setelah filter parsial tanpa reload. --}}
                        <div class="filter-actions" data-live-region="ppa-laporan-proses-actions"
                            style="flex-wrap: wrap; justify-content: flex-end;">
                            <button type="submit" class="btn btn-instansi-primary btn-pill"
                                style="white-space: nowrap;">
                                <i class="fa fa-filter"></i> Filter
                            </button>
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
                                <th>Nama Pelapor</th>
                                <th>Status</th>
                                <th>Kategori</th>
                                <th>Tanggal Laporan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $index => $data)
                                <tr>
                                    <th>{{ $index + 1 }}</th>
                                    <td>{{ $data->detail_pelapor->nama ?? '-' }}</td>
                                    <td>
                                        @include('backend.components.status-pill', ['status' => $data->status])
                                    </td>
                                    <td>{{ $data->kategori != 'unset' ? $data->kategori : '-' }}</td>
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
                            @empty
                                <tr>
                                    <td colspan="6">
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

            // Event listener untuk button rujuk
            $(document).off('click.ppaLaporanProses').on('click.ppaLaporanProses', '.rujuk-btn', function(e) {
                e.preventDefault();

                if ($(this).prop('disabled')) return;

                var button = $(this);
                var id = button.data('id');
                var nama = button.data('nama');

                confirmRujukAjax(id, nama, button);
            });

            // Event listener untuk button selesai
            $(document).off('click.ppaLaporanProses').on('click.ppaLaporanProses', '.selesai-btn', function(e) {
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
