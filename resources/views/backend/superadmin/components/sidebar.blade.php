<div class="quixnav superadmin-sidebar" style="background: #FFFFFF !important; border-right: 1px solid #EBECEF !important;">
    <div class="quixnav-scroll" style="padding-bottom: 20px;">
        <ul class="metismenu" id="menu">
            {{-- SEKSI 1: MAIN MENU --}}
            <li class="sidebar-section-label">Main Menu</li>
            <li class="{{ request()->routeIs('superadmin.dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.dashboard') }}" aria-expanded="false">
                    <i class="fa fa-th-large"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('superadmin.laporan.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.laporan.index') }}" aria-expanded="false" class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-line-chart"></i>
                        <span class="nav-text">Audit Laporan</span>
                    </div>
                    @php
                        $laporanCount = \App\Models\Laporan::count();
                    @endphp
                    @if ($laporanCount > 0)
                        <span class="badge bg-light text-muted rounded-pill px-2 py-1" style="font-size: 0.72rem; font-weight: 600;">{{ $laporanCount }}</span>
                    @endif
                </a>
            </li>
            <li class="{{ request()->routeIs('superadmin.akun.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.akun.index') }}" aria-expanded="false" class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="fa fa-users"></i>
                        <span class="nav-text">Kelola Akun</span>
                    </div>
                    @php
                        $akunCount = \App\Models\Akun::count();
                    @endphp
                    @if ($akunCount > 0)
                        <span class="badge bg-light text-muted rounded-pill px-2 py-1" style="font-size: 0.72rem; font-weight: 600;">{{ $akunCount }}</span>
                    @endif
                </a>
            </li>
            <li class="{{ request()->routeIs('superadmin.roles.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.roles.index') }}" aria-expanded="false">
                    <i class="fa fa-shield"></i>
                    <span class="nav-text">Role & Permission</span>
                </a>
            </li>

            {{-- SEKSI 2: PENGAWASAN INSTANSI --}}
            <li class="sidebar-section-label">Pengawasan Instansi</li>
            <li>
                <a href="{{ route('superadmin.laporan.index') }}?kategori=ppa" aria-expanded="false">
                    <i class="fa fa-female"></i>
                    <span class="nav-text">Kasus UPTD PPA</span>
                </a>
            </li>
            <li>
                <a href="{{ route('superadmin.laporan.index') }}?kategori=dinsos" aria-expanded="false">
                    <i class="fa fa-handshake"></i>
                    <span class="nav-text">Rujukan Dinas Sosial</span>
                </a>
            </li>

            {{-- SEKSI 3: GENERAL / PENGATURAN --}}
            <li class="sidebar-section-label">General</li>
            <li class="{{ request()->routeIs('superadmin.settings.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.settings.index') }}" aria-expanded="false">
                    <i class="fa fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
            <li>
                <a href="{{ route('dashboard') }}" aria-expanded="false" target="_blank">
                    <i class="fa fa-external-link"></i>
                    <span class="nav-text">Portal Operasional</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="confirmSuperadminLogout()" aria-expanded="false" class="text-danger">
                    <i class="fa fa-sign-out text-danger"></i>
                    <span class="nav-text text-danger">Log out</span>
                </a>
            </li>
        </ul>
    </div>
</div>
