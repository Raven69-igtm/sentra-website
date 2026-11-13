<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DinsosLaporanController extends Controller
{
    /**
     * Daftar kecamatan di Kabupaten Nganjuk, dipakai sebagai pilihan filter.
     * Daftar dibuat tetap (bukan diambil dari data) agar pilihan filter
     * selalu lengkap dan hanya berisi wilayah Kabupaten Nganjuk.
     */
    private const KECAMATAN_NGANJUK = [
        'Bagor',
        'Baron',
        'Berbek',
        'Gondang',
        'Jatikalen',
        'Kertosono',
        'Lengkong',
        'Loceret',
        'Nganjuk',
        'Ngetos',
        'Ngluyu',
        'Ngronggot',
        'Pace',
        'Patianrowo',
        'Prambon',
        'Rejoso',
        'Sawahan',
        'Sukomoro',
        'Tanjunganom',
        'Wilangan',
    ];

    public function index(Request $request)
    {
        $query = $this->baseQuery($request);

        $title = 'Semua Laporan Dinsos';
        $description = 'Seluruh laporan yang masuk ke ruang lingkup Dinas Sosial.';

        if ($request->filled('layanan') && array_key_exists($request->layanan, Laporan::LAYANAN_PJSRK)) {
            $this->filterByLayanan($query, $request->layanan);

            // Judul ikut menyesuaikan agar hasil filter layanan terlihat jelas.
            $title = Laporan::LAYANAN_PJSRK[$request->layanan];
            $description = 'Laporan layanan ' . $title . '.';
        }

        return $this->renderList($query, $title, $description, $request);
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
                'detail_pjsrk',
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
            ->with(['detail_pelapor', 'detail_kasus', 'detail_pjsrk']);

        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $like = '%' . $search . '%';

            $query->where(function ($q) use ($like) {
                $q->where('id_laporan', 'like', $like)
                    ->orWhere('judul', 'like', $like)
                    ->orWhere('kecamatan', 'like', $like)
                    ->orWhereHas('detail_pelapor', function ($pelapor) use ($like) {
                        $pelapor->where('nama', 'like', $like)
                            ->orWhere('alamat', 'like', $like);
                    })
                    ->orWhereHas('detail_kasus', function ($kasus) use ($like) {
                        $kasus->where('tempat_kejadian', 'like', $like);
                    })
                    ->orWhereHas('detail_pjsrk', function ($pjsrk) use ($like) {
                        $pjsrk->where('alamat', 'like', $like)
                            ->orWhere('jenis_permasalahan', 'like', $like)
                            ->orWhere('jenis_rehabilitasi', 'like', $like)
                            ->orWhere('jenis_bencana', 'like', $like)
                            ->orWhere('kelurahan', 'like', $like)
                            ->orWhere('kecamatan', 'like', $like)
                            ->orWhere('patokan_lokasi', 'like', $like)
                            ->orWhere('kondisi_saat_ditemukan', 'like', $like)
                            ->orWhere('ciri_ciri', 'like', $like);
                    })
                    // Laporan PJSRK tidak punya detail_pelapor, pemilik akun
                    // adalah pelapornya, jadi nama akun ikut dicari.
                    ->orWhereIn('id_akun', function ($akun) use ($like) {
                        $akun->select('id_akun')->from('akun')->where('nama', 'like', $like);
                    });
            });
        }

        $kecamatan = trim((string) $request->input('kecamatan', ''));

        if ($kecamatan !== '') {
            $lower = mb_strtolower($kecamatan);

            // Kecamatan bisa tersimpan di laporan, detail_pjsrk, alamat
            // kejadian, atau alamat pelapor. Semua dicocokkan (tanpa peduli
            // huruf besar/kecil) agar hasil filter sesuai lokasi di tabel.
            $query->where(function ($q) use ($lower) {
                $q->whereRaw('LOWER(laporan.kecamatan) = ?', [$lower])
                    ->orWhereHas('detail_pjsrk', function ($pjsrk) use ($lower) {
                        $pjsrk->whereRaw('LOWER(kecamatan) = ?', [$lower]);
                    })
                    ->orWhereHas('detail_kasus', function ($kasus) use ($lower) {
                        $kasus->whereRaw('LOWER(tempat_kejadian) LIKE ?', ['%' . $lower . '%']);
                    })
                    ->orWhereHas('detail_pelapor', function ($pelapor) use ($lower) {
                        $pelapor->whereRaw('LOWER(alamat) LIKE ?', ['%' . $lower . '%']);
                    });
            });
        }

        $status = strtolower(trim((string) $request->input('status', '')));

        // Status di luar daftar diabaikan supaya filter tidak menghasilkan
        // hasil kosong karena nilai yang tidak dikenal.
        if ($status !== '' && array_key_exists($status, $this->statusOptions())) {
            $query->whereRaw('LOWER(laporan.status) = ?', [$status]);
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

    /**
     * Pilihan filter kecamatan: hanya kecamatan di Kabupaten Nganjuk.
     */
    private function kecamatanOptions(): array
    {
        return self::KECAMATAN_NGANJUK;
    }

    /**
     * Status laporan beserta labelnya pada panel Dinsos.
     */
    private function statusOptions(): array
    {
        return [
            'dikirim' => 'Menunggu',
            'diterima' => 'Menunggu Verifikasi',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'dirujuk' => 'Dirujuk',
        ];
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
            'kecamatanList' => $this->kecamatanOptions(),
            'statusList' => $this->statusOptions(),
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
