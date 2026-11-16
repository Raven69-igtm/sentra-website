@extends('backend.layout.admin_layout')

@section('title', $title)

@section('admin')
    @php
        $filterRoute = $activeLayanan
            ? route('dinsos.layanan', str_replace('_', '-', $activeLayanan))
            : route('dinsos.laporan.index');
    @endphp

    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => $title,
            'subtitle' => 'Kelola laporan PJSRK yang masuk ke Dinas Sosial',
            'icon' => 'fa-file-shield',
        ])

        {{-- FILTER --}}
        <div class="filter-bar">
            <form method="GET" action="{{ $filterRoute }}">
                <div class="filter-grid">
                    <div class="filter-field" style="flex: 2 1 260px;">
                        <label for="filterSearch">Pencarian</label>
                        <input type="text" id="filterSearch" name="search" value="{{ request('search') }}"
                            class="form-control" placeholder="Cari tiket, judul, pelapor, atau lokasi...">
                    </div>
                    <div class="filter-field">
                        <label for="filterStatus">Status</label>
                        <select id="filterStatus" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach ($statusList as $value => $label)
                                <option value="{{ $value }}"
                                    {{ strcasecmp((string) request('status'), (string) $value) === 0 ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-field">
                        <label for="filterKecamatan">Kecamatan</label>
                        <select id="filterKecamatan" name="kecamatan" class="form-control">
                            <option value="">Semua Kecamatan</option>
                            @foreach ($kecamatanList as $kecamatan)
                                <option value="{{ $kecamatan }}"
                                    {{ strcasecmp((string) request('kecamatan'), (string) $kecamatan) === 0 ? 'selected' : '' }}>
                                    {{ $kecamatan }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @unless ($activeLayanan)
                        <div class="filter-field">
                            <label for="filterLayanan">Layanan</label>
                            <select id="filterLayanan" name="layanan" class="form-control">
                                <option value="">Semua Layanan</option>
                                @foreach ($layananList as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ strcasecmp((string) request('layanan'), (string) $value) === 0 ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endunless
                    {{-- Tombol aksi dibuat satu baris sendiri agar tidak
                         terpotong saat layar menyempit. --}}
                    <div class="filter-field" style="flex: 1 1 100%; min-width: 0;">
                        <div class="filter-actions" style="flex-wrap: wrap; justify-content: flex-end;">
                            <button type="submit" class="btn btn-instansi-primary btn-pill"
                                style="white-space: nowrap;">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            @if (request()->hasAny(['search', 'status', 'layanan', 'kecamatan']))
                                <a href="{{ $filterRoute }}" class="btn btn-light btn-pill"
                                    style="white-space: nowrap;">Reset</a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- TABEL LAPORAN --}}
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Daftar Laporan</h4>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        {{ $laporan->total() }} laporan ditemukan
                    </p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Tiket</th>
                                <th>Judul</th>
                                <th>Layanan</th>
                                <th>Lokasi</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $index => $item)
                                @php
                                    $pj = $item->detail_pjsrk;
                                    $kasus = $item->detail_kasus;
                                    $dampak = optional($pj)->tingkat_dampak;
                                    $dampakClass = match ($dampak) {
                                        'Berat' => 'badge-danger',
                                        'Sedang' => 'badge-warning',
                                        'Ringan' => 'badge-success',
                                        default => 'badge-light',
                                    };
                                    $jumlahFoto = $pj ? count($pj->foto_urls ?? []) : 0;
                                    $jenisPermasalahan = optional($pj)->jenis_permasalahan;
                                    $adaBadge = $dampak || $jenisPermasalahan || ($pj && $pj->rahasia) || $jumlahFoto > 0;

                                    $tanggalKasus = $kasus && !empty($kasus->tanggal)
                                        ? \Illuminate\Support\Carbon::parse($kasus->tanggal)
                                        : null;
                                    $adaJam = $tanggalKasus && !empty($kasus->waktu_kejadian);
                                    if ($adaJam) {
                                        $jam = \Illuminate\Support\Carbon::parse($kasus->waktu_kejadian);
                                        $tanggalKasus->setTime((int) $jam->format('H'), (int) $jam->format('i'));
                                    }
                                    // Waktu laporan masuk tersimpan dalam UTC (APP_TIMEZONE=UTC),
                                    // jadi dikonversi ke WIB agar sama dengan jam sebenarnya.
                                    $tanggalMasuk = $item->created_at
                                        ? \Illuminate\Support\Carbon::parse($item->created_at)->timezone('Asia/Jakarta')
                                        : null;
                                    // Waktu kejadian bencana diisi manual dari aplikasi dan sudah
                                    // memakai jam lokal, jadi ditampilkan apa adanya.
                                    $kejadianLokal = $adaJam && ($item->layanan_pjsrk === 'pengaduan_bencana'
                                        || !empty(optional($pj)->jenis_bencana));
                                    $tanggalTampil = $kejadianLokal ? $tanggalKasus : ($tanggalMasuk ?? $tanggalKasus);
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $laporan->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('dinsos.laporan.show', $item->id_laporan) }}"
                                            class="fw-semibold">
                                            {{ $item->id_laporan }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>{{ $item->judul_tampil }}</div>
                                        @if ($adaBadge)
                                            <div class="d-flex flex-wrap align-items-center"
                                                style="gap: 4px; margin-top: 4px;">
                                                @if ($dampak)
                                                    <span class="badge {{ $dampakClass }}"
                                                        title="Tingkat dampak bencana">{{ $dampak }}</span>
                                                @endif
                                                @if ($jenisPermasalahan)
                                                    <span class="badge badge-info"
                                                        title="Jenis permasalahan sosial">{{ $jenisPermasalahan }}</span>
                                                @endif
                                                @if ($pj && $pj->rahasia)
                                                    <span class="badge badge-warning"
                                                        title="Pelapor meminta identitas dirahasiakan">
                                                        <i class="fa fa-lock"></i> Rahasia
                                                    </span>
                                                @endif
                                                @if ($jumlahFoto > 0)
                                                    <span class="badge badge-light"
                                                        title="{{ $jumlahFoto }} foto dilampirkan">
                                                        <i class="fa fa-image"></i> {{ $jumlahFoto }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $item->layanan_label !== '-' ? $item->layanan_label : '-' }}</td>
                                    <td>
                                        @php
                                            $alamatLengkap = trim((string) optional($item->detail_pjsrk)->alamat);
                                        @endphp
                                        <div>{{ $item->kecamatan_tampil }}</div>
                                        @if ($alamatLengkap !== '' && $alamatLengkap !== $item->kecamatan_tampil)
                                            <small class="text-muted" title="{{ $alamatLengkap }}">
                                                {{ \Illuminate\Support\Str::limit($alamatLengkap, 40) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-muted">
                                        <div>{{ optional($tanggalTampil)->format('d-m-Y') ?? '-' }}</div>
                                        <small
                                            title="{{ $adaJam ? 'Waktu kejadian' : 'Waktu laporan masuk' }}">
                                            {{ optional($tanggalTampil)->format('H:i') ?? '-' }} WIB
                                        </small>
                                    </td>
                                    <td>
                                        @include('backend.components.status-pill', ['status' => $item->status])
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('dinsos.laporan.show', $item->id_laporan) }}"
                                                class="btn btn-instansi-soft btn-sm btn-pill">Detail</a>

                                            @if (in_array($item->status, ['dikirim', 'diterima'], true))
                                                <form method="POST"
                                                    action="{{ route('dinsos.laporan.verifikasi', $item->id_laporan) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-instansi-primary btn-sm btn-pill">Verifikasi</button>
                                                </form>
                                            @endif

                                            @if ($item->status === 'diproses')
                                                <form method="POST"
                                                    action="{{ route('dinsos.laporan.selesai', $item->id_laporan) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit"
                                                        class="btn btn-hijau btn-sm btn-pill">Selesai</button>
                                                </form>
                                            @endif

                                            @if (in_array($item->status, ['dikirim', 'diterima', 'diproses'], true))
                                                <button type="button" class="btn btn-danger btn-sm btn-pill js-reject"
                                                    data-toggle="modal" data-target="#rejectModal"
                                                    data-action="{{ route('dinsos.laporan.tolak', $item->id_laporan) }}"
                                                    data-ticket="{{ $item->id_laporan }}">
                                                    Tolak
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
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

                @if ($laporan->hasPages())
                    <div class="d-flex justify-content-center p-3">
                        {{ $laporan->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Tolak --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Laporan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="rejectForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p id="rejectDescription" class="text-muted" style="font-size: .85rem;">
                            Tambahkan alasan penolakan untuk laporan ini.
                        </p>
                        <div class="form-group mb-0">
                            <label class="form-label">Alasan Penolakan</label>
                            <textarea name="alasan_penolakan" class="form-control" rows="4" required maxlength="1000"
                                placeholder="Contoh: Laporan tidak sesuai dengan layanan Dinsos atau data tidak lengkap."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-pill" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-pill">Tolak Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Gaya modal ditulis per-id karena tema global memakai .modal-header
           berlatar gelap dan .modal-title berwarna #EEEEEE, sehingga judul dan
           tombol tutupnya tidak terbaca. */
        #rejectModal {
            z-index: 1060;
        }

        #rejectModal .modal-content {
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 48px rgba(16, 24, 40, .18);
        }

        #rejectModal .modal-header {
            background: var(--i-primary, #b45309);
            border: 0;
            padding: 18px 22px;
        }

        #rejectModal .modal-title {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
        }

        #rejectModal .modal-header .close {
            margin: 0;
            padding: 0;
            color: #fff;
            opacity: .85;
            text-shadow: none;
            font-size: 1.6rem;
            line-height: 1;
        }

        #rejectModal .modal-header .close:hover {
            color: #fff;
            opacity: 1;
        }

        #rejectModal .modal-body {
            padding: 20px 22px;
            max-height: none;
            overflow-y: visible;
        }

        #rejectModal .modal-footer {
            border: 0;
            padding: 0 22px 20px;
            flex-direction: row;
            gap: 8px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalEl = document.getElementById('rejectModal');
            var form = document.getElementById('rejectForm');
            var description = document.getElementById('rejectDescription');

            // Modal dipindah ke <body> agar tidak terkurung di dalam
            // #main-wrapper/.content-body yang memiliki overflow dan stacking
            // context sendiri (penyebab modal terpotong atau tertimpa lapisan).
            if (modalEl && modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }

            var $modal = window.jQuery ? window.jQuery(modalEl) : null;
            var canShow = $modal && typeof $modal.modal === 'function';

            function showRejectModal(button) {
                form.action = button.dataset.action;
                description.textContent = 'Tambahkan alasan penolakan untuk tiket ' + button.dataset.ticket + '.';

                // Dibuka manual supaya tidak bergantung pada data-api tema.
                if (canShow) {
                    $modal.modal('show');
                }
            }

            // Tombol "Tolak" pada setiap baris laporan.
            document.querySelectorAll('.js-reject').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    showRejectModal(button);
                });
            });

            if (canShow) {
                $modal.on('hidden.bs.modal', function() {
                    form.reset();
                    form.removeAttribute('action');
                });
            }
        });
    </script>
@endpush
