<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DinsosLaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->baseQuery($request);

        if ($request->filled('layanan') && array_key_exists($request->layanan, Laporan::LAYANAN_PJSRK)) {
            $this->filterByLayanan($query, $request->layanan);
        }

        return $this->renderList(
            $query,
            'Semua Laporan Dinsos',
            'Seluruh laporan yang masuk ke ruang lingkup Dinas Sosial.',
            $request
        );
    }

    public function layanan(Request $request, string $layanan)
    {
        $layanan = str_replace('-', '_', $layanan);

        abort_unless(array_key_exists($layanan, Laporan::LAYANAN_PJSRK), 404);

        $query = $this->baseQuery($request);
        $this->filterByLayanan($query, $layanan);

        return $this->renderList(
            $query,
            Laporan::LAYANAN_PJSRK[$layanan],
            'Laporan layanan ' . Laporan::LAYANAN_PJSRK[$layanan] . '.',
            $request,
            $layanan
        );
    }

    public function show(string $id)
    {
        $laporan = $this->scopedQuery()
            ->with([
                'detail_pelapor',
                'detail_penerima_manfaat',
                'detail_penerima_manfaat.informasi_anak',
                'detail_terlapor',
                'detail_kasus',
            ])
            ->where('id_laporan', $id)
            ->firstOrFail();

        return view('backend.dinsos.laporan.show', compact('laporan'));
    }

    public function verifikasi(string $id)
    {
        $laporan = $this->scopedQuery()->where('id_laporan', $id)->firstOrFail();

        if (!in_array($laporan->status, ['dikirim', 'diterima'], true)) {
            return $this->errorResponse('Laporan ini tidak sedang menunggu verifikasi.');
        }

        $statusLama = $laporan->status;
        $laporan->status = 'diproses';
        $laporan->save();

        ActivityLog::record('laporan_status', "Memverifikasi laporan #{$laporan->id_laporan} ({$statusLama} → diproses).", 'laporan', $laporan->id_laporan);

        return $this->successResponse('Laporan berhasil diverifikasi dan masuk proses.');
    }

    public function proses(string $id)
    {
        $laporan = $this->scopedQuery()->where('id_laporan', $id)->firstOrFail();

        if (in_array($laporan->status, ['selesai', 'ditolak'], true)) {
            return $this->errorResponse('Laporan yang sudah selesai/ditolak tidak dapat diproses.');
        }

        $statusLama = $laporan->status;
        $laporan->status = 'diproses';
        $laporan->save();

        ActivityLog::record('laporan_status', "Mengubah status laporan #{$laporan->id_laporan} ({$statusLama} → diproses).", 'laporan', $laporan->id_laporan);

        return $this->successResponse('Laporan sedang diproses.');
    }

    public function selesai(string $id)
    {
        $laporan = $this->scopedQuery()->where('id_laporan', $id)->firstOrFail();

        if (!in_array($laporan->status, ['diproses', 'diterima'], true)) {
            return $this->errorResponse('Laporan harus diverifikasi terlebih dahulu sebelum diselesaikan.');
        }

        $statusLama = $laporan->status;
        $laporan->status = 'selesai';
        $laporan->save();

        ActivityLog::record('laporan_status', "Menyelesaikan laporan #{$laporan->id_laporan} ({$statusLama} → selesai).", 'laporan', $laporan->id_laporan);

        return $this->successResponse('Laporan berhasil diselesaikan.');
    }

    public function tolak(Request $request, string $id)
    {
        $validated = $request->validate([
            'alasan_penolakan' => ['required', 'string', 'max:1000'],
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ]);

        $laporan = $this->scopedQuery()->where('id_laporan', $id)->firstOrFail();

        if (in_array($laporan->status, ['selesai', 'ditolak'], true)) {
            return $this->errorResponse('Laporan yang sudah selesai/ditolak tidak dapat ditolak lagi.');
        }

        $statusLama = $laporan->status;
        $laporan->status = 'ditolak';
        $laporan->alasan_penolakan = $validated['alasan_penolakan'];
        $laporan->save();

        // Alasan penolakan tidak dicatat di audit (potensi berisi detail kasus).
        ActivityLog::record('laporan_status', "Menolak laporan #{$laporan->id_laporan} ({$statusLama} → ditolak).", 'laporan', $laporan->id_laporan);

        return $this->successResponse('Laporan ditolak dengan alasan yang dicatat.');
    }

    private function baseQuery(Request $request)
    {
        $query = $this->scopedQuery()
            ->with(['detail_pelapor', 'detail_kasus']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id_laporan', 'like', "%{$search}%")
                    ->orWhere('judul', 'like', "%{$search}%")
                    ->orWhereHas('detail_pelapor', function ($pelapor) use ($search) {
                        $pelapor->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query->latest('created_at');
    }

    /**
     * Terapkan filter layanan PJSRK, termasuk fallback data lama yang belum
     * memiliki kolom layanan_pjsrk.
     */
    private function filterByLayanan($query, string $layanan): void
    {
        $query->where(function ($q) use ($layanan) {
            $q->where('layanan_pjsrk', $layanan);

            if ($layanan === 'rehabilitasi_sosial') {
                $q->orWhere(function ($legacy) {
                    $legacy->whereNull('layanan_pjsrk')
                        ->where('kategori', 'Penelantaran');
                });
            }

            if ($layanan === 'permasalahan_sosial') {
                $q->orWhere(function ($legacy) {
                    $legacy->whereNull('layanan_pjsrk')
                        ->where('kategori', 'Lainnya');
                })->orWhere(function ($referred) {
                    $referred->whereNull('layanan_pjsrk')
                        ->where('status', 'dirujuk');
                });
            }
        });
    }

    private function scopedQuery()
    {
        return Laporan::query()->forUser(Auth::user());
    }

    private function renderList($query, string $title, string $description, Request $request, ?string $activeLayanan = null)
    {
        $laporan = $query->paginate(10)->withQueryString();

        return view('backend.dinsos.laporan.index', [
            'laporan' => $laporan,
            'title' => $title,
            'description' => $description,
            'activeLayanan' => $activeLayanan,
            'layananList' => Laporan::LAYANAN_PJSRK,
            'statusList' => [
                'dikirim' => 'Menunggu',
                'diterima' => 'Menunggu Verifikasi',
                'diproses' => 'Diproses',
                'selesai' => 'Selesai',
                'ditolak' => 'Ditolak',
                'dirujuk' => 'Dirujuk',
            ],
        ]);
    }

    private function successResponse(string $message)
    {
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->back()->with('success', $message);
    }

    private function errorResponse(string $message)
    {
        if (request()->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }

        return redirect()->back()->with('error', $message);
    }
}
