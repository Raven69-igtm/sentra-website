@php
    // Peta status → warna semantik (tidak mengikuti aksen instansi)
    $statusMap = [
        'dikirim' => ['label' => 'Dikirim', 'class' => 'is-amber'],
        'diterima' => ['label' => 'Diterima', 'class' => 'is-amber'],
        'diproses' => ['label' => 'Diproses', 'class' => 'is-blue'],
        'selesai' => ['label' => 'Selesai', 'class' => 'is-green'],
        'dirujuk' => ['label' => 'Dirujuk', 'class' => 'is-slate'],
        'ditolak' => ['label' => 'Ditolak', 'class' => 'is-rose'],
    ];
    $meta = $statusMap[$status ?? ''] ?? ['label' => $status ? ucfirst($status) : '-', 'class' => 'is-slate'];
@endphp

<span class="status-pill {{ $meta['class'] }}">{{ $meta['label'] }}</span>
