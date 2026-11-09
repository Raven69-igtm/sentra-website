<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Akun;
use App\Models\Role;
use App\Models\Permission;

class AdminInstansiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. TAMBAH PERMISSIONS BARU
        $permissions = [
            ['name' => 'laporan.view_ppa', 'display_name' => 'Melihat Laporan Kasus PPA', 'group' => 'Laporan PPA'],
            ['name' => 'laporan.process_ppa', 'display_name' => 'Memproses Laporan Kasus PPA', 'group' => 'Laporan PPA'],
            ['name' => 'laporan.rujuk_dinsos', 'display_name' => 'Merujuk Laporan ke Dinas Sosial', 'group' => 'Laporan PPA'],
            ['name' => 'laporan.view_dinsos', 'display_name' => 'Melihat Laporan Dinsos & Kasus Dirujuk', 'group' => 'Laporan Dinsos'],
            ['name' => 'laporan.process_dinsos', 'display_name' => 'Memproses Laporan Dinsos & Kasus Dirujuk', 'group' => 'Laporan Dinsos'],
            ['name' => 'dinsos.dashboard.view', 'display_name' => 'Melihat Dashboard Dinsos', 'group' => 'Dinsos PJSRK'],
            ['name' => 'dinsos.laporan.verify', 'display_name' => 'Memverifikasi Laporan Dinsos', 'group' => 'Dinsos PJSRK'],
            ['name' => 'dinsos.laporan.finish', 'display_name' => 'Menyelesaikan Laporan Dinsos', 'group' => 'Dinsos PJSRK'],
            ['name' => 'dinsos.laporan.reject', 'display_name' => 'Menolak Laporan Dinsos', 'group' => 'Dinsos PJSRK'],
        ];

        foreach ($permissions as $p) {
            Permission::updateOrCreate(
                ['name' => $p['name']],
                ['display_name' => $p['display_name'], 'group' => $p['group']]
            );
        }

        // 2. DAFTARKAN ROLE ADMIN PPA DAN ADMIN DINSOS
        $roleSuperadmin = Role::where('name', 'superadmin')->first();
        if ($roleSuperadmin) {
            $roleSuperadmin->permissions()->sync(Permission::pluck('id')->toArray());
        }

        $rolePPA = Role::updateOrCreate(
            ['name' => 'admin_ppa'],
            [
                'display_name' => 'Admin PPA',
                'description' => 'Petugas Unit Pelaksana Teknis Perlindungan Perempuan dan Anak.',
                'is_system' => true,
            ]
        );

        $roleDinsos = Role::updateOrCreate(
            ['name' => 'admin_dinsos'],
            [
                'display_name' => 'Admin Dinsos',
                'description' => 'Petugas Dinas Sosial untuk penanganan masalah sosial, penelantaran, dan rujukan.',
                'is_system' => true,
            ]
        );

        // Permissions untuk Admin PPA
        $ppaPerms = Permission::whereIn('name', [
            'laporan.view', 'laporan.process', 'laporan.rujuk', 'laporan.view_ppa', 'laporan.process_ppa', 'laporan.rujuk_dinsos',
            'chat.manage', 'informasi.manage', 'rekapan.export'
        ])->pluck('id')->toArray();
        $rolePPA->permissions()->sync($ppaPerms);

        // Permissions untuk Admin Dinsos
        $dinsosPerms = Permission::whereIn('name', [
            'laporan.view', 'laporan.process', 'laporan.view_dinsos', 'laporan.process_dinsos',
            'dinsos.dashboard.view', 'dinsos.laporan.verify', 'dinsos.laporan.finish', 'dinsos.laporan.reject',
            'chat.manage', 'informasi.manage', 'rekapan.export'
        ])->pluck('id')->toArray();
        $roleDinsos->permissions()->sync($dinsosPerms);

        // 3. KONFIGURASI 3 AKUN ADMIN
        // Akun 1: Superadmin
        $superadmin = Akun::where('email', 'superadmin@sentra.com')->first();
        if ($superadmin) {
            $superadmin->update([
                'nama' => 'Superadmin Sentra',
                'role' => 'superadmin',
                'is_active' => true,
            ]);
        } else {
            Akun::create([
                'nama' => 'Superadmin Sentra',
                'email' => 'superadmin@sentra.com',
                'password' => 'Superadmin123!',
                'notelp' => '081299998888',
                'role' => 'superadmin',
                'is_active' => true,
            ]);
        }

        // Akun 2: Admin PPA (menggunakan akun admin existing atau buat baru)
        $adminPPA = Akun::where('email', 'admin.ppa@sentra.com')
            ->orWhere('email', 'admin@sentra.com')
            ->first();

        if ($adminPPA) {
            $adminPPA->email = 'admin.ppa@sentra.com';
            $adminPPA->nama = 'Admin PPA (Sentra)';
            $adminPPA->role = 'admin_ppa';
            $adminPPA->password = 'AdminPPA123!';
            $adminPPA->is_active = true;
            $adminPPA->save();
        } else {
            Akun::create([
                'nama' => 'Admin PPA (Sentra)',
                'email' => 'admin.ppa@sentra.com',
                'password' => 'AdminPPA123!',
                'notelp' => '081234567890',
                'role' => 'admin_ppa',
                'is_active' => true,
            ]);
        }

        // Akun 3: Admin Dinsos
        $adminDinsos = Akun::where('email', 'admin.dinsos@sentra.com')->first();
        if ($adminDinsos) {
            $adminDinsos->update([
                'nama' => 'Admin Dinsos',
                'role' => 'admin_dinsos',
                'is_active' => true,
            ]);
        } else {
            Akun::create([
                'nama' => 'Admin Dinsos',
                'email' => 'admin.dinsos@sentra.com',
                'password' => 'Dinsos123!',
                'notelp' => '081234567899',
                'role' => 'admin_dinsos',
                'is_active' => true,
            ]);
        }
    }
}
