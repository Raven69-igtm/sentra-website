{{-- Alert terpadu instansi: sukses / error / info / validasi.
     Dipakai lewat @include('backend.components.alerts') di setiap halaman. --}}
@if (session('success'))
    <div class="app-alert app-alert-success">
        <span><i class="fa fa-circle-check me-2"></i>{{ session('success') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('status'))
    <div class="app-alert app-alert-success">
        <span><i class="fa fa-circle-check me-2"></i>{{ session('status') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="app-alert app-alert-danger">
        <span><i class="fa fa-circle-exclamation me-2"></i>{{ session('error') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('info'))
    <div class="app-alert app-alert-info">
        <span><i class="fa fa-circle-info me-2"></i>{{ session('info') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if ($errors->any())
    <div class="app-alert app-alert-danger">
        <div>
            <strong class="d-block mb-1"><i class="fa fa-triangle-exclamation me-2"></i>Periksa kembali isian Anda</strong>
            <ul class="mb-0 pl-0" style="list-style: none;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
