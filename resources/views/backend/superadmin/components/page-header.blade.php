@php
    $pageTitle = $title ?? '';
    $pageSubtitle = $subtitle ?? '';
    $pageIcon = $icon ?? 'fa-circle';
    $pageActionUrl = $actionUrl ?? null;
    $pageActionLabel = $actionLabel ?? null;
    $pageActionIcon = $actionIcon ?? 'fa-plus';
    $pageActionClass = $actionClass ?? 'btn-oripio-primary';
@endphp

<div class="page-head d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center">
        <div class="page-head-icon">
            <i class="fa {{ $pageIcon }}"></i>
        </div>
        <div>
            <h2 class="page-head-title">{{ $pageTitle }}</h2>
            @if ($pageSubtitle)
                <p class="page-head-subtitle mb-0">{{ $pageSubtitle }}</p>
            @endif
        </div>
    </div>

    @if ($pageActionUrl && $pageActionLabel)
        <div class="mt-3 mt-md-0">
            <a href="{{ $pageActionUrl }}" wire:navigate class="btn {{ $pageActionClass }} btn-pill shadow-sm">
                <i class="fa {{ $pageActionIcon }} me-2"></i> {{ $pageActionLabel }}
            </a>
        </div>
    @endif
</div>
