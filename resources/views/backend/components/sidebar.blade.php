@php
    $user = Auth::user();
    $isDinsos = $user && $user->isAdminDinsos();

    // Badge chat belum dibaca (target AJAX memakai id yang sama)
    $unreadChat = \App\Models\ChatMobile::where('receiver_id', $user->id ?? 0)
        ->where('is_read', 0)
        ->count();
@endphp

<div class="quixnav">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">
            @if ($isDinsos)
                @php
                    $layananAktif = request()->routeIs('dinsos.layanan') ? (string) request()->route('layanan') : null;
                    $laporanAktif = request()->routeIs('dinsos.laporan.*') || request()->routeIs('dinsos.layanan');
                @endphp

                {{-- SEKSI: MENU UTAMA --}}
                <li class="sidebar-section-label">Menu Utama</li>

                <li class="{{ request()->routeIs('dinsos.dashboard') ? 'mm-active' : '' }}">
                    <a href="{{ route('dinsos.dashboard') }}" aria-expanded="false" title="Dashboard">
                        <i class="fa fa-gauge-high"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <li class="{{ $laporanAktif ? 'mm-active' : '' }}">
                    <a class="has-arrow" href="javascript:void()" title="Laporan PJSRK"
                        aria-expanded="{{ $laporanAktif ? 'true' : 'false' }}">
                        <i class="fa fa-file-shield"></i>
                        <span class="nav-text">Laporan PJSRK</span>
                    </a>
                    <ul aria-expanded="{{ $laporanAktif ? 'true' : 'false' }}" class="{{ $laporanAktif ? 'in' : '' }}">
                        <li>
                            <a href="{{ route('dinsos.laporan.index') }}"
                                class="{{ request()->routeIs('dinsos.laporan.index') ? 'mm-active' : '' }}">
                                Semua Laporan
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dinsos.layanan', 'rehabilitasi-sosial') }}"
                                class="{{ $layananAktif === 'rehabilitasi-sosial' ? 'mm-active' : '' }}">
                                Rehabilitasi Sosial
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dinsos.layanan', 'pengaduan-bencana') }}"
                                class="{{ $layananAktif === 'pengaduan-bencana' ? 'mm-active' : '' }}">
                                Pengaduan Bencana
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('dinsos.layanan', 'permasalahan-sosial') }}"
                                class="{{ $layananAktif === 'permasalahan-sosial' ? 'mm-active' : '' }}">
                                Permasalahan Sosial
                            </a>
                        </li>
                    </ul>
                </li>
            @else
                {{-- SEKSI: MENU UTAMA (Admin PPA) --}}
                <li class="sidebar-section-label">Menu Utama</li>

                <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                    <a href="{{ route('dashboard') }}" aria-expanded="false" title="Dashboard">
                        <i class="fa fa-gauge-high"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                @php
                    $laporanAktif = request()->routeIs('laporan_proses') || request()->routeIs('selesai') ||
                        request()->routeIs('laporan.show') || request()->routeIs('laporan.edit');
                @endphp

                <li class="{{ $laporanAktif ? 'mm-active' : '' }}">
                    <a class="has-arrow" href="javascript:void()" title="Laporan"
                        aria-expanded="{{ $laporanAktif ? 'true' : 'false' }}">
                        <i class="fa fa-clipboard-list"></i>
                        <span class="nav-text">Laporan</span>
                    </a>
                    <ul aria-expanded="{{ $laporanAktif ? 'true' : 'false' }}" class="{{ $laporanAktif ? 'in' : '' }}">
                        <li>
                            <a href="{{ route('laporan_proses') }}"
                                class="{{ request()->routeIs('laporan_proses') ? 'mm-active' : '' }}">
                                Diproses
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('selesai') }}"
                                class="{{ request()->routeIs('selesai') ? 'mm-active' : '' }}">
                                Selesai
                            </a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- SEKSI: LAYANAN (sama untuk kedua instansi) --}}
            <li class="sidebar-section-label">Layanan</li>

            <li class="{{ request()->routeIs('rekapan') ? 'mm-active' : '' }}">
                <a href="{{ route('rekapan') }}" aria-expanded="false" title="Rekapan">
                    <i class="fa fa-folder-open"></i>
                    <span class="nav-text">Rekapan</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('informasi.*') ? 'mm-active' : '' }}">
                <a href="{{ route('informasi.index') }}" aria-expanded="false" title="Informasi">
                    <i class="fa fa-circle-info"></i>
                    <span class="nav-text">Informasi</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('chat.*') ? 'mm-active' : '' }}">
                <a href="{{ route('chat.index') }}" aria-expanded="false" title="Chat">
                    <i class="fa fa-comments"></i>
                    <span class="nav-text">Chat</span>
                    {{-- ID dipertahankan: diperbarui oleh polling halaman chat --}}
                    <span class="badge badge-chat" id="sidebar-chat-badge"
                        style="{{ $unreadChat > 0 ? '' : 'display: none;' }}">{{ $unreadChat > 0 ? $unreadChat : '' }}</span>
                </a>
            </li>
        </ul>
    </div>
</div>
