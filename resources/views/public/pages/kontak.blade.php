@extends('public.layouts.app')

@section('nav-overlay', true)

@section('title', judul_halaman(__('public.nav.contact')))
@section('description', __('public.contact.meta_description'))

@push('head')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(
        \App\Support\StructuredData::breadcrumb([[__('public.nav.home'), route('home')], [__('public.nav.contact'), route('kontak')]]),
        ...\App\Support\StructuredData::offices(),
    ) !!}</script>
@endpush

@php
    $email = config('cdk.email');
    $bagianEmail = explode('@', $email, 2);
    $field = 'w-full rounded-xl border-0 bg-white px-4 py-3 text-ink ring-1 ring-forest-900/15 placeholder:text-ink/50 focus:ring-[3px] focus:ring-forest-700 focus:outline-none';
    $sosial = [
        ['icon' => 'bi-facebook', 'label' => 'Facebook', 'href' => 'https://www.facebook.com/cdktrenggalekprogram'],
        ['icon' => 'bi-instagram', 'label' => 'Instagram', 'href' => 'https://www.instagram.com/cdktrenggalek/'],
        ['icon' => 'bi-youtube', 'label' => 'YouTube', 'href' => 'https://www.youtube.com/channel/UCzVEqqMfVnTHqbAKe-ememg'],
    ];
@endphp

@section('content')
    {{-- Page header --}}
    <section class="relative isolate overflow-hidden bg-forest-950 text-white">
        <img src="{{ asset('images/background/gedung-cdk.webp') }}" alt="" fetchpriority="high"
            class="absolute inset-0 -z-20 size-full object-cover">
        <div class="absolute inset-0 -z-10 bg-forest-950/85 lg:bg-transparent lg:bg-gradient-to-r lg:from-forest-950 lg:via-forest-950/90 lg:to-forest-950/45"></div>

        <div class="mx-auto max-w-7xl px-5 pt-32 pb-14 sm:px-8 lg:pt-40 lg:pb-16">
            <nav data-reveal aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-white/75">
                    <li><a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 py-1 hover:text-white">
                            <i aria-hidden="true" class="bi bi-house-door"></i> {{ __('public.nav.home') }}</a></li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                    <li aria-current="page" class="font-semibold text-white">{{ __('public.nav.contact') }}</li>
                </ol>
            </nav>
            <h1 data-reveal style="--reveal-delay: 80ms"
                class="mt-6 max-w-3xl font-display text-[2.4rem] leading-[1.04] font-bold tracking-[-0.035em] text-balance sm:text-6xl">
                {!! __('public.contact.hero_title') !!}
            </h1>
            <p data-reveal style="--reveal-delay: 160ms" class="mt-5 max-w-xl text-base leading-relaxed text-white/85 sm:text-lg">
                {{ __('public.contact.hero_text') }}
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-5 py-14 sm:px-8 sm:py-20">
        <div class="grid gap-8 lg:grid-cols-12 lg:gap-10">
            {{-- Form --}}
            <div id="form-kontak" data-reveal class="scroll-mt-24 rounded-2xl bg-white/70 p-6 ring-1 ring-forest-900/10 sm:p-9 lg:col-span-7 lg:self-start">
                <h2 class="font-display text-2xl font-semibold">{{ __('public.contact.send_message') }}</h2>
                <p class="mt-2 text-ink/70">{{ __('public.contact.form_intro') }}</p>

                @if (session('success'))
                    <p role="status" class="mt-6 flex items-start gap-3 rounded-xl bg-forest-900 px-4 py-3 text-sm text-white">
                        <i aria-hidden="true" class="bi bi-check-circle-fill mt-0.5 text-leaf"></i> {{ session('success') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('kontak_store') }}" class="mt-8 grid gap-5 sm:grid-cols-2" novalidate>
                    @csrf

                    <div>
                        <label for="kontak-nama" class="text-sm font-semibold">{{ __('public.form.full_name') }} <span aria-hidden="true" class="text-clay">*</span></label>
                        <input id="kontak-nama" type="text" name="name" value="{{ old('name') }}" required maxlength="255" autocomplete="name"
                            @error('name') aria-invalid="true" aria-describedby="galat-nama" @enderror class="mt-2 {{ $field }}">
                        @error('name')<p id="galat-nama" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="kontak-email" class="text-sm font-semibold">{{ __('public.form.email') }} <span aria-hidden="true" class="text-clay">*</span></label>
                        <input id="kontak-email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email"
                            @error('email') aria-invalid="true" aria-describedby="galat-email" @enderror class="mt-2 {{ $field }}">
                        @error('email')<p id="galat-email" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="kontak-pesan" class="text-sm font-semibold">{{ __('public.form.message') }} <span aria-hidden="true" class="text-clay">*</span></label>
                        <textarea id="kontak-pesan" name="message" rows="6" required maxlength="5000"
                            @error('message') aria-invalid="true" aria-describedby="galat-pesan" @enderror
                            class="mt-2 {{ $field }}">{{ old('message') }}</textarea>
                        @error('message')<p id="galat-pesan" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col gap-4 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-ink/70">
                            {{ __('public.contact.form_notice') }}
                            <a href="{{ route('kebijakan-privasi') }}" class="font-semibold text-forest-800 underline underline-offset-4">{{ __('public.form.privacy_link') }}</a>
                        </p>
                        <button type="submit" class="btn-primary shrink-0 !bg-forest-900 !text-white hover:!bg-forest-700">
                            {{ __('public.contact.send_message') }} <i aria-hidden="true" class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Contact details --}}
            <aside data-reveal style="--reveal-delay: 120ms" class="flex flex-col gap-5 lg:col-span-5" aria-label="{{ __('public.contact.info') }}">
                <div class="rounded-2xl bg-forest-900 p-6 text-white sm:p-8">
                    <h2 class="flex items-center gap-3 font-display text-xl font-semibold">
                        <i aria-hidden="true" class="bi bi-clock text-leaf"></i> {{ __('public.common.service_hours_label') }}
                    </h2>
                    <dl class="mt-5 divide-y divide-white/10">
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-white/80">{{ __('public.contact.weekdays') }}</dt>
                            <dd class="font-semibold">{{ __('public.contact.weekday_hours') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 py-3">
                            <dt class="text-white/80">{{ __('public.contact.weekend') }}</dt>
                            <dd class="font-semibold">{{ __('public.contact.closed') }}</dd>
                        </div>
                    </dl>
                    <p class="mt-4 text-sm text-white/75">{{ __('public.contact.outside_hours') }}</p>
                </div>

                <div class="rounded-2xl bg-white/70 p-6 ring-1 ring-forest-900/10 sm:p-8">
                    <h2 class="flex items-center gap-3 font-display text-xl font-semibold">
                        <i aria-hidden="true" class="bi bi-envelope text-forest-700"></i> {{ __('public.contact.email_social') }}
                    </h2>
                    <div class="mt-5">
                        <p class="text-xs font-semibold tracking-[0.12em] text-ink/65 uppercase">{{ __('public.contact.email_address') }}</p>
                        <a href="mailto:{{ $email }}" class="mt-2 inline-block max-w-full font-semibold leading-relaxed text-forest-800 underline decoration-forest-700/40 underline-offset-4 transition hover:decoration-forest-700">{{ $bagianEmail[0] }}<wbr>&#64;{{ $bagianEmail[1] }}</a>
                    </div>
                    <p class="mt-6 border-t border-forest-900/10 pt-5 text-xs font-semibold tracking-[0.12em] text-ink/65 uppercase">{{ __('public.contact.social_media') }}</p>
                    <ul class="mt-3 flex flex-wrap gap-2">
                        @foreach ($sosial as $item)
                            <li>
                                <a href="{{ $item['href'] }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex min-h-11 items-center gap-2 rounded-full bg-white px-4 text-sm font-semibold text-forest-800 ring-1 ring-forest-900/15 transition hover:bg-forest-900 hover:text-white">
                                    <i aria-hidden="true" class="bi {{ $item['icon'] }}"></i> {{ $item['label'] }}
                                    <span class="sr-only">({{ __('public.common.new_tab') }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="rounded-2xl bg-white/70 p-6 ring-1 ring-forest-900/10 sm:p-8">
                    <h2 class="flex items-center gap-3 font-display text-xl font-semibold">
                        <i aria-hidden="true" class="bi bi-buildings text-forest-700"></i> {{ __('public.common.our_offices') }}
                    </h2>
                    <ul class="mt-4 divide-y divide-forest-900/10">
                        @foreach (kantor_cdk() as $kantor)
                            <li class="py-4 first:pt-1 last:pb-0">
                                <p class="font-semibold">{{ $kantor['nama'] }}</p>
                                <p class="mt-1 text-sm text-ink/75">{{ $kantor['alamat'] }}</p>
                                <a href="{{ $kantor['maps'] }}" target="_blank" rel="noopener noreferrer"
                                    class="mt-2 inline-flex min-h-9 items-center gap-1.5 text-sm font-semibold text-forest-800">
                                    {{ __('public.common.directions') }} <i aria-hidden="true" class="bi bi-arrow-up-right text-xs"></i>
                                    <span class="sr-only">{{ __('public.common.directions_to', ['place' => $kantor['nama']]) }} ({{ __('public.common.new_tab') }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
        </div>
    </section>
@endsection
