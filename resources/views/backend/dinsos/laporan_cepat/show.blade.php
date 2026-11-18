@extends('backend.layout.admin_layout')

@section('title', 'Detail Laporan Cepat PJSRK')

@section('admin')
    {{-- Detail satu laporan cepat PJSRK. Isinya hanya data pelapor + kasus,
         karena form ringkas tidak mengirim detail_pjsrk. --}}
    @php
        $pelapor = $laporan->detail_pelapor;
        $kasus = $laporan->detail_kasus;

        // created_at tersimpan UTC; tampilkan dalam WIB.
        $masuk = $laporan->created_at
            ? \Illuminate\Support\Carbon::parse($laporan->created_at)->timezone('Asia/Jakarta')
            : null;

        // Tempat kejadian diisi lokasi dari form cepat (bisa berupa alamat),
        // jadi fallback terakhir ke alamat pelapor.
        $tempat = trim((string) optional($kasus)->tempat_kejadian);
        $lokasi = $tempat !== '' && $tempat !== '-' ? $tempat : (optional($pelapor)->alamat ?: '-');
    @endphp

    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Detail Laporan Cepat',
            'subtitle' => 'Tiket ' . $laporan->id_laporan . ' — ' . $laporan->status_label,
            'icon' => 'fa-bolt',
            'actionUrl' => route('dinsos.laporan_cepat'),
            'actionLabel' => 'Kembali',
            'actionIcon' => 'fa-arrow-left',
            'actionClass' => 'btn-instansi-soft btn-pill',
        ])

        <div class="row">
            {{-- KOLOM KIRI: isi laporan --}}
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <div class="form-section">
                            <h5 class="form-section-title">Identitas Pelapor</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Nama Pelapor</span>
                                        <span class="detail-value">{{ optional($pelapor)->nama ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Nomor Telepon</span>
                                        <span class="detail-value">{{ optional($pelapor)->no_telp ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="detail-item">
                                        <span class="detail-label">Alamat Pelapor</span>
                                        <span class="detail-value">{{ optional($pelapor)->alamat ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h5 class="form-section-title">Detail Kasus</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Tanggal Kejadian</span>
                                        <span class="detail-value">
                                            {{ optional($kasus)->tanggal
                                                ? \Illuminate\Support\Carbon::parse($kasus->tanggal)->format('d-m-Y')
                                                : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Waktu Kejadian</span>
                                        <span class="detail-value">
                                            {{ optional($kasus)->waktu_kejadian
                                                ? \Illuminate\Support\Carbon::parse($kasus->waktu_kejadian)->format('H:i') . ' WIB'
                                                : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="detail-item">
                                        <span class="detail-label">Lokasi / Tempat Kejadian</span>
                                        <span class="detail-value">{{ $lokasi }}</span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="detail-item">
                                        <span class="detail-label">Kronologi / Deskripsi</span>
                                        <span class="detail-value" style="white-space: pre-line;">
                                            {{ optional($kasus)->kronologi ?: '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: ringkasan laporan --}}
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="form-section-title">Informasi Laporan</h5>

                        <div class="detail-item">
                            <span class="detail-label">No. Tiket</span>
                            <span class="detail-value fw-semibold">{{ $laporan->id_laporan }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Layanan</span>
                            <span class="detail-value">{{ $laporan->layanan_label }}</span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Status</span>
                            <span class="detail-value">
                                @include('backend.components.status-pill', ['status' => $laporan->status])
                            </span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Tanggal Masuk</span>
                            <span class="detail-value">
                                {{ optional($masuk)->format('d-m-Y H:i') ?? '-' }} WIB
                            </span>
                        </div>

                        <div class="detail-item">
                            <span class="detail-label">Sumber</span>
                            <span class="detail-value">Form Laporan Cepat (mobile)</span>
                        </div>

                        {{-- Tindak lanjut status tetap dilakukan dari halaman
                             Laporan PJSRK agar aturan verifikasi/proses hanya
                             ada di satu tempat. --}}
                        <a href="{{ route('dinsos.laporan.index', ['search' => $laporan->id_laporan]) }}" wire:navigate
                            class="btn btn-instansi-soft btn-pill w-100 mt-3">
                            <i class="fa fa-arrow-up-right-from-square"></i> Buka di Laporan PJSRK
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
