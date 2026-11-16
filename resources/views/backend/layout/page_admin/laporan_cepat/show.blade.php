@extends('backend.layout.admin_layout')

@section('title', 'Detail Laporan Cepat')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Detail Laporan Cepat',
            'subtitle' => 'Laporan ' . $laporan->id_laporan . ' — status ' . ucfirst($laporan->status),
            'icon' => 'fa-bolt',
            'actionUrl' => route('laporan_cepat'),
            'actionLabel' => 'Kembali',
            'actionIcon' => 'fa-arrow-left',
            'actionClass' => 'btn-instansi-soft btn-pill',
        ])

        <div class="card">
            <div class="card-body">
                {{-- Reuse partial detail Laporan PPA existing agar tampilan dan
                     penanganan nilai kosong (penerima manfaat/terlapor) konsisten.
                     Partial ini juga dipakai modal preview di halaman Laporan. --}}
                @include('backend.layout.page_admin.laporan.preview_content', ['laporan' => $laporan])
            </div>
        </div>
    </div>
@endsection
