@extends('backend.layout.admin_layout')

@section('title', 'Edit Laporan')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Edit Laporan',
            'subtitle' => 'Perbarui data laporan ' . $laporan->id_laporan,
            'icon' => 'fa-pen-to-square',
            'actionUrl' => route('laporan_proses'),
            'actionLabel' => 'Kembali',
            'actionIcon' => 'fa-arrow-left',
            'actionClass' => 'btn-instansi-soft btn-pill',
        ])

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('laporan.update', $laporan->id_laporan) }}" method="POST"
                            id="laporan-edit-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="anak_sync" value="1">

                            {{-- DATA LAPORAN --}}
                            <div class="form-section">
                                <h5 class="form-section-title">Data Laporan</h5>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="id_laporan">ID Laporan</label>
                                            <input type="text" id="id_laporan" name="id" class="form-control"
                                                value="{{ $laporan->id_laporan }}" readonly>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="kategori">Kategori Laporan PPA</label>
                                            <select id="kategori" name="kategori" class="form-control" required>
                                                <option value="">-- Pilih Kategori PPA --</option>
                                                @foreach (\App\Models\Laporan::PPA_CATEGORIES as $katPpa)
                                                    <option value="{{ $katPpa }}"
                                                        {{ old('kategori', $laporan->kategori) == $katPpa ? 'selected' : '' }}>
                                                        {{ $katPpa }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label class="form-label" for="tanggal">Tanggal &amp; Jam Laporan</label>
                                            <input type="text" id="tanggal" name="tanggal" class="form-control"
                                                value="{{ $laporan->created_at->format('d-m-Y H:i') }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- PELAPOR --}}
                            <div class="form-section">
                                <h5 class="form-section-title">Identitas Pelapor</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="pelapor_nik">NIK Pelapor</label>
                                            <input type="text" id="pelapor_nik" name="pelapor[nik]" class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->nik }}" required
                                                oninvalid="this.setCustomValidity('NIK salah')"
                                                oninput="this.setCustomValidity('')" pattern="\d{16}" inputmode="numeric"
                                                maxlength="16">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="pelapor_nama">Nama Pelapor</label>
                                            <input type="text" id="pelapor_nama" name="pelapor[nama]" class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->nama }}" required
                                                oninvalid="this.setCustomValidity('Data pelapor wajib  diisi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="pelapor_alamat">Alamat Pelapor</label>
                                            <input type="text" id="pelapor_alamat" name="pelapor[alamat]"
                                                class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->alamat }}" required
                                                oninvalid="this.setCustomValidity('Data pelapor wajib diisi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="pelapor_hubungan">Hubungan dengan Korban</label>
                                            @php
                                                $hubunganPelapor = old('pelapor.hubungan', optional($laporan->detail_pelapor)->hubungan_dengan_korban);
                                                $hubunganOptions = \App\Models\Laporan::HUBUNGAN_KORBAN_OPTIONS;
                                                $isCustomHubungan = $hubunganPelapor && $hubunganPelapor !== '-' && !in_array($hubunganPelapor, $hubunganOptions, true);
                                            @endphp
                                            <select id="pelapor_hubungan" name="pelapor[hubungan]" class="form-control" required
                                                oninvalid="this.setCustomValidity('Hubungan pelapor dengan korban wajib dipilih')"
                                                oninput="this.setCustomValidity('')">
                                                <option value="">Pilih hubungan dengan korban</option>
                                                @foreach ($hubunganOptions as $opsiHub)
                                                    <option value="{{ $opsiHub }}"
                                                        {{ $hubunganPelapor === $opsiHub ? 'selected' : '' }}>
                                                        {{ $opsiHub }}
                                                    </option>
                                                @endforeach
                                                @if ($isCustomHubungan)
                                                    <option value="{{ $hubunganPelapor }}" selected>{{ $hubunganPelapor }}</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label class="form-label" for="pelapor_telepon">Nomor Telepon Pelapor</label>
                                            <input type="text" id="pelapor_telepon" name="pelapor[telepon]"
                                                class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->no_telp }}" required
                                                oninvalid="this.setCustomValidity('Nomor telepon harus 10-15 digit angka')"
                                                oninput="this.setCustomValidity('')" pattern="\d{10,15}"
                                                inputmode="numeric" maxlength="15">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- PENERIMA MANFAAT --}}
                            <div class="form-section">
                                <h5 class="form-section-title">Penerima Manfaat</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_nik">NIK Penerima Manfaat</label>
                                            <input type="text" id="penerima_nik" name="penerima[nik]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->nik }}"
                                                oninvalid="this.setCustomValidity('NIK salah')"
                                                oninput="this.setCustomValidity('')" pattern="(\d{16}|-)?" inputmode="numeric"
                                                maxlength="16">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_nama">Nama Penerima Manfaat</label>
                                            <input type="text" id="penerima_nama" name="penerima[nama]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->nama }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_ttl">Tempat Lahir</label>
                                            <input type="text" id="penerima_ttl" name="penerima[ttl]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->tempat_lahir }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_tanggal">Tanggal Lahir</label>
                                            <input type="date" id="penerima_tanggal" name="penerima[tanggal]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->tanggal_lahir }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_umur">Umur</label>
                                            <input type="number" id="penerima_umur" name="penerima[umur]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->umur }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_jk">Jenis Kelamin</label>
                                            <select id="penerima_jk" name="penerima[jk]" class="form-control penerima-input">
                                                <option value="">Pilih</option>
                                                @foreach (\App\Models\Laporan::JENIS_KELAMIN_OPTIONS as $jkOpsi)
                                                    <option value="{{ $jkOpsi }}"
                                                        {{ strcasecmp((string) optional($laporan->detail_penerima_manfaat)->jenis_kelamin, $jkOpsi) === 0 ? 'selected' : '' }}>
                                                        {{ $jkOpsi }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_pekerjaan">Pekerjaan</label>
                                            <input type="text" id="penerima_pekerjaan" name="penerima[pekerjaan]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->pekerjaan }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_agama">Agama</label>
                                            <select id="penerima_agama" name="penerima[agama]"
                                                class="form-control penerima-input">
                                                <option value="">Pilih</option>
                                                @foreach (\App\Models\Laporan::AGAMA_OPTIONS as $agama)
                                                    <option value="{{ $agama }}"
                                                        {{ strcasecmp((string) optional($laporan->detail_penerima_manfaat)->agama, $agama) === 0 ? 'selected' : '' }}>
                                                        {{ $agama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_pendidikan">Pendidikan</label>
                                            <select id="penerima_pendidikan" name="penerima[pendidikan]"
                                                class="form-control penerima-input">
                                                <option value="">Pilih</option>
                                                @foreach (\App\Models\Laporan::PENDIDIKAN_PENERIMA_OPTIONS as $pendidikan)
                                                    <option value="{{ $pendidikan }}"
                                                        {{ strcasecmp((string) optional($laporan->detail_penerima_manfaat)->pendidikan, $pendidikan) === 0 ? 'selected' : '' }}>
                                                        {{ $pendidikan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_alamat">Alamat</label>
                                            <input type="text" id="penerima_alamat" name="penerima[alamat]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->alamat }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_hubungan_terlapor">Hubungan dengan
                                                Terlapor</label>
                                            <input type="text" id="penerima_hubungan_terlapor"
                                                name="penerima[hubungan_terlapor]" class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->hubungan_dengan_terlapor }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label" for="penerima_telepon">Nomor Telepon</label>
                                            <input type="text" id="penerima_telepon" name="penerima[telepon]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->notelp }}"
                                                oninvalid="this.setCustomValidity('Nomor telepon harus 10-15 digit angka')"
                                                oninput="this.setCustomValidity('')" pattern="(\d{10,15}|-)?"
                                                inputmode="numeric" maxlength="15">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-0">
                                            <label class="form-label" for="penerima_informasi_tambahan">Informasi
                                                Tambahan</label>
                                            <input type="text" id="penerima_informasi_tambahan"
                                                name="penerima[informasi_tambahan]" class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->informasi_tambahan }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- INFORMASI ANAK --}}
                            <div class="form-section">
                                <h5 class="form-section-title">Informasi Anak</h5>

                                <div id="informasi-anak-container"
                                    class="{{ $laporan->detail_penerima_manfaat ? '' : 'd-none' }}">
                                    <section id="anak-wrapper">
                                        @if (
                                            $laporan->detail_penerima_manfaat &&
                                                $laporan->detail_penerima_manfaat->informasi_anak &&
                                                count($laporan->detail_penerima_manfaat->informasi_anak) > 0)
                                            @foreach ($laporan->detail_penerima_manfaat->informasi_anak as $index => $anak)
                                                <h6 class="anak-block-title">ANAK KE-{{ $index + 1 }}</h6>
                                                <div class="row mb-4 anak-block">
                                                    <input type="hidden" name="anak[{{ $index }}][id]"
                                                        value="{{ $anak->id_anak }}">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Nama Anak</label>
                                                            <input type="text" name="anak[{{ $index }}][nama]"
                                                                class="form-control" value="{{ $anak->nama }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Tanggal Lahir (Opsional)</label>
                                                            <input type="date" name="anak[{{ $index }}][tanggal]"
                                                                class="form-control" value="{{ $anak->tanggal_lahir }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Umur (Opsional)</label>
                                                            <input type="number" name="anak[{{ $index }}][umur]"
                                                                class="form-control" value="{{ $anak->umur }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Jenis Kelamin (Opsional)</label>
                                                            <select name="anak[{{ $index }}][jenis_kelamin]"
                                                                class="form-control">
                                                                <option value="">Pilih</option>
                                                                @foreach (\App\Models\Laporan::JENIS_KELAMIN_OPTIONS as $jkAnak)
                                                                    <option value="{{ $jkAnak }}"
                                                                        {{ strcasecmp((string) $anak->jenis_kelamin, $jkAnak) === 0 ? 'selected' : '' }}>
                                                                        {{ $jkAnak }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Pendidikan (Opsional)</label>
                                                            <select name="anak[{{ $index }}][pendidikan]"
                                                                class="form-control">
                                                                <option value="">Pilih</option>
                                                                @foreach (\App\Models\Laporan::PENDIDIKAN_ANAK_OPTIONS as $pendidikan)
                                                                    <option value="{{ $pendidikan }}"
                                                                        {{ strcasecmp((string) $anak->pendidikan, $pendidikan) === 0 ? 'selected' : '' }}>
                                                                        {{ $pendidikan }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Agama (Opsional)</label>
                                                            <select name="anak[{{ $index }}][agama]"
                                                                class="form-control">
                                                                <option value="">Pilih</option>
                                                                @foreach (\App\Models\Laporan::AGAMA_OPTIONS as $agama)
                                                                    <option value="{{ $agama }}"
                                                                        {{ strcasecmp((string) $anak->agama, $agama) === 0 ? 'selected' : '' }}>
                                                                        {{ $agama }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label">Status (Opsional)</label>
                                                            <select name="anak[{{ $index }}][status]"
                                                                class="form-control">
                                                                <option value="">Pilih</option>
                                                                @foreach (\App\Models\Laporan::STATUS_ANAK_OPTIONS as $statusAnak)
                                                                    <option value="{{ $statusAnak }}"
                                                                        {{ strcasecmp((string) $anak->status, $statusAnak) === 0 ? 'selected' : '' }}>
                                                                        {{ $statusAnak }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-danger btn-sm btn-pill"
                                                            onclick="hapusFormIni(this)">Hapus Anak Ini</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </section>

                                    <!-- Tombol tambah anak -->
                                    <button type="button" class="btn btn-instansi-soft btn-pill btn-sm mb-3"
                                        id="btn-tambah-anak" onclick="tambahAnak()">
                                        <i class="fa fa-plus"></i> Tambah Anak
                                    </button>
                                </div>

                                <div class="alert alert-warning" id="pesan-penerima-kosong"
                                    {{ $laporan->detail_penerima_manfaat ? 'style=display:none' : '' }}>
                                    <strong>Perhatian!</strong> Silakan isi data Penerima Manfaat terlebih dahulu sebelum
                                    menambahkan informasi anak.
                                </div>
                            </div>

                            {{-- TERLAPOR --}}
                            <div class="form-section">
                                <h5 class="form-section-title">Terlapor</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">NIK Terlapor</label>
                                            <input type="text" name="terlapor[nik]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->nik }}"
                                                oninvalid="this.setCustomValidity('NIK salah')"
                                                oninput="this.setCustomValidity('')" pattern="(\d{16}|-)?" inputmode="numeric"
                                                maxlength="16">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Nama Terlapor</label>
                                            <input type="text" name="terlapor[nama]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->nama }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Umur</label>
                                            <input type="number" name="terlapor[umur]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->umur }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Alamat</label>
                                            <input type="text" name="terlapor[alamat]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->alamat }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Jenis Kelamin</label>
                                            <select name="terlapor[jk]" class="form-control">
                                                <option value="">Pilih</option>
                                                @foreach (\App\Models\Laporan::JENIS_KELAMIN_OPTIONS as $jkTerlapor)
                                                    <option value="{{ $jkTerlapor }}"
                                                        {{ strcasecmp((string) optional($laporan->detail_terlapor)->jenis_kelamin, $jkTerlapor) === 0 ? 'selected' : '' }}>
                                                        {{ $jkTerlapor }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Hubungan dengan Korban</label>
                                            <input type="text" name="terlapor[hubungan]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->hubungan_dengan_korban }}">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="form-label">Informasi Tambahan</label>
                                            <input type="text" name="terlapor[informasi_tambahan]"
                                                class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->informasi_tambahan }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- DETAIL KASUS --}}
                            <div class="form-section">
                                <h5 class="form-section-title">Detail Kasus</h5>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Kejadian</label>
                                            <input type="date" name="kasus[tanggal]" class="form-control"
                                                value="{{ optional($laporan->detail_kasus)->tanggal }}" required
                                                oninvalid="this.setCustomValidity('Detail kasus wajib di isi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Waktu Kejadian</label>
                                            <input type="time" name="kasus[waktu]" class="form-control"
                                                value="{{ optional($laporan->detail_kasus)->waktu_kejadian ? \Illuminate\Support\Carbon::parse(optional($laporan->detail_kasus)->waktu_kejadian)->format('H:i') : '' }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">Tempat Kejadian</label>
                                            <input type="text" name="kasus[tempat]" class="form-control"
                                                value="{{ optional($laporan->detail_kasus)->tempat_kejadian }}" required
                                                oninvalid="this.setCustomValidity('Detail kasus wajib di isi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-0">
                                            <label class="form-label">Kronologi Kejadian</label>
                                            <textarea name="kasus[kronologi]" class="form-control" rows="5" required
                                                oninvalid="this.setCustomValidity('Detail kasus wajib di isi')" oninput="this.setCustomValidity('')">{{ optional($laporan->detail_kasus)->kronologi }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <a href="{{ route('laporan_proses') }}" wire:navigate class="btn btn-light btn-pill">Batal</a>
                                <button type="submit" class="btn btn-instansi-primary btn-pill">
                                    <i class="fa fa-floppy-disk"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template anak baru (dipindahkan ke luar form) -->
    <template id="anak-template">
        <h6 class="anak-block-title">ANAK BARU</h6>
        <div class="row mb-4 anak-block">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" name="anak[__INDEX__][nama]" class="form-control" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir (Opsional)</label>
                    <input type="date" name="anak[__INDEX__][tanggal]" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Umur (Opsional)</label>
                    <input type="number" name="anak[__INDEX__][umur]" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin (Opsional)</label>
                    <select name="anak[__INDEX__][jenis_kelamin]" class="form-control">
                        <option value="">Pilih</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Pendidikan (Opsional)</label>
                    <select name="anak[__INDEX__][pendidikan]" class="form-control">
                        <option value="">Pilih</option>
                        <option value="Tidak Sekolah">Tidak Sekolah</option>
                        <option value="PAUD">PAUD</option>
                        <option value="TK">TK</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Agama (Opsional)</label>
                    <select name="anak[__INDEX__][agama]" class="form-control">
                        <option value="">Pilih</option>
                        <option value="Islam">Islam</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Status (Opsional)</label>
                    <select name="anak[__INDEX__][status]" class="form-control">
                        <option value="">Pilih</option>
                        <option value="Anak Kandung">Anak Kandung</option>
                        <option value="Anak Angkat">Anak Angkat</option>
                    </select>
                </div>
            </div>

            <!-- Tombol hapus form baru -->
            <div class="col-12">
                <button type="button" class="btn btn-danger btn-sm btn-pill" onclick="hapusFormIni(this)">Hapus Form
                    Ini</button>
            </div>
        </div>
    </template>

    <!-- JavaScript -->
    <script>
        // Variabel untuk menyimpan jumlah maksimum anak yang dapat ditambahkan.
        // Memakai var (bukan const) agar aman dieksekusi ulang oleh Livewire
        // soft navigation tanpa error "already declared".
        var MAX_ANAK = 10;

        // Menentukan index anak berdasarkan jumlah anak yang ada
        var indexAnak =
            {{ $laporan->detail_penerima_manfaat && $laporan->detail_penerima_manfaat->informasi_anak
                ? count($laporan->detail_penerima_manfaat->informasi_anak)
                : 0 }};

        // Siapkan form penerima manfaat. Dijalankan langsung karena elemen sudah
        // terpasang — DOMContentLoaded tidak terpicu ulang setelah soft navigation.
        (function initPenerimaForm() {
            // Cari semua input dan select di bagian penerima manfaat
            const penerimaInputs = document.querySelectorAll('.penerima-input');

            // Hapus event listener lama sebelum menambahkan yang baru
            penerimaInputs.forEach(input => {
                input.removeEventListener('change', checkPenerimaStatus);
                // Tambahkan event listener baru
                input.addEventListener('change', checkPenerimaStatus);
            });

            // Cek status awal
            checkPenerimaStatus();

            // Tambahkan event handler untuk validasi form sebelum submit
            document.getElementById('laporan-edit-form').addEventListener('submit', function(e) {
                // Validasi data anak jika form anak tampil
                if (!document.getElementById('informasi-anak-container').classList.contains('d-none')) {
                    if (!cekFormAnak()) {
                        e.preventDefault(); // Hentikan submit jika validasi gagal
                    }
                }
            });
        })();

        // Fungsi untuk memeriksa status penerima manfaat
        function checkPenerimaStatus() {
            const nikInput = document.querySelector('input[name="penerima[nik]"]');
            const namaInput = document.querySelector('input[name="penerima[nama]"]');

            // Penerima manfaat dianggap terisi jika nik atau nama terisi
            const hasData = (nikInput && nikInput.value.trim() !== '') ||
                (namaInput && namaInput.value.trim() !== '');

            // Tampilkan atau sembunyikan container informasi anak
            document.getElementById('informasi-anak-container').classList.toggle('d-none', !hasData);
            document.getElementById('pesan-penerima-kosong').style.display = hasData ? 'none' : 'block';
        }

        // Fungsi menambah form anak baru
        function tambahAnak() {
            // Validasi jumlah maksimum anak
            if (indexAnak >= MAX_ANAK) {
                alert('Maksimal ' + MAX_ANAK + ' anak yang dapat ditambahkan!');
                return;
            }

            // Pastikan penerima manfaat sudah terisi
            const nikInput = document.querySelector('input[name="penerima[nik]"]');
            const namaInput = document.querySelector('input[name="penerima[nama]"]');

            if ((nikInput.value.trim() === '') && (namaInput.value.trim() === '')) {
                alert('Silakan isi data Penerima Manfaat terlebih dahulu!');
                namaInput.focus();
                return;
            }

            // Ambil template anak dan tambahkan ke wrapper
            const template = document.getElementById('anak-template').innerHTML;
            const htmlBaru = template.replace(/__INDEX__/g, indexAnak);
            document.getElementById('anak-wrapper').insertAdjacentHTML('beforeend', htmlBaru);
            indexAnak++;

            // Beri nomor baru pada anak
            renumberAnakLabels();
        }

        // Fungsi hapus form anak
        function hapusFormIni(button) {
            const confirmHapus = confirm('Apakah Anda yakin ingin menghapus data anak ini?');
            if (confirmHapus) {
                button.closest('.row.mb-4').previousElementSibling.remove(); // Hapus header h6
                button.closest('.row.mb-4').remove(); // Hapus form
                renumberAnakLabels(); // Panggil fungsi untuk menomori ulang label anak
            }
        }

        // Fungsi untuk menomori ulang label anak setelah penghapusan
        function renumberAnakLabels() {
            const anakHeaders = document.querySelectorAll('#anak-wrapper h6');
            anakHeaders.forEach((header, index) => {
                header.textContent = `*ANAK KE-${index + 1}`;
            });
        }

        // Fungsi validasi form anak (hanya nama yang wajib, sesuai form Mobile)
        function cekFormAnak() {
            const anakInputs = document.querySelectorAll('#anak-wrapper input[required], #anak-wrapper select[required]');
            for (let input of anakInputs) {
                if (!input.checkValidity()) {
                    alert('Nama anak wajib diisi!');
                    input.focus();
                    return false;
                }
            }
            return true;
        }
    </script>
@endsection
