@php echo ld_apply_filters('settings_general_tab_before_section_start', '') @endphp
<div class="rounded-2xl border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="px-5 py-4 sm:px-6 sm:py-5">
        <h3 class="text-base font-medium text-gray-800 dark:text-white/90">
            {{ __('General Settings') }}
        </h3>
    </div>
    <div class="space-y-6 border-t border-gray-100 p-5 sm:p-6 dark:border-gray-800">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="relative">
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ __('Site Name') }}
                </label>
                <input type="text" name="app_name" placeholder="{{ __('Enter site name') }}"
                    value="{{ config('settings.app_name') ?? '' }}"
                    class="form-control">
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    {{ __('Default Pagination') }}
                </label>
                <input type="number" name="default_pagination" min="1" max="100"
                    value="{{ config('settings.default_pagination') ?? 10 }}"
                    class="form-control">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <div class="mt-2">
                    <x-inputs.file-input 
                        name="site_icon" 
                        id="site_icon"
                        label="{{ __('Site Icon') }}"
                        accept="image/*"
                        :existingAttachment="config('settings.site_icon') !== '' && !empty(config('settings.site_icon')) ? config('settings.site_icon') : null"
                        :existingAltText="''"
                    />
                </div>
            </div>
            <div>
                <div class="mt-2">
                    <x-inputs.file-input 
                        name="site_favicon" 
                        id="site_favicon"
                        label="{{ __('Site Favicon') }}"
                        accept="image/*"
                        :existingAttachment="config('settings.site_favicon') !== '' && !empty(config('settings.site_favicon')) ? config('settings.site_favicon') : null"
                        :existingAltText="''"
                    />
                </div>
            </div>
        </div>
    </div>
    @php echo ld_apply_filters('settings_general_tab_before_section_end', '') @endphp
</div>

