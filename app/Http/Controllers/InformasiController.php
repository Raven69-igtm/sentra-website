<?php

namespace App\Http\Controllers;

use App\Models\Informasi;

/**
 * Endpoint publik untuk aplikasi mobile.
 *
 * Pengelolaan berita sudah dipindahkan ke halaman Settings Superadmin
 * (SuperadminInformasiController). Controller ini sengaja hanya menyisakan
 * endpoint baca agar aplikasi masyarakat tetap dapat menampilkan informasi.
 */
class InformasiController extends Controller
{
    public function apiIndex()
    {
        $informasiList = Informasi::all()->map(function ($informasi) {
            return [
                'id' => $informasi->id_informasi,
                'judul' => $informasi->judul,
                'deskripsi' => $informasi->deskripsi,
                'gambar' => $informasi->gambar ? asset('uploads/informasi/' . $informasi->gambar) : null,
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $informasiList,
        ]);
    }
}
