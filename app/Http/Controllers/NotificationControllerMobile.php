<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserMobile;
use App\Models\Laporan;
use App\Models\Notifikasi;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NotificationControllerMobile extends Controller
{
    protected $fcmUrl;
    protected $client;

    public function __construct()
    {
        $projectId = config('services.firebase.project_id') ?: 'sentra-3e7a6';
        $this->fcmUrl = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
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
        $authUser = $request->user('sanctum');

        $validated = $request->validate([
            'sender_id' => $authUser ? 'nullable|exists:akun,id_akun' : 'required|exists:akun,id_akun',
            'receiver_id' => 'required|exists:akun,id_akun',
            'message' => 'required|string',
        ]);

        if ($authUser && isset($validated['sender_id'])
            && (int) $validated['sender_id'] !== (int) $authUser->id_akun) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh mengirim notifikasi atas nama akun lain.',
            ], 403);
        }

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
        $authUser = $request->user('sanctum') ?? $request->user();
        if (!$authUser) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $validated = $request->validate([
            'id_laporan' => 'required|exists:laporan,id_laporan',
            'user_id' => 'required|exists:akun,id_akun',
            'status' => 'required|in:dikirim,diterima,diproses,selesai,ditolak,dirujuk',
        ]);

        $laporan = Laporan::find($validated['id_laporan']);
        if (!$laporan) {
            return response()->json(['error' => 'Laporan tidak ditemukan'], 404);
        }

        $isAdmin = method_exists($authUser, 'canAccessWeb')
            ? $authUser->canAccessWeb()
            : in_array($authUser->role ?? null, \App\Models\Akun::WEB_ROLES, true);

        if (!$isAdmin && (int) $laporan->id_akun !== (int) $authUser->id_akun) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak berhak mengirim pembaruan status laporan ini.',
            ], 403);
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
            case 'ditolak':
                $title = 'Laporan Ditolak';
                $message = "Laporan Anda dengan ID {$validated['id_laporan']} tidak dapat diproses lebih lanjut.";
                break;
            case 'dirujuk':
                $title = 'Laporan Dirujuk';
                $message = "Laporan Anda dengan ID {$validated['id_laporan']} telah dirujuk ke instansi terkait.";
                break;
        }

        Notifikasi::create([
            'id_akun' => $validated['user_id'],
            'judul' => $validated['id_laporan'] . ' - ' . $title,
            'pesan' => $message,
            'tipe' => 'user',
            'status' => 'terkirim',
        ]);

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
        $authUser = $request->user('sanctum') ?? $request->user();
        if (!$authUser) {
            return response()->json(['message' => 'Tidak terautentikasi.'], 401);
        }

        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:akun,id_akun',
            'fcm_token' => 'required|string',
        ]);

        if (isset($validated['user_id']) && (int) $validated['user_id'] !== (int) $authUser->id_akun) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh mengubah FCM token akun lain.',
            ], 403);
        }

        $user = UserMobile::find($authUser->id_akun);
        $user->fcm_token = $validated['fcm_token'];
        $user->save();

        return response()->json(['message' => 'FCM token updated']);
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|integer|exists:akun,id_akun',
        ]);

        $tokenUser = $request->user('sanctum');
        $userId = $tokenUser?->id_akun ?? ($validated['user_id'] ?? null);

        if (!$userId) {
            return response()->json([
                'success' => false,
                'message' => 'user_id wajib diisi atau sertakan token login.',
            ], 422);
        }

        if ($tokenUser && isset($validated['user_id'])
            && (int) $validated['user_id'] !== (int) $tokenUser->id_akun) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh mengakses notifikasi akun lain.',
            ], 403);
        }

        $rows = Notifikasi::where('id_akun', $userId)
            ->orderByDesc('created_at')
            ->orderByDesc('id_notif')
            ->limit(200)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $this->formatNotifications($rows),
        ]);
    }

    public function markRead(Request $request, $id)
    {
        $notifikasi = Notifikasi::find($id);

        if (!$notifikasi) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        if (($forbidden = $this->tolakAkunLain($request, $notifikasi->id_akun))) {
            return $forbidden;
        }

        $notifikasi->status = 'dibaca';
        $notifikasi->save();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi ditandai sudah dibaca.',
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $notifikasi = Notifikasi::find($id);

        if (!$notifikasi) {
            return response()->json([
                'success' => false,
                'message' => 'Notifikasi tidak ditemukan.',
            ], 404);
        }

        if (($forbidden = $this->tolakAkunLain($request, $notifikasi->id_akun))) {
            return $forbidden;
        }

        $notifikasi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi dihapus.',
        ]);
    }

    public function restore(Request $request)
    {
        $validated = $request->validate([
            'id_notif' => 'nullable',
            'id_akun' => 'required|exists:akun,id_akun',
            'judul' => 'required|string|max:100',
            'pesan' => 'required|string|max:255',
            'tipe' => 'required|in:admin,user',
            'status' => 'nullable|in:terkirim,dibaca',
            'created_at' => 'nullable|date',
        ]);

        if (($forbidden = $this->tolakAkunLain($request, $validated['id_akun']))) {
            return $forbidden;
        }

        $idNotif = isset($validated['id_notif']) && ctype_digit((string) $validated['id_notif'])
            ? (int) $validated['id_notif']
            : null;

        $data = [
            'id_akun' => $validated['id_akun'],
            'judul' => $validated['judul'],
            'pesan' => $validated['pesan'],
            'tipe' => $validated['tipe'],
            'status' => $validated['status'] ?? 'terkirim',
        ];

        $createdAt = isset($validated['created_at'])
            ? Carbon::parse($validated['created_at'])->toDateTimeString()
            : now()->toDateTimeString();

        $existing = $idNotif ? Notifikasi::find($idNotif) : null;

        if ($existing) {
            if ((int) $existing->id_akun !== (int) $validated['id_akun']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi milik akun lain tidak bisa dipulihkan.',
                ], 403);
            }
            $existing->fill($data);
            $existing->save();
            $idNotif = $existing->id_notif;
        } elseif ($idNotif) {
            DB::table('notifikasi')->insert($data + [
                'id_notif' => $idNotif,
                'created_at' => $createdAt,
            ]);
        } else {
            $baru = Notifikasi::create($data);
            $idNotif = $baru->id_notif;
        }

        return response()->json([
            'success' => true,
            'message' => 'Notifikasi dipulihkan.',
            'data' => ['id_notif' => $idNotif],
        ]);
    }

    /**
     * Notifikasi disimpan di tabel yang sama untuk PPA, PJSRK, dan chat.
     * Sumber dibedakan dari laporan yang dirujuk judul/pesan, bukan tabel terpisah.
     */
    protected function formatNotifications($rows)
    {
        $idLaporanPerNotif = [];
        $idLaporanUnik = [];

        foreach ($rows as $row) {
            $idLaporan = $this->extractIdLaporan($row->judul, $row->pesan);
            $idLaporanPerNotif[$row->id_notif] = $idLaporan;
            if ($idLaporan) {
                $idLaporanUnik[] = $idLaporan;
            }
        }

        $layananPjsrk = empty($idLaporanUnik)
            ? collect()
            : DB::table('laporan')
                ->whereIn('id_laporan', array_unique($idLaporanUnik))
                ->pluck('layanan_pjsrk', 'id_laporan');

        return $rows->map(function ($row) use ($idLaporanPerNotif, $layananPjsrk) {
            $idLaporan = $idLaporanPerNotif[$row->id_notif];
            $source = null;

            if ($idLaporan && $layananPjsrk->has($idLaporan)) {
                $source = $layananPjsrk[$idLaporan] ? 'PJSRK' : 'PPA';
            } elseif (stripos($row->judul, 'Pesan Baru') !== false) {
                $source = 'Pesan';
            }

            return [
                'id_notif' => $row->id_notif,
                'id_akun' => $row->id_akun,
                'judul' => $row->judul,
                'pesan' => $row->pesan,
                'tipe' => $row->tipe,
                'status' => $row->status,
                'source' => $source,
                'id_laporan' => $idLaporan,
                'created_at' => optional($row->created_at)->toIso8601String(),
            ];
        })->values();
    }

    protected function extractIdLaporan(?string $judul, ?string $pesan): ?string
    {
        if ($judul && preg_match('/^(RKN\d+)\s*-/', $judul, $m)) {
            return $m[1];
        }

        if ($pesan && preg_match('/\b(RKN\d+)\b/', $pesan, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function tolakAkunLain(Request $request, $idAkun)
    {
        $tokenUser = $request->user('sanctum');

        if ($tokenUser && (int) $tokenUser->id_akun !== (int) $idAkun) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak boleh mengakses notifikasi akun lain.',
            ], 403);
        }

        return null;
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