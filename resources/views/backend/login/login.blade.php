<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    @if ($appLogo = \App\Models\SystemSetting::get('app_logo'))
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset($appLogo) }}">
        <link rel="icon" type="image/png" href="{{ asset($appLogo) }}">
    @else
        <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('backend/assets/img/logo-sentra.png') }}">
        <link rel="icon" type="image/png" href="{{ asset('backend/assets/img/logo-sentra.png') }}">
    @endif
    <title>
        {{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }} - Login
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="{{ 'backend/assets/css/nucleo-icons.css' }}" rel="stylesheet" />
    <link href="{{ 'backend/assets/css/nucleo-svg.css' }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ 'backend/assets/css/material-dashboard.css?v=3.2.0' }}" rel="stylesheet" />
    {{-- Ilustrasi & animasi daun (aset tunggal tree-sentra.png). Hanya halaman login. --}}
    <link href="{{ asset('backend/assets/css/login-motion.css?v=3') }}" rel="stylesheet" />
</head>

<body class="">
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                {{-- Daun untuk layar kecil: lapisan ini berada di belakang konten
                     halaman, jadi tidak menutupi form login. --}}
                <div class="login-leaves-page" aria-hidden="true" data-login-leaves="page"></div>

                <div class="container">
                    <div class="row">

                        <div
                            class="col-7 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                            {{-- Ilustrasi dari satu aset tree-sentra.png, dipecah oleh
                                 scripts/build-login-tree-sentra.py menjadi latar, pohon,
                                 dan ayunan agar bisa dianimasikan. Posisi ketiganya diam
                                 persis seperti gambar aslinya. --}}
                            <div
                                class="position-relative h-100 m-1 border-radius-lg d-flex flex-column justify-content-center login-hero">
                                <div class="login-hero__stage" aria-hidden="true">
                                    <img class="login-hero__scene"
                                        src="{{ asset('backend/assets/img/illustrations/login/sentra-scene.png') }}" alt="">
                                    <div class="login-hero__tree">
                                        <img src="{{ asset('backend/assets/img/illustrations/login/sentra-tree.png') }}"
                                            alt="">
                                        <div class="login-hero__swing">
                                            <img src="{{ asset('backend/assets/img/illustrations/login/sentra-swing.png') }}"
                                                alt="">
                                        </div>
                                    </div>
                                    {{-- Daun lahir di tepi bawah tajuk. data-spawns berisi
                                         [x%, y%] pada kanvas 1672x941, dicetak oleh skrip
                                         build-login-tree-sentra.py. 8-12 daun di layar lebar. --}}
                                    <div class="login-hero__leaves" data-login-leaves="hero"
                                        data-spawns="[[7.0,53.67],[9.87,54.2],[12.75,57.7],[15.62,58.02],[18.5,55.47],[21.37,57.81],[24.25,49.42],[27.12,40.06],[30.0,48.03],[32.87,53.13],[35.75,54.09],[38.62,56.64],[41.5,61.32],[44.37,59.19],[47.25,56.32]]"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-5 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-0">
                            <div class="card-header ms-3">
                                <h3 class="font-weight-bolder custom-title">Login {{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}</h3>
                                <p class="mb-0 custom-subtitle">Portal {{ \App\Models\SystemSetting::get('instansi_name', 'Dinas Sosial') }}</p>
                            </div>

                            <div class="card card-plain">
                                <div class="card-body">
                                    {{-- Flash dari alur reset password ("Password berhasil
                                         direset") dan dari redirect middleware role. --}}
                                    @if (session('message'))
                                        <div class="alert alert-soft-success text-center mb-3">
                                            {{ session('message') }}
                                        </div>
                                    @endif

                                    @if (session('error'))
                                        <div class="alert alert-soft-danger text-center mb-3">
                                            {{ session('error') }}
                                        </div>
                                    @endif

                                    <form action="login" method="POST">
                                        @csrf
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                required oninvalid="this.setCustomValidity('Email tidak boleh kosong')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Password</label>
                                            <input type="password" class="form-control" id="passwordInput"
                                                name="password" required
                                                oninvalid="this.setCustomValidity('Password wajib diisi')"
                                                oninput="this.setCustomValidity('')">
                                        </div>
                                        <div class="form-check form-check-info text-start ps-0 mb-3">
                                            <input class="form-check-input" type="checkbox" id="togglePassword">
                                            <label class="form-check-label" for="togglePassword">
                                                Show Password
                                            </label>
                                        </div>

                                        {{-- Tampilkan semua error, termasuk error kunci 'email'
                                             dari alur lupa password, bukan hanya 'login'. --}}
                                        @if ($errors->any())
                                            <div class="alert alert-soft-danger text-center mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <div>{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="text-center">
                                            <button type="submit"
                                                class="btn btn-lg custom-login-btn w-100 mt-4 mb-0">Login</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <p class="mb-2 text-sm mx-auto">
                                        Lupa password?
                                        <a href="{{ route('resetpw') }}"
                                            class="text-info text-gradient font-weight-bold">Reset password</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <script>
        document.getElementById('togglePassword').addEventListener('change', function() {
            const passwordInput = document.getElementById('passwordInput');
            passwordInput.type = this.checked ? 'text' : 'password';
        });
    </script>
    <!--   Core JS Files   -->
    <script src="{{ 'backend/assets/js/core/popper.min.js' }}"></script>
    <script src="{{ 'backend/assets/js/core/bootstrap.min.js' }}"></script>
    <script src="{{ 'backend/assets/js/plugins/perfect-scrollbar.min.js' }}"></script>
    <script src="{{ 'backend/assets/js/plugins/smooth-scrollbar.min.js' }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js'}}"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ 'backend/assets/js/material-dashboard.min.js?v=3.2.0' }}"></script>
    {{-- Menyiapkan daun animasi (JavaScript vanilla, tanpa library). --}}
    <script src="{{ asset('backend/assets/js/login-leaves.js?v=3') }}" defer></script>
</body>

</html>
