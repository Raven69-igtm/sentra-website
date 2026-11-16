<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Crypt;

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
                ->orWhereHas('detailPenerimaManfaat.informasiAnak', function ($q2) use ($keyword) {
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

    public function informasiAnak()
    {
        return $this->hasOne(InformasiAnak::class, 'id_penerima', 'id_penerima');
    }
}

class DetailPelapor extends Model
{
    use HasFactory;

    protected $table = 'detail_pelapor';
    public $timestamps = false;
    protected $fillable = ['id_laporan', 'nik', 'nama', 'alamat', 'hubungan_dengan_korban', 'no_telp'];

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function ($q) use ($keyword) {
            $q->where('nama', 'like', "%{$keyword}%")
                ->orWhereHas('detailPelapor', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                })
                ->orWhereHas('detailTerlapor', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                })
                ->orWhereHas('detailPenerimaManfaat', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                })
                ->orWhereHas('detailKasus', function ($q) use ($keyword) {
                    $q->where('deskripsi', 'like', "%{$keyword}%");
                })
                ->orWhereHas('informasiAnak', function ($q) use ($keyword) {
                    $q->where('nama_anak', 'like', "%{$keyword}%");
                });
        });

        // Enkripsi sudah ditangani di DetailPelapor.php
    }
}

class DetailTerlapor extends Model
{
    use HasFactory;
    protected $table = 'detail_terlapor';
    public $timestamps = false;
    protected $fillable = ['id_laporan', 'nik', 'nama', 'umur', 'alamat', 'jenis_kelamin', 'hubungan_dengan_korban', 'informasi_tambahan'];

    // Getter dan Setter untuk nik
    public function getNikAttribute($value)
    {
        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return $value;
        }
    }

    public function setNikAttribute($value)
    {
        $this->attributes['nik'] = Crypt::encryptString($value);
    }
}

class DetailPenerimaManfaat extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_penerima';
    protected $table = 'detail_penerima_manfaat';
    public $timestamps = false;
    protected $fillable = ['id_laporan', 'nik', 'nama', 'Tempat_lahir', 'tanggal_lahir', 'umur', 'jenis_kelamin', 'pekerjaan', 'agama', 'alamat', 'pendidikan', 'hubungan_dengan_terlapor', 'notelp', 'informasi_tambahan'];

    public function informasiAnak()
    {
        return $this->hasOne(InformasiAnak::class, 'id_penerima', 'id_penerima');
    }

    // Enkripsi sudah ditangani di DetailPenerimaManfaat.php
}

class DetailKasus extends Model
{
    use HasFactory;
    protected $table = 'detail_kasus';
    public $timestamps = false;
    protected $fillable = ['id_laporan', 'tanggal', 'waktu_kejadian', 'tempat_kejadian', 'kronologi'];
}

class InformasiAnak extends Model
{
    use HasFactory;
    protected $table = 'informasi_anak';
    public $timestamps = false;
    protected $fillable = ['id_penerima', 'nama', 'tanggal_lahir', 'umur', 'jenis_kelamin', 'pendidikan', 'agama', 'status'];
}