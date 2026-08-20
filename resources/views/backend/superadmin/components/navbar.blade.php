{{-- NAV HEADER BRAND & LOGO (MATCHING IMAGE #1) --}}
<div class="nav-header" style="background: #FFFFFF !important; border-bottom: 1px solid #EBECEF !important; border-right: 1px solid #EBECEF !important;">
    <a href="{{ route('superadmin.dashboard') }}" wire:navigate class="brand-logo d-flex align-items-center px-3"
        style="text-decoration: none; width: 100%; background: #FFFFFF !important;">
        <div class="d-flex align-items-center">
            @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
                <img src="{{ asset($appLogo) }}" alt="Logo"
                    style="height: 50px; width: auto; object-fit: contain;">
            @else
                <img src="{{ asset('admin/assets_2/images/logo_nganjuk.png') }}" alt="Logo Sentra"
                    style="height: 50px; width: auto; object-fit: contain;">
            @endif
            <div class="brand-title ms-3" style="min-width: 0; overflow: hidden;">
                {{-- Mask reveal hanya diputar sekali, tepat pada halaman pertama
                     setelah superadmin login; penanda sesi langsung dikonsumsi
                     (pull) agar navigasi berikutnya tidak beranimasi lagi. --}}
                @php $brandIntro = session()->pull('superadmin_brand_intro', false); @endphp
                <span class="text-dark {{ $brandIntro ? 'brand-unveil' : '' }}"
                    style="font-size: 1.5rem; font-weight: 700; letter-spacing: 0.05em; line-height: 1.1; display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}</span>
            </div>
        </div>
    </a>

    {{-- Tombol buka/tutup sidebar dipisah dari link brand agar klik tidak
         memicu navigasi ke dashboard. --}}
    <div class="nav-control" style="cursor: pointer;">
        <div class="hamburger" style="color: #64748B;">
            <span class="line" style="background: #64748B;"></span>
            <span class="line" style="background: #64748B;"></span>
            <span class="line" style="background: #64748B;"></span>
        </div>
    </div>
</div>

{{-- TOPBAR SUPERADMIN (MATCHING IMAGE #1 TOP BAR) --}}
<div class="header" style="background: #FFFFFF !important; border-bottom: 1px solid #EBECEF !important;">
    <div class="header-content" style="padding: 0 24px;">
        <nav class="navbar navbar-expand h-100">
            <div class="collapse navbar-collapse justify-content-between h-100">
                {{-- LEFT: MODERN SEARCH PILL WITH KBD SHORTCUT --}}
                <div class="header-left d-flex align-items-center">
                </div>

                {{-- RIGHT UTILITIES: USER PROFILE --}}
                <ul class="navbar-nav header-right d-flex align-items-center mb-0">
                    {{-- USER PROFILE CAPSULE --}}
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link d-flex align-items-center" href="#" role="button" data-toggle="dropdown" aria-expanded="false" style="padding: 6px 12px; border-radius: 9999px; background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <div class="position-relative me-2">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama ?? 'Superadmin Sentra') }}&background=006039&color=fff&bold=true&size=40" alt="Avatar" class="rounded-circle" style="width: 36px; height: 36px; object-fit: cover;">
                                <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle" style="width: 9px; height: 9px;"></span>
                            </div>
                            <div class="d-none d-md-block text-start me-2">
                                <span class="fw-bold text-dark d-block" style="font-size: 0.9rem; line-height: 1.15;">
                                    {{ Auth::user()->nama ?? 'Sajibur Rahman' }}
                                </span>
                                <small class="text-muted" style="font-size: 0.75rem;">Superadmin</small>
                            </div>
                            <i class="fa fa-chevron-down text-muted" style="font-size: 0.75rem;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow-lg border-0 py-2" style="min-width: 230px; border-radius: 16px;">
                            <div class="px-3 py-2 border-bottom">
                                <span class="text-muted small d-block">Masuk sebagai</span>
                                <strong class="text-dark" style="font-size: 0.875rem;">{{ Auth::user()->email ?? 'superadmin@sentra.com' }}</strong>
                            </div>
                            <a href="{{ route('superadmin.profile.edit') }}" wire:navigate class="dropdown-item py-2">
                                <i class="fa fa-user text-muted me-2"></i> Profil Saya
                            </a>
                            <a href="{{ route('superadmin.settings.index') }}" wire:navigate class="dropdown-item py-2">
                                <i class="fa fa-sliders text-muted me-2"></i> Settings
                            </a>
                            <a href="{{ route('superadmin.akun.index') }}" wire:navigate class="dropdown-item py-2">
                                <i class="fa fa-users text-muted me-2"></i> Kelola Akun
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" id="superadmin-logout-form">
                                @csrf
                                <button type="button" class="dropdown-item text-danger py-2" onclick="confirmSuperadminLogout()">
                                    <i class="fa fa-sign-out text-danger me-2"></i> Log out
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<script data-navigate-once>
    function confirmSuperadminLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda ingin keluar dari Konsol Superadmin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#006039',
            cancelButtonColor: '#94A3B8',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('superadmin-logout-form').submit();
            }
        });
    }

    // Keyboard shortcut CMD/CTRL + K focus search
    document.addEventListener('keydown', function(e) {
        if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
            e.preventDefault();
            const input = document.getElementById('globalSearchInput');
            if (input) input.focus();
        }
    });
</script>
