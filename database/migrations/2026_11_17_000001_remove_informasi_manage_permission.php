<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menghapus permission "informasi.manage".
 *
 * Fitur pengelolaan Informasi sudah dipindahkan ke halaman Settings
 * Superadmin dan tidak lagi memakai permission ini. Baris pivot pada
 * role_has_permissions ikut terhapus lewat foreign key onDelete cascade.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            DB::table('permissions')->where('name', 'informasi.manage')->delete();
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        $exists = DB::table('permissions')->where('name', 'informasi.manage')->exists();

        if (! $exists) {
            DB::table('permissions')->insert([
                'name' => 'informasi.manage',
                'display_name' => 'Mengelola Konten Informasi & Edukasi',
                'group' => 'Informasi',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
};
