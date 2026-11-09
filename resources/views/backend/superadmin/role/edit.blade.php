@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Atur Hak Akses',
        'subtitle' => 'Perbarui penugasan izin modul untuk peran ' . $role->display_name . '.',
        'icon' => 'fa-sliders',
        'actionUrl' => route('superadmin.roles.index'),
        'actionLabel' => 'Kembali ke Daftar',
        'actionIcon' => 'fa-arrow-left',
        'actionClass' => 'btn-oripio-light',
    ])

    @include('backend.superadmin.components.alerts')

    <div class="card p-2">
        <div class="card-body p-3">
            <div class="d-flex align-items-center mb-4">
                <div class="page-head-icon" style="width: 40px; height: 40px; flex-basis: 40px; font-size: 0.95rem;">
                    <i class="fa fa-id-badge"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Informasi & Matriks Peran</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">
                        Slug peran: <code class="text-muted">{{ $role->name }}</code>
                    </p>
                </div>
            </div>

            <form action="{{ route('superadmin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Nama Slug</label>
                            <input type="text" class="form-control" value="{{ $role->name }}" disabled readonly>
                            <small class="text-muted">Nama slug sistem terkunci untuk mencegah kegagalan relasi kode.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Nama Tampilan <span class="text-danger">*</span></label>
                            <input type="text" name="display_name" class="form-control" value="{{ old('display_name', $role->display_name) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3 mb-0">
                    <label class="fw-bold">Deskripsi Peran</label>
                    <textarea name="description" class="form-control" rows="2">{{ old('description', $role->description) }}</textarea>
                </div>

                @if ($role->name === 'superadmin')
                    <div class="app-alert app-alert-info mt-3" role="alert">
                        <div class="d-flex align-items-start">
                            <i class="fa fa-info-circle me-2 mt-1"></i>
                            <span>Peran <strong>Superadmin</strong> memiliki hak akses penuh (Wildcard) ke seluruh fitur aplikasi secara otomatis tanpa perlu dipilih satu per satu.</span>
                        </div>
                    </div>
                @endif

                <hr class="my-4" style="border-color: var(--oripio-border-subtle);">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="page-head-icon" style="width: 36px; height: 36px; flex-basis: 36px; font-size: 0.9rem; margin-right: 10px;">
                            <i class="fa fa-lock"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Matriks Hak Akses</h6>
                            <p class="text-muted mb-0" style="font-size: 0.78rem;">Centang izin modul yang dapat diakses oleh peran ini.</p>
                        </div>
                    </div>
                    <button type="button" class="btn btn-oripio-light btn-pill mt-2 mt-sm-0" id="toggleAllBtn" onclick="toggleAllPermissions()">
                        <i class="fa fa-square-check me-1"></i> Pilih Semua
                    </button>
                </div>

                <div class="perm-grid">
                    @forelse ($permissions as $group => $perms)
                        <div class="perm-grid-item">
                            <div class="card perm-card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 0.88rem;">{{ $group }}</h6>
                                    <button type="button" class="btn btn-sm btn-oripio-light btn-pill" onclick="toggleGroup('{{ Str::slug($group) }}')" style="font-size: 0.72rem; padding: 4px 10px;">
                                        Pilih Modul
                                    </button>
                                </div>
                                <div class="card-body">
                                    @foreach ($perms as $p)
                                        @php
                                            $checked = in_array($p->id, old('permissions', $assignedPermissionIds));
                                        @endphp
                                        <div class="form-check">
                                            <input class="form-check-input perm-check perm-group-{{ Str::slug($group) }}" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="perm_{{ $p->id }}" {{ $checked ? 'checked' : '' }}>
                                            <label class="form-check-label" for="perm_{{ $p->id }}">
                                                <strong>{{ $p->display_name }}</strong>
                                                <code class="text-muted fs-12 ms-1">({{ $p->name }})</code>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="perm-grid-item" style="column-span: all;">
                            <p class="text-muted mb-0">Belum ada daftar permission master yang terdaftar.</p>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-end align-items-center mt-4 pt-3" style="border-top: 1px solid var(--oripio-border-subtle);">
                    <a href="{{ route('superadmin.roles.index') }}" class="btn btn-oripio-light btn-pill me-2">Batal</a>
                    <button type="submit" class="btn btn-oripio-primary btn-pill">
                        <i class="fa fa-save me-1"></i> Simpan Perubahan Hak Akses
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let allChecked = false;

    function toggleAllPermissions() {
        allChecked = !allChecked;
        document.querySelectorAll('.perm-check').forEach(cb => cb.checked = allChecked);
        document.getElementById('toggleAllBtn').innerHTML = allChecked
            ? '<i class="fa fa-square me-1"></i> Batal Pilih Semua'
            : '<i class="fa fa-square-check me-1"></i> Pilih Semua';
    }

    function toggleGroup(groupSlug) {
        const checkboxes = document.querySelectorAll('.perm-group-' + groupSlug);
        const anyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
        checkboxes.forEach(cb => cb.checked = anyUnchecked);
    }
</script>
@endsection
