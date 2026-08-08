<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Identitas
            ['key' => 'app_name',          'value' => 'SENTRA',                                                                          'group' => 'general',  'type' => 'text',     'label' => 'Nama Aplikasi'],
            ['key' => 'instansi_name',     'value' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',                              'group' => 'general',  'type' => 'text',     'label' => 'Nama Instansi'],
            ['key' => 'app_description',   'value' => 'Sistem Informasi dan Pelayanan Terpadu Penanganan Kekerasan terhadap Perempuan dan Anak', 'group' => 'general', 'type' => 'textarea', 'label' => 'Deskripsi Layanan'],
            ['key' => 'footer_copyright',  'value' => '© 2026 SENTRA. All rights reserved.',                                            'group' => 'general',  'type' => 'text',     'label' => 'Hak Cipta Footer'],

            // Kontak
            ['key' => 'hotline_number',    'value' => '0812-3456-7890', 'group' => 'contact',  'type' => 'text',   'label' => 'Nomor Hotline'],
            ['key' => 'emergency_call',    'value' => '112 / 129',     'group' => 'contact',  'type' => 'text',   'label' => 'Panggilan Darurat'],
            ['key' => 'office_email',      'value' => 'sentraapplication@gmail.com', 'group' => 'contact', 'type' => 'text', 'label' => 'Email Layanan'],
            ['key' => 'office_address',    'value' => 'Jl. Pemuda No. 45, Kompleks Perkantoran Pemerintah', 'group' => 'contact', 'type' => 'text', 'label' => 'Alamat Kantor'],

            // Laporan & SLA
            ['key' => 'report_sla_days',           'value' => '3',   'group' => 'report', 'type' => 'number',  'label' => 'Target SLA Respon (Hari)'],
            ['key' => 'auto_archive_months',       'value' => '24',  'group' => 'report', 'type' => 'number',  'label' => 'Periode Retensi Arsip (Bulan)'],
            ['key' => 'enable_chat_consultation',  'value' => '1',   'group' => 'report', 'type' => 'boolean', 'label' => 'Layanan Konsultasi Chat'],

            // Keamanan
            ['key' => 'session_lifetime_minutes', 'value' => '120', 'group' => 'security', 'type' => 'number', 'label' => 'Durasi Timeout Sesi (Menit)'],
            ['key' => 'max_login_attempts',       'value' => '5',   'group' => 'security', 'type' => 'number', 'label' => 'Maksimal Percobaan Login'],
            ['key' => 'otp_expiry_minutes',       'value' => '5',   'group' => 'security', 'type' => 'number', 'label' => 'Masa Berlaku OTP (Menit)'],
        ];

        foreach ($settings as $s) {
            SystemSetting::firstOrCreate(
                ['key' => $s['key']],
                $s
            );
        }
    }
}
