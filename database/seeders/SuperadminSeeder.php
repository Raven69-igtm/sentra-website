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

            // Informasi
            ['name' => 'informasi.manage', 'display_name' => 'Mengelola Konten Informasi & Edukasi', 'group' => 'Informasi'],

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

        $roleAdmin = Role::updateOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Admin Operasional',
                'description' => 'Petugas operasional yang menangani laporan, rekapan, informasi, dan konsultasi chat.',
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

        // Assign operational permissions to admin
        $adminPermissions = Permission::whereIn('name', [
            'laporan.view', 'laporan.process', 'laporan.rujuk',
            'informasi.manage', 'chat.manage', 'rekapan.export'
        ])->pluck('id')->toArray();
        $roleAdmin->permissions()->sync($adminPermissions);

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
        $defaultSettings = [
            // General
            ['key' => 'app_name', 'value' => 'SENTRA', 'group' => 'general', 'label' => 'Nama Aplikasi', 'type' => 'text'],
            ['key' => 'instansi_name', 'value' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak', 'group' => 'general', 'label' => 'Nama Instansi', 'type' => 'text'],
            ['key' => 'app_description', 'value' => 'Sistem Informasi dan Pelayanan Terpadu Penanganan Kekerasan terhadap Perempuan dan Anak', 'group' => 'general', 'label' => 'Deskripsi Aplikasi', 'type' => 'textarea'],
            ['key' => 'footer_copyright', 'value' => '© 2026 SENTRA. All rights reserved.', 'group' => 'general', 'label' => 'Hak Cipta Footer', 'type' => 'text'],

            // Kontak
            ['key' => 'hotline_number', 'value' => '0812-3456-7890', 'group' => 'kontak', 'label' => 'Nomor WhatsApp Hotline', 'type' => 'text'],
            ['key' => 'emergency_call', 'value' => '112 / 129', 'group' => 'kontak', 'label' => 'Call Center Darurat', 'type' => 'text'],
            ['key' => 'office_email', 'value' => 'sentraapplication@gmail.com', 'group' => 'kontak', 'label' => 'Email Layanan', 'type' => 'text'],
            ['key' => 'office_address', 'value' => 'Jl. Pemuda No. 45, Kompleks Perkantoran Pemerintah', 'group' => 'kontak', 'label' => 'Alamat Kantor', 'type' => 'textarea'],

            // Laporan
            ['key' => 'report_sla_days', 'value' => '3', 'group' => 'laporan', 'label' => 'Target Respon Laporan (Hari)', 'type' => 'number'],
            ['key' => 'auto_archive_months', 'value' => '24', 'group' => 'laporan', 'label' => 'Retensi Arsip (Bulan)', 'type' => 'number'],
            ['key' => 'enable_chat_consultation', 'value' => '1', 'group' => 'laporan', 'label' => 'Layanan Konsultasi Chat', 'type' => 'boolean'],

            // Keamanan
            ['key' => 'session_lifetime_minutes', 'value' => '120', 'group' => 'keamanan', 'label' => 'Durasi Sesi Login (Menit)', 'type' => 'number'],
            ['key' => 'max_login_attempts', 'value' => '5', 'group' => 'keamanan', 'label' => 'Maksimal Percobaan Login Salah', 'type' => 'number'],
            ['key' => 'otp_expiry_minutes', 'value' => '5', 'group' => 'keamanan', 'label' => 'Masa Berlaku OTP (Menit)', 'type' => 'number'],
        ];

        foreach ($defaultSettings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
