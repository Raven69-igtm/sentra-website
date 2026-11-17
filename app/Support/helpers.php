<?php

use App\Models\SystemSetting;
use App\Support\SettingSchema;

if (! function_exists('setting')) {
    /**
     * Ambil nilai konfigurasi. Bila belum pernah diisi (null/kosong),
     * gunakan default dari SettingSchema agar tampilan tidak pernah rusak.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        $value = SystemSetting::get($key);

        if ($value === null || $value === '') {
            $value = $default ?? SettingSchema::default($key);
        }

        return $value;
    }
}

if (! function_exists('setting_bool')) {
    function setting_bool(string $key, bool $default = false): bool
    {
        $value = setting($key, $default ? '1' : '0');

        return in_array((string) $value, ['1', 'true', 'on', 'yes'], true);
    }
}

if (! function_exists('setting_int')) {
    function setting_int(string $key, int $default = 0): int
    {
        $value = setting($key, (string) $default);

        return is_numeric($value) ? (int) $value : $default;
    }
}

if (! function_exists('setting_json')) {
    /**
     * Ambil nilai JSON sebagai array. Jika kosong/rusak, pakai default schema.
     */
    function setting_json(string $key): array
    {
        $raw = setting($key);

        if (is_array($raw)) {
            return $raw;
        }

        $decoded = is_string($raw) ? json_decode($raw, true) : null;

        // Daftar kosong yang sengaja disimpan (mis. semua FAQ dihapus)
        // harus tetap dihormati, bukan dikembalikan ke default.
        if (is_array($decoded) && json_last_error() === JSON_ERROR_NONE) {
            return $decoded;
        }

        $fallback = SettingSchema::default($key, []);

        return is_array($fallback) ? $fallback : [];
    }
}

if (! function_exists('setting_image')) {
    /**
     * URL aset untuk setting bertipe gambar.
     * Mendukung path unggahan (uploads/...) maupun aset bawaan (landingpage/...).
     */
    function setting_image(string $key): string
    {
        $path = setting($key);

        if (! $path) {
            return '';
        }

        return asset($path);
    }
}
