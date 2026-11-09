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
        $persen = function ($jumlah) use ($totalLaporan) {
            return $totalLaporan > 0 ? round(($jumlah / $totalLaporan) * 100, 1) : 0;
        };
    @endphp

    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>Dashboard</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Menu</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('dinsos.dashboard') }}">Dashboard</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="stat-widget-two card-body">
                        <div class="stat-content">
                            <div class="stat-text">Total Laporan Masuk</div>
                            <div class="stat-digit"> <i class="fa fa-file-text-o"></i>
                                {{ number_format($totalLaporan) }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: 100%" role="progressbar" aria-valuenow="100"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="stat-widget-two card-body">
                        <div class="stat-content">
                            <div class="stat-text">Menunggu Verifikasi</div>
                            <div class="stat-digit"> <i class="fa fa-clock-o"></i>
                                {{ number_format($menungguVerifikasi) }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $persen($menungguVerifikasi) }}%"
                                role="progressbar" aria-valuenow="{{ $persen($menungguVerifikasi) }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="stat-widget-two card-body">
                        <div class="stat-content">
                            <div class="stat-text">Sedang Diproses</div>
                            <div class="stat-digit"> <i class="fa fa-refresh"></i>
                                {{ number_format($sedangDiproses) }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $persen($sedangDiproses) }}%"
                                role="progressbar" aria-valuenow="{{ $persen($sedangDiproses) }}" aria-valuemin="0"
                                aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card">
                    <div class="stat-widget-two card-body">
                        <div class="stat-content">
                            <div class="stat-text">Selesai</div>
                            <div class="stat-digit"> <i class="fa fa-check"></i> {{ number_format($selesai) }}</div>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $persen($selesai) }}%" role="progressbar"
                                aria-valuenow="{{ $persen($selesai) }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-ijo d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">LAPORAN PJSRK PER LAYANAN</h4>
                        @if ($ditolak > 0)
                            <span class="badge badge-danger">{{ number_format($ditolak) }} ditolak</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @forelse ($layananCounts as $layanan => $jumlah)
                            @php
                                $width = $layananMaks > 0 ? round(($jumlah / $layananMaks) * 100, 1) : 0;
                            @endphp
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="font-weight-bold">
                                        {{ \App\Models\Laporan::LAYANAN_PJSRK[$layanan] ?? $layanan }}
                                    </span>
                                    <span class="badge badge-primary">{{ number_format($jumlah) }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" style="width: {{ $width }}%" role="progressbar"
                                        aria-valuenow="{{ $width }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center mb-0">Belum ada data layanan.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-ijo d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">LAPORAN TERBARU</h4>
                        <a href="{{ route('dinsos.laporan.index') }}" class="btn btn-light btn-sm">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped text-center table-responsive-sm">
                                <thead class="custom-font-sidebar bg-ijo">
                                    <tr>
                                        <th>No. Tiket</th>
                                        <th>Judul</th>
                                        <th>Layanan</th>
                                        <th>Kecamatan</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($laporanTerbaru as $item)
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
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">
                                                Belum ada laporan yang masuk ke layanan Dinsos.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
