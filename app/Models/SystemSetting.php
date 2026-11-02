<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
        'description',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("system_setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, mixed $value, ?string $label = null, string $group = 'general'): void
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        if (!$setting->exists) {
            $setting->label = $label ?? ucfirst(str_replace('_', ' ', $key));
            $setting->group = $group;
            $setting->type = 'text';
        }
        $setting->save();
        Cache::forget("system_setting_{$key}");
    }
}
