<?php

namespace App\Support;

use App\Models\ActivityLog;
use App\Models\Akun;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class ActivitySearchService
{
    public function applyScope(Builder $query, ?string $scope): void
    {
        if ($scope === 'dinsos') {
            $query->where('instansi', 'Dinas Sosial')
                ->where('role', 'admin_dinsos');

            return;
        }

        if ($scope === 'ppa') {
            $query->where('instansi', 'UPTD PPA')
                ->whereIn('role', ['admin', 'admin_ppa']);
        }
    }

    public function applyFilters(Builder $query, array $filters): void
    {
        foreach (['instansi', 'jenis', 'role', 'status', 'module'] as $key) {
            $column = $key === 'jenis' ? 'activity_type' : $key;

            if (! empty($filters[$key])) {
                $query->where($column, $filters[$key]);
            }
        }

        // Tab tampilan audit. "Penting" (default) menyembunyikan login/logout
        // rutin tetapi tetap menampilkan login gagal.
        $tab = $filters['tab'] ?? 'penting';

        if ($tab === 'penting') {
            $query->where(function (Builder $sub) {
                $sub->where('activity_type', '!=', 'logout')
                    ->where(function (Builder $inner) {
                        $inner->where('activity_type', '!=', 'login')
                            ->orWhere('status', '!=', 'berhasil');
                    });
            });
        } elseif ($tab === 'login') {
            $query->whereIn('activity_type', ['login', 'logout']);
        }

        if (! empty($filters['akun_id'])) {
            $akunValue = trim((string) $filters['akun_id']);

            if ($akunValue !== '') {
                if (ctype_digit($akunValue)) {
                    $query->where('akun_id', (int) $akunValue);
                } else {
                    $query->whereIn(
                        'akun_id',
                        Akun::where('kode_akun', $akunValue)->pluck('id_akun')
                    );
                }
            }
        }

        if (! empty($filters['dari'])) {
            $query->whereDate('created_at', '>=', $filters['dari']);
        }

        if (! empty($filters['sampai'])) {
            $query->whereDate('created_at', '<=', $filters['sampai']);
        }

        if (! empty($filters['bulan']) && ! empty($filters['tahun'])) {
            $start = Carbon::create((int) $filters['tahun'], (int) $filters['bulan'], 1)->startOfMonth();
            $query->whereBetween('created_at', [$start, $start->copy()->endOfMonth()]);
        } elseif (! empty($filters['tahun'])) {
            $start = Carbon::create((int) $filters['tahun'], 1, 1)->startOfYear();
            $query->whereBetween('created_at', [$start, $start->copy()->endOfYear()]);
        } elseif (! empty($filters['bulan'])) {
            $query->whereMonth('created_at', (int) $filters['bulan']);
        }
    }

    public function parseAndApply(Builder $query, string $keyword): void
    {
        $parsed = $keyword === '' ? null : ActivitySearchParser::parse($keyword);

        if ($parsed === null || $parsed['type'] === 'empty') {
            return;
        }

        switch ($parsed['type']) {
            case 'numeric':
                $value = (int) $parsed['value'];

                if ($value >= 1900 && $value <= 2100) {
                    CreatedAtSearch::applyYear($query, $value);
                    break;
                }

                $this->applyNumericSearch($query, (string) $parsed['value'], $value);
                break;

            case 'date':
                CreatedAtSearch::applyDate($query, $parsed['date'], $parsed['time'] ?? null);
                break;

            case 'month':
                $this->applyMonthOrText(
                    $query,
                    [(int) $parsed['month']],
                    $parsed['year'] ?? null,
                    $parsed['time'] ?? null,
                    $keyword
                );
                break;

            case 'month_partial':
                $this->applyMonthOrText(
                    $query,
                    $parsed['months'],
                    $parsed['year'] ?? null,
                    $parsed['time'] ?? null,
                    $keyword
                );
                break;

            case 'time':
                CreatedAtSearch::applyTime($query, $parsed['time']);
                break;

            default:
                $this->applyTextSearch($query, (string) $parsed['keyword']);
        }
    }

    public function applyRelevance(Builder $query, string $keyword): void
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            return;
        }

        $parsed = ActivitySearchParser::parse($keyword);
        $months = $this->monthsForParsed($parsed, $keyword);

        if ($months !== []) {
            $placeholders = implode(', ', array_fill(0, count($months), '?'));
            $query->orderByRaw(
                "CASE WHEN MONTH(created_at) IN ({$placeholders}) THEN 0 ELSE 1 END",
                $months
            );
        }

        if ($parsed['type'] === 'numeric') {
            $value = (int) $parsed['value'];

            $query->orderByRaw(
                'CASE WHEN id = ? THEN 1 WHEN akun_id = ? THEN 2 WHEN reference = ? THEN 3 WHEN nama = ? THEN 4 ELSE 5 END',
                [$value, $value, $keyword, $keyword]
            );

            return;
        }

        $like = '%' . SearchHelper::escapeLike($keyword) . '%';

        $query->orderByRaw(
            'CASE
                WHEN reference = ? THEN 1
                WHEN nama = ? THEN 2
                WHEN nama LIKE ? THEN 3
                WHEN role LIKE ? OR instansi LIKE ? THEN 4
                WHEN activity_type LIKE ? OR module LIKE ? THEN 5
                WHEN description LIKE ? THEN 6
                ELSE 7
             END',
            [
                $keyword,
                $keyword,
                $like,
                $like,
                $like,
                $like,
                $like,
                $like,
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $parsed
     * @return array<int, int>
     */
    private function monthsForParsed(array $parsed, string $keyword): array
    {
        return match ($parsed['type']) {
            'month'         => [(int) $parsed['month']],
            'month_partial' => array_map('intval', $parsed['months']),
            'text'          => ActivitySearchParser::monthNumbersForKeyword($keyword),
            default         => [],
        };
    }

    /**
     * Kata kunci bulan mencakup data bulan tersebut ATAU teks yang cocok.
     *
     * @param  array<int, int>  $months
     */
    private function applyMonthOrText(
        Builder $query,
        array $months,
        ?int $year,
        ?string $time,
        string $keyword
    ): void {
        $query->where(function (Builder $sub) use ($months, $year, $time, $keyword) {
            $sub->where(function (Builder $monthQuery) use ($months, $year, $time) {
                CreatedAtSearch::applyMonths($monthQuery, $months, $year, $time);
            });

            $sub->orWhere(function (Builder $textQuery) use ($keyword) {
                $this->addTextConditions($textQuery, $keyword);
            });
        });
    }

    private function applyTextSearch(Builder $query, string $keyword): void
    {
        $months = ActivitySearchParser::monthNumbersForKeyword($keyword);

        $query->where(function (Builder $sub) use ($keyword, $months) {
            $this->addTextConditions($sub, $keyword);

            foreach ($months as $month) {
                $sub->orWhereMonth('created_at', $month);
            }
        });
    }

    private function addTextConditions(Builder $sub, string $keyword): void
    {
        $like = '%' . SearchHelper::escapeLike($keyword) . '%';
        $typeKeys = ActivityLog::typeKeysForKeyword($keyword);
        $roleKeys = ActivityLog::roleKeysForKeyword($keyword);
        $statusKeys = ActivityLog::statusKeysForKeyword($keyword);
        $moduleKeys = ActivityLog::moduleKeysForKeyword($keyword);
        $accountIds = Akun::where('kode_akun', 'like', $like)->pluck('id_akun');

        $sub->where('nama', 'like', $like)
            ->orWhere('role', 'like', $like)
            ->orWhere('instansi', 'like', $like)
            ->orWhere('activity_type', 'like', $like)
            ->orWhere('module', 'like', $like)
            ->orWhere('reference', 'like', $like)
            ->orWhere('description', 'like', $like)
            ->orWhere('status', 'like', $like);

        if ($accountIds->isNotEmpty()) {
            $sub->orWhereIn('akun_id', $accountIds);
        }

        if ($typeKeys !== []) {
            $sub->orWhereIn('activity_type', $typeKeys);
        }

        if ($roleKeys !== []) {
            $sub->orWhereIn('role', $roleKeys);
        }

        if ($statusKeys !== []) {
            $sub->orWhereIn('status', $statusKeys);
        }

        if ($moduleKeys !== []) {
            $sub->orWhereIn('module', $moduleKeys);
        }
    }

    private function applyNumericSearch(Builder $query, string $keyword, int $accountId): void
    {
        $like = '%' . SearchHelper::escapeLike($keyword) . '%';
        $days = ActivitySearchParser::dayNumbersForKeyword($keyword);

        $query->where(function (Builder $sub) use ($like, $accountId, $days) {
            $sub->where('id', $accountId)
                ->orWhere('akun_id', $accountId);

            foreach (['nama', 'role', 'instansi', 'activity_type', 'module', 'reference', 'description', 'status'] as $column) {
                $sub->orWhere($column, 'like', $like);
            }

            if ($days !== []) {
                $sub->orWhere(function (Builder $dayQuery) use ($days) {
                    CreatedAtSearch::applyDays($dayQuery, $days);
                });
            }
        });
    }
}
