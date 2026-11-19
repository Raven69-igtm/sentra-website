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
    <main class="main-content mt-0">
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
                                <h3 class="font-weight-bolder custom-title">Kode OTP</h3>
                                <p class="mb-0 custom-subtitle">Masukkan kode OTP untuk verifikasi</p>
                            </div>
                            <div class="card card-plain">
                                <div class="card-body">
                                    <form method="POST" action="{{ route('otp.verify') }}">
                                        @csrf
                                        <!-- Input email tersembunyi -->
                                        <input type="hidden" name="email" value="{{ session('email') }}">

                                        <!-- Input OTP -->
                                        <div class="otp-wrapper d-flex justify-content-between flex-wrap gap-2 mb-3">
                                            <input type="text" class="form-control otp-input" maxlength="1"
                                                name="otp[]" inputmode="numeric" required>
                                            <input type="text" class="form-control otp-input" maxlength="1"
                                                name="otp[]" inputmode="numeric" required>
                                            <input type="text" class="form-control otp-input" maxlength="1"
                                                name="otp[]" inputmode="numeric" required>
                                            <input type="text" class="form-control otp-input" maxlength="1"
                                                name="otp[]" inputmode="numeric" required>
                                            <input type="text" class="form-control otp-input" maxlength="1"
                                                name="otp[]" inputmode="numeric" required>
                                            <input type="text" class="form-control otp-input" maxlength="1"
                                                name="otp[]" inputmode="numeric" required>
                                        </div>
                                        @if (session('message'))
                                            <div class="alert alert-soft-success text-center mb-0">
                                                {{ session('message') }}
                                            </div>
                                        @endif

                                        @if ($errors->any())
                                            <div class="alert alert-soft-danger text-center mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <div>{{ $error }}</div>
                                                @endforeach
                                            </div>
                                        @endif


                                        <div class="text-center">
                                            <button type="submit"
                                                class="btn btn-lg custom-login-btn w-100 mt-4 mb-0">Verifikasi</button>
                                        </div>
                                    </form>
                                </div>

                                <div class="card-footer text-center pt-0 px-lg-2 px-1">
                                    <span class="mb-2 text-sm mx-auto">
                                        Kirim ulang kode OTP?
                                        <form id="resendOtpForm" method="POST" action="{{ route('send.otp') }}"
                                            class="d-inline">
                                            @csrf
                                            <input type="hidden" name="email" value="{{ session('email') }}">
                                            <a href="#"
                                                onclick="event.preventDefault(); document.getElementById('resendOtpForm').submit();"
                                                class="text-info text-gradient font-weight-bold">Kirim ulang</a>
                                        </form>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

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

    {{-- CUSTOM JS --}}
    <script>
        const inputs = document.querySelectorAll('.otp-input');

        inputs.forEach((input, index) => {
            // Pindah ke input selanjutnya saat ketik
            input.addEventListener('input', () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            // Handle paste dari clipboard
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const data = (e.clipboardData || window.clipboardData).getData('text');
                const digits = data.replace(/\D/g, '').split('').slice(0, inputs
                    .length); // ambil angka saja

                digits.forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                    }
                });

                // Optional: fokus ke input terakhir yang terisi
                const nextInput = inputs[digits.length];
                if (nextInput) {
                    nextInput.focus();
                }
            });
        });
    </script>

    <style>
        .otp-wrapper {
            max-width: 100%;
            justify-content: center;
        }

        .otp-input {
            flex: 1 1 48px;
            max-width: 48px;
            height: 55px;
            font-size: 1.5rem;
            color: #555;
            border: 1px solid #52AE77 !important;
            border-radius: 8px;
            text-align: center;
            background-color: #fff;
        }

        @media (max-width: 576px) {
            .otp-input {
                flex: 1 1 40px;
                max-width: 40px;
                height: 45px;
                font-size: 1.2rem;
            }
        }

        @media (max-width: 400px) {
            .otp-input {
                flex: 1 1 35px;
                max-width: 35px;
                height: 42px;
                font-size: 1.1rem;
            }
        }
    </style>
</body>

</html>
