<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            if (!Schema::hasIndex('activity_logs', 'activity_logs_role_index')) {
                $table->index('role');
            }

            if (!Schema::hasIndex('activity_logs', 'activity_logs_module_index')) {
                $table->index('module');
            }

            if (!Schema::hasIndex('activity_logs', 'activity_logs_status_index')) {
                $table->index('status');
            }

            if (!Schema::hasIndex('activity_logs', 'activity_logs_ip_address_index')) {
                $table->index('ip_address');
            }

            if (!Schema::hasIndex('activity_logs', 'activity_logs_created_at_index')) {
                $table->index('created_at');
            }

            if (!Schema::hasIndex('activity_logs', 'activity_logs_instansi_type_created_index')) {
                $table->index(['instansi', 'activity_type', 'created_at'], 'activity_logs_instansi_type_created_index');
            }
        });

        Schema::table('akun', function (Blueprint $table) {
            if (!Schema::hasIndex('akun', 'akun_role_index')) {
                $table->index('role');
            }

            if (!Schema::hasIndex('akun', 'akun_is_active_index')) {
                $table->index('is_active');
            }

            if (!Schema::hasIndex('akun', 'akun_jenis_kelamin_index')) {
                $table->index('jenis_kelamin');
            }

            if (!Schema::hasIndex('akun', 'akun_created_at_index')) {
                $table->index('created_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            foreach ([
                'activity_logs_role_index',
                'activity_logs_module_index',
                'activity_logs_status_index',
                'activity_logs_ip_address_index',
                'activity_logs_created_at_index',
                'activity_logs_instansi_type_created_index',
            ] as $index) {
                if (Schema::hasIndex('activity_logs', $index)) {
                    $table->dropIndex($index);
                }
            }
        });

        Schema::table('akun', function (Blueprint $table) {
            foreach ([
                'akun_role_index',
                'akun_is_active_index',
                'akun_jenis_kelamin_index',
                'akun_created_at_index',
            ] as $index) {
                if (Schema::hasIndex('akun', $index)) {
                    $table->dropIndex($index);
                }
            }
        });
    }
};
