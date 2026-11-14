{{-- Script analitik dari Pengaturan > Integrations. Hanya dimuat di situs publik. --}}
@if (! empty(config('settings.google_analytics_script')))
    {!! config('settings.google_analytics_script') !!}
@endif
