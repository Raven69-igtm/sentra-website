<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
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
        $inputSettings = $request->except(['_token', 'app_logo']);

        // Update key-value standar
        foreach ($inputSettings as $key => $value) {
            SystemSetting::set($key, is_null($value) ? '' : (string)$value);
        }

        // Handle upload logo
        if ($request->hasFile('app_logo')) {
            $request->validate([
                'app_logo' => 'image|mimes:jpeg,png,jpg,svg|max:2048',
            ]);

            $file = $request->file('app_logo');
            $uploadPath = public_path('uploads/settings');

            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $fileName = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);

            SystemSetting::set('app_logo', 'uploads/settings/' . $fileName);
        }

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }
}
