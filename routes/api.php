<?php
use App\Http\Controllers\AkunControllerMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllerMobile;
use App\Http\Controllers\LaporanControllerMobile;
use App\Http\Controllers\ChatControllerMobile;
use App\Http\Controllers\NotificationControllerMobile;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\PjsrkControllerMobile;




Route::middleware(['auth:sanctum', 'mobile.account'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('auth/biometric', [AkunControllerMobile::class, 'loginBiometric']);
    Route::put('auth/edit/{id}', [AkunControllerMobile::class, 'update']);
    Route::delete('auth/delete/{id}', [AkunControllerMobile::class, 'delete']);
    Route::post('auth/updatenomor', [AkunControllerMobile::class, 'updatenomor']);
    Route::post('auth/ubahpassword', [AkunControllerMobile::class, 'updatepassword']);

    // Laporan & Riwayat milik akun mobile yang sedang login.
    Route::post('/post-report', [LaporanControllerMobile::class, 'postReport']);
    Route::get('/search', [LaporanControllerMobile::class, 'Search']);
    Route::get('/user-reports/{id_akun}', [LaporanControllerMobile::class, 'getUserReports']);
    Route::get('/user-reports-by-category/{id_akun}/{status}', [LaporanControllerMobile::class, 'getUserReportsByCategory']);

    // Pengaduan layanan PJSRK terautentikasi.
    Route::post('/pjsrk/bencana', [PjsrkControllerMobile::class, 'storeBencana']);
    Route::post('/pjsrk/rehabilitasi', [PjsrkControllerMobile::class, 'storeRehabilitasi']);
    Route::post('/pjsrk/permasalahan-sosial', [PjsrkControllerMobile::class, 'storePermasalahanSosial']);

    // Notifikasi & FCM milik akun mobile yang sedang login.
    Route::get('/notifications', [NotificationControllerMobile::class, 'index']);
    Route::patch('/notifications/{id}/read', [NotificationControllerMobile::class, 'markRead']);
    Route::delete('/notifications/{id}', [NotificationControllerMobile::class, 'destroy']);
    Route::post('/notifications/restore', [NotificationControllerMobile::class, 'restore']);
    Route::post('/update-fcm-token', [NotificationControllerMobile::class, 'updateFCMToken']);
    Route::post('/new-message', [NotificationControllerMobile::class, 'newMessage']);

    // Chat konsultasi mobile. Wajib login agar pengirim diambil dari token
    // Sanctum dan kepemilikan pesan dapat diverifikasi.
    Route::post('/chats', [ChatControllerMobile::class, 'store']);
    Route::put('/chats/{chatId}/read', [ChatControllerMobile::class, 'markAsRead']);
    Route::put('/chat/update/{chatId}', [ChatControllerMobile::class, 'update']);
    Route::delete('/chat/delete/{chatId}', [ChatControllerMobile::class, 'destroy']);
    Route::delete('/chats/{senderId}/{receiverId}', [ChatControllerMobile::class, 'destroyConversation']);
    Route::get('/chats/{userId}', [ChatControllerMobile::class, 'getChats']);
    Route::get('/conversations/{userId}', [ChatControllerMobile::class, 'getConversations']);
});

// Pembaruan notifikasi status laporan memerlukan token Sanctum (admin atau pemilik laporan).
Route::middleware('auth:sanctum')->post('/report-update', [NotificationControllerMobile::class, 'reportUpdate']);

// Endpoint autentikasi publik (dilindungi rate limiting).
Route::middleware('throttle:15,1')->group(function () {
    Route::post('auth/register', [AkunControllerMobile::class, 'register']);
    Route::post('auth/login', [AkunControllerMobile::class, 'login']);
    Route::post('auth/email', [AkunControllerMobile::class, 'loginWithEmail']);
    Route::post('auth/get-emergency-question', [AkunControllerMobile::class, 'getEmergencyQuestion']);
    Route::post('auth/emergency-check', [AkunControllerMobile::class, 'emergencyCheck']);

    Route::post('/login', [AuthControllerMobile::class, 'login']);
    Route::post('/register', [AuthControllerMobile::class, 'register']);
    Route::post('/forgot-password', [AuthControllerMobile::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthControllerMobile::class, 'resetPassword']);
});

// Endpoint publik untuk pelaporan cepat (tanpa login) dan pelacakan tiket.
Route::middleware('throttle:20,1')->group(function () {
    Route::post('/track-report', [LaporanControllerMobile::class, 'trackReport']);
    Route::post('/laporan-cepat', [LaporanControllerMobile::class, 'quickAccess']);
    Route::post('/pjsrk/cepat', [PjsrkControllerMobile::class, 'storeCepat']);
});

Route::get('/report-statistics', [LaporanControllerMobile::class, 'reportStatistics']);
Route::get('/informasi', [InformasiController::class, 'apiIndex']);
Route::get('/informasi/{informasi}/gambar', [InformasiController::class, 'gambar']);