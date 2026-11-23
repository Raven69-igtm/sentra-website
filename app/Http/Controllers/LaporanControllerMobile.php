<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Akun, LaporanMobile, DetailPelapor, DetailTerlapor, DetailPenerimaManfaat, DetailKasus, InformasiAnak};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Log;
use Illuminate\Support\Facades\Crypt;

class LaporanControllerMobile extends Controller
{

    // TRACKING LAPORAN
    public function trackReport(Request $request)
    {
        $data = $request->validate([
            'id_laporan' => 'required|exists:laporan,id_laporan'
        ]);

        $laporan = LaporanMobile::with([
            'detailPelapor',
            'detailTerlapor',
            'detailPenerimaManfaat',
            'detailKasus',
            'detailPjsrk',
            // Relasi informasi anak berada di bawah penerima manfaat
            // (satu penerima bisa memiliki lebih dari satu anak).
            'detailPenerimaManfaat.informasi_anak',
        ])
            ->where('id_laporan', $data['id_laporan'])
            ->first();

        if (!$laporan) {
            return response()->json([
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'message' => 'Laporan ditemukan',
            'status' => $laporan->status,
            'laporan' => $laporan
        ]);
    }


    // POST LAPORAN
    public function postReport(Request $request)
    {
        $authUser = $request->user('sanctum') ?? $request->user();
        if (!$authUser) {
            return response()->json(['success' => false, 'message' => 'Tidak terautentikasi.'], 401);
        }

        $data = $request->validate([
            'id_akun' => 'nullable|integer',
            'kategori' => 'required|string',
            'status' => 'nullable|in:dikirim,diterima',
            'judul' => 'nullable|string|max:150',
            'kecamatan' => 'nullable|string|max:100',
            'layanan_pjsrk' => 'nullable|in:rehabilitasi_sosial,pengaduan_bencana,permasalahan_sosial',
            'detail_pelapor' => 'required|array',
            'detail_terlapor' => 'sometimes|array',
            'detail_penerima_manfaat' => 'required|array',
            'detail_kasus' => 'required|array',
            'informasi_anak' => 'sometimes|array'
        ]);

        // Identitas pelapor selalu berasal dari token Sanctum, bukan payload.
        $idAkun = (int) $authUser->id_akun;
        $statusAwal = $data['status'] ?? 'dikirim';

        $layananPjsrk = $data['layanan_pjsrk'] ?? match ($data['kategori']) {
            'Penelantaran' => 'rehabilitasi_sosial',
            'Lainnya' => 'permasalahan_sosial',
            default => null,
        };

        $judul = $data['judul'] ?? null;
        $kecamatanRaw = $data['kecamatan'] ?? ($data['detail_kasus']['tempat_kejadian'] ?? null);
        $kecamatan = $kecamatanRaw !== null ? Str::limit((string) $kecamatanRaw, 100, '') : null;
        $tempatLahir = $data['detail_penerima_manfaat']['Tempat_lahir']
            ?? $data['detail_penerima_manfaat']['tempat_lahir']
            ?? '-';

        DB::beginTransaction();

        try {
            // Ambil daftar id_laporan sebelumnya milik akun ini untuk menghindari
            // race condition pembacaan id yang dibuat trigger BEFORE INSERT.
            $idSebelumnya = DB::table('laporan')
                ->where('id_akun', $idAkun)
                ->pluck('id_laporan')
                ->all();

            // 1. Insert laporan utama
            DB::table('laporan')->insert([
                'id_akun' => $idAkun,
                'judul' => $judul,
                'kecamatan' => $kecamatan,
                'kategori' => $data['kategori'],
                'layanan_pjsrk' => $layananPjsrk,
                'status' => $statusAwal,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Ambil baris laporan yang baru dibuat berdasarkan selisih id.
            $laporanTerbaru = DB::table('laporan')
                ->where('id_akun', $idAkun)
                ->when($idSebelumnya !== [], fn ($q) => $q->whereNotIn('id_laporan', $idSebelumnya))
                ->orderByDesc('id_laporan')
                ->first();

            if (!$laporanTerbaru) {
                throw new \RuntimeException('Laporan gagal dibuat.');
            }

            $id_laporan = $laporanTerbaru->id_laporan;

            // 3. Insert detail_pelapor dengan enkripsi
            DB::table('detail_pelapor')->insert([
                'id_laporan' => $id_laporan,
                'nik' => Crypt::encryptString($data['detail_pelapor']['nik']),
                'nama' => $data['detail_pelapor']['nama'],
                'alamat' => $data['detail_pelapor']['alamat'],
                'no_telp' => Crypt::encryptString($data['detail_pelapor']['no_telp']),
                'hubungan_dengan_korban' => $data['detail_pelapor']['hubungan_dengan_korban']
            ]);

            // 4. Insert detail_penerima_manfaat dengan enkripsi
            $id_penerima = DB::table('detail_penerima_manfaat')->insertGetId([
                'id_laporan' => $id_laporan,
                'nik' => !empty($data['detail_penerima_manfaat']['nik'])
                    ? Crypt::encryptString((string) $data['detail_penerima_manfaat']['nik'])
                    : null,
                'nama' => $data['detail_penerima_manfaat']['nama'] ?? null,
                'Tempat_lahir' => $tempatLahir,
                'tanggal_lahir' => $data['detail_penerima_manfaat']['tanggal_lahir'] ?? null,
                'umur' => $data['detail_penerima_manfaat']['umur'] ?? null,
                'jenis_kelamin' => $data['detail_penerima_manfaat']['jenis_kelamin'] ?? null,
                'pekerjaan' => $data['detail_penerima_manfaat']['pekerjaan'] ?? null,
                'agama' => $data['detail_penerima_manfaat']['agama'] ?? null,
                'alamat' => $data['detail_penerima_manfaat']['alamat'] ?? null,
                'pendidikan' => $data['detail_penerima_manfaat']['pendidikan'] ?? null,
                'hubungan_dengan_terlapor' => $data['detail_penerima_manfaat']['hubungan_dengan_terlapor'] ?? null,
                'notelp' => !empty($data['detail_penerima_manfaat']['notelp'])
                    ? Crypt::encryptString((string) $data['detail_penerima_manfaat']['notelp'])
                    : null,
                'informasi_tambahan' => $data['detail_penerima_manfaat']['informasi_tambahan'] ?? null
            ]);

            // 5. Insert detail_terlapor dengan enkripsi
            // Support both single object (from Flutter) and array of objects
            if (!empty($data['detail_terlapor'])) {
                $terlaporInput = $data['detail_terlapor'];
                // If Flutter sends a single object (Map), wrap in array
                if (isset($terlaporInput['nama'])) {
                    $terlaporInput = [$terlaporInput];
                }
                foreach ($terlaporInput as $terlapor) {
                    if (empty($terlapor['nama'])) {
                        continue;
                    }
                    DB::table('detail_terlapor')->insert([
                        'id_laporan' => $id_laporan,
                        'nik' => isset($terlapor['nik']) ? Crypt::encryptString($terlapor['nik']) : null,
                        'nama' => $terlapor['nama'] ?? null,
                        'umur' => $terlapor['umur'] ?? null,
                        'alamat' => $terlapor['alamat'] ?? null,
                        'jenis_kelamin' => $terlapor['jenis_kelamin'] ?? null,
                        'hubungan_dengan_korban' => $terlapor['hubungan_dengan_korban'] ?? null,
                        'informasi_tambahan' => $terlapor['informasi_tambahan'] ?? null
                    ]);
                }
            }

            // 6. Insert detail_kasus
            DB::table('detail_kasus')->insert([
                'id_laporan' => $id_laporan,
                'tanggal' => $data['detail_kasus']['tanggal'],
                'waktu_kejadian' => !empty($data['detail_kasus']['waktu_kejadian'])
                    ? Carbon::parse($data['detail_kasus']['waktu_kejadian'])->format('H:i:s')
                    : null,
                'tempat_kejadian' => $data['detail_kasus']['tempat_kejadian'],
                'kronologi' => $data['detail_kasus']['kronologi']
            ]);

            // 7. Insert informasi_anak
            if (!empty($data['informasi_anak'])) {
                foreach ($data['informasi_anak'] as $anak) {
                    if (empty($anak['nama'])) {
                        continue;
                    }
                    DB::table('informasi_anak')->insert([
                        'id_penerima' => $id_penerima,
                        'nama' => $anak['nama'] ?? null,
                        'tanggal_lahir' => $anak['tanggal_lahir'] ?? null,
                        'umur' => $anak['umur'] ?? null,
                        'jenis_kelamin' => $anak['jenis_kelamin'] ?? null,
                        'pendidikan' => $anak['pendidikan'] ?? null,
                        'agama' => $anak['agama'] ?? null,
                        'status' => $anak['status'] ?? null
                    ]);
                }
            }

            DB::commit();

            // Kembalikan model Eloquent agar field terenkripsi (nik, notelp) terdekripsi otomatis.
            $detailPenerima = DetailPenerimaManfaat::find($id_penerima);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim',
                'data' => [
                    'laporan' => $laporanTerbaru,
                    'detail_penerima' => $detailPenerima,
                    'informasi_anak' => DB::table('informasi_anak')->where('id_penerima', $id_penerima)->get()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat laporan mobile: ' . $e->getMessage());
            return response()->json(array_filter([
                'success' => false,
                'message' => 'Gagal membuat laporan',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], fn ($v) => $v !== null), 500);
        }
    }



    // MONITORING RATA-RATA PERSENTASE LAPORAN DALAM 1 TAHUN
    public function reportStatistics()
    {
        $year = Carbon::now()->year;

        // Ambil data mentah untuk debugging
        $rawData = LaporanMobile::whereYear('created_at', $year)
            ->selectRaw("DATE_FORMAT(created_at, '%b') as month, kategori, COUNT(*) as count")
            ->groupBy('month', 'kategori')
            ->get();

        Log::info('Raw data from query: ' . json_encode($rawData));

        $statistics = $rawData
            ->groupBy('month')
            ->map(function ($monthData) {
                $result = $monthData->mapWithKeys(function ($item) {
                    $category = $item->kategori;
                    $count = (int) $item->count;
                    Log::info("Processing month: {$item->month}, category: {$category}, count: {$count}");
                    return [$category => $count];
                })->toArray();
                Log::info("Processed month data: " . json_encode($result));
                return $result;
            })->toArray();

        Log::info('Final statistics: ' . json_encode($statistics));

        return response()->json([
            'message' => 'Jumlah laporan per kategori per bulan tahun ini',
            'data' => $statistics
        ], 200, [], JSON_NUMERIC_CHECK);
    }

    // AMBIL SEMUA LAPORAN BERDASARKAN USER
    public function getUserReports(Request $request, $id_akun)
    {
        $authUser = $request->user('sanctum') ?? $request->user();
        if (!$authUser || (int) $authUser->id_akun !== (int) $id_akun) {
            return response()->json(['message' => 'Anda tidak berhak mengakses laporan akun ini'], 403);
        }

        $query = LaporanMobile::where('id_akun', $authUser->id_akun)
            ->with(['detailPelapor', 'detailTerlapor', 'detailPenerimaManfaat', 'detailKasus', 'detailPjsrk', 'detailPenerimaManfaat.informasi_anak']);

        $this->applyScopeFilter($query, $request->query('scope'));

        $laporan = $query->get();

        return response()->json(['message' => 'Laporan ditemukan', 'laporan' => $laporan]);
    }

    // AMBIL SEMUA LAPORAN BERDASARKAN USER DAN KATEGORI
    public function getUserReportsByCategory(Request $request, $id_akun, $status)
    {
        $authUser = $request->user('sanctum') ?? $request->user();
        if (!$authUser || (int) $authUser->id_akun !== (int) $id_akun) {
            return response()->json(['message' => 'Anda tidak berhak mengakses laporan akun ini'], 403);
        }

        $query = LaporanMobile::where('id_akun', $authUser->id_akun)
            ->where('status', $status)
            ->with(['detailPelapor', 'detailTerlapor', 'detailPenerimaManfaat', 'detailKasus', 'detailPjsrk', 'detailPenerimaManfaat.informasi_anak']);

        $this->applyScopeFilter($query, $request->query('scope'));

        $laporan = $query->get();

        return response()->json(['message' => 'Laporan ditemukan', 'laporan' => $laporan]);
    }
    public function quickAccess(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'id_akun' => 'nullable|integer|exists:akun,id_akun',
            'nik' => 'required|string|max:100',
            'nama' => 'required|string|max:150',
            'no_telp' => 'required|string|max:15',
            'alamat' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ], [
            'id_akun.exists' => 'Akun pelapor tidak ditemukan. Silakan login ulang.',
        ]);

        // Laporan Cepat boleh dikirim tanpa login: bila pengguna membawa token
        // Sanctum valid, pakai id_akun dari token; jika anonim, pakai akun guest.
        $authUser = $request->user('sanctum');
        $idAkun = $authUser ? (int) $authUser->id_akun : $this->akunAnonim()->id_akun;

        // Satu timestamp dipakai untuk insert dan pembacaan ulang, supaya
        // pencarian id_laporan tidak meleset saat detik berganti.
        $now = now();

        // ketiga insert (laporan + detail_pelapor + detail_kasus) harus utuh.
        DB::beginTransaction();

        try {
            DB::table('laporan')->insert([
                'id_akun' => $idAkun,
                'kategori' => 'unset',
                'status' => 'dikirim',
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            // id_laporan dibuat oleh trigger BEFORE INSERT, jadi dibaca ulang.
            // Filter detail_pelapor kosong menjaga laporan yang baru dibuat
            // tidak tertukar bila akun penampung menerima kiriman bersamaan.
            $id_laporan = DB::table('laporan as l')
                ->where('l.id_akun', $idAkun)
                ->where('l.created_at', $now)
                ->whereNotExists(function ($q) {
                    $q->select(DB::raw(1))
                        ->from('detail_pelapor')
                        ->whereColumn('detail_pelapor.id_laporan', 'l.id_laporan');
                })
                ->value('l.id_laporan');

            // Insert ke tabel detail_pelapor
            DB::table('detail_pelapor')->insert([
                'id_laporan' => $id_laporan,
                'nik' => Crypt::encryptString( $validated['nik']),
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'hubungan_dengan_korban' => '-',
                'no_telp' =>Crypt::encryptString( $validated['no_telp']),
            ]);

            // Insert ke tabel detail_kasus. Laporan cepat tidak meminta jam
            // kejadian, jadi waktu kiriman dipakai sebagai waktu kejadian agar
            // kolom ini tidak kosong di detail laporan.
            DB::table('detail_kasus')->insert([
                'id_laporan' => $id_laporan,
                'tanggal' => $now->format('Y-m-d'),
                'waktu_kejadian' => $now->format('H:i:s'),
                'tempat_kejadian' => '-',
                'kronologi' => $validated['deskripsi']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan cepat berhasil dikirim.',
                'data' => [
                    'id_laporan' => $id_laporan,
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat laporan cepat: ' . $e->getMessage());

            return response()->json(array_filter([
                'success' => false,
                'message' => 'Gagal membuat laporan cepat.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], fn ($v) => $v !== null), 500);
        }
    }
    /**
     * Akun penampung untuk laporan cepat tanpa login.
     *
     * Tabel laporan mewajibkan id_akun (foreign key laporan_id_akun_foreign),
     * sehingga kiriman anonim diatribusikan ke satu akun guest yang tidak bisa
     * dipakai login (is_active = false). Dibuat sekali saat kiriman anonim
     * pertama; idempoten karena email-nya unik.
     */
    private function akunAnonim(): Akun
    {
        return Akun::firstOrCreate(
            ['email' => 'anonim@sentra.local'],
            [
                'nama' => 'Pelapor Anonim',
                'notelp' => '0000000000',
                'password' => Str::random(40),
                'role' => 'guest',
                'is_active' => false,
            ]
        );
    }

    public function Search(Request $request)
    {
        $authUser = $request->user('sanctum') ?? $request->user();
        if (!$authUser) {
            return response()->json(['message' => 'Tidak terautentikasi'], 401);
        }

        $keyword = $request->query('keyword');
        $requestedIdAkun = $request->query('id_akun');

        if ($requestedIdAkun !== null && (int) $requestedIdAkun !== (int) $authUser->id_akun) {
            return response()->json(['message' => 'Anda tidak berhak mencari laporan akun lain'], 403);
        }

        $query = LaporanMobile::with([
            'detailPelapor',
            'detailTerlapor',
            'detailPenerimaManfaat',
            'detailKasus',
            'detailPjsrk',
            'detailPenerimaManfaat.informasi_anak',
        ])
            ->search($keyword, (int) $authUser->id_akun);

        $this->applyScopeFilter($query, $request->query('scope'));

        $laporan = $query->get();

        return response()->json($laporan);
    }

    /**
     * Memisahkan riwayat layanan PPA (layanan_pjsrk kosong/null) dan PJSRK
     * (layanan_pjsrk terisi) bila parameter query ?scope=ppa|pjsrk diberikan.
     */
    private function applyScopeFilter($query, ?string $scope): void
    {
        $normalized = strtolower(trim((string) $scope));
        if ($normalized === 'ppa') {
            $query->where(function ($q) {
                $q->whereNull('layanan_pjsrk')->orWhere('layanan_pjsrk', '');
            });
        } elseif ($normalized === 'pjsrk') {
            $query->whereNotNull('layanan_pjsrk')->where('layanan_pjsrk', '!=', '');
        }
    }

}
