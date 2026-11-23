<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Support\SearchHelper;
use App\Support\LaporanSearchParser;

class RekapanController extends Controller
{
    public function index(Request $request)
    {
        $dataLaporan = $this->baseQuery($request)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.layout.page_admin.rekapan', compact('dataLaporan'));
    }

    
    public function exportSimple(Request $request)
    {
        $dataLaporan = $this->baseQuery($request)->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = [
            'ID Laporan', 'Kategori', 'Tanggal Laporan', 'Status',
            'Nama Pelapor', 'NIK Pelapor', 'No HP Pelapor', 'Alamat Pelapor', 'Hubungan dengan Korban',
            'Nama Penerima', 'NIK Penerima', 'Alamat Penerima', 'Umur Penerima', 'Jenis Kelamin Penerima', 'Pendidikan Penerima', 'Hubungan dengan Terlapor',
            'Nama Terlapor', 'NIK Terlapor', 'Alamat Terlapor', 'Umur Terlapor', 'Jenis Kelamin Terlapor', 'Hubungan dengan Korban',
            'Nama Anak', 'Umur Anak', 'Jenis Kelamin Anak', 'Pendidikan Anak',
            'Tanggal Kejadian', 'Tempat Kejadian', 'Kronologi'
        ];

        $sheet->fromArray($headers, null, 'A1');
        $row = 2;

        foreach ($dataLaporan as $p) {
            $anakList = $p->detail_penerima_manfaat->informasi_anak ?? collect();

            $sheet->fromArray([
                $p->id_laporan,
                $p->kategori,
                optional($p->created_at)->format('Y-m-d H:i'),
                $p->status,

                $p->detail_pelapor->nama ?? '-',
                "'" . ($p->detail_pelapor->nik ?? '-'),
                $p->detail_pelapor->no_telp ?? '-',
                $p->detail_pelapor->alamat ?? '-',
                $p->detail_pelapor->hubungan_dengan_korban ?? '-',

                $p->detail_penerima_manfaat->nama ?? '-',
                "'" . ($p->detail_penerima_manfaat->nik ?? '-'),
                $p->detail_penerima_manfaat->alamat ?? '-',
                $p->detail_penerima_manfaat->umur ?? '-',
                $p->detail_penerima_manfaat->jenis_kelamin ?? '-',
                $p->detail_penerima_manfaat->pendidikan ?? '-',
                $p->detail_penerima_manfaat->hubungan_dengan_terlapor ?? '-',

                $p->detail_terlapor->nama ?? '-',
                "'" . ($p->detail_terlapor->nik ?? '-'),
                $p->detail_terlapor->alamat ?? '-',
                $p->detail_terlapor->umur ?? '-',
                $p->detail_terlapor->jenis_kelamin ?? '-',
                $p->detail_terlapor->hubungan_dengan_korban ?? '-',
                
                $anakList->pluck('nama')->filter()->implode("\n") ?: '-',
                $anakList->pluck('umur')->filter()->implode("\n") ?: '-',
                $anakList->pluck('jenis_kelamin')->filter()->implode("\n") ?: '-',
                $anakList->pluck('pendidikan')->filter()->implode("\n") ?: '-',

                $p->detail_kasus->tanggal ?? '-',
                $p->detail_kasus->tempat_kejadian ?? '-',
                $p->detail_kasus->kronologi ?? '-',
            ], null, 'A' . $row++);
        }

        $this->applyStyle($sheet, count($headers), $row - 1);

        return $this->downloadExcel($spreadsheet, 'rekapan');
    }


    /**
     * Status akhir yang direkap per instansi. Admin Dinsos tidak memiliki
     * kewenangan merujuk, sehingga tidak ada laporan milik Dinsos yang
     * berstatus 'dirujuk'.
     */
    private function statusRekap($user): array
    {
        return $user->isAdminDinsos()
            ? ['selesai', 'ditolak']
            : ['selesai', 'dirujuk'];
    }

    /**
     * Query dasar rekap — dipakai tabel dan export agar isinya selalu sama.
     * Untuk Admin Dinsos hasilnya dibatasi ke laporan milik Dinsos saja,
     * bukan rujukan dari PPA (lihat Laporan::scopeLaporanDinsos()).
     */
    private function baseQuery(Request $request)
    {
        $user = Auth::user();
        $statuses = $this->statusRekap($user);
        $requestedStatus = $request->input('status');

        $query = Laporan::with([
            'detail_pelapor',
            'detail_terlapor',
            'detail_kasus',
            'detail_penerima_manfaat.informasi_anak',
        ])->forUser($user);

        if ($user->isAdminDinsos()) {
            $query->laporanDinsos();
        }

        // Status di luar daftar role diabaikan → kembali ke status default.
        if (is_string($requestedStatus) && in_array($requestedStatus, $statuses, true)) {
            $query->where('status', $requestedStatus);
        } else {
            $query->whereIn('status', $statuses);
        }

        return $query
            ->when($request->filled('search'), function ($q) use ($request) {
                $like = '%' . SearchHelper::escapeLike(trim((string) $request->search)) . '%';
                $parsedDate = LaporanSearchParser::parse((string) $request->search);

                $q->where(function ($w) use ($like, $parsedDate) {
                    $w->where('id_laporan', 'like', $like)
                        ->orWhere('kategori', 'like', $like)
                        ->orWhere('status', 'like', $like)
                        ->orWhereHas('detail_pelapor', fn ($p) =>
                            $p->where('nama', 'like', $like)
                                ->orWhere('nik', 'like', $like)
                        )
                        ->orWhereHas('detail_penerima_manfaat', fn ($p) =>
                            $p->where('nama', 'like', $like)
                                ->orWhere('nik', 'like', $like)
                        )
                        ->orWhereHas('detail_terlapor', fn ($t) =>
                            $t->where('nama', 'like', $like)
                                ->orWhere('nik', 'like', $like)
                        );

                    if (! $parsedDate) {
                        return;
                    }

                    switch ($parsedDate['type']) {
                        case 'date':
                            $w->orWhereDate('created_at', $parsedDate['date']);
                            break;
                        case 'month':
                            $w->orWhere(function ($monthQuery) use ($parsedDate) {
                                $monthQuery->whereMonth('created_at', $parsedDate['month']);
                                if ($parsedDate['year']) {
                                    $monthQuery->whereYear('created_at', $parsedDate['year']);
                                }
                            });
                            break;
                        case 'year':
                            $w->orWhereYear('created_at', $parsedDate['year']);
                            break;
                        case 'day':
                            $w->orWhereRaw('DAY(created_at) = ?', [$parsedDate['day']]);
                            break;
                    }
                });
            })
            ->when($request->filled('start_date'), fn ($q) =>
                $q->whereDate('created_at', '>=', $request->start_date)
            )
            ->when($request->filled('end_date'), fn ($q) =>
                $q->whereDate('created_at', '<=', $request->end_date)
            )
            ->when($request->filled('kategori'), fn ($q) =>
                $q->where('kategori', $request->kategori)
            );
    }

    private function applyStyle($sheet, $columnCount, $lastRow)
    {
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9E1F2']
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ],
            'alignment' => ['wrapText' => true]
        ];

        $colEnd = Coordinate::stringFromColumnIndex($columnCount);
        $range = "A1:{$colEnd}{$lastRow}";

        $sheet->getStyle("A1:{$colEnd}1")->applyFromArray($headerStyle);
        $sheet->getStyle($range)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle($range)->getAlignment()->setWrapText(true);

        foreach (range(1, $columnCount) as $colIndex) {
            $col = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }

    private function downloadExcel(Spreadsheet $spreadsheet, string $baseFilename)
    {
        $writer = new Xlsx($spreadsheet);
        $filename = "{$baseFilename}_" . now()->format('Ymd_His') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
