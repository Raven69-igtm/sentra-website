<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Catatan metadata aktivitas administrator/sistem.
 *
 * Hanya menyimpan metadata (siapa, kapan, aktivitas apa, referensi teknis).
 * TIDAK menyimpan isi laporan, identitas pelapor/korban, dokumen, atau lampiran.
 */
class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    protected $fillable = [
        'akun_id',
        'nama',
        'role',
        'instansi',
        'activity_type',
        'module',
        'reference',
        'description',
        'status',
        'ip_address',
        'user_agent',
    ];

    public const TYPES = [
        'login'          => 'Login Sistem',
        'logout'         => 'Logout Sistem',
        'laporan_status' => 'Perubahan Status Laporan',
        'dokumen'        => 'Pengunggahan Dokumen',
        'data'           => 'Perubahan Data Administratif',
    ];

    /**
     * Catat aktivitas ke tabel activity_logs.
     * Kegagalan pencatatan sengaja diabaikan agar tidak mengganggu alur utama.
     */
    public static function record(
        string $type,
        string $description,
        ?string $module = null,
        ?string $reference = null,
        string $status = 'berhasil',
        $user = null
    ): void {
        try {
            $user = $user ?? Auth::user();

            static::create([
                'akun_id'      => $user->id_akun ?? null,
                'nama'         => $user->nama ?? 'Tidak Dikenal',
                'role'         => $user->role ?? 'tidak_dikenal',
                'instansi'     => $user ? self::instansiForRole($user->role) : 'Tidak Dikenal',
                'activity_type' => $type,
                'module'       => $module,
                'reference'    => $reference,
                'description'  => $description,
                'status'       => $status,
                'ip_address'   => request()?->ip(),
                'user_agent'   => mb_substr((string) request()?->userAgent(), 0, 255),
            ]);
        } catch (\Throwable $e) {
            // Abaikan: logging tidak boleh memutus alur utama aplikasi.
        }
    }

    /**
     * Pemetaan role ke label instansi.
     */
    public static function instansiForRole(?string $role): string
    {
        return match ($role) {
            'admin', 'admin_ppa' => 'UPTD PPA',
            'admin_dinsos'       => 'Dinas Sosial',
            'superadmin'         => 'Superadmin',
            default              => 'Masyarakat',
        };
    }
}
