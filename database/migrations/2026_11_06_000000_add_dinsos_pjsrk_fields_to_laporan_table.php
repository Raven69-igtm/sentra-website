<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('laporan', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan', 'judul')) {
                $table->string('judul', 150)->nullable()->after('id_akun');
            }

            if (!Schema::hasColumn('laporan', 'kecamatan')) {
                $table->string('kecamatan', 100)->nullable()->after('judul');
            }

            if (!Schema::hasColumn('laporan', 'layanan_pjsrk')) {
                $table->enum('layanan_pjsrk', [
                    'rehabilitasi_sosial',
                    'pengaduan_bencana',
                    'permasalahan_sosial',
                ])->nullable()->after('kategori');
            }

            if (!Schema::hasColumn('laporan', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('status');
            }
        });

        // Perluas enum status tanpa membutuhkan doctrine/dbal.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE laporan
                MODIFY status ENUM('dikirim','diterima','diproses','dirujuk','selesai','ditolak')
                NOT NULL DEFAULT 'dikirim'
            ");
        }

        // Backfill data lama agar laporan yang sudah ada tetap masuk scope Dinsos.
        DB::table('laporan')
            ->where('kategori', 'Penelantaran')
            ->whereNull('layanan_pjsrk')
            ->update(['layanan_pjsrk' => 'rehabilitasi_sosial']);

        DB::table('laporan')
            ->where('kategori', 'Lainnya')
            ->whereNull('layanan_pjsrk')
            ->update(['layanan_pjsrk' => 'permasalahan_sosial']);
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE laporan
                MODIFY status ENUM('dikirim','diterima','diproses','dirujuk','selesai')
                NOT NULL DEFAULT 'dikirim'
            ");
        }

        Schema::table('laporan', function (Blueprint $table) {
            foreach (['alasan_penolakan', 'layanan_pjsrk', 'kecamatan', 'judul'] as $column) {
                if (Schema::hasColumn('laporan', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
