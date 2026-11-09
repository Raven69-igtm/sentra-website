<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Superadmin Konsol - SENTRA</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/assets_2/images/logo-sentra.png') }}">
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

        /* SIDEBAR MODERN LIGHT */
        .quixnav {
            background: #FFFFFF !important;
            border-right: 1px solid var(--oripio-border) !important;
            width: var(--oripio-sidebar);
            top: var(--oripio-header) !important;
            padding-top: 15px;
            box-shadow: none !important;
        }

        .quixnav .metismenu {
            padding: 10px 14px !important;
        }

        .quixnav .metismenu > li > a {
            color: #475569 !important;
            font-size: 0.92rem !important;
            font-weight: 500 !important;
            padding: 12px 16px !important;
            border-radius: var(--oripio-radius-sm) !important;
            margin-bottom: 4px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .quixnav .metismenu > li > a i {
            font-size: 1.15rem;
            width: 24px;
            margin-right: 12px;
            color: #94A3B8;
            transition: color 0.2s ease;
        }

        .quixnav .metismenu > li:hover > a {
            background-color: #F8FAFC !important;
            color: var(--oripio-text-main) !important;
        }

        .quixnav .metismenu > li:hover > a i {
            color: var(--oripio-primary) !important;
        }

        .quixnav .metismenu > li.mm-active > a {
            background-color: var(--oripio-mint-light) !important;
            color: var(--oripio-primary) !important;
            font-weight: 700 !important;
        }

        .quixnav .metismenu > li.mm-active > a i {
            color: var(--oripio-primary) !important;
        }

        .sidebar-section-label {
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.06em !important;
            font-weight: 700 !important;
            color: #94A3B8 !important;
            padding: 16px 16px 6px !important;
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
            height: 28px !important;
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
        }

        .card-header {
            background: transparent !important;
            border-bottom: 1px solid var(--oripio-border-subtle) !important;
            padding: 20px 24px !important;
        }

        .card-body {
            padding: 24px !important;
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
    </style>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.all.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('admin/assets_2/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/quixnav-init.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/custom.min.js') }}"></script>
</body>

</html>
