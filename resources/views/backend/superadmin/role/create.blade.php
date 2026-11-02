@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-uppercase fw-bold"><i class="fa fa-plus me-2 text-primary"></i> Tambah Role Baru</h4>
                <p class="mb-0 text-muted">Definisikan peran baru beserta batasan izin akses modul</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.roles.index') }}">Role</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Tambah</a></li>
            </ol>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Terjadi Kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Informasi Peran Baru</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.roles.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Nama Slug (Identitas Sistem) <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: konselor_ahli (hanya huruf, angka, garis bawah)">
                            <small class="text-muted">Gunakan huruf kecil tanpa spasi.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Nama Tampilan <span class="text-danger">*</span></label>
                            <input type="text" name="display_name" class="form-control" value="{{ old('display_name') }}" required placeholder="Contoh: Konselor Ahli">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="fw-bold">Deskripsi Peran</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Jelaskan tanggung jawab atau wewenang peran ini">{{ old('description') }}</textarea>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><i class="fa fa-lock text-warning me-2"></i> Matriks Hak Akses (Permissions)</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="toggleAllBtn" onclick="toggleAllPermissions()">Pilih Semua</button>
                </div>

                <div class="row">
                    @forelse ($permissions as $group => $perms)
                        <div class="col-lg-6 col-md-12 mb-4">
                            <div class="card border">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $group }}</h6>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" onclick="toggleGroup('{{ Str::slug($group) }}')">Pilih Modul</button>
                                </div>
                                <div class="card-body py-3">
                                    @foreach ($perms as $p)
                                        <div class="form-check mb-2">
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
                        <div class="col-12">
                            <p class="text-muted">Belum ada daftar permission master yang terdaftar.</p>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('superadmin.roles.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Simpan Role Baru</button>
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
        document.getElementById('toggleAllBtn').innerText = allChecked ? 'Batal Pilih Semua' : 'Pilih Semua';
    }

    function toggleGroup(groupSlug) {
        const checkboxes = document.querySelectorAll('.perm-group-' + groupSlug);
        const anyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
        checkboxes.forEach(cb => cb.checked = anyUnchecked);
    }
</script>
@endsection
