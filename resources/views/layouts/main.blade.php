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
        .org-chart {
            position: relative;
            margin: 50px auto;
            width: fit-content;
            text-align: center;
        }

        .org-chart ul {
            padding-left: 0;
            position: relative;
            transition: all 0.5s;
            display: flex;
            align-items: flex-start;
        }

        .org-chart ul ul {
            margin-top: 20px;
        }

        .org-chart li {
            display: table-cell;
            text-align: center;
            list-style-type: none;
            position: relative;
            padding: 20px 40px 0 40px;
            transition: all 0.5s;
        }

        .org-chart li::before,
        .org-chart li::after {
            content: '';
            position: absolute;
            top: 0;
            right: 50%;
            border-top: 2px solid #2E7D32;
            width: 60%;
            height: 20px;
        }

        .org-chart li::after {
            right: auto;
            left: 50%;
            border-left: 2px solid #2E7D32;
        }

        .org-chart li:only-child::after,
        .org-chart li:only-child::before {
            display: none;
        }

        .org-chart li:only-child {
            padding-top: 0;
        }

        .org-chart li:first-child::before,
        .org-chart li:last-child::after {
            border: 0 none;
        }

        .org-chart li:last-child::before {
            border-right: 2px solid #2E7D32;
            border-radius: 0 5px 0 0;
        }

        .org-chart li:first-child::after {
            border-radius: 5px 0 0 0;
        }

        .org-chart ul ul::before {
            content: '';
            position: absolute;
            top: -20px;
            left: 50%;
            border-left: 2px solid #2E7D32;
            width: 0;
            height: 20px;
        }

        .org-chart li div {
            border: 2px solid #2E7D32;
            padding: 15px;
            display: inline-block;
            border-radius: 5px;
            background-color: white;
            transition: all 0.3s;
            min-width: 150px;
            max-width: 250px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 0 15px;
        }

        .org-chart li div:hover {
            background-color: #81C784;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        }

        .org-chart .position {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .org-chart .name {
            font-size: 0.9em;
        }

        @media screen and (max-width: 992px) {
            .org-chart li {
                display: block;
                padding: 10px 5px 0 5px;
            }

            .org-chart li::before,
            .org-chart li::after {
                display: none;
            }

            .org-chart ul ul::before {
                display: none;
            }

            .org-chart li div {
                margin-bottom: 20px;
            }
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
