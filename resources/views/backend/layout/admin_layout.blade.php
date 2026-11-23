<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Instansi') - {{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}</title>
    <!-- Favicon icon -->
    @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset($appLogo) }}">
    @else
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('admin/assets_2/images/logo-sentra.png') }}">
    @endif
    {{-- Font Awesome 6 (ikon sidebar, topbar & aksi) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    {{-- Google Font: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    {{-- Tema dasar Dlab/oripio (shell lama, ditimpa oleh instansi.css) --}}
    <link href="{{ asset('admin/assets_2/css/style.css') }}" rel="stylesheet">
    {{-- DataTables 1.13.6 + integrasi Bootstrap 5 --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    {{-- SweetAlert2 --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.min.css">
    <link href="{{ asset('admin/assets_2/css/preview-model.css') }}" rel="stylesheet">
    {{-- CSS reset password --}}
    @include('backend.layout.page_admin.reset_password.styles')
    {{-- DESIGN SYSTEM INSTANSI — dimuat terakhir agar menang atas tema.
         Satu sistem untuk Admin PPA & Admin Dinsos; aksen dibedakan lewat
         body.dinsos-admin (lihat token --i-*). --}}
    <link href="{{ asset('admin/assets_instansi/css/instansi.css') }}?v={{ filemtime(public_path('admin/assets_instansi/css/instansi.css')) }}" rel="stylesheet">
    @stack('styles')

    <style>
        /* ============================================================
           LIVEWIRE SOFT NAVIGATION
           Livewire hanya mengganti <body>, sehingga #preloader baru ikut
           terpasang kembali padahal animasi masuk hanya untuk load pertama.
           Sama seperti panel Superadmin, preloader dimatikan permanen dan
           #main-wrapper dipaksa terlihat (tema menyembunyikannya dengan
           opacity: 0 sampai class "show" dipasang).
           ============================================================ */
        html.instansi-navigated #preloader {
            display: none !important;
        }

        .live-search-loading {
            display: none;
        }

        html.live-filter-loading .live-search-loading {
            display: inline-flex !important;
            align-items: center;
        }

        html.instansi-navigated #main-wrapper {
            opacity: 1 !important;
        }
    </style>

    @livewireStyles
</head>

<body class="{{ Auth::check() && Auth::user()->isAdminDinsos() ? 'dinsos-admin' : 'instansi-admin' }}">
    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">
        @include('backend.components.navbar')
        @include('backend.components.sidebar')

        <div class="content-body">
            @yield('admin')
        </div>

        @include('backend.components.footer')
    </div>

    @include('backend.layout.page_admin.reset_password.reset-password')
    @include('backend.layout.page_admin.reset_password.scripts')

    <!-- SCRIPTS - URUTAN PENTING! -->
    {{-- Pustaka inti cukup dimuat sekali. Livewire navigate mengganti <body>,
         jadi tanpa data-navigate-once skrip yang sama dieksekusi ulang dan
         memicu error "already declared" serta listener ganda. --}}
    <!-- 1. jQuery PERTAMA (HAPUS DUPLIKASI) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" data-navigate-once></script>

    <!-- 3. DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js" data-navigate-once></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js" data-navigate-once></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js" data-navigate-once></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js" data-navigate-once></script>

    <!-- 4. SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.3/sweetalert2.all.min.js" data-navigate-once></script>

    <!-- 5. Custom Scripts dari @stack('scripts') — sengaja TIDAK ditandai:
         skrip halaman harus jalan ulang setiap halaman baru dipasang. -->
    @stack('scripts')

    <!-- 6. Required vendors -->
    <script src="{{ asset('admin/assets_2/vendor/global/global.min.js') }}" data-navigate-once></script>
    {{-- quixnav-init.js sengaja tidak ditandai: ia harus jalan ulang agar
         atribut data-* pada <body> yang baru dipasang kembali oleh quixSettings. --}}
    <script src="{{ asset('admin/assets_2/js/quixnav-init.js') }}"></script>
    <script src="{{ asset('admin/assets_2/js/custom.min.js') }}" data-navigate-once></script>

    <!-- Chart ChartJS plugin files -->
    <script src="{{ asset('admin/assets_2/js/plugins-init/chartjs-init.js') }}" data-navigate-once></script>

    <!-- Vectormap -->
    <script src="{{ asset('admin/assets_2/vendor/raphael/raphael.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('admin/assets_2/vendor/morris/morris.min.js') }}" data-navigate-once></script>

    <script src="{{ asset('admin/assets_2/vendor/circle-progress/circle-progress.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('admin/assets_2/vendor/chart.js/Chart.bundle.min.js') }}" data-navigate-once></script>

    <script src="{{ asset('admin/assets_2/vendor/gaugeJS/dist/gauge.min.js') }}" data-navigate-once></script>

    <!-- flot-chart js -->
    <script src="{{ asset('admin/assets_2/vendor/flot/jquery.flot.js') }}" data-navigate-once></script>
    <script src="{{ asset('admin/assets_2/vendor/flot/jquery.flot.resize.js') }}" data-navigate-once></script>

    <!-- Owl Carousel -->
    <script src="{{ asset('admin/assets_2/vendor/owl-carousel/js/owl.carousel.min.js') }}" data-navigate-once></script>

    <!-- Counter Up -->
    <script src="{{ asset('admin/assets_2/vendor/jqvmap/js/jquery.vmap.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('admin/assets_2/vendor/jqvmap/js/jquery.vmap.usa.js') }}" data-navigate-once></script>
    <script src="{{ asset('admin/assets_2/vendor/jquery.counterup/jquery.counterup.min.js') }}" data-navigate-once></script>

    <script src="{{ asset('admin/assets_2/js/dashboard/dashboard-1.js') }}" data-navigate-once></script>

    {{-- Form-wizard --}}
    <script src="{{ asset('admin/assets_2/vendor/jquery-steps/build/jquery.steps.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('admin/assets_2/vendor/jquery-validation/jquery.validate.min.js') }}" data-navigate-once></script>
    <!-- Form validate init -->
    <script src="{{ asset('admin/assets_2/js/plugins-init/jquery.validate-init.js') }}" data-navigate-once></script>
    <!-- Form step init -->
    <script src="{{ asset('admin/assets_2/js/plugins-init/jquery-steps-init.js') }}" data-navigate-once></script>

    <!-- Datatable -->
    <script src="{{ asset('admin/assets_2/vendor/datatables/js/jquery.dataTables.min.js') }}" data-navigate-once></script>
    <script src="{{ asset('admin/assets_2/js/plugins-init/datatables.init.js') }}" data-navigate-once></script>
    <script>
        // Bahasa DataTables untuk seluruh tabel panel instansi.
        // Ditaruh setelah datatables dimuat terakhir agar default-nya tidak tertimpa.
        // Kunci gaya lama (sEmptyTable) dan baru (emptyTable) diisi sekaligus
        // karena tema masih memuat DataTables 1.10 di samping versi CDN.
        if (window.jQuery && jQuery.fn && jQuery.fn.dataTable && jQuery.fn.dataTable.defaults) {
            jQuery.fn.dataTable.defaults.oLanguage = jQuery.extend(true, jQuery.fn.dataTable.defaults.oLanguage || {}, {
                emptyTable: 'Belum ada data untuk ditampilkan',
                zeroRecords: 'Data tidak ditemukan',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada data',
                infoFiltered: '(difilter dari _MAX_ data)',
                lengthMenu: '_MENU_ data per halaman',
                search: 'Cari:',
                sEmptyTable: 'Belum ada data untuk ditampilkan',
                sZeroRecords: 'Data tidak ditemukan',
                sInfo: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                sInfoEmpty: 'Tidak ada data',
                sInfoFiltered: '(difilter dari _MAX_ data)',
                sLengthMenu: '_MENU_ data per halaman',
                sSearch: 'Cari:',
                sInfoThousands: '.',
                paginate: {
                    first: 'Awal',
                    last: 'Akhir',
                    next: 'Berikutnya',
                    previous: 'Sebelumnya'
                },
                oPaginate: {
                    sFirst: 'Awal',
                    sLast: 'Akhir',
                    sNext: 'Berikutnya',
                    sPrevious: 'Sebelumnya'
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js" data-navigate-once></script>
    @hasSection('chart')
        @yield('chart')
    @endif

    @livewireScripts

    {{-- ============================================================
         SKRIP PERSISTEN PANEL INSTANSI (PPA & DINSOS)
         Didaftarkan sekali ke document (data-navigate-once) sehingga
         tetap hidup meski <body> diganti oleh Livewire. Isinya:
           1. reinit komponen tema setelah navigasi
           2. live filter parsial ([data-live-region]) tanpa reload
           3. toggle sidebar dan normalisasi gaya sidebar
         ============================================================ --}}
    <script data-navigate-once>
        (function() {
            'use strict';

            // ============ SIDEBAR: NORMALISASI & IKON HAMBURGER ============
            // Ikon X hanya untuk drawer mobile (menandakan "tutup").
            // Di desktop sidebar mengecil menjadi rail, jadi ikon tetap
            // hamburger agar tidak terbaca sebagai tombol tutup.
            function syncHamburger() {
                var wrapper = document.getElementById('main-wrapper');
                var isDrawer = window.innerWidth < 992;
                var opened = !!wrapper && wrapper.classList.contains('menu-toggle');

                document.querySelectorAll('.hamburger').forEach(function(el) {
                    el.classList.toggle('is-active', isDrawer && opened);
                });
            }

            // quixnav.min.js memaksa data-sidebar-style="mini" pada 768–1199px.
            // Gaya "mini" itu menyembunyikan teks menu, sedangkan geometri
            // sidebar instansi hanya punya dua keadaan: penuh 260px (teks tampil)
            // atau rail manual lewat #main-wrapper.menu-toggle.
            //  • < 992px  → "overlay": drawer, teks tetap tampil
            //  • 992–1199 → "full": sidebar penuh 260px + teks
            function normalizeSidebarStyle() {
                var width = window.innerWidth;
                var body = document.body;
                if (!body) return;

                var desired = width < 992 ? 'overlay' : (width < 1200 ? 'full' : null);
                if (desired && body.getAttribute('data-sidebar-style') !== desired) {
                    body.setAttribute('data-sidebar-style', desired);
                }
                syncHamburger();
            }

            // ============ REINIT SETELAH NAVIGASI LIVEWIRE ============
            function markNavigated() {
                document.documentElement.classList.add('instansi-navigated');

                var preloader = document.getElementById('preloader');
                if (preloader) preloader.style.display = 'none';
            }

            function reinitInstansi() {
                // Tema menyembunyikan #main-wrapper (opacity: 0) sampai class
                // "show" dipasang. Body baru hasil navigasi perlu class ini.
                var wrapperEl = document.getElementById('main-wrapper');
                if (wrapperEl) wrapperEl.classList.add('show');

                var $ = window.jQuery;
                if ($) {
                    // global.min.js menyimpan referensi lama `var body = $('body')`.
                    // Setelah Livewire mengganti <body>, referensi itu menunjuk ke
                    // body lama sehingga atribut posisi header/sidebar tidak
                    // terpasang pada body baru. Perbarui lalu jalankan ulang tema.
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

                    if ($.fn.metisMenu) {
                        $('#menu').metisMenu();
                    }

                    if (window.PerfectScrollbar && document.querySelector('.quixnav-scroll')) {
                        new window.PerfectScrollbar('.quixnav-scroll');
                    }

                    var winH = window.outerHeight;
                    if (winH > 0) {
                        $('.content-body').css('min-height', (winH + 60) + 'px');
                    }
                }

                // Di layar kecil sidebar adalah drawer: tutup setelah pindah
                // halaman agar konten tidak tertutup saat halaman baru dimuat.
                if (window.innerWidth < 992 && wrapperEl) {
                    wrapperEl.classList.remove('menu-toggle');
                }

                normalizeSidebarStyle();
            }

            document.addEventListener('livewire:navigating', markNavigated);

            document.addEventListener('livewire:navigated', function() {
                markNavigated();
                reinitInstansi();
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

                data.forEach(function(value, key) {
                    if (value === '' || value === null) return;
                    url.searchParams.set(key, value);
                });

                url.searchParams.delete('page');

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

            function startAdminLiveLoading() {
                document.documentElement.classList.add('live-filter-loading');
                document.querySelectorAll('[data-live-region]').forEach(function(el) {
                    el.setAttribute('aria-busy', 'true');
                });
            }

            function stopAdminLiveLoading() {
                document.documentElement.classList.remove('live-filter-loading');
                document.querySelectorAll('[data-live-region]').forEach(function(el) {
                    el.removeAttribute('aria-busy');
                });
            }

            function partialLoad(url) {
                var token = ++liveRegionToken;
                startAdminLiveLoading();

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        },
                        credentials: 'same-origin'
                    })
                    .then(function(response) {
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.text();
                    })
                    .then(function(html) {
                        if (token !== liveRegionToken) return; // respons kedaluwarsa, abaikan

                        var doc = new DOMParser().parseFromString(html, 'text/html');
                        var swapped = 0;

                        document.querySelectorAll('[data-live-region]').forEach(function(el) {
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
                        try {
                            window.history.replaceState(window.history.state, '', url);
                        } catch (e) {}

                        // Beri tahu skrip halaman bahwa area live sudah ditukar
                        // (mis. chat perlu menyinkronkan percakapan aktif).
                        document.dispatchEvent(new CustomEvent('sentra:live-region-swapped', {
                            detail: { url: url }
                        }));
                    })
                    .catch(function() {
                        fallbackLiveNavigate(url);
                    })
                    .finally(function() {
                        if (token === liveRegionToken) {
                            stopAdminLiveLoading();
                        }
                    });
            }

            function pageHasLiveRegions() {
                return !!document.querySelector('[data-live-region]');
            }

            // Dipakai halaman yang punya polling (mis. daftar laporan PPA)
            // agar penyegaran hanya menukar area hasil, bukan reload penuh.
            window.sentraLiveRefresh = function() {
                if (!pageHasLiveRegions()) return false;
                partialLoad(window.location.href);
                return true;
            };

            // Submit (tombol Filter / Enter) pada halaman live → update parsial.
            document.addEventListener('submit', function(event) {
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

                liveFilterTimers.set(form, setTimeout(function() {
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

            document.addEventListener('input', function(event) {
                var el = event.target;
                var form = el && el.closest ? el.closest('form[data-livewire-navigate]') : null;
                if (!form) return;
                if (el.name) lastLiveInputName = el.name;
                queueLiveFilterSubmit(form, 300);
            });

            document.addEventListener('change', function(event) {
                var el = event.target;
                var form = el && el.closest ? el.closest('form[data-livewire-navigate]') : null;
                if (!form) return;
                // Dropdown & tanggal langsung diterapkan; input teks didebounce singkat.
                queueLiveFilterSubmit(form, el.tagName === 'SELECT' || el.type === 'date' ? 0 : 300);
            });

            // Pagination dan tombol Reset di dalam area live → update parsial juga.
            // Fase capture agar mengungguli listener navigasi Livewire.
            document.addEventListener('click', function(event) {
                if (!event.target || typeof event.target.closest !== 'function') return;

                // Tautan di dalam area live yang bertanda data-live-link
                // ditukar parsial juga: instan, tanpa soft navigation dan
                // tanpa reload.
                var liveLink = event.target.closest('[data-live-region] a[data-live-link][href]');
                if (liveLink) {
                    // Biarkan perilaku bawaan untuk buka tab baru / klik kanan.
                    if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

                    event.preventDefault();
                    event.stopImmediatePropagation();
                    partialLoad(liveLink.href);
                    return;
                }

                var link = event.target.closest('[data-live-region] .pagination a[href]');
                if (link) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    partialLoad(link.href);
                    return;
                }

                var reset = event.target.closest('form[data-livewire-navigate] a[data-live-reset]');
                if (reset) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    var form = reset.closest('form');
                    if (!form) return;

                    form.querySelectorAll('input, select, textarea').forEach(function(field) {
                        if (!field.name || field.type === 'hidden') return;
                        if (field.tagName === 'SELECT') {
                            field.selectedIndex = 0;
                        } else {
                            field.value = '';
                        }
                    });

                    lastLiveInputName = null;
                    partialLoad(buildLiveUrl(form));
                }
            }, true);

            // Setelah fallback navigasi soft, kembalikan fokus ke input pencarian
            // terakhir agar pengguna bisa melanjutkan mengetik.
            document.addEventListener('livewire:navigated', function() {
                if (!lastLiveInputName) return;
                var el = document.querySelector('form[data-livewire-navigate] [name="' + lastLiveInputName + '"]');
                if (el && typeof el.focus === 'function') {
                    el.focus();
                    var len = (el.value || '').length;
                    try {
                        el.setSelectionRange(len, len);
                    } catch (e) { /* input non-teks */ }
                }
                lastLiveInputName = null;
            });

            // ============ TOGGLE SIDEBAR ============
            // custom.min.js sudah memasang handler sendiri pada .nav-control.
            // Agar satu klik tidak men-toggle dua kali (sidebar jadi tidak bisa
            // ditutup), hanya listener fase-capture di document yang menentukan,
            // lalu propagasi dihentikan sebelum handler tema berjalan.
            document.addEventListener('click', function(event) {
                if (!event.target || typeof event.target.closest !== 'function') return;

                var control = event.target.closest('.nav-control');
                if (!control) return;

                var wrapper = document.getElementById('main-wrapper');
                if (!wrapper) return;

                event.preventDefault();
                event.stopPropagation();

                wrapper.classList.toggle('menu-toggle');
                syncHamburger();
            }, true);

            // Di layar kecil sidebar adalah drawer: tutup pada load pertama.
            if (window.innerWidth < 992) {
                var wrapperAwal = document.getElementById('main-wrapper');
                if (wrapperAwal) wrapperAwal.classList.remove('menu-toggle');
            }

            normalizeSidebarStyle();
            window.addEventListener('resize', normalizeSidebarStyle, {
                passive: true
            });
        })();
    </script>
</body>

</html>
