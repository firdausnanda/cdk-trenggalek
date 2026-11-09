@php
    $onHome = request()->routeIs('home');
    $links = [
        ['label' => __('public.nav.home'), 'href' => $onHome ? '#top' : route('home'), 'active' => $onHome],
        ['label' => __('public.nav.about'), 'href' => route('tentang-kami'), 'active' => request()->routeIs('tentang-kami')],
        ['label' => __('public.nav.services'), 'href' => ($onHome ? '' : route('home')) . '#layanan', 'active' => false],
        ['label' => __('public.nav.news'), 'href' => route('berita'), 'active' => request()->routeIs('berita', 'berita.show')],
        ['label' => __('public.nav.contact'), 'href' => route('kontak'), 'active' => request()->routeIs('kontak')],
    ];
    // Language toggle (session based, see LocaleController). Each option is labelled in its own language.
    $bahasaAktif = app()->getLocale();
    $bahasa = [
        ['code' => 'id', 'short' => 'ID', 'name' => 'Bahasa Indonesia', 'label' => 'Indonesia', 'flag' => 'images/flags/language-id.svg'],
        ['code' => 'en', 'short' => 'EN', 'name' => 'English', 'label' => 'English', 'flag' => null, 'icon' => 'bi-globe2'],
    ];
@endphp

{{-- Pages with a dark full-bleed hero set @section('nav-overlay', true) to start with a transparent bar. --}}
<header x-data="{ overlay: @js(View::hasSection('nav-overlay')), scrolled: false, open: false }"
    x-init="scrolled = window.scrollY > 24" @scroll.window="scrolled = window.scrollY > 24"
    @keydown.escape.window="open = false" class="fixed inset-x-0 top-0 z-50 transition-colors duration-500"
    :class="(!overlay || scrolled || open) ? 'bg-cream/85 text-ink shadow-[0_1px_0_rgb(22_32_26/0.08)] backdrop-blur-xl' : 'text-white'">
    <nav class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-5 transition-[height] duration-500 sm:px-8"
        :class="scrolled ? 'h-16' : 'h-20'" aria-label="{{ __('public.nav.label') }}">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <img src="{{ config('settings.site_icon') }}" alt="" class="size-10 object-contain">
            <span class="leading-tight">
                <span class="block font-display text-[1.05rem] font-extrabold">{{ __('public.common.org_line1') }}</span>
                <span class="block text-xs tracking-wide opacity-75">{{ __('public.common.org_line2') }}</span>
            </span>
        </a>

        <ul class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $link)
                <li>
                    <a href="{{ $link['href'] }}" @if ($link['active']) aria-current="page" @endif
                        @class([
                            'relative rounded-full px-3 py-2 text-sm font-medium whitespace-nowrap transition hover:bg-current/10 xl:px-4',
                            'after:absolute after:inset-x-4 after:-bottom-0.5 after:h-0.5 after:rounded-full after:bg-leaf' => $link['active'],
                        ])>
                        {{ $link['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <div class="flex items-center gap-2">
            <ul class="hidden items-center gap-0.5 rounded-full p-1 text-xs font-bold ring-1 ring-current/20 sm:flex"
                aria-label="{{ __('public.nav.language') }}">
                @foreach ($bahasa as $item)
                    @php($aktif = $item['code'] === $bahasaAktif)
                    <li>
                        <a href="{{ route('locale.switch', $item['code']) }}" lang="{{ $item['code'] }}" hreflang="{{ $item['code'] }}"
                            title="{{ $item['name'] }}" rel="nofollow"
                            @if ($aktif) aria-current="true" @endif
                            @class([
                                'flex h-9 items-center gap-2 rounded-full pr-3 pl-2 tracking-wide transition',
                                'bg-leaf text-forest-950 shadow-sm' => $aktif,
                                'opacity-80 hover:bg-current/10 hover:opacity-100' => ! $aktif,
                            ])>
                            @if ($item['flag'])
                                <img src="{{ asset($item['flag']) }}" alt="" width="20" height="14"
                                    @class(['h-3.5 w-5 rounded-[3px] object-cover ring-1 ring-black/10', 'grayscale-[40%]' => ! $aktif])>
                            @else
                                <i aria-hidden="true" class="bi {{ $item['icon'] }} grid w-5 place-items-center text-[15px] leading-none"></i>
                            @endif
                            <span aria-hidden="true">{{ $item['short'] }}</span>
                            <span class="sr-only">{{ $item['name'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
            <button type="button" class="grid size-11 place-items-center rounded-full text-2xl transition hover:bg-current/10 lg:hidden"
                @click="open = !open" :aria-expanded="open" aria-controls="menu-mobile"
                :aria-label="open ? @js(__('public.nav.close_menu')) : @js(__('public.nav.open_menu'))">
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

        <div class="mt-6 border-t border-ink/10 px-4 pt-5">
            <p class="text-xs font-semibold tracking-[0.12em] text-ink/65 uppercase" id="bahasa-mobile">{{ __('public.nav.language') }}</p>
            <ul class="mt-3 grid grid-cols-2 gap-2" aria-labelledby="bahasa-mobile">
                @foreach ($bahasa as $item)
                    <li>
                        <a href="{{ route('locale.switch', $item['code']) }}" lang="{{ $item['code'] }}" hreflang="{{ $item['code'] }}" rel="nofollow"
                            @if ($item['code'] === $bahasaAktif) aria-current="true" @endif
                            @class([
                                'flex min-h-12 items-center justify-center gap-2 rounded-xl px-3 text-sm font-semibold transition',
                                'bg-forest-900 text-white' => $item['code'] === $bahasaAktif,
                                'ring-1 ring-forest-900/15 hover:bg-forest-900/5' => $item['code'] !== $bahasaAktif,
                            ])>
                            @if ($item['flag'])
                                <img src="{{ asset($item['flag']) }}" alt="" width="24" height="17"
                                    class="h-[17px] w-6 shrink-0 rounded-[3px] object-cover ring-1 ring-black/10">
                            @else
                                <i aria-hidden="true" class="bi {{ $item['icon'] }} grid w-6 shrink-0 place-items-center text-lg leading-none"></i>
                            @endif
                            <span aria-hidden="true" class="whitespace-nowrap">{{ $item['label'] }}</span>
                            <span class="sr-only">{{ $item['name'] }}</span>
                            @if ($item['code'] === $bahasaAktif)
                                <i aria-hidden="true" class="bi bi-check2 text-leaf"></i>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</header>
