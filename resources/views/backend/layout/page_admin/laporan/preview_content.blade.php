{{-- File: preview_content.blade.php (untuk response AJAX) --}}
<div class="preview-content">

    {{-- Info Laporan --}}
    <div class="section-card">
        <div class="section-header">
            <h6 class="section-title">
                <i class="fas fa-info-circle text-primary-custom"></i>
                INFORMASI LAPORAN
            </h6>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-item">
                        <label class="info-label">ID Laporan</label>
                        <div class="info-value">
                            <span class="report-id">{{ $laporan->id_laporan }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <label class="info-label">Kategori Laporan</label>
                        <div class="info-value">
                            {{ $laporan->kategori != 'unset' ? $laporan->kategori : '-' }}
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-item">
                        <label class="info-label">Tanggal</label>
                        <div class="info-value">
                            {{ $laporan->created_at->format('d-m-Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Identitas Pelapor --}}
    <div class="section-card">
        <div class="section-header">
            <h6 class="section-title">
                <i class="fas fa-user text-info-custom"></i>
                IDENTITAS PELAPOR
            </h6>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">NIK Pelapor</label>
                        <div class="info-value">{{ optional($laporan->detail_pelapor)->nik }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Nama Pelapor</label>
                        <div class="info-value">{{ optional($laporan->detail_pelapor)->nama }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Alamat Pelapor</label>
                        <div class="info-value">{{ optional($laporan->detail_pelapor)->alamat }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Hubungan dengan Korban</label>
                        <div class="info-value">{{ optional($laporan->detail_pelapor)->hubungan_dengan_korban }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Nomor Telepon Pelapor</label>
                        <div class="info-value">{{ optional($laporan->detail_pelapor)->no_telp }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Penerima Manfaat --}}
    <div class="section-card">
        <div class="section-header">
            <h6 class="section-title">
                <i class="fas fa-heart text-success-custom"></i>
                PENERIMA MANFAAT
            </h6>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">NIK Penerima Manfaat</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->nik }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Nama Penerima Manfaat</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->nama }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Tempat Lahir</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->tempat_lahir }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Tanggal Lahir</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->tanggal_lahir }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Umur</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->umur }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Jenis Kelamin</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->jenis_kelamin }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Pekerjaan</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->pekerjaan }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Agama</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->agama }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Pendidikan</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->pendidikan }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Alamat</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->alamat }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Hubungan dengan Terlapor</label>
                        <div class="info-value">
                            {{ optional($laporan->detail_penerima_manfaat)->hubungan_dengan_terlapor }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Nomor Telepon</label>
                        <div class="info-value">{{ optional($laporan->detail_penerima_manfaat)->notelp }}</div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="info-item">
                        <label class="info-label">Informasi Tambahan</label>
                        <div class="info-value large">
                            {{ optional($laporan->detail_penerima_manfaat)->informasi_tambahan }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Informasi Anak --}}
    @if ($laporan->detail_penerima_manfaat && $laporan->detail_penerima_manfaat->informasi_anak->count() > 0)
        <div class="section-card">
            <div class="section-header">
                <h6 class="section-title">
                    <i class="fas fa-child text-warning-custom"></i>
                    INFORMASI ANAK
                </h6>
            </div>
            <div class="section-body">
                @foreach ($laporan->detail_penerima_manfaat->informasi_anak as $index => $anak)
                    <div class="child-card">
                        <div class="child-number">ANAK KE-{{ $index + 1 }}</div>
                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Nama Anak</label>
                                    <div class="info-value">{{ $anak->nama }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Tanggal Lahir</label>
                                    <div class="info-value">{{ $anak->tanggal_lahir }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Umur</label>
                                    <div class="info-value">{{ $anak->umur }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Jenis Kelamin</label>
                                    <div class="info-value">{{ $anak->jenis_kelamin }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Pendidikan</label>
                                    <div class="info-value">{{ $anak->pendidikan }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Agama</label>
                                    <div class="info-value">{{ $anak->agama }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Status</label>
                                    <div class="info-value">
                                        <span class="status-badge">{{ $anak->status }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Terlapor --}}
    <div class="section-card">
        <div class="section-header">
            <h6 class="section-title">
                <i class="fas fa-user-times text-danger-custom"></i>
                TERLAPOR
            </h6>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">NIK Terlapor</label>
                        <div class="info-value">{{ optional($laporan->detail_terlapor)->nik }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Nama Terlapor</label>
                        <div class="info-value">{{ optional($laporan->detail_terlapor)->nama }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Umur</label>
                        <div class="info-value">{{ optional($laporan->detail_terlapor)->umur }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Alamat</label>
                        <div class="info-value">{{ optional($laporan->detail_terlapor)->alamat }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Jenis Kelamin</label>
                        <div class="info-value">{{ optional($laporan->detail_terlapor)->jenis_kelamin }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Hubungan dengan Korban</label>
                        <div class="info-value">{{ optional($laporan->detail_terlapor)->hubungan_dengan_korban }}
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="info-item">
                        <label class="info-label">Informasi Tambahan</label>
                        <div class="info-value large">{{ optional($laporan->detail_terlapor)->informasi_tambahan }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Kasus --}}
    <div class="section-card">
        <div class="section-header">
            <h6 class="section-title">
                <i class="fas fa-clipboard-list text-dark-custom"></i>
                DETAIL KASUS
            </h6>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Tanggal Kejadian</label>
                        <div class="info-value">{{ optional($laporan->detail_kasus)->tanggal }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Waktu Kejadian</label>
                        <div class="info-value">{{ optional($laporan->detail_kasus)->waktu_kejadian ? \Illuminate\Support\Carbon::parse($laporan->detail_kasus->waktu_kejadian)->format('H:i') : '-' }}</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <label class="info-label">Tempat Kejadian</label>
                        <div class="info-value">{{ optional($laporan->detail_kasus)->tempat_kejadian }}</div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="info-item">
                        <label class="info-label">Kronologi Kejadian</label>
                        <div class="info-value large">
                            {!! nl2br(e(optional($laporan->detail_kasus)->kronologi)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
