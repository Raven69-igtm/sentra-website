<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Laporan;
use Illuminate\Http\Request;

class SuperadminLaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Laporan::with('detail_pelapor');

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('instansi')) {
            if ($request->instansi === 'ppa') {
                $query->whereIn('kategori', ['Kekerasan Fisik', 'Kekerasan Psikis', 'Kekerasan Seksual', 'Eksploitasi', 'TPPO']);
            } elseif ($request->instansi === 'dinsos') {
                $query->where(function ($q) {
                    $q->whereIn('kategori', ['Penelantaran', 'Lainnya'])
                      ->orWhere('status', 'dirujuk');
                });
            }
        }

        if ($request->filled('q')) {
            $keyword = $request->q;
            $query->where(function ($q) use ($keyword) {
                $q->where('id_laporan', 'like', "%{$keyword}%")
                  ->orWhereHas('detail_pelapor', function ($sub) use ($keyword) {
                      $sub->where('nama', 'like', "%{$keyword}%");
                  });
            });
        }

        $laporanList = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('backend.superadmin.laporan.index', compact('laporanList'));
    }

    public function show($id)
    {
        $laporan = Laporan::with([
            'detail_pelapor',
            'detail_penerima_manfaat',
            'detail_penerima_manfaat.informasi_anak',
            'detail_terlapor',
            'detail_kasus'
        ])->findOrFail($id);

        return view('backend.superadmin.laporan.show', compact('laporan'));
    }
}
