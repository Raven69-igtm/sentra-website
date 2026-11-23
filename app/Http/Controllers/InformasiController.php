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
        // Urutkan dari yang terbaru agar mobile sama dengan section
        // "Informasi Terkini" di halaman utama (LandingPageController).
        $informasiList = Informasi::orderByDesc('created_at')->get()
            ->map(function ($informasi) {
                $createdAt = $informasi->created_at;

                return [
                    'id' => $informasi->id_informasi,
                    'judul' => $informasi->judul,
                    'deskripsi' => $informasi->deskripsi,
                    'gambar' => $informasi->gambar
                        ? asset('uploads/informasi/' . $informasi->gambar)
                        : null,
                    // URL gambar melalui endpoint API. Lebih aman dipakai
                    // mobile karena ikut origin API yang sedang dipakai dan
                    // membawa header CORS (file statis belum tentu memilikinya).
                    'gambar_url' => $informasi->gambar
                        ? url('/api/informasi/' . $informasi->id_informasi . '/gambar')
                        : null,
                    // Tanggal & waktu dipakai kartu informasi di aplikasi
                    // mobile. Sebelumnya field ini tidak dikirim sehingga
                    // selalu tampil "Tanggal/Waktu Tidak Tersedia".
                    'tanggal' => $createdAt
                        ? $createdAt->copy()->locale('id')->translatedFormat('d F Y')
                        : null,
                    'waktu' => $createdAt
                        ? $createdAt->copy()->locale('id')->diffForHumans()
                        : null,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $informasiList,
        ]);
    }

    /**
     * Sajikan gambar informasi lewat API agar klien mobile tidak bergantung
     * pada host/static file yang belum tentu sama dengan origin API.
     */
    public function gambar(Informasi $informasi)
    {
        if (! $informasi->gambar) {
            abort(404);
        }

        $path = public_path('uploads/informasi/' . $informasi->gambar);

        if (! is_file($path)) {
            abort(404);
        }

        return response()->file($path, [
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
