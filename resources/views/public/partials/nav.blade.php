@php
    $onHome = request()->routeIs('home');
    $links = [
        ['label' => 'Beranda', 'href' => $onHome ? '#top' : route('home'), 'active' => $onHome],
        ['label' => 'Tentang Kami', 'href' => route('tentang-kami'), 'active' => request()->routeIs('tentang-kami')],
        ['label' => 'Layanan', 'href' => ($onHome ? '' : route('home')) . '#layanan', 'active' => false],
        ['label' => 'Berita', 'href' => route('berita'), 'active' => request()->routeIs('berita', 'berita.show')],
        ['label' => 'Kontak', 'href' => route('kontak'), 'active' => request()->routeIs('kontak')],
    ];
@endphp

{{-- Pages with a dark full-bleed hero set @section('nav-overlay', true) to start with a transparent bar. --}}
<header x-data="{ overlay: @js(View::hasSection('nav-overlay')), scrolled: false, open: false }"
    x-init="scrolled = window.scrollY > 24" @scroll.window="scrolled = window.scrollY > 24"
    @keydown.escape.window="open = false" class="fixed inset-x-0 top-0 z-50 transition-colors duration-500"
    :class="(!overlay || scrolled || open) ? 'bg-cream/85 text-ink shadow-[0_1px_0_rgb(22_32_26/0.08)] backdrop-blur-xl' : 'text-white'">
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 transition-[height] duration-500 sm:px-8"
        :class="scrolled ? 'h-16' : 'h-20'" aria-label="Navigasi utama">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ config('settings.site_icon') }}" alt="" class="size-10 object-contain">
            <span class="leading-tight">
                <span class="block font-display text-[1.05rem] font-extrabold">Cabang Dinas Kehutanan</span>
                <span class="block text-xs tracking-wide opacity-75">Wilayah Trenggalek</span>
            </span>
        </a>

        <ul class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $link)
                <li>
                    <a href="{{ $link['href'] }}" @if ($link['active']) aria-current="page" @endif
                        @class([
                            'relative rounded-full px-4 py-2 text-sm font-medium transition hover:bg-current/10',
                            'after:absolute after:inset-x-4 after:-bottom-0.5 after:h-0.5 after:rounded-full after:bg-leaf' => $link['active'],
                        ])>
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2">
            <a href="{{ route('kontak') }}" class="btn-primary hidden !px-5 !py-2.5 text-sm sm:inline-flex">
                Hubungi kami <i aria-hidden="true" class="bi bi-arrow-up-right"></i>
            </a>
            <button type="button" class="grid size-11 place-items-center rounded-full text-2xl transition hover:bg-current/10 lg:hidden"
                @click="open = !open" :aria-expanded="open" aria-controls="menu-mobile" :aria-label="open ? 'Tutup menu' : 'Buka menu'">
                <i aria-hidden="true" class="bi" :class="open ? 'bi-x-lg' : 'bi-list'"></i>
            </button>
        </div>
    </nav>

    <div id="menu-mobile" x-cloak x-show="open" x-transition.opacity.duration.300ms
        class="border-t border-ink/10 px-5 pt-4 pb-8 lg:hidden">
        <ul class="space-y-1">
            @foreach ($links as $link)
                <li>
                    <a href="{{ $link['href'] }}" @click="open = false"
                        @class([
                            'flex items-center justify-between rounded-2xl px-4 py-3 font-display text-2xl transition hover:bg-forest-900/5',
                            'text-forest-600' => $link['active'],
                        ])>
                        {{ $link['label'] }} <i aria-hidden="true" class="bi bi-arrow-right text-base opacity-50"></i>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</header>
