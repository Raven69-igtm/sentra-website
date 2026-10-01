@php
    $user = Auth::user();
    $isDinsos = $user && $user->isAdminDinsos();
    $appName = \App\Models\SystemSetting::get('app_name', 'SENTRA');

    // Identitas instansi (Diferensiasi #2: sub-label di bawah nama aplikasi)
    $instansiLabel = $isDinsos ? 'Dinas Sosial PPPA' : 'UPTD PPA';
    $roleLabel = $isDinsos ? 'Admin Dinsos' : 'Admin PPA';
    $homeRoute = $isDinsos ? route('dinsos.dashboard') : route('dashboard');
    $accent = $isDinsos ? '#B45309' : '#006039';

    // Inisial avatar (tanpa dependensi jaringan)
    $namaUser = $user->nama ?? 'Admin';
    $namaParts = preg_split('/\s+/', trim($namaUser));
    $initials = mb_strtoupper(mb_substr($namaParts[0] ?? 'A', 0, 1) . mb_substr($namaParts[1] ?? '', 0, 1));
@endphp

{{-- ============================================================
     NAV HEADER — brand instansi (logo + nama aplikasi + sub-label)
     Hamburger dipisah dari tautan brand agar klik tidak bernavigasi.
     ============================================================ --}}
<div class="nav-header">
    <a href="{{ $homeRoute }}" class="brand-logo" title="{{ $appName }} — {{ $instansiLabel }}">
        @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
            <img src="{{ asset($appLogo) }}" alt="Logo {{ $instansiLabel }}">
        @else
            <img src="{{ asset('admin/assets_2/images/logo_nganjuk.png') }}" alt="Logo {{ $instansiLabel }}">
        @endif
        <span class="brand-meta">
            <span class="brand-name">{{ $appName }}</span>
            <span class="brand-sub">{{ $instansiLabel }}</span>
        </span>
    </a>

    <div class="nav-control" title="Buka / tutup menu" role="button" aria-label="Buka atau tutup menu">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>

{{-- ============================================================
     TOPBAR — notifikasi + chip profil
     ============================================================ --}}
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left d-flex align-items-center"></div>

                <ul class="navbar-nav header-right">
                    {{-- Notifikasi (ID dipertahankan untuk polling AJAX 5 detik) --}}
                    <li class="nav-item dropdown notification_dropdown">
                        <a class="topbar-icon-btn" href="#" role="button" data-toggle="dropdown"
                            aria-expanded="false" title="Notifikasi">
                            <i class="fa-regular fa-bell"></i>
                            <span id="notification-badge" class="icon-badge" style="display: none;"></span>
                            <span id="notification-pulse" class="pulse-css" style="display: none;"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="notif-head">
                                <strong>Notifikasi</strong>
                                <span id="notification-count-header" class="badge badge-danger"
                                    style="display: none;"></span>
                            </div>
                            <div id="notification-list" class="notification-list">
                                <div class="notif-empty">Memuat notifikasi…</div>
                            </div>
                            <div id="notification-footer" class="notif-foot" style="display: none;">
                                <a href="#" data-toggle="modal" data-target="#allNotificationsModal">
                                    Lihat semua notifikasi
                                </a>
                            </div>
                        </div>
                    </li>

                    {{-- Chip profil --}}
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="#" role="button" data-toggle="dropdown" aria-expanded="false">
                            <span class="profile-avatar">{{ $initials }}</span>
                            <span class="profile-meta d-none d-md-block">
                                <span class="profile-name">{{ $namaUser }}</span>
                                <span class="profile-role">{{ $roleLabel }}</span>
                            </span>
                            <i class="fa fa-chevron-down text-muted" style="font-size: .7rem;"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="profile-head">
                                <span class="d-block text-muted" style="font-size: .75rem;">Masuk sebagai</span>
                                <strong class="d-block text-dark" style="font-size: .85rem;">
                                    {{ $user->email ?? '-' }}
                                </strong>
                            </div>
                            <a href="#confirmPasswordModal" class="dropdown-item">
                                <i class="fa fa-key me-2"></i> Reset Password
                            </a>
                            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                @csrf
                                <button type="button" class="dropdown-item text-danger" onclick="confirmLogout()">
                                    <i class="fa fa-sign-out-alt me-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</div>

@include('backend.components.style-confirm')

<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Konfirmasi Logout',
            text: 'Apakah Anda yakin ingin keluar dari panel ini?',
            icon: 'question',
            iconColor: '{{ $accent }}',
            showCancelButton: true,
            confirmButtonColor: '{{ $accent }}',
            cancelButtonColor: '#94A3B8',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
