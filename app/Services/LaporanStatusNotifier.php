<?php

namespace App\Services;

use App\Models\Laporan;
use App\Models\Notifikasi;
use App\Models\UserMobile;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

/**
 * Mengirim notifikasi in-app (tabel `notifikasi`) dan push FCM (best-effort)
 * ke pelapor mobile setiap kali status laporan berubah dari panel Web Admin.
 *
 * Laporan anonim (`role = guest` / `anonim@sentra.local`) dilewati karena
 * tidak memiliki akun pelapor aktif di aplikasi mobile.
 */
class LaporanStatusNotifier
{
    public static function notifyStatusChanged(
        Laporan $laporan,
        ?string $statusLama,
        string $statusBaru
    ): void {
        if ($statusLama === $statusBaru) {
            return;
        }

        if (! $laporan->id_akun || $laporan->isAnonim()) {
            return;
        }

        $idLaporan = (string) $laporan->id_laporan;
        [$title, $message] = self::resolveContent($idLaporan, $statusBaru);

        if ($title === '') {
            return;
        }

        try {
            Notifikasi::create([
                'id_akun' => $laporan->id_akun,
                'judul' => $idLaporan . ' - ' . $title,
                'pesan' => $message,
                'tipe' => 'user',
                'status' => 'terkirim',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal menyimpan notifikasi status laporan: ' . $e->getMessage(), [
                'id_laporan' => $idLaporan,
                'status' => $statusBaru,
            ]);
            return;
        }

        self::sendFcmBestEffort((int) $laporan->id_akun, $title, $message, $idLaporan);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function resolveContent(string $idLaporan, string $status): array
    {
        return match ($status) {
            'dikirim' => [
                'Laporan Terkirim',
                "Laporan Anda dengan ID {$idLaporan} telah berhasil dikirim.",
            ],
            'diterima' => [
                'Laporan Diterima',
                "Laporan Anda dengan ID {$idLaporan} telah diterima oleh petugas PPA.",
            ],
            'diproses' => [
                'Laporan Dalam Proses',
                "Laporan Anda dengan ID {$idLaporan} sedang diproses.",
            ],
            'selesai' => [
                'Laporan Selesai',
                "Laporan Anda dengan ID {$idLaporan} telah selesai diproses.",
            ],
            'dirujuk' => [
                'Laporan Dirujuk',
                "Laporan Anda dengan ID {$idLaporan} telah dirujuk ke instansi terkait.",
            ],
            'ditolak' => [
                'Laporan Ditolak',
                "Laporan Anda dengan ID {$idLaporan} tidak dapat diproses lebih lanjut.",
            ],
            default => ['', ''],
        };
    }

    private static function sendFcmBestEffort(
        int $userId,
        string $title,
        string $body,
        string $idLaporan
    ): void {
        $credentialsPath = (string) config('services.firebase.credentials', '');
        if ($credentialsPath === '' || ! is_file($credentialsPath)) {
            return;
        }

        try {
            $user = UserMobile::find($userId);
            if (! $user || empty($user->fcm_token)) {
                return;
            }

            $serviceAccount = json_decode((string) file_get_contents($credentialsPath), true);
            if (! is_array($serviceAccount) || empty($serviceAccount['client_email']) || empty($serviceAccount['private_key'])) {
                return;
            }

            $projectId = config('services.firebase.project_id')
                ?: ($serviceAccount['project_id'] ?? 'sentra-3e7a6');
            $now = Carbon::now()->timestamp;
            $jwt = JWT::encode([
                'iss' => $serviceAccount['client_email'],
                'sub' => $serviceAccount['client_email'],
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            ], $serviceAccount['private_key'], 'RS256');

            $client = new Client(['timeout' => 5]);
            $tokenResp = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ],
            ]);
            $tokenData = json_decode((string) $tokenResp->getBody(), true);
            $accessToken = $tokenData['access_token'] ?? null;

            if (! $accessToken) {
                return;
            }

            $client->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'token' => $user->fcm_token,
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => [
                            'notification_type' => 'report',
                            'id_laporan' => $idLaporan,
                        ],
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning("FCM best-effort gagal untuk user {$userId}: " . $e->getMessage());
        }
    }
}
