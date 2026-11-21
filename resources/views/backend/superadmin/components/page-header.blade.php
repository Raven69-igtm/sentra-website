@php
    $pageTitle = $title ?? '';
    $pageSubtitle = $subtitle ?? '';
    $pageIcon = $icon ?? null;
    $pageActionUrl = $actionUrl ?? null;
    $pageActionLabel = $actionLabel ?? null;
    $pageActionIcon = $actionIcon ?? 'fa-plus';
    $pageActionClass = $actionClass ?? 'btn-jigate-primary';
@endphp

<div class="page-head d-flex flex-wrap justify-content-between align-items-center mb-4 pt-1">
    <div>
        <h2 class="fw-bold text-dark mb-1" style="font-size: 1.55rem; letter-spacing: -0.02em; color: #0F172A;">
            {{ $pageTitle }}
        </h2>
        @if ($pageSubtitle)
            <p class="text-muted mb-0" style="font-size: 0.88rem; color: #64748B;">{{ $pageSubtitle }}</p>
        @endif
    </div>

    @if ($pageActionUrl && $pageActionLabel)
        <div class="mt-3 mt-md-0">
            <a href="{{ $pageActionUrl }}" wire:navigate class="btn {{ $pageActionClass }}">
                <i class="fa {{ $pageActionIcon }} me-1" style="font-size: 0.82rem;"></i> {{ $pageActionLabel }}
            </a>
        </div>
    @endif
</div>
