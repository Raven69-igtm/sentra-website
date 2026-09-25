<?php
use App\Http\Controllers\AkunControllerMobile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControllerMobile;
use App\Http\Controllers\LaporanControllerMobile;
use App\Http\Controllers\ChatControllerMobile;
use App\Http\Controllers\NotificationControllerMobile;
use App\Http\Controllers\InformasiController;




Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('auth/getdata', [AkunControllerMobile::class, 'index']);
Route::post('auth/register', [AkunControllerMobile::class, 'register']);
Route::post('auth/login', [AkunControllerMobile::class, 'login']);
Route::post('auth/biometric', [AkunControllerMobile::class, 'loginBiometric']);
Route::post('auth/email', [AkunControllerMobile::class, 'loginWithEmail']);
Route::put('auth/edit/{id}', [AkunControllerMobile::class, 'update']);
Route::delete('auth/delete/{id}', [AkunControllerMobile::class, 'delete']);
Route::post('auth/updatenomor', [AkunControllerMobile::class, 'updatenomor']);
Route::post('auth/ubahpassword', [AkunControllerMobile::class, 'updatepassword']);
Route::post('auth/get-emergency-question', [AkunControllerMobile::class, 'getEmergencyQuestion']);
Route::post('auth/emergency-check', [AkunControllerMobile::class, 'emergencyCheck']);


Route::post('/login', [AuthControllerMobile::class, 'login']);
Route::post('/register', [AuthControllerMobile::class, 'register']);
Route::post('/forgot-password', [AuthControllerMobile::class, 'forgotPassword']);
Route::post('/reset-password', [AuthControllerMobile::class, 'resetPassword']);


Route::post('/track-report', [LaporanControllerMobile::class, 'trackReport']);
Route::post('/post-report', [LaporanControllerMobile::class, 'postReport']);
Route::get('/search', [LaporanControllerMobile::class, 'Search']);
Route::get('/report-statistics', [LaporanControllerMobile::class, 'reportStatistics']);
Route::get('/user-reports/{id_akun}', [LaporanControllerMobile::class, 'getUserReports']);
Route::get('/user-reports-by-category/{id_akun}/{status}', [LaporanControllerMobile::class, 'getUserReportsByCategory']);
Route::post('/laporan-cepat', [LaporanControllerMobile::class, 'quickAccess']);


Route::post('/chats', [ChatControllerMobile::class, 'store']);
Route::put('/chats/{chatId}/read', [ChatControllerMobile::class, 'markAsRead']);
Route::put('/chat/update/{chatId}', [ChatControllerMobile::class, 'update']);
Route::delete('/chat/delete/{chatId}', [ChatControllerMobile::class, 'destroy']);
Route::delete('/chats/{senderId}/{receiverId}', [ChatControllerMobile::class, 'destroyConversation']);
Route::get('/chats/{userId}', [ChatControllerMobile::class, 'getChats']);
Route::get('/conversations/{userId}', [ChatControllerMobile::class, 'getConversations']);

Route::post('/new-message', [NotificationControllerMobile::class, 'newMessage']);
Route::post('/report-update', [NotificationControllerMobile::class, 'reportUpdate']);
Route::post('/update-fcm-token', [NotificationControllerMobile::class, 'updateFCMToken']);

Route::get('/informasi', [InformasiController::class, 'apiIndex']);