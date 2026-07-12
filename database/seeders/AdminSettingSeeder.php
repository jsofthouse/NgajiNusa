<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use Illuminate\Database\Seeder;

class AdminSettingSeeder extends Seeder
{
    public function run(): void
    {
        // Ganti value di bawah dengan nomor WA admin asli (format 62xxxxxxxxxx, tanpa + atau 0 di depan)
        AdminSetting::updateOrCreate(
            ['key' => 'wa_admin_number'],
            ['value' => '628229274992']
        );
    }
}
