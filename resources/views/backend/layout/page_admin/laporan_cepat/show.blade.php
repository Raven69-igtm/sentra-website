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

        {{-- Bar Aksi Operasional (sinkron dengan alur status Mobile & /laporan) --}}
        @if (in_array($laporan->status, ['dikirim', 'diterima', 'diproses', 'dirujuk'], true))
            <div class="card mb-4">
                <div class="card-body d-flex flex-wrap align-items-center justify-content-between" style="gap: 12px;">
                    <div>
                        <span class="mr-2 font-weight-bold">Tindak Lanjut Laporan Cepat:</span>
                        @include('backend.components.status-pill', ['status' => $laporan->status])
                    </div>
                    <div class="d-flex flex-wrap" style="gap: 8px;">
                        @if ($laporan->status === 'dikirim')
                            <form action="{{ route('laporan.terima', $laporan->id_laporan) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-warning btn-pill btn-sm">
                                    <i class="fa fa-check mr-1"></i> Terima Laporan
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('laporan.edit', $laporan->id_laporan) }}" wire:navigate
                            class="btn btn-instansi-primary btn-pill btn-sm">
                            <i class="fa fa-pen-to-square mr-1"></i> Proses &amp; Lengkapi Data
                        </a>

                        @if ($laporan->status === 'diproses')
                            <form action="{{ route('laporan.rujuk', $laporan->id_laporan) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Rujuk laporan {{ $laporan->id_laporan }} ke instansi terkait?');">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-instansi-soft btn-pill btn-sm">
                                    <i class="fa fa-share mr-1"></i> Rujuk
                                </button>
                            </form>
                        @endif

                        @if (in_array($laporan->status, ['diproses', 'dirujuk'], true))
                            <form action="{{ route('laporan.selesai', $laporan->id_laporan) }}" method="POST"
                                style="display:inline;"
                                onsubmit="return confirm('Tandai laporan {{ $laporan->id_laporan }} selesai?');">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-hijau btn-pill btn-sm">
                                    <i class="fa fa-circle-check mr-1"></i> Selesai
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endif

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
