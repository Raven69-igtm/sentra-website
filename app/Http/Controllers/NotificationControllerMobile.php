<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserMobile;
use App\Models\Laporan;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;

class NotificationControllerMobile extends Controller
{
    protected $fcmUrl = 'https://fcm.googleapis.com/v1/projects/sentra-3e7a6/messages:send';
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    // Fungsi untuk menghasilkan JWT
    protected function generateJWT()
    {
        $serviceAccount = json_decode(file_get_contents(config('services.firebase.credentials')), true);

        $now = Carbon::now()->timestamp;
        $payload = [
            'iss' => $serviceAccount['client_email'],
            'sub' => $serviceAccount['client_email'],
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ];

        $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

        // Mendapatkan access token
        $response = $this->client->post('https://oauth2.googleapis.com/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    protected function sendFCMNotification($userId, $title, $body, $notificationType, $idLaporan = '')
    {
        $user = UserMobile::find($userId);
        if (!$user || !$user->fcm_token) {
            \Log::error("FCM token tidak ditemukan untuk user $userId");
            return;
        }

        $payload = [
            'message' => [
                'token' => $user->fcm_token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => [
                    'notification_type' => $notificationType,
                    'id_laporan' => (string) $idLaporan,
                ],
            ],
        ];

        try {
            $accessToken = $this->generateJWT();
            $response = $this->client->post($this->fcmUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            \Log::info("FCM notifikasi terkirim ke user $userId: " . $response->getBody());
        } catch (RequestException $e) {
            \Log::error("Gagal kirim FCM ke user $userId: " . $e->getMessage());
        }
    }

    public function newMessage(Request $request)
    {
        $validated = $request->validate([
            'sender_id' => 'required|exists:akun,id_akun',
            'receiver_id' => 'required|exists:akun,id_akun',
            'message' => 'required|string',
        ]);

        $this->sendFCMNotification(
            $validated['receiver_id'],
            'Pesan Baru',
            "{$validated['message']}",
            'chat'
        );

        return response()->json(['message' => 'Notifikasi dikirim']);
    }

    public function reportUpdate(Request $request)
    {
        $validated = $request->validate([
            'id_laporan' => 'required|exists:laporan,id',
            'user_id' => 'required|exists:akun,id_akun',
            'status' => 'required|in:dikirim,diterima,diproses,selesai',
        ]);

        $laporan = Laporan::find($validated['id_laporan']);
        if (!$laporan) {
            return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
        }

        $title = '';
        $message = '';

        switch ($validated['status']) {
            case 'dikirim':
                $title = 'Laporan Terkirim';
                $message = "Laporan Anda dengan ID {$validated['id_laporan']} telah berhasil dikirim.";
                break;
            case 'diterima':
                $title = 'Laporan Diterima';
                $message = "Laporan Anda dengan ID {$validated['id_laporan']} telah diterima oleh sistem.";
                break;
            case 'diproses':
                $title = 'Laporan Dalam Proses';
                $message = "Laporan Anda dengan ID {$validated['id_laporan']} sedang diproses.";
                break;
            case 'selesai':
                $title = 'Laporan Selesai';
                $message = "Laporan Anda dengan ID {$validated['id_laporan']} telah selesai diproses.";
                break;
        }

        $this->sendFCMNotification(
            $validated['user_id'],
            $title,
            $message,
            'report',
            $validated['id_laporan']
        );

        return response()->json(['message' => 'Notifikasi laporan dikirim']);
    }

    public function updateFCMToken(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:akun,id_akun',
            'fcm_token' => 'required|string',
        ]);

        $user = UserMobile::find($validated['user_id']);
        $user->fcm_token = $validated['fcm_token'];
        $user->save();

        return response()->json(['message' => 'FCM token updated']);
    }

    // protected function sendFCMNotification($userId, $title, $body, $notificationType, $idLaporan = '')
    // {
    //     $user = UserMobile::find($userId);
    //     if (!$user || !$user->fcm_token) {
    //         \Log::error("FCM token tidak ditemukan untuk user $userId");
    //         return;
    //     }

    //     $payload = [
    //         'to' => $user->fcm_token,
    //         'notification' => [
    //             'title' => $title,
    //             'body' => $body,
    //         ],
    //         'data' => [
    //             'notification_type' => $notificationType,
    //             'id_laporan' => (string) $idLaporan,
    //         ],
    //     ];

    //     try {
    //         $response = $this->client->post($this->fcmUrl, [
    //             'headers' => [
    //                 'Authorization' => 'key=' . config('services.firebase.server_key'),
    //                 'Content-Type' => 'application/json',
    //             ],
    //             'json' => $payload,
    //         ]);

    //         \Log::info("FCM notifikasi terkirim ke user $userId: " . $response->getBody());
    //     } catch (RequestException $e) {
    //         \Log::error("Gagal kirim FCM ke user $userId: " . $e->getMessage());
    //     }
    // }
}