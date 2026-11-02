<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            // Ubah role ke string agar fleksibel mendukung superadmin dan role kustom
            $table->string('role', 30)->default('guest')->change();
            if (!Schema::hasColumn('akun', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            if (Schema::hasColumn('akun', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
