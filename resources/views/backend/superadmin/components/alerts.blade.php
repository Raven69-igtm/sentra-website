@if (session('success'))
    <div class="app-alert app-alert-success app-alert-auto mb-4" role="alert">
        <div class="d-flex align-items-start">
            <i class="fa fa-check-circle me-2 mt-1"></i>
            <span class="fw-semibold">{{ session('success') }}</span>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (session('error'))
    <div class="app-alert app-alert-danger app-alert-auto mb-4" role="alert">
        <div class="d-flex align-items-start">
            <i class="fa fa-exclamation-circle me-2 mt-1"></i>
            <span class="fw-semibold">{{ session('error') }}</span>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if (isset($errors) && $errors->any())
    <div class="app-alert app-alert-danger mb-4" role="alert">
        <div class="d-flex align-items-start">
            <i class="fa fa-exclamation-circle me-2 mt-1"></i>
            <div>
                <span class="fw-semibold d-block mb-1">Terjadi kesalahan:</span>
                <ul class="mb-0 pl-3">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
