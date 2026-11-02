<div class="quixnav superadmin-sidebar" style="background: #0f172a !important;">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">
            {{-- SEKSI UTAMA --}}
            <li class="nav-label text-uppercase fs-6 fw-bold text-slate" style="color: #94a3b8 !important; padding: 15px 20px 5px;">
                Utama
            </li>
            <li class="{{ request()->routeIs('superadmin.dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.dashboard') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                    <i class="fa fa-dashboard me-2 icon-lg text-primary"></i>
                    <span class="custom-font-sidebar nav-text text-white">Dashboard</span>
                </a>
            </li>

            {{-- SEKSI PENGELOLAAN AKSES --}}
            <li class="nav-label text-uppercase fs-6 fw-bold" style="color: #94a3b8 !important; padding: 15px 20px 5px;">
                Akses & Identitas
            </li>
            <li class="{{ request()->routeIs('superadmin.akun.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.akun.index') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                    <i class="fa fa-users me-2 icon-lg text-info"></i>
                    <span class="custom-font-sidebar nav-text text-white">Kelola Akun</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('superadmin.roles.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.roles.index') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                    <i class="fa fa-key me-2 icon-lg text-warning"></i>
                    <span class="custom-font-sidebar nav-text text-white">Role & Permission</span>
                </a>
            </li>

            {{-- SEKSI PENGAWASAN LAPORAN --}}
            <li class="nav-label text-uppercase fs-6 fw-bold" style="color: #94a3b8 !important; padding: 15px 20px 5px;">
                Audit Sistem
            </li>
            <li class="{{ request()->routeIs('superadmin.laporan.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.laporan.index') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                    <i class="fa fa-file-text-o me-2 icon-lg text-success"></i>
                    <span class="custom-font-sidebar nav-text text-white">Audit Laporan</span>
                </a>
            </li>

            {{-- SEKSI KONFIGURASI --}}
            <li class="nav-label text-uppercase fs-6 fw-bold" style="color: #94a3b8 !important; padding: 15px 20px 5px;">
                Sistem
            </li>
            <li class="{{ request()->routeIs('superadmin.settings.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.settings.index') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                    <i class="fa fa-cogs me-2 icon-lg text-secondary"></i>
                    <span class="custom-font-sidebar nav-text text-white">Konfigurasi Sistem</span>
                </a>
            </li>

            {{-- SEKSI SWITCH PORTAL --}}
            <li class="nav-label text-uppercase fs-6 fw-bold" style="color: #94a3b8 !important; padding: 15px 20px 5px;">
                Pintasan
            </li>
            <li>
                <a href="{{ route('dashboard') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                    <i class="fa fa-external-link me-2 icon-lg text-light"></i>
                    <span class="custom-font-sidebar nav-text text-white-50">Portal Operasional</span>
                </a>
            </li>
        </ul>
    </div>
</div>
