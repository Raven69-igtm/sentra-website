<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DetailPjsrk extends Model
{
    use HasFactory;

    protected $table = 'detail_pjsrk';
    protected $primaryKey = 'id_pjsrk';
    public $timestamps = false;

    protected $fillable = [
        'id_laporan',
        'jenis_bencana',
        'tingkat_dampak',
        'jiwa_terdampak',
        'rumah_rusak',
        'kebutuhan_mendesak',
        'keterangan',
        'jenis_rehabilitasi',
        'jenis_permasalahan',
        'jumlah_penerima',
        'jenis_kelamin',
        'kelompok_usia',
        'kondisi_saat_ditemukan',
        'ciri_ciri',
        'rahasia',
        'alamat',
        'kelurahan',
        'kecamatan',
        'patokan_lokasi',
        'latitude',
        'longitude',
        'foto',
        'kontak',
    ];

    /**
     * URL foto ikut dikirim ke aplikasi mobile agar daftar/detail laporan
     * dapat menampilkan lampiran tanpa menyusun path sendiri.
     */
    protected $appends = ['foto_urls'];

    protected $casts = [
        'kebutuhan_mendesak' => 'array',
        'foto' => 'array',
        'rahasia' => 'boolean',
        'jiwa_terdampak' => 'integer',
        'rumah_rusak' => 'integer',
        'jumlah_penerima' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan', 'id_laporan');
    }

    // Kontak pelapor disimpan terenkripsi, sama seperti no_telp pada DetailPelapor.
    public function getKontakAttribute($value)
    {
        if (empty($value)) {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setKontakAttribute($value)
    {
        $this->attributes['kontak'] = empty($value) ? null : Crypt::encryptString($value);
    }

    public function getFotoUrlsAttribute(): array
    {
        $files = $this->foto ?? [];

        return array_map(
            fn ($file) => asset("uploads/pjsrk/{$this->id_laporan}/{$file}"),
            $files
        );
    }
}
