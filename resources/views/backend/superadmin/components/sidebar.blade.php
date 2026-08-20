<div class="quixnav superadmin-sidebar" style="background: #FFFFFF !important; border-right: 1px solid #EBECEF !important;">
    <div class="quixnav-scroll" style="padding-bottom: 20px;">
        <ul class="metismenu" id="menu">
            {{-- SEKSI 1: MAIN MENU --}}
            <li class="sidebar-section-label">Main Menu</li>
            <li class="{{ request()->routeIs('superadmin.dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.dashboard') }}" wire:navigate aria-expanded="false" title="Dashboard">
                    <i class="fa fa-th-large"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('superadmin.audit.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void()"
                    title="Audit Aktivitas Sistem"
                    aria-expanded="{{ request()->routeIs('superadmin.audit.*') ? 'true' : 'false' }}">
                    <i class="fa fa-history"></i>
                    <span class="nav-text">Audit Aktivitas</span>
                    @php
                        $aktivitasCount = \App\Models\ActivityLog::count();
                    @endphp
                    @if ($aktivitasCount > 0)
                        <span class="badge bg-light text-muted rounded-pill px-2 py-1">{{ $aktivitasCount }}</span>
                    @endif
                </a>
                <ul aria-expanded="{{ request()->routeIs('superadmin.audit.*') ? 'true' : 'false' }}"
                    class="{{ request()->routeIs('superadmin.audit.*') ? 'in' : '' }}">
                    <li>
                        <a href="{{ route('superadmin.audit.index') }}" wire:navigate
                            class="{{ request()->routeIs('superadmin.audit.*') && !request()->query('instansi') ? 'mm-active' : '' }}">
                            Semua Aktivitas
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.audit.index', ['instansi' => 'UPTD PPA']) }}" wire:navigate
                            class="{{ request()->query('instansi') === 'UPTD PPA' ? 'mm-active' : '' }}">
                            Aktivitas Admin PPA
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.audit.index', ['instansi' => 'Dinas Sosial']) }}" wire:navigate
                            class="{{ request()->query('instansi') === 'Dinas Sosial' ? 'mm-active' : '' }}">
                            Aktivitas Admin Dinsos
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ request()->routeIs('superadmin.akun.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.akun.index') }}" wire:navigate aria-expanded="false" title="Kelola Akun">
                    <i class="fa fa-users"></i>
                    <span class="nav-text">Kelola Akun</span>
                    @php
                        $akunCount = \App\Models\Akun::count();
                    @endphp
                    @if ($akunCount > 0)
                        <span class="badge bg-light text-muted rounded-pill px-2 py-1">{{ $akunCount }}</span>
                    @endif
                </a>
            </li>
            <li class="{{ request()->routeIs('superadmin.roles.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.roles.index') }}" wire:navigate aria-expanded="false" title="Role &amp; Permission">
                    <i class="fa fa-shield"></i>
                    <span class="nav-text">Role & Permission</span>
                </a>
            </li>

            {{-- SEKSI 2: GENERAL / PENGATURAN --}}
            <li class="sidebar-section-label">General</li>
            <li class="{{ request()->routeIs('superadmin.settings.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.settings.index') }}" wire:navigate aria-expanded="false" title="Settings">
                    <i class="fa fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" onclick="confirmSuperadminLogout()" aria-expanded="false" class="text-danger" title="Log out">
                    <i class="fa fa-sign-out text-danger"></i>
                    <span class="nav-text text-danger">Log out</span>
                </a>
            </li>
        </ul>
    </div>
</div>
