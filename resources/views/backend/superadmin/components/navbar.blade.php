{{-- NAV HEADER LOGO UNTUK SUPERADMIN --}}
<div class="nav-header" style="background: #0f172a !important; border-bottom: 1px solid rgba(255,255,255,0.08);">
    <a href="{{ route('superadmin.dashboard') }}" class="brand-logo d-flex align-items-center px-3" style="text-decoration: none;">
        <span class="p-2 bg-primary rounded-circle text-white me-2 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
            <i class="fa fa-shield fa-lg"></i>
        </span>
        <div class="brand-title ms-2">
            <span class="fw-bold text-white fs-5" style="letter-spacing: 0.5px;">SENTRA</span>
            <span class="badge badge-danger text-uppercase ms-1" style="font-size: 0.65rem; vertical-align: middle;">Root</span>
        </div>
    </a>

    <div class="nav-control">
        <div class="hamburger text-white">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>

{{-- TOPBAR SUPERADMIN --}}
<div class="header" style="background: #ffffff; border-bottom: 1px solid #e2e8f0; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left d-flex align-items-center">
                    <span class="badge badge-outline-primary me-2 px-3 py-2 fw-semibold">
                        <i class="fa fa-terminal me-1"></i> Konsol Superadmin
                    </span>
                    <span class="text-muted d-none d-md-inline" style="font-size: 0.85rem;">
                        <i class="fa fa-circle text-success me-1" style="font-size: 8px;"></i> Sistem Aktif & Terlindungi
                    </span>
                </div>

                <ul class="navbar-nav header-right d-flex align-items-center">
                    {{-- Quick Portal Switch --}}
                    <li class="nav-item me-3 d-none d-lg-block">
                        <a href="{{ route('dashboard') }}" class="btn btn-xs btn-outline-secondary" title="Buka Portal Admin Operasional">
                            <i class="fa fa-external-link me-1"></i> Portal Operasional
                        </a>
                    </li>

                    {{-- Profil Superadmin Dropdown --}}
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link d-flex align-items-center" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                            <div class="p-2 bg-danger text-white rounded-circle me-2 d-inline-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                                <i class="fa fa-user-secret"></i>
                            </div>
                            <div class="d-none d-sm-block text-start">
                                <span class="fw-bold text-dark d-block" style="font-size: 0.88rem; line-height: 1.2;">{{ Auth::user()->nama ?? 'Superadmin' }}</span>
                                <span class="badge badge-danger text-uppercase" style="font-size: 0.65rem;">Superadmin</span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow-sm border-0 py-2" style="min-width: 200px;">
                            <div class="px-3 py-2 border-bottom">
                                <span class="text-muted small d-block">Login sebagai</span>
                                <strong class="text-dark">{{ Auth::user()->email ?? 'superadmin@sentra.com' }}</strong>
                            </div>
                            <a href="{{ route('superadmin.settings.index') }}" class="dropdown-item py-2">
                                <i class="fa fa-sliders text-primary me-2"></i> Konfigurasi Sistem
                            </a>
                            <a href="{{ route('superadmin.akun.index') }}" class="dropdown-item py-2">
                                <i class="fa fa-users text-info me-2"></i> Manajemen Akun
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}" id="superadmin-logout-form">
                                @csrf
                                <button type="button" class="dropdown-item text-danger py-2" onclick="confirmSuperadminLogout()">
                                    <i class="fa fa-sign-out text-danger me-2"></i> Logout dari Konsol
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

<script>
    function confirmSuperadminLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Keluar dari Konsol Superadmin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('superadmin-logout-form').submit();
            }
        });
    }
</script>
