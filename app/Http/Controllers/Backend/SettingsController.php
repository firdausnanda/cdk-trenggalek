<?php

declare(strict_types=1);

namespace App\Http\Controllers\Backend;

use App\Enums\ActionType;
use App\Http\Controllers\Controller;
use App\Services\CacheService;
use App\Services\EnvWriter;
use App\Services\SettingService;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingService $settingService,
        private readonly EnvWriter $envWriter,
        private readonly CacheService $cacheService
    ) {
        // The cacheService is used in the EnvWriter for cache clearing operations
    }

    public function index($tab = null): Renderable
    {
        $this->checkAuthorization(Auth::user(), ['settings.edit']);

        $tab = $tab ?? request()->input('tab', 'general');

        return view('backend.pages.settings.index', compact('tab'))
            ->with([
                'breadcrumbs' => [
                    'title' => __('Settings'),
                ],
            ]);
    }

    /**
     * Setting teks yang boleh diubah dari halaman Pengaturan.
     */
    private const TEXT_FIELDS = ['app_name', 'default_pagination', 'google_analytics_script'];

    /**
     * Setting gambar yang boleh diunggah dari halaman Pengaturan.
     */
    private const IMAGE_FIELDS = ['site_icon', 'site_favicon'];

    private const UPLOAD_PATH = 'uploads/settings';

    public function store(Request $request)
    {
        $this->checkAuthorization(Auth::user(), ['settings.edit']);

        $imageRule = 'nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:2048';

        $validated = $request->validate([
            'app_name' => 'required|string|max:100',
            'default_pagination' => 'required|integer|min:1|max:100',
            'google_analytics_script' => 'nullable|string|max:10000',
            'site_icon' => $imageRule,
            'site_favicon' => $imageRule,
        ], [
            '*.image' => ':attribute harus berupa file gambar.',
            '*.mimes' => ':attribute harus berformat: jpg, jpeg, png, gif, webp, atau svg.',
            '*.max' => 'Ukuran :attribute maksimal 2MB.',
        ], [
            'app_name' => 'Site Name',
            'default_pagination' => 'Default Pagination',
            'google_analytics_script' => 'Google Analytics',
            'site_icon' => 'Site Icon',
            'site_favicon' => 'Site Favicon',
        ]);

        $fields = [];

        foreach (self::TEXT_FIELDS as $fieldName) {
            $fields[$fieldName] = $validated[$fieldName] ?? '';
            $this->settingService->addSetting($fieldName, $fields[$fieldName]);
        }

        foreach (self::IMAGE_FIELDS as $fieldName) {
            if (! $request->hasFile($fieldName)) {
                continue;
            }

            $this->deleteOldUpload((string) config('settings.'.$fieldName));
            $fields[$fieldName] = storeImageAndGetUrl($request, $fieldName, self::UPLOAD_PATH);
            $this->settingService->addSetting($fieldName, $fields[$fieldName]);
        }

        $this->envWriter->batchWriteKeysToEnvFile($fields);

        $this->storeActionLog(ActionType::UPDATED, [
            'settings' => $fields,
        ]);

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

    /**
     * Hapus file lama hanya jika berasal dari folder upload pengaturan,
     * supaya aset bawaan (mis. /images/logo/...) tidak ikut terhapus.
     */
    private function deleteOldUpload(string $url): void
    {
        $path = ltrim((string) parse_url($url, PHP_URL_PATH), '/');

        if (str_starts_with($path, self::UPLOAD_PATH.'/')) {
            deleteImageFromPublic($url);
        }
    }
}
