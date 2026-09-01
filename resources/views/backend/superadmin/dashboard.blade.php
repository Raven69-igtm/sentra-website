@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    {{-- TOP GREETING BANNER (MATCHING IMAGE #1) --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bolder text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.03em;">
                Welcome back {{ Auth::user()->nama ?? 'Admin' }}
            </h2>
            <p class="text-muted mb-0" style="font-size: 0.92rem;">
                Pantau dan kelola seluruh akun, wewenang instansi PPA & Dinsos, serta konfigurasi sistem hari ini.
            </p>
        </div>

        <div class="d-flex align-items-center mt-3 mt-md-0">
            {{-- DATE CHIP --}}
            <div class="bg-white border rounded-pill px-3 py-2 me-2 d-flex align-items-center shadow-sm" style="font-size: 0.86rem; font-weight: 600; color: #475569;">
                <i class="fa fa-calendar me-2 text-muted"></i>
                {{ \Carbon\Carbon::now()->translatedFormat('D, d F Y') }}
            </div>

            {{-- EXPORT / ACTION PILL BUTTON --}}
            <a href="{{ route('superadmin.audit.index') }}" wire:navigate class="btn btn-oripio-dark btn-pill shadow-sm">
                <i class="fa fa-history me-2" style="font-size: 0.85rem;"></i> Lihat Aktivitas
            </a>
        </div>
    </div>

    {{-- ALERTS --}}
    @include('backend.superadmin.components.alerts')

    {{-- ROW 1: 3 METRIC CARDS (MATCHING IMAGE #1) --}}
    <div class="row g-4 mb-4">
        {{-- CARD 1: HERO METRIC (ACCOUNT BALANCE STYLE) --}}
        <div class="col-xl-4 col-lg-4 col-md-12" data-reveal style="--d: 0ms;">
            <div class="card h-100 p-2">
                <div class="card-body d-flex flex-column justify-content-between p-3">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div class="me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #DCFCE7; border-radius: 12px; color: #006039;">
                                    <i class="fa fa-users" style="font-size: 1.1rem;"></i>
                                </div>
                                <span class="fw-semibold text-muted" style="font-size: 1.00rem;">Total Akun Sistem</span>
                            </div>

                            <span class="badge bg-light text-dark rounded-pill px-3 py-2 border" style="font-size: 0.76rem; font-weight: 600;">
                                <i class="fa fa-circle text-success me-1" style="font-size: 7px;"></i> Semua Role
                            </span>
                        </div>

                        <div class="d-flex align-items-baseline mb-2">
                            <h2 class="fw-bolder text-dark mb-0 me-3" style="font-size: 2.2rem; letter-spacing: -0.03em;">
                                <span data-countup data-target="{{ (int) $totalAkun }}">{{ number_format($totalAkun) }}</span>
                            </h2>
                            <span class="text-muted" style="font-size: 1.00rem;">Pengguna</span>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <span class="badge-pill-soft badge-soft-green me-2">
                                <i class="fa fa-check me-1" style="font-size: 0.7rem;"></i> {{ number_format($totalAkun - $totalNonAktif) }} Aktif
                            </span>
                            <small class="text-muted">{{ number_format($totalNonAktif) }} akun nonaktif</small>
                        </div>

                        {{-- RINCIAN AKUN PER KELOMPOK --}}
                        <div class="p-3 bg-light rounded-4" style="border-radius: 14px;">
                            <div class="d-flex text-center" style="gap: 4px;">
                                <div class="flex-fill">
                                    <div class="fw-bolder text-dark" style="font-size: 1rem;">{{ number_format($totalSuperadmin) }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Superadmin</div>
                                </div>
                                <div class="flex-fill border-start">
                                    <div class="fw-bolder text-dark" style="font-size: 1rem;">{{ number_format($totalAdmin) }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Admin Instansi</div>
                                </div>
                                <div class="flex-fill border-start">
                                    <div class="fw-bolder text-dark" style="font-size: 1rem;">{{ number_format($totalUser) }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Masyarakat</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- BOTTOM ACTION BUTTONS --}}
                    <div class="d-flex align-items-center pt-2">
                        <a href="{{ route('superadmin.akun.create') }}" wire:navigate class="btn btn-oripio-primary btn-pill me-2 flex-grow-1">
                            <i class="fa fa-user-plus me-2"></i> Tambah Akun
                        </a>
                        <a href="{{ route('superadmin.roles.index') }}" wire:navigate class="btn btn-oripio-light btn-pill flex-grow-1">
                            <i class="fa fa-key me-2"></i> Kelola Role
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 2: TOTAL EXPENSES STYLE (LAPORAN PPA) --}}
        <div class="col-xl-4 col-lg-4 col-md-6" data-reveal style="--d: 60ms;">
            <div class="card h-100 p-2">
                <div class="card-body p-3 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #E0F2FE; border-radius: 12px; color: #0284C7;">
                                <i class="fa fa-female" style="font-size: 1.15rem;"></i>
                            </div>
                            <span class="fw-semibold text-muted" style="font-size: 1.00rem;">Laporan UPTD PPA</span>
                        </div>

                        <span class="badge-pill-soft badge-soft-blue" style="font-size: 0.68rem;">PPA</span>
                    </div>

                    <div class="d-flex align-items-baseline mb-2">
                        <h2 class="fw-bolder text-dark mb-0 me-3" style="font-size: 2.2rem; letter-spacing: -0.03em;">
                            <span data-countup data-target="{{ (int) $totalLaporanPPA }}">{{ number_format($totalLaporanPPA) }}</span>
                        </h2>
                        <span class="text-muted" style="font-size: 1.00rem;">Kasus</span>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <span class="badge-pill-soft badge-soft-blue me-2">
                            <i class="fa fa-shield me-1" style="font-size: 0.7rem;"></i> UPTD PPA
                        </span>
                        <small class="text-muted">kekerasan perempuan & anak</small>
                    </div>

                    <div class="p-3 bg-light rounded-4 mt-2" style="border-radius: 14px;">
                        <div class="d-flex justify-content-between align-items-center" style="font-size: 0.82rem;">
                            <span class="text-muted">Kewenangan Utama:</span>
                            <span class="fw-bold text-dark">Fisik, Psikis, Seksual, TPPO</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center pt-3 mt-auto">
                        <a href="{{ route('superadmin.audit.index', ['instansi' => 'UPTD PPA']) }}" wire:navigate class="btn btn-oripio-light btn-pill w-100">
                            <i class="fa fa-arrow-right me-1"></i> Lihat Aktivitas PPA
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- CARD 3: TOTAL SAVINGS STYLE (RUJUKAN & DINSOS) --}}
        <div class="col-xl-4 col-lg-4 col-md-6" data-reveal style="--d: 120ms;">
            <div class="card h-100 p-2">
                <div class="card-body p-3 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background: #FEF3C7; border-radius: 12px; color: #D97706;">
                                <i class="fa fa-handshake" style="font-size: 1.15rem;"></i>
                            </div>
                            <span class="fw-semibold text-muted" style="font-size: 1.00rem;">Laporan & Rujukan Dinsos</span>
                        </div>

                        <span class="badge-pill-soft badge-soft-amber" style="font-size: 0.68rem;">Dinsos</span>
                    </div>

                    <div class="d-flex align-items-baseline mb-2">
                        <h2 class="fw-bolder text-dark mb-0 me-3" style="font-size: 2.2rem; letter-spacing: -0.03em;">
                            <span data-countup data-target="{{ (int) $totalLaporanDinsos }}">{{ number_format($totalLaporanDinsos) }}</span>
                        </h2>
                        <span class="text-muted" style="font-size: 1.00rem;">Kasus</span>
                    </div>

                    <div class="d-flex align-items-center mb-3">
                        <span class="badge-pill-soft badge-soft-amber me-2">
                            <i class="fa fa-share me-1" style="font-size: 0.7rem;"></i> {{ number_format($totalLaporanDirujuk) }} Dirujuk
                        </span>
                        <small class="text-muted">status rujukan aktif</small>
                    </div>

                    <div class="p-3 bg-light rounded-4 mt-2" style="border-radius: 14px;">
                        <div class="d-flex justify-content-between align-items-center" style="font-size: 0.82rem;">
                            <span class="text-muted">Kewenangan Utama:</span>
                            <span class="fw-bold text-dark">Penelantaran & Kasus Dirujuk</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center pt-3 mt-auto">
                        <a href="{{ route('superadmin.audit.index', ['instansi' => 'Dinas Sosial']) }}" wire:navigate class="btn btn-oripio-light btn-pill w-100">
                            <i class="fa fa-arrow-right me-1"></i> Lihat Aktivitas Dinsos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: MAIN GRID (LEFT 5 COLS / RIGHT 7 COLS) --}}
    <div class="row g-4">
        {{-- LEFT COLUMN: WALLET STYLE (INSTANSI) + SAVINGS PLAN STYLE (SLA) --}}
        <div class="col-xl-5 col-lg-5">
            {{-- WIDGET A: MY WALLET STYLE -> KELOMPOK AKUN INSTANSI --}}
            <div class="card mb-4 p-2">
                <div class="card-header d-flex justify-content-between align-items-center border-0 pb-0">
                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Kelompok Akun</h5>
                    <a href="{{ route('superadmin.akun.create') }}" wire:navigate class="btn btn-sm btn-oripio-light btn-pill">
                        <i class="fa fa-plus me-1"></i> Tambah
                    </a>
                </div>
                <div class="card-body pt-3">
                    <div class="row g-3">
                        {{-- 1. SUPERADMIN CARD --}}
                        <div class="col-6">
                            <div class="p-3 border rounded-4 h-100" style="border-radius: 16px; background: #FAFBFD;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 10px; background: #DCFCE7; color: #006039;">
                                        <i class="fa fa-crown" style="font-size: 0.82rem;"></i>
                                    </div>
                                    <span class="badge-pill-soft badge-soft-green" style="font-size: 0.68rem; padding: 2px 8px;"><i class="fa fa-circle me-1" style="font-size: 6px;"></i> Aktif</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Superadmin</h6>
                                <p class="fw-bolder text-dark mb-1" style="font-size: 1.1rem;">{{ $totalSuperadmin }} <small class="text-muted fw-normal" style="font-size: 0.78rem;">Akun</small></p>
                            </div>
                        </div>

                        {{-- 2. ADMIN PPA CARD --}}
                        <div class="col-6">
                            <div class="p-3 border rounded-4 h-100" style="border-radius: 16px; background: #FAFBFD;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 10px; background: #E0F2FE; color: #0284C7;">
                                        <i class="fa fa-university" style="font-size: 0.82rem;"></i>
                                    </div>
                                    <span class="badge-pill-soft badge-soft-green" style="font-size: 0.68rem; padding: 2px 8px;"><i class="fa fa-circle me-1" style="font-size: 6px;"></i> Aktif</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Admin PPA</h6>
                                <p class="fw-bolder text-dark mb-1" style="font-size: 1.1rem;">{{ $totalAdminPPA }} <small class="text-muted fw-normal" style="font-size: 0.78rem;">Akun</small></p>
                            </div>
                        </div>

                        {{-- 3. ADMIN DINSOS CARD --}}
                        <div class="col-6">
                            <div class="p-3 border rounded-4 h-100" style="border-radius: 16px; background: #FAFBFD;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 10px; background: #FEF3C7; color: #D97706;">
                                        <i class="fa fa-handshake" style="font-size: 0.82rem;"></i>
                                    </div>
                                    <span class="badge-pill-soft badge-soft-green" style="font-size: 0.68rem; padding: 2px 8px;"><i class="fa fa-circle me-1" style="font-size: 6px;"></i> Aktif</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Admin Dinsos</h6>
                                <p class="fw-bolder text-dark mb-1" style="font-size: 1.1rem;">{{ $totalAdminDinsos }} <small class="text-muted fw-normal" style="font-size: 0.78rem;">Akun</small></p>
                            </div>
                        </div>

                        {{-- 4. USER PELAPOR CARD --}}
                        <div class="col-6">
                            <div class="p-3 border rounded-4 h-100" style="border-radius: 16px; background: #FAFBFD;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 10px; background: #F1F5F9; color: #64748B;">
                                        <i class="fa fa-user" style="font-size: 0.82rem;"></i>
                                    </div>
                                    <span class="badge-pill-soft badge-soft-green" style="font-size: 0.68rem; padding: 2px 8px;"><i class="fa fa-circle me-1" style="font-size: 6px;"></i> Aktif</span>
                                </div>
                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">Masyarakat</h6>
                                <p class="fw-bolder text-dark mb-1" style="font-size: 1.1rem;">{{ $totalUser }} <small class="text-muted fw-normal" style="font-size: 0.78rem;">User</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WIDGET B: MY SAVINGS PLAN STYLE -> TARGET SLA & STANDAR LAYANAN --}}
            <div class="card p-2">
                <div class="card-header d-flex justify-content-between align-items-center border-0 pb-0">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-bullseye me-2 text-success" style="font-size: 1.1rem;"></i>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Standar Layanan & SLA</h5>
                    </div>
                </div>
                <div class="card-body pt-3">
                    {{-- ITEM 1: SLA RESPON KASUS PPA --}}
                    <div class="p-3 border rounded-4 mb-3" style="border-radius: 16px;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 p-2 rounded-circle" style="background: #E0F2FE; color: #0284C7;">
                                <i class="fa fa-clock"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">SLA Respon Kasus PPA</h6>
                                <small class="text-muted">{{ $reportSla }} Hari / Maks. 7 Hari Target</small>
                            </div>
                            <span class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $slaPercentPPA }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 9999px; background: #F1F5F9;">
                            <div class="progress-bar" style="width: {{ $slaPercentPPA }}%; background-color: #006039; border-radius: 9999px;"></div>
                        </div>
                    </div>

                    {{-- ITEM 2: PENYELESAIAN RUJUKAN DINSOS --}}
                    <div class="p-3 border rounded-4 mb-3" style="border-radius: 16px;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 p-2 rounded-circle" style="background: #FEF3C7; color: #D97706;">
                                <i class="fa fa-shield"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">Penyelesaian Rujukan Dinsos</h6>
                                <small class="text-muted">Target 5 Hari Kerja</small>
                            </div>
                            <span class="fw-bold text-dark" style="font-size: 0.86rem;">{{ $slaPercentDinsos }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 9999px; background: #F1F5F9;">
                            <div class="progress-bar" style="width: {{ $slaPercentDinsos }}%; background-color: #10B981; border-radius: 9999px;"></div>
                        </div>
                    </div>

                    {{-- ITEM 3: ENKRIPSI DATABASE & KEAMANAN --}}
                    <div class="p-3 border rounded-4" style="border-radius: 16px;">
                        <div class="d-flex align-items-center mb-2">
                            <div class="me-3 p-2 rounded-circle" style="background: #F3E8FF; color: #9333EA;">
                                <i class="fa fa-lock"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">Enkripsi Data Sensitif (AES-256)</h6>
                                <small class="text-muted">Telepon & NIK Terenkripsi</small>
                            </div>
                            <span class="fw-bold text-dark" style="font-size: 0.86rem;">100%</span>
                        </div>
                        <div class="progress" style="height: 6px; border-radius: 9999px; background: #F1F5F9;">
                            <div class="progress-bar" style="width: 100%; background-color: #006039; border-radius: 9999px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- WIDGET C: RINGKASAN STATUS KASUS --}}
            <div class="card mt-4 p-2">
                <div class="card-header d-flex justify-content-between align-items-center border-0 pb-0">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-folder-open me-2" style="color: #006039; font-size: 1.05rem;"></i>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Ringkasan Kasus</h5>
                    </div>
                    <a href="{{ route('superadmin.audit.index') }}" wire:navigate class="btn btn-sm btn-oripio-light btn-pill">
                        <i class="fa fa-arrow-right me-1"></i> Lihat
                    </a>
                </div>
                <div class="card-body pt-3">
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--oripio-border-subtle);">
                        <span class="text-muted" style="font-size: 0.84rem;"><i class="fa fa-file-lines me-2" style="color: #0284C7;"></i> Total Kasus</span>
                        <strong class="text-dark" style="font-size: 0.95rem;">{{ number_format($totalLaporan) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom: 1px solid var(--oripio-border-subtle);">
                        <span class="text-muted" style="font-size: 0.84rem;"><i class="fa fa-check-circle me-2" style="color: #059669;"></i> Selesai</span>
                        <strong class="text-dark" style="font-size: 0.95rem;">{{ number_format($totalLaporanSelesai) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between align-items-center py-2">
                        <span class="text-muted" style="font-size: 0.84rem;"><i class="fa fa-share me-2" style="color: #D97706;"></i> Dirujuk</span>
                        <strong class="text-dark" style="font-size: 0.95rem;">{{ number_format($totalLaporanDirujuk) }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: OVERVIEW CHART + RECENT ACTIVITIES TABLE --}}
        <div class="col-xl-7 col-lg-7">
            {{-- WIDGET C: OVERVIEW CHART (MATCHING EXACT VISUAL IN IMAGE #1) --}}
            <div class="card mb-4 p-2">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center border-0 pb-0">
                    <div class="d-flex align-items-center mb-2 mb-sm-0">
                        <i class="fa fa-bar-chart me-2" style="color: #006039; font-size: 1.15rem;"></i>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Overview Kasus Bulanan</h5>
                    </div>

                    <div class="d-flex align-items-center">
                        <div class="me-3 d-flex align-items-center" style="font-size: 0.82rem;">
                            <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #006039;"></span>
                            <span class="text-dark fw-semibold me-3">Kasus Masuk</span>
                            <span class="d-inline-block rounded-circle me-1" style="width: 8px; height: 8px; background: #A7F3D0;"></span>
                            <span class="text-muted">Rujukan</span>
                        </div>

                        {{-- Filter tahun interaktif (GET ?tahun=) — otomatis diterapkan via live filter --}}
                        <form method="GET" action="{{ route('superadmin.dashboard') }}" data-livewire-navigate class="m-0">
                            <select name="tahun"
                                class="bg-light text-dark rounded-pill px-3 py-2 border"
                                style="font-size: 0.78rem; font-weight: 600; border-color: #E2E8F0; cursor: pointer; outline: none;">
                                @foreach ($availableYears as $y)
                                    <option value="{{ $y }}" {{ (int) $y === (int) $tahun ? 'selected' : '' }}>Tahun {{ $y }}</option>
                                @endforeach
                            </select>
                        </form>

                    </div>
                </div>

                <div class="card-body pt-4">
                    {{-- VERTICAL BAR CHART: 2 SERI (KASUS MASUK & RUJUKAN) --}}
                    <div style="height: 240px; display: flex; align-items: flex-end; justify-content: space-between; padding: 20px 10px 0; border-bottom: 1px solid #EBECEF; position: relative;">
                        @php
                            $maxVal = max(array_merge($chartDataMasuk, $chartDataRujukan, [10]));
                        @endphp

                        @foreach ($months as $idx => $mName)
                            @php
                                $masuk = $chartDataMasuk[$idx] ?? 0;
                                $rujukan = $chartDataRujukan[$idx] ?? 0;
                                $heightMasuk = max(2, min(95, round(($masuk / $maxVal) * 85)));
                                $heightRujukan = max(2, min(95, round(($rujukan / $maxVal) * 85)));
                                $isHighlighted = ($mName === $highlightName);
                            @endphp
                            <div style="flex: 1; margin: 0 4px; position: relative; height: 100%; display: flex; align-items: flex-end; justify-content: center; gap: 3px;">
                                @if ($isHighlighted)
                                    {{-- TOOLTIP PILL PADA BULAN YANG DISOROT --}}
                                    <div style="position: absolute; bottom: calc({{ max($heightMasuk, $heightRujukan) }}% + 10px); background: #0F172A; color: #FFFFFF; padding: 4px 10px; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; white-space: nowrap; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 5;">
                                        Masuk: {{ $highlightMasuk }} &bull; Rujukan: {{ $highlightRujukan }}
                                    </div>
                                    <div style="position: absolute; bottom: {{ max($heightMasuk, $heightRujukan) }}%; width: 8px; height: 8px; background: #0F172A; border-radius: 50%; z-index: 4;"></div>
                                @endif

                                {{-- BAR KASUS MASUK --}}
                                <div data-grow-bar style="--d: {{ $idx * 30 }}ms; width: 44%; max-width: 19px; height: {{ $heightMasuk }}%; background: #006039; border-radius: 12px 12px 4px 4px; transition: background-color 0.3s ease;" title="{{ $mName }} — Kasus Masuk: {{ $masuk }}"></div>
                                {{-- BAR RUJUKAN --}}
                                <div data-grow-bar style="--d: {{ $idx * 30 }}ms; width: 44%; max-width: 19px; height: {{ $heightRujukan }}%; background: #A7F3D0; border-radius: 12px 12px 4px 4px; transition: background-color 0.3s ease;" title="{{ $mName }} — Rujukan: {{ $rujukan }}"></div>
                            </div>
                        @endforeach
                    </div>

                    {{-- X-AXIS LABELS --}}
                    <div class="d-flex justify-content-between pt-2 px-2 text-muted" style="font-size: 0.76rem; font-weight: 600;">
                        @foreach ($months as $mName)
                            <div style="flex: 1; text-align: center; color: {{ $mName === $highlightName ? '#006039; font-weight: 800;' : '#94A3B8;' }}">
                                {{ $mName }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- WIDGET D: RECENT ACTIVITY TABLE (MATCHING "RECENT TRANSACTION" IN IMAGE #1) --}}
            <div class="card p-2">
                <div class="card-header d-flex justify-content-between align-items-center border-0 pb-0">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-clock me-2" style="color: #006039; font-size: 1.15rem;"></i>
                        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Aktivitas Sistem Terkini</h5>
                    </div>

                    <a href="{{ route('superadmin.audit.index') }}" wire:navigate class="btn btn-sm btn-oripio-light btn-pill" style="font-size: 0.8rem; padding: 4px 14px;">
                        <i class="fa fa-filter me-1"></i> Filter <i class="fa fa-chevron-down ms-1" style="font-size: 0.62rem;"></i>
                    </a>
                </div>
                <div class="card-body pt-3 px-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 table-activity">
                            <thead>
                                <tr>
                                    <th>Aktivitas & Subjek</th>
                                    <th>Tanggal</th>
                                    <th>Instansi / Pelaku</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($recentActivities as $act)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border-radius: 12px; background: {{ $act->type === 'laporan' ? '#DCFCE7' : '#E0F2FE' }}; color: {{ $act->type === 'laporan' ? '#006039' : '#0284C7' }};">
                                                    <i class="fa {{ $act->icon }}"></i>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.86rem;">{{ $act->title }}</h6>
                                                    <small class="text-muted" style="font-size: 0.74rem;">{{ $act->subtitle }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-muted text-nowrap" style="font-size: 0.82rem;">
                                            {{ $act->date }}
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border px-2 py-1 rounded-pill" style="font-size: 0.74rem;">
                                                {{ $act->instansi }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($act->status === 'Aktif' || $act->status === 'Selesai')
                                                <span class="badge-pill-soft badge-soft-green" style="font-size: 0.72rem;">
                                                    <i class="fa fa-circle me-1" style="font-size: 6px;"></i> {{ $act->status }}
                                                </span>
                                            @elseif ($act->status === 'Dirujuk')
                                                <span class="badge-pill-soft badge-soft-amber" style="font-size: 0.72rem;">
                                                    <i class="fa fa-circle me-1" style="font-size: 6px;"></i> {{ $act->status }}
                                                </span>
                                            @else
                                                <span class="badge-pill-soft badge-soft-blue" style="font-size: 0.72rem;">
                                                    <i class="fa fa-circle me-1" style="font-size: 6px;"></i> {{ $act->status }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada aktivitas tercatat</td>
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
