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

    public const PPA_CATEGORIES = [
        'Kekerasan Fisik',
        'Kekerasan Psikis',
        'Kekerasan Seksual',
        'Eksploitasi',
        'TPPO',
    ];

    public const DINSOS_CATEGORIES = [
        'Penelantaran',
        'Lainnya',
    ];

    public const LAYANAN_PJSRK = [
        'rehabilitasi_sosial' => 'Rehabilitasi Sosial',
        'pengaduan_bencana' => 'Pengaduan Bencana',
        'permasalahan_sosial' => 'Permasalahan Sosial',
    ];

    protected $fillable = [
        'id_laporan',
        'id_akun',
        'judul',
        'kecamatan',
        'kategori',
        'layanan_pjsrk',
        'status',
        'alasan_penolakan',
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
            return $query->whereIn('kategori', self::PPA_CATEGORIES);
        }

        if (method_exists($user, 'isAdminDinsos') && $user->isAdminDinsos()) {
            return $query->where(function ($q) {
                $q->whereIn('layanan_pjsrk', array_keys(self::LAYANAN_PJSRK))
                    ->orWhere('status', 'dirujuk')
                    ->orWhere(function ($legacy) {
                        $legacy->whereNull('layanan_pjsrk')
                            ->whereIn('kategori', self::DINSOS_CATEGORIES);
                    });
            });
        }

        // Default untuk user pelapor: laporan miliknya sendiri
        return $query->where('id_akun', $user->id_akun ?? 0);
    }

    /**
     * Cek apakah laporan boleh diakses oleh user tertentu.
     */
    public function isVisibleTo($user): bool
    {
        if (!$user) {
            return false;
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        if (method_exists($user, 'isAdminPPA') && $user->isAdminPPA()) {
            return in_array($this->kategori, self::PPA_CATEGORIES, true);
        }

        if (method_exists($user, 'isAdminDinsos') && $user->isAdminDinsos()) {
            return in_array($this->layanan_pjsrk, array_keys(self::LAYANAN_PJSRK), true)
                || $this->status === 'dirujuk'
                || (empty($this->layanan_pjsrk) && in_array($this->kategori, self::DINSOS_CATEGORIES, true));
        }

        return $this->id_akun === ($user->id_akun ?? null);
    }

    public function getLayananLabelAttribute(): string
    {
        return self::LAYANAN_PJSRK[$this->layanan_pjsrk] ?? '-';
    }

    public function getJudulTampilAttribute(): string
    {
        if (!empty($this->judul)) {
            return $this->judul;
        }

        $kronologi = trim((string) optional($this->detail_kasus)->kronologi);

        if ($kronologi !== '') {
            return \Illuminate\Support\Str::limit($kronologi, 60);
        }

        return 'Laporan ' . ($this->kategori ?: 'Sosial');
    }

    public function getKecamatanTampilAttribute(): string
    {
        if (!empty($this->kecamatan)) {
            return $this->kecamatan;
        }

        $tempat = trim((string) optional($this->detail_kasus)->tempat_kejadian);

        if ($tempat !== '') {
            return $tempat;
        }

        $alamat = trim((string) optional($this->detail_pelapor)->alamat);

        return $alamat !== '' ? $alamat : '-';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'dikirim' => 'Menunggu',
            'diterima' => 'Menunggu Verifikasi',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'dirujuk' => 'Dirujuk',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'dikirim', 'diterima' => 'is-waiting',
            'diproses' => 'is-process',
            'selesai' => 'is-done',
            'ditolak' => 'is-rejected',
            'dirujuk' => 'is-referred',
            default => 'is-neutral',
        };
    }

}


