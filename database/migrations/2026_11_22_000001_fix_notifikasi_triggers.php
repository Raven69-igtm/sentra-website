<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Memperbaiki trigger notifikasi.
 *
 * Masalah sebelumnya:
 *  - after_laporan_insert_admin selalu mengirim notifikasi ke id_akun = 1
 *    (akun pelapor biasa), sehingga lonceng Admin PPA/Dinsos selalu kosong.
 *  - after_chat_insert selalu menulis tipe = 'user', sehingga pesan untuk
 *    admin tidak muncul di lonceng panel admin.
 *
 * Perbaikan:
 *  - Laporan Dinsos (layanan PJSRK terisi, atau kategori legacy
 *    "Penelantaran"/"Lainnya" tanpa layanan) → semua Admin Dinsos aktif.
 *  - Laporan PPA (sisanya) → semua Admin PPA aktif.
 *  - Chat → tipe 'admin' bila penerima akun admin, selain itu 'user'.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `after_laporan_insert_admin`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_chat_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_laporan_insert_user`');

        DB::unprepared('
            CREATE TRIGGER `after_laporan_insert_admin`
            AFTER INSERT ON `laporan`
            FOR EACH ROW
            BEGIN
                IF (
                    (NEW.layanan_pjsrk IS NOT NULL AND NEW.layanan_pjsrk <> \'\')
                    OR (NEW.layanan_pjsrk IS NULL AND NEW.kategori IN (\'Penelantaran\', \'Lainnya\'))
                ) THEN
                    INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                    SELECT
                        a.id_akun,
                        CONCAT(NEW.id_laporan, " - Laporan Baru"),
                        CONCAT(
                            "Laporan baru dengan ID ", NEW.id_laporan,
                            " telah dibuat oleh ",
                            COALESCE(
                                (SELECT nama FROM akun WHERE id_akun = NEW.id_akun),
                                "pelapor anonim"
                            )
                        ),
                        "admin",
                        "terkirim",
                        NOW()
                    FROM akun a
                    WHERE a.is_active = 1
                      AND a.role = "admin_dinsos";
                ELSE
                    INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                    SELECT
                        a.id_akun,
                        CONCAT(NEW.id_laporan, " - Laporan Baru"),
                        CONCAT(
                            "Laporan baru dengan ID ", NEW.id_laporan,
                            " telah dibuat oleh ",
                            COALESCE(
                                (SELECT nama FROM akun WHERE id_akun = NEW.id_akun),
                                "pelapor anonim"
                            )
                        ),
                        "admin",
                        "terkirim",
                        NOW()
                    FROM akun a
                    WHERE a.is_active = 1
                      AND a.role IN ("admin_ppa", "admin");
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER `after_chat_insert`
            AFTER INSERT ON `chats`
            FOR EACH ROW
            BEGIN
                INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                SELECT
                    NEW.receiver_id,
                    "Pesan Baru",
                    CONCAT(
                        "Anda menerima pesan baru dari ",
                        COALESCE(
                            (SELECT nama FROM akun WHERE id_akun = NEW.sender_id),
                            "Pengguna"
                        ),
                        ": ", NEW.message
                    ),
                    CASE
                        WHEN a.role IN ("admin_ppa", "admin", "admin_dinsos", "superadmin")
                        THEN "admin"
                        ELSE "user"
                    END,
                    "terkirim",
                    NOW()
                FROM akun a
                WHERE a.id_akun = NEW.receiver_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER `after_laporan_insert_user`
            AFTER INSERT ON `laporan`
            FOR EACH ROW
            BEGIN
                INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                VALUES (
                    NEW.id_akun,
                    CONCAT(NEW.id_laporan, " - Laporan Terkirim"),
                    CONCAT(
                        "Laporan Anda dengan ID ", NEW.id_laporan,
                        " telah berhasil dikirim dan sedang menunggu verifikasi."
                    ),
                    "user",
                    "terkirim",
                    NOW()
                );
            END
        ');

        // Bersihkan notifikasi admin lama yang salah alamat ke akun non-admin
        // (mis. id_akun = 1 dengan role user) agar tidak tercampur di mobile.
        DB::table('notifikasi')
            ->where('tipe', 'admin')
            ->whereIn('id_akun', function ($query) {
                $query->select('id_akun')
                    ->from('akun')
                    ->whereNotIn('role', ['admin_ppa', 'admin', 'admin_dinsos', 'superadmin']);
            })
            ->delete();
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `after_laporan_insert_admin`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_chat_insert`');
        DB::unprepared('DROP TRIGGER IF EXISTS `after_laporan_insert_user`');

        // Kembalikan definisi lama (untuk rollback).
        DB::unprepared('
            CREATE TRIGGER `after_chat_insert`
            AFTER INSERT ON `chats`
            FOR EACH ROW
            BEGIN
                INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                VALUES (
                    NEW.receiver_id,
                    "Pesan Baru",
                    CONCAT(
                        "Anda menerima pesan baru dari ",
                        (SELECT nama FROM akun WHERE id_akun = NEW.sender_id),
                        ": ", NEW.message
                    ),
                    "user",
                    "terkirim",
                    NOW()
                );
            END
        ');

        DB::unprepared('
            CREATE TRIGGER `after_laporan_insert_admin`
            AFTER INSERT ON `laporan`
            FOR EACH ROW
            BEGIN
                INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                SELECT
                    id_akun,
                    CONCAT(NEW.id_laporan, " - Laporan Baru"),
                    CONCAT(
                        "Laporan baru dengan ID ", NEW.id_laporan,
                        " telah dibuat oleh ",
                        (SELECT nama FROM akun WHERE id_akun = NEW.id_akun)
                    ),
                    "admin",
                    "terkirim",
                    NOW()
                FROM akun
                WHERE id_akun = 1;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER `after_laporan_insert_user`
            AFTER INSERT ON `laporan`
            FOR EACH ROW
            BEGIN
                INSERT INTO notifikasi (id_akun, judul, pesan, tipe, status, created_at)
                VALUES (
                    NEW.id_akun,
                    CONCAT(NEW.id_laporan, " - Laporan Terkirim"),
                    CONCAT(
                        "Laporan Anda dengan ID ", NEW.id_laporan,
                        " telah berhasil dikirim dan sedang menunggu verifikasi."
                    ),
                    "user",
                    "terkirim",
                    NOW()
                );
            END
        ');
    }
};
