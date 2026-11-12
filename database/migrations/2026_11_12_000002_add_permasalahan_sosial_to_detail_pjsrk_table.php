<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Laporan permasalahan sosial dari aplikasi mobile hanya menyimpan jenis
     * permasalahan; uraian kejadian tetap memakai detail_kasus.kronologi.
     */
    public function up(): void
    {
        Schema::table('detail_pjsrk', function (Blueprint $table) {
            $table->string('jenis_permasalahan', 100)
                ->nullable()
                ->after('jenis_rehabilitasi');
        });
    }

    public function down(): void
    {
        Schema::table('detail_pjsrk', function (Blueprint $table) {
            $table->dropColumn('jenis_permasalahan');
        });
    }
};
