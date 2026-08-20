<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Superadmin Konsol - {{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}</title>
    <!-- Favicon icon -->
    @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($appLogo) }}">
    @else
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/assets_2/images/logo-sentra.png') }}">
    @endif
    <link href="{{ asset('admin/assets_2/css/style.css') }}" rel="stylesheet">
    <!-- DataTables + Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Font: Plus Jakarta Sans (Modern Fintech / Clean SaaS) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.min.css">

    <!-- ORIPIO-INSPIRED MODERN CLEAN GREEN THEME -->
    <style>
        :root {
            --oripio-bg: #F5F7FA;
            --oripio-card: #FFFFFF;
            --oripio-primary: #006039;
            --oripio-primary-hover: #004d2e;
            --oripio-mint: #10B981;
            --oripio-mint-light: #E8F7EE;
            --oripio-mint-subtle: #DCFCE7;
            --oripio-text-main: #111827;
            --oripio-text-muted: #64748B;
            --oripio-border: #EBECEF;
            --oripio-border-subtle: #F1F3F5;
            --oripio-radius: 20px;
            --oripio-radius-sm: 12px;
            --oripio-pill: 9999px;
            --oripio-shadow: 0 2px 14px rgba(16, 24, 40, 0.03);
            --oripio-shadow-hover: 0 8px 24px rgba(16, 24, 40, 0.06);
            --oripio-sidebar: 260px;
            --oripio-header: 76px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif !important;
            background-color: var(--oripio-bg) !important;
            color: var(--oripio-text-main) !important;
            letter-spacing: -0.01em;
        }

        /* HEADER & NAVBAR */
        .header {
            background: #FFFFFF !important;
            border-bottom: 1px solid var(--oripio-border) !important;
            height: var(--oripio-header) !important;
            padding-left: var(--oripio-sidebar);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
        }

        .nav-header {
            background: #FFFFFF !important;
            border-bottom: 1px solid var(--oripio-border) !important;
            border-right: 1px solid var(--oripio-border) !important;
            height: var(--oripio-header) !important;
            width: var(--oripio-sidebar);
        }

        /* BRAND SIDEBAR: logo + nama aplikasi mengisi header.
           Memakai !important agar tidak bisa ditimpa CSS template
           (template membatasi .brand-title dengan max-width: 150px). */
        .nav-header .brand-title {
            max-width: none !important;
            margin-left: 14px !important;
        }

        .nav-header .brand-title span {
            font-size: 1.5rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.05em !important;
            line-height: 1.1 !important;
        }

        /* MASK REVEAL pada nama aplikasi ("tirai" tersingkap kiri→kanan).
           Hanya `clip-path` + `opacity` — tanpa split huruf, tanpa kursor.
           Kelas `.brand-unveil` dipasang HANYA pada halaman pertama setelah
           login (navbar memakai session()->pull), jadi perpindahan halaman
           berikutnya tidak menjalankan animasi apa pun. */
        @keyframes brand-unveil {
            from {
                clip-path: inset(0 100% 0 0);
                opacity: 0.35;
            }
            to {
                clip-path: inset(0 0 0 0);
                opacity: 1;
            }
        }

        .nav-header .brand-title .brand-unveil {
            animation: brand-unveil 1000ms cubic-bezier(0.4, 0, 0.2, 1) backwards;
        }

        /* SIDEBAR MODERN LIGHT */
        .quixnav {
            background: #FFFFFF !important;
            border-right: 1px solid var(--oripio-border) !important;
            width: var(--oripio-sidebar);
            top: var(--oripio-header) !important;
            padding-top: 15px;
            box-shadow: none !important;
        }

        /* SIDEBAR MENU — satu geometri untuk ikon & teks.
           Semua item memakai struktur sama (<i> + <span.nav-text> [+ badge])
           dengan `gap` tetap. Prefix #main-wrapper dipakai agar seluruh
           aturan di bawah konsisten menang atas CSS tema (style.css), yang
           memberi item aktif padding/margin/radius berbeda. */
        #main-wrapper .quixnav .metismenu {
            padding: 10px 12px !important;
        }

        #main-wrapper .quixnav .metismenu > li > a {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 12px !important;
            padding: 11px 14px !important;
            margin: 0 0 4px 0 !important;
            border-radius: var(--oripio-radius-sm) !important;
            color: #475569 !important;
            font-size: 0.92rem !important;
            font-weight: 500 !important;
            line-height: 1.35 !important;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        /* Kotak ikon 20x20, glyph selalu terpusat lewat flex-center sehingga
           posisi ikon tidak bergantung lebar glyph Font Awesome. Tema memberi
           ikon `padding: 0 7px; top: -2px` (style.css:10322) — dinetralkan. */
        #main-wrapper .quixnav .metismenu > li > a > i {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex: 0 0 20px !important;
            width: 20px !important;
            height: 20px !important;
            padding: 0 !important;
            margin: 0 !important;
            position: static !important;
            top: auto !important;
            font-size: 1rem !important;
            line-height: 1 !important;
            text-align: center !important;
            color: #94A3B8 !important;
            transition: color 0.2s ease;
        }

        /* Teks mengisi sisa ruang → awal teks selalu x = 14 + 20 + 12 = 46px. */
        #main-wrapper .quixnav .metismenu > li > a .nav-text {
            flex: 1 1 auto !important;
            min-width: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            color: inherit !important;
        }

        /* Badge angka menempel kanan dengan jarak tetap 18px dari tepi pil,
           sama untuk item bercaret (has-arrow) maupun tidak, agar kolom
           angka rata kanan. */
        #main-wrapper .quixnav .metismenu > li > a .badge {
            flex: 0 0 auto !important;
            margin: 0 18px 0 auto !important;
        }

        /* State hover: geometri identik dengan state normal. */
        #main-wrapper .quixnav .metismenu > li:hover > a,
        #main-wrapper .quixnav .metismenu > li:focus > a {
            background-color: #F8FAFC !important;
            color: var(--oripio-text-main) !important;
            padding: 11px 14px !important;
            margin-right: 0 !important;
            border-radius: var(--oripio-radius-sm) !important;
        }

        #main-wrapper .quixnav .metismenu > li:hover > a i {
            color: var(--oripio-primary) !important;
        }

        /* State aktif: padding & margin disamakan (tema memakai 12px 18px
           dan margin-right 8px, penyebab ikon menu aktif bergeser 4px). */
        #main-wrapper .quixnav .metismenu > li.mm-active > a {
            background-color: var(--oripio-mint-light) !important;
            color: var(--oripio-primary) !important;
            font-weight: 700 !important;
            padding: 11px 14px !important;
            margin-right: 0 !important;
            border-radius: var(--oripio-radius-sm) !important;
        }

        #main-wrapper .quixnav .metismenu > li.mm-active > a i,
        #main-wrapper .quixnav .metismenu > li.mm-active > a .nav-text {
            color: var(--oripio-primary) !important;
        }

        /* Sub-menu (menu anak) — submenu Audit Aktivitas Sistem.
           Override tema bawaan (sidebar gelap) agar cocok dengan sidebar
           putih oripio: teks slate, hover mint lembut, aktif hijau primer. */
        .quixnav .metismenu ul {
            background: transparent !important;
            padding: 0 0 6px !important;
        }

        /* Template asli mengecat ul sub-menu hijau pekat (#059652) saat
           induknya aktif. Selector persis sama + ditulis setelah CSS
           template = aturan ini yang menang. */
        .quixnav .metismenu > li.mm-active ul,
        .quixnav .metismenu > li.mm-active ul ul {
            background: transparent !important;
        }

        .quixnav .metismenu ul li a {
            display: block !important;
            color: #64748B !important;
            font-size: 0.85rem !important;
            font-weight: 500 !important;
            /* 46px = padding 14 + kolom ikon 20 + gap 12 → sejajar teks induk */
            padding: 9px 14px 9px 46px !important;
            border-radius: var(--oripio-radius-sm) !important;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        .quixnav .metismenu ul li a:hover {
            background-color: #F8FAFC !important;
            color: var(--oripio-primary) !important;
        }

        .quixnav .metismenu ul li a.mm-active {
            color: var(--oripio-primary) !important;
            font-weight: 600 !important;
            background-color: var(--oripio-mint-light) !important;
        }

        .quixnav .metismenu li > a.has-arrow::after {
            color: #94A3B8;
        }

        /* Label seksi (MAIN MENU / GENERAL) sejajar dengan awal teks menu:
           12 (padding metismenu) + 46 (14 padding + 20 ikon + 12 gap). */
        .sidebar-section-label {
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            font-weight: 700 !important;
            color: #94A3B8 !important;
            padding: 16px 12px 6px 46px !important;
            list-style: none;
        }

        /* LAYOUT SHELL
           Header & sidebar memakai position: fixed dari theme, jadi wrapper
           perlu padding-top agar judul halaman tidak tertutup topbar. */
        #main-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: var(--oripio-header);
        }

        /* CONTENT BODY */
        .content-body {
            background-color: var(--oripio-bg) !important;
            margin-left: var(--oripio-sidebar);
            flex: 1 0 auto;
            min-height: 0;
            padding-top: 24px !important;
            padding-bottom: 40px !important;
        }

        .footer {
            padding-left: var(--oripio-sidebar);
            flex-shrink: 0;
        }

        /* ============================================================
           SIDEBAR COLLAPSE (dipicu oleh class menu-toggle pada
           #main-wrapper melalui tombol hamburger)
           ============================================================ */
        #main-wrapper.menu-toggle .nav-header .brand-logo {
            padding-left: 0 !important;
            padding-right: 0 !important;
            justify-content: center !important;
        }

        #main-wrapper.menu-toggle .nav-header .brand-title {
            display: none !important;
        }

        #main-wrapper.menu-toggle .nav-header .brand-logo img {
            height: 32px !important;
            width: auto !important;
        }

        /* Saat sidebar benar-benar menyempit (desktop), sembunyikan label,
           badge, dan sesuaikan padding footer. Di mode overlay (mobile)
           class menu-toggle berarti sidebar sedang TERBUKA, jadi isinya
           harus tetap tampil lengkap. */
        @media (min-width: 768px) {
            #main-wrapper.menu-toggle .footer {
                padding-left: 3.75rem;
            }

            #main-wrapper.menu-toggle .content-body {
                margin-left: 3.75rem;
            }

            #main-wrapper.menu-toggle .quixnav .sidebar-section-label {
                display: none !important;
            }

            #main-wrapper.menu-toggle .quixnav .metismenu > li > a .badge {
                display: none !important;
            }

            /* ---- Mode ikon-only (sidebar tertutup, desktop) ----
               Rail selebar 3.75rem (60px). Padding 8px → pil 44px, dan
               kolom ikon dibuat selebar pil sehingga ikon SELALU tepat
               di tengah. Flyout hover bawaan tema (a melebar 250px)
               dibatalkan agar label tidak menembus area konten; label
               tetap tersedia lewat atribut title (tooltip). */
            /* ---- Mode ikon-only (sidebar tertutup, desktop) ----
               Rail 3.75rem (60px). Padding 8px → pil 44px; kolom ikon dibuat
               selebar pil sehingga ikon SELALU tepat di tengah. Flyout hover
               bawaan tema (a melebar 250px) dibatalkan agar label tidak
               menembus area konten; label tetap ada lewat atribut title. */
            #main-wrapper.menu-toggle .quixnav .metismenu {
                padding: 10px 8px !important;
            }

            #main-wrapper.menu-toggle .quixnav .metismenu > li > a,
            #main-wrapper.menu-toggle .quixnav .metismenu > li:hover > a,
            #main-wrapper.menu-toggle .quixnav .metismenu > li.mm-active > a {
                width: auto !important;
                padding: 11px 0 !important;
                margin-right: 0 !important;
                gap: 0 !important;
                justify-content: center !important;
            }

            /* Ikon mengisi lebar pil (44px) → titik tengah ikon berimpit
               dengan titik tengah pil, apa pun lebar glyph-nya. */
            #main-wrapper.menu-toggle .quixnav .metismenu > li > a > i,
            #main-wrapper.menu-toggle .quixnav .metismenu > li:hover > a > i,
            #main-wrapper.menu-toggle .quixnav .metismenu > li.mm-active > a > i {
                flex: 0 0 44px !important;
                width: 44px !important;
                min-width: 44px !important;
                height: 22px !important;
                margin: 0 !important;
                font-size: 1.05rem !important;
            }

            #main-wrapper.menu-toggle .quixnav .metismenu > li > a .nav-text,
            #main-wrapper.menu-toggle .quixnav .metismenu > li > a .badge {
                display: none !important;
            }

            /* Mode ikon-only: sub-menu ikut disembunyikan agar tidak
               menembus area konten. */
            #main-wrapper.menu-toggle .quixnav .metismenu ul {
                display: none !important;
            }
        }

        /* CARDS */
        .card {
            background: var(--oripio-card) !important;
            border: 1px solid var(--oripio-border) !important;
            border-radius: var(--oripio-radius) !important;
            box-shadow: var(--oripio-shadow) !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: var(--oripio-shadow-hover) !important;
            transform: translateY(-2px);
        }

        .card-header {
            background: transparent !important;
            border-bottom: 1px solid var(--oripio-border-subtle) !important;
            padding: 20px 24px !important;
        }

        .card-body {
            padding: 24px !important;
        }

        /* ============================================================
           MOTION SYSTEM — mikro-animasi elegan & hemat performa.
           Aturan: hanya `transform` dan `opacity` yang dianimasikan
           (tidak pernah width/height/top/left) agar tidak memicu
           layout ulang. Semua memakai fill-mode `backwards` supaya
           transform hover/press tetap berfungsi setelah animasi usai.
           ============================================================ */
        @keyframes fade-rise {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fade-in {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes grow-bar {
            from { transform: scaleY(0); }
            to { transform: scaleY(1); }
        }

        @keyframes pop-in {
            0% { transform: scale(1); }
            45% { transform: scale(1.15); }
            100% { transform: scale(1); }
        }

        @keyframes slide-down {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes menu-in {
            from { opacity: 0; transform: translateY(-6px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* [1] PAGE ENTER — konten masuk halus, replay tiap navigasi */
        .content-body > .container-fluid {
            animation: fade-rise 400ms cubic-bezier(0.22, 1, 0.36, 1) backwards;
        }

        /* [2][5] REVEAL — jeda berjenjang diambil dari custom property --d */
        [data-reveal] {
            animation: fade-rise 400ms cubic-bezier(0.22, 1, 0.36, 1) backwards;
            animation-delay: var(--d, 0ms);
        }

        /* [4] BAR CHART GROW — scaleY dari dasar, bukan animasi height */
        [data-grow-bar] {
            transform-origin: bottom center;
            animation: grow-bar 500ms cubic-bezier(0.22, 1, 0.36, 1) backwards;
            animation-delay: var(--d, 0ms);
        }

        /* [6] ROW FADE — hanya saat load awal halaman. Setelah body
           mendapat class `page-settled` (600ms), live filter menukar
           region tanpa animasi agar tabel tidak berkedip. */
        body:not(.page-settled) [data-live-region] table > tbody > tr {
            animation: fade-in 250ms ease-out backwards;
        }

        /* [5] STAGGER BARIS AKTIVITAS TERKINI — maks 5 item, 40ms */
        .table-activity > tbody > tr {
            animation: fade-rise 250ms ease-out backwards;
        }

        .table-activity > tbody > tr:nth-child(2) { animation-delay: 40ms; }
        .table-activity > tbody > tr:nth-child(3) { animation-delay: 80ms; }
        .table-activity > tbody > tr:nth-child(4) { animation-delay: 120ms; }
        .table-activity > tbody > tr:nth-child(5) { animation-delay: 160ms; }

        /* [8] BUTTON PRESS — specificity `body .btn` agar menang atas
           .btn-oripio-primary:hover (translateY) yang muncul lebih bawah */
        body .btn:active,
        body .btn-pill:active {
            transform: scale(0.97);
            transition-duration: 120ms;
        }

        /* [9] DROPDOWN PROFIL — fade + scale dari sudut kanan atas */
        .header-profile .dropdown-menu.show {
            transform-origin: top right;
            animation: menu-in 180ms ease-out backwards;
        }

        /* [10] ALERT MASUK */
        .app-alert {
            animation: slide-down 300ms ease-out backwards;
        }

        /* [12] BADGE SIDEBAR POP */
        .quixnav .metismenu .badge {
            animation: pop-in 300ms ease-out backwards;
        }

        /* KILL-SWITCH — hormati preferensi "reduce motion" pengguna */
        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                animation-duration: 0.001ms !important;
                transition-duration: 0.001ms !important;
            }
        }

        /* BUTTONS & PILLS */
        .btn-pill {
            border-radius: var(--oripio-pill) !important;
            padding: 8px 20px !important;
            font-weight: 600 !important;
            font-size: 0.88rem !important;
            letter-spacing: -0.01em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .btn-oripio-primary {
            background-color: var(--oripio-primary) !important;
            color: #FFFFFF !important;
            border: none !important;
        }

        .btn-oripio-primary:hover {
            background-color: var(--oripio-primary-hover) !important;
            color: #FFFFFF !important;
            transform: translateY(-1px);
        }

        .btn-oripio-light {
            background-color: #F1F5F9 !important;
            color: var(--oripio-text-main) !important;
            border: 1px solid #E2E8F0 !important;
        }

        .btn-oripio-light:hover {
            background-color: #E2E8F0 !important;
            color: var(--oripio-text-main) !important;
        }

        .btn-oripio-dark {
            background-color: #0F172A !important;
            color: #FFFFFF !important;
            border: none !important;
        }

        .btn-oripio-dark:hover {
            background-color: #1E293B !important;
            color: #FFFFFF !important;
            transform: translateY(-1px);
        }

        /* BADGES */
        .badge-pill-soft {
            border-radius: var(--oripio-pill) !important;
            padding: 6px 14px !important;
            font-weight: 600 !important;
            font-size: 0.78rem !important;
            display: inline-flex;
            align-items: center;
        }

        .badge-soft-green {
            background-color: #DCFCE7 !important;
            color: #059669 !important;
        }

        .badge-soft-rose {
            background-color: #FEE2E2 !important;
            color: #DC2626 !important;
        }

        .badge-soft-blue {
            background-color: #E0F2FE !important;
            color: #0284C7 !important;
        }

        .badge-soft-amber {
            background-color: #FEF3C7 !important;
            color: #D97706 !important;
        }

        .badge-soft-gray {
            background-color: #F1F5F9 !important;
            color: #64748B !important;
        }

        /* MODERN SEARCH BAR */
        .search-pill-wrapper {
            background-color: #F1F5F9;
            border-radius: var(--oripio-pill);
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid #E2E8F0;
            width: 320px;
            max-width: 100%;
            transition: all 0.2s ease;
        }

        .search-pill-wrapper > i {
            flex: 0 0 auto;
            line-height: 1;
        }

        .search-pill-wrapper:focus-within {
            background-color: #FFFFFF;
            border-color: var(--oripio-mint);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .search-pill-wrapper input {
            flex: 1 1 auto;
            min-width: 0;
            width: auto;
            border: none;
            background: transparent;
            outline: none;
            font-size: 0.88rem;
            color: var(--oripio-text-main);
            margin-left: 0;
            padding: 0;
            line-height: 1.4;
        }

        .search-pill-wrapper input::placeholder {
            color: #94A3B8;
            opacity: 1;
        }

        .kbd-shortcut {
            flex: 0 0 auto;
            background: #FFFFFF;
            border: 1px solid #CBD5E1;
            border-radius: 6px;
            padding: 3px 7px;
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1;
            color: #64748B;
            white-space: nowrap;
        }

        /* ICON BUTTONS */
        .btn-circle-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            transition: all 0.2s ease;
        }

        .btn-circle-action:hover {
            background: #F8FAFC;
            color: var(--oripio-primary);
            border-color: #CBD5E1;
        }

        /* DATA TABLE REFINEMENT */
        .table thead th {
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            font-weight: 700 !important;
            color: #94A3B8 !important;
            border-bottom: 1px solid var(--oripio-border) !important;
            padding: 14px 12px !important;
            background: #FAFBFD !important;
            white-space: nowrap !important;
        }

        .table tbody td {
            padding: 14px 12px !important;
            vertical-align: middle !important;
            border-bottom: 1px solid var(--oripio-border-subtle) !important;
            font-size: 0.88rem !important;
        }

        .table tbody td code,
        .table tbody td .btn,
        .table tbody td .badge,
        .table tbody td .badge-pill-soft {
            white-space: nowrap;
        }

        /* Tabel aktivitas dashboard punya ruang lebih sempit, jadi padding
           dan badge dibuat lebih ringkas agar tidak melebar keluar kartu. */
        .table-activity thead th {
            padding: 10px 8px !important;
            font-size: 0.68rem !important;
            white-space: normal !important;
        }

        .table-activity tbody td {
            padding: 10px 8px !important;
        }

        .table-activity tbody td .badge {
            padding: 4px 10px !important;
            font-size: 0.68rem !important;
        }

        /* Di layar sempit kolom tabel tidak boleh dipaksa menyempit sampai
           teksnya bertumpuk per kata. Biarkan baris tetap satu baris dan
           andalkan .table-responsive untuk scroll horizontal. */
        @media (max-width: 767.98px) {
            .table-responsive .table tbody td {
                white-space: nowrap !important;
            }
        }

        .table-hover tbody tr:hover {
            background-color: #F8FAFC !important;
        }

        .table-bordered {
            border: 0 !important;
        }

        .table-bordered th,
        .table-bordered td {
            border-left: 0 !important;
            border-right: 0 !important;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #FAFBFD !important;
        }

        .thead-primary th {
            background: #F8FAFC !important;
            color: #64748B !important;
        }

        /* ============================================================
           BOOTSTRAP 5 UTILITY COMPATIBILITY
           The admin theme is built on Bootstrap 4, while the newer
           superadmin screens use Bootstrap 5 spacing/typography helpers.
           These small shims keep every page visually consistent.
           ============================================================ */
        .fw-normal { font-weight: 400 !important; }
        .fw-semibold { font-weight: 600 !important; }
        .fw-bold { font-weight: 700 !important; }
        .fw-bolder { font-weight: 800 !important; }

        .fs-5 { font-size: 1.15rem !important; }
        .fs-6 { font-size: 0.95rem !important; }
        .fs-12 { font-size: 12px !important; }

        .text-start { text-align: left !important; }
        .text-end { text-align: right !important; }
        .opacity-50 { opacity: 0.5 !important; }

        .rounded-4 { border-radius: 1rem !important; }
        .rounded-pill { border-radius: 9999px !important; }

        .me-0 { margin-right: 0 !important; }
        .me-1 { margin-right: 0.25rem !important; }
        .me-2 { margin-right: 0.5rem !important; }
        .me-3 { margin-right: 1rem !important; }
        .me-4 { margin-right: 1.5rem !important; }
        .ms-1 { margin-left: 0.25rem !important; }
        .ms-2 { margin-left: 0.5rem !important; }
        .ms-3 { margin-left: 1rem !important; }

        .gap-1 { gap: 0.25rem !important; }
        .gap-2 { gap: 0.5rem !important; }
        .gap-3 { gap: 1rem !important; }

        .row.g-2 { margin-left: -0.5rem; margin-right: -0.5rem; }
        .row.g-2 > [class*="col-"] { padding-left: 0.5rem; padding-right: 0.5rem; }
        .row.g-3 { margin-left: -0.75rem; margin-right: -0.75rem; }
        .row.g-3 > [class*="col-"] { padding-left: 0.75rem; padding-right: 0.75rem; }
        .row.g-4 { margin-left: -1rem; margin-right: -1rem; }
        .row.g-4 > [class*="col-"] { padding-left: 1rem; padding-right: 1rem; }

        .top-0 { top: 0 !important; }
        .bottom-0 { bottom: 0 !important; }
        .start-100 { left: 100% !important; }
        .end-0 { right: 0 !important; }
        .translate-middle { transform: translate(-50%, -50%) !important; }

        /* ============================================================
           PAGE HEADER
           ============================================================ */
        .page-head {
            gap: 16px;
        }

        .page-head-icon {
            width: 48px;
            height: 48px;
            flex: 0 0 48px;
            margin-right: 14px;
            border-radius: 14px;
            background: var(--oripio-mint-light);
            color: var(--oripio-primary);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }

        .page-head-title {
            margin: 0 0 3px;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.03em;
            color: var(--oripio-text-main);
        }

        .page-head-subtitle {
            font-size: 0.9rem;
            color: var(--oripio-text-muted);
            line-height: 1.5;
        }

        /* ============================================================
           FORMS
           ============================================================ */
        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-control,
        .form-select,
        .custom-select {
            min-height: 44px;
            padding: 10px 14px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background-color: #FFFFFF;
            color: var(--oripio-text-main);
            font-size: 0.88rem;
            box-shadow: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus,
        .custom-select:focus {
            border-color: var(--oripio-mint);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.12);
        }

        .form-control::placeholder {
            color: #94A3B8;
        }

        textarea.form-control {
            min-height: 96px;
            resize: vertical;
        }

        .form-control:disabled,
        .form-control[readonly] {
            background-color: #F8FAFC;
            color: #64748B;
        }

        .form-group label,
        .form-label {
            margin-bottom: 6px;
            font-size: 0.84rem;
            font-weight: 700;
            color: #334155;
        }

        .form-text,
        small.text-muted {
            font-size: 0.78rem;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            border-color: #CBD5E1;
        }

        .form-check-input:checked {
            background-color: var(--oripio-primary);
            border-color: var(--oripio-primary);
        }

        .form-check-label {
            padding-left: 4px;
            font-size: 0.86rem;
            color: #334155;
        }

        .input-group-text {
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            background: #F8FAFC;
            color: #64748B;
        }

        /* ============================================================
           BUTTONS
           ============================================================ */
        .btn {
            border-radius: 10px;
            font-size: 0.87rem;
            font-weight: 600;
            padding: 9px 16px;
            transition: all 0.2s ease;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8rem;
        }

        .btn-primary {
            background-color: var(--oripio-primary) !important;
            border-color: var(--oripio-primary) !important;
            color: #FFFFFF !important;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background-color: var(--oripio-primary-hover) !important;
            border-color: var(--oripio-primary-hover) !important;
        }

        .btn-outline-primary {
            color: var(--oripio-primary) !important;
            border-color: var(--oripio-primary) !important;
        }

        .btn-outline-primary:hover,
        .btn-outline-primary:focus {
            background-color: var(--oripio-primary) !important;
            border-color: var(--oripio-primary) !important;
            color: #FFFFFF !important;
        }

        .btn-secondary {
            background-color: #F1F5F9 !important;
            border-color: #E2E8F0 !important;
            color: #475569 !important;
        }

        .btn-secondary:hover,
        .btn-secondary:focus {
            background-color: #E2E8F0 !important;
            border-color: #CBD5E1 !important;
            color: #334155 !important;
        }

        .btn-outline-secondary {
            color: #475569 !important;
            border-color: #E2E8F0 !important;
        }

        .btn-outline-secondary:hover,
        .btn-outline-secondary:focus {
            background-color: #F1F5F9 !important;
            border-color: #CBD5E1 !important;
            color: #334155 !important;
        }

        .btn-info {
            background-color: #0284C7 !important;
            border-color: #0284C7 !important;
            color: #FFFFFF !important;
        }

        .btn-info:hover,
        .btn-info:focus {
            background-color: #0369A1 !important;
            border-color: #0369A1 !important;
        }

        .btn-outline-info {
            color: #0284C7 !important;
            border-color: #BAE6FD !important;
        }

        .btn-outline-info:hover,
        .btn-outline-info:focus {
            background-color: #E0F2FE !important;
            color: #0369A1 !important;
        }

        .btn-danger {
            background-color: #DC2626 !important;
            border-color: #DC2626 !important;
        }

        .btn-outline-danger {
            color: #DC2626 !important;
            border-color: #FECACA !important;
        }

        .btn-outline-danger:hover,
        .btn-outline-danger:focus {
            background-color: #FEE2E2 !important;
            color: #B91C1C !important;
        }

        /* ============================================================
           BADGES
           ============================================================ */
        .badge {
            border-radius: var(--oripio-pill);
            padding: 6px 12px;
            font-size: 0.73rem;
            font-weight: 600;
            letter-spacing: 0;
        }

        .badge-primary {
            background-color: #E0F2FE !important;
            color: #0369A1 !important;
        }

        .badge-success {
            background-color: #DCFCE7 !important;
            color: #059669 !important;
        }

        .badge-warning {
            background-color: #FEF3C7 !important;
            color: #B45309 !important;
        }

        .badge-info {
            background-color: #E0F2FE !important;
            color: #0369A1 !important;
        }

        .badge-secondary {
            background-color: #F1F5F9 !important;
            color: #64748B !important;
        }

        .badge-light {
            background-color: #F8FAFC !important;
            color: #475569 !important;
        }

        .badge-outline-primary {
            background-color: transparent !important;
            color: var(--oripio-primary) !important;
            border: 1px solid #A7F3D0;
        }

        .badge-lg {
            padding: 9px 16px;
            font-size: 0.82rem;
        }

        /* ============================================================
           PAGINATION
           ============================================================ */
        .pagination {
            margin-bottom: 0;
            flex-wrap: wrap;
        }

        .pagination .page-link {
            margin: 0 3px;
            padding: 8px 13px;
            border: 1px solid #E2E8F0;
            border-radius: 10px !important;
            color: #475569;
            font-size: 0.83rem;
            font-weight: 600;
            background: #FFFFFF;
        }

        .pagination .page-link:hover {
            background: #F8FAFC;
            border-color: #CBD5E1;
            color: var(--oripio-primary);
        }

        .pagination .page-item.active .page-link {
            background: var(--oripio-primary);
            border-color: var(--oripio-primary);
            color: #FFFFFF;
        }

        .pagination .page-item.disabled .page-link {
            color: #CBD5E1;
            background: #FFFFFF;
            border-color: #F1F5F9;
        }

        /* ============================================================
           NAV TABS
           ============================================================ */
        .nav-tabs {
            border-bottom: 1px solid var(--oripio-border);
            gap: 4px;
        }

        .nav-tabs .nav-link {
            padding: 10px 16px;
            border: 0;
            border-radius: 10px;
            color: #64748B;
            font-size: 0.86rem;
            font-weight: 600;
        }

        .nav-tabs .nav-link:hover {
            background: #F8FAFC;
            color: var(--oripio-text-main);
        }

        .nav-tabs .nav-link.active {
            background: var(--oripio-mint-light);
            color: var(--oripio-primary);
        }

        /* ============================================================
           ALERTS, MODALS & DROPDOWNS
           ============================================================ */
        .app-alert {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            padding: 15px 18px;
            border-radius: 16px;
            font-size: 0.88rem;
        }

        .app-alert-success {
            background: #DCFCE7;
            color: #065F46;
        }

        .app-alert-danger {
            background: #FEE2E2;
            color: #991B1B;
        }

        .app-alert-info {
            background: #E0F2FE;
            color: #075985;
        }

        .app-alert .close {
            color: inherit;
            opacity: 0.65;
            text-shadow: none;
            font-size: 1.25rem;
            line-height: 1;
        }

        .app-alert .close:hover {
            opacity: 1;
        }

        .alert {
            border: 0;
            border-radius: 16px;
            font-size: 0.88rem;
        }

        .alert-info {
            background-color: #E0F2FE;
            color: #075985;
        }

        .alert-danger {
            background-color: #FEE2E2;
            color: #991B1B;
        }

        .modal-content {
            border: 0;
            border-radius: 20px;
        }

        .modal-header,
        .modal-footer {
            border: 0;
        }

        .dropdown-menu {
            padding: 8px;
            border: 1px solid var(--oripio-border);
            border-radius: 14px;
            box-shadow: 0 12px 30px rgba(16, 24, 40, 0.08);
        }

        .dropdown-item {
            border-radius: 9px;
            padding: 9px 12px;
            font-size: 0.86rem;
            font-weight: 500;
        }

        .dropdown-item:hover,
        .dropdown-item:focus {
            background-color: #F8FAFC;
            color: var(--oripio-primary);
        }

        .dropdown-divider {
            border-color: var(--oripio-border-subtle);
        }

        /* ============================================================
           PERMISSION MATRIX / NESTED CARDS
           ============================================================ */
        .perm-card {
            height: auto;
            border: 1px solid var(--oripio-border) !important;
            border-radius: 16px !important;
            box-shadow: none !important;
            overflow: hidden;
        }

        /* Grid kolom untuk kartu permission: setiap kartu mengikuti tinggi
           isinya sendiri sehingga tidak ada ruang kosong di dalam kartu. */
        .perm-grid {
            column-count: 2;
            column-gap: 1.5rem;
        }

        .perm-grid-item {
            break-inside: avoid;
            page-break-inside: avoid;
            margin-bottom: 1.5rem;
        }

        .perm-grid-item:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 991.98px) {
            .perm-grid {
                column-count: 1;
            }
        }

        .perm-card .card-header {
            padding: 14px 18px !important;
            background: #FAFBFD !important;
            border-bottom: 1px solid var(--oripio-border-subtle) !important;
        }

        .perm-card .card-body {
            padding: 16px 18px !important;
        }

        .perm-card .form-check {
            padding: 7px 10px 7px 32px;
            margin-bottom: 2px !important;
            border-radius: 9px;
            transition: background 0.15s ease;
        }

        .perm-card .form-check:hover {
            background: #F8FAFC;
        }

        .perm-card .form-check-input {
            margin-left: -22px;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */
        @media (min-width: 768px) {
            .menu-toggle .footer {
                padding-left: 3.75rem;
            }
        }

        @media (max-width: 991.98px) {
            .content-body {
                margin-left: 0;
            }

            .page-head-title {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 767.98px) {
            .nav-header {
                width: 3.75rem;
            }

            .footer {
                padding-left: 0;
            }

            .page-head-icon {
                width: 42px;
                height: 42px;
                flex-basis: 42px;
                font-size: 1rem;
            }

            .page-head-title {
                font-size: 1.2rem;
            }

            .content-body .container-fluid {
                padding-right: 18px;
                padding-left: 18px;
            }

            .card-body {
                padding: 18px !important;
            }

            .search-pill-wrapper {
                width: 180px;
                padding: 7px 14px;
            }

            .kbd-shortcut {
                display: none;
            }
        }

        @media (max-width: 575.98px) {
            .search-pill-wrapper {
                display: none;
            }

            .header-content {
                padding: 0 14px !important;
            }
        }

        /* ============================================================
           GLOBAL FONT SIZE — SEMUA TEKS SUPERADMIN 12px
           Ikon Font Awesome (<i>) dikecualikan agar tetap proporsional.
           ============================================================ */
        body,
        body *:not(i):not(svg) {
            font-size: 12px !important;
        }

        /* Navigasi Livewire: preloader hanya dipakai pada load pertama. */
        html.superadmin-navigated #preloader {
            display: none !important;
        }

        /* custom.min.js menambahkan class "show" hanya sekali saat window load.
           Karena body diganti oleh Livewire navigate, pastikan wrapper baru
           tetap terlihat (tanpa ini halaman tampak blank karena opacity: 0). */
        html.superadmin-navigated #main-wrapper {
            opacity: 1 !important;
        }
    </style>
    @livewireStyles
</head>

<body>
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">
        @include('backend.superadmin.components.navbar')
        @include('backend.superadmin.components.sidebar')

        <div class="content-body">
            @yield('admin')
        </div>

        @include('backend.superadmin.components.footer')
    </div>

    <!-- SCRIPTS -->
    {{-- Pustaka inti cukup dimuat sekali. Livewire navigate hanya mengganti
         <body>, jadi tanpa data-navigate-once script akan dieksekusi ulang dan
         memicu error "already declared". --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" data-navigate-once></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js" data-navigate-once></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js" data-navigate-once></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js" data-navigate-once></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js" data-navigate-once></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.all.min.js" data-navigate-once></script>
    @stack('scripts')
    <script src="{{ asset('admin/assets_2/vendor/global/global.min.js') }}" data-navigate-once></script>
    {{-- quixnav-init.js sengaja tidak ditandai: ia harus jalan ulang agar atribut
         data-* pada <body> yang baru dipasang kembali oleh quixSettings. --}}
    <script src="{{ asset('admin/assets_2/js/quixnav-init.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/custom.min.js') }}" data-navigate-once></script>

    @livewireScripts

    {{-- Handler persisten (didaftarkan sekali ke document, tidak ikut terhapus
         saat <body> diganti). Menyiapkan ulang komponen tema setelah navigasi. --}}
    <script data-navigate-once>
        (function () {
            // ============ MOTION SYSTEM ============
            // Pelengkap animasi CSS: count-up angka, penanda body.page-settled,
            // dan auto-dismiss alert. Semua idempoten sehingga aman dipanggil
            // berulang (load awal + setiap livewire:navigated).
            var motionSettleTimer = null;

            function motionCountUp(el) {
                if (el.hasAttribute('data-countup-done')) return;
                el.setAttribute('data-countup-done', '1');

                var target = parseInt(el.getAttribute('data-target'), 10);
                if (isNaN(target) || target <= 0) return;

                var duration = 700;
                var startedAt = null;

                function frame(now) {
                    if (startedAt === null) startedAt = now;
                    var progress = Math.min(1, (now - startedAt) / duration);
                    var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
                    el.textContent = Math.round(target * eased).toLocaleString('id-ID');
                    if (progress < 1) window.requestAnimationFrame(frame);
                }

                el.textContent = '0';
                window.requestAnimationFrame(frame);
            }

            function initMotion() {
                // [3] COUNT-UP — mulai sekali saat kartu terlihat
                var counters = document.querySelectorAll('[data-countup]:not([data-countup-done])');

                if (counters.length && 'IntersectionObserver' in window) {
                    var observer = new IntersectionObserver(function (entries) {
                        entries.forEach(function (entry) {
                            if (!entry.isIntersecting) return;
                            motionCountUp(entry.target);
                            observer.unobserve(entry.target);
                        });
                    }, { threshold: 0.25 });

                    counters.forEach(function (counter) { observer.observe(counter); });
                } else {
                    counters.forEach(motionCountUp);
                }

                // [6] Setelah 600ms halaman dianggap "settled": row fade mati
                // agar penukaran region oleh live filter tidak berkedip.
                if (motionSettleTimer) clearTimeout(motionSettleTimer);
                if (document.body) {
                    document.body.classList.remove('page-settled');
                    motionSettleTimer = setTimeout(function () {
                        if (document.body) document.body.classList.add('page-settled');
                    }, 600);
                }

                // [11] AUTO-DISMISS ALERT — memudar setelah 6 detik
                document.querySelectorAll('.app-alert-auto:not([data-dismiss-armed])').forEach(function (alertEl) {
                    alertEl.setAttribute('data-dismiss-armed', '1');

                    setTimeout(function () {
                        if (!alertEl.isConnected) return;
                        alertEl.style.transition = 'opacity 400ms ease, transform 400ms ease';
                        alertEl.style.opacity = '0';
                        alertEl.style.transform = 'translateY(-6px)';
                        setTimeout(function () {
                            if (alertEl.isConnected) alertEl.remove();
                        }, 420);
                    }, 6000);
                });
            }

            function reinitSuperadmin() {
                // Animasi & perilaku halaman dijalankan lebih dulu agar tetap
                // berjalan walau jQuery belum siap.
                initMotion();

                // Tema menyembunyikan #main-wrapper (opacity: 0) sampai class
                // "show" dipasang. Body baru hasil navigasi perlu class ini.
                var wrapperEl = document.getElementById('main-wrapper');
                if (wrapperEl) wrapperEl.classList.add('show');

                var $ = window.jQuery;
                if (!$) return;

                // global.min.js menyimpan referensi lama `var body = $('body')`.
                // Setelah Livewire mengganti <body>, referensi itu menunjuk ke
                // body lama sehingga atribut posisi header/sidebar tidak
                // terpasang pada body baru (navbar profil jadi bergeser).
                // Perbarui referensinya lalu jalankan ulang konfigurasi tema.
                if (window.quixSettings) {
                    window.body = $('body');
                    new window.quixSettings({
                        typography: 'roboto',
                        version: 'light',
                        layout: 'vertical',
                        headerBg: 'color_1',
                        navheaderBg: 'color_1',
                        sidebarBg: 'color_1',
                        sidebarStyle: 'full',
                        sidebarPosition: 'fixed',
                        headerPosition: 'fixed',
                        containerLayout: 'wide',
                        direction: 'ltr'
                    });
                }

                // metisMenu sidebar
                if ($.fn.metisMenu) {
                    $('#menu').metisMenu();
                }

                // Scrollbar sidebar
                if (window.PerfectScrollbar && document.querySelector('.quixnav-scroll')) {
                    new window.PerfectScrollbar('.quixnav-scroll');
                }

                // Tinggi minimum area konten seperti custom.min.js
                var winH = window.outerHeight;
                if (winH > 0) {
                    $('.content-body').css('min-height', (winH + 60) + 'px');
                }

                // Di layar kecil, tutup sidebar setelah pindah halaman
                if (window.innerWidth < 1200) {
                    var wrapper = document.getElementById('main-wrapper');
                    if (wrapper) wrapper.classList.remove('menu-toggle');
                }
            }

            document.addEventListener('livewire:navigated', function () {
                // Preloader tidak perlu tampil lagi setelah navigasi pertama.
                document.documentElement.classList.add('superadmin-navigated');
                var preloader = document.getElementById('preloader');
                if (preloader) preloader.style.display = 'none';

                reinitSuperadmin();
            });

            // ============ LIVE FILTER (tanpa refresh) ============
            // Form filter GET bertanda data-livewire-navigate diperbarui secara
            // parsial: hanya elemen [data-live-region] yang ditukar hasilnya,
            // sementara sidebar, header, dan form filter tidak disentuh —
            // sehingga terasa instan tanpa reload halaman.

            function buildLiveUrl(form) {
                var target = form.getAttribute('action') || window.location.pathname;
                var url = new URL(target, window.location.origin);
                var data = new FormData(form);

                data.forEach(function (value, key) {
                    if (value === '' || value === null) return;
                    url.searchParams.set(key, value);
                });

                return url.pathname + url.search;
            }

            function fallbackLiveNavigate(url) {
                if (window.Livewire && typeof window.Livewire.navigate === 'function') {
                    window.Livewire.navigate(url);
                } else {
                    window.location.href = url;
                }
            }

            var liveRegionToken = 0;

            function partialLoad(url) {
                var token = ++liveRegionToken;

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin'
                })
                    .then(function (response) {
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.text();
                    })
                    .then(function (html) {
                        if (token !== liveRegionToken) return; // respons kedaluwarsa, abaikan

                        var doc = new DOMParser().parseFromString(html, 'text/html');
                        var swapped = 0;

                        document.querySelectorAll('[data-live-region]').forEach(function (el) {
                            var fresh = doc.querySelector('[data-live-region="' + el.getAttribute('data-live-region') + '"]');
                            if (fresh) {
                                el.innerHTML = fresh.innerHTML;
                                swapped++;
                            }
                        });

                        if (!swapped) {
                            fallbackLiveNavigate(url);
                            return;
                        }

                        // Selaraskan URL address bar tanpa menambah riwayat.
                        try { window.history.replaceState(window.history.state, '', url); } catch (e) {}
                    })
                    .catch(function () { fallbackLiveNavigate(url); });
            }

            function pageHasLiveRegions() {
                return !!document.querySelector('[data-live-region]');
            }

            // Submit (tombol Filter / Enter) pada halaman live → update parsial.
            document.addEventListener('submit', function (event) {
                var form = event.target;
                if (!form || !form.hasAttribute('data-livewire-navigate')) return;
                if ((form.getAttribute('method') || 'get').toLowerCase() !== 'get') return;

                var url = buildLiveUrl(form);

                if (pageHasLiveRegions()) {
                    event.preventDefault();
                    partialLoad(url);
                    return;
                }

                // Fallback: navigasi Livewire penuh (soft navigation).
                if (!window.Livewire || typeof window.Livewire.navigate !== 'function') return;
                event.preventDefault();
                window.Livewire.navigate(url);
            });

            // Auto-submit: filter langsung diterapkan saat mengetik/memilih.
            var liveFilterTimers = new WeakMap();
            var lastLiveInputName = null;

            function queueLiveFilterSubmit(form, delay) {
                if (!form || !form.hasAttribute('data-livewire-navigate')) return;
                if ((form.getAttribute('method') || 'get').toLowerCase() !== 'get') return;

                var pending = liveFilterTimers.get(form);
                if (pending) clearTimeout(pending);

                liveFilterTimers.set(form, setTimeout(function () {
                    liveFilterTimers.delete(form);
                    if (!form.isConnected) return;

                    if (pageHasLiveRegions()) {
                        partialLoad(buildLiveUrl(form));
                        return;
                    }

                    // Halaman tanpa area live: submit biasa — interceptor di atas
                    // akan memprosesnya sebagai navigasi Livewire.
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                }, delay));
            }

            document.addEventListener('input', function (event) {
                var el = event.target;
                var form = el && el.closest ? el.closest('form[data-livewire-navigate]') : null;
                if (!form) return;
                if (el.name) lastLiveInputName = el.name;
                queueLiveFilterSubmit(form, 450);
            });

            document.addEventListener('change', function (event) {
                var el = event.target;
                var form = el && el.closest ? el.closest('form[data-livewire-navigate]') : null;
                if (!form) return;
                // Dropdown & tanggal langsung diterapkan; input teks didebounce singkat.
                queueLiveFilterSubmit(form, el.tagName === 'SELECT' || el.type === 'date' ? 0 : 150);
            });

            // Klik pagination di dalam area live → update parsial juga (tanpa reload).
            // Fase capture agar mengungguli listener navigasi Livewire.
            document.addEventListener('click', function (event) {
                if (!event.target || typeof event.target.closest !== 'function') return;
                var link = event.target.closest('[data-live-region] .pagination a[href]');
                if (!link) return;

                event.preventDefault();
                event.stopImmediatePropagation();
                partialLoad(link.href);
            }, true);

            // Setelah fallback navigasi soft, kembalikan fokus ke input pencarian
            // terakhir agar pengguna bisa melanjutkan mengetik.
            document.addEventListener('livewire:navigated', function () {
                if (!lastLiveInputName) return;
                var el = document.querySelector('form[data-livewire-navigate] [name="' + lastLiveInputName + '"]');
                if (el && typeof el.focus === 'function') {
                    el.focus();
                    var len = (el.value || '').length;
                    try { el.setSelectionRange(len, len); } catch (e) { /* input non-teks */ }
                }
                lastLiveInputName = null;
            });

            // ============ TOGGLE SIDEBAR ============
            // custom.min.js sudah memasang handler sendiri pada .nav-control.
            // Dulu kita menambah handler kedua (via jQuery) sehingga satu klik
            // men-toggle DUA kali → sidebar tidak bisa ditutup pada load awal.
            // Sekarang hanya SATU penentu: listener fase-capture di document,
            // yang berjalan lebih dulu lalu menghentikan propagasi agar handler
            // tema (dan handler lain) tidak ikut men-toggle.
            document.addEventListener('click', function (event) {
                if (!event.target || typeof event.target.closest !== 'function') return;

                var control = event.target.closest('.nav-control');
                if (!control) return;

                var wrapper = document.getElementById('main-wrapper');
                if (!wrapper) return;

                event.preventDefault();
                event.stopPropagation();

                wrapper.classList.toggle('menu-toggle');

                document.querySelectorAll('.hamburger').forEach(function (el) {
                    el.classList.toggle('is-active');
                });
            }, true);

            // Load awal: jalankan motion sekali di sini. Navigasi berikutnya
            // ditangani lewat reinitSuperadmin (event livewire:navigated).
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initMotion);
            } else {
                initMotion();
            }
        })();
    </script>
</body>

</html>
