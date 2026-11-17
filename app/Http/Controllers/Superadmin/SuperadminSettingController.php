<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Informasi;
use App\Models\SystemSetting;
use App\Support\SettingSchema;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class SuperadminSettingController extends Controller
{
    /** Folder penyimpanan unggahan konfigurasi (relatif terhadap public/). */
    private const UPLOAD_DIR = 'uploads/settings';

    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');

        // Berita halaman utama ikut dikelola dari halaman ini agar seluruh
        // konten publik terpusat di Superadmin.
        $informasiList = Informasi::latest()->get();

        return view('backend.superadmin.setting.index', compact('settings', 'informasiList'));
    }

    public function update(Request $request)
    {
        // Validasi seluruh input (termasuk upload) sebelum menyimpan apa pun,
        // sehingga saat terjadi error data lama tidak berubah sebagian.
        $validated = $request->validate($this->rules());

        $this->saveTextSettings($validated);
        $this->saveJsonSettings($request);
        $this->saveScreenshots($request);
        $this->saveFileSettings($request);

        ActivityLog::record(
            'data',
            'Memperbarui konfigurasi sistem (' . ($validated['app_name'] ?? 'SENTRA') . ').',
            'setting'
        );

        return back()->with('success', 'Konfigurasi sistem berhasil diperbarui.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(): array
    {
        $rules = SettingSchema::rules();

        $rules['mobile_screenshots'] = ['nullable', 'array'];
        $rules['mobile_screenshots.*.src'] = ['nullable', 'string', 'max:500'];
        $rules['mobile_screenshots.*.alt'] = ['nullable', 'string', 'max:200'];
        $rules['mobile_screenshots_upload'] = ['nullable', 'array'];
        $rules['mobile_screenshots_upload.*'] = ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,webp', 'max:4096'];

        $rules['mobile_features.*.icon'] = ['nullable', 'string', 'max:100'];
        $rules['mobile_features.*.title'] = ['nullable', 'string', 'max:150'];
        $rules['mobile_features.*.desc'] = ['nullable', 'string', 'max:300'];

        $rules['faq_items.*.question'] = ['nullable', 'string', 'max:255'];
        $rules['faq_items.*.answer'] = ['nullable', 'string', 'max:1000'];

        $rules['footer_links.*.label'] = ['nullable', 'string', 'max:100'];
        $rules['footer_links.*.href'] = ['nullable', 'string', 'max:300'];
        $rules['footer_services.*.label'] = ['nullable', 'string', 'max:150'];
        $rules['footer_services.*.href'] = ['nullable', 'string', 'max:300'];

        return $rules;
    }

    /**
     * Simpan seluruh field teks/angka (kecuali file dan JSON).
     */
    private function saveTextSettings(array $validated): void
    {
        $values = [];

        foreach (SettingSchema::fields() as $key => $field) {
            if (in_array($field['type'] ?? 'text', ['file', 'json'], true)) {
                continue;
            }

            if (array_key_exists($key, $validated)) {
                $values[$key] = is_null($validated[$key]) ? '' : (string) $validated[$key];
            }
        }

        SystemSetting::setMany($values);
    }

    /**
     * Simpan field JSON (fitur aplikasi, FAQ, tautan footer).
     */
    private function saveJsonSettings(Request $request): void
    {
        $allowedColumns = [
            'mobile_features' => ['icon', 'title', 'desc'],
            'faq_items'       => ['question', 'answer'],
            'footer_links'    => ['label', 'href'],
            'footer_services' => ['label', 'href'],
        ];

        foreach ($allowedColumns as $key => $columns) {
            // Hidden marker menandakan repeater benar-benar dikirim, sehingga
            // penghapusan seluruh baris juga tersimpan (bukan dianggap kosong).
            if (! $request->boolean($key . '_present')) {
                continue;
            }

            $rows = $request->input($key, []);
            $clean = [];

            foreach ((array) $rows as $row) {
                if (! is_array($row)) {
                    continue;
                }

                $item = [];

                foreach ($columns as $column) {
                    $value = trim((string) ($row[$column] ?? ''));
                    if ($value !== '') {
                        $item[$column] = $value;
                    }
                }

                if ($item !== []) {
                    $clean[] = $item;
                }
            }

            SystemSetting::set($key, json_encode($clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }
    }

    /**
     * Simpan tangkapan layar aplikasi (dihapus, diubah alt-nya, atau ditambah).
     */
    private function saveScreenshots(Request $request): void
    {
        if (! $request->boolean('mobile_screenshots_present')) {
            return;
        }

        $rows = (array) $request->input('mobile_screenshots', []);
        $removed = array_filter((array) $request->input('mobile_screenshots_remove', []));
        $result = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $src = trim((string) ($row['src'] ?? ''));

            if ($src === '') {
                continue;
            }

            if (in_array($src, $removed, true)) {
                $this->deleteUpload($src);
                continue;
            }

            $result[] = [
                'src' => $src,
                'alt' => trim((string) ($row['alt'] ?? '')),
            ];
        }

        foreach ((array) $request->file('mobile_screenshots_upload', []) as $file) {
            if ($file instanceof UploadedFile) {
                $result[] = [
                    'src' => $this->storeUpload($file, 'screenshot'),
                    'alt' => '',
                ];
            }
        }

        SystemSetting::set(
            'mobile_screenshots',
            json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Simpan unggahan tunggal (logo, background hero, gambar tentang kami).
     */
    private function saveFileSettings(Request $request): void
    {
        foreach (SettingSchema::fileKeys() as $key) {
            $stored = SystemSetting::get($key);

            // Hapus gambar unggahan dan kembali ke aset bawaan.
            if ($request->boolean('remove_upload.' . $key)) {
                $this->deleteUpload($stored);
                SystemSetting::set($key, '');
                continue;
            }

            if (! $request->hasFile($key)) {
                continue;
            }

            $file = $request->file($key);

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $this->deleteUpload($stored);
            SystemSetting::set($key, $this->storeUpload($file, $key));

            if ($key === 'app_logo') {
                ActivityLog::record('dokumen', 'Mengunggah logo aplikasi baru.', 'setting', 'app_logo');
            }
        }
    }

    private function storeUpload(UploadedFile $file, string $prefix): string
    {
        $directory = public_path(self::UPLOAD_DIR);

        if (! File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $fileName = $prefix . '_' . time() . '_' . Str::lower(Str::random(6))
            . '.' . $file->getClientOriginalExtension();

        $file->move($directory, $fileName);

        return self::UPLOAD_DIR . '/' . $fileName;
    }

    private function deleteUpload(?string $path): void
    {
        if (! $path || ! Str::startsWith($path, self::UPLOAD_DIR . '/')) {
            return;
        }

        if (File::exists(public_path($path))) {
            File::delete(public_path($path));
        }
    }
}
