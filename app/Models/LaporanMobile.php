<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model laporan khusus endpoint mobile.
 *
 * Catatan: model detail (DetailPelapor, DetailTerlapor,
 * DetailPenerimaManfaat, DetailKasus, InformasiAnak) TIDAK lagi
 * didefinisikan ulang di file ini. Sebelumnya definisi duplikat di sini
 * menimpa model asli di app/Models sehingga accessor dekripsi NIK/no. telepon
 * dan relasi informasi anak hilang saat dipakai endpoint mobile.
 */
class LaporanMobile extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_laporan';
    protected $table = 'laporan';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $fillable = ['id_akun', 'judul', 'kecamatan', 'kategori', 'layanan_pjsrk', 'status', 'alasan_penolakan'];

    public function scopeSearch(Builder $query, $keyword, $idAkun)
    {
        return $query->where('id_akun', $idAkun)->where(function ($q) use ($keyword) {
            $q->where('kategori', 'like', "%{$keyword}%")
                ->orWhere('status', 'like', "%{$keyword}%")
                ->orWhere('judul', 'like', "%{$keyword}%")
                ->orWhere('layanan_pjsrk', 'like', "%{$keyword}%")
                ->orWhereHas('detailPjsrk', function ($q2) use ($keyword) {
                    $q2->where('jenis_permasalahan', 'like', "%{$keyword}%")
                        ->orWhere('jenis_rehabilitasi', 'like', "%{$keyword}%")
                        ->orWhere('jenis_bencana', 'like', "%{$keyword}%")
                        ->orWhere('alamat', 'like', "%{$keyword}%")
                        ->orWhere('kecamatan', 'like', "%{$keyword}%");
                })
                ->orWhereHas('detailPelapor', function ($q2) use ($keyword) {
                    $q2->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('alamat', 'like', "%{$keyword}%")
                        ->orWhere('hubungan_dengan_korban', 'like', "%{$keyword}%")
                        ->orWhere('no_telp', 'like', "%{$keyword}%");
                })
                ->orWhereHas('detailTerlapor', function ($q2) use ($keyword) {
                    $q2->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('alamat', 'like', "%{$keyword}%")
                        ->orWhere('informasi_tambahan', 'like', "%{$keyword}%");
                })
                ->orWhereHas('detailPenerimaManfaat', function ($q2) use ($keyword) {
                    $q2->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('alamat', 'like', "%{$keyword}%")
                        ->orWhere('informasi_tambahan', 'like', "%{$keyword}%");
                })
                ->orWhereHas('detailKasus', function ($q2) use ($keyword) {
                    $q2->where('tempat_kejadian', 'like', "%{$keyword}%")
                        ->orWhere('kronologi', 'like', "%{$keyword}%");
                })
                ->orWhereHas('detailPenerimaManfaat.informasi_anak', function ($q2) use ($keyword) {
                    $q2->where('nama', 'like', "%{$keyword}%")
                        ->orWhere('pendidikan', 'like', "%{$keyword}%")
                        ->orWhere('agama', 'like', "%{$keyword}%")
                        ->orWhere('status', 'like', "%{$keyword}%");
                });
        });
    }

    public function detailPelapor()
    {
        return $this->hasOne(DetailPelapor::class, 'id_laporan');
    }

    public function detailTerlapor()
    {
        return $this->hasOne(DetailTerlapor::class, 'id_laporan');
    }

    public function detailPenerimaManfaat()
    {
        return $this->hasOne(DetailPenerimaManfaat::class, 'id_laporan');
    }

    public function detailKasus()
    {
        return $this->hasOne(DetailKasus::class, 'id_laporan');
    }

    public function detailPjsrk()
    {
        return $this->hasOne(DetailPjsrk::class, 'id_laporan', 'id_laporan');
    }
}
