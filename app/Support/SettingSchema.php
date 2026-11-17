<?php

namespace App\Support;

/**
 * Sumber tunggal daftar konfigurasi SENTRA.
 *
 * Semua nilai default di bawah ini disalin dari konten yang sebelumnya
 * ditulis langsung (hardcoded) pada halaman utama. Tujuannya agar tampilan
 * landing page tidak berubah sama sekali ketika setting belum pernah diubah.
 */
class SettingSchema
{
    /**
     * Metadata tab pada halaman konfigurasi Superadmin.
     */
    public static function groups(): array
    {
        return [
            'identitas' => ['label' => 'Identitas & Umum', 'icon' => 'fa-info-circle'],
            'kontak'    => ['label' => 'Kontak & Hotline', 'icon' => 'fa-phone'],
            'hero'      => ['label' => 'Beranda — Hero & Tentang', 'icon' => 'fa-image'],
            'aplikasi'  => ['label' => 'Beranda — Aplikasi', 'icon' => 'fa-mobile-alt'],
            'faq'       => ['label' => 'Beranda — FAQ & Informasi', 'icon' => 'fa-question-circle'],
            'beranda'   => ['label' => 'Beranda — Kontak & Navigasi', 'icon' => 'fa-map-marker-alt'],
            'footer'    => ['label' => 'Beranda — Footer & Sosmed', 'icon' => 'fa-shoe-prints'],
            'laporan'   => ['label' => 'Alur Laporan & SLA', 'icon' => 'fa-file-text'],
            'keamanan'  => ['label' => 'Keamanan & Sesi', 'icon' => 'fa-shield'],
        ];
    }

    /**
     * Daftar field: key => atribut.
     *
     * type: text | textarea | number | select | file | json
     */
    public static function fields(): array
    {
        return [
            /* ------------------------------------------------------------
             * IDENTITAS & UMUM (sudah ada sebelumnya)
             * ---------------------------------------------------------- */
            'app_name' => [
                'group' => 'identitas', 'type' => 'text',
                'label' => 'Nama Aplikasi',
                'hint'  => 'Nama brand aplikasi yang tampil pada navbar dan judul web.',
                'default' => 'SENTRA',
                'rules' => ['required', 'string', 'max:100'],
            ],
            'instansi_name' => [
                'group' => 'identitas', 'type' => 'text',
                'label' => 'Nama Instansi / Lembaga',
                'hint'  => 'Ditampilkan pada hero beranda, subjudul kontak, dan judul web.',
                'default' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
                'rules' => ['required', 'string', 'max:150'],
            ],
            'app_description' => [
                'group' => 'identitas', 'type' => 'textarea',
                'label' => 'Deskripsi Ringkas Layanan',
                'hint'  => 'Tampil pada hero beranda dan sebagai teks tentang kami di footer.',
                'default' => 'Sistem Informasi dan Pelayanan Terpadu Penanganan Kekerasan terhadap Perempuan dan Anak',
                'rules' => ['nullable', 'string', 'max:500'],
            ],
            'footer_copyright' => [
                'group' => 'identitas', 'type' => 'text',
                'label' => 'Hak Cipta Footer (Copyright)',
                'hint'  => 'Teks hak cipta pada footer halaman utama.',
                'default' => '© 2026 SENTRA. All rights reserved.',
                'rules' => ['nullable', 'string', 'max:150'],
            ],
            'app_logo' => [
                'group' => 'identitas', 'type' => 'file',
                'label' => 'Logo Aplikasi',
                'hint'  => 'Logo pada navbar, hero, footer, dan favicon bila favicon belum diatur.',
                'default' => null,
                'rules' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            ],

            /* ------------------------------------------------------------
             * HERO & TENTANG
             * ---------------------------------------------------------- */
            'hero_background' => [
                'group' => 'hero', 'type' => 'file',
                'label' => 'Gambar Latar Hero',
                'hint'  => 'Gambar latar bagian paling atas beranda. Rekomendasi 1920x1080 px.',
                'default' => 'landingpage/assets/img/nganjuk.webp',
                'rules' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:4096'],
            ],
            'about_title' => [
                'group' => 'hero', 'type' => 'text',
                'label' => 'Judul Section Tentang Kami',
                'default' => 'TENTANG KAMI',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'about_subtitle' => [
                'group' => 'hero', 'type' => 'text',
                'label' => 'Subjudul Section Tentang Kami',
                'default' => 'Tentang Web Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak',
                'rules' => ['nullable', 'string', 'max:200'],
            ],
            'about_image' => [
                'group' => 'hero', 'type' => 'file',
                'label' => 'Gambar Section Tentang Kami',
                'default' => 'landingpage/assets/img/family-1.png',
                'rules' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048'],
            ],
            'about_paragraph_1' => [
                'group' => 'hero', 'type' => 'textarea',
                'label' => 'Paragraf Pembuka Tentang Kami',
                'default' => 'Website ini merupakan portal informasi resmi dari Dinas Sosial P3A yang bertujuan untuk memberikan layanan informasi, edukasi, serta wadah komunikasi bagi masyarakat terkait isu-isu sosial, pemberdayaan perempuan, dan perlindungan anak. Melalui platform ini, kami berkomitmen untuk:',
                'rules' => ['nullable', 'string', 'max:1000'],
            ],
            'about_bullet_1' => [
                'group' => 'hero', 'type' => 'text',
                'label' => 'Poin Tentang Kami #1',
                'default' => 'Meningkatkan transparansi dan akuntabilitas dalam penyelenggaraan program sosial.',
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'about_bullet_2' => [
                'group' => 'hero', 'type' => 'text',
                'label' => 'Poin Tentang Kami #2',
                'default' => 'Memberikan akses mudah terhadap layanan sosial, pengaduan, dan perlindungan hak-hak perempuan dan anak.',
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'about_bullet_3' => [
                'group' => 'hero', 'type' => 'text',
                'label' => 'Poin Tentang Kami #3',
                'default' => 'Menyediakan informasi terkini mengenai kegiatan, program kerja, serta kebijakan pemerintah dibidang sosial dan pemberdayaan masyarakat.',
                'rules' => ['nullable', 'string', 'max:255'],
            ],
            'about_paragraph_2' => [
                'group' => 'hero', 'type' => 'textarea',
                'label' => 'Paragraf Penutup Tentang Kami',
                'default' => 'Kami percaya bahwa sinergi antara pemerintah dan masyarakat menjadi kunci utama dalam mewujudkan kesejahteraan sosial yang inklusif dan berkeadilan. Mari bersama menciptakan lingkungan yang aman, ramah, dan memberdayakan bagi selruh warga, terutama perempuan dan anak-anak.',
                'rules' => ['nullable', 'string', 'max:1000'],
            ],
            'about_closing' => [
                'group' => 'hero', 'type' => 'text',
                'label' => 'Salam Penutup',
                'default' => 'Salam hangat,',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'about_signature' => [
                'group' => 'hero', 'type' => 'text',
                'label' => 'Tanda Tangan / Nama Tim',
                'default' => 'Tim Dinas Sosial P3A',
                'rules' => ['nullable', 'string', 'max:150'],
            ],

            /* ------------------------------------------------------------
             * APLIKASI MOBILE
             * ---------------------------------------------------------- */
            'mobile_title' => [
                'group' => 'aplikasi', 'type' => 'text',
                'label' => 'Judul Section Aplikasi Mobile',
                'default' => 'APLIKASI MOBILE',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'mobile_subtitle' => [
                'group' => 'aplikasi', 'type' => 'text',
                'label' => 'Subjudul Section Aplikasi Mobile',
                'default' => 'Aplikasi dari Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak',
                'rules' => ['nullable', 'string', 'max:200'],
            ],
            'mobile_app_name' => [
                'group' => 'aplikasi', 'type' => 'text',
                'label' => 'Nama Aplikasi (judul besar)',
                'default' => 'SENTRA',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'mobile_description' => [
                'group' => 'aplikasi', 'type' => 'textarea',
                'label' => 'Deskripsi Aplikasi Mobile',
                'hint'  => 'Tanda kutip di awal & akhir ditampilkan otomatis oleh template.',
                'default' => 'SENTRA adalah aplikasi pelaporan cepat untuk kasus yang melibatkan perempuan dan anak di Kabupaten Nganjuk. Melalui SENTRA, masyarakat dapat langsung melapor ke Dinas Sosial P3A tanpa harus datang ke kantor, sehingga proses pelaporan menjadi lebih mudah, cepat, dan efisien.',
                'rules' => ['nullable', 'string', 'max:1000'],
            ],
            'mobile_screenshots' => [
                'group' => 'aplikasi', 'type' => 'json',
                'label' => 'Tangkapan Layar Aplikasi',
                'hint'  => 'Urutan gambar mengikuti urutan pada daftar (carousel beranda).',
                'default' => [
                    ['src' => 'landingpage/assets/img/pertama.png', 'alt' => 'Fitur Pencarian Lapangan'],
                    ['src' => 'landingpage/assets/img/halaman-login.png', 'alt' => 'Fitur Booking Online'],
                    ['src' => 'landingpage/assets/img/laporan-cepat.png', 'alt' => 'Fitur Pembayaran Aman'],
                    ['src' => 'landingpage/assets/img/beranda.png', 'alt' => 'Fitur Info Event'],
                    ['src' => 'landingpage/assets/img/chat.png', 'alt' => 'Fitur Notifikasi'],
                    ['src' => 'landingpage/assets/img/laporan.png', 'alt' => 'Fitur Profile'],
                    ['src' => 'landingpage/assets/img/profil.png', 'alt' => 'Fitur Rating'],
                ],
                'rules' => ['nullable', 'array'],
            ],
            'mobile_features' => [
                'group' => 'aplikasi', 'type' => 'json',
                'label' => 'Daftar Fitur Aplikasi',
                'hint'  => 'Ikon memakai class Bootstrap Icons, contoh: bi-send-fill.',
                'default' => [
                    ['icon' => 'bi bi-send-fill', 'title' => 'Pelaporan Cepat', 'desc' => 'Membuat laporan tanpa perlu registrasi dan login'],
                    ['icon' => 'bi bi-geo-alt-fill', 'title' => 'Tracking Laporan', 'desc' => 'Tracking laporan anda sudah sampai tahap mana'],
                    ['icon' => 'bi bi-clipboard-data-fill', 'title' => 'Riwayat laporan', 'desc' => 'Lihat jumlah riwayat laporan yang sudah ditangani oleh Dinas Sosial P3A'],
                    ['icon' => 'bi bi-chat-dots-fill', 'title' => 'Chat Konsultasi', 'desc' => 'Anda bisa konsultasi kepada admin melalui aplikasi SENTRA'],
                ],
                'rules' => ['nullable', 'array'],
            ],
            'mobile_download_text' => [
                'group' => 'aplikasi', 'type' => 'text',
                'label' => 'Teks Status Unduhan',
                'default' => 'Cooming Soon!',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'playstore_url' => [
                'group' => 'aplikasi', 'type' => 'text',
                'label' => 'URL Play Store',
                'default' => 'https://play.google.com/store/apps/details?id=com.example.app',
                'rules' => ['nullable', 'string', 'max:500'],
            ],
            'playstore_label' => [
                'group' => 'aplikasi', 'type' => 'text',
                'label' => 'Label Tombol Unduh',
                'default' => 'Download Gratis',
                'rules' => ['nullable', 'string', 'max:100'],
            ],

            /* ------------------------------------------------------------
             * FAQ & INFORMASI
             * ---------------------------------------------------------- */
            'faq_title' => [
                'group' => 'faq', 'type' => 'text',
                'label' => 'Judul Section FAQ',
                'default' => 'PERTANYAAN UMUM',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'faq_subtitle' => [
                'group' => 'faq', 'type' => 'text',
                'label' => 'Subjudul Section FAQ',
                'default' => 'Pertanyaan umum terkait Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak',
                'rules' => ['nullable', 'string', 'max:200'],
            ],
            'faq_items' => [
                'group' => 'faq', 'type' => 'json',
                'label' => 'Daftar Pertanyaan Umum',
                'default' => [
                    [
                        'question' => 'Apa itu Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak?',
                        'answer'   => 'Dinas Sosial P3A adalah instansi pemerintah yang bertugas dalam bidang kesejahteraan sosial, perlindungan perempuan, dan perlindungan anak.',
                    ],
                    [
                        'question' => 'Bagaimana cara melaporkan kasus kekerasan terhadap perempuan dan anak?',
                        'answer'   => 'Laporan pengaduan dapat dilakukan melalui aplikasi mobile yang dapat diunduh melalui Website resmi Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak.',
                    ],
                    [
                        'question' => 'Siapa yang dapat mengakses layanan Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak?',
                        'answer'   => 'Masyarakat yang membutuhkan informasi dan edukasi terkait hak perempuan dan anak, perempuan dan anak korban kekerasan atau diskriminasi, dan pihak keluarga atau masyarakat yang ingin melaporkan kasus kekerasan, pelecehan, dan lain sebagainya.',
                    ],
                    [
                        'question' => 'Apakah layanan di Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak berbayar?',
                        'answer'   => 'Tidak. Semua layanan yang diberikan oleh Dinas Sosial Pemberdayaan Perlindungan Anak bersifat gratis dan terbuka bagi masyarakat yang membutuhkan.',
                    ],
                    [
                        'question' => 'Bagaimana cara mengetahui informasi terbaru dari Dinas Sosial Pemberdayaan perempuan dan Perlindungan Anak?',
                        'answer'   => 'Informasi terbaru dari Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak dapat diperoleh melalui Website resmi, Sosial Media resmi, atau dapat langsung ke Kantor Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak.',
                    ],
                ],
                'rules' => ['nullable', 'array'],
            ],
            'information_title' => [
                'group' => 'faq', 'type' => 'text',
                'label' => 'Judul Section Informasi Terkini',
                'default' => 'INFORMASI TERKINI',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'information_subtitle' => [
                'group' => 'faq', 'type' => 'text',
                'label' => 'Subjudul Section Informasi Terkini',
                'default' => 'Informasi terbaru dari Dinas Sosial Pemberdayaan Perempuan dan Perlindungan Anak',
                'rules' => ['nullable', 'string', 'max:200'],
            ],
            'information_empty_title' => [
                'group' => 'faq', 'type' => 'text',
                'label' => 'Judul Saat Belum Ada Informasi',
                'default' => 'Belum Ada Informasi',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'information_empty_text' => [
                'group' => 'faq', 'type' => 'text',
                'label' => 'Teks Saat Belum Ada Informasi',
                'default' => 'Informasi terbaru akan segera tersedia.',
                'rules' => ['nullable', 'string', 'max:200'],
            ],

            /* ------------------------------------------------------------
             * KONTAK & NAVIGASI (halaman utama)
             * ---------------------------------------------------------- */
            'contact_title' => [
                'group' => 'beranda', 'type' => 'text',
                'label' => 'Judul Section Kontak',
                'default' => 'CEK KONTAK KAMI',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'contact_subtitle_prefix' => [
                'group' => 'beranda', 'type' => 'text',
                'label' => 'Awalan Subjudul Kontak',
                'hint'  => 'Nama instansi ditambahkan otomatis setelah teks ini.',
                'default' => 'Informasi terkait Kontak',
                'rules' => ['nullable', 'string', 'max:150'],
            ],
            'contact_label_address' => [
                'group' => 'beranda', 'type' => 'text',
                'label' => 'Label Item: Alamat',
                'default' => 'Alamat',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'contact_label_hotline' => [
                'group' => 'beranda', 'type' => 'text',
                'label' => 'Label Item: Hotline',
                'default' => 'Hotline & Telepon',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'contact_label_email' => [
                'group' => 'beranda', 'type' => 'text',
                'label' => 'Label Item: Email',
                'default' => 'Email',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'contact_label_hours' => [
                'group' => 'beranda', 'type' => 'text',
                'label' => 'Label Item: Jam Buka',
                'default' => 'Jam Buka',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'office_hours_day' => [
                'group' => 'beranda', 'type' => 'text',
                'label' => 'Hari Operasional',
                'default' => "Senin - Jum'at",
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'office_hours_time' => [
                'group' => 'beranda', 'type' => 'text',
                'label' => 'Jam Operasional',
                'default' => '07:30 AM - 03:30 PM',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'maps_embed_url' => [
                'group' => 'beranda', 'type' => 'textarea',
                'label' => 'URL Embed Google Maps',
                'hint'  => 'Ambil dari Google Maps → Bagikan → Sematkan peta → salin nilai src pada iframe.',
                'default' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.8438585054073!2d111.898124!3d-7.6013195!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e784b06ef170309%3A0xb23b3f2501c3c28e!2sDinas%20Sosial%20Pemberdayaan%20Perempuan%20dan%20Perlindungan%20Anak!5e0!3m2!1sid!2sid!4v1713866091206!5m2!1sid!2sid',
                'rules' => ['nullable', 'string', 'max:2000'],
            ],
            'nav_label_home' => [
                'group' => 'beranda', 'type' => 'text', 'label' => 'Menu: Beranda', 'default' => 'Beranda',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'nav_label_about' => [
                'group' => 'beranda', 'type' => 'text', 'label' => 'Menu: Tentang Kami', 'default' => 'Tentang Kami',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'nav_label_app' => [
                'group' => 'beranda', 'type' => 'text', 'label' => 'Menu: Aplikasi', 'default' => 'Aplikasi',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'nav_label_faq' => [
                'group' => 'beranda', 'type' => 'text', 'label' => 'Menu: Pertanyaan Umum', 'default' => 'Pertanyaan Umum',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'nav_label_info' => [
                'group' => 'beranda', 'type' => 'text', 'label' => 'Menu: Informasi', 'default' => 'Informasi',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'nav_label_contact' => [
                'group' => 'beranda', 'type' => 'text', 'label' => 'Menu: Kontak', 'default' => 'Kontak',
                'rules' => ['nullable', 'string', 'max:50'],
            ],
            'nav_label_admin' => [
                'group' => 'beranda', 'type' => 'text', 'label' => 'Tombol Masuk Admin', 'default' => 'Admin',
                'rules' => ['nullable', 'string', 'max:50'],
            ],

            /* ------------------------------------------------------------
             * FOOTER & MEDIA SOSIAL
             * ---------------------------------------------------------- */
            'footer_about_text' => [
                'group' => 'footer', 'type' => 'textarea',
                'label' => 'Teks Tentang di Footer',
                'hint'  => 'Kosongkan untuk memakai Deskripsi Ringkas Layanan.',
                'default' => '',
                'rules' => ['nullable', 'string', 'max:500'],
            ],
            'footer_links_title' => [
                'group' => 'footer', 'type' => 'text',
                'label' => 'Judul Kolom Tautan',
                'default' => 'Useful Links',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'footer_links' => [
                'group' => 'footer', 'type' => 'json',
                'label' => 'Tautan Navigasi Footer',
                'default' => [
                    ['label' => 'Beranda', 'href' => '#hero'],
                    ['label' => 'Tentang Kami', 'href' => '#about'],
                    ['label' => 'Aplikasi', 'href' => '#swipper'],
                    ['label' => 'Pertanyaan Umum', 'href' => '#faq'],
                    ['label' => 'Kontak', 'href' => '#contact'],
                ],
                'rules' => ['nullable', 'array'],
            ],
            'footer_services_title' => [
                'group' => 'footer', 'type' => 'text',
                'label' => 'Judul Kolom Layanan',
                'default' => 'Layanan Kami',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'footer_services' => [
                'group' => 'footer', 'type' => 'json',
                'label' => 'Tautan Layanan Footer',
                'default' => [
                    ['label' => 'Layanan Pemberdayaan Perempuan', 'href' => '/layanan/perempuan'],
                    ['label' => 'Layanan Perlindungan Anak', 'href' => '/layanan/anak'],
                    ['label' => 'Layanan Sosial dan Bantuan Kesejahteraan', 'href' => '/layanan/kesejahteraan'],
                    ['label' => 'Layanan Konsultasi dan Edukasi', 'href' => '/layanan/edukasi'],
                ],
                'rules' => ['nullable', 'array'],
            ],
            'footer_social_title' => [
                'group' => 'footer', 'type' => 'text',
                'label' => 'Judul Kolom Media Sosial',
                'default' => 'Sosial Media Kami',
                'rules' => ['nullable', 'string', 'max:100'],
            ],
            'footer_social_text' => [
                'group' => 'footer', 'type' => 'text',
                'label' => 'Teks Kolom Media Sosial',
                'default' => 'Follow Media Sosial untuk mendapatkan informasi terbaru kami.',
                'rules' => ['nullable', 'string', 'max:200'],
            ],
            'instagram_url' => [
                'group' => 'footer', 'type' => 'text',
                'label' => 'URL Instagram',
                'hint'  => 'Kosongkan bila tidak ingin menampilkan ikon Instagram.',
                'default' => 'https://www.instagram.com/dinsosp3anganjuk',
                'rules' => ['nullable', 'string', 'max:300'],
            ],
            'tiktok_url' => [
                'group' => 'footer', 'type' => 'text',
                'label' => 'URL TikTok',
                'hint'  => 'Kosongkan bila tidak ingin menampilkan ikon TikTok.',
                'default' => 'https://www.tiktok.com/@dinsosp3a_nganjuk',
                'rules' => ['nullable', 'string', 'max:300'],
            ],

            /* ------------------------------------------------------------
             * LAPORAN & SLA (sudah ada sebelumnya)
             * ---------------------------------------------------------- */
            'report_sla_days' => [
                'group' => 'laporan', 'type' => 'number',
                'label' => 'Target SLA Respon Laporan (Hari)',
                'default' => '3',
                'rules' => ['required', 'integer', 'min:1', 'max:30'],
            ],
            'auto_archive_months' => [
                'group' => 'laporan', 'type' => 'number',
                'label' => 'Periode Retensi Arsip (Bulan)',
                'default' => '24',
                'rules' => ['nullable', 'integer', 'min:1', 'max:120'],
            ],
            'enable_chat_consultation' => [
                'group' => 'laporan', 'type' => 'select',
                'label' => 'Layanan Konsultasi Chat',
                'default' => '1',
                'options' => ['1' => 'Aktif', '0' => 'Nonaktif Sementara'],
                'rules' => ['required', 'in:0,1'],
            ],

            /* ------------------------------------------------------------
             * KEAMANAN & SESI (sudah ada sebelumnya)
             * ---------------------------------------------------------- */
            'session_lifetime_minutes' => [
                'group' => 'keamanan', 'type' => 'number',
                'label' => 'Durasi Timeout Sesi (Menit)',
                'default' => '120',
                'rules' => ['required', 'integer', 'min:15', 'max:1440'],
            ],
            'max_login_attempts' => [
                'group' => 'keamanan', 'type' => 'number',
                'label' => 'Maksimal Percobaan Login Salah',
                'default' => '5',
                'rules' => ['required', 'integer', 'min:3', 'max:20'],
            ],
            'otp_expiry_minutes' => [
                'group' => 'keamanan', 'type' => 'number',
                'label' => 'Masa Berlaku OTP (Menit)',
                'default' => '5',
                'rules' => ['required', 'integer', 'min:1', 'max:60'],
            ],
        ];
    }

    public static function keys(): array
    {
        return array_keys(static::fields());
    }

    public static function fileKeys(): array
    {
        return array_keys(array_filter(static::fields(), fn ($f) => ($f['type'] ?? 'text') === 'file'));
    }

    public static function jsonKeys(): array
    {
        return array_keys(array_filter(static::fields(), fn ($f) => ($f['type'] ?? 'text') === 'json'));
    }

    public static function default(string $key, mixed $fallback = null): mixed
    {
        return static::fields()[$key]['default'] ?? $fallback;
    }

    /**
     * Aturan validasi untuk seluruh field teks/angka + upload tunggal.
     */
    public static function rules(): array
    {
        $rules = [];

        foreach (static::fields() as $key => $field) {
            if ($field['type'] === 'json') {
                $rules[$key] = $field['rules'] ?? ['nullable', 'array'];
                continue;
            }

            $rules[$key] = $field['rules'] ?? ['nullable', 'string', 'max:255'];
        }

        return $rules;
    }

    /**
     * Data awal untuk seeder: key => atribut baris system_settings.
     */
    public static function seedRows(): array
    {
        $rows = [];

        foreach (static::fields() as $key => $field) {
            $default = $field['default'] ?? null;

            if ($field['type'] === 'json') {
                $default = json_encode($default, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            $rows[] = [
                'key'   => $key,
                'value' => $default,
                'group' => $field['group'] ?? 'general',
                'label' => $field['label'] ?? ucfirst(str_replace('_', ' ', $key)),
                'type'  => $field['type'] ?? 'text',
            ];
        }

        return $rows;
    }
}
