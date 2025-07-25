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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">

    <style>
        .post-content {
            width: 100%;
            max-width: 100%;
            /* Pastikan tidak melebihi lebar container */
        }

        .post-content p {
            margin: 0 0 1rem 0;
            /* Atur margin bawah saja */
            padding: 0;
            width: 100%;
            hyphens: auto;
            /* Sambungan kata otomatis dengan tanda hubung (-) */
            word-break: break-word;
            /* Untuk kata panjang */
            overflow-wrap: anywhere;
            /* Alternatif modern untuk word-wrap */
        }

        .post-content span {
            display: inline;
            /* Kembalikan ke default inline */
            white-space: normal;
            /* Pastikan teks bisa wrap */
            background-color: inherit;
            /* Warisi warna background */
            color: inherit;
            /* Warisi warna teks */
            line-height: inherit;
            /* Pertahankan line-height */
        }

        .post-content img {
            max-width: 100%;
            /* Gambar tidak melebihi lebar parent */
            height: auto;
            /* Pertahankan aspect ratio */
            display: block;
            /* Hilangkan space bawah bawaan img inline */
            margin: 0 auto;
            /* Pusatkan gambar (opsional) */
            object-fit: contain;
            /* Pastikan gambar utuh terlihat */
            border-radius: 4px;
            /* Optional: rounded corners */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <!-- Loading -->
    <div id="loading">
        <div class="spinner-container">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-text">Memuat...</div>
        </div>
    </div>

    <!-- Menu -->
    @include('layouts.partials.menu')

    <!-- Content -->
    @yield('content')

    <!-- Footer -->
    @include('layouts.partials.footer')

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
        integrity="sha256-4+XzXVhsDmqanXGHaHvgh1gMQKX40OUvDEBTu8JcmNs=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/share.js') }}"></script>

    <script src="{{ asset('js/landing.js') }}"></script>
</body>

</html>
