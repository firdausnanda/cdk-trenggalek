{{--
    Search and social-sharing metadata for the public pages. Pages set these with @section:
    title, description, robots (default "index, follow"), canonical (default: the current URL
    without its query string), og_type (default "website") and og_image (absolute URL).
    Page-specific JSON-LD goes in @push('head') with App\Support\StructuredData::toJson().
--}}
@php
    $deskripsiDefault = 'Cabang Dinas Kehutanan Wilayah Trenggalek — melindungi, mengelola, dan melestarikan hutan di Trenggalek, Tulungagung, dan Kediri.';
@endphp
<meta name="description" content="@yield('description', $deskripsiDefault)">
<meta name="robots" content="@yield('robots', 'index, follow')">
<link rel="canonical" href="@yield('canonical', url()->current())">

<meta property="og:site_name" content="{{ config('settings.app_name') }}">
<meta property="og:locale" content="id_ID">
<meta property="og:type" content="@yield('og_type', 'website')">
<meta property="og:title" content="@yield('title', config('settings.app_name'))">
<meta property="og:description" content="@yield('description', $deskripsiDefault)">
<meta property="og:url" content="@yield('canonical', url()->current())">
<meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
<meta name="twitter:card" content="summary_large_image">

<script type="application/ld+json">{!! \App\Support\StructuredData::toJson(\App\Support\StructuredData::organization(), \App\Support\StructuredData::website()) !!}</script>
