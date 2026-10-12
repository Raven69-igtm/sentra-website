<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; {{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }} Dinsos</title>

    @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($appLogo) }}">
    @else
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/assets_2/images/logo-sentra.png') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('admin/assets_dinsos/css/dinsos.css') }}">

    @include('backend.layout.page_admin.reset_password.styles')
    @stack('styles')
</head>

<body class="dinsos-body">
    @php
        $notifQuery = \App\Models\Notifikasi::where('id_akun', Auth::id())
            ->where('tipe', 'admin')
            ->where('status', 'terkirim');
        $notifCount = (clone $notifQuery)->count();
        $notifItems = (clone $notifQuery)->latest('created_at')->limit(5)->get();
        $activeLayanan = request()->routeIs('dinsos.layanan')
            ? str_replace('-', '_', (string) request()->route('layanan'))
            : null;
        $pjserkOpen = request()->routeIs('dinsos.layanan') || request()->routeIs('dinsos.laporan.index');
    @endphp

    <div class="dinsos-app" id="dinsosApp">
        <aside class="dinsos-sidebar" id="dinsosSidebar">
            <div class="dinsos-brand">
                <div class="dinsos-brand-logo">
                    @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
                        <img src="{{ asset($appLogo) }}" alt="Logo" style="max-height: 38px; width: auto; object-fit: contain;">
                    @else
                        <img src="{{ asset('admin/assets_2/images/logo_nganjuk.png') }}"
                            alt="Logo Kabupaten Nganjuk">
                    @endif
                </div>
                <div>
                    <span class="dinsos-brand-title">{{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }} DINSOS</span>
                    <span class="dinsos-brand-sub">{{ \App\Models\SystemSetting::get('instansi_name', 'Dinas Sosial PPPA') }}</span>
                </div>
            </div>

            <div class="dinsos-menu-label">Main Menu</div>
            <a href="{{ route('dinsos.dashboard') }}"
                class="dinsos-menu-item {{ request()->routeIs('dinsos.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            <div class="dinsos-menu-label">PJSRK</div>
            <div class="dinsos-menu-group {{ $pjserkOpen ? 'is-open' : '' }}" id="pjserkGroup">
                <button type="button" class="dinsos-menu-item" id="pjserkToggle">
                    <i class="fa-regular fa-folder-open"></i>
                    <span>Laporan PJSRK</span>
                    <i class="fa-solid fa-chevron-down menu-chevron"></i>
                </button>
                <div class="dinsos-submenu">
                    <a href="{{ route('dinsos.layanan', 'rehabilitasi-sosial') }}"
                        class="{{ $activeLayanan === 'rehabilitasi_sosial' ? 'active' : '' }}">
                        Rehabilitasi Sosial
                    </a>
                    <a href="{{ route('dinsos.layanan', 'pengaduan-bencana') }}"
                        class="{{ $activeLayanan === 'pengaduan_bencana' ? 'active' : '' }}">
                        Pengaduan Bencana
                    </a>
                    <a href="{{ route('dinsos.layanan', 'permasalahan-sosial') }}"
                        class="{{ $activeLayanan === 'permasalahan_sosial' ? 'active' : '' }}">
                        Permasalahan Sosial
                    </a>
                </div>
            </div>

            <div class="dinsos-menu-label">Lainnya</div>
            <a href="{{ route('rekapan') }}" class="dinsos-menu-item">
                <i class="fa-regular fa-rectangle-list"></i>
                <span>Rekapan</span>
            </a>
            <a href="{{ route('informasi.index') }}" class="dinsos-menu-item">
                <i class="fa-regular fa-circle-info"></i>
                <span>Informasi</span>
            </a>
            <a href="{{ route('chat.index') }}" class="dinsos-menu-item">
                <i class="fa-regular fa-comment-dots"></i>
                <span>Chat</span>
            </a>
        </aside>

        <div class="dinsos-main">
            <header class="dinsos-topbar">
                <button type="button" class="dinsos-hamburger" id="dinsosHamburger" aria-label="Buka menu">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="dinsos-topbar-right">
                    <div class="dinsos-profile">
                        <button type="button" class="dinsos-icon-btn" id="notifToggle" aria-label="Notifikasi">
                            <i class="fa-regular fa-bell"></i>
                            @if ($notifCount > 0)
                                <span class="dinsos-notif-dot">{{ $notifCount > 9 ? '9+' : $notifCount }}</span>
                            @endif
                        </button>
                        <div class="dinsos-notif-panel" id="notifPanel">
                            <div class="dinsos-notif-head">
                                <span>Notifikasi</span>
                                <span class="dinsos-badge is-waiting">{{ $notifCount }} baru</span>
                            </div>
                            @forelse ($notifItems as $notif)
                                <div class="dinsos-notif-item">
                                    <strong>{{ $notif->judul }}</strong>
                                    <span>{{ $notif->pesan }}</span>
                                </div>
                            @empty
                                <div class="dinsos-notif-item">
                                    <span>Tidak ada notifikasi baru.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="dinsos-profile">
                        <button type="button" class="dinsos-profile-btn" id="profileToggle">
                            <span class="dinsos-avatar">{{ strtoupper(substr(Auth::user()->nama ?? 'A', 0, 1)) }}</span>
                            <span class="dinsos-profile-name">{{ Auth::user()->nama ?? 'Admin' }}</span>
                            <i class="fa-solid fa-chevron-down" style="font-size: 9px; color: #8a94a6;"></i>
                        </button>
                        <div class="dinsos-dropdown" id="profileDropdown">
                            <a href="#confirmPasswordModal">
                                <i class="fa-solid fa-key"></i>
                                <span>Reset Password</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="dinsos-content">
                @if (session('success'))
                    <div class="dinsos-alert is-success">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="dinsos-alert is-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="dinsos-alert is-error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="dinsos-footer">
                &copy; Copyright SENTRA {{ date('Y') }}
            </footer>
        </div>
    </div>

    @include('backend.layout.page_admin.reset_password.reset-password')

    <script>
        (function () {
            const app = document.getElementById('dinsosApp');
            const sidebar = document.getElementById('dinsosSidebar');
            const hamburger = document.getElementById('dinsosHamburger');
            const pjserkToggle = document.getElementById('pjserkToggle');
            const pjserkGroup = document.getElementById('pjserkGroup');
            const notifToggle = document.getElementById('notifToggle');
            const notifPanel = document.getElementById('notifPanel');
            const profileToggle = document.getElementById('profileToggle');
            const profileDropdown = document.getElementById('profileDropdown');

            hamburger?.addEventListener('click', function () {
                if (window.innerWidth > 900) {
                    app.classList.toggle('sidebar-collapsed');
                } else {
                    app.classList.toggle('sidebar-open');
                }
            });

            pjserkToggle?.addEventListener('click', function () {
                pjserkGroup.classList.toggle('is-open');
            });

            function togglePanel(panel) {
                document.querySelectorAll('.dinsos-notif-panel, .dinsos-dropdown').forEach(function (item) {
                    if (item !== panel) {
                        item.classList.remove('is-open');
                    }
                });
                panel?.classList.toggle('is-open');
            }

            notifToggle?.addEventListener('click', function (event) {
                event.stopPropagation();
                togglePanel(notifPanel);
            });

            profileToggle?.addEventListener('click', function (event) {
                event.stopPropagation();
                togglePanel(profileDropdown);
            });

            document.addEventListener('click', function () {
                document.querySelectorAll('.dinsos-notif-panel, .dinsos-dropdown').forEach(function (item) {
                    item.classList.remove('is-open');
                });
            });

            document.querySelectorAll('.dinsos-modal').forEach(function (modal) {
                modal.addEventListener('click', function (event) {
                    if (event.target === modal || event.target.classList.contains('dinsos-modal-overlay')) {
                        modal.classList.remove('is-open');
                    }
                });
            });
        })();
    </script>

    @include('backend.layout.page_admin.reset_password.scripts')
    @stack('scripts')
</body>

</html>
