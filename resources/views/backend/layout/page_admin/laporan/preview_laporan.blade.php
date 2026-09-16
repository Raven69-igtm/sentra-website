@extends('backend.layout.admin_layout')

@section('title', 'Preview Laporan')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Preview Laporan',
            'subtitle' => 'Laporan ' . $laporan->id_laporan . ' — status ' . ucfirst($laporan->status),
            'icon' => 'fa-eye',
            'actionUrl' => route('selesai'),
            'actionLabel' => 'Kembali',
            'actionIcon' => 'fa-arrow-left',
            'actionClass' => 'btn-instansi-soft btn-pill',
        ])

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        {{-- INFORMASI LAPORAN --}}
                        <div class="form-section">
                            <h5 class="form-section-title">Informasi Laporan</h5>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="detail-item">
                                        <span class="detail-label">ID Laporan</span>
                                        <span class="detail-value">{{ $laporan->id_laporan }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-item">
                                        <span class="detail-label">Kategori Laporan</span>
                                        <span
                                            class="detail-value">{{ $laporan->kategori != 'unset' ? $laporan->kategori : '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="detail-item">
                                        <span class="detail-label">Tanggal</span>
                                        <span class="detail-value">{{ $laporan->created_at->format('d-m-Y') }}</span>
                                    </div>
                                </div>
                                @if ($laporan->layanan_pjsrk)
                                    <div class="col-md-4">
                                        <div class="detail-item">
                                            <span class="detail-label">Layanan PJSRK</span>
                                            <span
                                                class="detail-value">{{ \App\Models\Laporan::LAYANAN_PJSRK[$laporan->layanan_pjsrk] ?? $laporan->layanan_pjsrk }}</span>
                                        </div>
                                    </div>
                                @endif
                                @if ($laporan->judul)
                                    <div class="col-md-4">
                                        <div class="detail-item">
                                            <span class="detail-label">Judul Laporan</span>
                                            <span class="detail-value">{{ $laporan->judul }}</span>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <div class="detail-item">
                                        <span class="detail-label">Status</span>
                                        <span class="detail-value">
                                            @include('backend.components.status-pill', [
                                                'status' => $laporan->status,
                                            ])
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- IDENTITAS PELAPOR --}}
                        <div class="form-section">
                            <h5 class="form-section-title">Identitas Pelapor</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">NIK Pelapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_pelapor)->nik ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Nama Pelapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_pelapor)->nama ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Alamat Pelapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_pelapor)->alamat ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Hubungan dengan Korban</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_pelapor)->hubungan_dengan_korban ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Nomor Telepon Pelapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_pelapor)->no_telp ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PENERIMA MANFAAT --}}
                        <div class="form-section">
                            <h5 class="form-section-title">Penerima Manfaat</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">NIK Penerima Manfaat</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->nik ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Nama Penerima Manfaat</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->nama ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Tempat Lahir</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->tempat_lahir ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Tanggal Lahir</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->tanggal_lahir ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Umur</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->umur ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Jenis Kelamin</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->jenis_kelamin ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Pekerjaan</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->pekerjaan ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Agama</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->agama ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Pendidikan</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->pendidikan ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Alamat</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->alamat ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Hubungan dengan Terlapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->hubungan_dengan_terlapor ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Nomor Telepon</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->notelp ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-item mb-0">
                                        <span class="detail-label">Informasi Tambahan</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_penerima_manfaat)->informasi_tambahan ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- INFORMASI ANAK --}}
                        @if ($laporan->detail_penerima_manfaat && $laporan->detail_penerima_manfaat->informasi_anak->count() > 0)
                            <div class="form-section">
                                <h5 class="form-section-title">Informasi Anak</h5>
                                @foreach ($laporan->detail_penerima_manfaat->informasi_anak as $index => $anak)
                                    <h6 class="anak-block-title">Anak ke-{{ $index + 1 }}</h6>
                                    <div class="row mb-4 anak-block">
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Nama Anak</span>
                                                <span class="detail-value">{{ $anak->nama ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Tanggal Lahir</span>
                                                <span class="detail-value">{{ $anak->tanggal_lahir ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Umur</span>
                                                <span class="detail-value">{{ $anak->umur ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Jenis Kelamin</span>
                                                <span class="detail-value">{{ $anak->jenis_kelamin ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Pendidikan</span>
                                                <span class="detail-value">{{ $anak->pendidikan ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Agama</span>
                                                <span class="detail-value">{{ $anak->agama ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item mb-0">
                                                <span class="detail-label">Status</span>
                                                <span class="detail-value">{{ $anak->status ?: '-' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- TERLAPOR --}}
                        <div class="form-section">
                            <h5 class="form-section-title">Terlapor</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">NIK Terlapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_terlapor)->nik ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Nama Terlapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_terlapor)->nama ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Umur</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_terlapor)->umur ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Alamat</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_terlapor)->alamat ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Jenis Kelamin</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_terlapor)->jenis_kelamin ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Hubungan dengan Korban</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_terlapor)->hubungan_dengan_korban ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-item mb-0">
                                        <span class="detail-label">Informasi Tambahan</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_terlapor)->informasi_tambahan ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DETAIL KASUS --}}
                        <div class="form-section">
                            <h5 class="form-section-title">Detail Kasus</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Tanggal Kejadian</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_kasus)->tanggal ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Tempat Kejadian</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_kasus)->tempat_kejadian ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-item mb-0">
                                        <span class="detail-label">Kronologi Kejadian</span>
                                        <div class="detail-text">
                                            {!! nl2br(e(optional($laporan->detail_kasus)->kronologi)) ?: '<span class="text-muted">-</span>' !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
