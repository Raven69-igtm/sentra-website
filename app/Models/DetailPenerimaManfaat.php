<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DetailPenerimaManfaat extends Model
{
    use HasFactory;

    protected $table = 'detail_penerima_manfaat';
    protected $primaryKey = 'id_penerima';
    public $timestamps = false;

    protected $fillable = [
        'id_laporan',
        'id_penerima',
        'nama',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'umur',
        'jenis_kelamin',
        'pekerjaan',
        'agama',
        'alamat',
        'pendidikan',
        'hubungan_dengan_terlapor',
        'notelp',
        'informasi_tambahan',
    ];

    public function rekapan()
    {
        return $this->belongsTo(Rekapan::class, 'id_laporan', 'id_laporan');
    }

    // Relasi ke informasi anak. Nama snake_case adalah nama kanonis yang
    // dipakai API mobile (detail_penerima_manfaat.informasi_anak).
    public function informasi_anak()
    {
        return $this->hasMany(InformasiAnak::class, 'id_penerima', 'id_penerima');
    }

    /**
     * Alias camelCase untuk kode lama yang masih memakai
     * `detailPenerimaManfaat.informasiAnak`.
     */
    public function informasiAnak()
    {
        return $this->informasi_anak();
    }
    // NIK
    public function getNikAttribute($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setNikAttribute($value)
    {
        if ($value === null || trim((string) $value) === '') {
            $this->attributes['nik'] = null;
            return;
        }
        $this->attributes['nik'] = Crypt::encryptString((string) $value);
    }

    // No Telepon (nama kolom DB: `notelp`, tanpa underscore)
    public function getNotelpAttribute($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setNotelpAttribute($value)
    {
        if ($value === null || trim((string) $value) === '') {
            $this->attributes['notelp'] = null;
            return;
        }
        $this->attributes['notelp'] = Crypt::encryptString((string) $value);
    }
}
