@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4 sa-dashboard">
    {{-- HEADER --}}
    <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-3">
        <div>
            <h1 class="fw-bolder text-dark mb-1" style="font-size: 1.9rem; letter-spacing: -0.03em;">Dashboard</h1>
            <p class="fw-semibold text-dark mb-1" style="font-size: 0.96rem;">
                Selamat datang kembali, {{ Auth::user()->nama ?? 'Superadmin' }}.
            </p>
            <p class="text-muted mb-0" style="font-size: 0.86rem;">
                Ringkasan kondisi pengelolaan akun, konfigurasi, keamanan, dan kesehatan sistem SENTRA.
            </p>
        </div>

        <div class="bg-white border rounded-pill px-3 py-2 d-flex align-items-center shadow-sm"
             style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); border-color: var(--border) !important;">
            <i class="fa fa-calendar me-2"></i>
            {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y · H:i') }}
        </div>
    </div>

    @include('backend.superadmin.components.alerts')

    @php
        $levelMap = [
            'normal'   => ['label' => 'Normal', 'class' => 'badge-soft-green'],
            'warning'  => ['label' => 'Perhatian', 'class' => 'badge-soft-amber'],
            'critical' => ['label' => 'Kritis', 'class' => 'badge-soft-rose'],
        ];
        $persenAktif = $totalAkun > 0 ? (int) round(($totalAkunAktif / $totalAkun) * 100) : 0;
    @endphp

    {{-- 1. SUMMARY CARDS --}}
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="card sa-card h-100">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div>
                        <div class="sa-label">Total Akun</div>
                        <div class="sa-number" data-countup data-target="{{ (int) $totalAkun }}">{{ number_format($totalAkun) }}</div>
                        <div class="sa-hint">
                            {{ number_format($newUsersThisWeek) }} baru minggu ini · {{ number_format($newUsersToday) }} hari ini
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <a href="{{ route('superadmin.akun.index') }}" wire:navigate
                           class="sa-review-link" title="Tinjau Akun" aria-label="Tinjau Akun">
                            <svg class="sa-lucide sa-lucide-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>
                        <div class="sa-icon sa-icon-info">
                            <svg class="sa-lucide sa-lucide-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="card sa-card h-100">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div>
                        <div class="sa-label">Akun Aktif</div>
                        <div class="sa-number" data-countup data-target="{{ (int) $totalAkunAktif }}">{{ number_format($totalAkunAktif) }}</div>
                        <div class="sa-hint">{{ $persenAktif }}% dari total akun</div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <a href="{{ route('superadmin.akun.index', ['status' => 'aktif']) }}" wire:navigate
                           class="sa-review-link" title="Tinjau Akun" aria-label="Tinjau Akun">
                            <svg class="sa-lucide sa-lucide-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>
                        <div class="sa-icon sa-icon-success">
                            <svg class="sa-lucide sa-lucide-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="card sa-card h-100">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div>
                        <div class="sa-label">Akun Nonaktif</div>
                        <div class="sa-number" data-countup data-target="{{ (int) $totalNonAktif }}">{{ number_format($totalNonAktif) }}</div>
                        <div class="sa-hint {{ $totalNonAktif > 0 ? 'text-warning' : '' }}">
                            {{ $totalNonAktif > 0 ? 'Perlu ditinjau' : 'Tidak ada akun nonaktif' }}
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <a href="{{ route('superadmin.akun.index', ['status' => 'nonaktif']) }}" wire:navigate
                           class="sa-review-link" title="Tinjau Akun" aria-label="Tinjau Akun">
                            <svg class="sa-lucide sa-lucide-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>
                        <div class="sa-icon sa-icon-warning">
                            <svg class="sa-lucide sa-lucide-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <line x1="17" y1="8" x2="22" y2="13"></line>
                                <line x1="22" y1="8" x2="17" y2="13"></line>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-12">
            <div class="card sa-card h-100">
                <div class="card-body d-flex align-items-start justify-content-between">
                    <div>
                        <div class="sa-label">Total Role</div>
                        <div class="sa-number" data-countup data-target="{{ (int) $totalRoles }}">{{ number_format($totalRoles) }}</div>
                        <div class="sa-hint">{{ number_format($systemRoles) }} role sistem</div>
                    </div>
                    <div class="d-flex flex-column align-items-end gap-2">
                        <a href="{{ route('superadmin.roles.index') }}" wire:navigate
                           class="sa-review-link" title="Tinjau Role" aria-label="Tinjau Role">
                            <svg class="sa-lucide sa-lucide-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </a>
                        <div class="sa-icon sa-icon-primary">
                            <svg class="sa-lucide sa-lucide-lg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"></path>
                                <circle cx="16.5" cy="7.5" r="0.5" fill="currentColor"></circle>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. DISTRIBUSI AKUN BERDASARKAN ROLE --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card sa-card h-100">
                <div class="card-header sa-card-header d-flex align-items-center justify-content-between">
                    <h5 class="sa-card-title sa-panel-title">
                        <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 12a9 9 0 1 1-9-9"></path>
                            <path d="M21 3v6h-6"></path>
                            <path d="M12 12 16.5 6"></path>
                        </svg>
                        Distribusi Akun berdasarkan Role
                    </h5>
                    <span class="sa-panel-caption">{{ number_format($totalAkun) }} akun terdaftar</span>
                </div>
                <div class="card-body">
                    @if ($totalAkun > 0)
                        @php
                            $legendTotal = max($totalAkun, 1);
                        @endphp
                        <div class="row g-4 align-items-center">
                            <div class="col-lg-5">
                                <div class="sa-donut-3d" role="img" aria-label="Grafik distribusi akun berdasarkan role">
                                    <div id="saRoleDonutDepth" class="sa-donut-depth" aria-hidden="true"></div>
                                    <div id="saRoleDonut" class="sa-donut-front"></div>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="sa-legend-panel">
                                    <div class="sa-legend-title">
                                        <span>Komposisi akun per role</span>
                                        <span class="sa-legend-title-total">{{ number_format($totalAkun) }} akun</span>
                                    </div>
                                    <ul class="sa-role-legend">
                                        @foreach ($roleDistributionLabels as $index => $label)
                                            @php
                                                $legendValue = (int) $roleDistributionValues[$index];
                                                $legendColor = $roleChartColors[$index % count($roleChartColors)];
                                                $legendPercent = (int) round(($legendValue / $legendTotal) * 100);
                                            @endphp
                                            <li>
                                                <span class="sa-legend-dot" style="background: {{ $legendColor }};"></span>
                                                <span class="sa-legend-label">{{ $label }}</span>
                                                <strong>{{ number_format($legendValue) }}</strong>
                                                <span class="sa-legend-percent">{{ $legendPercent }}%</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="sa-chart-empty">
                            <svg class="sa-lucide sa-lucide-xl" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M21 12a9 9 0 1 1-9-9"></path>
                                <path d="M21 3v6h-6"></path>
                            </svg>
                            Belum ada akun untuk divisualisasikan.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- 3 & 4. SYSTEM HEALTH + SECURITY ACTIVITY --}}
    <div class="row g-3 mb-4">
        @php
                $overviewLevelMap = [
                    'normal'   => ['label' => 'Normal', 'class' => 'badge-soft-green', 'badge' => 'sa-badge-normal', 'icon' => 'sa-level-normal'],
                    'warning'  => ['label' => 'Perhatian', 'class' => 'badge-soft-amber', 'badge' => 'sa-badge-warning', 'icon' => 'sa-level-warning'],
                    'critical' => ['label' => 'Kritis', 'class' => 'badge-soft-rose', 'badge' => 'sa-badge-critical', 'icon' => 'sa-level-critical'],
                    'unknown'  => ['label' => 'Belum tersedia', 'class' => 'badge-soft-gray', 'badge' => 'sa-badge-unknown', 'icon' => 'sa-level-unknown'],
                ];
                $storageLevel = $overviewLevelMap[$healthOverview['storage_level']] ?? $overviewLevelMap['unknown'];
                $tokensAvailable = $securityActivityOverview['expiring_tokens'] !== null;
            @endphp

            <div class="row g-3">
                {{-- SYSTEM HEALTH OVERVIEW --}}
                <div class="col-12 col-lg-6">
                    <div class="card sa-card sa-health-panel h-100">
                        <div class="card-header sa-card-header d-flex align-items-center justify-content-between">
                            <h5 class="sa-card-title sa-panel-title">
                                <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                                    <path d="M3.22 12H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"></path>
                                </svg>
                                System Health Overview
                            </h5>
                            <span class="sa-panel-caption">Kondisi sistem saat ini</span>
                        </div>
                        <div class="card-body">
                            <div class="sa-health-grid">
                                <div class="sa-health-item">
                                    <div class="sa-health-item-top">
                                        <span class="sa-health-icon {{ $healthOverview['db_status'] === 'Connected' ? 'sa-health-icon-green' : 'sa-health-icon-rose' }}">
                                            <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                                                <path d="M3 5v14c0 1.66 4.03 3 9 3s9-1.34 9-3V5"></path>
                                                <path d="M3 12c0 1.66 4.03 3 9 3s9-1.34 9-3"></path>
                                            </svg>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="sa-health-label">Database Status</div>
                                            <span class="badge-pill-soft {{ $healthOverview['db_status_class'] }}">{{ $healthOverview['db_status'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="sa-health-item">
                                    <div class="sa-health-item-top">
                                        <span class="sa-health-icon sa-health-icon-amber">
                                            <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                                            </svg>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="sa-health-label">System Uptime</div>
                                            <div class="sa-health-value">{{ $healthOverview['uptime'] }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sa-health-item sa-health-item-wide">
                                    <div class="sa-health-item-top">
                                        <span class="sa-health-icon {{ $storageLevel['icon'] }}">
                                            <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <line x1="22" y1="12" x2="2" y2="12"></line>
                                                <path d="M5.45 5.11 2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path>
                                                <line x1="6" y1="16" x2="6.01" y2="16"></line>
                                                <line x1="10" y1="16" x2="10.01" y2="16"></line>
                                            </svg>
                                        </span>
                                        <div class="min-w-0 flex-grow-1">
                                            <div class="sa-health-label">Storage Usage</div>
                                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                                <span class="sa-health-value">{{ $healthOverview['storage_used'] }}</span>
                                                <span class="badge-pill-soft {{ $storageLevel['badge'] }}">{{ $healthOverview['storage_percent_label'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress sa-health-progress" role="progressbar"
                                         aria-valuenow="{{ (int) $healthOverview['storage_percent'] }}"
                                         aria-valuemin="0" aria-valuemax="100"
                                         aria-label="Penggunaan kapasitas storage">
                                        <div class="progress-bar {{ $healthOverview['storage_level'] === 'critical' ? 'bg-danger' : ($healthOverview['storage_level'] === 'warning' ? 'bg-warning' : ($healthOverview['storage_level'] === 'normal' ? 'bg-success' : 'bg-secondary')) }}"
                                             style="width: {{ (int) $healthOverview['storage_percent'] }}%;"></div>
                                    </div>
                                    <div class="sa-health-meta">
                                        <span>Terpakai</span>
                                        <span>Kapasitas: {{ $healthOverview['storage_capacity'] }}</span>
                                    </div>
                                </div>

                                <div class="sa-health-item sa-health-item-wide">
                                    <div class="sa-health-item-top">
                                        <span class="sa-health-icon sa-health-icon-green">
                                            <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <rect x="2" y="4" width="20" height="5" rx="1"></rect>
                                                <path d="M4 9v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9"></path>
                                                <line x1="10" y1="13" x2="14" y2="13"></line>
                                            </svg>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="sa-health-label">Last Backup</div>
                                            <div class="sa-health-value {{ $healthOverview['last_backup'] === 'Belum tersedia' ? 'text-muted' : '' }}" style="{{ $healthOverview['last_backup'] === 'Belum tersedia' ? 'font-size: 0.86rem; font-weight: 600;' : '' }}">
                                                {{ $healthOverview['last_backup'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECURITY ACTIVITY OVERVIEW --}}
                <div class="col-12 col-lg-6">
                    <div class="card sa-card sa-security-panel h-100">
                        <div class="card-header sa-card-header d-flex align-items-center justify-content-between">
                            <h5 class="sa-card-title sa-panel-title">
                                <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                                    <path d="m9 12 2 2 4-4"></path>
                                </svg>
                                Security Activity Overview
                            </h5>
                            <span class="sa-panel-caption">Aktivitas &amp; akses sistem</span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6 mb-2">
                                    @php $failedLevel = $overviewLevelMap[$securityActivityOverview['failed_logins_level']] ?? $overviewLevelMap['unknown']; @endphp
                                    <div class="sa-security-metric">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="sa-security-label">Failed Login Attempts</span>
                                            <span class="sa-security-icon {{ $failedLevel['icon'] }}">
                                                <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
                                                    <path d="M12 8v4"></path><path d="M12 16h.01"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="sa-security-value">{{ number_format($securityActivityOverview['failed_logins_24h']) }}</div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="sa-security-sub">24 jam terakhir</span>
                                            <span class="badge-pill-soft {{ $failedLevel['class'] }}">{{ $failedLevel['label'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6 mb-2">
                                    @php $deactivatedLevel = $overviewLevelMap[$securityActivityOverview['deactivated_accounts_level']] ?? $overviewLevelMap['unknown']; @endphp
                                    <div class="sa-security-metric">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="sa-security-label">Deactivated Accounts</span>
                                            <span class="sa-security-icon {{ $deactivatedLevel['icon'] }}">
                                                <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <line x1="17" y1="8" x2="22" y2="13"></line>
                                                    <line x1="22" y1="8" x2="17" y2="13"></line>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="sa-security-value">{{ number_format($securityActivityOverview['deactivated_accounts']) }}</div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="sa-security-sub">Total akun nonaktif</span>
                                            <span class="badge-pill-soft {{ $deactivatedLevel['class'] }}">{{ $deactivatedLevel['label'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    @php $tokenLevel = $tokensAvailable ? ($overviewLevelMap[$securityActivityOverview['expiring_tokens_level']] ?? $overviewLevelMap['unknown']) : $overviewLevelMap['unknown']; @endphp
                                    <div class="sa-security-metric">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="sa-security-label">Expiring Tokens</span>
                                            <span class="sa-security-icon {{ $tokenLevel['icon'] }}">
                                                <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"></path>
                                                    <circle cx="16.5" cy="7.5" r="0.5" fill="currentColor"></circle>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="sa-security-value {{ $tokensAvailable ? '' : 'sa-security-value-text text-muted' }}">
                                            {{ $tokensAvailable ? number_format($securityActivityOverview['expiring_tokens']) : 'Belum tersedia' }}
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="sa-security-sub">7 hari ke depan</span>
                                            <span class="badge-pill-soft {{ $tokenLevel['class'] }}">{{ $tokenLevel['label'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    @php $sessionLevel = $overviewLevelMap[$securityActivityOverview['active_sessions_level']] ?? $overviewLevelMap['unknown']; @endphp
                                    <div class="sa-security-metric">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <span class="sa-security-label">Active Sessions</span>
                                            <span class="sa-security-icon {{ $sessionLevel['icon'] }}">
                                                <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                                    <circle cx="9" cy="7" r="4"></circle>
                                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="sa-security-value">{{ number_format($securityActivityOverview['active_sessions']) }}</div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="sa-security-sub">15 menit terakhir</span>
                                            <span class="badge-pill-soft {{ $sessionLevel['class'] }}">{{ $sessionLevel['label'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="sa-login-list">
                                <div class="sa-login-list-title">Recent Login Activity</div>
                                @forelse ($securityActivityOverview['recent_logins'] as $login)
                                    <div class="sa-login-item">
                                        <span class="sa-login-icon">
                                            <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                                <polyline points="10 17 15 12 10 7"></polyline>
                                                <line x1="15" y1="12" x2="3" y2="12"></line>
                                            </svg>
                                        </span>
                                        <div class="sa-login-main">
                                            <div class="sa-login-name">{{ $login['actor'] }}</div>
                                            <div class="sa-login-meta">{{ $login['identity'] }} · {{ $login['time'] }}</div>
                                        </div>
                                        <span class="badge-pill-soft {{ $login['status_class'] }}">{{ $login['status'] }}</span>
                                    </div>
                                @empty
                                    <div class="sa-login-empty">Belum tersedia</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {{-- 5. ROLE & PERMISSION OVERVIEW --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card sa-card h-100">
                <div class="card-header sa-card-header d-flex align-items-center justify-content-between">
                    <h5 class="sa-card-title sa-panel-title">
                        <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M2.586 17.414A2 2 0 0 0 2 18.828V21a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h1a1 1 0 0 0 1-1v-1a1 1 0 0 1 1-1h.172a2 2 0 0 0 1.414-.586l.814-.814a6.5 6.5 0 1 0-4-4z"></path>
                            <circle cx="16.5" cy="7.5" r="0.5" fill="currentColor"></circle>
                        </svg>
                        Role &amp; Permission Overview
                    </h5>
                    <a href="{{ route('superadmin.roles.index') }}" wire:navigate class="btn btn-sm btn-oripio-light btn-pill">Kelola</a>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="sa-mini">
                                <div class="sa-mini-number">{{ number_format($totalRoles) }}</div>
                                <div class="sa-mini-label">Total Role</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="sa-mini">
                                <div class="sa-mini-number">{{ number_format($totalPermissions) }}</div>
                                <div class="sa-mini-label">Total Permission</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="sa-mini">
                                <div class="sa-mini-number">{{ number_format($systemRoles) }}</div>
                                <div class="sa-mini-label">Role Sistem</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 6. CONFIGURATION STATUS --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card sa-card h-100">
                <div class="card-header sa-card-header d-flex align-items-center justify-content-between">
                    <h5 class="sa-card-title sa-panel-title">
                        <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M4 21v-7"></path><path d="M4 10V3"></path><path d="M12 21v-9"></path><path d="M12 8V3"></path><path d="M20 21v-5"></path><path d="M20 12V3"></path>
                            <path d="M1 14h6"></path><path d="M9 8h6"></path><path d="M17 16h6"></path>
                        </svg>
                        Configuration Status
                    </h5>
                    <a href="{{ route('superadmin.settings.index') }}" wire:navigate class="btn btn-sm btn-oripio-light btn-pill">Atur</a>
                </div>
                <div class="card-body">
                    @php
                        $configChecklist = [
                            [
                                'ok' => $configStatus['is_complete'],
                                'label' => 'Data identitas & operasional',
                                'sub' => $configStatus['completed'] . ' dari ' . $configStatus['total'] . ' pengaturan penting terisi',
                                'badge' => $configStatus['completion_badge'],
                                'class' => $configStatus['completion_badge_class'],
                            ],
                            [
                                'ok' => $configStatus['logo_status_class'] === 'badge-soft-green',
                                'label' => 'Logo & branding',
                                'sub' => $configStatus['logo_status'],
                                'badge' => $configStatus['logo_badge'],
                                'class' => $configStatus['logo_status_class'],
                            ],
                            [
                                'ok' => $configStatus['contact_status_class'] === 'badge-soft-green',
                                'label' => 'Kontak darurat',
                                'sub' => $configStatus['contact_status'],
                                'badge' => $configStatus['contact_badge'],
                                'class' => $configStatus['contact_status_class'],
                            ],
                        ];
                    @endphp

                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="sa-config-progress">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="sa-metric-label mb-0">Kelengkapan konfigurasi</span>
                                    <span class="badge-pill-soft {{ $configStatus['completion_badge_class'] }}" style="font-size: 0.68rem;">
                                        {{ $configStatus['completion_badge'] }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-baseline gap-2 mb-2">
                                    <span class="sa-config-percent">{{ $configStatus['completion_percent'] }}%</span>
                                    <span class="text-muted" style="font-size: 0.76rem;">{{ $configStatus['completed'] }}/{{ $configStatus['total'] }} item</span>
                                </div>
                                <div class="progress" style="height: 8px; border-radius: 999px;">
                                    <div class="progress-bar {{ $configStatus['is_complete'] ? 'bg-success' : 'bg-warning' }}"
                                         role="progressbar"
                                         style="width: {{ $configStatus['completion_percent'] }}%; border-radius: 999px;"
                                         aria-valuenow="{{ $configStatus['completion_percent'] }}"
                                         aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="sa-checklist">
                                @foreach ($configChecklist as $item)
                                    <div class="sa-check-item">
                                        <span class="sa-check-icon {{ $item['ok'] ? 'sa-check-ok' : 'sa-check-warn' }}">
                                            @if ($item['ok'])
                                                <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                            @else
                                                <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path></svg>
                                            @endif
                                        </span>
                                        <div class="sa-check-main">
                                            <div class="sa-check-title">{{ $item['label'] }}</div>
                                            <div class="sa-check-sub">{{ $item['sub'] }}</div>
                                        </div>
                                        <span class="badge-pill-soft {{ $item['class'] }}">{{ $item['badge'] }}</span>
                                    </div>
                                @endforeach
                            </div>

                            @if ($configStatus['warnings'] !== [])
                                <div class="sa-note mt-3">
                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.8rem;">
                                        {{ count($configStatus['warnings']) }} konfigurasi perlu perhatian
                                    </div>
                                    <ul class="mb-0 ps-3 text-muted" style="font-size: 0.76rem; line-height: 1.6;">
                                        @foreach (array_slice($configStatus['warnings'], 0, 4) as $warning)
                                            <li>{{ $warning['message'] }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @else
                                <div class="sa-note badge-soft-green mt-3" style="font-size: 0.8rem;">
                                    Seluruh konfigurasi penting sudah terisi.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 7. RECENT SYSTEM ACTIVITIES --}}
    <div class="row g-3">
        <div class="col-12">
            <div class="card sa-card">
                <div class="card-header sa-card-header d-flex align-items-center justify-content-between">
                    <h5 class="sa-card-title sa-panel-title">
                        <svg class="sa-lucide" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path>
                            <path d="m3 6 1 1 2-2"></path><path d="m3 12 1 1 2-2"></path><path d="m3 18 1 1 2-2"></path>
                        </svg>
                        Recent System Activities
                    </h5>
                    <a href="{{ route('superadmin.audit.index') }}" wire:navigate class="btn btn-sm btn-oripio-light btn-pill">
                        Lihat semua
                        <svg class="sa-lucide sa-lucide-sm ms-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
                <div class="card-body sa-timeline-body">
                    @forelse ($recentActivities as $activity)
                        <div class="sa-timeline-item">
                            <div class="sa-timeline-rail">
                                <span class="sa-timeline-dot {{ $activity['bg_color'] ?? 'badge-soft-gray' }}">
                                    <svg class="sa-lucide sa-lucide-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="sa-timeline-content">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                                    <span class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $activity['title'] }}</span>
                                    <span class="badge-pill-soft {{ $activity['status_class'] }}" style="font-size: 0.66rem; padding: 2px 9px;">{{ $activity['status'] }}</span>
                                </div>
                                <div class="text-muted text-truncate" style="font-size: 0.8rem;">{{ $activity['subtitle'] }}</div>
                                <div class="d-flex flex-wrap align-items-center text-muted mt-1" style="font-size: 0.72rem; gap: 12px;">
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <svg class="sa-lucide sa-lucide-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                                        {{ $activity['actor'] }}
                                    </span>
                                    <span class="d-none d-sm-inline-flex align-items-center gap-1">
                                        <svg class="sa-lucide sa-lucide-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"></rect><path d="M3 10h18"></path></svg>
                                        {{ $activity['instansi'] }}
                                    </span>
                                    <span class="d-inline-flex align-items-center gap-1">
                                        <svg class="sa-lucide sa-lucide-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                                        {{ $activity['date'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5" style="font-size: 0.86rem;">
                            <svg class="sa-lucide sa-lucide-xl mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12a9 9 0 1 0 3-6.7L3 8"></path><path d="M3 3v5h5"></path></svg>
                            Belum ada aktivitas sistem tercatat.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <p class="text-muted mt-3 mb-0" style="font-size: 0.74rem;">
        Dashboard ini hanya menampilkan metadata sistem. Isi laporan, data kasus, dan komunikasi penanganan tidak tersedia di sini.
    </p>
</div>
@endsection

@push('scripts')
<style>
    .sa-dashboard .sa-card {
        border: 1px solid var(--border) !important;
        border-radius: 18px !important;
        box-shadow: var(--shadow) !important;
        background: var(--surface) !important;
    }

    .sa-dashboard .sa-card-header {
        border-bottom: 1px solid var(--border) !important;
        background: transparent !important;
        padding: 16px 20px 12px !important;
    }

    .sa-dashboard .sa-card-title {
        margin: 0 !important;
        font-size: 0.98rem !important;
        font-weight: 700 !important;
        color: var(--text) !important;
    }

    .sa-dashboard .sa-label {
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .sa-dashboard .sa-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.1;
        color: var(--text);
        letter-spacing: -0.035em;
    }

    .sa-dashboard .sa-hint {
        color: var(--text-muted);
        font-size: 0.74rem;
        margin-top: 6px;
    }

    .sa-dashboard .sa-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        flex-shrink: 0;
        border: 1px solid var(--border);
        box-shadow: var(--shadow), inset 0 1px 0 var(--surface);
        transform: translateY(-1px);
    }

    .sa-dashboard .sa-icon-info {
        background: var(--info-bg);
        color: var(--info-fg);
    }

    .sa-dashboard .sa-icon-success {
        background: var(--success-bg);
        color: var(--success-fg);
    }

    .sa-dashboard .sa-icon-warning {
        background: var(--warning-bg);
        color: var(--warning-fg);
    }

    .sa-dashboard .sa-icon-primary {
        background: var(--primary-soft);
        color: var(--primary-ink);
    }

    .sa-dashboard .sa-review-link {
        width: 32px;
        height: 32px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        color: var(--primary-ink);
        background: var(--primary-soft);
        border: 1px solid var(--border);
        text-decoration: none;
        transition: background-color 0.18s ease, color 0.18s ease, border-color 0.18s ease,
                    transform 0.18s ease, box-shadow 0.18s ease;
    }

    .sa-dashboard .sa-review-link:hover {
        background: var(--primary);
        border-color: var(--primary);
        color: var(--surface);
        transform: translateY(-1px);
        box-shadow: 0 6px 14px var(--primary-ring);
    }

    .sa-dashboard .sa-review-link:focus-visible {
        outline: 2px solid var(--primary);
        outline-offset: 2px;
    }

    .sa-dashboard .sa-mini {
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 14px;
    }

    .sa-dashboard .sa-mini-number {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1.1;
    }

    .sa-dashboard .sa-mini-label {
        font-size: 0.76rem;
        color: var(--text-muted);
        margin-top: 4px;
    }

    .sa-dashboard .sa-metric-label {
        display: block;
        color: var(--text-muted);
        font-size: 0.76rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .sa-dashboard .sa-note {
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px 14px;
    }

    .sa-dashboard .sa-config-progress {
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 14px;
        height: 100%;
    }

    .sa-dashboard .sa-config-percent {
        font-size: 1.8rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1.1;
        letter-spacing: -0.03em;
    }

    .sa-dashboard .sa-checklist {
        display: grid;
        gap: 8px;
    }

    .sa-dashboard .sa-check-item {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 10px 12px;
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 12px;
        min-width: 0;
    }

    .sa-dashboard .sa-check-icon {
        width: 28px;
        height: 28px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid var(--border);
    }

    .sa-dashboard .sa-check-ok {
        background: var(--success-bg);
        color: var(--success-fg);
    }

    .sa-dashboard .sa-check-warn {
        background: var(--warning-bg);
        color: var(--warning-fg);
    }

    .sa-dashboard .sa-check-main {
        flex: 1;
        min-width: 0;
    }

    .sa-dashboard .sa-check-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--text);
    }

    .sa-dashboard .sa-check-sub {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .sa-dashboard .sa-timeline-body {
        padding-top: 18px;
        padding-bottom: 8px;
    }

    .sa-dashboard .sa-timeline-item {
        position: relative;
        display: flex;
        gap: 14px;
        padding-bottom: 18px;
    }

    .sa-dashboard .sa-timeline-item:not(:last-child)::before {
        content: '';
        position: absolute;
        left: 17px;
        top: 36px;
        bottom: 0;
        width: 1px;
        background: var(--border);
    }

    .sa-dashboard .sa-timeline-rail {
        position: relative;
        z-index: 1;
        flex-shrink: 0;
    }

    .sa-dashboard .sa-timeline-dot {
        width: 36px;
        height: 36px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
    }

    .sa-dashboard .sa-timeline-content {
        flex: 1;
        min-width: 0;
        padding-top: 2px;
    }

    .sa-dashboard .sa-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
    }

    .sa-dashboard .sa-section-subtitle {
        font-size: 0.8rem;
        color: var(--text-muted);
    }

    .sa-dashboard .sa-donut-3d {
        position: relative;
        height: 250px;
        margin: 0 auto;
        max-width: 320px;
    }

    .sa-dashboard .sa-donut-depth,
    .sa-dashboard .sa-donut-front {
        position: absolute;
        inset: 0;
    }

    .sa-dashboard .sa-donut-depth {
        z-index: 1;
        pointer-events: none;
        transform: translateY(8px);
        opacity: 0.85;
        filter: saturate(0.95);
    }

    .sa-dashboard .sa-donut-front {
        z-index: 2;
        filter: drop-shadow(0 10px 14px var(--shadow-ink));
    }

    .sa-dashboard .sa-legend-panel {
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 14px 16px 8px;
        height: 100%;
    }

    .sa-dashboard .sa-role-legend {
        list-style: none;
        margin: 0;
        padding: 0;
        display: grid;
        gap: 0;
    }

    .sa-dashboard .sa-role-legend li {
        display: grid;
        grid-template-columns: 10px minmax(0, 1fr) auto 52px;
        align-items: center;
        gap: 10px;
        padding: 9px 0;
        border-bottom: 1px solid var(--border);
        font-size: 0.8rem;
        color: var(--text-muted);
        min-width: 0;
    }

    .sa-dashboard .sa-role-legend li:last-child { border-bottom: 0; }

    .sa-dashboard .sa-role-legend strong {
        margin-left: 0;
        color: var(--text);
        font-size: 0.85rem;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .sa-dashboard .sa-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
        box-shadow: 0 0 0 3px var(--surface);
    }

    .sa-dashboard .sa-legend-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: var(--text);
        font-weight: 600;
    }

    .sa-dashboard .sa-legend-percent {
        min-width: 52px;
        padding: 2px 7px;
        border: 1px solid var(--border);
        border-radius: 999px;
        background: var(--surface);
        color: var(--text-muted);
        font-size: 0.7rem;
        font-weight: 700;
        text-align: center;
        font-variant-numeric: tabular-nums;
    }

    .sa-dashboard .sa-panel-title {
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .sa-dashboard .sa-lucide {
        width: 17px;
        height: 17px;
        stroke-width: 1.9;
        flex-shrink: 0;
    }

    .sa-dashboard .sa-lucide-lg { width: 21px; height: 21px; }
    .sa-dashboard .sa-lucide-xl { width: 28px; height: 28px; }
    .sa-dashboard .sa-lucide-sm { width: 14px; height: 14px; }
    .sa-dashboard .sa-lucide-xs { width: 12px; height: 12px; }
    .sa-dashboard .sa-icon .sa-lucide { width: 21px; height: 21px; }

    .sa-dashboard .sa-legend-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 6px;
        color: var(--text);
        font-size: 0.78rem;
        font-weight: 700;
    }

    .sa-dashboard .sa-legend-title-total {
        color: var(--text-muted);
        font-size: 0.7rem;
        font-weight: 600;
    }

    .sa-dashboard .sa-panel-caption {
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .sa-dashboard .sa-health-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
    }

    .sa-dashboard .sa-health-item {
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 13px 14px;
        min-width: 0;
    }

    .sa-dashboard .sa-health-item-wide { grid-column: 1 / -1; }

    .sa-dashboard .sa-health-item-top {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }

    .sa-dashboard .sa-health-icon,
    .sa-dashboard .sa-security-icon,
    .sa-dashboard .sa-login-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        border: 1px solid var(--border);
    }

    .sa-dashboard .sa-health-icon-green,
    .sa-dashboard .sa-level-normal,
    .sa-dashboard .sa-login-icon {
        background: var(--primary-soft);
        color: var(--primary-ink);
    }

    .sa-dashboard .sa-health-icon-amber,
    .sa-dashboard .sa-level-warning {
        background: var(--warning-bg);
        color: var(--warning-fg);
    }

    .sa-dashboard .sa-health-icon-rose,
    .sa-dashboard .sa-level-critical {
        background: var(--danger-bg);
        color: var(--danger-fg);
    }

    .sa-dashboard .sa-level-unknown {
        background: var(--surface-muted);
        color: var(--text-muted);
    }

    .sa-dashboard .sa-health-label,
    .sa-dashboard .sa-security-label {
        display: block;
        font-size: 0.74rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 5px;
    }

    .sa-dashboard .sa-health-value {
        font-size: 1rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1.25;
    }

    .sa-dashboard .sa-badge-normal {
        background: var(--success-bg);
        color: var(--success-fg);
    }

    .sa-dashboard .sa-badge-warning {
        background: var(--warning-bg);
        color: var(--warning-fg);
    }

    .sa-dashboard .sa-badge-critical {
        background: var(--danger-bg);
        color: var(--danger-fg);
    }

    .sa-dashboard .sa-badge-unknown {
        background: var(--surface-muted);
        color: var(--text-muted);
    }

    .sa-dashboard .sa-health-progress {
        height: 8px;
        border-radius: 999px;
        margin-top: 12px;
        overflow: hidden;
    }

    .sa-dashboard .sa-health-progress .progress-bar {
        border-radius: 999px;
    }

    .sa-dashboard .sa-health-meta {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-top: 6px;
        font-size: 0.7rem;
        color: var(--text-muted);
    }

    .sa-dashboard .sa-security-metric {
        height: 100%;
        background: var(--surface-muted);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 12px 13px;
        min-width: 0;
    }

    .sa-dashboard .sa-security-value {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--text);
        line-height: 1.2;
        margin: 2px 0 6px;
        overflow-wrap: anywhere;
    }

    .sa-dashboard .sa-security-value-text {
        font-size: 0.86rem;
        font-weight: 600;
    }

    .sa-dashboard .sa-security-sub {
        font-size: 0.68rem;
        color: var(--text-muted);
    }

    .sa-dashboard .sa-login-list {
        border-top: 1px solid var(--border);
        padding-top: 12px;
        margin-top: 4px;
    }

    .sa-dashboard .sa-login-list-title {
        font-size: 0.76rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 4px;
    }

    .sa-dashboard .sa-login-item {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
    }

    .sa-dashboard .sa-login-item:last-child { border-bottom: none; }

    .sa-dashboard .sa-login-main {
        flex: 1;
        min-width: 0;
    }

    .sa-dashboard .sa-login-name,
    .sa-dashboard .sa-login-meta {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sa-dashboard .sa-login-name {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--text);
    }

    .sa-dashboard .sa-login-meta {
        font-size: 0.68rem;
        color: var(--text-muted);
        margin-top: 2px;
    }

    .sa-dashboard .sa-login-empty {
        padding: 14px 10px;
        text-align: center;
        font-size: 0.78rem;
        color: var(--text-muted);
        border: 1px dashed var(--border);
        border-radius: 12px;
        background: var(--surface-muted);
    }

    .sa-dashboard .sa-chart-empty {
        min-height: 240px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        border: 1px dashed var(--border);
        border-radius: 14px;
        background: var(--surface-muted);
        color: var(--text-muted);
        font-size: 0.82rem;
    }

    @media (max-width: 575.98px) {
        .sa-dashboard .sa-number { font-size: 1.75rem; }
        .sa-dashboard .sa-donut-3d { height: 230px; }
        .sa-dashboard .sa-timeline-body { padding-left: 14px; padding-right: 14px; }
        .sa-dashboard .sa-health-grid { grid-template-columns: 1fr; }
        .sa-dashboard .sa-health-item-wide { grid-column: auto; }
        .sa-dashboard .sa-panel-caption { display: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        window.saDashboardCharts = window.saDashboardCharts || {};

        function cssVar(name) {
            return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        }

        var roleLabels = @json($roleDistributionLabels);
        var roleValues = @json($roleDistributionValues);
        var chartPalette = @json($roleChartColors);
        var depthPalette = @json($roleDepthColors);
        var roleColors = roleLabels.map(function (label, index) {
            return chartPalette[index % chartPalette.length];
        });
        var depthColors = roleLabels.map(function (label, index) {
            return depthPalette[index % depthPalette.length];
        });
        var roleTotal = roleValues.reduce(function (sum, value) { return sum + value; }, 0);

        function destroyChart(key) {
            if (!window.saDashboardCharts[key]) return;
            try { window.saDashboardCharts[key].destroy(); } catch (e) {}
            window.saDashboardCharts[key] = null;
        }

        function mountChart(key, element, options) {
            if (!element || element.getAttribute('data-sa-chart-ready') === '1') return;
            destroyChart(key);
            var chart = new ApexCharts(element, options);
            chart.render();
            window.saDashboardCharts[key] = chart;
            element.setAttribute('data-sa-chart-ready', '1');
        }

        function initCharts() {
            if (typeof ApexCharts === 'undefined') return;

            var front = document.getElementById('saRoleDonut');
            var depth = document.getElementById('saRoleDonutDepth');
            var chartHeight = front ? (front.clientHeight || (window.innerWidth < 576 ? 230 : 250)) : 250;

            if (!front) destroyChart('roleFront');
            if (!depth) destroyChart('roleDepth');

            if (front && depth && roleTotal > 0) {
                mountChart('roleDepth', depth, {
                    chart: {
                        type: 'donut',
                        height: chartHeight,
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        animations: { enabled: false }
                    },
                    series: roleValues,
                    labels: roleLabels,
                    colors: depthColors,
                    stroke: { width: 0 },
                    fill: { type: 'solid' },
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    tooltip: { enabled: false },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '68%',
                                labels: { show: false }
                            }
                        }
                    }
                });

                mountChart('roleFront', front, {
                    chart: {
                        type: 'donut',
                        height: chartHeight,
                        toolbar: { show: false },
                        fontFamily: 'inherit',
                        animations: { enabled: true, speed: 650 }
                    },
                    series: roleValues,
                    labels: roleLabels,
                    colors: roleColors,
                    stroke: { width: 3, colors: [cssVar('--surface')] },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shade: 'dark',
                            type: 'vertical',
                            shadeIntensity: 0.12,
                            opacityFrom: 1,
                            opacityTo: 1,
                            stops: [0, 100]
                        }
                    },
                    legend: { show: false },
                    dataLabels: {
                        enabled: true,
                        formatter: function (value) {
                            return value >= 4 ? Math.round(value) + '%' : '';
                        },
                        dropShadow: { enabled: false },
                        style: {
                            fontSize: '11px',
                            fontWeight: 700,
                            colors: roleColors.map(function (color, index) {
                                return index < 3 ? cssVar('--surface') : cssVar('--primary-ink');
                            })
                        }
                    },
                    tooltip: {
                        enabled: true,
                        y: {
                            formatter: function (value) {
                                var percent = roleTotal > 0 ? Math.round((value / roleTotal) * 100) : 0;
                                return value + ' akun · ' + percent + '%';
                            }
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '68%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        offsetY: -6,
                                        fontSize: '12px',
                                        color: cssVar('--text-muted')
                                    },
                                    value: { show: false },
                                    total: {
                                        show: true,
                                        label: 'Total Akun',
                                        fontSize: '22px',
                                        fontWeight: 700,
                                        color: cssVar('--primary-ink'),
                                        formatter: function () { return roleTotal; }
                                    }
                                }
                            }
                        }
                    }
                });
            }

        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCharts);
        } else {
            initCharts();
        }

        document.addEventListener('livewire:navigated', initCharts);
    })();
</script>
@endpush
