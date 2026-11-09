@extends('public.layouts.app')

@section('nav-overlay', true)

@section('title', judul_halaman(__('public.nav.accessibility')))
@section('description', __('public.accessibility.meta_description'))

@push('head')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(
        \App\Support\StructuredData::breadcrumb([[__('public.nav.home'), route('home')], [__('public.nav.accessibility'), route('aksesibilitas')]]),
    ) !!}</script>
@endpush

@php
    $fitur = [
        ['icon' => 'bi-fonts', 'label' => __('public.a11y.text_size'), 'text' => __('public.accessibility.feature_size')],
        ['icon' => 'bi-circle-half', 'label' => __('public.a11y.contrast'), 'text' => __('public.accessibility.feature_contrast')],
        ['icon' => 'bi-text-paragraph', 'label' => __('public.a11y.spacing'), 'text' => __('public.accessibility.feature_spacing')],
        ['icon' => 'bi-link-45deg', 'label' => __('public.a11y.underline'), 'text' => __('public.accessibility.feature_underline')],
        ['icon' => 'bi-pause-circle', 'label' => __('public.a11y.no_motion'), 'text' => __('public.accessibility.feature_motion')],
        ['icon' => 'bi-eyeglasses', 'label' => __('public.a11y.readable_font'), 'text' => __('public.accessibility.feature_font')],
    ];
    $penerapan = [
        __('public.accessibility.done_keyboard'),
        __('public.accessibility.done_skip_link'),
        __('public.accessibility.done_headings'),
        __('public.accessibility.done_alt'),
        __('public.accessibility.done_contrast'),
        __('public.accessibility.done_motion'),
        __('public.accessibility.done_mobile'),
        __('public.accessibility.done_new_tab'),
    ];
    $keterbatasan = [
        __('public.accessibility.limit_alt'),
        __('public.accessibility.limit_map'),
        __('public.accessibility.limit_documents'),
    ];
@endphp

@section('content')
    <section class="bg-forest-950 pt-36 pb-16 text-white sm:pb-20">
        <div class="mx-auto max-w-4xl px-5 sm:px-8">
            <p class="eyebrow !text-leaf">{{ __('public.a11y.title') }}</p>
            <h1 class="section-title">{{ __('public.a11y.statement') }}</h1>
            <p class="mt-6 max-w-2xl text-lg text-white/80">
                {{ __('public.accessibility.intro') }}
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-4xl space-y-16 px-5 py-16 sm:px-8 sm:py-20">
        <section aria-labelledby="standar">
            <h2 id="standar" class="font-display text-3xl">{{ __('public.accessibility.standard_title') }}</h2>
            <p class="mt-4 text-lg leading-relaxed text-ink/80">
                {!! __('public.accessibility.standard_p1') !!}
            </p>
            <p class="mt-4 text-lg leading-relaxed text-ink/80">
                {!! __('public.accessibility.standard_p2') !!}
            </p>
        </section>

        <section aria-labelledby="menu">
            <h2 id="menu" class="font-display text-3xl">{{ __('public.accessibility.menu_title') }}</h2>
            <p class="mt-4 text-lg leading-relaxed text-ink/80">
                {!! __('public.accessibility.menu_p1', ['button' => '<i aria-hidden="true" class="bi bi-universal-access"></i> <strong>' . e(__('public.a11y.title')) . '</strong>']) !!}
            </p>
            <ul class="mt-8 grid gap-3 sm:grid-cols-2">
                @foreach ($fitur as $item)
                    <li class="flex gap-4 rounded-2xl border border-forest-900/10 bg-white/60 p-5">
                        <i aria-hidden="true" class="bi {{ $item['icon'] }} text-2xl text-forest-700"></i>
                        <span>
                            <span class="block font-semibold">{{ $item['label'] }}</span>
                            <span class="mt-1 block text-sm text-ink/70">{{ $item['text'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-6 text-ink/80">
                {{ __('public.accessibility.menu_p2') }}
            </p>
        </section>

        <section aria-labelledby="penerapan">
            <h2 id="penerapan" class="font-display text-3xl">{{ __('public.accessibility.done_title') }}</h2>
            <ul class="mt-6 space-y-3">
                @foreach ($penerapan as $item)
                    <li class="flex gap-3 text-lg text-ink/80">
                        <i aria-hidden="true" class="bi bi-check-circle-fill mt-0.5 text-forest-600"></i>
                        <span>{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section aria-labelledby="keterbatasan">
            <h2 id="keterbatasan" class="font-display text-3xl">{{ __('public.accessibility.limits_title') }}</h2>
            <ul class="mt-6 space-y-3">
                @foreach ($keterbatasan as $item)
                    <li class="flex gap-3 text-lg text-ink/80">
                        <i aria-hidden="true" class="bi bi-exclamation-circle mt-0.5 text-clay"></i>
                        <span>{{ $item }}</span>
                    </li>
                @endforeach
            </ul>
        </section>

        <section aria-labelledby="umpan-balik" class="rounded-[2rem] bg-forest-900 p-8 text-white sm:p-10">
            <h2 id="umpan-balik" class="font-display text-3xl">{{ __('public.accessibility.report_title') }}</h2>
            <p class="mt-4 text-lg text-white/80">
                {{ __('public.accessibility.report_text') }}
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="mailto:cdkwilayahtrenggalek@jatimprov.go.id?subject={{ rawurlencode(__('public.accessibility.report_subject')) }}" class="btn-primary">
                    <i aria-hidden="true" class="bi bi-envelope"></i> cdkwilayahtrenggalek@jatimprov.go.id
                </a>
                <a href="{{ route('kontak') }}" class="btn-ghost">{{ __('public.common.contact_form') }}</a>
            </div>
        </section>

        <p class="text-sm text-ink/70">{{ __('public.accessibility.reviewed') }}</p>
    </div>
@endsection
