@extends('backend.layout.admin_layout')
@section('admin')
    @if ($errors->has('kasus'))
        <div class="alert alert-danger">
            {{ $errors->first('kasus') }}
        </div>
    @endif
    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>EDIT LAPORAN</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('laporan_proses') }}">Laporan</a></li>
                    <li class="breadcrumb-item active"><a href="{{ route('laporan_proses') }}">Diproses</a></li>
                    <li class="breadcrumb-item active"><a href="">Edit</a></li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 col-xxl-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('laporan.update', $laporan->id_laporan) }}" method="POST"
                            id="step-form-horizontal" class="step-form-horizontal">
                            @csrf
                            @method('PUT')
                            <div class="card-header bg-ijo">
                                <h4 class="card-title">LAPORAN</h4>
                            </div>
                            <br><br>
                            <div>
                                <section>
                                    <div class="row">
                                        <div class="col-lg-12 mb-10">
                                            <div class="form-group">
                                                <label>ID Laporan</label>
                                                <input type="text" name="id" class="form-control form-control-lg"
                                                    value="{{ $laporan->id_laporan }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-10">
                                            <div class="form-group">
                                                <label>Kategori Laporan</label>
                                                <select name="kategori" class="form-control form-control-lg">
                                                    <option value="Kekerasan Fisik"
                                                        {{ $laporan->kategori == 'Kekerasan Fisik' ? 'selected' : '' }}>
                                                        Kekerasan Fisik
                                                    </option>
                                                    <option value="Kekerasan Psikis"
                                                        {{ $laporan->kategori == 'Kekerasan Psikis' ? 'selected' : '' }}>
                                                        Kekerasan Psikis
                                                    </option>
                                                    <option value="Kekerasan Seksual"
                                                        {{ $laporan->kategori == 'Kekerasan Seksual' ? 'selected' : '' }}>
                                                        Kekerasan Seksual
                                                    </option>
                                                    <option value="Penelantaran"
                                                        {{ $laporan->kategori == 'Penelantaran' ? 'selected' : '' }}>
                                                        Penelantaran
                                                    </option>
                                                    <option value="Eksploitasi"
                                                        {{ $laporan->kategori == 'Eksploitasi' ? 'selected' : '' }}>
                                                        Eksploitasi
                                                    </option>
                                                    <option value="TPPO"
                                                        {{ $laporan->kategori == 'TPPO' ? 'selected' : '' }}>
                                                        TPPO
                                                    </option>
                                                    <option value="unset"
                                                        {{ $laporan->kategori == 'unset' ? 'selected' : '' }}>
                                                        -</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-10">
                                            <div class="form-group">
                                                <label>Tanggal</label>
                                                <input type="date" name="tanggal" class="form-control form-control-lg"
                                                    value="{{ $laporan->created_at->format('Y-m-d') }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </section>
                                <br>
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">IDENTITAS PELAPOR</h4>
                                </div>
                                <section>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>NIK Pelapor</label>
                                            <input type="text" name="pelapor[nik]" class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->nik }}" required
                                                oninvalid="this.setCustomValidity('NIK salah')"
                                                oninput="this.setCustomValidity('')" pattern="\d{16}" inputmode="numeric"
                                                maxlength="16">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Nama Pelapor</label>
                                            <input type="text" name="pelapor[nama]" class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->nama }}" required
                                                oninvalid="this.setCustomValidity('Data pelapor wajib  diisi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Alamat Pelapor</label>
                                            <input type="text" name="pelapor[alamat]" class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->alamat }}" required
                                                oninvalid="this.setCustomValidity('Data pelapor wajib diisi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Hubungan dengan Korban</label>
                                            <input type="text" name="pelapor[hubungan]" class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->hubungan_dengan_korban }}"
                                                required oninvalid="this.setCustomValidity('Data pelapor wajib diisi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Nomor Telepon Pelapor</label>
                                            <input type="text" name="pelapor[telepon]" class="form-control"
                                                value="{{ optional($laporan->detail_pelapor)->no_telp }}" required
                                                oninvalid="this.setCustomValidity('Nomor telepon salah')"
                                                oninput="this.setCustomValidity('')" pattern="\d{11,15}" inputmode="numeric"
                                                maxlength="15">
                                        </div>
                                    </div>
                                </section>
                                <br>
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">PENERIMA MANFAAT</h4>
                                </div>
                                <section>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>NIK Penerima Manfaat</label>
                                            <input type="text" name="penerima[nik]" class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->nik }}"
                                                oninvalid="this.setCustomValidity('NIK salah')"
                                                oninput="this.setCustomValidity('')" pattern="(\d{16}|-)?" inputmode="numeric"
                                                maxlength="16">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Nama Penerima Manfaat</label>
                                            <input type="text" name="penerima[nama]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->nama }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Tempat Lahir</label>
                                            <input type="text" name="penerima[ttl]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->tempat_lahir }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Tanggal Lahir</label>
                                            <input type="date" name="penerima[tanggal]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->tanggal_lahir }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Umur</label>
                                            <input type="number" name="penerima[umur]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->umur }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Jenis Kelamin</label>
                                            <select name="penerima[jk]" class="form-control penerima-input">
                                                <option value="">Pilih</option>
                                                <option value="Laki-laki"
                                                    {{ optional($laporan->detail_penerima_manfaat)->jk == 'Laki-laki' ? 'selected' : '' }}>
                                                    Laki-Laki</option>
                                                <option value="Perempuan"
                                                    {{ optional($laporan->detail_penerima_manfaat)->jk == 'Perempuan' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Pekerjaan</label>
                                            <input type="text" name="penerima[pekerjaan]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->pekerjaan }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Agama</label>
                                            <select name="penerima[agama]" class="form-control penerima-input">
                                                <option value="">Pilih</option>
                                                @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'lainnya'] as $agama)
                                                    <option value="{{ $agama }}"
                                                        {{ optional($laporan->detail_penerima_manfaat)->agama == $agama ? 'selected' : '' }}>
                                                        {{ $agama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Pendidikan</label>
                                            <select name="penerima[pendidikan]" class="form-control penerima-input">
                                                <option value="">Pilih</option>
                                                @foreach (['Tidak Sekolah', 'SD', 'SMP', 'SMA', 'Diploma', 'S1', 'S2', 'S3', 'Lainnya'] as $pendidikan)
                                                    <option value="{{ $pendidikan }}"
                                                        {{ optional($laporan->detail_penerima_manfaat)->pendidikan == $pendidikan ? 'selected' : '' }}>
                                                        {{ $pendidikan }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Alamat</label>
                                            <input type="text" name="penerima[alamat]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->alamat }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Hubungan dengan Terlapor</label>
                                            <input type="text" name="penerima[hubungan_terlapor]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->hubungan_dengan_terlapor }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Nomor Telepon</label>
                                            <input type="text" name="penerima[telepon]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->notelp }}"
                                                oninvalid="this.setCustomValidity('Nomor telepon salah')"
                                                oninput="this.setCustomValidity('')" pattern="\d{11,15}"
                                                inputmode="numeric" maxlength="15">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Informasi Tambahan</label>
                                            <input type="text" name="penerima[informasi_tambahan]"
                                                class="form-control penerima-input"
                                                value="{{ optional($laporan->detail_penerima_manfaat)->informasi_tambahan }}">
                                        </div>
                                    </div>
                                </section>
                                <br>
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">INFORMASI ANAK</h4>
                                </div>
                                <div id="informasi-anak-container"
                                    {{ $laporan->detail_penerima_manfaat ? '' : 'class=d-none' }}>
                                    <section id="anak-wrapper">
                                        @if (
                                            $laporan->detail_penerima_manfaat &&
                                                $laporan->detail_penerima_manfaat->informasi_anak &&
                                                count($laporan->detail_penerima_manfaat->informasi_anak) > 0)
                                            @foreach ($laporan->detail_penerima_manfaat->informasi_anak as $index => $anak)
                                                <h6>*ANAK KE-{{ $index + 1 }}</h6>
                                                <div class="row mb-4 border rounded p-3">
                                                    <input type="hidden" name="anak[{{ $index }}][id]"
                                                        value="{{ $anak->id_anak }}">
                                                    <div class="col-md-6 mb-3">
                                                        <label>Nama Anak</label>
                                                        <input type="text" name="anak[{{ $index }}][nama]"
                                                            class="form-control" value="{{ $anak->nama }}" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Tanggal Lahir</label>
                                                        <input type="date" name="anak[{{ $index }}][tanggal]"
                                                            class="form-control" value="{{ $anak->tanggal_lahir }}"
                                                            required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Umur</label>
                                                        <input type="number" name="anak[{{ $index }}][umur]"
                                                            class="form-control" value="{{ $anak->umur }}" required>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Jenis Kelamin</label>
                                                        <select name="anak[{{ $index }}][jenis_kelamin]"
                                                            class="form-control" required>
                                                            <option value="Laki-laki"
                                                                {{ $anak->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>
                                                                Laki-Laki</option>
                                                            <option value="Perempuan"
                                                                {{ $anak->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>
                                                                Perempuan</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Pendidikan</label>
                                                        <select name="anak[{{ $index }}][pendidikan]"
                                                            class="form-control" required>
                                                            @foreach (['Tidak Sekolah', 'PAUD', 'TK', 'SD', 'SMP', 'SMA', 'Lainnya'] as $pendidikan)
                                                                <option value="{{ $pendidikan }}"
                                                                    {{ $anak->pendidikan == $pendidikan ? 'selected' : '' }}>
                                                                    {{ $pendidikan }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Agama</label>
                                                        <select name="anak[{{ $index }}][agama]"
                                                            class="form-control" required>
                                                            @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu', 'lainnya'] as $agama)
                                                                <option value="{{ $agama }}"
                                                                    {{ $anak->agama == $agama ? 'selected' : '' }}>
                                                                    {{ $agama }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label>Status</label>
                                                        <select name="anak[{{ $index }}][status]"
                                                            class="form-control" required>
                                                            <option value="Anak Kandung"
                                                                {{ $anak->status == 'Anak Kandung' ? 'selected' : '' }}>
                                                                Anak Kandung</option>
                                                            <option value="Anak Angkat"
                                                                {{ $anak->status == 'Anak Angkat' ? 'selected' : '' }}>Anak
                                                                Angkat</option>
                                                        </select>
                                                    </div>

                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-danger btn-sm mt-2"
                                                            onclick="hapusFormIni(this)">Hapus Anak Ini</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </section>

                                    <!-- Tombol tambah anak -->
                                    <button type="button" class="btn btn-success btn-sm mb-3" id="btn-tambah-anak"
                                        onclick="tambahAnak()">+ Tambah Anak</button>
                                </div>

                                <div class="alert alert-warning" id="pesan-penerima-kosong"
                                    {{ $laporan->detail_penerima_manfaat ? 'style=display:none' : '' }}>
                                    <strong>Perhatian!</strong> Silakan isi data Penerima Manfaat terlebih dahulu sebelum
                                    menambahkan informasi anak.
                                </div>

                                <br>
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">TERLAPOR</h4>
                                </div>
                                <section>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>NIK Terlapor</label>
                                            <input type="text" name="terlapor[nik]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->nik }}"
                                                oninvalid="this.setCustomValidity('NIK salah')"
                                                oninput="this.setCustomValidity('')" pattern="(\d{16}|-)?" inputmode="numeric"
                                                maxlength="16">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Nama Terlapor</label>
                                            <input type="text" name="terlapor[nama]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->nama }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Umur</label>
                                            <input type="number" name="terlapor[umur]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->umur }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Alamat</label>
                                            <input type="text" name="terlapor[alamat]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->alamat }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Jenis Kelamin</label>
                                            <select name="terlapor[jk]" class="form-control">
                                                <option value="">Pilih</option>
                                                <option value="Laki-laki"
                                                    {{ optional($laporan->detail_terlapor)->jk == 'Laki-laki' ? 'selected' : '' }}>
                                                    Laki-Laki</option>
                                                <option value="Perempuan"
                                                    {{ optional($laporan->detail_terlapor)->jk == 'Perempuan' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Hubungan dengan Korban</label>
                                            <input type="text" name="terlapor[hubungan]" class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->hubungan_dengan_korban }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Informasi Tambahan</label>
                                            <input type="text" name="terlapor[informasi_tambahan]"
                                                class="form-control"
                                                value="{{ optional($laporan->detail_terlapor)->informasi_tambahan }}">
                                        </div>
                                    </div>
                                </section>
                                <br>
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">DETAIL KASUS</h4>
                                </div>
                                <section>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Tanggal Kejadian</label>
                                            <input type="date" name="kasus[tanggal]" class="form-control"
                                                value="{{ optional($laporan->detail_kasus)->tanggal }}" required
                                                oninvalid="this.setCustomValidity('Detail kasus wajib di isi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Tempat Kejadian</label>
                                            <input type="text" name="kasus[tempat]" class="form-control"
                                                value="{{ optional($laporan->detail_kasus)->tempat_kejadian }}" required
                                                oninvalid="this.setCustomValidity('Detail kasus wajib di isi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                        <div class="col-md-12 mb-3">
                                            <label>Kronologi Kejadian</label>
                                            <textarea name="kasus[kronologi]" class="form-control" rows="5" required
                                                oninvalid="this.setCustomValidity('Detail kasus wajib di isi')" oninput="this.setCustomValidity('')">{{ optional($laporan->detail_kasus)->kronologi }}</textarea>
                                        </div>
                                    </div>
                                </section>
                            </div>
                            <div class="mt-4 text-right">
                                <button type="submit" class="btn btn-hijau">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template anak baru (dipindahkan ke luar form) -->
    <template id="anak-template">
        <h6>*ANAK BARU</h6>
        <div class="row mb-4 border rounded p-3">
            <div class="col-md-6 mb-3">
                <label>Nama</label>
                <input type="text" name="anak[__INDEX__][nama]" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Tanggal Lahir</label>
                <input type="date" name="anak[__INDEX__][tanggal]" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Umur</label>
                <input type="number" name="anak[__INDEX__][umur]" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Jenis Kelamin</label>
                <select name="anak[__INDEX__][jenis_kelamin]" class="form-control" required>
                    <option value="">Pilih</option>
                    <option value="Laki-laki">Laki-Laki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Pendidikan</label>
                <select name="anak[__INDEX__][pendidikan]" class="form-control" required>
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
            <div class="col-md-6 mb-3">
                <label>Agama</label>
                <select name="anak[__INDEX__][agama]" class="form-control" required>
                    <option value="">Pilih</option>
                    <option value="Islam">Islam</option>
                    <option value="Kristen">Kristen</option>
                    <option value="Katolik">Katolik</option>
                    <option value="Hindu">Hindu</option>
                    <option value="Buddha">Buddha</option>
                    <option value="Konghucu">Konghucu</option>
                    <option value="lainnya">lainnya</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Status</label>
                <select name="anak[__INDEX__][status]" class="form-control" required>
                    <option value="">Pilih</option>
                    <option value="Anak Kandung">Anak Kandung</option>
                    <option value="Anak Angkat">Anak Angkat</option>
                </select>
            </div>

            <!-- Tombol hapus form baru -->
            <div class="col-12">
                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="hapusFormIni(this)">Hapus Form
                    Ini</button>
            </div>
        </div>
    </template>

    <!-- JavaScript -->
    <script>
        // Variabel untuk menyimpan jumlah maksimum anak yang dapat ditambahkan
        const MAX_ANAK = 10;

        // Menentukan index anak berdasarkan jumlah anak yang ada
        let indexAnak =
            {{ $laporan->detail_penerima_manfaat && $laporan->detail_penerima_manfaat->informasi_anak
                ? count($laporan->detail_penerima_manfaat->informasi_anak)
                : 0 }};

        // Mendeteksi perubahan pada form penerima manfaat saat dokumen siap
        document.addEventListener('DOMContentLoaded', function() {
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
            document.getElementById('step-form-horizontal').addEventListener('submit', function(e) {
                // Validasi data anak jika form anak tampil
                if (!document.getElementById('informasi-anak-container').classList.contains('d-none')) {
                    if (!cekFormAnak()) {
                        e.preventDefault(); // Hentikan submit jika validasi gagal
                    }
                }
            });
        });

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

        // Fungsi validasi form anak
        function cekFormAnak() {
            const anakInputs = document.querySelectorAll('#anak-wrapper input[required], #anak-wrapper select[required]');
            for (let input of anakInputs) {
                if (!input.checkValidity()) {
                    alert('Semua field anak wajib diisi!');
                    input.focus();
                    return false;
                }
            }
            return true;
        }
    </script>
@endsection
