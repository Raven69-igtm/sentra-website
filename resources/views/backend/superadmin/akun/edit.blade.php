@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid">
    <div class="row page-titles mx-0">
        <div class="col-sm-6 p-md-0">
            <div class="welcome-text">
                <h4 class="text-uppercase fw-bold"><i class="fa fa-pencil me-2 text-primary"></i> Edit Akun</h4>
                <p class="mb-0 text-muted">Perbarui informasi profil dan penugasan peran akun <strong>{{ $akun->nama }}</strong></p>
            </div>
        </div>
        <div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('superadmin.akun.index') }}">Akun</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Edit</a></li>
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
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Formulir Perubahan Data Akun</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('superadmin.akun.update', $akun->id_akun) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $akun->nama) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $akun->email) }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="notelp" class="form-control" value="{{ old('notelp', $akun->notelp) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="fw-bold">Ubah Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                            <small class="text-muted">Isi hanya jika ingin mengganti kata sandi secara langsung.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="fw-bold">Peran (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-control" required>
                                <option value="admin_ppa" {{ in_array(old('role', $akun->role), ['admin_ppa', 'admin']) ? 'selected' : '' }}>Admin PPA (Kekerasan Perempuan & Anak)</option>
                                <option value="admin_dinsos" {{ old('role', $akun->role) === 'admin_dinsos' ? 'selected' : '' }}>Admin Dinsos (Sosial & Rujukan)</option>
                                <option value="superadmin" {{ old('role', $akun->role) === 'superadmin' ? 'selected' : '' }}>Superadmin (Akses Penuh Sistem)</option>
                                <option value="user" {{ old('role', $akun->role) === 'user' ? 'selected' : '' }}>User (Masyarakat / Pelapor)</option>
                                <option value="guest" {{ old('role', $akun->role) === 'guest' ? 'selected' : '' }}>Guest</option>
                                @foreach ($roles as $r)
                                    @if (!in_array($r->name, ['superadmin', 'admin', 'admin_ppa', 'admin_dinsos', 'user', 'guest']))
                                        <option value="{{ $r->name }}" {{ old('role', $akun->role) === $r->name ? 'selected' : '' }}>{{ $r->display_name }}</option>
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
                                <option value="Pria" {{ old('jenis_kelamin', $akun->jenis_kelamin) === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $akun->jenis_kelamin) === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="fw-bold d-block">Status Akun</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActiveEdit" value="1" {{ old('is_active', $akun->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="isActiveEdit">
                                    Akun Aktif
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="fw-bold">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $akun->alamat) }}</textarea>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('superadmin.akun.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save me-1"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
