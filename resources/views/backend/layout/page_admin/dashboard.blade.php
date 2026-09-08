@extends('backend.layout.admin_layout')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Dashboard',
            'subtitle' => 'Ringkasan laporan masuk UPTD PPA tahun ' . $tahun_aktif,
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
                                <div class="stat-label">Total Masuk</div>
                                <div class="stat-value" data-countup="{{ $total_masuk }}">0</div>
                                <div class="stat-hint">Semua status &middot; {{ $tahun_aktif }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card h-100" data-reveal style="--d:60ms">
                    <div class="card-body">
                        <div class="stat-card">
                            <span class="stat-icon is-blue"><i class="fa fa-spinner"></i></span>
                            <div>
                                <div class="stat-label">Diproses</div>
                                <div class="stat-value" data-countup="{{ $total_diproses }}">0</div>
                                <div class="stat-hint">Sedang ditangani petugas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card h-100" data-reveal style="--d:120ms">
                    <div class="card-body">
                        <div class="stat-card">
                            <span class="stat-icon is-green"><i class="fa fa-circle-check"></i></span>
                            <div>
                                <div class="stat-label">Selesai</div>
                                <div class="stat-value" data-countup="{{ $total_selesai }}">0</div>
                                <div class="stat-hint">Pendampingan tuntas</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card h-100" data-reveal style="--d:180ms">
                    <div class="card-body">
                        <div class="stat-card">
                            <span class="stat-icon is-slate"><i class="fa fa-share"></i></span>
                            <div>
                                <div class="stat-label">Dirujuk</div>
                                <div class="stat-value" data-countup="{{ $total_dirujuk }}">0</div>
                                <div class="stat-hint">Diteruskan ke instansi lain</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BARIS 2 — GRAFIK + SEBARAN KATEGORI --}}
        <div class="row">
            <div class="col-xl-8 mb-4">
                <div class="card h-100" data-reveal style="--d:240ms">
                    <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title mb-1">Tren Laporan Selesai</h4>
                            <p class="mb-0 text-muted" style="font-size: .82rem;">
                                Jumlah laporan selesai &amp; dirujuk per kategori &mdash; tahun {{ $tahun_aktif }}
                            </p>
                        </div>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <label for="tahun-filter" class="mb-0 text-muted"
                                style="font-size:.78rem;font-weight:700;">Tahun</label>
                            <select class="form-control" id="tahun-filter" onchange="updateChart()"
                                style="width:110px;">
                                @foreach ($tahun_list as $tahun)
                                    <option value="{{ $tahun }}" {{ $tahun == $tahun_aktif ? 'selected' : '' }}>
                                        {{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrap" style="height: 320px;">
                            <canvas id="laporanAreaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 mb-4">
                <div class="card h-100" data-reveal style="--d:300ms">
                    <div class="card-header">
                        <h4 class="card-title mb-1">Sebaran Kategori</h4>
                        <p class="mb-0 text-muted" style="font-size: .82rem;">
                            Proporsi laporan selesai tahun {{ $tahun_aktif }}
                        </p>
                    </div>
                    <div class="card-body">
                        @php
                            $kategoriList = [
                                ['label' => 'Kekerasan Fisik', 'total' => $data_kekerasan_fisik_total, 'persen' => $persen_kekerasan_fisik],
                                ['label' => 'Kekerasan Psikis', 'total' => $kekerasan_psikis, 'persen' => $persen_kekerasan_psikis],
                                ['label' => 'Kekerasan Seksual', 'total' => $kekerasan_seksual, 'persen' => $persen_kekerasan_seksual],
                                ['label' => 'Penelantaran', 'total' => $penelantaran, 'persen' => $persen_penelantaran],
                                ['label' => 'Eksploitasi', 'total' => $eksploitasi, 'persen' => $persen_eksploitasi],
                                ['label' => 'TPPO', 'total' => $tppo, 'persen' => $persen_tppo],
                            ];
                        @endphp

                        @foreach ($kategoriList as $kategori)
                            <div class="progress-line">
                                <div style="width: 100%;">
                                    <div class="progress-line-head">
                                        <span class="progress-line-label">{{ $kategori['label'] }}</span>
                                        <span class="progress-line-value">{{ $kategori['total'] }}</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $kategori['persen'] }}%"
                                            aria-valuenow="{{ $kategori['persen'] }}" aria-valuemin="0"
                                            aria-valuemax="100"></div>
                                    </div>
                                    <div class="progress-line-meta">{{ $kategori['persen'] }}% dari total</div>
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
                            <p class="mb-0 text-muted" style="font-size: .82rem;">5 laporan terakhir yang masuk</p>
                        </div>
                        <a href="{{ route('laporan_proses') }}" class="btn btn-instansi-soft btn-pill">
                            Lihat semua <i class="fa fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID Laporan</th>
                                        <th>Judul</th>
                                        <th>Kategori</th>
                                        <th>Status</th>
                                        <th>Tanggal</th>
                                        <th class="text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($laporan_terbaru as $laporan)
                                        <tr>
                                            <td class="fw-semibold">{{ $laporan->id_laporan }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($laporan->judul_tampil, 46) }}</td>
                                            <td class="text-muted">{{ $laporan->kategori }}</td>
                                            <td>@include('backend.components.status-pill', ['status' => $laporan->status])</td>
                                            <td class="text-muted">
                                                {{ optional($laporan->created_at)->format('d M Y') }}</td>
                                            <td class="text-right">
                                                <a href="{{ route('laporan.show', $laporan->id_laporan) }}"
                                                    class="btn btn-instansi-soft btn-pill btn-sm">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <div class="empty-icon"><i class="fa fa-folder-open"></i></div>
                                                    <p class="mb-0">Belum ada laporan pada tahun {{ $tahun_aktif }}.</p>
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

            // Token dibaca dari <body> agar override body.dinsos-admin ikut terbaca.
            var rootStyle = getComputedStyle(document.body);
            function token(name, fallback) {
                var value = (rootStyle.getPropertyValue(name) || '').trim();
                return value || fallback;
            }

            var palette = [
                token('--i-chart-1', '#006039'),
                token('--i-chart-2', '#0E7A46'),
                token('--i-chart-3', '#10B981'),
                token('--i-chart-4', '#34D399'),
                token('--i-chart-5', '#0EA5E9'),
                token('--i-chart-6', '#94A3B8')
            ];

            var series = [
                { label: 'Kekerasan Fisik', data: @json($data_kekerasan_fisik) },
                { label: 'Kekerasan Psikis', data: @json($data_kekerasan_psikis) },
                { label: 'Kekerasan Seksual', data: @json($data_kekerasan_seksual) },
                { label: 'Penelantaran', data: @json($data_penelantaran) },
                { label: 'Eksploitasi', data: @json($data_eksploitasi) },
                { label: 'TPPO', data: @json($data_tppo) }
            ];

            var datasets = series.map(function(item, index) {
                var color = palette[index] || palette[palette.length - 1];
                return {
                    label: item.label,
                    data: item.data,
                    borderColor: color,
                    backgroundColor: color + '26',
                    pointBackgroundColor: color,
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                };
            });

            var chart = null;

            function buildChart() {
                var canvas = document.getElementById('laporanAreaChart');
                if (!canvas || typeof Chart === 'undefined') return;

                if (chart) {
                    chart.destroy();
                }

                chart = new Chart(canvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: @json($bulan),
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8,
                                    padding: 16,
                                    font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" }
                                }
                            },
                            tooltip: {
                                backgroundColor: '#111827',
                                padding: 10,
                                cornerRadius: 10,
                                titleFont: { family: "'Plus Jakarta Sans', sans-serif" },
                                bodyFont: { family: "'Plus Jakarta Sans', sans-serif" }
                            }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: {
                                    color: '#94A3B8',
                                    font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" }
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: '#F1F3F5' },
                                border: { display: false },
                                ticks: {
                                    precision: 0,
                                    color: '#94A3B8',
                                    font: { size: 11, family: "'Plus Jakarta Sans', sans-serif" }
                                }
                            }
                        }
                    }
                });
            }

            // Angka KPI: hitung naik satu satuan (hemat, satu kali jalan)
            function runCountUp() {
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
            }

            function boot() {
                buildChart();
                runCountUp();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', boot);
            } else {
                boot();
            }

            // Filter tahun (perilaku lama dipertahankan)
            window.updateChart = function() {
                var tahun = document.getElementById('tahun-filter').value;
                window.location.href = "{{ route('dashboard') }}?tahun=" + tahun;
            };
        })();
    </script>
@endsection
