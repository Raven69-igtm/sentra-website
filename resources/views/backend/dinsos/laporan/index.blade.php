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
                    <div class="filter-field" style="flex: 2 1 280px;">
                        <label for="filterSearch">Pencarian</label>
                        <input type="text" id="filterSearch" name="search" value="{{ request('search') }}"
                            class="form-control" placeholder="Cari tiket, judul, atau nama pelapor...">
                    </div>
                    <div class="filter-field">
                        <label for="filterStatus">Status</label>
                        <select id="filterStatus" name="status" class="form-control">
                            <option value="">Semua Status</option>
                            @foreach ($statusList as $value => $label)
                                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                    {{ $label }}
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
                                        {{ request('layanan') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endunless
                    <div class="filter-field" style="flex: 0 0 auto;">
                        <label>&nbsp;</label>
                        <div class="d-flex" style="gap: 8px;">
                            <button type="submit" class="btn btn-instansi-primary btn-pill">
                                <i class="fa fa-filter"></i> Filter
                            </button>
                            @if (request()->hasAny(['search', 'status', 'layanan']))
                                <a href="{{ $filterRoute }}" class="btn btn-light btn-pill">Reset</a>
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
                                <th>Kecamatan</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $index => $item)
                                <tr>
                                    <td class="text-muted">{{ $laporan->firstItem() + $index }}</td>
                                    <td>
                                        <a href="{{ route('dinsos.laporan.show', $item->id_laporan) }}"
                                            class="fw-semibold">
                                            {{ $item->id_laporan }}
                                        </a>
                                    </td>
                                    <td>{{ $item->judul_tampil }}</td>
                                    <td>{{ $item->layanan_label !== '-' ? $item->layanan_label : '-' }}</td>
                                    <td>{{ $item->kecamatan_tampil }}</td>
                                    <td class="text-muted">{{ optional($item->created_at)->format('d-m-Y') }}</td>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('rejectModal');
            var form = document.getElementById('rejectForm');
            var description = document.getElementById('rejectDescription');

            document.querySelectorAll('.js-reject').forEach(function(button) {
                button.addEventListener('click', function() {
                    form.action = button.dataset.action;
                    description.textContent = 'Tambahkan alasan penolakan untuk tiket ' + button.dataset.ticket + '.';
                });
            });

            if (window.jQuery) {
                window.jQuery(modal).on('hidden.bs.modal', function() {
                    form.reset();
                });
            }
        });
    </script>
@endpush
