<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class UserMobile extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'akun'; // Nama tabel sesuai dengan database kamu
    protected $primaryKey = 'id_akun'; // Primary key yang digunakan

    protected $fillable = [
        'notelp',
        'nama',
        'email',
        'password',
        'role',
        'is_active',
        'emerquest',
        'answquest',
        'otp',
        'otp_expiry',
        'fcm_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'otp_expiry',
        'answquest',
        'fcm_token',
    ];

    public function canAccessMobile(): bool
    {
        return $this->is_active !== false
            && in_array($this->role, Akun::MOBILE_ROLES, true);
    }

    public function canAccessWeb(): bool
    {
        return $this->is_active !== false
            && in_array($this->role, Akun::WEB_ROLES, true);
    }

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expiry' => 'datetime',
    ];
}
