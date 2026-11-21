<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Akun lama dapat memiliki is_active NULL karena kolom ditambahkan
        // setelah data sudah ada. Tampilan menganggap NULL sebagai aktif,
        // jadi samakan datanya agar filter & hitungan konsisten.
        DB::table('akun')
            ->whereNull('is_active')
            ->update(['is_active' => true]);

        // Normalisasi role legacy "Pelapor Cepat" ke role guest yang
        // terdaftar di tabel roles.
        DB::table('akun')
            ->where('role', 'Pelapor Cepat')
            ->update(['role' => 'guest']);
    }

    public function down(): void
    {
        // Tidak ada rollback yang aman: nilai NULL lama tidak dapat
        // dibedakan lagi setelah backfill.
    }
};
