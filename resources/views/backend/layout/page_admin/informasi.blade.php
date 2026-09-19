@extends('backend.layout.admin_layout')

@section('admin')
    <div class="container-fluid">
        @include('backend.components.alerts')

        @include('backend.components.page-header', [
            'title' => 'Informasi',
            'subtitle' => 'Kelola konten informasi yang tampil pada aplikasi masyarakat',
            'icon' => 'fa-circle-info',
        ])

        {{-- FORMULIR --}}
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">{{ isset($informasi) ? 'Ubah Informasi' : 'Tambah Informasi' }}</h4>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">
                        Judul, deskripsi, dan gambar pendukung konten
                    </p>
                </div>
                @if (isset($informasi))
                    <a href="{{ route('informasi.index') }}" class="btn btn-light btn-pill btn-sm">
                        <i class="fa fa-xmark me-2"></i> Batal Edit
                    </a>
                @endif
            </div>
            <div class="card-body">
                <form
                    action="{{ isset($informasi) ? route('informasi.update', $informasi->id_informasi) : route('informasi.store') }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($informasi))
                        @method('PUT')
                    @endif

                    <div class="row">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label class="form-label" for="judulInformasi">Judul Informasi</label>
                                <input type="text" id="judulInformasi" name="judul"
                                    class="form-control @error('judul') is-invalid @enderror"
                                    placeholder="Masukkan judul informasi"
                                    value="{{ isset($informasi) ? $informasi->judul : old('judul') }}">
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="form-label" for="customFile">Gambar Pendukung</label>
                                <div class="custom-file">
                                    <input type="file" name="gambar"
                                        class="custom-file-input @error('gambar') is-invalid @enderror" id="customFile">
                                    <label class="custom-file-label" for="customFile">
                                        {{ isset($informasi) && $informasi->gambar ? $informasi->gambar : 'Pilih gambar' }}
                                    </label>
                                    @error('gambar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="deskripsiInformasi">Deskripsi Informasi</label>
                        <textarea id="deskripsiInformasi" name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                            rows="4" placeholder="Masukkan deskripsi informasi">{{ isset($informasi) ? $informasi->deskripsi : old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-instansi-primary btn-pill">
                        <i class="fa fa-floppy-disk me-2"></i>
                        {{ isset($informasi) ? 'Update' : 'Simpan' }}
                    </button>
                    @if (isset($informasi))
                        <a href="{{ route('informasi.index') }}" class="btn btn-light btn-pill ml-2">Batal</a>
                    @endif
                </form>
            </div>
        </div>

        {{-- DAFTAR INFORMASI --}}
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-1">Daftar Informasi</h4>
                    <p class="mb-0 text-muted" style="font-size: .82rem;">Semua konten yang sudah dipublikasikan</p>
                </div>
                <div style="min-width: 240px;">
                    <input type="text" id="searchJudul" class="form-control"
                        placeholder="Cari judul informasi...">
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="InformasiTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Informasi</th>
                                <th>Deskripsi</th>
                                <th>Gambar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($informasiList as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $item->judul }}</td>
                                    <td class="text-muted">{{ \Illuminate\Support\Str::limit($item->deskripsi, 100) }}</td>
                                    <td>
                                        @if ($item->gambar)
                                            <img src="{{ asset('uploads/informasi/' . $item->gambar) }}"
                                                alt="{{ $item->judul }}" class="info-thumb">
                                        @else
                                            <span class="text-muted" style="font-size: .82rem;">Tidak ada gambar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <form action="{{ route('informasi.edit', $item->id_informasi) }}"
                                                method="GET">
                                                <button type="submit" class="btn btn-instansi-soft btn-pill btn-sm">
                                                    Edit
                                                </button>
                                            </form>

                                            <form method="POST"
                                                action="{{ route('informasi.destroy', $item->id_informasi) }}"
                                                id="delete-form-{{ $item->id_informasi }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-pill btn-sm"
                                                    onclick="confirmDelete({{ $item->id_informasi }})">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- INI SCRIPT JS --}}
@push('scripts')
    <script>
        // Custom file input label
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    </script>

    <script>
        $(document).ready(function() {
            const table = $('#InformasiTable').DataTable({
                dom: 'rt',
                ordering: false,
            });

            // Search berdasarkan judul informasi (kolom ke-1)
            $('#searchJudul').on('keyup', function() {
                table.column(1).search(this.value).draw();
            });
        });
    </script>
    @include('backend.components.style-confirm')
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                iconColor: '#ff4d4f',
                showCancelButton: true,
                confirmButtonColor: '#059652',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                buttonsStyling: true,
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
