<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    protected $casts = [
        'value' => 'string',
    ];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            'date' => $setting->value,
            default => $setting->value,
        };
    }

    public static function set($key, $value, $type = 'string')
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
    }

    public static function isSystemAccessible()
    {
        if (self::get('system_locked', false)) return false;
        if (self::get('subscription_status') === 'inactive') return false;

        $lockStart = self::get('lock_start_date');
        $lockEnd = self::get('lock_end_date');
        
        if ($lockStart && $lockEnd) {
            $now = now();
            $start = \Carbon\Carbon::parse($lockStart);
            $end = \Carbon\Carbon::parse($lockEnd);
            if ($now->between($start, $end)) return false;
        }

        if (self::get('maintenance_mode', false)) return false;

        return true;
    }

    public static function getAllSettings()
    {
        $settings = self::all()->pluck('value', 'key')->toArray();
        
        foreach (self::all() as $setting) {
            $settings[$setting->key] = match ($setting->type) {
                'boolean' => (bool) $setting->value,
                'integer' => (int) $setting->value,
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        }
        
        return $settings;
    }
}