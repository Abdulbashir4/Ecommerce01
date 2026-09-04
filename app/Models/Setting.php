<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = Cache::rememberForever('setting:' . $key, function () use ($key) {
            return static::query()->where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true) ?? $default,
            default => $setting->value,
        };
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'string'): self
    {
        $stored = match ($type) {
            'boolean' => $value ? '1' : '0',
            'integer' => (string) (int) $value,
            'json' => json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            default => (string) $value,
        };

        $setting = static::updateOrCreate(
            ['key' => $key],
            ['value' => $stored, 'group' => $group, 'type' => $type]
        );

        Cache::forget('setting:' . $key);

        return $setting;
    }

    public static function forgetCache(): void
    {
        $settings = static::query()->pluck('key');
        foreach ($settings as $key) {
            Cache::forget('setting:' . $key);
        }
    }
}
