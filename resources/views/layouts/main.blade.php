<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('settings.app_name') }}</title>
    <meta name="description" content="Dinas Kehutanan Wilayah Trenggalek">
    <meta name="keywords" content="Dinas Kehutanan Wilayah Trenggalek">
    <meta name="author" content="Dinas Kehutanan Wilayah Trenggalek">
    <meta name="robots" content="index, follow">
    <meta name="googlebot" content="index, follow">
    <meta name="google" content="notranslate">

    <link rel="icon" href="{{ config('settings.site_icon') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ config('settings.site_icon') }}">
    <link rel="manifest" href="{{ config('settings.site_icon') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/animate/animate.min.css') }}">
    <!-- Custom CSS -->
    @stack('css')
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>

<body>

    <!-- Loading -->
    @include('components.loading-screen')
    {{-- <div id="loading">
        <div class="spinner-container">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-text">Memuat...</div>
        </div>
    </div> --}}

    <!-- Menu -->
    @include('layouts.partials.menu')

    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    @include('layouts.partials.footer')

    <!-- Bootstrap JS -->
    <script src="{{ asset('vendor/jquery/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('vendor/sweetalert/sweetalert2@11.js') }}"></script>
    <script src="{{ asset('js/share.js') }}"></script>

    <script src="{{ asset('js/landing.js') }}"></script>
    <script>
        // Re-trigger animation when slide changes
        const heroCarousel = document.getElementById('heroCarousel');
        heroCarousel.addEventListener('slide.bs.carousel', (e) => {
            const next = e.relatedTarget;
            const content = next.querySelector('.content');
            content.style.animation = 'none';
            setTimeout(() => {
                content.style.animation = '';
            }, 10);
        });
    </script>

    @stack('js')
</body>

</html>
