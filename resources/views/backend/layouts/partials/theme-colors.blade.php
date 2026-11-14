@php
use App\Services\ThemeColorService;

// The admin follows the CDK forest palette. The old LaraDashboard defaults (purple / slate)
// are treated as "not customised", so a colour picked in Settings still wins.
$primaryColor = config('settings.theme_primary_color') ?: '#1f5f3d';
$secondaryColor = config('settings.theme_secondary_color') ?: '#0b2118';
if (strtolower($primaryColor) === '#635bff') {
    $primaryColor = '#1f5f3d';
}
if (strtolower($secondaryColor) === '#1f2937') {
    $secondaryColor = '#0b2118';
}

$primaryPalette = ThemeColorService::generateColorPalette($primaryColor);
@endphp

<style>
    :root {
        /* Base colors */
        --color-primary: {{ $primaryColor }};
        --color-secondary: {{ $secondaryColor }};
        
        /* Brand color palette */
        --color-brand-50: {{ $primaryPalette[50] }};
        --color-brand-100: {{ $primaryPalette[100] }};
        --color-brand-200: {{ $primaryPalette[200] }};
        --color-brand-300: {{ $primaryPalette[300] }};
        --color-brand-400: {{ $primaryPalette[400] }};
        --color-brand-500: {{ $primaryColor }};
        --color-brand-600: {{ $primaryPalette[600] }};
        --color-brand-700: {{ $primaryPalette[700] }};
        --color-brand-800: {{ $primaryPalette[800] }};
        --color-brand-900: {{ $primaryPalette[900] }};
    }
</style>
