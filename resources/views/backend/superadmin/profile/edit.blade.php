@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Profil Saya',
        'subtitle' => 'Kelola data pribadi dan keamanan akun Superadmin Anda.',
        'icon' => 'fa-user',
    ])

    @include('backend.superadmin.components.alerts')

    <div class="row g-4">
        {{-- KARTU 1: DATA PROFIL --}}
        <div class="col-xl-7">
            <div class="card p-2 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #DCFCE7; border-radius: 14px; color: #006039;">
                            <i class="fa fa-id-card" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Data Profil</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Hanya nama, email, dan nomor telepon yang dapat diubah.</p>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="{{ old('nama', $user->nama) }}" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-7">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Nomor Telepon</label>
                                    <input type="text" name="notelp" class="form-control" value="{{ old('notelp', $user->notelp) }}" required>
                                </div>
                            </div>
                        </div>

                        {{-- Informasi saja: role & status TIDAK dapat diubah dari halaman ini --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Role</label>
                                    <input type="text" class="form-control" value="{{ $user->role_display_name }}" disabled>
                                    <small class="text-muted">Role tidak dapat diubah dari halaman profil.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Status Akun</label>
                                    <input type="text" class="form-control" value="{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}" disabled>
                                    <small class="text-muted">Status akun hanya dapat diubah dari Kelola Akun.</small>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-oripio-dark btn-pill">
                            <i class="fa fa-save me-2"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- KARTU 2: GANTI PASSWORD --}}
        <div class="col-xl-5">
            <div class="card p-2 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center mb-4">
                        <div class="me-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background: #DCFCE7; border-radius: 14px; color: #006039;">
                            <i class="fa fa-key" style="font-size: 1.1rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Ganti Password</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Minimal 8 karakter, kombinasi huruf besar, kecil, dan angka.</p>
                        </div>
                    </div>

                    <form action="{{ route('superadmin.profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label class="fw-bold">Password Saat Ini</label>
                            <input type="password" name="current_password" class="form-control" autocomplete="current-password" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="fw-bold">Password Baru</label>
                            <input type="password" name="password" class="form-control" autocomplete="new-password" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required>
                        </div>

                        <button type="submit" class="btn btn-oripio-dark btn-pill">
                            <i class="fa fa-key me-2"></i> Ubah Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
