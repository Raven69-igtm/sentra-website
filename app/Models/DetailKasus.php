<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKasus extends Model
{
    use HasFactory;

    protected $table = 'detail_kasus';
    protected $primaryKey = 'id_kasus';
    public $timestamps = false;

    protected $fillable = [
        'tanggal',
        'waktu_kejadian',
        'tempat_kejadian',
        'kronologi',
        'bukti',
        'id_laporan', // jika relasi laporan diperlukan
    ];

    public function laporan()
    {
        return $this->belongsTo(Laporan::class, 'id_laporan', 'id_laporan');
    }
}