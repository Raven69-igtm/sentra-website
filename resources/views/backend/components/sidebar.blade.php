<div class="quixnav">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">
            @if (Auth::check() && Auth::user()->isAdminDinsos())
                @php
                    $layananAktif = request()->routeIs('dinsos.layanan') ? (string) request()->route('layanan') : null;
                    $laporanAktif = request()->routeIs('dinsos.laporan.*') || request()->routeIs('dinsos.layanan');
                @endphp

                <li class="nav-label first text-uppercase fs-6 fw-bold text-white">
                    Menu Admin Dinsos
                </li>

                <li class="{{ request()->routeIs('dinsos.dashboard') ? 'mm-active' : '' }}">
                    <a href="{{ route('dinsos.dashboard') }}" aria-expanded="false"
                        class="d-flex align-items-center fs-5">
                        <i class="ti-home me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="{{ $laporanAktif ? 'mm-active' : '' }}">
                    <a class="has-arrow d-flex align-items-center fs-5" href="javascript:void()"
                        aria-expanded="{{ $laporanAktif ? 'true' : 'false' }}">
                        <i class="ti-agenda me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Laporan PJSRK</span>
                    </a>
                    <ul aria-expanded="{{ $laporanAktif ? 'true' : 'false' }}"
                        class="{{ $laporanAktif ? 'in' : '' }}">
                        <li>
                            <a href="{{ route('dinsos.laporan.index') }}"
                                class="custom-font-sidebar fs-6 {{ request()->routeIs('dinsos.laporan.index') ? 'mm-active' : '' }}">
                                Semua Laporan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dinsos.layanan', 'rehabilitasi-sosial') }}"
                                class="custom-font-sidebar fs-6 {{ $layananAktif === 'rehabilitasi-sosial' ? 'mm-active' : '' }}">
                                Rehabilitasi Sosial
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dinsos.layanan', 'pengaduan-bencana') }}"
                                class="custom-font-sidebar fs-6 {{ $layananAktif === 'pengaduan-bencana' ? 'mm-active' : '' }}">
                                Pengaduan Bencana
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dinsos.layanan', 'permasalahan-sosial') }}"
                                class="custom-font-sidebar fs-6 {{ $layananAktif === 'permasalahan-sosial' ? 'mm-active' : '' }}">
                                Permasalahan Sosial
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="{{ request()->routeIs('rekapan') ? 'mm-active' : '' }}">
                    <a href="{{ route('rekapan') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                        <i class="ti-folder me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Rekapan</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('informasi.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('informasi.index') }}" aria-expanded="false"
                        class="d-flex align-items-center fs-5">
                        <i class="ti-info-alt me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Informasi</span>
                    </a>
                </li>

                <li class="{{ request()->routeIs('chat.*') ? 'mm-active' : '' }}">
                    <a href="{{ route('chat.index') }}" aria-expanded="false"
                        class="d-flex align-items-center fs-5">
                        <i class="ti-comment-alt me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Chat</span>
                        @php
                            $unreadChat = \App\Models\ChatMobile::where('receiver_id', \Illuminate\Support\Facades\Auth::id())
                                ->where('is_read', 0)
                                ->count();
                        @endphp
                        @if ($unreadChat > 0)
                            <span class="badge bg-danger text-white rounded-pill ms-auto me-2"
                                id="sidebar-chat-badge" style="font-size: 0.7rem;">{{ $unreadChat }}</span>
                        @else
                            <span class="badge bg-danger text-white rounded-pill ms-auto me-2"
                                id="sidebar-chat-badge" style="font-size: 0.7rem; display: none;"></span>
                        @endif
                    </a>
                </li>
            @else
                <li class="nav-label first text-uppercase fs-6 fw-bold text-white">
                    @if (Auth::check() && Auth::user()->isAdminPPA())
                        Menu Admin PPA
                    @else
                        Main Menu
                    @endif
                </li>

                <li>
                    <a href="{{ route('dashboard') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                        <i class="ti-home me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a class="has-arrow d-flex align-items-center fs-5" href="javascript:void()" aria-expanded="false">
                        <i class="ti-agenda me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Laporan</span>
                    </a>
                    <ul aria-expanded="false">
                        <li><a href="{{ route('laporan_proses') }}" class="custom-font-sidebar fs-6">Diproses</a></li>
                        <li><a href="{{ route('selesai') }}" class="custom-font-sidebar fs-6">Selesai</a></li>
                    </ul>
                </li>

                <li>
                    <a href="{{ route('rekapan') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                        <i class="ti-folder me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Rekapan</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('informasi.index') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                        <i class="ti-info-alt me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Informasi</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('chat.index') }}" aria-expanded="false" class="d-flex align-items-center fs-5">
                        <i class="ti-comment-alt me-2 icon-lg"></i>
                        <span class="custom-font-sidebar nav-text">Chat</span>
                        @php
                            $unreadChat = \App\Models\ChatMobile::where('receiver_id', \Illuminate\Support\Facades\Auth::id())
                                ->where('is_read', 0)
                                ->count();
                        @endphp
                        @if ($unreadChat > 0)
                            <span class="badge bg-danger text-white rounded-pill ms-auto me-2"
                                id="sidebar-chat-badge" style="font-size: 0.7rem;">{{ $unreadChat }}</span>
                        @else
                            <span class="badge bg-danger text-white rounded-pill ms-auto me-2"
                                id="sidebar-chat-badge" style="font-size: 0.7rem; display: none;"></span>
                        @endif
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
