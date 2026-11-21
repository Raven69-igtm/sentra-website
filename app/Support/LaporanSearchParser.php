<?php

namespace App\Support;


class LaporanSearchParser
{
    private const MONTHS = [
        'januari' => 1, 'jan' => 1,
        'februari' => 2, 'feb' => 2,
        'maret' => 3, 'mar' => 3,
        'april' => 4, 'apr' => 4,
        'mei' => 5,
        'juni' => 6, 'jun' => 6,
        'juli' => 7, 'jul' => 7,
        'agustus' => 8, 'agu' => 8, 'agt' => 8,
        'september' => 9, 'sep' => 9, 'sept' => 9,
        'oktober' => 10, 'okt' => 10,
        'november' => 11, 'nov' => 11,
        'desember' => 12, 'des' => 12,
    ];

    public static function parse(string $keyword): ?array
    {
        $value = trim(preg_replace('/\s+/u', ' ', $keyword) ?? '');

        if ($value === '') {
            return null;
        }

        if (preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})$/', $value, $m)
            && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            return ['type' => 'date', 'date' => sprintf('%04d-%02d-%02d', $m[1], $m[2], $m[3])];
        }

        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $value, $m)
            && checkdate((int) $m[2], (int) $m[1], (int) $m[3])) {
            return ['type' => 'date', 'date' => sprintf('%04d-%02d-%02d', $m[3], $m[2], $m[1])];
        }

        if (preg_match('/^(\d{1,2})\s+([A-Za-z]+)\.?\s+(\d{4})$/u', $value, $m)) {
            $month = self::monthNumber($m[2]);

            if ($month && checkdate($month, (int) $m[1], (int) $m[3])) {
                return [
                    'type' => 'date',
                    'date' => sprintf('%04d-%02d-%02d', $m[3], $month, $m[1]),
                ];
            }
        }

        if (preg_match('/^([A-Za-z]+)\.?(?:\s+(\d{4}))?$/u', $value, $m)) {
            $month = self::monthNumber($m[1]);

            if ($month) {
                $year = isset($m[2]) && $m[2] !== '' ? (int) $m[2] : null;

                if (! $year || ($year >= 1900 && $year <= 2100)) {
                    return ['type' => 'month', 'month' => $month, 'year' => $year];
                }
            }
        }

        if (preg_match('/^(\d{4})$/', $value, $m)) {
            $year = (int) $m[1];

            if ($year >= 1900 && $year <= 2100) {
                return ['type' => 'year', 'year' => $year];
            }
        }

        if (ctype_digit($value)) {
            $day = (int) $value;

            if ($day >= 1 && $day <= 31) {
                return ['type' => 'day', 'day' => $day];
            }
        }

        return null;
    }

    private static function monthNumber(string $name): ?int
    {
        return self::MONTHS[mb_strtolower(trim($name))] ?? null;
    }
}
