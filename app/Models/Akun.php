<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Akun extends Authenticatable
{
    use HasApiTokens;
    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $timestamps = true;

    public const ROLE_LABELS = [
        'superadmin'    => 'Superadmin',
        'admin_ppa'     => 'Admin PPA',
        'admin_dinsos'  => 'Admin Dinsos',
        'user'          => 'User / Pelapor',
        'guest'         => 'Guest',
        'Pelapor Cepat' => 'Guest / Pelapor Cepat',
    ];

    /**
     * Role yang boleh masuk ke panel web (admin).
     */
    public const WEB_ROLES = ['superadmin', 'admin_ppa', 'admin_dinsos'];

    /**
     * Role yang boleh masuk lewat aplikasi mobile.
     * 'Pelapor Cepat' adalah role legacy yang diperlakukan seperti guest.
     */
    public const MOBILE_ROLES = ['user', 'guest', 'Pelapor Cepat'];

    public const INSTANSI_OPTIONS = [
        'superadmin'   => 'Superadmin',
        'uptd_ppa'     => 'UPTD PPA',
        'dinas_sosial' => 'Dinas Sosial',
        'masyarakat'   => 'Masyarakat',
        'lainnya'      => 'Lainnya',
    ];

    protected $fillable = [
        'kode_akun',
        'notelp',
        'nama',
        'email',
        'jenis_kelamin',
        'alamat',
        'password',
        'role',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'last_logout_at',
        'emerquest',
        'answquest',
        'otp',
        'otp_expiry',
    ];

    protected $casts = [
        'otp_expiry' => 'datetime',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'last_logout_at' => 'datetime',
    ];

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    /**
     * ID yang ditampilkan ke pengguna: kode publik jika ada, selain itu
     * fallback ke primary key lama.
     */
    public function displayId(): string
    {
        return $this->kode_akun ?: (string) $this->getKey();
    }

    public function isAdminPPA(): bool
    {
        return $this->role === 'admin_ppa';
    }

    public function isAdminDinsos(): bool
    {
        return $this->role === 'admin_dinsos';
    }

    public function isAdminRole(): bool
    {
        return in_array($this->role, self::WEB_ROLES, true);
    }

    /**
     * Akun boleh mengakses panel web hanya jika role-nya role web dan aktif.
     */
    public function canAccessWeb(): bool
    {
        return $this->is_active !== false
            && in_array($this->role, self::WEB_ROLES, true);
    }

    /**
     * Akun boleh mengakses aplikasi mobile hanya jika role-nya role mobile
     * dan aktif.
     */
    public function canAccessMobile(): bool
    {
        return $this->is_active !== false
            && in_array($this->role, self::MOBILE_ROLES, true);
    }

    /**
     * Cabut seluruh token Sanctum milik akun ini, termasuk token yang dibuat
     * lewat model UserMobile (tokenable_type berbeda).
     */
    public function revokeAccessTokens(): void
    {
        \Illuminate\Support\Facades\DB::table('personal_access_tokens')
            ->whereIn('tokenable_type', [self::class, UserMobile::class])
            ->where('tokenable_id', $this->getKey())
            ->delete();
    }

    public function getRoleDisplayNameAttribute(): string
    {
        return self::roleLabel($this->role);
    }

    public static function roleLabel(?string $role): string
    {
        if ($role === null || $role === '') {
            return '-';
        }

        return self::ROLE_LABELS[$role] ?? ucfirst(str_replace('_', ' ', $role));
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
     * Key role yang termasuk kategori instansi tertentu.
     *
     * @return array<int, string>
     */
    public static function roleKeysForInstansi(string $instansi): array
    {
        return match ($instansi) {
            'superadmin'   => ['superadmin'],
            'uptd_ppa'     => ['admin_ppa', 'admin'],
            'dinas_sosial' => ['admin_dinsos'],
            'masyarakat'   => ['user', 'guest', 'Pelapor Cepat'],
            default        => [],
        };
    }

    public static function instansiLabel(?string $role): string
    {
        return match ($role) {
            'superadmin'              => 'Superadmin',
            'admin_ppa'               => 'UPTD PPA',
            'admin_dinsos'            => 'Dinas Sosial',
            'user', 'guest', 'Pelapor Cepat' => 'Masyarakat',
            default                   => 'Lainnya',
        };
    }

    public function hasRole(string|array $roles): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    public function hasPermission(string $permission): bool
    {
        // Superadmin bypasses all permission checks
        if ($this->isSuperAdmin()) {
            return true;
        }

        $role = $this->roleRelation;
        if (!$role) {
            return false;
        }

        return $role->permissions()->where('permissions.name', $permission)->exists();
    }

    public function setPasswordAttribute($value)
    {
        // Hanya hash kalau password-nya memang belum di-hash
        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    public function getNotelpAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setNotelpAttribute($value)
    {
        $this->attributes['notelp'] = Crypt::encryptString($value);
    }
}
