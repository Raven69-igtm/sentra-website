<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pjsrk', function (Blueprint $table) {
            $table->id('id_pjsrk');
            $table->string('id_laporan', 50)->unique();

            // Pengaduan bencana
            $table->string('jenis_bencana', 100)->nullable();
            $table->string('tingkat_dampak', 20)->nullable();
            $table->unsignedInteger('jiwa_terdampak')->nullable();
            $table->unsignedInteger('rumah_rusak')->nullable();
            $table->json('kebutuhan_mendesak')->nullable();
            $table->text('keterangan')->nullable();

            // Rehabilitasi sosial
            $table->string('jenis_rehabilitasi', 50)->nullable();
            $table->unsignedInteger('jumlah_penerima')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('kelompok_usia', 30)->nullable();
            $table->string('kondisi_saat_ditemukan', 30)->nullable();
            $table->text('ciri_ciri')->nullable();
            $table->boolean('rahasia')->default(false);

            // Lokasi & kontak (dipakai kedua layanan)
            $table->string('alamat', 255)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('patokan_lokasi', 150)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('foto')->nullable();
            $table->text('kontak')->nullable();

            $table->foreign('id_laporan')
                ->references('id_laporan')
                ->on('laporan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pjsrk');
    }
};
