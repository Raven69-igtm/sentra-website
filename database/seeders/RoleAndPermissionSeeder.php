<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Roles bawaan
        $roles = [
            ['name' => 'superadmin',   'display_name' => 'Superadmin',   'description' => 'Akses penuh ke seluruh sistem',          'is_system' => true],
            ['name' => 'admin_ppa',    'display_name' => 'Admin PPA',    'description' => 'Administrator UPTD PPA',                 'is_system' => true],
            ['name' => 'admin_dinsos', 'display_name' => 'Admin Dinsos', 'description' => 'Administrator Dinas Sosial',             'is_system' => true],
            ['name' => 'user',         'display_name' => 'Masyarakat',   'description' => 'Pengguna pelapor masyarakat umum',       'is_system' => true],
            ['name' => 'guest',        'display_name' => 'Guest',        'description' => 'Pengguna tamu dengan akses terbatas',    'is_system' => true],
        ];

        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r['name']], $r);
        }

        // Permissions bawaan
        $permissions = [
            // Laporan
            ['name' => 'laporan.view',   'display_name' => 'Lihat Laporan',   'group' => 'Laporan'],
            ['name' => 'laporan.create', 'display_name' => 'Buat Laporan',    'group' => 'Laporan'],
            ['name' => 'laporan.edit',   'display_name' => 'Edit Laporan',    'group' => 'Laporan'],
            ['name' => 'laporan.delete', 'display_name' => 'Hapus Laporan',   'group' => 'Laporan'],
            ['name' => 'laporan.verify', 'display_name' => 'Verifikasi Laporan', 'group' => 'Laporan'],
            ['name' => 'laporan.refer',  'display_name' => 'Rujuk Laporan',   'group' => 'Laporan'],

            // Akun
            ['name' => 'akun.view',   'display_name' => 'Lihat Akun',   'group' => 'Akun'],
            ['name' => 'akun.create', 'display_name' => 'Buat Akun',    'group' => 'Akun'],
            ['name' => 'akun.edit',   'display_name' => 'Edit Akun',    'group' => 'Akun'],
            ['name' => 'akun.delete', 'display_name' => 'Hapus Akun',   'group' => 'Akun'],

            // Role & Permission
            ['name' => 'role.view',   'display_name' => 'Lihat Role',   'group' => 'Role & Permission'],
            ['name' => 'role.create', 'display_name' => 'Buat Role',    'group' => 'Role & Permission'],
            ['name' => 'role.edit',   'display_name' => 'Edit Role',    'group' => 'Role & Permission'],
            ['name' => 'role.delete', 'display_name' => 'Hapus Role',   'group' => 'Role & Permission'],

            // Sistem
            ['name' => 'settings.view',   'display_name' => 'Lihat Pengaturan', 'group' => 'Sistem'],
            ['name' => 'settings.update', 'display_name' => 'Ubah Pengaturan',  'group' => 'Sistem'],
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p['name']], $p);
        }

        // Assign permissions ke role admin_ppa
        $adminPpa = Role::where('name', 'admin_ppa')->first();
        if ($adminPpa) {
            $ppaPermissions = Permission::whereIn('name', [
                'laporan.view', 'laporan.create', 'laporan.edit', 'laporan.verify',
            ])->pluck('id');
            $adminPpa->permissions()->syncWithoutDetaching($ppaPermissions);
        }

        // Assign permissions ke role admin_dinsos
        $adminDinsos = Role::where('name', 'admin_dinsos')->first();
        if ($adminDinsos) {
            $dinsosPermissions = Permission::whereIn('name', [
                'laporan.view', 'laporan.edit', 'laporan.refer',
            ])->pluck('id');
            $adminDinsos->permissions()->syncWithoutDetaching($dinsosPermissions);
        }
    }
}
