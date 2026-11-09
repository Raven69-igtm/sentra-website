@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Konfigurasi Sistem',
        'subtitle' => 'Pengaturan parameter operasional, identitas aplikasi, kontak darurat, dan kebijakan keamanan.',
        'icon' => 'fa-cogs',
    ])

    @include('backend.superadmin.components.alerts')

    <div class="card p-2">
        <div class="card-header" style="padding: 8px 8px 0 !important;">
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs" id="settingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">
                        <i class="fa fa-info-circle me-1"></i> Identitas & Umum
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab">
                        <i class="fa fa-phone me-1"></i> Kontak & Hotline
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="report-tab" data-toggle="tab" href="#report" role="tab">
                        <i class="fa fa-file-text me-1"></i> Alur Laporan & SLA
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="security-tab" data-toggle="tab" href="#security" role="tab">
                        <i class="fa fa-shield me-1"></i> Keamanan & Sesi
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body p-3">
            <form action="{{ route('superadmin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="tab-content" id="settingTabsContent">
                    <!-- TAB 1: IDENTITAS & UMUM -->
                    <div class="tab-pane fade show active" id="general" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Identitas Aplikasi & Lembaga</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Informasi dasar yang tampil pada aplikasi dan halaman publik.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Nama Aplikasi</label>
                                    <input type="text" name="app_name" class="form-control" value="{{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}" required>
                                    <small class="text-muted">Nama brand aplikasi yang tampil pada navbar dan judul web.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Nama Instansi / Lembaga</label>
                                    <input type="text" name="instansi_name" class="form-control" value="{{ \App\Models\SystemSetting::get('instansi_name', 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="fw-bold">Deskripsi Ringkas Layanan</label>
                            <textarea name="app_description" class="form-control" rows="3">{{ \App\Models\SystemSetting::get('app_description', 'Sistem Informasi dan Pelayanan Terpadu Penanganan Kekerasan terhadap Perempuan dan Anak') }}</textarea>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Hak Cipta Footer (Copyright)</label>
                                    <input type="text" name="footer_copyright" class="form-control" value="{{ \App\Models\SystemSetting::get('footer_copyright', '© 2026 SENTRA. All rights reserved.') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Logo Aplikasi</label>
                                    <input type="file" name="app_logo" class="form-control" accept="image/*">
                                    @if ($logo = \App\Models\SystemSetting::get('app_logo'))
                                        <div class="mt-2 d-flex align-items-center">
                                            <img src="{{ asset($logo) }}" alt="Logo" class="img-thumbnail me-2" style="max-height: 44px; border-radius: 10px;">
                                            <small class="text-muted">Logo aktif saat ini</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: KONTAK & HOTLINE -->
                    <div class="tab-pane fade" id="contact" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Kontak Layanan Darurat & Pengaduan</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Nomor dan kanal kontak yang digunakan pelapor untuk menghubungi layanan.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Nomor Hotline Pengaduan (WhatsApp)</label>
                                    <input type="text" name="hotline_number" class="form-control" value="{{ \App\Models\SystemSetting::get('hotline_number', '0812-3456-7890') }}" required>
                                    <small class="text-muted">Nomor WhatsApp rujukan untuk pelapor darurat.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Panggilan Darurat Cepat (Call Center)</label>
                                    <input type="text" name="emergency_call" class="form-control" value="{{ \App\Models\SystemSetting::get('emergency_call', '112 / 129') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Email Layanan</label>
                                    <input type="email" name="office_email" class="form-control" value="{{ \App\Models\SystemSetting::get('office_email', 'sentraapplication@gmail.com') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Alamat Kantor Pelayanan</label>
                                    <input type="text" name="office_address" class="form-control" value="{{ \App\Models\SystemSetting::get('office_address', 'Jl. Pemuda No. 45, Kompleks Perkantoran Pemerintah') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: ALUR LAPORAN & SLA -->
                    <div class="tab-pane fade" id="report" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Standar Pelayanan & Manajemen Laporan</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Atur target respon, retensi arsip, dan ketersediaan layanan konsultasi.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Target SLA Respon Laporan (Hari)</label>
                                    <input type="number" name="report_sla_days" class="form-control" min="1" max="30" value="{{ \App\Models\SystemSetting::get('report_sla_days', '3') }}" required>
                                    <small class="text-muted">Maksimal batas waktu awal penanganan laporan baru.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Periode Retensi Arsip (Bulan)</label>
                                    <input type="number" name="auto_archive_months" class="form-control" min="1" max="120" value="{{ \App\Models\SystemSetting::get('auto_archive_months', '24') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Layanan Konsultasi Chat</label>
                                    <select name="enable_chat_consultation" class="form-control">
                                        <option value="1" {{ \App\Models\SystemSetting::get('enable_chat_consultation', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ \App\Models\SystemSetting::get('enable_chat_consultation', '1') == '0' ? 'selected' : '' }}>Nonaktif Sementara</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: KEAMANAN & SESI -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Kebijakan Sesi & Akses Autentikasi</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Pengaturan keamanan sesi login, pembatasan percobaan, dan masa berlaku OTP.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Durasi Timeout Sesi (Menit)</label>
                                    <input type="number" name="session_lifetime_minutes" class="form-control" min="15" max="1440" value="{{ \App\Models\SystemSetting::get('session_lifetime_minutes', '120') }}" required>
                                    <small class="text-muted">Masa kedaluwarsa sesi login jika tidak ada aktivitas.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Maksimal Percobaan Login Salah</label>
                                    <input type="number" name="max_login_attempts" class="form-control" min="3" max="20" value="{{ \App\Models\SystemSetting::get('max_login_attempts', '5') }}" required>
                                    <small class="text-muted">Batas throttle brute-force sebelum akun terkunci sementara.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Masa Berlaku OTP (Menit)</label>
                                    <input type="number" name="otp_expiry_minutes" class="form-control" min="1" max="60" value="{{ \App\Models\SystemSetting::get('otp_expiry_minutes', '5') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center mt-4 pt-3" style="border-top: 1px solid var(--oripio-border-subtle);">
                    <button type="submit" class="btn btn-oripio-primary btn-pill">
                        <i class="fa fa-save me-1"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
