{{-- Panel notifikasi topbar Admin Dinsos (Livewire, polling 10 detik). --}}
<div class="dinsos-profile" wire:poll.10s wire:click.outside="close">
    <button type="button" class="dinsos-icon-btn" wire:click.stop="toggle" aria-label="Notifikasi">
        <i class="fa-regular fa-bell"></i>
        @if ($count > 0)
            <span class="dinsos-notif-dot">{{ $count > 9 ? '9+' : $count }}</span>
        @endif
    </button>

    <div class="dinsos-notif-panel {{ $open ? 'is-open' : '' }}" id="notifPanel" wire:click.stop>
        <div class="dinsos-notif-head">
            <span>Notifikasi</span>
            <span class="dinsos-badge is-waiting">{{ $count }} baru</span>
        </div>

        @forelse ($notifikasi as $notif)
            <div class="dinsos-notif-item" wire:key="notif-{{ $notif->id_notif }}">
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
