<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Menghapus role lama "admin" (Admin Operasional).
 *
 * Role ini sudah digantikan oleh admin_ppa dan admin_dinsos. Akun yang
 * masih memakai role lama dipindahkan ke admin_ppa terlebih dahulu agar
 * tidak ada akun yang kehilangan akses.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('akun')
            ->where('role', 'admin')
            ->update([
                'role' => 'admin_ppa',
                'updated_at' => now(),
            ]);

        $roleId = DB::table('roles')->where('name', 'admin')->value('id');

        if ($roleId !== null) {
            DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }
    }

    public function down(): void
    {
        $roleId = DB::table('roles')->where('name', 'admin')->value('id');

        if ($roleId === null) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'admin',
                'display_name' => 'Admin Operasional',
                'description' => 'Petugas operasional yang menangani laporan, rekapan, dan konsultasi chat.',
                'is_system' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $permissionIds = DB::table('permissions')->whereIn('name', [
            'laporan.view', 'laporan.process', 'laporan.rujuk', 'chat.manage', 'rekapan.export',
        ])->pluck('id');

        foreach ($permissionIds as $permissionId) {
            DB::table('role_has_permissions')->insertOrIgnore([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }
};
