@extends('backend.layout.admin_layout')

@section('admin')
    @if (session('success'))
        <div class="alert alert-custom-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @php
        $badgeClass = match ($laporan->status) {
            'dikirim', 'diterima' => 'badge-warning',
            'diproses' => 'badge-info',
            'selesai' => 'badge-success',
            'ditolak' => 'badge-danger',
            'dirujuk' => 'badge-secondary',
            default => 'badge-light',
        };
    @endphp

    <div class="container-fluid">
        <div class="row page-titles mx-0">
            <div class="col-sm-6 p-md-0">
                <div class="welcome-text">
                    <h4>PREVIEW LAPORAN</h4>
                </div>
            </div>
            <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dinsos.dashboard') }}">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('dinsos.laporan.index') }}">Laporan</a></li>
                    <li class="breadcrumb-item active"><a href="">{{ $laporan->id_laporan }}</a></li>
                </ol>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12 col-xxl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-header bg-ijo d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0">DETAIL LAPORAN</h4>
                            <span class="badge {{ $badgeClass }}">{{ $laporan->status_label }}</span>
                        </div>
                        <br>
                        <section>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">ID Laporan</label>
                                        <p class="form-control form-control-lg">{{ $laporan->id_laporan }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Layanan PJSRK</label>
                                        <p class="form-control form-control-lg">{{ $laporan->layanan_label }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Tanggal</label>
                                        <p class="form-control form-control-lg">
                                            {{ optional($laporan->created_at)->format('d-m-Y') }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Kategori Laporan</label>
                                        <p class="form-control form-control-lg">{{ $laporan->kategori ?: '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Kecamatan</label>
                                        <p class="form-control form-control-lg">{{ $laporan->kecamatan_tampil }}</p>
                                    </div>
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold">Judul Laporan</label>
                                        <p class="form-control form-control-lg">{{ $laporan->judul_tampil }}</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <br>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">IDENTITAS PELAPOR</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">NIK Pelapor</label>
                                        <p class="form-control">{{ optional($laporan->detail_pelapor)->nik ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Nama Pelapor</label>
                                        <p class="form-control">{{ optional($laporan->detail_pelapor)->nama ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Nomor Telepon Pelapor</label>
                                        <p class="form-control">{{ optional($laporan->detail_pelapor)->no_telp ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Hubungan dengan Korban</label>
                                        <p class="form-control">
                                            {{ optional($laporan->detail_pelapor)->hubungan_dengan_korban ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-12 mb-0">
                                        <label class="font-weight-bold">Alamat Pelapor</label>
                                        <p class="form-control mb-0">{{ optional($laporan->detail_pelapor)->alamat ?: '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">PENERIMA MANFAAT</h4>
                            </div>
                            <div class="card-body">
                                @if ($laporan->detail_penerima_manfaat)
                                    @php $penerima = $laporan->detail_penerima_manfaat; @endphp
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">NIK Penerima Manfaat</label>
                                            <p class="form-control">{{ $penerima->nik ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Nama Penerima Manfaat</label>
                                            <p class="form-control">{{ $penerima->nama ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Tempat Lahir</label>
                                            <p class="form-control">{{ $penerima->tempat_lahir ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Tanggal Lahir</label>
                                            <p class="form-control">{{ $penerima->tanggal_lahir ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Umur</label>
                                            <p class="form-control">{{ $penerima->umur ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Jenis Kelamin</label>
                                            <p class="form-control">{{ $penerima->jenis_kelamin ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Pekerjaan</label>
                                            <p class="form-control">{{ $penerima->pekerjaan ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Agama</label>
                                            <p class="form-control">{{ $penerima->agama ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Pendidikan</label>
                                            <p class="form-control">{{ $penerima->pendidikan ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Nomor Telepon</label>
                                            <p class="form-control">{{ $penerima->notelp ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Hubungan dengan Terlapor</label>
                                            <p class="form-control">{{ $penerima->hubungan_dengan_terlapor ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Informasi Tambahan</label>
                                            <p class="form-control">{{ $penerima->informasi_tambahan ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-0">
                                            <label class="font-weight-bold">Alamat</label>
                                            <p class="form-control mb-0">{{ $penerima->alamat ?: '-' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="mb-0">Data penerima manfaat belum diisi.</p>
                                @endif
                            </div>
                        </div>

                        @if (
                            $laporan->detail_penerima_manfaat &&
                                $laporan->detail_penerima_manfaat->informasi_anak &&
                                $laporan->detail_penerima_manfaat->informasi_anak->count() > 0)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">INFORMASI ANAK</h4>
                                </div>
                                <div class="card-body">
                                    @foreach ($laporan->detail_penerima_manfaat->informasi_anak as $index => $anak)
                                        <div class="border rounded p-3 mb-4">
                                            <h6 class="font-weight-bold mb-3">ANAK KE-{{ $index + 1 }}</h6>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold">Nama Anak</label>
                                                    <p class="form-control">{{ $anak->nama ?: '-' }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold">Tanggal Lahir</label>
                                                    <p class="form-control">{{ $anak->tanggal_lahir ?: '-' }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold">Umur</label>
                                                    <p class="form-control">{{ $anak->umur ?: '-' }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold">Jenis Kelamin</label>
                                                    <p class="form-control">{{ $anak->jenis_kelamin ?: '-' }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold">Pendidikan</label>
                                                    <p class="form-control">{{ $anak->pendidikan ?: '-' }}</p>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="font-weight-bold">Agama</label>
                                                    <p class="form-control">{{ $anak->agama ?: '-' }}</p>
                                                </div>
                                                <div class="col-md-6 mb-0">
                                                    <label class="font-weight-bold">Status</label>
                                                    <p class="form-control mb-0">{{ $anak->status ?: '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">TERLAPOR</h4>
                            </div>
                            <div class="card-body">
                                @if ($laporan->detail_terlapor)
                                    @php $terlapor = $laporan->detail_terlapor; @endphp
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">NIK Terlapor</label>
                                            <p class="form-control">{{ $terlapor->nik ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Nama Terlapor</label>
                                            <p class="form-control">{{ $terlapor->nama ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Umur</label>
                                            <p class="form-control">{{ $terlapor->umur ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Jenis Kelamin</label>
                                            <p class="form-control">{{ $terlapor->jenis_kelamin ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Hubungan dengan Korban</label>
                                            <p class="form-control">{{ $terlapor->hubungan_dengan_korban ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold">Informasi Tambahan</label>
                                            <p class="form-control">{{ $terlapor->informasi_tambahan ?: '-' }}</p>
                                        </div>
                                        <div class="col-md-12 mb-0">
                                            <label class="font-weight-bold">Alamat</label>
                                            <p class="form-control mb-0">{{ $terlapor->alamat ?: '-' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="mb-0">Data terlapor belum diisi.</p>
                                @endif
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h4 class="mb-0">DETAIL KASUS</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Tanggal Kejadian</label>
                                        <p class="form-control">{{ optional($laporan->detail_kasus)->tanggal ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="font-weight-bold">Tempat Kejadian</label>
                                        <p class="form-control">
                                            {{ optional($laporan->detail_kasus)->tempat_kejadian ?: '-' }}</p>
                                    </div>
                                    <div class="col-md-12 mb-0">
                                        <label class="font-weight-bold">Kronologi Kejadian</label>
                                        <div class="p-4 bg-white border rounded shadow-sm"
                                            style="line-height: 1.7; font-size: 1rem; color: #333;">
                                            {!! nl2br(e(optional($laporan->detail_kasus)->kronologi ?: 'Kronologi belum diisi.')) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($laporan->status === 'ditolak' && $laporan->alasan_penolakan)
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h4 class="mb-0">ALASAN PENOLAKAN</h4>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-danger mb-0">{{ $laporan->alasan_penolakan }}</div>
                                </div>
                            </div>
                        @endif

                        <div class="mt-4 d-flex justify-content-end flex-wrap">
                            <a href="{{ route('dinsos.laporan.index') }}" class="btn btn-light mr-2 mb-2">Kembali</a>

                            @if (in_array($laporan->status, ['dikirim', 'diterima'], true))
                                <form method="POST"
                                    action="{{ route('dinsos.laporan.verifikasi', $laporan->id_laporan) }}"
                                    class="mr-2 mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-primary">Verifikasi Laporan</button>
                                </form>
                            @endif

                            @if (in_array($laporan->status, ['dikirim', 'diterima', 'diproses'], true))
                                <form method="POST" action="{{ route('dinsos.laporan.proses', $laporan->id_laporan) }}"
                                    class="mr-2 mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-warning">Tandai Diproses</button>
                                </form>
                            @endif

                            @if (in_array($laporan->status, ['diproses', 'diterima'], true))
                                <form method="POST" action="{{ route('dinsos.laporan.selesai', $laporan->id_laporan) }}"
                                    class="mr-2 mb-2">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-hijau">Selesaikan Laporan</button>
                                </form>
                            @endif

                            @if (in_array($laporan->status, ['dikirim', 'diterima', 'diproses'], true))
                                <button type="button" class="btn btn-danger mb-2 js-reject" data-toggle="modal"
                                    data-target="#rejectModal"
                                    data-action="{{ route('dinsos.laporan.tolak', $laporan->id_laporan) }}"
                                    data-ticket="{{ $laporan->id_laporan }}">
                                    Tolak Laporan
                                </button>
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
                <div class="modal-header bg-ijo">
                    <h5 class="modal-title" id="rejectModalLabel">Tolak Laporan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="rejectForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <p id="rejectDescription">Tambahkan alasan penolakan untuk laporan ini.</p>
                        <div class="form-group mb-0">
                            <label class="font-weight-bold">Alasan Penolakan</label>
                            <textarea name="alasan_penolakan" class="form-control" rows="4" required maxlength="1000"
                                placeholder="Contoh: Laporan tidak sesuai dengan layanan Dinsos atau data tidak lengkap."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('rejectModal');
            var form = document.getElementById('rejectForm');
            var description = document.getElementById('rejectDescription');

            document.querySelectorAll('.js-reject').forEach(function(button) {
                button.addEventListener('click', function() {
                    form.action = button.dataset.action;
                    description.textContent = 'Tambahkan alasan penolakan untuk tiket ' + button.dataset.ticket + '.';
                });
            });

            if (window.jQuery) {
                window.jQuery(modal).on('hidden.bs.modal', function() {
                    form.reset();
                });
            }
        });
    </script>
@endpush
