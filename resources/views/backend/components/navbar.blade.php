        {{-- NAV --}}
        <div class="nav-header">
            <a class="brand-logo">
                <img class="logo-abbr" src="{{ asset('admin/assets_2/images/logo_nganjuk.png') }}" alt="">
                <img class="logo-compact" src="{{ asset('admin/assets_2/images/dinsos.png') }}" alt="">
                @if (Auth::check() && Auth::user()->isAdminDinsos())
                    <span class="brand-title brand-title-text">DINAS SOSIAL</span>
                @else
                    <img class="brand-title" src="{{ asset('admin/assets_2/images/dinsos.png') }}" alt="">
                @endif
            </a>

            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>

        {{-- HEADER --}}
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left"></div>
                        <ul class="navbar-nav header-right">
                            <li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link" href="#" role="button" data-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="mdi mdi-bell mdi-24px"></i>
                                    <span id="notification-badge" class="badge badge-danger"
                                        style="display: none;"></span>
                                    <div id="notification-pulse" class="pulse-css" style="display: none;"></div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right p-0"
                                    style="width: 100%; max-width: 350px;">
                                    <div id="notification-header" class="bg-ijo text-white text-center py-2 px-3">
                                        <strong>Notifikasi</strong>
                                        <span id="notification-count-header" class="badge badge-danger ml-2"
                                            style="display: none;"></span>
                                    </div>
                                    <div id="notification-list" class="notification-list"
                                        style="max-height: 300px; overflow-y: auto;">
                                        <!-- Isi akan diisi oleh AJAX -->
                                    </div>
                                    <div id="notification-footer" class="text-center p-2 border-top"
                                        style="display: none;">
                                        <a href="#" data-toggle="modal" data-target="#allNotificationsModal"
                                            class="small">Lihat Semua Notifikasi</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown header-profile">
                                <a class="nav-link d-flex align-items-center" href="#" role="button"
                                    data-toggle="dropdown">
                                    <i class="fas fa-user-circle fa-lg me-1"></i>
                                    <span class="custom-font">
                                        @if (Auth::check())
                                            {{ Auth::user()->nama }}
                                            @if (Auth::user()->isSuperAdmin())
                                                <span class="badge badge-danger ml-1" style="font-size: 0.7rem;">Superadmin</span>
                                            @elseif (Auth::user()->isAdminDinsos())
                                                <span class="badge badge-warning text-white ml-1" style="font-size: 0.7rem;">Admin Dinsos</span>
                                            @elseif (Auth::user()->isAdminPPA())
                                                <span class="badge badge-primary ml-1" style="font-size: 0.7rem;">Admin PPA</span>
                                            @endif
                                        @else
                                            Admin
                                        @endif
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a href="#confirmPasswordModal" class="dropdown-item">
                                        <i class="fas fa-key"></i>
                                        <span class="ml-2">Reset Password</span>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" id="logout-form">
                                        @csrf
                                        <button type="button" class="dropdown-item" onclick="confirmLogout()">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span class="ml-2">Logout</span>
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
                    title: 'Konfirmasi',
                    text: 'Apakah Anda yakin ingin logout?',
                    icon: 'question',
                    iconColor: '#059652',
                    showCancelButton: true,
                    confirmButtonColor: '#059652',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Logout',
                    cancelButtonText: 'Batal',
                    buttonsStyling: true,
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('logout-form').submit();
                    }
                });
            }
        </script>
