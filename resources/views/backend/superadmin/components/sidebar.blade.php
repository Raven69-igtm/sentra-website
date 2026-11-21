<div class="quixnav superadmin-sidebar" style="background: var(--surface) !important; border-right: 1px solid var(--border) !important;">
    <div class="quixnav-scroll sidebar-nav">
        <ul class="metismenu" id="menu">
            {{-- SEKSI 1: HOME --}}
            <li class="sidebar-section-label sidebar-section-label--first">Home</li>
            <li class="{{ request()->routeIs('superadmin.dashboard') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.dashboard') }}" wire:navigate aria-expanded="false" title="Dashboard" @if (request()->routeIs('superadmin.dashboard')) aria-current="page" @endif>
                    <i class="fa fa-home sidebar-nav__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                            <path d="M3 10.5 12 3l9 7.5" />
                            <path d="M5.5 9.5V20a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1V9.5" />
                            <path d="M9.5 21v-6.5h5V21" />
                        </svg>
                    </i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- SEKSI 2: USER MANAGEMENT --}}
            <li class="sidebar-section-label">User Management</li>
            <li class="{{ request()->routeIs('superadmin.akun.index') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.akun.index') }}" wire:navigate aria-expanded="false" title="Users" @if (request()->routeIs('superadmin.akun.index')) aria-current="page" @endif>
                    <i class="fa fa-users sidebar-nav__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                            <path d="M16 20v-1.5a3.5 3.5 0 0 0-3.5-3.5h-5A3.5 3.5 0 0 0 4 18.5V20" />
                            <circle cx="10" cy="8" r="3.25" />
                            <path d="M20 20v-1.5a3.5 3.5 0 0 0-2.6-3.38" />
                            <path d="M15.5 5.1a3.25 3.25 0 0 1 0 5.8" />
                        </svg>
                    </i>
                    <span class="nav-text">Users</span>
                    @php
                        $akunCount = \App\Models\Akun::count();
                    @endphp
                    @if ($akunCount > 0)
                        <span class="badge bg-light text-muted rounded-pill px-2 py-1">{{ $akunCount }}</span>
                    @endif
                </a>
            </li>
            <li class="{{ request()->routeIs('superadmin.roles.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.roles.index') }}" wire:navigate aria-expanded="false" title="Roles & Permissions" @if (request()->routeIs('superadmin.roles.*')) aria-current="page" @endif>
                    <i class="fa fa-shield sidebar-nav__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                            <path d="M12 3 4.5 6v5.5c0 4.6 3.1 8.1 7.5 9.5 4.4-1.4 7.5-4.9 7.5-9.5V6L12 3Z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </i>
                    <span class="nav-text">Roles & Permissions</span>
                </a>
            </li>

            {{-- SEKSI 3: OPERATIONS --}}
            <li class="sidebar-section-label">Operations</li>
            <li class="{{ request()->routeIs('superadmin.audit.*') ? 'mm-active' : '' }}">
                <a class="has-arrow" href="javascript:void()"
                    title="Audit Activity"
                    aria-expanded="{{ request()->routeIs('superadmin.audit.*') ? 'true' : 'false' }}"
                    @if (request()->routeIs('superadmin.audit.*')) aria-current="page" @endif>
                    <i class="fa fa-history sidebar-nav__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                            <path d="M3.5 12a8.5 8.5 0 1 0 2.6-6.1" />
                            <path d="M3.5 4.5V9H8" />
                            <path d="M12 7.5V12l3 2" />
                        </svg>
                    </i>
                    <span class="nav-text">Audit Activity</span>
                    @php
                        $aktivitasCount = \App\Models\ActivityLog::count();
                    @endphp
                    @if ($aktivitasCount > 0)
                        <span class="badge bg-light text-muted rounded-pill px-2 py-1">{{ $aktivitasCount }}</span>
                    @endif
                    <span class="sidebar-nav__chevron" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false">
                            <path d="m9 6 6 6-6 6" />
                        </svg>
                    </span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('superadmin.audit.*') ? 'true' : 'false' }}"
                    class="sidebar-nav__submenu {{ request()->routeIs('superadmin.audit.*') ? 'in' : '' }}">
                    <li>
                        <a href="{{ route('superadmin.audit.index', ['tab' => 'semua']) }}" wire:navigate
                            class="{{ request()->routeIs('superadmin.audit.*') && !request()->query('instansi') && request()->query('tab') === 'semua' ? 'mm-active' : '' }}">
                            All Activities
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.audit.index', ['instansi' => 'UPTD PPA']) }}" wire:navigate
                            class="{{ request()->query('instansi') === 'UPTD PPA' ? 'mm-active' : '' }}">
                            Admin PPA Log
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.audit.index', ['instansi' => 'Dinas Sosial']) }}" wire:navigate
                            class="{{ request()->query('instansi') === 'Dinas Sosial' ? 'mm-active' : '' }}">
                            Admin Dinsos Log
                        </a>
                    </li>
                </ul>
            </li>

            {{-- SEKSI 4: ACCOUNT --}}
            <li class="sidebar-section-label sidebar-nav__account-start">Account</li>
            <li class="{{ request()->routeIs('superadmin.settings.*') ? 'mm-active' : '' }}">
                <a href="{{ route('superadmin.settings.index') }}" wire:navigate aria-expanded="false" title="Account Settings" @if (request()->routeIs('superadmin.settings.*')) aria-current="page" @endif>
                    <i class="fa fa-cog sidebar-nav__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" focusable="false" aria-hidden="true">
                            <path d="M4 7h9" />
                            <path d="M17 7h3" />
                            <circle cx="15" cy="7" r="2" />
                            <path d="M4 17h3" />
                            <path d="M11 17h9" />
                            <circle cx="9" cy="17" r="2" />
                        </svg>
                    </i>
                    <span class="nav-text">Account Settings</span>
                </a>
            </li>
        </ul>
    </div>
</div>
