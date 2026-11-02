<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class Akun extends Authenticatable
{
    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $timestamps = true;

    protected $fillable = [
        'notelp',
        'nama',
        'email',
        'jenis_kelamin',
        'alamat',
        'password',
        'role',
        'is_active',
        'emerquest',
        'answquest',
        'otp',
        'otp_expiry',
    ];

    protected $casts = [
        'otp_expiry' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role', 'name');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isAdminPPA(): bool
    {
        return in_array($this->role, ['admin_ppa', 'admin'], true);
    }

    public function isAdminDinsos(): bool
    {
        return $this->role === 'admin_dinsos';
    }

    public function isAdminRole(): bool
    {
        return in_array($this->role, ['superadmin', 'admin', 'admin_ppa', 'admin_dinsos'], true);
    }

    public function getRoleDisplayNameAttribute(): string
    {
        return match ($this->role) {
            'superadmin' => 'Superadmin',
            'admin_ppa', 'admin' => 'Admin PPA',
            'admin_dinsos' => 'Admin Dinsos',
            'user' => 'User / Pelapor',
            'guest' => 'Guest',
            default => ucfirst(str_replace('_', ' ', $this->role)),
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
