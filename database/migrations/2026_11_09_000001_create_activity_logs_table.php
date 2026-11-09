<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('activity_logs')) {
            Schema::create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('akun_id')->nullable()->index();
                $table->string('nama', 100);                       // snapshot nama pelaku aktivitas
                $table->string('role', 30);                        // snapshot role pelaku
                $table->string('instansi', 50);                    // UPTD PPA / Dinas Sosial / Superadmin
                $table->string('activity_type', 40)->index();      // login | logout | laporan_status | dokumen | data
                $table->string('module', 40)->nullable();          // auth | laporan | akun | role | setting | informasi
                $table->string('reference', 100)->nullable();      // referensi metadata (id laporan, email, dll)
                $table->text('description');                       // deskripsi metadata aktivitas (tanpa isi laporan)
                $table->string('status', 20)->default('berhasil'); // berhasil | gagal
                $table->string('ip_address', 45)->nullable();
                $table->string('user_agent', 255)->nullable();
                $table->timestamps();

                $table->index(['instansi', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
