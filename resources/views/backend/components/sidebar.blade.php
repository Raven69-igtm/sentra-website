<div class="quixnav">
    <div class="quixnav-scroll">
        <ul class="metismenu" id="menu">
            <li class="nav-label first text-uppercase fs-6 fw-bold text-white">
                @if (Auth::check() && Auth::user()->isAdminDinsos())
                    Menu Admin Dinsos
                @elseif (Auth::check() && Auth::user()->isAdminPPA())
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
                        <span class="badge bg-danger text-white rounded-pill ms-auto me-2" id="sidebar-chat-badge" style="font-size: 0.7rem;">{{ $unreadChat }}</span>
                    @else
                        <span class="badge bg-danger text-white rounded-pill ms-auto me-2" id="sidebar-chat-badge" style="font-size: 0.7rem; display: none;"></span>
                    @endif
                </a>
            </li>
        </ul>
    </div>
</div>
