<?php

namespace App\Livewire;

use App\Models\ActivityLog;
use App\Models\Laporan;
use App\Models\Notifikasi;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Lonceng notifikasi panel Admin PPA & Admin Dinsos.
 *
 * Satu komponen dipakai di kedua panel agar logika pengambilan notifikasi,
 * penandaan "dibaca", dan penerimaan laporan hanya ada di satu tempat.
 * Tampilan dibedakan lewat properti $variant:
 *   - ppa    → dropdown Bootstrap di navbar panel PPA (termasuk modal daftar)
 *   - dinsos → panel notifikasi kustom pada topbar Dinsos
 */
class NotifikasiBell extends Component
{
    /** Panel asal komponen: "ppa" atau "dinsos". */
    public string $variant = 'ppa';

    /** Status buka/tutup dropdown atau panel notifikasi. */
    public bool $open = false;

    /** Status buka/tutup modal "Daftar Notifikasi" (khusus panel PPA). */
    public bool $showAll = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    public function close(): void
    {
        $this->open = false;
    }

    public function openAll(): void
    {
        $this->open = false;
        $this->showAll = true;
    }

    public function closeAll(): void
    {
        $this->showAll = false;
    }

    /**
     * Tandai satu notifikasi sebagai dibaca.
     */
    public function tandaiDibaca(int $idNotif): void
    {
        $this->notifikasiQuery()
            ->whereKey($idNotif)
            ->update(['status' => 'dibaca']);
    }

    /**
     * Terima laporan yang dirujuk sebuah notifikasi.
     *
     * Perilaku ini mengikuti NotifikasiController@terimaLaporan: status
     * notifikasi menjadi "dibaca" dan status laporan menjadi "diterima".
     */
    public function terimaLaporan(int $idNotif): void
    {
        $notifikasi = $this->notifikasiQuery()->whereKey($idNotif)->first();

        if (! $notifikasi) {
            return;
        }

        $idLaporan = $this->laporanIdDari($notifikasi);
        $laporan = $idLaporan ? Laporan::find($idLaporan) : null;

        if (! $laporan) {
            $this->tandaiDibaca($idNotif);

            $this->dispatch(
                'notif-terima',
                message: 'Notifikasi ditandai dibaca. Laporan terkait tidak ditemukan.'
            );

            return;
        }

        $laporan->status = 'diterima';
        $laporan->save();

        ActivityLog::record(
            'laporan_status',
            "Menerima laporan #{$laporan->id_laporan} melalui notifikasi (→ diterima).",
            'laporan',
            $laporan->id_laporan
        );

        $notifikasi->status = 'dibaca';
        $notifikasi->save();

        $this->dispatch(
            'notif-terima',
            message: "Laporan #{$laporan->id_laporan} berhasil diterima!"
        );
    }

    public function render(): View
    {
        $notifikasi = $this->notifikasiQuery()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $count = $this->notifikasiQuery()->count();

        $semua = $this->showAll
            ? $this->notifikasiQuery()->orderByDesc('created_at')->get()
            : collect();

        return view($this->variant === 'dinsos' ? 'livewire.notifikasi-dinsos' : 'livewire.notifikasi-bell', [
            'notifikasi' => $notifikasi,
            'count' => $count,
            'semua' => $semua,
            'laporanIds' => $this->laporanIdsUntuk($notifikasi->merge($semua)),
        ]);
    }

    /**
     * Notifikasi milik akun yang sedang login dan belum dibaca.
     */
    private function notifikasiQuery(): Builder
    {
        return Notifikasi::query()
            ->where('id_akun', Auth::id())
            ->where('tipe', 'admin')
            ->where('status', 'terkirim');
    }

    /**
     * Notifikasi laporan baru menyimpan id laporan di awal judul:
     * "12 - Laporan Baru: ...". Format lama ini dipertahankan agar
     * notifikasi yang sudah ada tetap dapat diproses.
     */
    private function laporanIdDari(Notifikasi $notifikasi): ?int
    {
        if (! str_contains((string) $notifikasi->judul, 'Laporan Baru')) {
            return null;
        }

        $bagian = explode(' - ', (string) $notifikasi->judul);

        if (count($bagian) < 2) {
            return null;
        }

        $id = trim($bagian[0]);

        return ctype_digit($id) ? (int) $id : null;
    }

    /**
     * Peta id_notif → id_laporan agar view tidak perlu mem-parsing judul.
     *
     * @param  Collection<int, Notifikasi>  $daftar
     * @return array<int, int|null>
     */
    private function laporanIdsUntuk(Collection $daftar): array
    {
        return $daftar
            ->mapWithKeys(fn (Notifikasi $notifikasi) => [
                $notifikasi->id_notif => $this->laporanIdDari($notifikasi),
            ])
            ->all();
    }
}
