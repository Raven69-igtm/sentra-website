<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

/**
 * Arsipkan lalu hapus activity_logs yang melewati masa retensi.
 *
 * Login/logout memakai retensi lebih pendek (default 90 hari) karena
 * rutin, sedangkan aktivitas administratif lain disimpan lebih lama
 * (default 365 hari).
 */
class PruneAuditLogs extends Command
{
    protected $signature = 'audit:prune
        {--days= : Retensi audit umum dalam hari (default dari config/audit.php)}
        {--auth-days= : Retensi login/logout dalam hari (default dari config/audit.php)}
        {--dry-run : Tampilkan jumlah yang akan dihapus tanpa mengarsipkan atau menghapus}
        {--no-archive : Hapus tanpa menulis arsip JSONL}';

    protected $description = 'Arsipkan dan hapus catatan audit yang melewati masa retensi.';

    private const AUTH_TYPES = ['login', 'logout'];

    public function handle(): int
    {
        $days = $this->positiveInt($this->option('days'), (int) config('audit.retention_days', 365));
        $authDays = $this->positiveInt($this->option('auth-days'), (int) config('audit.auth_retention_days', 90));

        $generalCutoff = now()->subDays($days);
        $authCutoff = now()->subDays($authDays);

        $query = $this->expiredQuery($generalCutoff, $authCutoff);
        $total = (clone $query)->count();

        $this->line("Retensi audit umum: {$days} hari (sebelum {$generalCutoff->toDateTimeString()}).");
        $this->line("Retensi login/logout: {$authDays} hari (sebelum {$authCutoff->toDateTimeString()}).");

        if ($total === 0) {
            $this->info('Tidak ada catatan audit yang melewati masa retensi.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn("[DRY RUN] {$total} catatan audit akan dihapus. Tidak ada perubahan yang dilakukan.");

            return self::SUCCESS;
        }

        if (! $this->option('no-archive')) {
            $path = $this->archive($query);

            if ($path === null) {
                $this->error('Pengarsipan gagal; penghapusan dibatalkan.');

                return self::FAILURE;
            }

            $this->info("Arsip ditulis: {$path} ({$total} baris).");
        }

        $deleted = $this->deleteExpired($query);

        $this->info("Selesai. {$deleted} catatan audit dihapus.");

        return self::SUCCESS;
    }

    private function expiredQuery($generalCutoff, $authCutoff): Builder
    {
        return ActivityLog::query()->where(function (Builder $query) use ($generalCutoff, $authCutoff) {
            $query->where(function (Builder $sub) use ($generalCutoff) {
                $sub->whereNotIn('activity_type', self::AUTH_TYPES)
                    ->where('created_at', '<', $generalCutoff);
            })->orWhere(function (Builder $sub) use ($authCutoff) {
                $sub->whereIn('activity_type', self::AUTH_TYPES)
                    ->where('created_at', '<', $authCutoff);
            });
        });
    }

    /**
     * Tulis baris kedaluwarsa ke berkas JSONL. Mengembalikan path relatif
     * atau null jika penulisan gagal.
     */
    private function archive(Builder $query): ?string
    {
        $disk = (string) config('audit.archive_disk', 'local');
        $directory = trim((string) config('audit.archive_path', 'audit-archive'), '/');
        $path = $directory . '/audit-' . now()->format('Ymd-His') . '.jsonl';

        try {
            $storage = Storage::disk($disk);

            $query->orderBy('id')->chunkById(500, function ($logs) use ($storage, $path) {
                $lines = '';

                foreach ($logs as $log) {
                    $lines .= json_encode(
                        $log->getAttributes(),
                        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                    ) . PHP_EOL;
                }

                $storage->append($path, $lines);
            });
        } catch (\Throwable $exception) {
            $this->error('Gagal menulis arsip: ' . $exception->getMessage());

            return null;
        }

        return $path;
    }

    private function deleteExpired(Builder $query): int
    {
        $deleted = 0;

        while (true) {
            $ids = (clone $query)->orderBy('id')->limit(500)->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $deleted += ActivityLog::whereIn('id', $ids)->delete();
        }

        return $deleted;
    }

    private function positiveInt(mixed $value, int $default): int
    {
        $value = (int) $value;

        return $value > 0 ? $value : $default;
    }
}
