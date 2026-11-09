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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @php
        $filterRoute = $activeLayanan
            ? route('dinsos.layanan', str_replace('_', '-', $activeLayanan))
            : route('dinsos.laporan.index');
    @endphp

    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>{{ strtoupper($title) }}</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dinsos.dashboard') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dinsos.laporan.index') }}">Laporan</a></li>
                    <li class="breadcrumb-item active"><a href="{{ $filterRoute }}">{{ $title }}</a></li>
                </ol>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-lg-12">
                <form method="GET" action="{{ $filterRoute }}">
                    <div class="row">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                                placeholder="Cari tiket, judul, atau nama pelapor...">
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                @foreach ($statusList as $value => $label)
                                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @unless ($activeLayanan)
                            <div class="col-md-3 mb-3 mb-md-0">
                                <select name="layanan" class="form-control">
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
                        <div class="col-md-2 d-flex align-items-start">
                            <button type="submit" class="btn btn-primary mr-2">Filter</button>
                            @if (request()->hasAny(['search', 'status', 'layanan']))
                                <a href="{{ $filterRoute }}" class="btn btn-light">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped text-center table-responsive-sm">
                                <thead class="custom-font-sidebar bg-ijo">
                                    <tr>
                                        <th>No</th>
                                        <th>No. Tiket</th>
                                        <th>Judul</th>
                                        <th>Layanan</th>
                                        <th>Kecamatan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($laporan as $index => $item)
                                        @php
                                            $badgeClass = match ($item->status) {
                                                'dikirim', 'diterima' => 'badge-warning',
                                                'diproses' => 'badge-info',
                                                'selesai' => 'badge-success',
                                                'ditolak' => 'badge-danger',
                                                'dirujuk' => 'badge-secondary',
                                                default => 'badge-light',
                                            };
                                        @endphp
                                        <tr>
                                            <th>{{ $laporan->firstItem() + $index }}</th>
                                            <td>
                                                <a href="{{ route('dinsos.laporan.show', $item->id_laporan) }}">
                                                    {{ $item->id_laporan }}
                                                </a>
                                            </td>
                                            <td>{{ $item->judul_tampil }}</td>
                                            <td>{{ $item->layanan_label !== '-' ? $item->layanan_label : '-' }}</td>
                                            <td>{{ $item->kecamatan_tampil }}</td>
                                            <td>{{ optional($item->created_at)->format('d-m-Y') }}</td>
                                            <td>
                                                <span class="badge {{ $badgeClass }}">{{ $item->status_label }}</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('dinsos.laporan.show', $item->id_laporan) }}"
                                                    class="btn btn-primary btn-sm">Detail</a>

                                                @if (in_array($item->status, ['dikirim', 'diterima'], true))
                                                    <form method="POST"
                                                        action="{{ route('dinsos.laporan.verifikasi', $item->id_laporan) }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit"
                                                            class="btn btn-warning btn-sm">Verifikasi</button>
                                                    </form>
                                                @endif

                                                @if ($item->status === 'diproses')
                                                    <form method="POST"
                                                        action="{{ route('dinsos.laporan.selesai', $item->id_laporan) }}"
                                                        style="display:inline;">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-hijau btn-sm">Selesai</button>
                                                    </form>
                                                @endif

                                                @if (in_array($item->status, ['dikirim', 'diterima', 'diproses'], true))
                                                    <button type="button" class="btn btn-danger btn-sm js-reject"
                                                        data-toggle="modal" data-target="#rejectModal"
                                                        data-action="{{ route('dinsos.laporan.tolak', $item->id_laporan) }}"
                                                        data-ticket="{{ $item->id_laporan }}">
                                                        Tolak
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                Tidak ada laporan yang sesuai dengan filter.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if ($laporan->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $laporan->links('pagination::bootstrap-4') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tolak --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-ijo">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Laporan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="rejectForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p id="rejectDescription">Tambahkan alasan penolakan untuk laporan ini.</p>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Alasan Penolakan</label>
                            <textarea name="alasan_penolakan" class="form-control" rows="4" required maxlength="1000"
                                placeholder="Contoh: Laporan tidak sesuai dengan layanan Dinsos atau data tidak lengkap."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Laporan</button>
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
