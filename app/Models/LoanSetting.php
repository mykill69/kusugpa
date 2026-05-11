<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanSetting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    public static function get($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'decimal' => (float) $setting->value,
            default => $setting->value,
        };
    }

    public static function set($key, $value, $type = 'string')
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
    }
}