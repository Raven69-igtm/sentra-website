<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Logika pencarian berbasis kolom waktu (tanggal, bulan, tahun, hari, jam).
 *
 * Dipakai bersama oleh Audit Aktivitas dan Kelola Akun agar aturan pencarian
 * periode tetap konsisten di kedua fitur.
 */
class CreatedAtSearch
{
    public static function applyDate(
        Builder $query,
        string $date,
        ?string $time = null,
        string $column = 'created_at'
    ): void {
        $start = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();

        if ($time !== null) {
            [$hour, $minute] = array_map('intval', explode(':', $time));

            $query->whereBetween($column, [
                $start->copy()->setTime($hour, $minute, 0),
                $start->copy()->setTime($hour, $minute, 59),
            ]);

            return;
        }

        $query->whereBetween($column, [$start, $start->copy()->endOfDay()]);
    }

    /**
     * @param  array<int, int>  $months
     */
    public static function applyMonths(
        Builder $query,
        array $months,
        ?int $year = null,
        ?string $time = null,
        string $column = 'created_at'
    ): void {
        $months = array_values(array_unique(array_map('intval', $months)));

        if ($months === []) {
            return;
        }

        $query->where(function (Builder $sub) use ($months, $column) {
            foreach ($months as $index => $month) {
                if ($index === 0) {
                    $sub->whereMonth($column, $month);
                } else {
                    $sub->orWhereMonth($column, $month);
                }
            }
        });

        if ($year !== null) {
            $query->whereYear($column, $year);
        }

        if ($time !== null) {
            self::applyTime($query, $time, $column);
        }
    }

    public static function applyMonth(
        Builder $query,
        int $month,
        ?int $year = null,
        ?string $time = null,
        string $column = 'created_at'
    ): void {
        self::applyMonths($query, [$month], $year, $time, $column);
    }

    public static function applyYear(Builder $query, int $year, string $column = 'created_at'): void
    {
        $query->whereYear($column, $year);
    }

    /**
     * @param  array<int, int>  $days
     */
    public static function applyDays(Builder $query, array $days, string $column = 'created_at'): void
    {
        $days = array_values(array_unique(array_map('intval', $days)));

        if ($days === []) {
            return;
        }

        $query->where(function (Builder $sub) use ($days, $column) {
            foreach ($days as $index => $day) {
                if ($index === 0) {
                    $sub->whereDay($column, $day);
                } else {
                    $sub->orWhereDay($column, $day);
                }
            }
        });
    }

    public static function applyTime(Builder $query, string $time, string $column = 'created_at'): void
    {
        $query->whereTime($column, '>=', $time . ':00')
            ->whereTime($column, '<=', $time . ':59');
    }
}
