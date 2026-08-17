@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Edit Akun',
        'subtitle' => 'Perbarui informasi profil dan penugasan peran akun ' . $akun->nama . '.',
        'icon' => 'fa-pencil',
        'actionUrl' => route('superadmin.akun.index'),
        'actionLabel' => 'Kembali ke Daftar',
        'actionIcon' => 'fa-arrow-left',
        'actionClass' => 'btn-oripio-light',
    ])

    @include('backend.superadmin.components.alerts')

    <div class="card p-2">
        <div class="card-body p-3">
            <div class="d-flex align-items-center mb-4">
                <div class="page-head-icon" style="width: 40px; height: 40px; flex-basis: 40px; font-size: 0.95rem;">
                    <i class="fa fa-id-card"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Formulir Perubahan Data Akun</h5>
                    <p class="text-muted mb-0" style="font-size: 0.82rem;">Pastikan data yang diperbarui sudah sesuai.</p>
                </div>
            </div>

            <form action="{{ route('superadmin.akun.update', $akun->id_akun) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $akun->nama) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $akun->email) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" name="notelp" class="form-control" value="{{ old('notelp', $akun->notelp) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Ubah Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                            <small class="text-muted">Isi hanya jika ingin mengganti kata sandi secara langsung.</small>
                        </div>
                    </div>
                </div>

                <hr class="my-4" style="border-color: var(--oripio-border-subtle);">

                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Peran (Role) <span class="text-danger">*</span></label>
                            <select name="role" class="form-control" required>
                                <option value="admin_ppa" {{ in_array(old('role', $akun->role), ['admin_ppa', 'admin']) ? 'selected' : '' }}>Admin PPA (Kekerasan Perempuan & Anak)</option>
                                <option value="admin_dinsos" {{ old('role', $akun->role) === 'admin_dinsos' ? 'selected' : '' }}>Admin Dinsos (Sosial & Rujukan)</option>
                                <option value="superadmin" {{ old('role', $akun->role) === 'superadmin' ? 'selected' : '' }}>Superadmin (Akses Penuh Sistem)</option>
                                <option value="user" {{ old('role', $akun->role) === 'user' ? 'selected' : '' }}>User (Masyarakat / Pelapor)</option>
                                <option value="Pelapor Cepat" {{ old('role', $akun->role) === 'Pelapor Cepat' ? 'selected' : '' }}>Pelapor Cepat</option>
                                @foreach ($roles as $r)
                                    @if (!in_array($r->name, ['superadmin', 'admin', 'admin_ppa', 'admin_dinsos', 'user', 'guest']))
                                        <option value="{{ $r->name }}" {{ old('role', $akun->role) === $r->name ? 'selected' : '' }}>{{ $r->display_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control">
                                <option value="">-- Pilih (Opsional) --</option>
                                <option value="Pria" {{ old('jenis_kelamin', $akun->jenis_kelamin) === 'Pria' ? 'selected' : '' }}>Pria</option>
                                <option value="Wanita" {{ old('jenis_kelamin', $akun->jenis_kelamin) === 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label class="fw-bold d-block">Status Akun</label>
                            <div class="d-flex align-items-center" style="min-height: 44px;">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActiveEdit" value="1" {{ old('is_active', $akun->is_active ? '1' : '0') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="isActiveEdit">
                                        Akun Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3 mb-0">
                    <label class="fw-bold">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3">{{ old('alamat', $akun->alamat) }}</textarea>
                </div>

                <div class="d-flex justify-content-end align-items-center mt-4 pt-3" style="border-top: 1px solid var(--oripio-border-subtle);">
                    <a href="{{ route('superadmin.akun.index') }}" wire:navigate class="btn btn-oripio-light btn-pill me-2">Batal</a>
                    <button type="submit" class="btn btn-oripio-primary btn-pill">
                        <i class="fa fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
