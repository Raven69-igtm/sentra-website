@extends('backend.layout.admin_layout')

@section('title', 'Detail Laporan')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Detail Laporan',
            'subtitle' => 'Tiket ' . $laporan->id_laporan . ' — ' . $laporan->status_label,
            'icon' => 'fa-file-circle-check',
            'actionUrl' => route('dinsos.laporan.index'),
            'actionLabel' => 'Kembali',
            'actionIcon' => 'fa-arrow-left',
            'actionClass' => 'btn-instansi-soft btn-pill',
        ])

        <div class="row">
            {{-- KOLOM KIRI: ISI LAPORAN --}}
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        @php $pj = $laporan->detail_pjsrk; @endphp

                        @if ($pj)
                            {{-- DATA PENGADUAN BENCANA (PJSRK) --}}
                            @if ($laporan->layanan_pjsrk === 'pengaduan_bencana')
                                <div class="form-section">
                                    <h5 class="form-section-title">Data Pengaduan Bencana</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Jenis Bencana</span>
                                                <span class="detail-value">{{ $pj->jenis_bencana ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Tingkat Dampak</span>
                                                <span class="detail-value">{{ $pj->tingkat_dampak ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Tanggal Kejadian</span>
                                                <span
                                                    class="detail-value">{{ optional($laporan->detail_kasus)->tanggal ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Jam Kejadian</span>
                                                <span
                                                    class="detail-value">{{ optional($laporan->detail_kasus)->waktu_kejadian ? \Illuminate\Support\Carbon::parse($laporan->detail_kasus->waktu_kejadian)->format('H:i') . ' WIB' : '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Jiwa Terdampak</span>
                                                <span
                                                    class="detail-value">{{ is_null($pj->jiwa_terdampak) ? '-' : $pj->jiwa_terdampak . ' jiwa' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Rumah Rusak</span>
                                                <span
                                                    class="detail-value">{{ is_null($pj->rumah_rusak) ? '-' : $pj->rumah_rusak . ' unit' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="detail-item">
                                                <span class="detail-label">Kebutuhan Mendesak</span>
                                                <div class="detail-value">
                                                    @forelse ($pj->kebutuhan_mendesak ?? [] as $kebutuhan)
                                                        <span class="badge badge-light">{{ $kebutuhan }}</span>
                                                    @empty
                                                        -
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="detail-item mb-0">
                                                <span class="detail-label">Keterangan</span>
                                                <div class="detail-text">
                                                    {!! nl2br(e($pj->keterangan ?: '-')) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- DATA REHABILITASI SOSIAL (PJSRK) --}}
                            @if ($laporan->layanan_pjsrk === 'rehabilitasi_sosial')
                                <div class="form-section">
                                    <h5 class="form-section-title">
                                        Data Rehabilitasi Sosial
                                        @if ($pj->rahasia)
                                            <span class="badge badge-warning ms-2"><i class="fa fa-lock me-1"></i>
                                                Rahasia</span>
                                        @endif
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Jenis Rehabilitasi</span>
                                                <span class="detail-value">{{ $pj->jenis_rehabilitasi ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Jumlah Penerima</span>
                                                <span
                                                    class="detail-value">{{ is_null($pj->jumlah_penerima) ? '-' : $pj->jumlah_penerima . ' orang' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Jenis Kelamin</span>
                                                <span class="detail-value">{{ $pj->jenis_kelamin ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Kelompok Usia</span>
                                                <span class="detail-value">{{ $pj->kelompok_usia ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Kondisi Saat Ditemukan</span>
                                                <span class="detail-value">{{ $pj->kondisi_saat_ditemukan ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="detail-item mb-0">
                                                <span class="detail-label">Ciri-ciri</span>
                                                <div class="detail-text">
                                                    {!! nl2br(e($pj->ciri_ciri ?: '-')) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- DATA PERMASALAHAN SOSIAL (PJSRK) --}}
                            @if ($laporan->layanan_pjsrk === 'permasalahan_sosial')
                                <div class="form-section">
                                    <h5 class="form-section-title">
                                        Data Permasalahan Sosial
                                        @if ($pj->rahasia)
                                            <span class="badge badge-warning ms-2"><i class="fa fa-lock me-1"></i>
                                                Rahasia</span>
                                        @endif
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Jenis Permasalahan</span>
                                                <span class="detail-value">{{ $pj->jenis_permasalahan ?: '-' }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Judul Laporan</span>
                                                <span class="detail-value">{{ $laporan->judul_tampil }}</span>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="detail-item mb-0">
                                                <span class="detail-label">Uraian Kejadian</span>
                                                <div class="detail-text">
                                                    {!! nl2br(e(optional($laporan->detail_kasus)->kronologi ?: '-')) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- LOKASI & KONTAK (PJSRK) --}}
                            <div class="form-section">
                                <h5 class="form-section-title">Lokasi & Kontak</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Kontak Pelapor</span>
                                            <span class="detail-value">{{ $pj->kontak ?: '-' }}</span>
                                        </div>
                                    </div>
                                    @if ($pj->patokan_lokasi)
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Patokan Lokasi</span>
                                                <span class="detail-value">{{ $pj->patokan_lokasi }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($pj->kelurahan)
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Kelurahan</span>
                                                <span class="detail-value">{{ $pj->kelurahan }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($pj->kecamatan)
                                        <div class="col-md-6">
                                            <div class="detail-item">
                                                <span class="detail-label">Kecamatan</span>
                                                <span class="detail-value">{{ $pj->kecamatan }}</span>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12">
                                        <div class="detail-item">
                                            <span class="detail-label">Alamat</span>
                                            <span class="detail-value">{{ $pj->alamat ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="detail-item mb-0">
                                            <span class="detail-label">Koordinat</span>
                                            @if (!is_null($pj->latitude) && !is_null($pj->longitude))
                                                <div class="detail-value">
                                                    <span class="me-3">{{ $pj->latitude }}, {{ $pj->longitude }}</span>
                                                    <a href="https://www.google.com/maps?q={{ $pj->latitude }},{{ $pj->longitude }}"
                                                        target="_blank" rel="noopener"
                                                        class="btn btn-instansi-soft btn-pill btn-sm">
                                                        <i class="fa fa-map-location-dot"></i> Buka Peta
                                                    </a>
                                                </div>
                                            @else
                                                <span class="detail-value">-</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- FOTO / LAMPIRAN (PJSRK) --}}
                            <div class="form-section">
                                <h5 class="form-section-title">Foto / Lampiran</h5>
                                @if (count($pj->foto_urls) > 0)
                                    <div class="pjsrk-photo-grid">
                                        @foreach ($pj->foto_urls as $index => $url)
                                            <a href="{{ $url }}" target="_blank" rel="noopener"
                                                title="Foto {{ $index + 1 }}">
                                                <img src="{{ $url }}" alt="Foto laporan {{ $index + 1 }}"
                                                    class="pjsrk-photo">
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="empty-state">
                                        <div class="empty-icon"><i class="fa fa-image"></i></div>
                                        <p class="mb-0">Tidak ada foto yang dilampirkan.</p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @unless ($pj)
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
                                        <span class="detail-label">Nomor Telepon Pelapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_pelapor)->no_telp ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item">
                                        <span class="detail-label">Hubungan dengan Korban</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_pelapor)->hubungan_dengan_korban ?: '-' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="detail-item mb-0">
                                        <span class="detail-label">Alamat Pelapor</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_pelapor)->alamat ?: '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- PENERIMA MANFAAT --}}
                        @if ($laporan->detail_penerima_manfaat || !$laporan->layanan_pjsrk)
                        <div class="form-section">
                            <h5 class="form-section-title">Penerima Manfaat</h5>
                            @if ($laporan->detail_penerima_manfaat)
                                @php $penerima = $laporan->detail_penerima_manfaat; @endphp
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">NIK Penerima Manfaat</span>
                                            <span class="detail-value">{{ $penerima->nik ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Nama Penerima Manfaat</span>
                                            <span class="detail-value">{{ $penerima->nama ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Tempat Lahir</span>
                                            <span class="detail-value">{{ $penerima->tempat_lahir ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Tanggal Lahir</span>
                                            <span class="detail-value">{{ $penerima->tanggal_lahir ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Umur</span>
                                            <span class="detail-value">{{ $penerima->umur ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Jenis Kelamin</span>
                                            <span class="detail-value">{{ $penerima->jenis_kelamin ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Pekerjaan</span>
                                            <span class="detail-value">{{ $penerima->pekerjaan ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Agama</span>
                                            <span class="detail-value">{{ $penerima->agama ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Pendidikan</span>
                                            <span class="detail-value">{{ $penerima->pendidikan ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Nomor Telepon</span>
                                            <span class="detail-value">{{ $penerima->notelp ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Hubungan dengan Terlapor</span>
                                            <span
                                                class="detail-value">{{ $penerima->hubungan_dengan_terlapor ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Informasi Tambahan</span>
                                            <span class="detail-value">{{ $penerima->informasi_tambahan ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="detail-item mb-0">
                                            <span class="detail-label">Alamat</span>
                                            <span class="detail-value">{{ $penerima->alamat ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fa fa-user-slash"></i></div>
                                    <p class="mb-0">Data penerima manfaat belum diisi.</p>
                                </div>
                            @endif
                        </div>
                        @endif

                        {{-- INFORMASI ANAK --}}
                        @if (
                            $laporan->detail_penerima_manfaat &&
                                $laporan->detail_penerima_manfaat->informasi_anak &&
                                $laporan->detail_penerima_manfaat->informasi_anak->count() > 0)
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
                        @if ($laporan->detail_terlapor || !$laporan->layanan_pjsrk)
                        <div class="form-section">
                            <h5 class="form-section-title">Terlapor</h5>
                            @if ($laporan->detail_terlapor)
                                @php $terlapor = $laporan->detail_terlapor; @endphp
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">NIK Terlapor</span>
                                            <span class="detail-value">{{ $terlapor->nik ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Nama Terlapor</span>
                                            <span class="detail-value">{{ $terlapor->nama ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Umur</span>
                                            <span class="detail-value">{{ $terlapor->umur ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Jenis Kelamin</span>
                                            <span class="detail-value">{{ $terlapor->jenis_kelamin ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Hubungan dengan Korban</span>
                                            <span
                                                class="detail-value">{{ $terlapor->hubungan_dengan_korban ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <span class="detail-label">Informasi Tambahan</span>
                                            <span class="detail-value">{{ $terlapor->informasi_tambahan ?: '-' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="detail-item mb-0">
                                            <span class="detail-label">Alamat</span>
                                            <span class="detail-value">{{ $terlapor->alamat ?: '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fa fa-user-slash"></i></div>
                                    <p class="mb-0">Data terlapor belum diisi.</p>
                                </div>
                            @endif
                        </div>
                        @endif
                        @endunless

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
                                        <span class="detail-label">Waktu Kejadian</span>
                                        <span
                                            class="detail-value">{{ optional($laporan->detail_kasus)->waktu_kejadian ? \Illuminate\Support\Carbon::parse($laporan->detail_kasus->waktu_kejadian)->format('H:i') : '-' }}</span>
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
                                            {!! nl2br(e(optional($laporan->detail_kasus)->kronologi ?: 'Kronologi belum diisi.')) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ALASAN PENOLAKAN --}}
                        @if ($laporan->status === 'ditolak' && $laporan->alasan_penolakan)
                            <div class="form-section">
                                <h5 class="form-section-title">Alasan Penolakan</h5>
                                <div class="app-alert app-alert-danger mb-0">
                                    <span><i class="fa fa-circle-exclamation me-2"></i>{{ $laporan->alasan_penolakan }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: RINGKASAN + AKSI --}}
            <div class="col-xl-4">
                <div class="sticky-panel">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Ringkasan Laporan</h4>
                        </div>
                        <div class="card-body">
                            <div class="summary-head">
                                <span class="summary-ticket">{{ $laporan->id_laporan }}</span>
                                @include('backend.components.status-pill', ['status' => $laporan->status])
                            </div>

                            <div class="summary-row">
                                <span class="summary-key">Status</span>
                                <span class="summary-value">{{ $laporan->status_label }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-key">Layanan</span>
                                <span class="summary-value">{{ $laporan->layanan_label }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-key">Kategori</span>
                                <span class="summary-value">{{ $laporan->kategori ?: '-' }}</span>
                            </div>
                            @if (optional($laporan->detail_pjsrk)->jenis_permasalahan)
                                <div class="summary-row">
                                    <span class="summary-key">Jenis</span>
                                    <span
                                        class="summary-value">{{ $laporan->detail_pjsrk->jenis_permasalahan }}</span>
                                </div>
                            @endif
                            <div class="summary-row">
                                <span class="summary-key">Kecamatan</span>
                                <span class="summary-value">{{ $laporan->kecamatan_tampil }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-key">Tanggal Masuk</span>
                                <span
                                    class="summary-value">{{ optional($laporan->created_at)->format('d-m-Y') }}</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-key">Judul</span>
                                <span class="summary-value">{{ $laporan->judul_tampil }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-0">
                        <div class="card-header">
                            <h4 class="card-title mb-0">Tindakan</h4>
                        </div>
                        <div class="card-body">
                            <div class="action-stack">
                                @if (in_array($laporan->status, ['dikirim', 'diterima'], true))
                                    <form method="POST"
                                        action="{{ route('dinsos.laporan.verifikasi', $laporan->id_laporan) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-instansi-primary btn-pill">
                                            <i class="fa fa-circle-check"></i> Verifikasi Laporan
                                        </button>
                                    </form>
                                @endif

                                @if (in_array($laporan->status, ['dikirim', 'diterima', 'diproses'], true))
                                    <form method="POST"
                                        action="{{ route('dinsos.laporan.proses', $laporan->id_laporan) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-instansi-soft btn-pill">
                                            <i class="fa fa-gears"></i> Tandai Diproses
                                        </button>
                                    </form>
                                @endif

                                @if (in_array($laporan->status, ['diproses', 'diterima'], true))
                                    <form method="POST"
                                        action="{{ route('dinsos.laporan.selesai', $laporan->id_laporan) }}">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-hijau btn-pill">
                                            <i class="fa fa-flag-checkered"></i> Selesaikan Laporan
                                        </button>
                                    </form>
                                @endif

                                @if (in_array($laporan->status, ['dikirim', 'diterima', 'diproses'], true))
                                    <button type="button" class="btn btn-danger btn-pill js-reject" data-toggle="modal"
                                        data-target="#rejectModal"
                                        data-action="{{ route('dinsos.laporan.tolak', $laporan->id_laporan) }}"
                                        data-ticket="{{ $laporan->id_laporan }}">
                                        <i class="fa fa-circle-xmark"></i> Tolak Laporan
                                    </button>
                                @endif

                                <a href="{{ route('dinsos.laporan.index') }}" class="btn btn-light btn-pill">
                                    <i class="fa fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>

                            @if ($laporan->status === 'ditolak')
                                <p class="action-note mb-0">
                                    Laporan ini sudah ditolak, tidak ada tindakan lanjutan yang tersedia.
                                </p>
                            @elseif ($laporan->status === 'selesai')
                                <p class="action-note mb-0">
                                    Laporan sudah selesai ditangani.
                                </p>
                            @elseif ($laporan->status === 'dirujuk')
                                <p class="action-note mb-0">
                                    Laporan telah dirujuk ke instansi lain.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Tolak --}}
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Laporan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="rejectForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p id="rejectDescription" class="text-muted" style="font-size: .85rem;">
                            Tambahkan alasan penolakan untuk laporan ini.
                        </p>
                        <div class="form-group mb-0">
                            <label class="form-label">Alasan Penolakan</label>
                            <textarea name="alasan_penolakan" class="form-control" rows="4" required maxlength="1000"
                                placeholder="Contoh: Laporan tidak sesuai dengan layanan Dinsos atau data tidak lengkap."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-pill" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-pill">Tolak Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .pjsrk-photo-grid {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
        }

        .pjsrk-photo {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border-radius: .5rem;
            border: 1px solid rgba(0, 0, 0, .1);
        }

        /* Gaya modal ditulis per-id karena tema global memakai .modal-header
           berlatar gelap dan .modal-title berwarna #EEEEEE, sehingga judul dan
           tombol tutupnya tidak terbaca. */
        #rejectModal {
            z-index: 1060;
        }

        #rejectModal .modal-content {
            border: 0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 24px 48px rgba(16, 24, 40, .18);
        }

        #rejectModal .modal-header {
            background: var(--i-primary, #b45309);
            border: 0;
            padding: 18px 22px;
        }

        #rejectModal .modal-title {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 700;
        }

        #rejectModal .modal-header .close {
            margin: 0;
            padding: 0;
            color: #fff;
            opacity: .85;
            text-shadow: none;
            font-size: 1.6rem;
            line-height: 1;
        }

        #rejectModal .modal-header .close:hover {
            color: #fff;
            opacity: 1;
        }

        #rejectModal .modal-body {
            padding: 20px 22px;
            max-height: none;
            overflow-y: visible;
        }

        #rejectModal .modal-footer {
            border: 0;
            padding: 0 22px 20px;
            flex-direction: row;
            gap: 8px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalEl = document.getElementById('rejectModal');
            var form = document.getElementById('rejectForm');
            var description = document.getElementById('rejectDescription');

            // Modal dipindah ke <body> agar tidak terkurung di dalam
            // #main-wrapper/.content-body yang memiliki overflow dan stacking
            // context sendiri (penyebab modal terpotong atau tertimpa lapisan).
            if (modalEl && modalEl.parentElement !== document.body) {
                document.body.appendChild(modalEl);
            }

            var $modal = window.jQuery ? window.jQuery(modalEl) : null;
            var canShow = $modal && typeof $modal.modal === 'function';

            function showRejectModal(button) {
                form.action = button.dataset.action;
                description.textContent = 'Tambahkan alasan penolakan untuk tiket ' + button.dataset.ticket + '.';

                // Dibuka manual supaya tidak bergantung pada data-api tema.
                if (canShow) {
                    $modal.modal('show');
                }
            }

            // Tombol "Tolak Laporan" pada halaman detail.
            document.querySelectorAll('.js-reject').forEach(function(button) {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    showRejectModal(button);
                });
            });

            if (canShow) {
                $modal.on('hidden.bs.modal', function() {
                    form.reset();
                    form.removeAttribute('action');
                });
            }
        });
    </script>
@endpush
