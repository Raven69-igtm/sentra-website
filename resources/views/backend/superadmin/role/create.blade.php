@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Tambah Role Baru',
        'subtitle' => 'Definisikan peran baru beserta batasan izin akses modul.',
        'icon' => 'fa-plus',
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
                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Informasi Peran Baru</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">Isi identitas peran, lalu tentukan hak aksesnya.</p>
                </div>
            </div>

            <form action="{{ route('superadmin.roles.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Nama Slug (Identitas Sistem) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: konselor_ahli">
                            <small class="text-muted">Gunakan huruf kecil, angka, atau garis bawah tanpa spasi.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Nama Tampilan <span class="text-danger">*</span></label>
                            <input type="text" name="display_name" class="form-control" value="{{ old('display_name') }}" required placeholder="Contoh: Konselor Ahli">
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3 mb-0">
                    <label class="fw-bold">Deskripsi Peran</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Jelaskan tanggung jawab atau wewenang peran ini">{{ old('description') }}</textarea>
                </div>

                <hr class="my-4" style="border-color: var(--oripio-border-subtle);">

                <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center">
                        <div class="page-head-icon" style="width: 36px; height: 36px; flex-basis: 36px; font-size: 0.9rem; margin-right: 10px;">
                            <i class="fa fa-lock"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Matriks Hak Akses</h6>
                            <p class="text-muted mb-0" style="font-size: 0.78rem;">Pilih izin modul yang dapat diakses oleh peran ini.</p>
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
                                        <div class="form-check">
                                            <input class="form-check-input perm-check perm-group-{{ Str::slug($group) }}" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="perm_{{ $p->id }}" {{ is_array(old('permissions')) && in_array($p->id, old('permissions')) ? 'checked' : '' }}>
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
                    <a href="{{ route('superadmin.roles.index') }}" wire:navigate class="btn btn-oripio-light btn-pill me-2">Batal</a>
                    <button type="submit" class="btn btn-oripio-primary btn-pill">
                        <i class="fa fa-save me-1"></i> Simpan Role Baru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.__permAllChecked = false;

    window.toggleAllPermissions = function () {
        window.__permAllChecked = !window.__permAllChecked;
        document.querySelectorAll('.perm-check').forEach(function (cb) {
            cb.checked = window.__permAllChecked;
        });
        var btn = document.getElementById('toggleAllBtn');
        if (btn) {
            btn.innerHTML = window.__permAllChecked
                ? '<i class="fa fa-square me-1"></i> Batal Pilih Semua'
                : '<i class="fa fa-square-check me-1"></i> Pilih Semua';
        }
    };

    window.toggleGroup = function (groupSlug) {
        var checkboxes = document.querySelectorAll('.perm-group-' + groupSlug);
        var anyUnchecked = Array.prototype.some.call(checkboxes, function (cb) {
            return !cb.checked;
        });
        checkboxes.forEach(function (cb) {
            cb.checked = anyUnchecked;
        });
    };
</script>
@endsection
