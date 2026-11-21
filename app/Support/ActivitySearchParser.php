<?php

namespace App\Support;

/**
 * Pengurai kata kunci Global Search pada Log Aktivitas Administrator.
 *
 * Tujuannya memisahkan keyword menjadi jenis pencarian yang tepat:
 * - angka murni  : ID akun atau atribut tekstual yang memuat angka, serta tahun
 * - tanggal      : 19 November 2026 / 19-11-2026 / 2026-11-19
 * - bulan        : nov / november / nov 2026 / okt 2025
 * - waktu        : 13:30
 * - teks         : pencarian parsial pada atribut teks
 *
 * Dengan begitu angka tidak pernah dicocokkan sebagai substring pada
 * kolom timestamp (mis. "9" tidak dianggap cocok dengan tanggal 19).
 */
class ActivitySearchParser
{
    /**
     * Nama bulan Indonesia beserta singkatan yang diterima.
     *
     * @var array<int, array<int, string>>
     */
    public const MONTH_ALIASES = [
        1  => ['januari', 'jan'],
        2  => ['februari', 'feb'],
        3  => ['maret', 'mar'],
        4  => ['april', 'apr'],
        5  => ['mei'],
        6  => ['juni', 'jun'],
        7  => ['juli', 'jul'],
        8  => ['agustus', 'agu', 'agt'],
        9  => ['september', 'sep', 'sept'],
        10 => ['oktober', 'okt'],
        11 => ['november', 'nov'],
        12 => ['desember', 'des'],
    ];

    /**
     * Daftar bulan untuk dropdown filter manual.
     *
     * @var array<int, string>
     */
    public const MONTH_LABELS = [
        1  => 'Januari',
        2  => 'Februari',
        3  => 'Maret',
        4  => 'April',
        5  => 'Mei',
        6  => 'Juni',
        7  => 'Juli',
        8  => 'Agustus',
        9  => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * @return array{
     *     type: string,
     *     keyword: string,
     *     value?: int,
     *     date?: string,
     *     month?: int,
     *     year?: int|null,
     *     time?: string|null
     * }
     */
    public static function parse(string $keyword): array
    {
        $keyword = trim((string) preg_replace('/\s+/u', ' ', $keyword));

        if ($keyword === '') {
            return ['type' => 'empty', 'keyword' => ''];
        }

        // Waktu murni, mis. "13:30".
        if (preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/u', $keyword, $matches)) {
            return [
                'type'    => 'time',
                'time'    => self::normalizeTime($matches[1], $matches[2]),
                'keyword' => $keyword,
            ];
        }

        // Pisahkan waktu di akhir periode/tanggal, mis.
        // "19 November 2026 13:30" atau "nov 2026 13:30".
        $time = null;
        $datePart = $keyword;

        if (preg_match('/^(.*?)\s+([01]?\d|2[0-3]):([0-5]\d)$/u', $keyword, $matches)) {
            $datePart = trim($matches[1]);
            $time = self::normalizeTime($matches[2], $matches[3]);
        }

        $date = self::parseDate($datePart);

        if ($date !== null) {
            return [
                'type'    => 'date',
                'date'    => $date,
                'time'    => $time,
                'keyword' => $keyword,
            ];
        }

        // Nama bulan / bulan + tahun diprioritaskan sebagai periode aktivitas.
        $month = self::parseMonth($datePart);

        if ($month !== null) {
            return [
                'type'    => 'month',
                'month'   => $month['month'],
                'year'    => $month['year'],
                'time'    => $time,
                'keyword' => $keyword,
            ];
        }

        // Potongan nama bulan, mis. "no" / "v" untuk November.
        $partialMonth = self::parsePartialMonth($datePart);

        if ($partialMonth !== null) {
            return [
                'type'    => 'month_partial',
                'months'  => $partialMonth['months'],
                'year'    => $partialMonth['year'],
                'time'    => $time,
                'keyword' => $keyword,
            ];
        }

        // Angka murni: nomor urut, ID akun, atau tahun (exact match).
        if (ctype_digit($keyword)) {
            return [
                'type'    => 'numeric',
                'value'   => (int) $keyword,
                'keyword' => $keyword,
            ];
        }

        return ['type' => 'text', 'keyword' => $keyword];
    }

    /**
     * @return array{month: int, year: int|null}|null
     */
    private static function parseMonth(string $value): ?array
    {
        $value = trim($value);

        if (! preg_match('/^([A-Za-z]+)\.?(?:\s+(\d{4}))?$/u', $value, $matches)) {
            return null;
        }

        $month = self::monthNumber($matches[1]);

        if ($month === null) {
            return null;
        }

        $year = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : null;

        if ($year !== null && ($year < 1900 || $year > 2100)) {
            return null;
        }

        return ['month' => $month, 'year' => $year];
    }

    /**
     * @return array{months: array<int, int>, year: int|null}|null
     */
    private static function parsePartialMonth(string $value): ?array
    {
        $value = trim($value);

        if (! preg_match('/^([A-Za-z]+)\.?(?:\s+(\d{4}))?$/u', $value, $matches)) {
            return null;
        }

        $months = self::monthNumbersForKeyword($matches[1]);

        if ($months === []) {
            return null;
        }

        $year = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : null;

        if ($year !== null && ($year < 1900 || $year > 2100)) {
            return null;
        }

        return ['months' => $months, 'year' => $year];
    }

    private static function parseDate(string $value): ?string
    {
        $value = trim($value);

        // 2026-11-19
        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/u', $value, $matches)) {
            return self::buildDate((int) $matches[3], (int) $matches[2], (int) $matches[1]);
        }

        // 19-11-2026 (format Indonesia: hari-bulan-tahun)
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/u', $value, $matches)) {
            return self::buildDate((int) $matches[1], (int) $matches[2], (int) $matches[3]);
        }

        // 19 November 2026 / 19 no 2026 (potongan bulan yang unik)
        if (preg_match('/^(\d{1,2})\s+([A-Za-z]+)\.?\s+(\d{4})$/u', $value, $matches)) {
            $months = self::monthNumbersForKeyword($matches[2]);

            if (count($months) !== 1) {
                return null;
            }

            return self::buildDate((int) $matches[1], $months[0], (int) $matches[3]);
        }

        return null;
    }

    private static function buildDate(int $day, int $month, int $year): ?string
    {
        if ($year < 1900 || $year > 2100 || ! checkdate($month, $day, $year)) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }

    /**
     * Bulan yang nama/aliasnya mengandung keyword (partial matching).
     *
     * @return array<int, int>
     */
    public static function monthNumbersForKeyword(string $keyword): array
    {
        $needle = mb_strtolower(trim($keyword));
        $needle = rtrim($needle, '.');

        if ($needle === '') {
            return [];
        }

        $months = [];

        foreach (self::MONTH_ALIASES as $number => $aliases) {
            foreach ($aliases as $alias) {
                if (str_contains($alias, $needle)) {
                    $months[] = $number;
                    break;
                }
            }
        }

        return array_values(array_unique($months));
    }

    /**
     * Tanggal (1-31) yang representasi angkanya mengandung keyword.
     *
     * @return array<int, int>
     */
    public static function dayNumbersForKeyword(string $keyword): array
    {
        $keyword = trim($keyword);

        if ($keyword === '' || ! ctype_digit($keyword) || strlen($keyword) > 2) {
            return [];
        }

        $keyword = ltrim($keyword, '0');
        $keyword = $keyword === '' ? '0' : $keyword;

        $days = [];

        for ($day = 1; $day <= 31; $day++) {
            if (str_contains((string) $day, $keyword)) {
                $days[] = $day;
            }
        }

        return $days;
    }

    private static function monthNumber(string $name): ?int
    {
        $name = mb_strtolower(trim($name));

        foreach (self::MONTH_ALIASES as $number => $aliases) {
            if (in_array($name, $aliases, true)) {
                return $number;
            }
        }

        return null;
    }

    private static function normalizeTime(string $hour, string $minute): string
    {
        return sprintf('%02d:%02d', (int) $hour, (int) $minute);
    }
}
