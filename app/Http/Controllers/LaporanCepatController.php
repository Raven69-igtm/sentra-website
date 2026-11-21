<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Support\SearchHelper;
use App\Support\LaporanSearchParser;

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
    public function index(Request $request)
    {
        // Laporan Cepat hanya untuk Admin PPA; Admin Dinsos dialihkan ke modulnya.
        if (Auth::user()->isAdminDinsos()) {
            return redirect()->route('dinsos.laporan.index');
        }

        // Eager load relasi yang dipakai tabel agar tidak query per baris.
        $query = Laporan::with(['detail_pelapor', 'detail_kasus'])
            ->forUser(Auth::user())
            ->laporanCepat();

        // Pencarian lintas tiket, nama pelapor, dan lokasi kejadian.
        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $like = '%' . SearchHelper::escapeLike($search) . '%';
            $parsedDate = LaporanSearchParser::parse($search);

            $query->where(function ($q) use ($like, $parsedDate) {
                $q->where('id_laporan', 'like', $like)
                    ->orWhere('judul', 'like', $like)
                    ->orWhereHas('detail_pelapor', function ($pelapor) use ($like) {
                        $pelapor->where('nama', 'like', $like);
                    })
                    ->orWhereHas('detail_kasus', function ($kasus) use ($like) {
                        $kasus->where('tempat_kejadian', 'like', $like);
                    });

                if (! $parsedDate) {
                    return;
                }

                switch ($parsedDate['type']) {
                    case 'date':
                        $q->orWhereDate('created_at', $parsedDate['date']);
                        break;
                    case 'month':
                        $q->orWhere(function ($monthQuery) use ($parsedDate) {
                            $monthQuery->whereMonth('created_at', $parsedDate['month']);
                            if ($parsedDate['year']) {
                                $monthQuery->whereYear('created_at', $parsedDate['year']);
                            }
                        });
                        break;
                    case 'year':
                        $q->orWhereYear('created_at', $parsedDate['year']);
                        break;
                    case 'day':
                        $q->orWhereRaw('DAY(created_at) = ?', [$parsedDate['day']]);
                        break;
                }
            });
        }

        $status = strtolower(trim((string) $request->input('status', '')));

        if (in_array($status, ['dikirim', 'diterima', 'diproses', 'dirujuk', 'selesai', 'ditolak'], true)) {
            $query->where('status', $status);
        }

        $laporan = $query->orderByDesc('created_at')->get();

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
