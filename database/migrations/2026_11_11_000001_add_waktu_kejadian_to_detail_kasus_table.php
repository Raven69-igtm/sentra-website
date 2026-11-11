<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_kasus', function (Blueprint $table) {
            $table->time('waktu_kejadian')->nullable()->after('tanggal');
        });
    }

    public function down(): void
    {
        Schema::table('detail_kasus', function (Blueprint $table) {
            $table->dropColumn('waktu_kejadian');
        });
    }
};
