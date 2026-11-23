<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ActivityLog;
use App\Models\Laporan;
use App\Services\LaporanStatusNotifier;
use Illuminate\Support\Facades\Auth;
use App\Support\SearchHelper;
use App\Support\LaporanSearchParser;

class LaporanController extends Controller
{
    private function applyParsedDate($query, array $parsed): void
    {
        switch ($parsed['type']) {
            case 'date':
                $query->orWhereDate('created_at', $parsed['date']);
                break;

            case 'month':
                $query->orWhere(function ($monthQuery) use ($parsed) {
                    $monthQuery->whereMonth('created_at', $parsed['month']);

                    if ($parsed['year']) {
                        $monthQuery->whereYear('created_at', $parsed['year']);
                    }
                });
                break;

            case 'year':
                $query->orWhereYear('created_at', $parsed['year']);
                break;

            case 'day':
                $query->orWhereRaw('DAY(created_at) = ?', [$parsed['day']]);
                break;
        }
    }

    public function laporan_selesai(Request $request)
    {
        if (Auth::user()->isAdminDinsos()) {
            return redirect()->route('dinsos.laporan.index', ['status' => 'selesai']);
        }

        $query = Laporan::with(['detail_pelapor', 'detail_penerima_manfaat', 'detail_terlapor'])
            ->forUser(Auth::user())
            ->where('status', 'selesai');

        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $like = '%' . SearchHelper::escapeLike($search) . '%';
            $parsedDate = LaporanSearchParser::parse($search);

            $query->where(function ($q) use ($like, $parsedDate) {
                $q->where('id_laporan', 'like', $like)
                    ->orWhere('judul', 'like', $like)
                    ->orWhereHas('detail_pelapor', function ($pelapor) use ($like) {
                        $pelapor->where('nama', 'like', $like);
                    });

                if ($parsedDate) {
                    $this->applyParsedDate($q, $parsedDate);
                }
            });
        }

        $kategori = trim((string) $request->input('kategori', ''));

        if ($kategori !== '') {
            $query->where('kategori', $kategori);
        }

        $laporan = $query->latest('created_at')->get();

        return view('backend.layout.page_admin.laporan.selesai_laporan', compact('laporan'));
    }

// Method untuk menampilkan preview laporan dalam modal
public function laporan_preview($id)
{
    $laporan = Laporan::with([
        'akun',
        'detail_pelapor',
        'detail_penerima_manfaat',
        'detail_penerima_manfaat.informasi_anak',
        'detail_terlapor',
        'detail_kasus'
    ])->forUser(Auth::user())->where('id_laporan', $id)->firstOrFail();

    // Return hanya content untuk modal (tanpa layout)
    return view('backend.layout.page_admin.laporan.preview_content', compact('laporan'));
}

// Method lama tetap ada untuk keperluan lain jika diperlukan
public function laporan_show($id)
{
    if (Auth::user()->isAdminDinsos()) {
        return redirect()->route('dinsos.laporan.show', $id);
    }

    $laporan = Laporan::with([
        'akun',
        'detail_pelapor',
        'detail_penerima_manfaat',
        'detail_penerima_manfaat.informasi_anak',
        'detail_terlapor',
        'detail_kasus'
    ])->forUser(Auth::user())->where('id_laporan', $id)->firstOrFail();

    return view('backend.layout.page_admin.laporan.preview_laporan', compact('laporan'));
}

    public function proses(Request $request)
    {
        if (Auth::user()->isAdminDinsos()) {
            return redirect()->route('dinsos.laporan.index');
        }

        $query = Laporan::with(['akun', 'detail_pelapor', 'detail_penerima_manfaat', 'detail_terlapor'])
            ->forUser(Auth::user())
            ->whereIn('status', ['dikirim', 'diterima', 'diproses', 'dirujuk']);

        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $like = '%' . SearchHelper::escapeLike($search) . '%';
            $parsedDate = LaporanSearchParser::parse($search);

            $query->where(function ($q) use ($like, $parsedDate) {
                $q->where('id_laporan', 'like', $like)
                    ->orWhere('judul', 'like', $like)
                    ->orWhereHas('detail_pelapor', function ($pelapor) use ($like) {
                        $pelapor->where('nama', 'like', $like);
                    });

                if ($parsedDate) {
                    $this->applyParsedDate($q, $parsedDate);
                }
            });
        }

        $status = strtolower(trim((string) $request->input('status', '')));

        if (in_array($status, ['dikirim', 'diterima', 'diproses', 'dirujuk'], true)) {
            $query->where('status', $status);
        }

        $laporan = $query->latest('created_at')->get();

        return view('backend.layout.page_admin.laporan.proses_laporan', compact('laporan'));
    }

    /**
     * Menerima laporan baru (dikirim -> diterima) langsung dari tabel /laporan
     * atau halaman detail /laporan-cepat/{id}.
     */
    public function terima($id)
    {
        abort_if(Auth::user()->isAdminDinsos(), 403, 'Admin Dinsos tidak dapat menerima laporan PPA.');

        try {
            $laporan = Laporan::with('akun')->forUser(Auth::user())->where('id_laporan', $id)->firstOrFail();

            if ($laporan->status !== 'dikirim') {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Hanya laporan berstatus Dikirim yang dapat diterima.',
                    ], 422);
                }

                return redirect()->back()->with('error', 'Hanya laporan berstatus Dikirim yang dapat diterima.');
            }

            $statusLama = $laporan->status;
            $laporan->status = 'diterima';
            $laporan->save();

            ActivityLog::record('laporan_status', "Menerima laporan #{$laporan->id_laporan} ({$statusLama} → diterima).", 'laporan', $laporan->id_laporan);
            LaporanStatusNotifier::notifyStatusChanged($laporan, $statusLama, 'diterima');

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan berhasil diterima.',
                    'status' => 'diterima',
                ]);
            }

            return redirect()->back()->with('success', 'Laporan berhasil diterima.');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menerima laporan: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->back()->with('error', 'Gagal menerima laporan.');
        }
    }

public function selesai($id)
    {
        // Penyelesaian laporan operasional memakai endpoint Dinsos sendiri.
        abort_if(Auth::user()->isAdminDinsos(), 403, 'Admin Dinsos tidak dapat menyelesaikan laporan PPA.');

        try {
            $laporan = Laporan::with('akun')->forUser(Auth::user())->where('id_laporan', $id)->firstOrFail();
            
            // Pastikan laporan sudah diproses atau dirujuk sebelum diselesaikan
            if (! in_array($laporan->status, ['diproses', 'dirujuk'], true)) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Laporan harus diproses atau dirujuk terlebih dahulu sebelum diselesaikan.'
                    ], 422);
                }
                
                return redirect()->route('laporan_proses')->with('error', 'Laporan harus diproses atau dirujuk terlebih dahulu.');
            }
            
            // Update status menjadi selesai
            $statusLama = $laporan->status;
            $laporan->status = 'selesai';
            $laporan->save();

            ActivityLog::record('laporan_status', "Menyelesaikan laporan #{$laporan->id_laporan} ({$statusLama} → selesai).", 'laporan', $laporan->id_laporan);
            LaporanStatusNotifier::notifyStatusChanged($laporan, $statusLama, 'selesai');

            // Cek apakah request AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan berhasil diselesaikan.'
                ]);
            }
            
            return redirect()->route('laporan_proses')->with('success', 'Laporan diselesaikan!');
            
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyelesaikan laporan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('laporan_proses')->with('error', 'Gagal menyelesaikan laporan.');
        }
    }


public function rujuk($id)
    {
        if (Auth::user()->isAdminDinsos()) {
            abort(403, 'Admin Dinsos tidak memiliki kewenangan merujuk laporan.');
        }

        try {
            $laporan = Laporan::with('akun')->forUser(Auth::user())->where('id_laporan', $id)->firstOrFail();
            
            // Pastikan laporan sudah diproses dulu
            if ($laporan->status !== 'diproses') {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Laporan harus diproses terlebih dahulu sebelum dirujuk.'
                    ], 422);
                }
                
                return redirect()->route('laporan_proses')->with('error', 'Laporan harus diproses terlebih dahulu.');
            }
            
            // Update status menjadi dirujuk
            $statusLama = $laporan->status;
            $laporan->status = 'dirujuk';
            $laporan->save();

            ActivityLog::record('laporan_status', "Merujuk laporan #{$laporan->id_laporan} ({$statusLama} → dirujuk).", 'laporan', $laporan->id_laporan);
            LaporanStatusNotifier::notifyStatusChanged($laporan, $statusLama, 'dirujuk');

            // Cek apakah request AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Laporan berhasil dirujuk.'
                ]);
            }
            
            return redirect()->route('laporan_proses')->with('success', 'Laporan dirujuk!');
            
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal merujuk laporan: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('laporan_proses')->with('error', 'Gagal merujuk laporan.');
        }
    }
public function proseskan($id)
    {
        try {
            $laporan = Laporan::forUser(Auth::user())->where('id_laporan', $id)->firstOrFail();
            
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Proses Laporan...',
                    'redirect_url' => route('laporan.edit', $id)
                ]);
            }

            return redirect()->route('laporan.edit', $id);
            
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Laporan tidak ditemukan: ' . $e->getMessage()
                ], 404);
            }
            
            return redirect()->route('laporan_proses')->with('error', 'Laporan tidak ditemukan.');
        }
    }

    
    public function edit($id)
    {
        if (Auth::user()->isAdminDinsos()) {
            return redirect()->route('dinsos.laporan.show', $id);
        }

        try {
            $laporan = Laporan::with([
                'detail_pelapor',
                'detail_penerima_manfaat.informasi_anak',
                'detail_terlapor',
                'detail_kasus'
            ])->forUser(Auth::user())->where('id_laporan', $id)->firstOrFail();

            return view('backend.layout.page_admin.laporan.edit_laporan', compact('laporan'));
            
        } catch (\Exception $e) {
            return redirect()->route('laporan_proses')->with('error', 'Laporan tidak ditemukan.');
        }
    }

public function update(Request $request, $id_laporan)
{
    // Edit data laporan operasional adalah wilayah Admin PPA. Admin Dinsos
    // sudah dialihkan ke halaman detail Dinsos untuk edit(), jadi endpoint
    // mutasi ini juga harus ditutup (termasuk laporan berstatus "dirujuk"
    // yang sengaja terlihat di daftar Dinsos).
    abort_if(Auth::user()->isAdminDinsos(), 403, 'Admin Dinsos tidak dapat mengubah laporan PPA.');

    try {
        $validatedData = $request->validate([
            'kategori' => ['required', Rule::in(Laporan::PPA_CATEGORIES)],
            'judul' => 'nullable|string|max:150',
            'kecamatan' => 'nullable|string|max:100',
            'layanan_pjsrk' => 'nullable|in:rehabilitasi_sosial,pengaduan_bencana,permasalahan_sosial',
            'pelapor.nama' => 'required',
            'pelapor.nik' => 'required',
            'pelapor.telepon' => 'required',
            'pelapor.alamat' => 'required',
            'kasus.kronologi' => 'required',
            'kasus.waktu' => 'nullable|date_format:H:i',
        ], [
            'kategori.required' => 'Kategori laporan harus diisi',
            'kategori.in' => 'Kategori laporan harus termasuk kategori resmi PPA',
            'pelapor.nama.required' => 'Nama pelapor harus diisi',
            'pelapor.nik.required' => 'NIK pelapor harus diisi',
            'pelapor.telepon.required' => 'Nomor telepon pelapor harus diisi',
            'pelapor.alamat.required' => 'Alamat pelapor harus diisi',
            'kasus.kronologi.required' => 'Kronologi kejadian harus diisi',
        ]);

        $laporan = Laporan::with([
            'akun',
            'detail_pelapor',
            'detail_penerima_manfaat.informasi_anak',
            'detail_terlapor',
            'detail_kasus',
        ])->forUser(Auth::user())->findOrFail($id_laporan);

        $statusLama = $laporan->status;

        $laporan->kategori = $request->kategori;

        if ($request->filled('judul')) {
            $laporan->judul = $request->judul;
        }

        if ($request->filled('kecamatan')) {
            $laporan->kecamatan = $request->kecamatan;
        }

        if ($request->filled('layanan_pjsrk') && array_key_exists($request->layanan_pjsrk, Laporan::LAYANAN_PJSRK)) {
            $laporan->layanan_pjsrk = $request->layanan_pjsrk;
        }

        if (in_array($laporan->status, ['dikirim', 'diterima'], true)) {
            $laporan->status = 'diproses';
        }
        $laporan->save();

        $pelaporData = [
            'nik' => $request->input('pelapor.nik'),
            'nama' => $request->input('pelapor.nama'),
            'alamat' => $request->input('pelapor.alamat'),
            'hubungan_dengan_korban' => $request->input('pelapor.hubungan') ?: '-',
            'no_telp' => $request->input('pelapor.telepon'),
        ];

        if ($laporan->detail_pelapor) {
            $laporan->detail_pelapor->update($pelaporData);
        } else {
            $laporan->detail_pelapor()->create($pelaporData);
        }

        $penerima = $laporan->detail_penerima_manfaat;
        $penerimaNama = trim((string) $request->input('penerima.nama', ''));
        if ($request->has('penerima') && $penerimaNama !== '') {
            $penerimaData = [
                'nik' => $request->input('penerima.nik') ?: null,
                'nama' => $penerimaNama,
                'tempat_lahir' => $request->input('penerima.ttl') ?: null,
                'tanggal_lahir' => $request->input('penerima.tanggal') ?: null,
                'umur' => $request->input('penerima.umur') !== null && $request->input('penerima.umur') !== ''
                    ? (int) $request->input('penerima.umur')
                    : null,
                'jenis_kelamin' => $request->input('penerima.jk') ?: null,
                'pekerjaan' => $request->input('penerima.pekerjaan') ?: null,
                'agama' => $request->input('penerima.agama') ?: null,
                'pendidikan' => $request->input('penerima.pendidikan') ?: null,
                'alamat' => $request->input('penerima.alamat') ?: null,
                'hubungan_dengan_terlapor' => $request->input('penerima.hubungan_terlapor') ?: null,
                'notelp' => $request->input('penerima.telepon') ?: null,
                'informasi_tambahan' => $request->input('penerima.informasi_tambahan') ?: null,
            ];

            if ($penerima) {
                $penerima->update($penerimaData);
            } else {
                $penerima = $laporan->detail_penerima_manfaat()->create($penerimaData);
            }
        }

        $terlaporNama = trim((string) $request->input('terlapor.nama', ''));
        if ($request->has('terlapor') && $terlaporNama !== '') {
            $terlaporData = [
                'nik' => $request->input('terlapor.nik') ?: null,
                'nama' => $terlaporNama,
                'umur' => $request->input('terlapor.umur') !== null && $request->input('terlapor.umur') !== ''
                    ? (int) $request->input('terlapor.umur')
                    : null,
                'alamat' => $request->input('terlapor.alamat') ?: null,
                'jenis_kelamin' => $request->input('terlapor.jk') ?: null,
                'hubungan_dengan_korban' => $request->input('terlapor.hubungan') ?: null,
                'informasi_tambahan' => $request->input('terlapor.informasi_tambahan') ?: null,
            ];

            if ($laporan->detail_terlapor) {
                $laporan->detail_terlapor->update($terlaporData);
            } else {
                $laporan->detail_terlapor()->create($terlaporData);
            }
        }

        if ($request->has('kasus') && $request->input('kasus.kronologi')) {
            $kasusData = [
                'tanggal' => $request->input('kasus.tanggal') ?: null,
                'waktu_kejadian' => $request->input('kasus.waktu') ?: null,
                'tempat_kejadian' => $request->input('kasus.tempat') ?: '-',
                'kronologi' => $request->input('kasus.kronologi'),
            ];

            if ($laporan->detail_kasus) {
                $laporan->detail_kasus->update($kasusData);
            } else {
                $laporan->detail_kasus()->create($kasusData);
            }
        }

        // Sinkron dengan Mobile: hanya `nama` anak yang wajib, 6 field lainnya opsional (boleh null)
        // sehingga data anak parsial yang dikirim dari Mobile tidak pernah terhapus saat diedit admin.
        if ($penerima && ($request->has('anak') || $request->input('anak_sync') === '1')) {
            $existingAnakIds = [];
            $anakInput = $request->input('anak', []);

            if (is_array($anakInput)) {
                foreach ($anakInput as $dataAnak) {
                    $namaAnak = trim((string) ($dataAnak['nama'] ?? ''));
                    if ($namaAnak === '') {
                        continue;
                    }

                    $anakPayload = [
                        'nama' => $namaAnak,
                        'tanggal_lahir' => !empty($dataAnak['tanggal']) ? $dataAnak['tanggal'] : null,
                        'umur' => isset($dataAnak['umur']) && $dataAnak['umur'] !== '' ? (int) $dataAnak['umur'] : null,
                        'jenis_kelamin' => !empty($dataAnak['jenis_kelamin']) ? $dataAnak['jenis_kelamin'] : null,
                        'pendidikan' => !empty($dataAnak['pendidikan']) ? $dataAnak['pendidikan'] : null,
                        'agama' => !empty($dataAnak['agama']) ? $dataAnak['agama'] : null,
                        'status' => !empty($dataAnak['status']) ? $dataAnak['status'] : null,
                    ];

                    if (!empty($dataAnak['id'])) {
                        $anak = $penerima->informasi_anak()->find($dataAnak['id']);
                        if ($anak) {
                            $anak->update($anakPayload);
                            $existingAnakIds[] = $anak->id_anak;
                        }
                    } else {
                        $anakBaru = $penerima->informasi_anak()->create($anakPayload);
                        $existingAnakIds[] = $anakBaru->id_anak;
                    }
                }

                if (count($existingAnakIds) > 0) {
                    $penerima->informasi_anak()->whereNotIn('id_anak', $existingAnakIds)->delete();
                } elseif ($request->input('anak_sync') === '1') {
                    $penerima->informasi_anak()->delete();
                }
            }
        }

        // Audit & Notifikasi Mobile: status berubah (dikirim/diterima → diproses) atau pembaruan data.
        if ($statusLama !== $laporan->status) {
            ActivityLog::record('laporan_status', "Memproses laporan #{$laporan->id_laporan} ({$statusLama} → {$laporan->status}).", 'laporan', $laporan->id_laporan);
            LaporanStatusNotifier::notifyStatusChanged($laporan, $statusLama, $laporan->status);
        } else {
            ActivityLog::record('data', "Memperbarui data laporan #{$laporan->id_laporan}.", 'laporan', $laporan->id_laporan);
        }

        return redirect()->route('laporan_proses')
            ->with('success', 'Laporan berhasil diproses.');

    } catch (\Illuminate\Validation\ValidationException $e) {
        throw $e;
    } catch (\Exception $e) {
        return redirect()->route('laporan_proses')
            ->with('error', 'Gagal mengupdate laporan.');
    }
}



public function destroy($id)
{
    $user = Auth::user();

    // Sama seperti update(): Admin Dinsos tidak boleh menghapus laporan PPA,
    // termasuk laporan rujukan yang memang tampil di panel Dinsos.
    abort_if($user->isAdminDinsos(), 403, 'Admin Dinsos tidak dapat menghapus laporan PPA.');

    // Superadmin tidak berwenang menghapus laporan privat instansi (hanya audit aktivitas).
    abort_if($user->isSuperAdmin(), 403, 'Superadmin tidak memiliki kewenangan menghapus laporan.');

    // Hanya admin instansi, dan laporan dibatasi sesuai instansinya masing-masing.
    abort_unless(in_array($user->role, ['admin', 'admin_ppa', 'admin_dinsos'], true), 403, 'Anda tidak memiliki kewenangan menghapus laporan.');

    $laporan = Laporan::with([
        'detail_pelapor',
        'detail_penerima_manfaat.informasi_anak',
        'detail_terlapor',
        'detail_kasus'
    ])->forUser($user)->findOrFail($id);

    // Hapus informasi anak (jika ada)
    if ($laporan->detail_penerima_manfaat) {
        $laporan->detail_penerima_manfaat->informasi_anak()->delete();
    }

    // Hapus detail penerima manfaat
    if ($laporan->detail_penerima_manfaat) {
        $laporan->detail_penerima_manfaat->delete();
    }

    // Hapus detail pelapor
    if ($laporan->detail_pelapor) {
        $laporan->detail_pelapor->delete();
    }

    // Hapus detail terlapor
    if ($laporan->detail_terlapor) {
        $laporan->detail_terlapor->delete();
    }

    // Hapus detail kasus
    if ($laporan->detail_kasus) {
        $laporan->detail_kasus->delete();
    }

    // Hapus laporan utama
    $laporan->delete();

    ActivityLog::record('data', "Menghapus laporan #{$laporan->id_laporan} beserta seluruh detailnya.", 'laporan', $laporan->id_laporan);

    return redirect()->route('selesai')->with('success', 'Data laporan berhasil dihapus.');
}



}
