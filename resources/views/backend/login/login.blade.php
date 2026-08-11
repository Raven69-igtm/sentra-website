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
</head>

<body class="">
    <main class="main-content  mt-0">
        <section>
            <div class="page-header min-vh-100">
                <div class="container">
                    <div class="row">

                        <div
                            class="col-7 d-lg-flex d-none h-100 my-auto pe-0 position-absolute top-0 start-0 text-center justify-content-center flex-column">
                            <div class="position-relative bg-gradient-primary h-100 m-1 px-7 border-radius-lg d-flex flex-column justify-content-center"
                                style="background-image: url('backend/assets/img/illustrations/tree-1.png'); background-size: cover; background-position: center;">
                            </div>
                        </div>

                        <div class="col-xl-5 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-0">
                            <div class="card-header ms-3">
                                <h3 class="font-weight-bolder custom-title">Login {{ \App\Models\SystemSetting::get('app_name', 'SENTRA') }}</h3>
                                <p class="mb-0 custom-subtitle">Portal {{ \App\Models\SystemSetting::get('instansi_name', 'Dinas Sosial PPPA') }}</p>
                            </div>

                            <div class="card card-plain">
                                <div class="card-body">
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

                                        @if ($errors->has('login'))
                                            <div class="alert alert-soft-danger text-center mb-0">
                                                {{ $errors->first('login') }}
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
</body>

</html>
