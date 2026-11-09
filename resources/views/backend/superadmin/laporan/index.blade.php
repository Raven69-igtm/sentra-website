@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Pengawasan & Audit Laporan',
        'subtitle' => 'Pemantauan global seluruh kasus yang ditangani oleh Unit PPA dan Dinas Sosial.',
        'icon' => 'fa-file-lines',
    ])

    @include('backend.superadmin.components.alerts')

    <div class="card p-2">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Daftar Seluruh Laporan Masuk</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Menampilkan {{ $laporanList->count() }} dari {{ $laporanList->total() }} laporan.
                    </p>
                </div>
                <span class="badge badge-soft-blue badge-pill-soft mt-2 mt-sm-0" style="font-size: 0.76rem;">
                    <i class="fa fa-copy me-1"></i> Total: {{ $laporanList->total() }} Laporan
                </span>
            </div>

            <!-- Filter Bar -->
            <form method="GET" action="{{ route('superadmin.laporan.index') }}" class="mb-4">
                <div class="row g-2 align-items-center">
                    <div class="col-lg-3 col-md-6">
                        <div class="search-pill-wrapper w-100" style="height: 44px;">
                            <i class="fa fa-search text-muted"></i>
                            <input type="text" name="q" placeholder="Cari ID atau nama pelapor..." value="{{ request('q') }}">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="instansi" class="form-control">
                            <option value="">-- Wewenang Instansi --</option>
                            <option value="ppa" {{ request('instansi') === 'ppa' ? 'selected' : '' }}>Unit PPA (Kekerasan)</option>
                            <option value="dinsos" {{ request('instansi') === 'dinsos' ? 'selected' : '' }}>Dinas Sosial (Sosial & Rujukan)</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
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
                    <div class="col-lg-2 col-md-6">
                        <select name="status" class="form-control">
                            <option value="">-- Status Kasus --</option>
                            <option value="dikirim" {{ request('status') === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="diterima" {{ request('status') === 'diterima' ? 'selected' : '' }}>Diterima</option>
                            <option value="diproses" {{ request('status') === 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="dirujuk" {{ request('status') === 'dirujuk' ? 'selected' : '' }}>Dirujuk</option>
                            <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12 d-flex">
                        <button type="submit" class="btn btn-oripio-dark btn-pill me-2 flex-grow-1" style="height: 44px;">
                            <i class="fa fa-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('superadmin.laporan.index') }}" class="btn btn-oripio-light btn-pill" style="height: 44px;">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>ID Laporan</th>
                            <th>Nama Pelapor</th>
                            <th>Kategori Kasus</th>
                            <th>Instansi Pembina</th>
                            <th>Status</th>
                            <th>Tanggal Lapor</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporanList as $index => $item)
                            @php
                                $isPPA = in_array($item->kategori, ['Kekerasan Fisik', 'Kekerasan Psikis', 'Kekerasan Seksual', 'Eksploitasi', 'TPPO']);
                            @endphp
                            <tr>
                                <td class="text-muted fw-bold">{{ $laporanList->firstItem() + $index }}</td>
                                <td><code class="text-muted" style="font-size: 0.8rem;">{{ $item->id_laporan }}</code></td>
                                <td><span class="fw-bold text-dark" style="font-size: 0.88rem;">{{ $item->detail_pelapor->nama ?? 'Anonim' }}</span></td>
                                <td>
                                    <span class="badge-pill-soft badge-soft-gray">{{ $item->kategori }}</span>
                                </td>
                                <td>
                                    @if ($item->status === 'dirujuk')
                                        <span class="badge-pill-soft badge-soft-amber">Dirujuk ke Dinsos</span>
                                    @elseif ($isPPA)
                                        <span class="badge-pill-soft badge-soft-blue">Unit PPA</span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-amber">Dinas Sosial</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($item->status === 'selesai')
                                        <span class="badge-pill-soft badge-soft-green"><i class="fa fa-check me-1"></i> Selesai</span>
                                    @elseif ($item->status === 'dirujuk')
                                        <span class="badge-pill-soft badge-soft-amber"><i class="fa fa-share me-1"></i> Dirujuk</span>
                                    @elseif ($item->status === 'diproses')
                                        <span class="badge-pill-soft badge-soft-blue"><i class="fa fa-refresh me-1"></i> Diproses</span>
                                    @else
                                        <span class="badge-pill-soft badge-soft-gray">{{ ucfirst($item->status) }}</span>
                                    @endif
                                </td>
                                <td class="text-muted text-nowrap" style="font-size: 0.82rem;">{{ $item->created_at ? $item->created_at->format('d M Y H:i') : '-' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('superadmin.laporan.show', $item->id_laporan) }}" class="btn btn-oripio-light btn-pill" title="Lihat Detail Audit" style="font-size: 0.78rem; padding: 6px 14px;">
                                        <i class="fa fa-eye me-1"></i> Detail Audit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="fa fa-file-lines fa-3x mb-2 text-muted opacity-50"></i>
                                    <p class="mb-0">Data laporan tidak ditemukan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-4">
                {{ $laporanList->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
</div>
@endsection
