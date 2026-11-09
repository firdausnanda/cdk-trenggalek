{{--
    Shared layout for the 403, 404 and 500 pages. Uses the same logo as the rest of the site; the
    settings are loaded at boot, and a bundled file stands in if they could not be read.
--}}
@php($logo = config('settings.site_icon') ?: asset('images/logo/icon.webp'))
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex">
    <title>@yield('code') · @yield('title') · Cabang Dinas Kehutanan Wilayah Trenggalek</title>
    <meta name="theme-color" content="#0b2118">
    <link rel="icon" href="{{ $logo }}">
    @include('public.partials.head-script')
    @vite(['resources/css/public.css', 'resources/js/public.js'])
</head>

<body class="bg-cream font-sans text-ink antialiased">
    <div class="flex min-h-[100svh] flex-col">
        <header class="border-b border-forest-900/10">
            <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ $logo }}" alt="" class="size-10 object-contain">
                    <span class="leading-tight">
                        <span class="block font-display font-semibold">Cabang Dinas Kehutanan</span>
                        <span class="block text-xs text-ink/70">Wilayah Trenggalek</span>
                    </span>
                </a>
            </div>
        </header>

        <main id="konten" class="relative isolate flex flex-1 items-center overflow-hidden px-5 py-16 sm:px-8">
            {{-- Oversized error code as a quiet background mark. --}}
            <p aria-hidden="true"
                class="pointer-events-none absolute inset-x-0 top-1/2 -z-10 -translate-y-1/2 text-center font-display text-[11rem] leading-none font-bold tracking-tighter text-forest-900/[0.04] select-none sm:text-[20rem] lg:text-[26rem]">
                @yield('code')</p>

            <div class="mx-auto w-full max-w-2xl text-center">
                <span class="mx-auto grid size-16 place-items-center rounded-2xl bg-forest-900 text-3xl text-leaf shadow-lg shadow-forest-950/20">
                    <i aria-hidden="true" class="bi @yield('icon')"></i>
                </span>
                <p class="mt-6 text-sm font-semibold tracking-[0.18em] text-clay uppercase">Galat @yield('code')</p>
                <h1 class="mt-3 font-display text-3xl font-bold tracking-[-0.025em] text-balance sm:text-5xl">@yield('title')</h1>
                <p class="mx-auto mt-4 max-w-xl text-lg leading-relaxed text-ink/75">@yield('message')</p>

                <div class="mt-9 flex flex-wrap justify-center gap-3">
                    @yield('actions')
                </div>

                @yield('extra')
            </div>
        </main>

        <footer class="border-t border-forest-900/10 px-5 py-5 text-center text-sm text-ink/70 sm:px-8">
            Butuh bantuan? Hubungi
            <a href="mailto:cdkwilayahtrenggalek@jatimprov.go.id" class="font-semibold text-forest-800 underline underline-offset-4">cdkwilayahtrenggalek@jatimprov.go.id</a>
        </footer>
    </div>
</body>

</html>
