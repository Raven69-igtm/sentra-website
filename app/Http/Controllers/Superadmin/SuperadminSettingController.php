<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SuperadminSettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');

        return view('backend.superadmin.setting.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Validasi seluruh input (termasuk logo) sebelum menyimpan apa pun,
        // sehingga saat terjadi error data lama tidak berubah sebagian.
        $validated = $request->validate([
            'app_name'                 => ['required', 'string', 'max:100'],
            'instansi_name'            => ['required', 'string', 'max:150'],
            'app_description'          => ['nullable', 'string', 'max:500'],
            'footer_copyright'         => ['nullable', 'string', 'max:150'],
            'hotline_number'           => ['required', 'string', 'max:30'],
            'emergency_call'           => ['nullable', 'string', 'max:30'],
            'office_email'             => ['nullable', 'email', 'max:100'],
            'office_address'           => ['nullable', 'string', 'max:255'],
            'report_sla_days'          => ['required', 'integer', 'min:1', 'max:30'],
            'auto_archive_months'      => ['nullable', 'integer', 'min:1', 'max:120'],
            'enable_chat_consultation' => ['required', 'in:0,1'],
            'session_lifetime_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'max_login_attempts'       => ['required', 'integer', 'min:3', 'max:20'],
            'otp_expiry_minutes'       => ['required', 'integer', 'min:1', 'max:60'],
            'app_logo'                 => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ]);

        // Update key-value standar (hanya key yang dikenali sistem)
        foreach ($validated as $key => $value) {
            if ($key === 'app_logo') {
                continue;
            }
            SystemSetting::set($key, is_null($value) ? '' : (string) $value);
        }

        // Handle upload logo
        if ($request->hasFile('app_logo')) {
            $file = $request->file('app_logo');
            $uploadPath = public_path('uploads/settings');

            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Hapus logo lama jika ada
            $oldLogo = SystemSetting::get('app_logo');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }

            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            SystemSetting::set('app_logo', 'uploads/settings/' . $fileName);

            ActivityLog::record('dokumen', "Mengunggah logo aplikasi baru ({$fileName}).", 'setting', 'app_logo');
        }

        ActivityLog::record('data', "Memperbarui konfigurasi sistem ({$validated['app_name']}).", 'setting');

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
