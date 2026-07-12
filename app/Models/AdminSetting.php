<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $table = 'admin_settings';

    protected $fillable = ['key', 'value'];

    /**
     * Helper ambil value setting by key, dengan fallback default.
     * Contoh: AdminSetting::get('wa_admin_number', '6280000000000')
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }
}
