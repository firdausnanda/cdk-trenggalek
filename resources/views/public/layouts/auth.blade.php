<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', config('settings.app_name'))</title>
    <meta name="theme-color" content="#0b2118">
    <link rel="icon" href="{{ config('settings.site_icon') }}">

    @include('public.partials.head-script')
    @vite(['resources/css/public.css', 'resources/js/public.js'])
    @include('public.partials.analytics')
</head>

<body class="bg-cream font-sans text-ink antialiased">
    <a href="#konten"
        class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-full focus:bg-leaf focus:px-4 focus:py-2 focus:text-forest-950">
        Langsung ke konten
    </a>

    <div class="grid min-h-[100svh] lg:grid-cols-2">
        {{-- Brand panel --}}
        <aside class="relative isolate hidden overflow-hidden bg-forest-950 text-white lg:flex lg:flex-col" aria-label="Tentang panel admin">
            <img src="{{ asset('images/background/bg.avif') }}" alt="" class="absolute inset-0 -z-20 size-full object-cover">
            <div class="absolute inset-0 -z-10 bg-gradient-to-br from-forest-950/95 via-forest-950/80 to-forest-900/60"></div>

            <a href="{{ route('home') }}" class="flex items-center gap-3 px-12 pt-10">
                <img src="{{ config('settings.site_icon') }}" alt="" class="size-11 object-contain">
                <span class="leading-tight">
                    <span class="block font-display text-lg font-semibold">Cabang Dinas Kehutanan</span>
                    <span class="block text-sm text-white/75">Wilayah Trenggalek</span>
                </span>
            </a>

            <div class="mt-auto px-12 pb-24">
                <p class="text-sm font-semibold tracking-[0.18em] text-leaf uppercase">Panel admin</p>
                <p class="mt-4 max-w-md font-display text-4xl leading-tight font-bold tracking-[-0.03em] text-balance">
                    Kelola informasi kehutanan untuk masyarakat.
                </p>
                <p class="mt-4 max-w-md text-white/80">
                    Berita, halaman, dan pengaturan situs Cabang Dinas Kehutanan Wilayah Trenggalek.
                </p>
            </div>
        </aside>

        <main id="konten" class="flex flex-col px-5 pt-8 pb-20 sm:px-10 sm:pb-8">
            <div class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3 lg:hidden">
                    <img src="{{ config('settings.site_icon') }}" alt="" class="size-10 object-contain">
                    <span class="font-display font-semibold leading-tight">Cabang Dinas Kehutanan<br><span class="text-sm font-normal text-ink/70">Wilayah Trenggalek</span></span>
                </a>
                <a href="{{ route('home') }}" class="ml-auto inline-flex min-h-11 items-center gap-2 text-sm font-semibold text-forest-800 hover:text-forest-600">
                    <i aria-hidden="true" class="bi bi-arrow-left"></i> Kembali ke situs
                </a>
            </div>

            <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center py-12">
                @yield('content')
            </div>

            <p class="text-center text-xs text-ink/70">© {{ date('Y') }} Cabang Dinas Kehutanan Wilayah Trenggalek</p>
        </main>
    </div>

    @include('public.partials.aksesibilitas')
    @stack('scripts')
</body>

</html>
