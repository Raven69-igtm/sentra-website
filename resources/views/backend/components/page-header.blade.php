@php
    $pageTitle = $title ?? '';
    $pageSubtitle = $subtitle ?? '';
    $pageIcon = $icon ?? 'fa-circle';
    $pageActions = $actions ?? [];
    $pageActionUrl = $actionUrl ?? null;
    $pageActionLabel = $actionLabel ?? null;
    $pageActionIcon = $actionIcon ?? 'fa-plus';
    $pageActionClass = $actionClass ?? 'btn-instansi-primary';
@endphp

{{-- Header halaman instansi: ikon + judul + subtitle + slot aksi.
     Menggantikan pola page-titles + breadcrumb pada halaman lama. --}}
<div class="page-head d-flex flex-wrap justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <div class="page-head-icon">
            <i class="fa {{ $pageIcon }}"></i>
        </div>
        <div>
            <h2 class="page-head-title">{{ $pageTitle }}</h2>
            @if ($pageSubtitle)
                <p class="page-head-subtitle">{{ $pageSubtitle }}</p>
            @endif
        </div>
    </div>

    <div class="page-head-actions d-flex flex-wrap align-items-center">
        @if ($pageActionUrl && $pageActionLabel)
            <a href="{{ $pageActionUrl }}" class="btn {{ $pageActionClass }} btn-pill">
                <i class="fa {{ $pageActionIcon }} me-2"></i> {{ $pageActionLabel }}
            </a>
        @endif

        @foreach ($pageActions as $action)
            <a href="{{ $action['url'] ?? '#' }}"
                class="btn {{ $action['class'] ?? 'btn-instansi-soft' }} btn-pill">
                @if (!empty($action['icon']))
                    <i class="fa {{ $action['icon'] }} me-2"></i>
                @endif
                {{ $action['label'] ?? '' }}
            </a>
        @endforeach

        @if (!empty($slot))
            {{ $slot }}
        @endif
    </div>
</div>
