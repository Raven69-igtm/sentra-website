<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    /**
     * Cache tunggal berisi seluruh key => value.
     * Landing page memakai puluhan setting, sehingga satu cache map jauh
     * lebih murah daripada satu cache entry per key.
     */
    protected const CACHE_KEY = 'system_settings.map';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    public static function allCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            return static::query()->pluck('value', 'key')->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $map = static::allCached();

        return array_key_exists($key, $map) ? $map[$key] : $default;
    }

    public static function set(string $key, mixed $value, ?string $label = null, string $group = 'general'): void
    {
        static::writeValue($key, $value, $label, $group);

        static::flushCache();
    }

    /**
     * Simpan banyak nilai sekaligus dan invalidasi cache satu kali.
     *
     * @param  array<string, mixed>  $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::writeValue($key, $value);
        }

        static::flushCache();
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function writeValue(string $key, mixed $value, ?string $label = null, string $group = 'general'): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;

        if (! $setting->exists) {
            $setting->label = $label ?? ucfirst(str_replace('_', ' ', $key));
            $setting->group = $group;
            $setting->type = 'text';
        }

        $setting->save();
    }
}
