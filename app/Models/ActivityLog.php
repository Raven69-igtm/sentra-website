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

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'akun_id', 'id_akun');
    }

    public const TYPES = [
        'login'          => 'Login Sistem',
        'logout'         => 'Logout Sistem',
        'laporan_status' => 'Perubahan Status Laporan',
        'dokumen'        => 'Pengunggahan Dokumen',
        'data'           => 'Perubahan Data Administratif',
    ];

    public const ROLE_LABELS = [
        'superadmin'    => 'Superadmin',
        'admin'         => 'Admin PPA',
        'admin_ppa'     => 'Admin PPA',
        'admin_dinsos'  => 'Admin Dinsos',
        'user'          => 'User / Pelapor',
        'guest'         => 'Guest',
        'Pelapor Cepat' => 'Guest / Pelapor Cepat',
    ];

    public const STATUSES = [
        'berhasil' => 'Berhasil',
        'gagal'    => 'Gagal',
    ];

    public static function typeLabel(?string $type): string
    {
        if ($type === null || $type === '') {
            return '-';
        }

        return self::TYPES[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    public static function roleLabel(?string $role): string
    {
        if ($role === null || $role === '') {
            return '-';
        }

        return self::ROLE_LABELS[$role] ?? ucfirst(str_replace('_', ' ', $role));
    }

    public static function statusLabel(?string $status): string
    {
        if ($status === null || $status === '') {
            return '-';
        }

        return self::STATUSES[$status] ?? ucfirst($status);
    }

    public static function moduleLabel(?string $module): string
    {
        if ($module === null || $module === '') {
            return '-';
        }

        return ucfirst(str_replace('_', ' ', $module));
    }

    /**
     * Key activity_type yang labelnya cocok dengan keyword.
     *
     * @return array<int, string>
     */
    public static function typeKeysForKeyword(string $keyword): array
    {
        $needle = mb_strtolower($keyword);

        return array_values(array_filter(array_keys(self::TYPES), function (string $key) use ($needle) {
            return str_contains(mb_strtolower($key), $needle)
                || str_contains(mb_strtolower(self::TYPES[$key]), $needle);
        }));
    }

    /**
     * Key module yang key/labelnya cocok dengan keyword.
     *
     * @return array<int, string>
     */
    public static function moduleKeysForKeyword(string $keyword): array
    {
        $needle = mb_strtolower($keyword);
        $slugNeedle = str_replace([' ', '-'], '_', $needle);

        $modules = self::query()
            ->whereNotNull('module')
            ->distinct()
            ->pluck('module');

        return $modules
            ->filter(function ($module) use ($needle, $slugNeedle) {
                $module = (string) $module;

                return str_contains(mb_strtolower($module), $needle)
                    || str_contains(mb_strtolower($module), $slugNeedle)
                    || str_contains(mb_strtolower(self::moduleLabel($module)), $needle);
            })
            ->values()
            ->all();
    }

    /**
     * Key role yang labelnya cocok dengan keyword.
     *
     * @return array<int, string>
     */
    public static function roleKeysForKeyword(string $keyword): array
    {
        $needle = mb_strtolower($keyword);
        $slugNeedle = str_replace([' ', '-'], '_', $needle);

        $keys = [];

        foreach (self::ROLE_LABELS as $key => $label) {
            if (str_contains(mb_strtolower($key), $needle)
                || str_contains(mb_strtolower($label), $needle)
                || str_contains(mb_strtolower($key), $slugNeedle)) {
                $keys[] = $key;
            }
        }

        return array_values(array_unique($keys));
    }

    /**
     * Key status yang labelnya cocok dengan keyword.
     *
     * @return array<int, string>
     */
    public static function statusKeysForKeyword(string $keyword): array
    {
        $needle = mb_strtolower($keyword);

        return array_values(array_filter(array_keys(self::STATUSES), function (string $key) use ($needle) {
            return str_contains(mb_strtolower($key), $needle)
                || str_contains(mb_strtolower(self::STATUSES[$key]), $needle);
        }));
    }

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
