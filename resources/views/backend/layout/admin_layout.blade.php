<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>SENTRA</title>
    <!-- Favicon icon -->
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/assets_2/images/logo-sentra.png') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets_2/vendor/owl-carousel/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/assets_2/vendor/owl-carousel/css/owl.theme.default.min.css') }}">
    <!-- Form step -->
    <link href="{{ asset('admin/assets_2/vendor/jquery-steps/css/jquery.steps.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/assets_2/vendor/jqvmap/css/jqvmap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/assets_2/css/style.css') }}" rel="stylesheet">
    <!-- Datatable -->
    <link href="{{ asset('admin/assets_2/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <!-- DataTables + Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Styles -->
    <link href="{{ asset('admin/assets/css/lib/themify-icons.css') }}" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- link font --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    {{-- Link DataTables --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.min.css">
    <link href="{{ asset('admin/assets_2/css/preview-model.css') }}" rel="stylesheet">
    {{-- CSS reset password --}}
    @include('backend.layout.page_admin.reset_password.styles')
    @if (Auth::check() && Auth::user()->isAdminDinsos())
        {{-- Tema orange untuk Admin Dinsos, struktur sama dengan Admin PPA --}}
        <link href="{{ asset('admin/assets_dinsos/css/dinsos_admin.css') }}" rel="stylesheet">
    @endif
    @stack('styles')
</head>

<body class="{{ Auth::check() && Auth::user()->isAdminDinsos() ? 'dinsos-admin' : '' }}">
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>
    <div id="main-wrapper">
        <div class="header"></div>
        @include('backend.components.navbar')
        @include('backend.components.sidebar')
        <div class="content-body">
            @yield('admin')
        </div>
        @include('backend.components.footer')
    </div>

    @include('backend.layout.page_admin.reset_password.reset-password')
    @include('backend.layout.page_admin.reset_password.scripts')
    @include('backend.layout.page_admin.notifikasi')

    <!-- SCRIPTS - URUTAN PENTING! -->
    <!-- 1. jQuery PERTAMA (HAPUS DUPLIKASI) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- 3. DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <!-- 4. SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.all.min.js"></script>

    <!-- 5. Custom Scripts dari @stack('scripts') -->
    @stack('scripts')

    <!-- 6. Required vendors -->
    <script src="{{ asset('admin/assets_2/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/quixnav-init.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/custom.min.js') }}"></script>

    <!-- Chart ChartJS plugin files -->
    <script src="{{ asset('admin/assets_2/js/plugins-init/chartjs-init.js') }}"></script>

    <!-- Vectormap -->
    <script src="{{ asset('admin/assets_2/vendor/raphael/raphael.min.js') }}"></script>
    <script src="{{ asset('admin/assets_2/vendor/morris/morris.min.js') }}"></script>

    <script src="{{ asset('admin/assets_2/vendor/circle-progress/circle-progress.min.js') }}"></script>
    <script src="{{ asset('admin/assets_2/vendor/chart.js/Chart.bundle.min.js') }}"></script>

    <script src="{{ asset('admin/assets_2/vendor/gaugeJS/dist/gauge.min.js') }}"></script>

    <!-- flot-chart js -->
    <script src="{{ asset('admin/assets_2/vendor/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('admin/assets_2/vendor/flot/jquery.flot.resize.js') }}"></script>

    <!-- Owl Carousel -->
    <script src="{{ asset('admin/assets_2/vendor/owl-carousel/js/owl.carousel.min.js') }}"></script>

    <!-- Counter Up -->
    <script src="{{ asset('admin/assets_2/vendor/jqvmap/js/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('admin/assets_2/vendor/jqvmap/js/jquery.vmap.usa.js') }}"></script>
    <script src="{{ asset('admin/assets_2/vendor/jquery.counterup/jquery.counterup.min.js') }}"></script>

    <script src="{{ asset('admin/assets_2/js/dashboard/dashboard-1.js') }}"></script>

    {{-- Form-wizard --}}
    <script src="{{ asset('admin/assets_2/vendor/jquery-steps/build/jquery.steps.min.js') }}"></script>
    <script src="{{ asset('admin/assets_2/vendor/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Form validate init -->
    <script src="{{ asset('admin/assets_2/js/plugins-init/jquery.validate-init.js') }}"></script>
    <!-- Form step init -->
    <script src="{{ asset('admin/assets_2/js/plugins-init/jquery-steps-init.js') }}"></script>

    <!-- Datatable -->
    <script src="{{ asset('admin/assets_2/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/plugins-init/datatables.init.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @hasSection('chart')
        @yield('chart')
    @endif
</body>

<script>
    $(document).ready(function() {
        // Fungsi untuk mengambil dan memperbarui notifikasi di dropdown
        function fetchDropdownNotifications() {
            $.ajax({
                url: '{{ route('notifikasi.get') }}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var count = response.count;
                        var notifikasi = response.notifikasi;

                        // Update badge dan pulse di navbar
                        if (count > 0) {
                            $('#notification-badge').text(count).show();
                            $('#notification-pulse').show();
                            $('#notification-count-header').text(count + ' baru').show();
                            $('#notification-footer').show();
                        } else {
                            $('#notification-badge').hide();
                            $('#notification-pulse').hide();
                            $('#notification-count-header').hide();
                            $('#notification-footer').hide();
                        }

                        // Update isi dropdown
                        var listContent = '';
                        if (notifikasi.length > 0) {
                            $.each(notifikasi, function(index, notif) {
                                var id_laporan = null;
                                if (notif.judul.includes('Laporan Baru')) {
                                    var parts = notif.judul.split(' - ');
                                    if (parts.length > 1) {
                                        id_laporan = parts[0].trim();
                                        console.log('Parsed id_laporan for notif', notif
                                            .id_notif, ':', id_laporan); // Debugging
                                    } else {
                                        console.warn('Invalid judul format for notif', notif
                                            .id_notif, ':', notif.judul);
                                    }
                                }
                                var formAction = id_laporan ?
                                    '{{ route('admin.notifikasi.terima-laporan', ['id_notif' => ':id_notif', 'id_laporan' => ':id_laporan']) }}'
                                    .replace(':id_notif', notif.id_notif)
                                    .replace(':id_laporan', id_laporan) :
                                    '';
                                listContent += `
                                <div class="media dropdown-item align-items-start py-2 px-3 border-bottom">
                                    <span class="success mr-2"><i class="ti-user"></i></span>
                                    <div class="media-body" style="width: 100%;">
                                        <p class="mb-1 font-weight-bold" style="font-size: 0.9rem;">${notif.judul}</p>
                                        <p class="mb-2 text-muted" style="font-size: 0.85rem;">${notif.pesan}</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            ${id_laporan ? `
                                                <form method="POST" action="${formAction}">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <button type="submit" class="btn btn-sm btn-hijau">TERIMA</button>
                                                </form>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>`;
                            });
                        } else {
                            listContent =
                                '<div class="text-center p-3"><p class="mb-0">Tidak ada notifikasi baru</p></div>';
                        }
                        $('#notification-list').html(listContent);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengambil notifikasi dropdown:', error);
                }
            });
        }

        // Simpan URL dengan placeholder dummy
        var baseUrlTerima =
            '{{ route('admin.notifikasi.terima-laporan', ['id_notif' => '__id_notif__', 'id_laporan' => '__id_laporan__']) }}';
        var baseUrlRead = '{{ route('admin.notifikasi.mark-read', ['id' => '__id_notif__']) }}';

        // Fungsi untuk mengambil dan memperbarui notifikasi di modal
        function fetchModalNotifications() {
            $.ajax({
                url: '{{ route('notifikasi.get') }}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        var notifikasi = response.notifikasi;
                        var tableContent = '';

                        if (notifikasi.length > 0) {
                            $.each(notifikasi, function(index, notif) {
                                var id_laporan = null;

                                // parsing id laporan dari judul
                                if (notif.judul.includes('Laporan Baru')) {
                                    var parts = notif.judul.split(' - ');
                                    if (parts.length > 1) {
                                        id_laporan = parts[0].trim();
                                    }
                                }

                                var actionForm = '';
                                if (id_laporan) {
                                    // ganti placeholder dengan id asli
                                    var actionUrl = baseUrlTerima
                                        .replace('__id_notif__', notif.id_notif)
                                        .replace('__id_laporan__', id_laporan);

                                    actionForm = `
                                    <form method="POST" action="${actionUrl}" class="d-inline">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-sm btn-hijau">TERIMA</button>
                                    </form>
                                `;
                                } else {
                                    var actionUrl = baseUrlRead.replace('__id_notif__',
                                        notif.id_notif);

                                    actionForm = `
                                    <form method="POST" action="${actionUrl}" class="d-inline">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <button type="submit" class="btn btn-sm btn-info">Tandai Dibaca</button>
                                    </form>
                                `;
                                }

                                tableContent += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${notif.judul}</td>
                                    <td>${notif.pesan}</td>
                                    <td>${new Date(notif.created_at).toLocaleString('id-ID', {
                                        day: '2-digit', month: 'short', year: 'numeric',
                                        hour: '2-digit', minute: '2-digit'
                                    })}</td>
                                    <td>${actionForm}</td>
                                </tr>`;
                            });
                        } else {
                            tableContent =
                                '<tr><td colspan="5" class="text-center">Tidak ada notifikasi yang belum dibaca</td></tr>';
                        }

                        $('#notification-table-body').html(tableContent);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Gagal mengambil notifikasi modal:', error);
                }
            });
        }

        // Panggil fetch awal untuk dropdown
        fetchDropdownNotifications();

        // Polling setiap 5 detik untuk dropdown
        setInterval(fetchDropdownNotifications, 5000);

        // Load notifikasi di modal saat dibuka
        $('#allNotificationsModal').on('shown.bs.modal', function() {
            fetchModalNotifications();
        });

        // Handler untuk tombol TERIMA di dropdown
        $(document).on('submit', '.notification-list form', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();
            var notificationItem = form.closest('.media');
            var submitButton = form.find('button[type="submit"]');
            var originalText = submitButton.text();
            submitButton.prop('disabled', true).text('Processing...');

            console.log('Submitting form to:', url); // Debugging

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        notificationItem.fadeOut(300, function() {
                            $(this).remove();
                            updateNotificationCounter();
                            checkEmptyNotifications();
                        });
                        showNotificationMessage(response.message, 'success');
                        fetchModalNotifications(); // Refresh modal jika terbuka
                    } else {
                        submitButton.prop('disabled', false).text(originalText);
                        showNotificationMessage(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    submitButton.prop('disabled', false).text(originalText);
                    var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotificationMessage(errorMessage, 'error');
                    console.error('Error:', error, 'Response:', xhr.responseText);
                }
            });
        });

        // Handler untuk tombol TERIMA dan Tandai Dibaca di modal
        $(document).on('submit', '#allNotificationsModal form', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();
            var tableRow = form.closest('tr');
            var submitButton = form.find('button[type="submit"]');
            var originalText = submitButton.text();
            submitButton.prop('disabled', true).text('Processing...');

            console.log('Submitting form to:', url); // Debugging

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        tableRow.fadeOut(300, function() {
                            $(this).remove();
                            updateTableNumbers();
                            checkEmptyTable();
                        });
                        showNotificationMessage(response.message, 'success');
                        fetchDropdownNotifications(); // Refresh dropdown
                    } else {
                        submitButton.prop('disabled', false).text(originalText);
                        showNotificationMessage(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    submitButton.prop('disabled', false).text(originalText);
                    var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotificationMessage(errorMessage, 'error');
                    console.error('Error:', error, 'Response:', xhr.responseText);
                }
            });
        });

        // Handler untuk mark-read
        $(document).on('submit', 'form[action*="mark-read"]', function(e) {
            e.preventDefault();
            var form = $(this);
            var url = form.attr('action');
            var formData = form.serialize();
            var tableRow = form.closest('tr');
            var submitButton = form.find('button[type="submit"]');
            var originalText = submitButton.text();
            submitButton.prop('disabled', true).text('Processing...');

            console.log('Submitting mark-read to:', url); // Debugging

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        tableRow.fadeOut(300, function() {
                            $(this).remove();
                            updateTableNumbers();
                            checkEmptyTable();
                        });
                        showNotificationMessage(response.message, 'success');
                        fetchDropdownNotifications(); // Refresh dropdown
                    } else {
                        submitButton.prop('disabled', false).text(originalText);
                        showNotificationMessage(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    submitButton.prop('disabled', false).text(originalText);
                    var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showNotificationMessage(errorMessage, 'error');
                    console.error('Error:', error, 'Response:', xhr.responseText);
                }
            });
        });
    });

    // Fungsi-fungsi pendukung yang sudah ada
    function updateNotificationCounter() {
        var remainingNotifications = $('.notification-list .media').length;
        var badge = $('#notification-badge');

        if (remainingNotifications > 0) {
            badge.text(remainingNotifications).show();
        } else {
            badge.hide();
        }
    }

    function checkEmptyNotifications() {
        if ($('.notification-list .media').length === 0) {
            $('.notification-list').html(
                '<div class="text-center p-3"><p class="mb-0">Tidak ada notifikasi baru</p></div>');
            $('#notification-footer').hide();
        }
    }

    function updateTableNumbers() {
        $('#notification-table-body tr').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    function checkEmptyTable() {
        if ($('#notification-table-body tr').length === 0) {
            $('#notification-table-body').html(
                '<tr><td colspan="5" class="text-center">Tidak ada notifikasi yang belum dibaca</td></tr>');
        }
    }

    function showNotificationMessage(message, type) {
        var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        var toast = $('<div class="alert ' + alertClass +
            ' alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;">' +
            '<span>' + message + '</span>' +
            '<button type="button" class="close" data-dismiss="alert">' +
            '<span>&times;</span>' +
            '</button>' +
            '</div>');

        $('body').append(toast);

        setTimeout(function() {
            toast.alert('close');
        }, 3000);
    }
</script>

</html>
