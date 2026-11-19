<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ 'backend/assets/img/logo-sentra.png' }}">
    <link rel="icon" type="image/png" href="{{ 'backend/assets/img/logo-sentra.png' }}">
    <title>
        SENTRA
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
                                style="background-image: url('backend/assets/img/illustrations/password.png'); background-size: cover; background-position: center;">
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-5 col-md-7 d-flex flex-column ms-auto me-auto ms-lg-auto me-lg-0">
                            <div class="card-header ms-3">
                                <h3 class="font-weight-bolder custom-title">Password Baru</h3>
                                <p class="mb-0 custom-subtitle">Masukkan password baru anda</p>
                            </div>
                            <div class="card card-plain">
                                <div class="card-body">
                                    <form role="form" method="POST" action="{{ route('otp.reset') }}">
                                        @csrf
                                        <input type="hidden" name="email" value="{{ $email ?? session('verified_email') }}" />

                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Password baru</label>
                                            <input type="password" class="form-control" name="password" id="password"
                                                required>
                                        </div>

                                        <div class="input-group input-group-outline mb-3">
                                            <label class="form-label">Ketikkan ulang password</label>
                                            <input type="password" class="form-control" name="password_confirmation"
                                                id="password_confirmation" required>
                                        </div>

                                        <div class="form-check form-check-info text-start ps-0 mb-3">
                                            <input class="form-check-input" type="checkbox" id="togglePassword">
                                            <label class="form-check-label" for="togglePassword">
                                                Tampilkan Password
                                            </label>
                                        </div>
                                        @if ($errors->any())
                                            <div class="alert alert-soft-danger text-center mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <div>{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="text-center">
                                            <button type="submit"
                                                class="btn btn-lg custom-login-btn w-100 mt-4 mb-0">Ubah
                                                Password</button>
                                        </div>
                                    </form>
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
            const pw = document.getElementById('password');
            const pwConfirm = document.getElementById('password_confirmation');
            const type = this.checked ? 'text' : 'password';
            pw.type = type;
            pwConfirm.type = type;
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
