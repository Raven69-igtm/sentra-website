<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Support\Facades\Auth;

/**
 * Halaman Admin PPA → Laporan Cepat.
 *
 * Sumber data: laporan yang dikirim fitur "Laporan Cepat" di mobile
 * (POST /api/laporan-cepat → LaporanControllerMobile::quickAccess).
 * Penanda sumbernya bukan kolom khusus, melainkan kombinasi data yang selalu
 * dihasilkan endpoint tersebut (lihat Laporan::scopeLaporanCepat).
 */
class LaporanCepatController extends Controller
{
    /**
     * Daftar Laporan Cepat.
     *
     * Alur: scope forUser() membatasi ke instansi PPA → scope laporanCepat()
     * menyaring hanya laporan dari fitur Laporan Cepat → view menampilkan tabel.
     */
    public function index()
    {
        // Laporan Cepat hanya untuk Admin PPA; Admin Dinsos dialihkan ke modulnya.
        if (Auth::user()->isAdminDinsos()) {
            return redirect()->route('dinsos.laporan.index');
        }

        // Eager load relasi yang dipakai tabel agar tidak query per baris.
        $laporan = Laporan::with(['detail_pelapor', 'detail_kasus'])
            ->forUser(Auth::user())
            ->laporanCepat()
            ->orderByDesc('created_at')
            ->get();

        return view('backend.layout.page_admin.laporan_cepat.index', compact('laporan'));
    }

    /**
     * Detail satu Laporan Cepat.
     *
     * Relasi yang dimuat sama dengan detail Laporan PPA existing karena
     * view-nya memakai ulang partial preview_content.
     */
    public function show($id)
    {
        if (Auth::user()->isAdminDinsos()) {
            return redirect()->route('dinsos.laporan.index');
        }

        // forUser() + laporanCepat() memastikan admin hanya membuka laporan
        // dalam scope-nya DAN memang berasal dari fitur Laporan Cepat.
        $laporan = Laporan::with([
            'detail_pelapor',
            'detail_penerima_manfaat',
            'detail_penerima_manfaat.informasi_anak',
            'detail_terlapor',
            'detail_kasus',
        ])->forUser(Auth::user())
            ->laporanCepat()
            ->where('id_laporan', $id)
            ->firstOrFail();

        return view('backend.layout.page_admin.laporan_cepat.show', compact('laporan'));
    }
}
