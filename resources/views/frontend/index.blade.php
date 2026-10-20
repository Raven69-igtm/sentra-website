@extends ('frontend.template')

@section('content')

    <body class="index-page">

        <header id="header" class="header d-flex align-items-center fixed-top">
            <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

                <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                    @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
                        <img src="{{ asset($appLogo) }}" alt="Logo" style="max-height: 38px; margin-right: 10px; border-radius: 6px;">
                    @endif
                    <h1 class="sitename">{{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}</h1>
                </a>

                <nav id="navmenu" class="navmenu">
                    <ul>
                        <li><a href="#hero" class="active">Beranda</a></li>
                        <li><a href="#about">Tentang Kami</a></li>
                        <li><a href="#swipper">Aplikasi</a></li>
                        <li><a href="#faq">Pertanyaan Umum</a></li>
                        <li><a href="#information">Informasi</a></li>
                        <li><a href="#contact">Kontak</a></li>
                        {{-- <li><a href="#team">Team</a></li> --}}
                    </ul>
                </nav>

                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('login') }}" class="btn btn-success"><i class="bi bi-box-arrow-in-right me-1"></i>
                        Admin </a>
                </div>

                <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>

            </div>
        </header>

        <!-- Hero Section -->
        <section id="hero" class="hero section dark-background2">
            <img src="{{ asset('landingpage/assets/img/nganjuk.webp') }}" alt="" class="hero-bg">

            <div class="container d-flex flex-column align-items-center justify-content-center text-center"
                style="min-height: 100vh;">
                @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
                    <img src="{{ asset($appLogo) }}" class="img-fluid animated mb-4"
                        alt="Logo" style="max-width: 180px; max-height: 140px; object-fit: contain;">
                @else
                    <img src="{{ asset('landingpage/assets/img/logo_nganjuk.png') }}" class="img-fluid animated mb-4"
                        alt="" style="max-width: 250px; height: auto;">
                @endif

                <h1 class="text-white"><span>{{ \App\Models\SystemSetting::get('instansi_name', 'Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak') }}</span></h1>
                <p>{{ \App\Models\SystemSetting::get('app_description', 'Pusat informasi dan layanan digital untuk mewujudkan perlindungan dan pemberdayaan yang berkeadilan.') }}</p>
            </div>

            <svg class="hero-waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                viewBox="0 24 150 28 " preserveAspectRatio="none">
                <defs>
                    <path id="wave-path" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z">
                    </path>
                </defs>
                <g class="wave1">
                    <use xlink:href="#wave-path" x="50" y="3"></use>
                </g>
                <g class="wave2">
                    <use xlink:href="#wave-path" x="50" y="0"></use>
                </g>
                <g class="wave3">
                    <use xlink:href="#wave-path" x="50" y="9"></use>
                </g>
            </svg>

        </section><!-- /Hero Section -->

        <!-- About Section -->
        <section id="about" class="about section">

            <!-- Section Title -->
            <div class="container section-title text-center" data-aos="fade-down" data-aos-duration="800">
                <h2 class="section-main-title">TENTANG KAMI</h2>
                <p class="section-description">Tentang Web Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak</p>
            </div><!-- End Section Title -->

            <div class="container">
                <div class="row gy-4 align-items-center features-item px-xl-5">
                    <div class="col-md-5 d-flex align-items-center" data-aos="zoom-in" data-aos-delay="200">
                        <img src="{{ asset('landingpage/assets/img/family-1.png') }}" class="img-fluid" alt="">
                    </div>
                    <div class="col-md-7" data-aos="fade-left" data-aos-delay="300" style="text-align: justify;">
                        <p data-aos="fade-up" data-aos-delay="400">
                            Website ini merupakan portal informasi resmi dari Dinas Sosial P3A yang bertujuan untuk
                            memberikan layanan informasi, edukasi, serta wadah komunikasi
                            bagi masyarakat terkait isu-isu sosial, pemberdayaan perempuan, dan perlindungan anak.
                            Melalui platform ini, kami berkomitmen untuk:
                        </p>
                        <ul data-aos="fade-up" data-aos-delay="500">
                            <li><i class="bi bi-check2-circle"></i><span> Meningkatkan transparansi dan akuntabilitas dalam
                                    penyelenggaraan program sosial.</span></li>
                            <li><i class="bi bi-check2-circle"></i><span> Memberikan akses mudah terhadap layanan sosial,
                                    pengaduan, dan perlindungan hak-hak perempuan dan anak.</span></li>
                            <li><i class="bi bi-check2-circle"></i><span> Menyediakan informasi terkini mengenai kegiatan,
                                    program kerja, serta kebijakan pemerintah dibidang sosial dan pemberdayaan
                                    masyarakat.</span></li>
                        </ul>
                        <p data-aos="fade-up" data-aos-delay="600">
                            Kami percaya bahwa sinergi antara pemerintah dan masyarakat menjadi kunci utama dalam mewujudkan
                            kesejahteraan sosial yang inklusif dan berkeadilan.
                            Mari bersama menciptakan lingkungan yang aman, ramah, dan memberdayakan bagi selruh warga,
                            terutama perempuan dan anak-anak.
                        </p>
                        <p data-aos="fade-up" data-aos-delay="700"> Salam hangat, </p>
                        <p class="fst-italic" data-aos="fade-up" data-aos-delay="750">Tim Dinas Sosial P3A</p>
                    </div>
                </div>
            </div>

        </section><!-- /About Section -->

        <section id="swipper" class="fitur-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="container section-title text-center" data-aos="fade-up" data-aos-delay="100">
                        <h2 class="section-main-title">APLIKASI MOBILE</h2>
                        <p class="section-description">Aplikasi dari Dinas Sosial Pemberdayaan Perempuan dan Perlindungan
                            Anak</p>
                    </div>
                    <!-- Kolom kiri: Swiper 3D -->
                    <div class="col-lg-6">
                        <div class="swiper-container-wrapper">
                            <div class="swiper my-swiper-3d">
                                <div class="swiper-wrapper">
                                    <div class="swiper-slide">
                                        <div class="phone-mockup">
                                            <img src="landingpage/assets/img/pertama.png" alt="Fitur Pencarian Lapangan"
                                                class="app-screenshot">

                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="phone-mockup">
                                            <img src="landingpage/assets/img/halaman-login.png" alt="Fitur Booking Online"
                                                class="app-screenshot">

                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="phone-mockup">
                                            <img src="landingpage/assets/img/laporan-cepat.png"
                                                alt="Fitur Pembayaran Aman" class="app-screenshot">

                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="phone-mockup">
                                            <img src="landingpage/assets/img/beranda.png" alt="Fitur Info Event"
                                                class="app-screenshot">

                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="phone-mockup">
                                            <img src="landingpage/assets/img/chat.png" alt="Fitur Notifikasi"
                                                class="app-screenshot">

                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="phone-mockup">
                                            <img src="landingpage/assets/img/laporan.png" alt="Fitur Profile"
                                                class="app-screenshot">

                                        </div>
                                    </div>
                                    <div class="swiper-slide">
                                        <div class="phone-mockup">
                                            <img src="landingpage/assets/img/profil.png" alt="Fitur Rating"
                                                class="app-screenshot">

                                        </div>
                                    </div>
                                </div>
                                <!-- Pagination -->
                                <div class="swiper-pagination"></div>
                                <!-- Navigation -->
                                <div class="swiper-button-next">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                                <div class="swiper-button-prev">
                                    <i class="fas fa-chevron-left"></i>
                                </div>
                            </div>

                            <!-- Decorative elements -->
                            <div class="floating-element element-1"></div>
                            <div class="floating-element element-2"></div>
                            <div class="floating-element element-3"></div>
                        </div>
                    </div>

                    <!-- Kolom kanan: Deskripsi -->
                    <div class="col-lg-6">
                        <div class="content-wrapper">
                            <h2>
                                <span class="highlight">SENTRA</span>
                            </h2>
                            <p class="section-description">
                                "SENTRA adalah aplikasi pelaporan cepat untuk kasus yang melibatkan perempuan dan anak di
                                Kabupaten Nganjuk.
                                Melalui SENTRA, masyarakat dapat langsung melapor ke Dinas Sosial P3A tanpa harus datang ke
                                kantor, sehingga proses pelaporan menjadi lebih mudah, cepat, dan efisien."
                            </p>

                            <div class="features-list">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="bi bi-send-fill"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Pelaporan Cepat</h4>
                                        <p>Membuat laporan tanpa perlu registrasi dan login</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Tracking Laporan</h4>
                                        <p>Tracking laporan anda sudah sampai tahap mana</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="bi bi-clipboard-data-fill"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Riwayat laporan</h4>
                                        <p>Lihat jumlah riwayat laporan yang sudah ditangani oleh Dinas Sosial P3A</p>
                                    </div>
                                </div>

                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="bi bi-chat-dots-fill"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h4>Chat Konsultasi</h4>
                                        <p>Anda bisa konsultasi kepada admin melalui aplikasi SENTRA</p>
                                    </div>
                                </div>
                            </div>

                            <div class="download-section">
                                <p class="download-text">Cooming Soon!</p>
                                <a href="https://play.google.com/store/apps/details?id=com.example.app" target="_blank"
                                    class="download-btn">
                                    <img src="landingpage/assets/img/playstore.png" alt="Download di Play Store"
                                        class="playstore-img">
                                    <div class="btn-overlay">
                                        <i class="fab fa-google-play"></i>
                                        <span>Download Gratis</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Faq Section -->
        <section id="faq" class="faq section">

            <div class="container-fluid">

                <div class="row gy-4">

                    <div class="col-lg-7 mx-auto d-flex flex-column justify-content-center order-2 order-lg-1">

                        <div class="container section-title text-center" data-aos="fade-up" data-aos-delay="100">
                            <h2 class="section-main-title">PERTANYAAN UMUM</h2>
                            <p class="section-description">Pertanyaan umum terkait Dinas Sosial Pemberdayaan Perempuan dan
                                Perlindungan Anak</p>
                        </div>

                        <div class="faq-container px-xl-5" data-aos="fade-up" data-aos-delay="200">

                            <div class="faq-item">
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>Apa itu Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak?</h3>
                                <div class="faq-content">
                                    <p> Dinas Sosial P3A adalah instansi pemerintah yang bertugas dalam bidang
                                        kesejahteraan sosial,
                                        perlindungan perempuan, dan perlindungan anak.
                                    </p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>Bagaimana cara melaporkan kasus kekerasan terhadap perempuan dan anak?</h3>
                                <div class="faq-content">
                                    <p> Laporan pengaduan dapat dilakukan melalui aplikasi mobile yang dapat diunduh melalui
                                        Website
                                        resmi Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak.
                                    </p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>Siapa yang dapat mengakses layanan Dinas Sosial Pemberdayaan Perempuan dan Perlindungan
                                    Anak?</h3>
                                <div class="faq-content">
                                    <p> Masyarakat yang membutuhkan informasi dan edukasi terkait hak perempuan dan anak,
                                        perempuan dan anak korban kekerasan atau diskriminasi,
                                        dan pihak keluarga atau masyarakat yang ingin melaporkan kasus kekerasan, pelecehan,
                                        dan lain sebagainya.</p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>Apakah layanan di Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak berbayar?
                                </h3>
                                <div class="faq-content">
                                    <p> Tidak. Semua layanan yang diberikan oleh Dinas Sosial Pemberdayaan Perlindungan Anak
                                        bersifat gratis
                                        dan terbuka bagi masyarakat yang membutuhkan.
                                    </p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                            <div class="faq-item">
                                <i class="faq-icon bi bi-question-circle"></i>
                                <h3>Bagaimana cara mengetahui informasi terbaru dari Dinas Sosial Pemberdayaan perempuan dan
                                    Perlindungan Anak?</h3>
                                <div class="faq-content">
                                    <p> Informasi terbaru dari Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak
                                        dapat diperoleh melalui Website resmi,
                                        Sosial Media resmi, atau dapat langsung ke Kantor Dinas Sosial Pemberdayaan
                                        Perempuan dan Perlindungan Anak.
                                    </p>
                                </div>
                                <i class="faq-toggle bi bi-chevron-right"></i>
                            </div><!-- End Faq item-->

                        </div>

                    </div>

                </div>

            </div>

        </section><!-- /Faq Section -->

        <section id="information" class="fitur-section">
            <div class="container">
                <!-- Section Title -->
                <div class="section-title text-center" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="section-main-title">INFORMASI TERKINI</h2>
                    <p class="section-description">Informasi terbaru dari Dinas Sosial Pemberdayaan Perempuan dan
                        Perlindungan Anak</p>
                </div>

                <div class="row g-4">
                    @if (isset($informasiList) && $informasiList->count() > 0)
                        @foreach ($informasiList as $index => $informasi)
                            @php
                                $isLastRowSingle = $loop->remaining == 0 && $loop->count % 3 == 1;
                                $isLastRowDouble = $loop->remaining == 1 && $loop->count % 3 == 2;
                            @endphp

                            <div
                                class="@if ($isLastRowSingle) col-lg-4 offset-lg-4 col-md-6 offset-md-3 @elseif($isLastRowDouble) col-lg-4 col-md-6 @else col-md-6 col-lg-4 @endif mb-4">
                                <div class="card h-100 shadow-sm border-0 rounded-4 hover-shadow">

                                    <!-- Gambar dengan error handling yang lebih baik -->
                                    @if (!empty($informasi->gambar))
                                        @php
                                            $imagePath = public_path('uploads/informasi/' . $informasi->gambar);
                                            $imageExists = file_exists($imagePath);
                                        @endphp

                                        @if ($imageExists)
                                            <img src="{{ asset('uploads/informasi/' . $informasi->gambar) }}"
                                                class="card-img-top" alt="{{ $informasi->judul }}"
                                                style="height: 200px; object-fit: cover;" loading="lazy">
                                        @else
                                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                                style="height: 200px;">
                                                <div class="text-center text-muted">
                                                    <i class="bi bi-image" style="font-size: 3rem;"></i>
                                                    <p class="mt-2 mb-0 small">Gambar tidak ditemukan</p>
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                            style="height: 200px;">
                                            <div class="text-center text-muted">
                                                <i class="bi bi-image" style="font-size: 3rem;"></i>
                                                <p class="mt-2 mb-0 small">Tidak ada gambar</p>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title fw-semibold description-title">
                                            {{ $informasi->judul ?? 'Judul tidak tersedia' }}
                                        </h5>
                                        <p class="card-text text-muted">
                                            {{ \Illuminate\Support\Str::limit(strip_tags($informasi->deskripsi ?? ''), 100, '...') }}
                                        </p>

                                        <div class="mt-auto d-flex justify-content-between align-items-center">
                                            <small class="badge bg-light text-dark">
                                                {{ $informasi->created_at ? $informasi->created_at->diffForHumans() : 'Tanggal tidak tersedia' }}
                                            </small>
                                            <button type="button"
                                                class="btn btn-outline-success btn-sm btn-modal-trigger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#infoModal{{ $informasi->id_informasi ?? $index }}"
                                                data-info-id="{{ $informasi->id_informasi ?? $index }}">
                                                <i class="bi bi-eye me-1"></i>Baca Selengkapnya
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Jika tidak ada data informasi -->
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="bi bi-info-circle text-muted" style="font-size: 4rem;"></i>
                                </div>
                                <h4 class="text-muted">Belum Ada Informasi</h4>
                                <p class="text-muted mb-0">Informasi terbaru akan segera tersedia.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Modal untuk Informasi dengan Error Handling -->
        @if (isset($informasiList) && $informasiList->count() > 0)
            @foreach ($informasiList as $index => $informasi)
                <div class="modal fade" id="infoModal{{ $informasi->id_informasi ?? $index }}" tabindex="-1"
                    aria-labelledby="infoModalLabel{{ $informasi->id_informasi ?? $index }}" aria-hidden="true"
                    data-bs-backdrop="true" data-bs-keyboard="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">

                            <!-- Modal Header -->
                            <div class="modal-header border-bottom">
                                <h5 class="modal-title fw-bold"
                                    id="infoModalLabel{{ $informasi->id_informasi ?? $index }}">
                                    <i class="bi bi-info-circle text-primary me-2"></i>
                                    {{ $informasi->judul ?? 'Informasi' }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <!-- Modal Body -->
                            <div class="modal-body">
                                <!-- Gambar -->
                                @if (!empty($informasi->gambar))
                                    @php
                                        $imagePath = public_path('uploads/informasi/' . $informasi->gambar);
                                        $imageExists = file_exists($imagePath);
                                    @endphp

                                    @if ($imageExists)
                                        <div class="text-center mb-4">
                                            <img src="{{ asset('uploads/informasi/' . $informasi->gambar) }}"
                                                class="img-fluid rounded shadow-sm" alt="{{ $informasi->judul }}"
                                                style="max-height: 400px; width: auto;" loading="lazy">
                                        </div>
                                    @endif
                                @endif

                                <!-- Meta Information -->
                                <div class="d-flex align-items-center mb-4 text-muted small border-bottom pb-3">
                                    <span class="badge bg-success me-3">
                                        <i class="bi bi-megaphone me-1"></i>Informasi
                                    </span>
                                    @if ($informasi->created_at)
                                        <span class="me-3">
                                            <i class="bi bi-clock me-1"></i>{{ $informasi->created_at->diffForHumans() }}
                                        </span>
                                        <span>
                                            <i
                                                class="bi bi-calendar me-1"></i>{{ $informasi->created_at->format('d F Y') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="content-text" style="line-height: 1.7;">
                                    @if (!empty($informasi->deskripsi))
                                        {!! nl2br(e($informasi->deskripsi)) !!}
                                    @else
                                        <p class="text-muted fst-italic">Deskripsi tidak tersedia.</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="modal-footer border-top">
                                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                                    <i class="bi bi-check-circle me-1"></i>Tutup
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        @endif


        <!-- Contact Section -->
        <section id="contact" class="contact section">

            <!-- Section Title -->
            <div class="container section-title text-center" data-aos="fade-up">
                <h2 class="section-main-title">CEK KONTAK KAMI</h2>
                <p class="section-description">Informasi terkait Kontak {{ \App\Models\SystemSetting::get('instansi_name', 'Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak') }}</p>
            </div><!-- End Section Title -->

            <div class="container" data-aos="fade" data-aos-delay="100">

                <div class="info-wrapper grid-layout">

                    <div class="info-item text-center" data-aos="fade-up" data-aos-delay="200">
                        <i class="bi bi-geo-alt"></i>
                        <div>
                            <h3>Alamat</h3>
                            <p>{{ \App\Models\SystemSetting::get('office_address', 'Jl Supriyadi No 7, Kauman, Mangundikaran, Mangun Dikaran, Kec. Nganjuk, Kabupaten Nganjuk, Jawa Timur 64412') }}</p>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="info-item text-center" data-aos="fade-up" data-aos-delay="300">
                        <i class="bi bi-telephone"></i>
                        <div>
                            <h3>Hotline & Telepon</h3>
                            <p>{{ \App\Models\SystemSetting::get('hotline_number', '(0358) 3550722') }}</p>
                            @if ($emergencyCall = \App\Models\SystemSetting::get('emergency_call'))
                                <p><small class="text-danger fw-bold"><i class="bi bi-exclamation-octagon me-1"></i>Darurat: {{ $emergencyCall }}</small></p>
                            @endif
                        </div>
                    </div><!-- End Info Item -->

                    <div class="info-item text-center" data-aos="fade-up" data-aos-delay="300">
                        <i class="bi bi-envelope"></i>
                        <div>
                            <h3>Email</h3>
                            <p>{{ \App\Models\SystemSetting::get('office_email', 'dinsospppa@nganjukkab.go.id') }}</p>
                        </div>
                    </div><!-- End Info Item -->

                    <div class="info-item text-center" data-aos="fade-up" data-aos-delay="300">
                        <i class="bi bi-clock"></i>
                        <div>
                            <h3>Jam Buka</h3>
                            <p>Senin - Jum'at</p>
                            <p>07:30 AM - 03:30 PM</p>
                        </div>
                    </div><!-- End Info Item -->
                </div>
            </div>

            <div class="container-fluid mt-5">

                <div class="container my-4" style="max-width: 800px;">
                    <div class="responsive-map">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.8438585054073!2d111.898124!3d-7.6013195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e784b06ef170309%3A0xb23b3f2501c3c28e!2sDinas%20Sosial%20Pemberdayaan%20Perempuan%20dan%20Perlindungan%20Anak!5e0!3m2!1sid!2sid!4v1713866091206!5m2!1sid!2sid"
                            width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </section><!-- /Contact Section -->

        <footer id="footer" class="footer dark-background">

            <div class="container footer-top">
                <div class="row gy-4">
                    <div class="col-lg-4 col-md-6 footer-about">
                        <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                            @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
                                <img src="{{ asset($appLogo) }}" alt="Logo" style="max-height: 35px; margin-right: 10px; border-radius: 4px;">
                            @endif
                            <span class="sitename">{{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}</span>
                        </a>
                        <p>{{ \App\Models\SystemSetting::get('app_description', 'Sistem Entitas Pelaporan dan Respons Tanggap Aksi') }}</p>
                    </div>

                    <div class="col-lg-2 col-md-3 footer-links">
                        <h4>Useful Links</h4>
                        <ul>
                            <li><a href="#hero" class="active">Beranda</a></li>
                            <li><a href="#about">Tentang Kami</a></li>
                            <li><a href="#swipper">Aplikasi</a></li>
                            <li><a href="#faq">Pertanyaan Umum</a></li>
                            <li><a href="#contact">Kontak</a></li>
                            {{-- <li><a href="#team">Team</a></li> --}}
                        </ul>
                    </div>

                    <div class="col-lg-2 col-md-3 footer-links">
                        <h4>Layanan Kami</h4>
                        <ul>
                            <li><a href="/layanan/perempuan">Layanan Pemberdayaan Perempuan</a></li>
                            <li><a href="/layanan/anak">Layanan Perlindungan Anak</a></li>
                            <li><a href="/layanan/kesejahteraan">Layanan Sosial dan Bantuan Kesejahteraan</a></li>
                            <li><a href="/layanan/edukasi">Layanan Konsultasi dan Edukasi</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-12 footer-newsletter">
                        <h4>Sosial Media Kami</h4>
                        <p>Follow Media Sosial untuk mendapatkan informasi terbaru kami.</p>
                        <div class="social-links d-flex">
                            <a href="https://www.instagram.com/dinsosp3anganjuk" target="_blank" class="mx-2"><i
                                    class="bi bi-instagram"></i></a>
                            <a href="https://www.tiktok.com/@dinsosp3a_nganjuk" target="_blank" class="mx-2"><i
                                    class="bi bi-tiktok"></i></a>
                        </div>

                    </div>
                </div>

                <div class="container copyright text-center mt-4">
                    <p>{{ \App\Models\SystemSetting::get('footer_copyright', '© Copyright SENTRA All Rights Reserved') }}</p>
                    <div class="credits">
                    </div>
                </div>

        </footer>

    </body>
@endsection
