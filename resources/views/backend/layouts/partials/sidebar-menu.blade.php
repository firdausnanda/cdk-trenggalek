@php
    $menuService = app(\App\Services\MenuService\AdminMenuService::class);
    $menuGroups = $menuService->getMenu();
@endphp

<nav
    x-data="{
        openDrawer(drawerId) {
            if (typeof window.openDrawer === 'function') {
                window.openDrawer(drawerId);
            }
        }
    }"
    class="transition-all duration-300 ease-in-out"
>
    @foreach($menuGroups as $groupName => $groupItems)
        {!! ld_apply_filters('sidebar_menu_group_before_' . Str::slug($groupName), '') !!}
        <div>
            {!! ld_apply_filters('sidebar_menu_group_heading_before_' . Str::slug($groupName), '') !!}
            <h3 class="menu-group-title mb-2 px-6 text-[11px] font-semibold uppercase tracking-[0.14em] text-white/40"
                :class="sidebarToggle && !isHovered ? 'lg:invisible' : ''">
                {{ __($groupName) }}
            </h3>
            {!! ld_apply_filters('sidebar_menu_group_heading_after_' . Str::slug($groupName), '') !!}
            <ul class="mb-6 flex flex-col gap-0.5">
                {!! ld_apply_filters('sidebar_menu_before_all_' . Str::slug($groupName), '') !!}
                {!! $menuService->render($groupItems) !!}
                {!! ld_apply_filters('sidebar_menu_after_all_' . Str::slug($groupName), '') !!}
            </ul>
        </div>
        {!! ld_apply_filters('sidebar_menu_group_after_' . Str::slug($groupName), '') !!}
    @endforeach
</nav>

<script>
    // Ensure drawer triggers work in the sidebar
    document.addEventListener('DOMContentLoaded', function() {
        // Handle drawer trigger clicks
        document.querySelectorAll('[data-drawer-trigger]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                const drawerId = this.getAttribute('data-drawer-trigger');
                if (drawerId) {
                    e.preventDefault();
                    if (typeof window.openDrawer === 'function') {
                        window.openDrawer(drawerId);
                    } else {
                        // Fallback if the global function isn't available yet
                        window.dispatchEvent(new CustomEvent('open-drawer-' + drawerId));
                    }
                    return false;
                }
            });
        });
    });
</script>
