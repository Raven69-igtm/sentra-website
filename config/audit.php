<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Retensi Audit
    |--------------------------------------------------------------------------
    |
    | Audit umum (perubahan data, role, setting, laporan, dokumen) disimpan
    | lebih lama daripada aktivitas autentikasi rutin.
    |
    */

    'retention_days' => (int) env('AUDIT_RETENTION_DAYS', 365),

    'auth_retention_days' => (int) env('AUDIT_AUTH_RETENTION_DAYS', 90),

    /*
    |--------------------------------------------------------------------------
    | Arsip
    |--------------------------------------------------------------------------
    |
    | Sebelum dihapus, baris audit ditulis ke berkas JSONL agar tetap dapat
    | diaudit tanpa membebani tabel utama.
    |
    */

    'archive_disk' => env('AUDIT_ARCHIVE_DISK', 'local'),

    'archive_path' => env('AUDIT_ARCHIVE_PATH', 'audit-archive'),

];
