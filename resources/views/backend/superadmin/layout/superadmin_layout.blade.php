<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Konsol Superadmin - SENTRA</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/assets_2/images/logo-sentra.png') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets_2/vendor/owl-carousel/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets_2/vendor/owl-carousel/css/owl.theme.default.min.css') }}">
    <link href="{{ asset('admin/assets_2/vendor/jquery-steps/css/jquery.steps.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/assets_2/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/assets_2/css/style.css') }}" rel="stylesheet">
    <!-- DataTables + Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Styles -->
    <link href="{{ asset('admin/assets/css/lib/themify-icons.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.min.css">

    <!-- Superadmin Dedicated Theme Styles -->
    <style>
        :root {
            --super-sidebar-bg: #0f172a;
            --super-sidebar-hover: #1e293b;
            --super-accent: #4f46e5;
        }

        body {
            font-family: 'Poppins', sans-serif !important;
            background-color: #f8fafc !important;
        }

        .quixnav {
            background: var(--super-sidebar-bg) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .quixnav .metismenu>li.mm-active>a {
            background-color: #1e293b !important;
            color: #ffffff !important;
            border-left: 4px solid var(--super-accent);
        }

        .quixnav .metismenu>li:hover>a {
            background-color: #1e293b !important;
            color: #ffffff !important;
        }

        .quixnav .metismenu a {
            color: #cbd5e1 !important;
        }

        .content-body {
            background-color: #f1f5f9 !important;
            min-height: calc(100vh - 120px);
        }

        .card {
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05) !important;
            border-radius: 8px !important;
        }

        .card-header {
            border-bottom: 1px solid #e2e8f0 !important;
            background-color: #ffffff !important;
        }

        .btn {
            border-radius: 6px !important;
        }

        .table thead th {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
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
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.all.min.js"></script>
    @stack('scripts')
    <script src="{{ asset('admin/assets_2/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/quixnav-init.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/custom.min.js') }}"></script>
</body>

</html>
