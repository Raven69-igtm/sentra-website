<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Support\SearchHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Halaman Admin Dinsos → Laporan Cepat PJSRK.
 *
 * Sumber data: form ringkas "Laporan Cepat" di menu mobile Pelaporan Cepat
 * (Rehabilitasi Sosial / Bencana Alam / Permasalahan Sosial) yang dikirim ke
 * POST /api/pjsrk/cepat → PjsrkControllerMobile::storeCepat.
 *
 * Halaman ini terpisah dari "Laporan PJSRK" karena laporan cepat hanya memuat
 * data pelapor + kasus (tanpa detail_pjsrk), sehingga butuh tampilan ringkas
 * sendiri. Penanda sumbernya ada di Laporan::scopeLaporanCepatPjsrk.
 */
class DinsosLaporanCepatController extends Controller
{
    /** Status yang dipakai panel Dinsos, sejalan dengan daftar laporan PJSRK. */
    private const STATUS_LABELS = [
        'dikirim' => 'Menunggu',
        'diterima' => 'Menunggu Verifikasi',
        'diproses' => 'Diproses',
        'dirujuk' => 'Dirujuk',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak',
    ];

    public function index(Request $request)
    {
        // Eager load relasi yang dipakai tabel agar tidak query per baris.
        $query = Laporan::with(['detail_pelapor', 'detail_kasus'])
            ->forUser(Auth::user())
            ->laporanCepatPjsrk();

        if ($request->filled('layanan') && array_key_exists($request->layanan, Laporan::LAYANAN_PJSRK)) {
            $query->where('layanan_pjsrk', $request->layanan);
        }

        if ($request->filled('status') && array_key_exists($request->status, self::STATUS_LABELS)) {
            $query->where('status', $request->status);
        }

        // Pencarian lintas tiket, judul, nama pelapor, dan lokasi kejadian.
        if ($request->filled('search')) {
            $search = '%' . SearchHelper::escapeLike(trim((string) $request->search)) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('id_laporan', 'like', $search)
                    ->orWhere('judul', 'like', $search)
                    ->orWhereHas('detail_pelapor', fn ($p) => $p->where('nama', 'like', $search))
                    ->orWhereHas('detail_kasus', fn ($k) => $k->where('tempat_kejadian', 'like', $search));
            });
        }

        $laporan = $query->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('backend.dinsos.laporan_cepat.index', [
            'laporan' => $laporan,
            'layananList' => Laporan::LAYANAN_PJSRK,
            'statusList' => self::STATUS_LABELS,
        ]);
    }

    public function show(string $id)
    {
        // forUser() + laporanCepatPjsrk() memastikan admin hanya membuka
        // laporan dalam scope instansinya DAN memang laporan cepat PJSRK.
        $laporan = Laporan::with(['detail_pelapor', 'detail_kasus'])
            ->forUser(Auth::user())
            ->laporanCepatPjsrk()
            ->where('id_laporan', $id)
            ->firstOrFail();

        return view('backend.dinsos.laporan_cepat.show', compact('laporan'));
    }
}
