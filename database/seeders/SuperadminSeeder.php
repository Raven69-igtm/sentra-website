<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Akun;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemSetting;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SEED PERMISSIONS
        $permissions = [
            // Akun
            ['name' => 'akun.view', 'display_name' => 'Melihat Daftar Akun', 'group' => 'Manajemen Akun'],
            ['name' => 'akun.create', 'display_name' => 'Membuat Akun Baru', 'group' => 'Manajemen Akun'],
            ['name' => 'akun.edit', 'display_name' => 'Mengubah Data Akun', 'group' => 'Manajemen Akun'],
            ['name' => 'akun.delete', 'display_name' => 'Menghapus Akun', 'group' => 'Manajemen Akun'],
            ['name' => 'akun.reset_password', 'display_name' => 'Mereset Password Akun', 'group' => 'Manajemen Akun'],

            // Role & Permission
            ['name' => 'role.view', 'display_name' => 'Melihat Daftar Role', 'group' => 'Role & Permission'],
            ['name' => 'role.create', 'display_name' => 'Membuat Role Baru', 'group' => 'Role & Permission'],
            ['name' => 'role.edit', 'display_name' => 'Mengubah Role & Hak Akses', 'group' => 'Role & Permission'],
            ['name' => 'role.delete', 'display_name' => 'Menghapus Role', 'group' => 'Role & Permission'],

            // Konfigurasi Sistem
            ['name' => 'setting.view', 'display_name' => 'Melihat Pengaturan Sistem', 'group' => 'Konfigurasi Sistem'],
            ['name' => 'setting.edit', 'display_name' => 'Mengubah Pengaturan Sistem', 'group' => 'Konfigurasi Sistem'],

            // Laporan
            ['name' => 'laporan.view', 'display_name' => 'Melihat Data Laporan', 'group' => 'Laporan'],
            ['name' => 'laporan.process', 'display_name' => 'Memproses Status Laporan', 'group' => 'Laporan'],
            ['name' => 'laporan.rujuk', 'display_name' => 'Merujuk Laporan ke Lembaga Lain', 'group' => 'Laporan'],
            ['name' => 'laporan.delete', 'display_name' => 'Menghapus Laporan', 'group' => 'Laporan'],

            // Chat
            ['name' => 'chat.manage', 'display_name' => 'Mengelola Chat & Konsultasi', 'group' => 'Chat Konsultasi'],

            // Rekapan
            ['name' => 'rekapan.export', 'display_name' => 'Mengunduh Export Rekapan Laporan', 'group' => 'Rekapan'],
        ];

        $permissionModels = [];
        foreach ($permissions as $p) {
            $permissionModels[$p['name']] = Permission::updateOrCreate(
                ['name' => $p['name']],
                ['display_name' => $p['display_name'], 'group' => $p['group']]
            );
        }

        // 2. SEED ROLES
        $roleSuperadmin = Role::updateOrCreate(
            ['name' => 'superadmin'],
            [
                'display_name' => 'Superadmin',
                'description' => 'Akses penuh mengelola seluruh akun, role, dan konfigurasi sistem.',
                'is_system' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'User / Pelapor',
                'description' => 'Masyarakat pelapor pengaduan kasus kekerasan perempuan dan anak.',
                'is_system' => true,
            ]
        );

        Role::updateOrCreate(
            ['name' => 'guest'],
            [
                'display_name' => 'Guest',
                'description' => 'Akses publik terbatas untuk penelusuran status laporan.',
                'is_system' => true,
            ]
        );

        // Assign all permissions to superadmin
        $roleSuperadmin->permissions()->sync(Permission::pluck('id')->toArray());

        // 3. SEED DEFAULT SUPERADMIN ACCOUNT
        $existingSuperadmin = Akun::where('email', 'superadmin@sentra.com')->first();
        if (!$existingSuperadmin) {
            Akun::create([
                'nama' => 'Superadmin Sentra',
                'email' => 'superadmin@sentra.com',
                'notelp' => '081299998888',
                'password' => 'Superadmin123!',
                'role' => 'superadmin',
                'is_active' => true,
            ]);
        } else {
            $existingSuperadmin->update([
                'role' => 'superadmin',
                'is_active' => true,
            ]);
        }

        // 4. SEED DEFAULT SYSTEM SETTINGS
        // Daftar key, default, dan label berasal dari App\Support\SettingSchema
        // agar tidak ada duplikasi nilai default antara seeder dan tampilan.
        // firstOrCreate dipakai supaya nilai yang sudah diubah Superadmin
        // tidak tertimpa ketika seeder dijalankan ulang.
        foreach (\App\Support\SettingSchema::seedRows() as $setting) {
            SystemSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
