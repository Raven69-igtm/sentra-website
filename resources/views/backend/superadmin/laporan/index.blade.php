@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-uppercase fw-bold"><i class="fa fa-file-text-o me-2 text-primary"></i> Pengawasan & Audit Laporan</h4>
                <p class="mb-0 text-muted">Pemantauan global seluruh kasus yang ditangani oleh Unit PPA dan Dinas Sosial</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.dashboard') }}">Superadmin</a></li>
                <li class="breadcrumb-item active"><a href="{{ route('superadmin.laporan.index') }}">Audit Laporan</a></li>
            </ol>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Seluruh Laporan Masuk</h5>
            <span class="badge badge-info px-3 py-2">
                Total: {{ $laporanList->total() }} Laporan
            </span>
        </div>
        <div class="card-body">
            <!-- Filter Bar -->
            <form method="GET" action="{{ route('superadmin.laporan.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-md-3 col-sm-6">
                        <input type="text" name="q" class="form-control" placeholder="Cari ID atau nama pelapor..." value="{{ request('q') }}">
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <select name="instansi" class="form-control">
                            <option value="">-- Wewenang Instansi --</option>
                            <option value="ppa" {{ request('instansi') === 'ppa' ? 'selected' : '' }}>Unit PPA (Kekerasan)</option>
                            <option value="dinsos" {{ request('instansi') === 'dinsos' ? 'selected' : '' }}>Dinas Sosial (Sosial & Rujukan)</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <select name="kategori" class="form-control">
                            <option value="">-- Kategori Kasus --</option>
                            <option value="Kekerasan Fisik" {{ request('kategori') === 'Kekerasan Fisik' ? 'selected' : '' }}>Kekerasan Fisik</option>
                            <option value="Kekerasan Psikis" {{ request('kategori') === 'Kekerasan Psikis' ? 'selected' : '' }}>Kekerasan Psikis</option>
                            <option value="Kekerasan Seksual" {{ request('kategori') === 'Kekerasan Seksual' ? 'selected' : '' }}>Kekerasan Seksual</option>
                            <option value="Penelantaran" {{ request('kategori') === 'Penelantaran' ? 'selected' : '' }}>Penelantaran</option>
                            <option value="Eksploitasi" {{ request('kategori') === 'Eksploitasi' ? 'selected' : '' }}>Eksploitasi</option>
                            <option value="TPPO" {{ request('kategori') === 'TPPO' ? 'selected' : '' }}>TPPO</option>
                            <option value="Lainnya" {{ request('kategori') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-6">
                        <select name="status" class="form-control">
                            <option value="">-- Status Kasus --</option>
                            <option value="dikirim" {{ request('status') === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="dirujuk" {{ request('status') === 'dirujuk' ? 'selected' : '' }}>Dirujuk</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-2 col-sm-12 d-flex">
                        <button type="submit" class="btn btn-info me-2 w-100">
                            <i class="fa fa-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('superadmin.laporan.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-primary">
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>ID Laporan</th>
                            <th>Nama Pelapor</th>
                            <th>Kategori Kasus</th>
                            <th>Instansi Pembina</th>
                            <th>Status</th>
                            <th>Tanggal Lapor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanList as $index => $item)
                            @php
                                $isPPA = in_array($item->kategori, ['Kekerasan Fisik', 'Kekerasan Psikis', 'Kekerasan Seksual', 'Eksploitasi', 'TPPO']);
                            @endphp
                            <tr>
                                <td>{{ $laporanList->firstItem() + $index }}</td>
                                <td><code>{{ $item->id_laporan }}</code></td>
                                <td><strong>{{ $item->detail_pelapor->nama ?? 'Anonim' }}</strong></td>
                                <td>
                                    <span class="badge badge-light text-dark border">{{ $item->kategori }}</span>
                                </td>
                                <td>
                                    @if ($item->status === 'dirujuk')
                                        <span class="badge badge-warning text-white">Dirujuk ke Dinsos</span>
                                    @elseif ($isPPA)
                                        <span class="badge badge-primary">Unit PPA</span>
                                    @else
                                        <span class="badge badge-info">Dinas Sosial</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status === 'selesai')
                                        <span class="badge badge-success"><i class="fa fa-check"></i> Selesai</span>
                                    @elseif ($item->status === 'dirujuk')
                                        <span class="badge badge-warning text-white"><i class="fa fa-share"></i> Dirujuk</span>
                                    @elseif ($item->status === 'diproses')
                                        <span class="badge badge-info"><i class="fa fa-refresh"></i> Diproses</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td>{{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}</td>
                                <td>
                                    <a href="{{ route('superadmin.laporan.show', $item->id_laporan) }}" class="btn btn-sm btn-outline-primary" title="Lihat Detail Audit">
                                        <i class="fa fa-eye me-1"></i> Detail Audit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Data laporan tidak ditemukan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $laporanList->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
