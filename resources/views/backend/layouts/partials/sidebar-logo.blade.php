<aside
    :class="sidebarToggle ? 'translate-x-0 lg:w-[88px] app-sidebar-minified' : '-translate-x-full'"
    class="sidebar fixed left-0 top-0 z-10 flex h-screen w-[280px] flex-col overflow-y-hidden bg-forest-950 text-white transition-all duration-300 ease-in-out lg:static lg:translate-x-0"
    id="appSidebar"
    aria-label="{{ __('Admin navigation') }}"
    x-data="{
        isHovered: false,
        init() {
            if (localStorage.getItem('sidebarToggle')) {
                sidebarToggle = JSON.parse(localStorage.getItem('sidebarToggle'));
            }
        }
    }"
    x-init="init()"
    @mouseenter="if(sidebarToggle) { isHovered = true; $el.classList.add('lg:w-[280px]'); $el.classList.remove('lg:w-[88px]', 'app-sidebar-minified'); }"
    @mouseleave="if(sidebarToggle) { isHovered = false; $el.classList.add('lg:w-[88px]', 'app-sidebar-minified'); $el.classList.remove('lg:w-[280px]'); }"
>
    {{-- Soft canopy glow behind the brand --}}
    <div aria-hidden="true" class="pointer-events-none absolute -top-24 -left-16 size-64 rounded-full bg-forest-600/30 blur-3xl"></div>

    <!-- Sidebar Header -->
    <div
        :class="sidebarToggle && !isHovered ? 'lg:justify-center lg:px-0' : ''"
        class="sidebar-header relative flex h-[76px] shrink-0 items-center gap-3 px-6 transition-all duration-300"
    >
        <a href="{{ route('admin.dashboard') }}" class="flex min-w-0 items-center gap-3">
            <span class="flex size-10 shrink-0 items-center justify-center">
                <img
                    class="size-9 object-contain"
                    src="{{ config('settings.site_icon') ?: asset('images/logo/icon.png') }}"
                    alt=""
                />
            </span>
            <span class="logo min-w-0 leading-tight transition-opacity duration-300"
                :class="sidebarToggle && !isHovered ? 'lg:hidden' : ''">
                <span class="block truncate font-display text-[15px] font-semibold">Cabang Dinas Kehutanan</span>
                <span class="block truncate text-xs text-white/60">Wilayah Trenggalek · Admin</span>
            </span>
        </a>
    </div>
    <!-- End Sidebar Header -->

    <div class="mx-6 h-px shrink-0 bg-white/10" :class="sidebarToggle && !isHovered ? 'lg:mx-4' : ''"></div>

    <div class="relative flex flex-1 flex-col overflow-x-hidden overflow-y-auto py-5 no-scrollbar">
        @include('backend.layouts.partials.sidebar-menu')
    </div>

    <!-- Sidebar Footer -->
    <div class="relative shrink-0 border-t border-white/10 p-4">
        <a href="{{ route('home') }}" target="_blank" rel="noopener"
            class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/70 transition-colors hover:bg-white/5 hover:text-white"
            :class="sidebarToggle && !isHovered ? 'lg:justify-center' : ''">
            <i class="bi bi-box-arrow-up-right w-5 text-center" aria-hidden="true"></i>
            <span class="menu-item-text" :class="sidebarToggle && !isHovered ? 'lg:hidden' : ''">{{ __('View website') }}</span>
        </a>
    </div>
</aside>
<!-- End Sidebar -->
