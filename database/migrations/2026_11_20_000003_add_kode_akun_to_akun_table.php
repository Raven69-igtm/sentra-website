<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kode publik akun admin. Primary key tetap id_akun agar FK, API mobile,
     * token, dan URL lama tidak berubah.
     */
    public function up(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            $table->string('kode_akun', 32)->nullable()->unique()->after('id_akun');
        });

        $codes = [
            'superadmin@sentra.com'   => 'spadmin111',
            'admin.ppa@sentra.com'    => 'adminppa233',
            'admin.dinsos@sentra.com' => 'admindinsos433',
        ];

        foreach ($codes as $email => $code) {
            DB::table('akun')->where('email', $email)->update(['kode_akun' => $code]);
        }
    }

    public function down(): void
    {
        Schema::table('akun', function (Blueprint $table) {
            $table->dropUnique(['kode_akun']);
            $table->dropColumn('kode_akun');
        });
    }
};
