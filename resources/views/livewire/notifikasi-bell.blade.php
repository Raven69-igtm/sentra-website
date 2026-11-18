{{-- Lonceng notifikasi panel Admin PPA (Livewire, polling 10 detik). --}}
<li class="nav-item dropdown notification_dropdown {{ $open ? 'show' : '' }}" wire:poll.10s wire:click.outside="close"
    x-on:notif-terima.window="window.Swal && Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: $event.detail.message, showConfirmButton: false, timer: 2500, timerProgressBar: true })">
    <a class="topbar-icon-btn" href="#" role="button" wire:click.prevent="toggle"
        aria-expanded="{{ $open ? 'true' : 'false' }}" title="Notifikasi">
        <i class="fa-regular fa-bell"></i>
        @if ($count > 0)
            <span id="notification-badge" class="icon-badge">{{ $count }}</span>
            <span id="notification-pulse" class="pulse-css"></span>
        @endif
    </a>

    <div class="dropdown-menu dropdown-menu-right {{ $open ? 'show' : '' }}">
        <div class="notif-head">
            <strong>Notifikasi</strong>
            @if ($count > 0)
                <span id="notification-count-header" class="badge badge-danger">{{ $count }} baru</span>
            @endif
        </div>

        <div id="notification-list" class="notification-list">
            @forelse ($notifikasi as $notif)
                <div class="media dropdown-item align-items-start py-2 px-3 border-bottom" wire:key="notif-{{ $notif->id_notif }}">
                    <span class="success mr-2"><i class="fa fa-user"></i></span>
                    <div class="media-body" style="width: 100%;">
                        <p class="mb-1 font-weight-bold" style="font-size: 0.9rem;">{{ $notif->judul }}</p>
                        <p class="mb-2 text-muted" style="font-size: 0.85rem;">{{ $notif->pesan }}</p>

                        @if (! empty($laporanIds[$notif->id_notif]))
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-sm btn-hijau"
                                    wire:click="terimaLaporan({{ $notif->id_notif }})" wire:loading.attr="disabled">
                                    TERIMA
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="notif-empty">Tidak ada notifikasi baru</div>
            @endforelse
        </div>

        @if ($count > 0)
            <div id="notification-footer" class="notif-foot">
                <a href="#" wire:click.prevent="openAll">Lihat semua notifikasi</a>
            </div>
        @endif
    </div>

    {{-- Modal dipindah ke <body> agar tidak terpotong stacking context navbar. --}}
    @teleport('body')
        <div class="modal fade {{ $showAll ? 'show' : '' }}" id="allNotificationsModal" tabindex="-1" role="dialog"
            aria-labelledby="notificationModalLabel" aria-hidden="{{ $showAll ? 'false' : 'true' }}"
            @if ($showAll) style="display: block;" @endif>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notificationModalLabel">
                            <i class="fa fa-bell me-2"></i>Daftar Notifikasi
                        </h5>
                        <button type="button" class="close" wire:click="closeAll" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Pesan</th>
                                        <th>Waktu</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="notification-table-body">
                                    @forelse ($semua as $index => $notif)
                                        <tr wire:key="notif-all-{{ $notif->id_notif }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $notif->judul }}</td>
                                            <td>{{ $notif->pesan }}</td>
                                            <td>{{ $notif->created_at ? $notif->created_at->format('d M Y H:i') : '-' }}</td>
                                            <td>
                                                @if (! empty($laporanIds[$notif->id_notif]))
                                                    <button type="button" class="btn btn-sm btn-hijau"
                                                        wire:click="terimaLaporan({{ $notif->id_notif }})"
                                                        wire:loading.attr="disabled">
                                                        TERIMA
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-sm btn-info"
                                                        wire:click="tandaiDibaca({{ $notif->id_notif }})"
                                                        wire:loading.attr="disabled">
                                                        Tandai Dibaca
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada notifikasi yang belum dibaca</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-pill" wire:click="closeAll">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        @if ($showAll)
            <div class="modal-backdrop fade show" wire:click="closeAll"></div>
        @endif
    @endteleport
</li>
