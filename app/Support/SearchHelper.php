<?php

namespace App\Support;

class SearchHelper
{
    /**
     * Escape karakter khusus LIKE agar keyword pengguna tidak diperlakukan
     * sebagai wildcard (% atau _).
     */
    public static function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    /**
     * Varian nomor telepon untuk pencarian parsial.
     * Contoh: 0812 -> [0812, 62812].
     *
     * @return array<int, string>
     */
    public static function phoneSearchVariants(string $value): array
    {
        $digits = preg_replace('/\D+/', '', $value) ?? '';

        if ($digits === '') {
            return [];
        }

        $variants = [$digits];

        if (str_starts_with($digits, '62')) {
            $variants[] = '0' . substr($digits, 2);
        }

        if (str_starts_with($digits, '0')) {
            $variants[] = '62' . substr($digits, 1);
        }

        if (str_starts_with($digits, '8')) {
            $variants[] = '0' . $digits;
            $variants[] = '62' . $digits;
        }

        return array_values(array_unique(array_filter($variants)));
    }

    /**
     * Cocokkan nomor telepon yang sudah didekripsi dengan varian keyword.
     *
     * @param  array<int, string>  $variants
     */
    public static function phoneMatches(?string $phone, array $variants): bool
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        if ($digits === '') {
            return false;
        }

        foreach ($variants as $variant) {
            if ($variant !== '' && str_contains($digits, $variant)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Apakah keyword cukup spesifik untuk dianggap sebagai pencarian telepon.
     * Pencarian telepon hanya dijalankan jika keyword tidak mengandung huruf,
     * sehingga pencarian nama/email tidak memicu dekripsi massal.
     */
    public static function looksLikePhoneSearch(string $value): bool
    {
        preg_match_all('/\d/', $value, $matches);

        if (count($matches[0] ?? []) < 3) {
            return false;
        }

        return !preg_match('/[A-Za-z]/', $value);
    }
}
