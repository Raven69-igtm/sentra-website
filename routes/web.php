<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\RekapanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\InformasiAnakController;
use App\Http\Controllers\GantiSandiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\Superadmin\SuperadminDashboardController;
use App\Http\Controllers\Superadmin\SuperadminAkunController;
use App\Http\Controllers\Superadmin\SuperadminRoleController;
use App\Http\Controllers\Superadmin\SuperadminSettingController;
use App\Http\Controllers\Superadmin\SuperadminLaporanController;
use App\Http\Controllers\DinsosDashboardController;
use App\Http\Controllers\DinsosLaporanController;

// =======================
// ROUTE GUEST (BELUM LOGIN)
// =======================
Route::middleware(['guest'])->group(function () {
    Route::get('/', [LandingPageController::class, 'index'])->name('index');

    // Login & Reset Password
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/resetpassword', [PageController::class, 'reset_pw'])->name('resetpw');
    Route::get('/resetpassword1', [PageController::class, 'otp'])->name('inputOTP');
    Route::get('/resetpassword2', [PageController::class, 'new_pw'])->name('newpassword');

    // Halaman Layanan
    Route::view('/layanan/perempuan', 'frontend.layanan.perempuan');
    Route::view('/layanan/anak', 'frontend.layanan.anak');
    Route::view('/layanan/kesejahteraan', 'frontend.layanan.kesejahteraan');
    Route::view('/layanan/edukasi', 'frontend.layanan.edukasi');
});

// =======================
// ROUTE AUTH (SETELAH LOGIN)
// =======================
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Laporan
Route::controller(LaporanController::class)->group(function () {
    Route::get('/laporan', 'proses')->name('laporan_proses');
    Route::get('/laporan_selesai', 'laporan_selesai')->name('selesai');
    Route::get('/laporan/{id}', 'laporan_show')->name('laporan.show');
    Route::get('/laporan/preview/{id}', 'laporan_preview')->name('laporan.preview');
    Route::get('/laporan/{id}/edit', 'edit')->name('laporan.edit');
    Route::put('/laporan/{id}', 'update')->name('laporan.update');
    Route::delete('/laporan/{id}', 'destroy')->name('laporan.destroy');
    Route::put('/laporan/proseskan/{id}', 'proseskan')->name('laporan.proseskan');
    Route::put('/laporan/{id}/selesai', 'selesai')->name('laporan.selesai');
    Route::put('/laporan/{id}/rujuk', 'rujuk')->name('laporan.rujuk');
});

    // Informasi
    Route::controller(InformasiController::class)->group(function () {
        Route::get('/informasi', 'index')->name('informasi.index');
        Route::post('/informasi', 'store')->name('informasi.store');
        Route::get('/informasi/{id}/edit', 'edit')->name('informasi.edit');
        Route::put('/informasi/{id}', 'update')->name('informasi.update');
        Route::delete('/informasi/{id}', 'destroy')->name('informasi.destroy');
    });

    // Informasi Anak
    Route::delete('/anak/{id}', [InformasiAnakController::class, 'destroy'])->name('anak.destroy');

    // Rekapan
    Route::get('/rekapan', [RekapanController::class, 'index'])->name('rekapan');
    Route::post('/rekapan/export', [RekapanController::class, 'exportSimple'])->name('rekapan.export');

    // Notifikasi
    Route::controller(NotifikasiController::class)->prefix('admin/notifikasi')->group(function () {
        Route::get('/', 'index')->name('admin.notifikasi');
        Route::get('/dropdown', 'showNotifikasi')->name('admin.notifikasi.dropdown');
        Route::get('/count', 'getCount')->name('admin.notifikasi.count');
        Route::post('/{id}/read', 'markAsRead')->name('admin.notifikasi.mark-read');
        Route::post('/{id_notif}/terima-laporan/{id_laporan}', 'terimaLaporan')->name('admin.notifikasi.terima-laporan');

        // Route untuk AJAX
        Route::get('/notifikasi/count', 'getCount')->name('notifikasi.count');
        Route::get('/notifikasi/get', 'getNotifikasi')->name('notifikasi.get');
    });

    // Ganti Password (Dari dalam aplikasi)
    Route::controller(GantiSandiController::class)->prefix('reset-password')->group(function () {
        Route::get('/', 'showResetForm')->name('password.reset.form');
        Route::post('/confirm', 'confirm')->name('password.confirm');
        Route::post('/reset', 'reset')->name('password.reset');
    });
    Route::post('/password/close-modal', [GantiSandiController::class, 'closeModal'])->name('password.close-modal');

    // Chat Konsultasi
    Route::controller(ChatController::class)->prefix('chat')->group(function () {
        Route::get('/', 'index')->name('chat.index');
        Route::post('/send', 'send')->name('chat.send');
        Route::get('/fetch/{userId}', 'fetchMessages')->name('chat.fetch');
        Route::get('/{userId}', 'show')->name('chat.show');
    });

    // =======================
    // ROUTE ADMIN DINSOS (PJSRK)
    // =======================
    Route::middleware(['role:admin_dinsos'])
        ->prefix('dinsos')
        ->name('dinsos.')
        ->group(function () {
            Route::get('/dashboard', [DinsosDashboardController::class, 'index'])->name('dashboard');

            Route::get('/laporan', [DinsosLaporanController::class, 'index'])->name('laporan.index');
            Route::get('/layanan/{layanan}', [DinsosLaporanController::class, 'layanan'])->name('layanan');
            Route::get('/laporan/{id}', [DinsosLaporanController::class, 'show'])->name('laporan.show');
            Route::put('/laporan/{id}/verifikasi', [DinsosLaporanController::class, 'verifikasi'])->name('laporan.verifikasi');
            Route::put('/laporan/{id}/proses', [DinsosLaporanController::class, 'proses'])->name('laporan.proses');
            Route::put('/laporan/{id}/selesai', [DinsosLaporanController::class, 'selesai'])->name('laporan.selesai');
            Route::put('/laporan/{id}/tolak', [DinsosLaporanController::class, 'tolak'])->name('laporan.tolak');
        });

    // =======================
    // ROUTE SUPERADMIN
    // =======================
    Route::middleware(['role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');

        // Akun
        Route::post('/akun/{id}/reset-password', [SuperadminAkunController::class, 'resetPassword'])->name('akun.reset-password');
        Route::post('/akun/{id}/toggle-status', [SuperadminAkunController::class, 'toggleStatus'])->name('akun.toggle-status');
        Route::resource('akun', SuperadminAkunController::class);

        // Role & Permission
        Route::resource('roles', SuperadminRoleController::class);

        // Konfigurasi Sistem
        Route::get('/settings', [SuperadminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SuperadminSettingController::class, 'update'])->name('settings.update');

        // Monitoring & Audit Laporan Global
        Route::get('/laporan', [SuperadminLaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/{id}', [SuperadminLaporanController::class, 'show'])->name('laporan.show');
    });

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('index');
    })->name('logout');
});

// =======================
// ROUTE UMUM (WEB MIDDLEWARE)
// =======================
Route::middleware('web')->group(function () {
    // OTP
    Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send.otp');
    Route::get('/otp', [OtpController::class, 'showOtpForm'])->name('otp.form');
    Route::post('/otp', [OtpController::class, 'verifyOtp'])->name('otp.verify');

    // Reset Password setelah OTP
    Route::get('/new-password', [ResetPasswordController::class, 'showNewPasswordForm'])->name('password.new');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('otp.reset');
});
