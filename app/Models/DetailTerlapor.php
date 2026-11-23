<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
class DetailTerlapor extends Model
{
    use HasFactory;

    protected $table = 'detail_terlapor';
    protected $primaryKey = 'id_terlapor';
    public $timestamps = false;


    protected $fillable = [
        'nik',
        'nama',
        'umur',
        'alamat',
        'jenis_kelamin',
        'hubungan_dengan_korban',
        'informasi_tambahan',
        'id_laporan', 
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan', 'id_laporan');
    }
    
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
}
