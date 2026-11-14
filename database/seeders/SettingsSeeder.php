<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            ['option_name' => 'app_name', 'option_value' => 'Cabang Dinas Kehutanan Wilayah Trenggalek'],
            ['option_name' => 'site_icon', 'option_value' => '/images/logo/icon.webp'],
            ['option_name' => 'site_favicon', 'option_value' => '/images/logo/icon.webp'],
            ['option_name' => 'default_pagination', 'option_value' => '10'],
            ['option_name' => 'google_analytics_script', 'option_value' => ''],
        ]);
    }
}
