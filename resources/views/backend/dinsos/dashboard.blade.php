@extends('backend.layout.admin_layout')

@section('admin')
    @php
        $persen = function ($jumlah) use ($totalLaporan) {
            return $totalLaporan > 0 ? round(($jumlah / $totalLaporan) * 100, 1) : 0;
        };

        $statusRingkas = [
            ['label' => 'Menunggu verifikasi', 'jumlah' => $menungguVerifikasi, 'class' => 'is-amber'],
            ['label' => 'Sedang diproses', 'jumlah' => $sedangDiproses, 'class' => 'is-blue'],
            ['label' => 'Selesai', 'jumlah' => $selesai, 'class' => 'is-green'],
            ['label' => 'Ditolak', 'jumlah' => $ditolak, 'class' => 'is-rose'],
        ];
    @endphp

    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Dashboard',
            'subtitle' => 'Ringkasan laporan PJSRK Dinas Sosial PPPA',
            'icon' => 'fa-gauge-high',
        ])

        {{-- BARIS 1 — KPI --}}
        <div class="row">
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card h-100" data-reveal style="--d:0ms">
                    <div class="card-body">
                        <div class="stat-card">
                            <span class="stat-icon"><i class="fa fa-inbox"></i></span>
                            <div>
                                <div class="stat-label">Total Laporan</div>
                                <div class="stat-value" data-countup="{{ $totalLaporan }}">0</div>
                                <div class="stat-hint">Seluruh layanan PJSRK</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card h-100" data-reveal style="--d:60ms">
                    <div class="card-body">
                        <div class="stat-card">
                            <span class="stat-icon is-amber"><i class="fa fa-hourglass-half"></i></span>
                            <div>
                                <div class="stat-label">Menunggu Verifikasi</div>
                                <div class="stat-value" data-countup="{{ $menungguVerifikasi }}">0</div>
                                <div class="stat-hint">{{ $persen($menungguVerifikasi) }}% dari total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card h-100" data-reveal style="--d:120ms">
                    <div class="card-body">
                        <div class="stat-card">
                            <span class="stat-icon is-blue"><i class="fa fa-spinner"></i></span>
                            <div>
                                <div class="stat-label">Sedang Diproses</div>
                                <div class="stat-value" data-countup="{{ $sedangDiproses }}">0</div>
                                <div class="stat-hint">{{ $persen($sedangDiproses) }}% dari total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card h-100" data-reveal style="--d:180ms">
                    <div class="card-body">
                        <div class="stat-card">
                            <span class="stat-icon is-green"><i class="fa fa-circle-check"></i></span>
                            <div>
                                <div class="stat-label">Selesai</div>
                                <div class="stat-value" data-countup="{{ $selesai }}">0</div>
                                <div class="stat-hint">{{ $persen($selesai) }}% dari total</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 2 — LAYANAN + STATUS --}}
        <div class="row">
            <div class="col-xl-7 mb-4">
                <div class="card h-100" data-reveal style="--d:240ms">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">Laporan per Layanan</h4>
                            <p class="mb-0 text-muted" style="font-size: .82rem;">
                                Distribusi laporan pada layanan PJSRK
                            </p>
                        </div>
                        @if ($ditolak > 0)
                            <span class="badge badge-light">{{ number_format($ditolak) }} ditolak</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @forelse ($layananCounts as $layanan => $jumlah)
                            @php
                                $width = $layananMaks > 0 ? round(($jumlah / $layananMaks) * 100, 1) : 0;
                            @endphp
                            <div class="progress-line">
                                <div style="width: 100%;">
                                    <div class="progress-line-head">
                                        <span class="progress-line-label">
                                            {{ \App\Models\Laporan::LAYANAN_PJSRK[$layanan] ?? $layanan }}
                                        </span>
                                        <span class="progress-line-value">{{ number_format($jumlah) }}</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $width }}%" aria-valuenow="{{ $width }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fa fa-folder-open"></i></div>
                                <p class="mb-0">Belum ada data layanan.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-xl-5 mb-4">
                <div class="card h-100" data-reveal style="--d:300ms">
                    <div class="card-header">
                        <h4 class="card-title mb-1">Status Penanganan</h4>
                        <p class="mb-0 text-muted" style="font-size: .82rem;">Komposisi status seluruh laporan</p>
                    </div>
                    <div class="card-body">
                        @foreach ($statusRingkas as $status)
                            <div class="progress-line">
                                <div style="width: 100%;">
                                    <div class="progress-line-head">
                                        <span class="progress-line-label">{{ $status['label'] }}</span>
                                        <span class="progress-line-value">{{ number_format($status['jumlah']) }}</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $persen($status['jumlah']) }}%"
                                            aria-valuenow="{{ $persen($status['jumlah']) }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <div class="progress-line-meta">{{ $persen($status['jumlah']) }}% dari total</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 3 — LAPORAN TERBARU --}}
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card" data-reveal style="--d:360ms">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">Laporan Terbaru</h4>
                            <p class="mb-0 text-muted" style="font-size: .82rem;">8 laporan terakhir pada layanan PJSRK</p>
                        </div>
                        <a href="{{ route('dinsos.laporan.index') }}" class="btn btn-instansi-soft btn-pill">
                            Lihat semua <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
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
                                        <tr>
                                            <td>
                                                <a href="{{ route('dinsos.laporan.show', $item->id_laporan) }}"
                                                    class="fw-semibold">{{ $item->id_laporan }}</a>
                                            </td>
                                            <td>{{ \Illuminate\Support\Str::limit($item->judul_tampil, 42) }}</td>
                                            <td class="text-muted">{{ $item->layanan_label !== '-' ? $item->layanan_label : '-' }}</td>
                                            <td class="text-muted">{{ $item->kecamatan_tampil }}</td>
                                            <td class="text-muted">{{ optional($item->created_at)->format('d M Y') }}</td>
                                            <td>@include('backend.components.status-pill', ['status' => $item->status])</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <div class="empty-icon"><i class="fa fa-folder-open"></i></div>
                                                    <p class="mb-0">Belum ada laporan yang masuk ke layanan Dinsos.</p>
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
        </div>
    </div>

    <script>
        (function() {
            'use strict';

            // Angka KPI: hitung naik satu satuan (sekali jalan, hemat)
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            document.querySelectorAll('[data-countup]').forEach(function(el) {
                var target = parseInt(el.getAttribute('data-countup'), 10) || 0;

                if (reduce || target <= 0) {
                    el.textContent = target.toLocaleString('id-ID');
                    return;
                }

                var start = performance.now();
                var duration = 650;

                function step(now) {
                    var progress = Math.min((now - start) / duration, 1);
                    var eased = 1 - Math.pow(1 - progress, 3);
                    el.textContent = Math.round(target * eased).toLocaleString('id-ID');
                    if (progress < 1) requestAnimationFrame(step);
                }

                requestAnimationFrame(step);
            });
        })();
    </script>
@endsection
