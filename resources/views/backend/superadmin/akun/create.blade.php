@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-uppercase fw-bold"><i class="fa fa-user-plus me-2 text-primary"></i> Tambah Akun Baru</h4>
                <p class="mb-0 text-muted">Pembuatan akun staf, admin, atau pengguna aplikasi Sentra</p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.akun.index') }}">Akun</a></li>
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
            <h5 class="card-title mb-0">Formulir Pendaftaran Akun</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.akun.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" required placeholder="Contoh: Budi Santoso">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="Contoh: user@sentra.com">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="notelp" class="form-control" value="{{ old('notelp') }}" required placeholder="Contoh: 081234567890">
                            <small class="text-muted">Nomor telepon akan otomatis dienkripsi secara aman.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6" placeholder="Minimal 6 karakter">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="fw-bold">Peran (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-control" required>
                                <option value="admin_ppa" {{ old('role', 'admin_ppa') === 'admin_ppa' ? 'selected' : '' }}>Admin PPA (Kekerasan Perempuan & Anak)</option>
                                <option value="admin_dinsos" {{ old('role') === 'admin_dinsos' ? 'selected' : '' }}>Admin Dinsos (Sosial & Rujukan)</option>
                                <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin (Akses Penuh Sistem)</option>
                                <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User (Masyarakat / Pelapor)</option>
                                <option value="guest" {{ old('role') === 'guest' ? 'selected' : '' }}>Guest</option>
                                @foreach ($roles as $r)
                                    @if (!in_array($r->name, ['superadmin', 'admin', 'admin_ppa', 'admin_dinsos', 'user', 'guest']))
                                        <option value="{{ $r->name }}" {{ old('role') === $r->name ? 'selected' : '' }}>{{ $r->display_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">-- Pilih (Opsional) --</option>
                                <option value="Pria" {{ old('jenis_kelamin') === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin') === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="fw-bold d-block">Status Akun</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveCheck" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isActiveCheck">
                                    Aktifkan akun segera
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="fw-bold">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat domisili pengguna (opsional)">{{ old('alamat') }}</textarea>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('superadmin.akun.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
