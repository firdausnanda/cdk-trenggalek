<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Hapus baris setting sisa template LaraDashboard yang tidak lagi
 * punya form di halaman Pengaturan dan tidak dibaca di mana pun.
 */
return new class extends Migration
{
    /**
     * Nilai bawaan dipakai untuk mengembalikan baris saat rollback.
     *
     * @var array<string, string>
     */
    private array $defaults = [
        'theme_primary_color' => '#635bff',
        'theme_secondary_color' => '#1f2937',
        'default_mode' => 'lite',
        'sidebar_bg_lite' => '#FFFFFF',
        'sidebar_bg_dark' => '#171f2e',
        'sidebar_li_hover_lite' => '#E8E6FF',
        'sidebar_li_hover_dark' => '#E8E6FF',
        'sidebar_text_lite' => '#090909',
        'sidebar_text_dark' => '#ffffff',
        'navbar_bg_lite' => '#FFFFFF',
        'navbar_bg_dark' => '#171f2e',
        'navbar_text_lite' => '#090909',
        'navbar_text_dark' => '#ffffff',
        'text_color_lite' => '#212529',
        'text_color_dark' => '#f8f9fa',
        'site_logo_lite' => '/images/logo/lara-dashboard.png',
        'site_logo_dark' => '/images/logo/lara-dashboard-dark.png',
        'google_tag_manager_script' => '',
        'global_custom_css' => '',
        'global_custom_js' => '',
    ];

    public function up(): void
    {
        DB::table('settings')
            ->whereIn('option_name', array_keys($this->defaults))
            ->orWhere('option_name', 'like', '%\_text')
            ->delete();
    }

    public function down(): void
    {
        foreach ($this->defaults as $name => $value) {
            DB::table('settings')->updateOrInsert(
                ['option_name' => $name],
                ['option_value' => $value]
            );
        }
    }
};
