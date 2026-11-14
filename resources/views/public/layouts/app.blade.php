<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('settings.app_name'))</title>
    <meta name="author" content="Firdaus Nanda Christian">
    @include('public.partials.seo')
    <meta name="theme-color" content="#0b2118">

    <link rel="icon" href="{{ config('settings.site_icon') }}">
    <link rel="apple-touch-icon" href="{{ config('settings.site_icon') }}">

    @include('public.partials.head-script')
    @stack('head')
    @vite(['resources/css/public.css', 'resources/js/public.js'])
    @include('public.partials.analytics')
</head>

<body id="top" class="bg-cream font-sans text-ink antialiased">
    <a href="#konten"
        class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-full focus:bg-leaf focus:px-4 focus:py-2 focus:text-forest-950">
        {{ __('public.common.skip_to_content') }}
    </a>

    @include('public.partials.nav')

    <main id="konten">
        @yield('content')
    </main>

    @include('public.partials.footer')

    @include('public.partials.cookie-consent')
    @include('public.partials.aksesibilitas')

    @stack('scripts')
</body>

</html>
