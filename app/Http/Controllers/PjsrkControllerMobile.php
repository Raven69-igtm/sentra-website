<?php

namespace App\Http\Controllers;

use App\Models\DetailPelapor;
use App\Models\DetailPjsrk;
use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class PjsrkControllerMobile extends Controller
{
    /**
     * Terima pengaduan bencana dari aplikasi mobile PJSRK.
     */
    public function storeBencana(Request $request)
    {
        $data = $request->validate([
            'id_akun' => ['required', 'exists:akun,id_akun'],
            'jenis_bencana' => ['required', 'string', 'max:100'],
            'waktu_kejadian' => ['required', 'date'],
            'tingkat_dampak' => ['required', 'in:Ringan,Sedang,Berat'],
            'jiwa_terdampak' => ['required', 'integer', 'min:0'],
            'rumah_rusak' => ['required', 'integer', 'min:0'],
            'keterangan' => ['required', 'string'],
            'kebutuhan_mendesak' => ['required', 'array', 'min:1'],
            'kebutuhan_mendesak.*' => ['string', 'max:50'],
            'alamat' => ['required', 'string', 'max:255'],
            'kelurahan' => ['required', 'string', 'max:100'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'patokan_lokasi' => ['nullable', 'string', 'max:150'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'kontak' => ['required', 'string', 'max:30'],
            'foto' => ['nullable', 'array', 'max:4'],
            'foto.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $kejadian = Carbon::parse($data['waktu_kejadian']);

        DB::beginTransaction();

        try {
            $idLaporan = $this->insertLaporan([
                'id_akun' => $data['id_akun'],
                'judul' => Str::limit(
                    'Pengaduan Bencana - ' . $data['jenis_bencana'] . ' (' . $data['kecamatan'] . ')',
                    150
                ),
                'kecamatan' => $data['kecamatan'],
                'kategori' => 'unset',
                'layanan_pjsrk' => 'pengaduan_bencana',
            ]);

            $this->insertDetailKasus($idLaporan, [
                'tanggal' => $kejadian->format('Y-m-d'),
                'waktu_kejadian' => $kejadian->format('H:i:s'),
                'tempat_kejadian' => $data['alamat'],
                'kronologi' => $data['keterangan'],
            ]);

            DetailPjsrk::create([
                'id_laporan' => $idLaporan,
                'jenis_bencana' => $data['jenis_bencana'],
                'tingkat_dampak' => $data['tingkat_dampak'],
                'jiwa_terdampak' => $data['jiwa_terdampak'],
                'rumah_rusak' => $data['rumah_rusak'],
                'kebutuhan_mendesak' => array_values($data['kebutuhan_mendesak']),
                'keterangan' => $data['keterangan'],
                'alamat' => $data['alamat'],
                'kelurahan' => $data['kelurahan'],
                'kecamatan' => $data['kecamatan'],
                'patokan_lokasi' => $data['patokan_lokasi'] ?? null,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'foto' => $this->simpanFoto($request, $idLaporan),
                'kontak' => $data['kontak'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan pengaduan bencana berhasil dikirim.',
                'data' => [
                    'id_laporan' => $idLaporan,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan pengaduan bencana.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Terima laporan rehabilitasi sosial dari aplikasi mobile PJSRK.
     */
    public function storeRehabilitasi(Request $request)
    {
        $data = $request->validate([
            'id_akun' => ['required', 'exists:akun,id_akun'],
            'jenis_rehabilitasi' => ['required', 'in:Tunawisma,Anak Terlantar'],
            'jumlah_penerima' => ['required', 'integer', 'min:1'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan,Campuran'],
            'kelompok_usia' => ['required', 'in:Dewasa,Lansia,Bersama anak'],
            'kondisi_saat_ditemukan' => ['required', 'in:Sehat,Sakit,Gangguan jiwa'],
            'ciri_ciri' => ['required', 'string'],
            'rahasia' => ['boolean'],
            'alamat' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'kontak' => ['required', 'string', 'max:30'],
            'foto' => ['nullable', 'array', 'max:4'],
            'foto.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ]);

        $rahasia = filter_var($request->input('rahasia', false), FILTER_VALIDATE_BOOLEAN);

        DB::beginTransaction();

        try {
            $idLaporan = $this->insertLaporan([
                'id_akun' => $data['id_akun'],
                'judul' => Str::limit('Rehabilitasi Sosial - ' . $data['jenis_rehabilitasi'], 150),
                'kecamatan' => null,
                'kategori' => 'Penelantaran',
                'layanan_pjsrk' => 'rehabilitasi_sosial',
            ]);

            $this->insertDetailKasus($idLaporan, [
                'tanggal' => now()->format('Y-m-d'),
                'waktu_kejadian' => null,
                'tempat_kejadian' => $data['alamat'],
                'kronologi' => $data['ciri_ciri'],
            ]);

            DetailPjsrk::create([
                'id_laporan' => $idLaporan,
                'jenis_rehabilitasi' => $data['jenis_rehabilitasi'],
                'jumlah_penerima' => $data['jumlah_penerima'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'kelompok_usia' => $data['kelompok_usia'],
                'kondisi_saat_ditemukan' => $data['kondisi_saat_ditemukan'],
                'ciri_ciri' => $data['ciri_ciri'],
                'rahasia' => $rahasia,
                'alamat' => $data['alamat'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'foto' => $this->simpanFoto($request, $idLaporan),
                'kontak' => $data['kontak'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan rehabilitasi sosial berhasil dikirim.',
                'data' => [
                    'id_laporan' => $idLaporan,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan rehabilitasi sosial.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Terima laporan cepat PJSRK dari menu "Laporan Cepat" mobile.
     *
     * Form ini lebih ringkas dari alur 3 langkah: hanya nama pelapor, nomor
     * telepon, lokasi, jenis, dan deskripsi. Karena tidak memuat field khusus
     * PJSRK (tingkat dampak, jumlah penerima, dsb.), data disimpan sebagai
     * detail pelapor + detail kasus supaya tetap tampil utuh di admin Dinsos.
     */
    public function storeCepat(Request $request)
    {
        $data = $request->validate([
            'id_akun' => ['required', 'exists:akun,id_akun'],
            'layanan' => ['required', 'in:pengaduan_bencana,rehabilitasi_sosial,permasalahan_sosial'],
            'nama' => ['required', 'string', 'max:150'],
            'telepon' => ['required', 'string', 'max:30'],
            'lokasi' => ['required', 'string', 'max:255'],
            'jenis' => ['required', 'string', 'max:100'],
            'deskripsi' => ['required', 'string'],
        ]);

        $kategori = match ($data['layanan']) {
            'rehabilitasi_sosial' => 'Penelantaran',
            'permasalahan_sosial' => 'Lainnya',
            default => 'unset',
        };

        DB::beginTransaction();

        try {
            $idLaporan = $this->insertLaporan([
                'id_akun' => $data['id_akun'],
                'judul' => Str::limit(
                    Laporan::LAYANAN_PJSRK[$data['layanan']] . ' - ' . $data['jenis'],
                    150
                ),
                'kecamatan' => null,
                'kategori' => $kategori,
                'layanan_pjsrk' => $data['layanan'],
            ]);

            DetailPelapor::create([
                'id_laporan' => $idLaporan,
                'nama' => $data['nama'],
                'alamat' => $data['lokasi'],
                'no_telp' => $data['telepon'],
            ]);

            $sekarang = now();

            $this->insertDetailKasus($idLaporan, [
                'tanggal' => $sekarang->format('Y-m-d'),
                'waktu_kejadian' => $sekarang->format('H:i:s'),
                'tempat_kejadian' => $data['lokasi'],
                'kronologi' => $data['deskripsi'],
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laporan cepat berhasil dikirim.',
                'data' => [
                    'id_laporan' => $idLaporan,
                ],
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan cepat.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Insert laporan lalu kembalikan id_laporan yang dibuat trigger.
     *
     * created_at hanya presisi detik, sehingga mengambil "baris terbaru"
     * bisa salah saat ada lebih dari satu laporan pada detik yang sama.
     * Karena itu baris baru dikenali dari selisih id sebelum dan sesudah insert.
     */
    private function insertLaporan(array $atribut): string
    {
        $idAkun = (int) $atribut['id_akun'];

        $idSebelumnya = DB::table('laporan')
            ->where('id_akun', $idAkun)
            ->pluck('id_laporan')
            ->all();

        DB::table('laporan')->insert(array_merge($atribut, [
            'status' => 'dikirim',
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        $idLaporan = DB::table('laporan')
            ->where('id_akun', $idAkun)
            ->when($idSebelumnya !== [], fn ($query) => $query->whereNotIn('id_laporan', $idSebelumnya))
            ->orderByDesc('id_laporan')
            ->value('id_laporan');

        if (!$idLaporan) {
            throw new \RuntimeException('Laporan gagal dibuat.');
        }

        return $idLaporan;
    }

    private function insertDetailKasus(string $idLaporan, array $atribut): void
    {
        DB::table('detail_kasus')->insert(array_merge($atribut, [
            'id_laporan' => $idLaporan,
        ]));
    }

    private function simpanFoto(Request $request, string $idLaporan): array
    {
        if (!$request->hasFile('foto')) {
            return [];
        }

        $directory = public_path("uploads/pjsrk/{$idLaporan}");
        File::ensureDirectoryExists($directory);

        $tersimpan = [];

        foreach ($request->file('foto') as $index => $file) {
            $nama = 'pjsrk-' . time() . '-' . $index . '.' . strtolower($file->getClientOriginalExtension());
            $file->move($directory, $nama);
            $tersimpan[] = $nama;
        }

        return $tersimpan;
    }
}
