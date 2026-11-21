@extends('backend.superadmin.layout.superadmin_layout')

@section('admin')
<div class="container-fluid px-4">
    @include('backend.superadmin.components.page-header', [
        'title' => 'Konfigurasi Sistem',
        'subtitle' => 'Pengaturan parameter operasional, identitas aplikasi, konten halaman utama, kontak darurat, dan kebijakan keamanan.',
        'icon' => 'fa-cogs',
    ])

    @include('backend.superadmin.components.alerts')

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong class="d-block mb-1">Periksa kembali isian berikut:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        // Nilai field: prioritaskan input lama (saat validasi gagal),
        // lalu nilai tersimpan, lalu default bawaan.
        $val = fn ($key) => old($key, setting($key));

        $featureRows = old('mobile_features', setting_json('mobile_features'));
        $faqRows = old('faq_items', setting_json('faq_items'));
        $footerLinkRows = old('footer_links', setting_json('footer_links'));
        $footerServiceRows = old('footer_services', setting_json('footer_services'));
        $screenshotRows = old('mobile_screenshots', setting_json('mobile_screenshots'));

        $isUploaded = fn ($key) => \Illuminate\Support\Str::startsWith(
            (string) \App\Models\SystemSetting::get($key),
            'uploads/settings/'
        );
    @endphp

    <div class="card p-2">
        <div class="card-header" style="padding: 8px 8px 0 !important;">
            <!-- Nav Tabs -->
            <ul class="nav nav-tabs flex-wrap" id="settingTabs" role="tablist">
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
                    <a class="nav-link" id="hero-tab" data-toggle="tab" href="#hero" role="tab">
                        <i class="fa fa-image me-1"></i> Beranda: Hero & Tentang
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="aplikasi-tab" data-toggle="tab" href="#aplikasi" role="tab">
                        <i class="fa fa-mobile-alt me-1"></i> Beranda: Aplikasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="faq-tab" data-toggle="tab" href="#faq" role="tab">
                        <i class="fa fa-question-circle me-1"></i> Beranda: FAQ & Informasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="beranda-tab" data-toggle="tab" href="#beranda" role="tab">
                        <i class="fa fa-map-marker-alt me-1"></i> Beranda: Kontak & Navigasi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="footer-tab" data-toggle="tab" href="#footer" role="tab">
                        <i class="fa fa-shoe-prints me-1"></i> Beranda: Footer & Sosmed
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
                                    <input type="text" name="app_name" class="form-control" value="{{ $val('app_name') }}" required>
                                    <small class="text-muted">Nama brand aplikasi yang tampil pada navbar dan judul web.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Nama Instansi / Lembaga</label>
                                    <input type="text" name="instansi_name" class="form-control" value="{{ $val('instansi_name') }}" required>
                                    <small class="text-muted">Tampil pada hero beranda, subjudul kontak, dan judul web.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="fw-bold">Deskripsi Ringkas Layanan</label>
                            <textarea name="app_description" class="form-control" rows="3">{{ $val('app_description') }}</textarea>
                            <small class="text-muted">Tampil pada hero beranda dan sebagai teks tentang kami di footer.</small>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Hak Cipta Footer (Copyright)</label>
                                    <input type="text" name="footer_copyright" class="form-control" value="{{ $val('footer_copyright') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Logo Aplikasi</label>
                                    <input type="file" name="app_logo" class="form-control" accept="image/*">
                                    @if ($logo = setting('app_logo'))
                                        <div class="mt-2 d-flex align-items-center">
                                            <img src="{{ asset($logo) }}" alt="Logo" class="img-thumbnail me-2" style="max-height: 44px; border-radius: 10px;">
                                            <small class="text-muted">Logo aktif saat ini</small>
                                        </div>
                                    @endif
                                    @if ($isUploaded('app_logo'))
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="remove_upload[app_logo]" value="1" id="removeAppLogo">
                                            <label class="form-check-label small text-danger" for="removeAppLogo">Hapus logo unggahan (kembali ke logo bawaan)</label>
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
                                    <input type="text" name="hotline_number" class="form-control" value="{{ $val('hotline_number') }}" required>
                                    <small class="text-muted">Nomor WhatsApp rujukan untuk pelapor darurat.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Panggilan Darurat Cepat (Call Center)</label>
                                    <input type="text" name="emergency_call" class="form-control" value="{{ $val('emergency_call') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Email Layanan</label>
                                    <input type="email" name="office_email" class="form-control" value="{{ $val('office_email') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Alamat Kantor Pelayanan</label>
                                    <input type="text" name="office_address" class="form-control" value="{{ $val('office_address') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: BERANDA — HERO & TENTANG -->
                    <div class="tab-pane fade" id="hero" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Bagian Hero & Tentang Kami</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Konten dua bagian pertama halaman utama. Judul hero memakai Nama Instansi dan deskripsi memakai Deskripsi Ringkas Layanan.</p>
                        </div>

                        <div class="form-group mb-0">
                            <label class="fw-bold">Gambar Latar Hero</label>
                            <input type="file" name="hero_background" class="form-control" accept="image/*">
                            @if ($heroBackground = setting('hero_background'))
                                <div class="mt-2 d-flex align-items-center">
                                    <img src="{{ asset($heroBackground) }}" alt="Background Hero" class="img-thumbnail me-2" style="max-height: 70px; border-radius: 10px;">
                                    <small class="text-muted">Gambar aktif saat ini</small>
                                </div>
                            @endif
                            @if ($isUploaded('hero_background'))
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_upload[hero_background]" value="1" id="removeHeroBg">
                                    <label class="form-check-label small text-danger" for="removeHeroBg">Hapus gambar unggahan (kembali ke gambar bawaan)</label>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Section Tentang Kami</label>
                                    <input type="text" name="about_title" class="form-control" value="{{ $val('about_title') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Subjudul Section Tentang Kami</label>
                                    <input type="text" name="about_subtitle" class="form-control" value="{{ $val('about_subtitle') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="fw-bold">Gambar Section Tentang Kami</label>
                            <input type="file" name="about_image" class="form-control" accept="image/*">
                            @if ($aboutImage = setting('about_image'))
                                <div class="mt-2 d-flex align-items-center">
                                    <img src="{{ asset($aboutImage) }}" alt="Gambar Tentang Kami" class="img-thumbnail me-2" style="max-height: 70px; border-radius: 10px;">
                                    <small class="text-muted">Gambar aktif saat ini</small>
                                </div>
                            @endif
                            @if ($isUploaded('about_image'))
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_upload[about_image]" value="1" id="removeAboutImage">
                                    <label class="form-check-label small text-danger" for="removeAboutImage">Hapus gambar unggahan (kembali ke gambar bawaan)</label>
                                </div>
                            @endif
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="fw-bold">Paragraf Pembuka</label>
                            <textarea name="about_paragraph_1" class="form-control" rows="3">{{ $val('about_paragraph_1') }}</textarea>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Poin #1</label>
                                    <input type="text" name="about_bullet_1" class="form-control" value="{{ $val('about_bullet_1') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Poin #2</label>
                                    <input type="text" name="about_bullet_2" class="form-control" value="{{ $val('about_bullet_2') }}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Poin #3</label>
                                    <input type="text" name="about_bullet_3" class="form-control" value="{{ $val('about_bullet_3') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="fw-bold">Paragraf Penutup</label>
                            <textarea name="about_paragraph_2" class="form-control" rows="3">{{ $val('about_paragraph_2') }}</textarea>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Salam Penutup</label>
                                    <input type="text" name="about_closing" class="form-control" value="{{ $val('about_closing') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Tanda Tangan / Nama Tim</label>
                                    <input type="text" name="about_signature" class="form-control" value="{{ $val('about_signature') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: BERANDA — APLIKASI -->
                    <div class="tab-pane fade" id="aplikasi" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Bagian Aplikasi Mobile</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Carousel tangkapan layar, daftar fitur, dan tautan unduhan aplikasi.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Section</label>
                                    <input type="text" name="mobile_title" class="form-control" value="{{ $val('mobile_title') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Subjudul Section</label>
                                    <input type="text" name="mobile_subtitle" class="form-control" value="{{ $val('mobile_subtitle') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Nama Aplikasi (judul besar)</label>
                                    <input type="text" name="mobile_app_name" class="form-control" value="{{ $val('mobile_app_name') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Teks Status Unduhan</label>
                                    <input type="text" name="mobile_download_text" class="form-control" value="{{ $val('mobile_download_text') }}">
                                    <small class="text-muted">Contoh: Cooming Soon! atau Unduh Sekarang.</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="fw-bold">Deskripsi Aplikasi</label>
                            <textarea name="mobile_description" class="form-control" rows="3">{{ $val('mobile_description') }}</textarea>
                            <small class="text-muted">Tanda kutip di awal dan akhir ditambahkan otomatis oleh template.</small>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">URL Play Store</label>
                                    <input type="text" name="playstore_url" class="form-control" value="{{ $val('playstore_url') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Label Tombol Unduh</label>
                                    <input type="text" name="playstore_label" class="form-control" value="{{ $val('playstore_label') }}">
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Tangkapan Layar Aplikasi</h6>
                            <small class="text-muted">{{ count($screenshotRows) }} gambar</small>
                        </div>
                        <input type="hidden" name="mobile_screenshots_present" value="1">

                        <div class="row g-3">
                            @foreach ($screenshotRows as $index => $shot)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start border rounded p-2">
                                        <img src="{{ asset($shot['src'] ?? '') }}" alt="" class="me-2" style="height: 72px; width: 48px; object-fit: cover; border-radius: 6px;">
                                        <div class="flex-grow-1">
                                            <input type="hidden" name="mobile_screenshots[{{ $index }}][src]" value="{{ $shot['src'] ?? '' }}">
                                            <input type="text" name="mobile_screenshots[{{ $index }}][alt]" class="form-control form-control-sm" value="{{ $shot['alt'] ?? '' }}" placeholder="Teks alternatif gambar">
                                            <div class="form-check mt-2">
                                                <input class="form-check-input" type="checkbox" name="mobile_screenshots_remove[]" value="{{ $shot['src'] ?? '' }}" id="shotRemove{{ $index }}">
                                                <label class="form-check-label small text-danger" for="shotRemove{{ $index }}">Hapus gambar ini</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="fw-bold">Tambah Tangkapan Layar</label>
                            <input type="file" name="mobile_screenshots_upload[]" class="form-control" accept="image/*" multiple>
                            <small class="text-muted">Bisa pilih beberapa gambar sekaligus. Gambar baru ditambahkan di akhir carousel.</small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">Daftar Fitur Aplikasi</h6>
                        </div>

                        <div data-repeater="mobile_features">
                            <div data-repeater-rows>
                                @foreach ($featureRows as $index => $feature)
                                    <div class="repeater-row card p-2 mb-2" data-repeater-row>
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-3">
                                                <label class="small fw-bold mb-1">Ikon</label>
                                                <input type="text" name="mobile_features[{{ $index }}][icon]" class="form-control form-control-sm" value="{{ $feature['icon'] ?? '' }}">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="small fw-bold mb-1">Judul</label>
                                                <input type="text" name="mobile_features[{{ $index }}][title]" class="form-control form-control-sm" value="{{ $feature['title'] ?? '' }}">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="small fw-bold mb-1">Deskripsi</label>
                                                <input type="text" name="mobile_features[{{ $index }}][desc]" class="form-control form-control-sm" value="{{ $feature['desc'] ?? '' }}">
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-repeater-remove title="Hapus baris">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" data-repeater-add>
                                <i class="fa fa-plus me-1"></i> Tambah Fitur
                            </button>
                            <input type="hidden" name="mobile_features_present" value="1">
                        </div>

                        <template data-repeater-template="mobile_features">
                            <div class="repeater-row card p-2 mb-2" data-repeater-row>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="small fw-bold mb-1">Ikon</label>
                                        <input type="text" name="mobile_features[__INDEX__][icon]" class="form-control form-control-sm" value="bi bi-check-circle-fill">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small fw-bold mb-1">Judul</label>
                                        <input type="text" name="mobile_features[__INDEX__][title]" class="form-control form-control-sm" value="">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="small fw-bold mb-1">Deskripsi</label>
                                        <input type="text" name="mobile_features[__INDEX__][desc]" class="form-control form-control-sm" value="">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-repeater-remove title="Hapus baris">&times;</button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- TAB 5: BERANDA — FAQ & INFORMASI -->
                    <div class="tab-pane fade" id="faq" role="tabpanel">
                        <div class="d-flex flex-wrap justify-content-between align-items-start mb-4">
                            <div>
                                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Bagian Pertanyaan Umum & Informasi Terkini</h5>
                                <p class="text-muted mb-0" style="font-size: 0.82rem;">Daftar pertanyaan umum dapat ditambah, diubah, dan dihapus. Isi berita dikelola pada kartu <strong>Informasi Terkini (Berita)</strong> di bagian bawah halaman ini.</p>
                            </div>
                            <a href="#kelola-berita" class="btn btn-sm btn-outline-success btn-pill mt-2 mt-md-0">
                                <i class="fa fa-newspaper me-1"></i> Kelola Berita
                            </a>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Section FAQ</label>
                                    <input type="text" name="faq_title" class="form-control" value="{{ $val('faq_title') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Subjudul Section FAQ</label>
                                    <input type="text" name="faq_subtitle" class="form-control" value="{{ $val('faq_subtitle') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                            <h6 class="fw-bold mb-0">Daftar Pertanyaan Umum</h6>
                        </div>

                        <div data-repeater="faq_items">
                            <div data-repeater-rows>
                                @foreach ($faqRows as $index => $faq)
                                    <div class="repeater-row card p-2 mb-2" data-repeater-row>
                                        <div class="row g-2">
                                            <div class="col-md-11">
                                                <label class="small fw-bold mb-1">Pertanyaan</label>
                                                <input type="text" name="faq_items[{{ $index }}][question]" class="form-control form-control-sm" value="{{ $faq['question'] ?? '' }}">
                                                <label class="small fw-bold mb-1 mt-2">Jawaban</label>
                                                <textarea name="faq_items[{{ $index }}][answer]" class="form-control form-control-sm" rows="2">{{ $faq['answer'] ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-repeater-remove title="Hapus baris">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" data-repeater-add>
                                <i class="fa fa-plus me-1"></i> Tambah FAQ
                            </button>
                            <input type="hidden" name="faq_items_present" value="1">
                        </div>

                        <template data-repeater-template="faq_items">
                            <div class="repeater-row card p-2 mb-2" data-repeater-row>
                                <div class="row g-2">
                                    <div class="col-md-11">
                                        <label class="small fw-bold mb-1">Pertanyaan</label>
                                        <input type="text" name="faq_items[__INDEX__][question]" class="form-control form-control-sm" value="">
                                        <label class="small fw-bold mb-1 mt-2">Jawaban</label>
                                        <textarea name="faq_items[__INDEX__][answer]" class="form-control form-control-sm" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-repeater-remove title="Hapus baris">&times;</button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <hr>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Section Informasi</label>
                                    <input type="text" name="information_title" class="form-control" value="{{ $val('information_title') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Subjudul Section Informasi</label>
                                    <input type="text" name="information_subtitle" class="form-control" value="{{ $val('information_subtitle') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Saat Belum Ada Informasi</label>
                                    <input type="text" name="information_empty_title" class="form-control" value="{{ $val('information_empty_title') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Teks Saat Belum Ada Informasi</label>
                                    <input type="text" name="information_empty_text" class="form-control" value="{{ $val('information_empty_text') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 6: BERANDA — KONTAK & NAVIGASI -->
                    <div class="tab-pane fade" id="beranda" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Bagian Kontak, Jam Buka, Peta & Menu</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Alamat, nomor hotline, dan email diambil dari tab Kontak & Hotline. Tab ini mengatur teks pendukung dan menu navigasi.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Section Kontak</label>
                                    <input type="text" name="contact_title" class="form-control" value="{{ $val('contact_title') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Awalan Subjudul Kontak</label>
                                    <input type="text" name="contact_subtitle_prefix" class="form-control" value="{{ $val('contact_subtitle_prefix') }}">
                                    <small class="text-muted">Nama instansi ditambahkan otomatis setelah teks ini.</small>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Label Alamat</label>
                                    <input type="text" name="contact_label_address" class="form-control" value="{{ $val('contact_label_address') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Label Hotline</label>
                                    <input type="text" name="contact_label_hotline" class="form-control" value="{{ $val('contact_label_hotline') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Label Email</label>
                                    <input type="text" name="contact_label_email" class="form-control" value="{{ $val('contact_label_email') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Label Jam Buka</label>
                                    <input type="text" name="contact_label_hours" class="form-control" value="{{ $val('contact_label_hours') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Hari Operasional</label>
                                    <input type="text" name="office_hours_day" class="form-control" value="{{ $val('office_hours_day') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Jam Operasional</label>
                                    <input type="text" name="office_hours_time" class="form-control" value="{{ $val('office_hours_time') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-3 mb-0">
                            <label class="fw-bold">URL Embed Google Maps</label>
                            <textarea name="maps_embed_url" class="form-control" rows="3">{{ $val('maps_embed_url') }}</textarea>
                            <small class="text-muted">Google Maps &rarr; Bagikan &rarr; Sematkan peta &rarr; salin nilai src pada iframe.</small>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-2">Label Menu Navigasi</h6>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Beranda</label>
                                    <input type="text" name="nav_label_home" class="form-control" value="{{ $val('nav_label_home') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Tentang Kami</label>
                                    <input type="text" name="nav_label_about" class="form-control" value="{{ $val('nav_label_about') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Aplikasi</label>
                                    <input type="text" name="nav_label_app" class="form-control" value="{{ $val('nav_label_app') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Pertanyaan Umum</label>
                                    <input type="text" name="nav_label_faq" class="form-control" value="{{ $val('nav_label_faq') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Informasi</label>
                                    <input type="text" name="nav_label_info" class="form-control" value="{{ $val('nav_label_info') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Kontak</label>
                                    <input type="text" name="nav_label_contact" class="form-control" value="{{ $val('nav_label_contact') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Tombol Masuk Admin</label>
                                    <input type="text" name="nav_label_admin" class="form-control" value="{{ $val('nav_label_admin') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 7: BERANDA — FOOTER & SOSMED -->
                    <div class="tab-pane fade" id="footer" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Footer & Media Sosial</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Teks tentang, tautan, dan akun media sosial pada bagian bawah halaman utama.</p>
                        </div>

                        <div class="form-group mb-0">
                            <label class="fw-bold">Teks Tentang di Footer</label>
                            <textarea name="footer_about_text" class="form-control" rows="3">{{ $val('footer_about_text') }}</textarea>
                            <small class="text-muted">Kosongkan untuk memakai Deskripsi Ringkas Layanan.</small>
                        </div>

                        <div class="row g-3 mt-0">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Kolom Tautan</label>
                                    <input type="text" name="footer_links_title" class="form-control" value="{{ $val('footer_links_title') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Kolom Layanan</label>
                                    <input type="text" name="footer_services_title" class="form-control" value="{{ $val('footer_services_title') }}">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                            <h6 class="fw-bold mb-0">Tautan Navigasi Footer</h6>
                        </div>
                        <div data-repeater="footer_links">
                            <div data-repeater-rows>
                                @foreach ($footerLinkRows as $index => $link)
                                    <div class="repeater-row card p-2 mb-2" data-repeater-row>
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-5">
                                                <label class="small fw-bold mb-1">Label</label>
                                                <input type="text" name="footer_links[{{ $index }}][label]" class="form-control form-control-sm" value="{{ $link['label'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small fw-bold mb-1">Tujuan (contoh: #hero)</label>
                                                <input type="text" name="footer_links[{{ $index }}][href]" class="form-control form-control-sm" value="{{ $link['href'] ?? '' }}">
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-repeater-remove title="Hapus baris">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" data-repeater-add>
                                <i class="fa fa-plus me-1"></i> Tambah Tautan
                            </button>
                            <input type="hidden" name="footer_links_present" value="1">
                        </div>
                        <template data-repeater-template="footer_links">
                            <div class="repeater-row card p-2 mb-2" data-repeater-row>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-5">
                                        <label class="small fw-bold mb-1">Label</label>
                                        <input type="text" name="footer_links[__INDEX__][label]" class="form-control form-control-sm" value="">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold mb-1">Tujuan (contoh: #hero)</label>
                                        <input type="text" name="footer_links[__INDEX__][href]" class="form-control form-control-sm" value="">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-repeater-remove title="Hapus baris">&times;</button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <div class="d-flex justify-content-between align-items-center mt-4 mb-2">
                            <h6 class="fw-bold mb-0">Tautan Layanan Footer</h6>
                        </div>
                        <div data-repeater="footer_services">
                            <div data-repeater-rows>
                                @foreach ($footerServiceRows as $index => $service)
                                    <div class="repeater-row card p-2 mb-2" data-repeater-row>
                                        <div class="row g-2 align-items-end">
                                            <div class="col-md-5">
                                                <label class="small fw-bold mb-1">Label</label>
                                                <input type="text" name="footer_services[{{ $index }}][label]" class="form-control form-control-sm" value="{{ $service['label'] ?? '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="small fw-bold mb-1">Tujuan (contoh: /layanan/anak)</label>
                                                <input type="text" name="footer_services[{{ $index }}][href]" class="form-control form-control-sm" value="{{ $service['href'] ?? '' }}">
                                            </div>
                                            <div class="col-md-1 text-end">
                                                <button type="button" class="btn btn-sm btn-outline-danger" data-repeater-remove title="Hapus baris">&times;</button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success" data-repeater-add>
                                <i class="fa fa-plus me-1"></i> Tambah Layanan
                            </button>
                            <input type="hidden" name="footer_services_present" value="1">
                        </div>
                        <template data-repeater-template="footer_services">
                            <div class="repeater-row card p-2 mb-2" data-repeater-row>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-5">
                                        <label class="small fw-bold mb-1">Label</label>
                                        <input type="text" name="footer_services[__INDEX__][label]" class="form-control form-control-sm" value="">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="small fw-bold mb-1">Tujuan (contoh: /layanan/anak)</label>
                                        <input type="text" name="footer_services[__INDEX__][href]" class="form-control form-control-sm" value="">
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-repeater-remove title="Hapus baris">&times;</button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <hr>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Judul Kolom Media Sosial</label>
                                    <input type="text" name="footer_social_title" class="form-control" value="{{ $val('footer_social_title') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Teks Kolom Media Sosial</label>
                                    <input type="text" name="footer_social_text" class="form-control" value="{{ $val('footer_social_text') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">URL Instagram</label>
                                    <input type="text" name="instagram_url" class="form-control" value="{{ $val('instagram_url') }}">
                                    <small class="text-muted">Kosongkan bila ikon tidak ingin ditampilkan.</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">URL TikTok</label>
                                    <input type="text" name="tiktok_url" class="form-control" value="{{ $val('tiktok_url') }}">
                                    <small class="text-muted">Kosongkan bila ikon tidak ingin ditampilkan.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 8: ALUR LAPORAN & SLA -->
                    <div class="tab-pane fade" id="report" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Standar Pelayanan & Manajemen Laporan</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Atur target respon, retensi arsip, dan ketersediaan layanan konsultasi.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Target SLA Respon Laporan (Hari)</label>
                                    <input type="number" name="report_sla_days" class="form-control" min="1" max="30" value="{{ $val('report_sla_days') }}" required>
                                    <small class="text-muted">Maksimal batas waktu awal penanganan laporan baru.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Periode Retensi Arsip (Bulan)</label>
                                    <input type="number" name="auto_archive_months" class="form-control" min="1" max="120" value="{{ $val('auto_archive_months') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Layanan Konsultasi Chat</label>
                                    <select name="enable_chat_consultation" class="form-control">
                                        <option value="1" {{ $val('enable_chat_consultation') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $val('enable_chat_consultation') == '0' ? 'selected' : '' }}>Nonaktif Sementara</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 9: KEAMANAN & SESI -->
                    <div class="tab-pane fade" id="security" role="tabpanel">
                        <div class="mb-4">
                            <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">Kebijakan Sesi & Akses Autentikasi</h5>
                            <p class="text-muted mb-0" style="font-size: 0.82rem;">Pengaturan keamanan sesi login, pembatasan percobaan, dan masa berlaku OTP.</p>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Durasi Timeout Sesi (Menit)</label>
                                    <input type="number" name="session_lifetime_minutes" class="form-control" min="15" max="1440" value="{{ $val('session_lifetime_minutes') }}" required>
                                    <small class="text-muted">Masa kedaluwarsa sesi login jika tidak ada aktivitas.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Maksimal Percobaan Login Salah</label>
                                    <input type="number" name="max_login_attempts" class="form-control" min="3" max="20" value="{{ $val('max_login_attempts') }}" required>
                                    <small class="text-muted">Batas throttle brute-force sebelum akun terkunci sementara.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="fw-bold">Masa Berlaku OTP (Menit)</label>
                                    <input type="number" name="otp_expiry_minutes" class="form-control" min="1" max="60" value="{{ $val('otp_expiry_minutes') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid var(--oripio-border-subtle);">
                    <div class="d-flex gap-2">
                        <a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-secondary btn-pill">
                            <i class="fa fa-external-link-alt me-1"></i> Lihat Halaman Utama
                        </a>
                    </div>
                    <button type="submit" class="btn btn-oripio-primary btn-pill">
                        <i class="fa fa-save me-1"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- KELOLA BERITA: INFORMASI TERKINI HALAMAN UTAMA --}}
    <div class="card p-2 mt-4" id="kelola-berita">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center" style="padding: 16px 18px 4px !important;">
            <div>
                <h5 class="fw-bold text-dark mb-1" style="font-size: 1.02rem;">
                    <i class="fa fa-newspaper text-success me-1"></i> Informasi Terkini (Berita Beranda)
                </h5>
                <p class="text-muted mb-0" style="font-size: 0.82rem;">
                    Berita pada bagian ini tampil di section Informasi Terkini halaman utama (maksimal 6 berita terbaru).
                </p>
            </div>
            <span class="badge badge-light mt-2 mt-md-0">{{ $informasiList->count() }} berita</span>
        </div>
        <div class="card-body p-3">
            <form action="{{ route('superadmin.informasi.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Judul Informasi</label>
                            <input type="text" name="judul" class="form-control" maxlength="100" value="{{ old('judul') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Gambar Pendukung</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">
                            <small class="text-muted">Opsional, maksimal 2 MB.</small>
                            <small class="text-danger gambar-error d-none" data-gambar-error></small>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label class="fw-bold">Deskripsi Informasi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" required>{{ old('deskripsi') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-oripio-primary btn-pill">
                        <i class="fa fa-plus me-1"></i> Tambah Berita
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 56px;">No</th>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th>Gambar</th>
                            <th>Tanggal</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($informasiList as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $item->judul }}</td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit($item->deskripsi, 90) }}</td>
                                <td>
                                    @if ($item->gambar && file_exists(public_path('uploads/informasi/' . $item->gambar)))
                                        <img src="{{ asset('uploads/informasi/' . $item->gambar) }}"
                                            alt="{{ $item->judul }}"
                                            style="height: 46px; width: 64px; object-fit: cover; border-radius: 8px;">
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-success btn-pill"
                                        data-toggle="modal" data-target="#editBerita{{ $item->id_informasi }}">
                                        <i class="fa fa-pen me-1"></i> Edit
                                    </button>
                                    <form action="{{ route('superadmin.informasi.destroy', $item) }}" method="POST"
                                        class="d-inline js-hapus-berita" data-judul="{{ $item->judul }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-pill">
                                            <i class="fa fa-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada berita. Tambahkan berita pertama melalui formulir di atas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
    (function () {
        document.querySelectorAll('.js-hapus-berita').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (form.dataset.confirmed === '1') {
                    return;
                }

                event.preventDefault();

                var judul = form.getAttribute('data-judul') || 'berita ini';

                function submitForm() {
                    form.dataset.confirmed = '1';
                    form.submit();
                }

                if (window.Swal) {
                    Swal.fire({
                        title: 'Hapus Berita?',
                        text: 'Berita "' + judul + '" akan dihapus permanen.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#059652',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            submitForm();
                        }
                    });
                } else if (window.confirm('Hapus berita "' + judul + '"?')) {
                    submitForm();
                }
            });
        });
    })();
</script>

<script>
    (function () {
        var maxSize = 2 * 1024 * 1024;
        var message = 'Ukuran gambar pendukung maksimal 2 MB.';

        document.querySelectorAll('input[type="file"][name="gambar"]').forEach(function (input) {
            var form = input.closest('form');
            var error = input.parentElement.querySelector('[data-gambar-error]');

            function validateImage() {
                var file = input.files && input.files[0];
                var invalid = file && file.size > maxSize;

                input.setCustomValidity(invalid ? message : '');
                input.dataset.invalid = invalid ? '1' : '0';

                if (error) {
                    error.textContent = invalid ? message : '';
                    error.classList.toggle('d-none', !invalid);
                }

                if (invalid) {
                    input.value = '';
                    input.setCustomValidity(message);
                }

                return !invalid;
            }

            input.addEventListener('change', validateImage);

            if (form) {
                form.addEventListener('submit', function (event) {
                    if (input.dataset.invalid === '1') {
                        event.preventDefault();
                        input.reportValidity();
                    }
                });
            }
        });
    })();
</script>

<script>
    (function () {
        var counters = {};

        document.querySelectorAll('[data-repeater]').forEach(function (root) {
            var key = root.getAttribute('data-repeater');
            var rows = root.querySelector('[data-repeater-rows]');

            counters[key] = rows ? rows.querySelectorAll('[data-repeater-row]').length : 0;

            root.addEventListener('click', function (event) {
                var addButton = event.target.closest('[data-repeater-add]');

                if (addButton) {
                    var template = document.querySelector('template[data-repeater-template="' + key + '"]');

                    if (!template || !rows) {
                        return;
                    }

                    var html = template.innerHTML.replace(/__INDEX__/g, counters[key]++);
                    var holder = document.createElement('div');
                    holder.innerHTML = html.trim();
                    rows.appendChild(holder.firstElementChild);

                    return;
                }

                var removeButton = event.target.closest('[data-repeater-remove]');

                if (removeButton) {
                    var row = removeButton.closest('[data-repeater-row]');

                    if (row) {
                        row.remove();
                    }
                }
            });
        });
    })();
</script>
@endsection

{{-- MODAL EDIT BERITA
     Dirender lewat @yield('modals') di layout agar berada di luar #main-wrapper.
     Bila tetap di dalam .content-body, modal tertutup .modal-backdrop dan
     tombol Simpan Perubahan tidak bisa diklik. --}}
@section('modals')
    @foreach ($informasiList as $item)
        <div class="modal fade" id="editBerita{{ $item->id_informasi }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('superadmin.informasi.update', $item) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Berita</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group">
                                <label class="fw-bold">Judul Informasi</label>
                                <input type="text" name="judul" class="form-control" maxlength="100"
                                    value="{{ $item->judul }}" required>
                            </div>

                            <div class="form-group">
                                <label class="fw-bold">Gambar Pendukung</label>
                                <input type="file" name="gambar" class="form-control" accept="image/*">
                                <small class="text-muted">Kosongkan bila gambar tidak diubah. Maksimal 2 MB.</small>
                                <small class="text-danger gambar-error d-none" data-gambar-error></small>

                                @if ($item->gambar && file_exists(public_path('uploads/informasi/' . $item->gambar)))
                                    <div class="d-flex align-items-center mt-2">
                                        <img src="{{ asset('uploads/informasi/' . $item->gambar) }}"
                                            alt="{{ $item->judul }}"
                                            style="height: 56px; width: 78px; object-fit: cover; border-radius: 8px;"
                                            class="me-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="hapus_gambar"
                                                value="1" id="hapusGambar{{ $item->id_informasi }}">
                                            <label class="form-check-label text-danger"
                                                for="hapusGambar{{ $item->id_informasi }}">
                                                Hapus gambar ini
                                            </label>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="form-group mb-0">
                                <label class="fw-bold">Deskripsi Informasi</label>
                                <textarea name="deskripsi" class="form-control" rows="5" required>{{ $item->deskripsi }}</textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-pill" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-oripio-primary btn-pill">
                                <i class="fa fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
