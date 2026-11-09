<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{LaporanMobile, DetailPelapor, DetailTerlapor, DetailPenerimaManfaat, DetailKasus, InformasiAnak};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
            'informasiAnak'
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
        $data = $request->validate([
            'id_akun' => 'required|exists:akun,id_akun',
            'kategori' => 'required|string',
            'status' => 'required|in:dikirim,diterima,diproses,selesai',
            'judul' => 'nullable|string|max:150',
            'kecamatan' => 'nullable|string|max:100',
            'layanan_pjsrk' => 'nullable|in:rehabilitasi_sosial,pengaduan_bencana,permasalahan_sosial',
            'detail_pelapor' => 'required|array',
            'detail_terlapor' => 'sometimes|array',
            'detail_penerima_manfaat' => 'required|array',
            'detail_kasus' => 'required|array',
            'informasi_anak' => 'sometimes|array'
        ]);

        $layananPjsrk = $data['layanan_pjsrk'] ?? match ($data['kategori']) {
            'Penelantaran' => 'rehabilitasi_sosial',
            'Lainnya' => 'permasalahan_sosial',
            default => null,
        };

        $judul = $data['judul'] ?? null;
        $kecamatan = $data['kecamatan'] ?? ($data['detail_kasus']['tempat_kejadian'] ?? null);

        DB::beginTransaction();

        try {
            // 1. Insert laporan utama
            DB::table('laporan')->insert([
                'id_akun' => $data['id_akun'],
                'judul' => $judul,
                'kecamatan' => $kecamatan,
                'kategori' => $data['kategori'],
                'layanan_pjsrk' => $layananPjsrk,
                'status' => $data['status'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2. Ambil laporan terbaru
            $laporanTerbaru = DB::table('laporan')
                ->where('id_akun', $data['id_akun'])
                ->orderBy('created_at', 'desc')
                ->first();

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
                'nik' => Crypt::encryptString($data['detail_penerima_manfaat']['nik']),
                'nama' => $data['detail_penerima_manfaat']['nama'],
                'Tempat_lahir' => $data['detail_penerima_manfaat']['Tempat_lahir'],
                'tanggal_lahir' => $data['detail_penerima_manfaat']['tanggal_lahir'],
                'umur' => $data['detail_penerima_manfaat']['umur'],
                'jenis_kelamin' => $data['detail_penerima_manfaat']['jenis_kelamin'],
                'pekerjaan' => $data['detail_penerima_manfaat']['pekerjaan'],
                'agama' => $data['detail_penerima_manfaat']['agama'],
                'alamat' => $data['detail_penerima_manfaat']['alamat'],
                'pendidikan' => $data['detail_penerima_manfaat']['pendidikan'],
                'hubungan_dengan_terlapor' => $data['detail_penerima_manfaat']['hubungan_dengan_terlapor'],
                'notelp' => Crypt::encryptString($data['detail_penerima_manfaat']['notelp']),
                'informasi_tambahan' => $data['detail_penerima_manfaat']['informasi_tambahan']
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

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim',
                'data' => [
                    'laporan' => $laporanTerbaru,
                    'detail_penerima' => DB::table('detail_penerima_manfaat')->where('id_penerima', $id_penerima)->first(),
                    'informasi_anak' => DB::table('informasi_anak')->where('id_penerima', $id_penerima)->get()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan',
                'error' => $e->getMessage(),
            ], 500);
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
    public function getUserReports($id_akun)
    {

        $laporan = LaporanMobile::where('id_akun', $id_akun)
            ->with(['detailPelapor', 'detailTerlapor', 'detailPenerimaManfaat', 'detailKasus', 'informasiAnak'])
            ->get();

        return response()->json(['message' => 'Laporan ditemukan', 'laporan' => $laporan]);
    }

    // AMBIL SEMUA LAPORAN BERDASARKAN USER DAN KATEGORI
    public function getUserReportsByCategory($id_akun, $status)
    {

        $laporan = LaporanMobile::where('id_akun', $id_akun)
            ->where('status', $status)
            ->with(['detailPelapor', 'detailTerlapor', 'detailPenerimaManfaat', 'detailKasus', 'informasiAnak'])
            ->get();

        return response()->json(['message' => 'Laporan ditemukan', 'laporan' => $laporan]);
    }
    public function quickAccess(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'id_akun' => 'required|int',
            'nik' => 'required|string|max:100',
            'nama' => 'required|string|max:150',
            'no_telp' => 'required|string|max:15',
            'alamat' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            DB::table('laporan')->insertGetId([
                'id_akun' => $validated['id_akun'],
                'kategori' => 'unset',
                'status' => 'dikirim',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $id_laporan = DB::table('laporan')
                ->where('id_akun', $validated['id_akun'])
                ->where('created_at', now())
                ->value('id_laporan');

            // Insert ke tabel detail_pelapor
            DB::table('detail_pelapor')->insert([
                'id_laporan' => $id_laporan,
                'nik' => Crypt::encryptString( $validated['nik']),
                'nama' => $validated['nama'],
                'alamat' => $validated['alamat'],
                'hubungan_dengan_korban' => '-',
                'no_telp' =>Crypt::encryptString( $validated['no_telp']),
            ]);

            // Insert ke tabel detail_kasus
            DB::table('detail_kasus')->insert([
                'id_laporan' => $id_laporan,
                'tanggal' => Carbon::now()->format('Y-m-d'),
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

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat laporan cepat.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function Search(Request $request)
    {
        $keyword = $request->query('keyword');
        $idAkun = $request->query('id_akun');

        $laporan = LaporanMobile::with([
            'detailPelapor',
            'detailTerlapor',
            'detailPenerimaManfaat',
            'detailKasus',
            'detailPenerimaManfaat.informasiAnak'
        ])
            ->search($keyword, $idAkun)
            ->get();

        return response()->json($laporan);
    }

}
