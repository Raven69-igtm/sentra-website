<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $table = 'laporan';
    protected $primaryKey = 'id_laporan';
    protected $keyType = 'string';
    public $incrementing = false; 
    public $timestamps = true;

        protected $fillable = [
        'id_laporan',
        'id_akun',
        'kategori',
        'status',
    ];

    public function detail_pelapor()
    {
        return $this->hasOne(DetailPelapor::class, 'id_laporan', 'id_laporan');
    }

    
    public function detail_penerima_manfaat()
    {
        return $this->hasOne(DetailPenerimaManfaat::class, 'id_laporan', 'id_laporan');
    }

    
    public function detail_terlapor()
    {
        return $this->hasOne(DetailTerlapor::class, 'id_laporan', 'id_laporan');
    }

    
    public function detail_kasus()
    {
        return $this->hasOne(DetailKasus::class, 'id_laporan', 'id_laporan');
    }

    public function scopeForUser($query, $user)
    {
        if (!$user || (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())) {
            return $query;
        }

        if (method_exists($user, 'isAdminPPA') && $user->isAdminPPA()) {
            return $query->whereIn('kategori', [
                'Kekerasan Fisik',
                'Kekerasan Psikis',
                'Kekerasan Seksual',
                'Eksploitasi',
                'TPPO'
            ]);
        }

        if (method_exists($user, 'isAdminDinsos') && $user->isAdminDinsos()) {
            return $query->where(function ($q) {
                $q->whereIn('kategori', ['Penelantaran', 'Lainnya'])
                  ->orWhere('status', 'dirujuk');
            });
        }

        // Default untuk user pelapor: laporan miliknya sendiri
        return $query->where('id_akun', $user->id_akun ?? 0);
    }

}


