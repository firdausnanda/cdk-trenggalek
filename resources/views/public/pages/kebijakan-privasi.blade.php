@extends('public.layouts.app')

@section('nav-overlay', true)

@section('title', judul_halaman(__('public.nav.privacy')))
@section('description', __('public.privacy.meta_description'))

@push('head')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(
        \App\Support\StructuredData::breadcrumb([[__('public.nav.home'), route('home')], [__('public.nav.privacy'), route('kebijakan-privasi')]]),
    ) !!}</script>
@endpush

@php
    $email = 'cdkwilayahtrenggalek@jatimprov.go.id';
    $pilihanCookie = \App\Support\StatisticsConsent::choice(request());

    $daftarIsi = [
        'pengelola' => __('public.privacy.s1'),
        'data' => __('public.privacy.s2'),
        'tujuan' => __('public.privacy.s3'),
        'cookie' => __('public.privacy.s4'),
        'pihak-ketiga' => __('public.privacy.s5'),
        'penyimpanan' => __('public.privacy.s6'),
        'hak' => __('public.privacy.s7'),
        'perubahan' => __('public.privacy.s8'),
    ];

    $dataDikumpulkan = [
        [
            'sumber' => __('public.common.contact_form'),
            'data' => __('public.privacy.data_contact'),
            'tujuan' => __('public.privacy.purpose_contact'),
            'publik' => __('public.privacy.not_shown'),
        ],
        [
            'sumber' => __('public.privacy.source_comments'),
            'data' => __('public.privacy.data_comments'),
            'tujuan' => __('public.privacy.purpose_comments'),
            'publik' => __('public.privacy.public_comments'),
        ],
        [
            'sumber' => __('public.privacy.source_counter'),
            'data' => __('public.privacy.data_counter'),
            'tujuan' => __('public.privacy.purpose_counter'),
            'publik' => __('public.privacy.public_counter'),
        ],
        [
            'sumber' => __('public.privacy.source_logs'),
            'data' => __('public.privacy.data_logs'),
            'tujuan' => __('public.privacy.purpose_logs'),
            'publik' => __('public.privacy.not_shown'),
        ],
    ];

    $cookie = [
        ['nama' => config('session.cookie'), 'jenis' => __('public.privacy.cookie_required'), 'fungsi' => __('public.privacy.cookie_session'), 'masa' => __('public.privacy.minutes', ['count' => config('session.lifetime')])],
        ['nama' => 'XSRF-TOKEN', 'jenis' => __('public.privacy.cookie_required'), 'fungsi' => __('public.privacy.cookie_xsrf'), 'masa' => __('public.privacy.minutes', ['count' => config('session.lifetime')])],
        ['nama' => config('eloquent-viewable.visitor_cookie_key'), 'jenis' => __('public.privacy.cookie_counter_type'), 'fungsi' => __('public.privacy.cookie_counter'), 'masa' => trans_choice('public.privacy.years', 5)],
        ['nama' => \App\Support\StatisticsConsent::COOKIE_NAME, 'jenis' => __('public.cookie.title'), 'fungsi' => __('public.privacy.cookie_choice'), 'masa' => trans_choice('public.privacy.years', 1)],
        ['nama' => 'cdk-aksesibilitas', 'jenis' => __('public.privacy.cookie_pref_type'), 'fungsi' => __('public.privacy.cookie_pref'), 'masa' => __('public.privacy.until_deleted')],
        ['nama' => Auth::guard('commenter')->getRecallerName(), 'jenis' => __('public.privacy.cookie_login_type'), 'fungsi' => __('public.privacy.cookie_login'), 'masa' => __('public.privacy.until_signout')],
    ];

    $hak = [
        ['icon' => 'bi-eye', 'label' => __('public.privacy.right_access'), 'text' => __('public.privacy.right_access_text')],
        ['icon' => 'bi-pencil-square', 'label' => __('public.privacy.right_rectify'), 'text' => __('public.privacy.right_rectify_text')],
        ['icon' => 'bi-trash3', 'label' => __('public.privacy.right_erase'), 'text' => __('public.privacy.right_erase_text')],
        ['icon' => 'bi-hand-index', 'label' => __('public.privacy.right_withdraw'), 'text' => __('public.privacy.right_withdraw_text')],
        ['icon' => 'bi-exclamation-octagon', 'label' => __('public.privacy.right_object'), 'text' => __('public.privacy.right_object_text')],
    ];
@endphp

@section('content')
    <section class="bg-forest-950 pt-36 pb-16 text-white sm:pb-20">
        <div class="mx-auto max-w-4xl px-5 sm:px-8">
            <p class="eyebrow !text-leaf">{{ __('public.privacy.eyebrow') }}</p>
            <h1 class="section-title">{{ __('public.privacy.title') }}</h1>
            <p class="mt-6 max-w-2xl text-lg text-white/80">
                {{ __('public.privacy.intro') }}
            </p>
            <p class="mt-6 text-sm text-white/70">{{ __('public.privacy.effective') }}</p>
        </div>
    </section>

    <div class="mx-auto grid max-w-6xl gap-12 px-5 py-16 sm:px-8 sm:py-20 lg:grid-cols-[14rem_1fr]">
        <nav aria-labelledby="daftar-isi" class="lg:sticky lg:top-24 lg:self-start">
            <h2 id="daftar-isi" class="text-xs font-semibold tracking-[0.18em] text-ink/70 uppercase">{{ __('public.privacy.toc') }}</h2>
            <ol class="mt-4 space-y-1 text-sm">
                @foreach ($daftarIsi as $id => $judul)
                    <li><a href="#{{ $id }}" class="inline-block py-1 text-ink/80 transition hover:text-forest-700">{{ $loop->iteration }}. {{ $judul }}</a></li>
                @endforeach
            </ol>
        </nav>

        <div class="min-w-0 space-y-16 text-lg leading-relaxed text-ink/80">
            <section id="pengelola" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">1. {{ __('public.privacy.s1') }}</h2>
                <p class="mt-4">
                    {!! __('public.privacy.s1_p1') !!}
                </p>
                <p class="mt-4">
                    Gg. Menak Sopal, Sukobanteng, Karangsoko, Kec. Trenggalek, Kabupaten Trenggalek, Jawa Timur 66319 ·
                    <a href="mailto:{{ $email }}" class="font-semibold text-forest-700 underline underline-offset-4">{{ $email }}</a>
                </p>
            </section>

            <section id="data" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">2. {{ __('public.privacy.s2') }}</h2>
                <p class="mt-4">
                    {{ __('public.privacy.s2_p1') }}
                </p>
                <div class="mt-6 overflow-x-auto rounded-2xl border border-forest-900/10">
                    <table class="w-full min-w-[40rem] text-left text-base">
                        <caption class="sr-only">{{ __('public.privacy.data_caption') }}</caption>
                        <thead class="bg-sand text-sm text-ink">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">{{ __('public.privacy.col_source') }}</th>
                                <th scope="col" class="px-4 py-3 font-semibold">{{ __('public.privacy.col_data') }}</th>
                                <th scope="col" class="px-4 py-3 font-semibold">{{ __('public.privacy.col_purpose') }}</th>
                                <th scope="col" class="px-4 py-3 font-semibold">{{ __('public.privacy.col_public') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-forest-900/10 bg-white/60">
                            @foreach ($dataDikumpulkan as $baris)
                                <tr>
                                    <th scope="row" class="px-4 py-3 align-top font-semibold text-ink">{{ $baris['sumber'] }}</th>
                                    <td class="px-4 py-3 align-top">{{ $baris['data'] }}</td>
                                    <td class="px-4 py-3 align-top">{{ $baris['tujuan'] }}</td>
                                    <td class="px-4 py-3 align-top">{{ $baris['publik'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            <section id="tujuan" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">3. {{ __('public.privacy.s3') }}</h2>
                <ul class="mt-4 space-y-3">
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-check-circle-fill mt-1 text-forest-600"></i>
                        <span>{!! __('public.privacy.basis_consent') !!}</span></li>
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-check-circle-fill mt-1 text-forest-600"></i>
                        <span>{!! __('public.privacy.basis_public_task') !!}</span></li>
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-check-circle-fill mt-1 text-forest-600"></i>
                        <span>{!! __('public.privacy.basis_legitimate') !!}</span></li>
                </ul>
                <p class="mt-6">
                    {!! __('public.privacy.s3_p1') !!}
                </p>
            </section>

            <section id="cookie" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">4. {{ __('public.privacy.s4') }}</h2>
                <p class="mt-4">
                    {{ __('public.privacy.s4_p1') }}
                </p>
                <div class="mt-6 overflow-x-auto rounded-2xl border border-forest-900/10">
                    <table class="w-full min-w-[40rem] text-left text-base">
                        <caption class="sr-only">{{ __('public.privacy.cookie_caption') }}</caption>
                        <thead class="bg-sand text-sm text-ink">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">{{ __('public.privacy.col_name') }}</th>
                                <th scope="col" class="px-4 py-3 font-semibold">{{ __('public.privacy.col_type') }}</th>
                                <th scope="col" class="px-4 py-3 font-semibold">{{ __('public.privacy.col_function') }}</th>
                                <th scope="col" class="px-4 py-3 font-semibold">{{ __('public.privacy.col_duration') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-forest-900/10 bg-white/60">
                            @foreach ($cookie as $baris)
                                <tr>
                                    <th scope="row" class="px-4 py-3 align-top font-mono text-sm font-semibold break-all text-ink">{{ $baris['nama'] }}</th>
                                    <td class="px-4 py-3 align-top">{{ $baris['jenis'] }}</td>
                                    <td class="px-4 py-3 align-top">{{ $baris['fungsi'] }}</td>
                                    <td class="px-4 py-3 align-top whitespace-nowrap">{{ $baris['masa'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 rounded-xl border border-forest-900/15 bg-white/70 p-5 sm:p-6">
                    <h3 class="font-display text-xl font-semibold text-ink">{{ __('public.cookie.title') }}</h3>
                    <p class="mt-2 text-base" role="status">
                        @if ($pilihanCookie === \App\Support\StatisticsConsent::ACCEPT)
                            {{ __('public.privacy.choice_accepted') }}
                        @elseif ($pilihanCookie === \App\Support\StatisticsConsent::REJECT)
                            {{ __('public.privacy.choice_rejected') }}
                        @else
                            {{ __('public.privacy.choice_none') }}
                        @endif
                    </p>
                    <form method="POST" action="{{ route('cookie-consent.store') }}" class="mt-4 flex flex-wrap gap-3">
                        @csrf
                        <input type="hidden" name="return_to" value="{{ route('kebijakan-privasi', absolute: false) }}#cookie">
                        <button type="submit" name="choice" value="reject" class="min-h-11 cursor-pointer rounded-lg border border-forest-900 px-4 py-2 text-sm font-semibold text-forest-900 transition hover:bg-forest-50 active:bg-forest-100">{{ __('public.cookie.reject') }}</button>
                        <button type="submit" name="choice" value="accept" class="min-h-11 cursor-pointer rounded-lg bg-forest-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-forest-700 active:bg-forest-800">{{ __('public.cookie.accept') }}</button>
                    </form>
                </div>
                <p class="mt-6">
                    {{ __('public.privacy.s4_p2') }}
                </p>
            </section>

            <section id="pihak-ketiga" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">5. {{ __('public.privacy.s5') }}</h2>
                <ul class="mt-4 space-y-3">
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-dot mt-1 text-2xl leading-none text-forest-600"></i>
                        <span>{{ __('public.privacy.s5_assets') }}</span></li>
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-dot mt-1 text-2xl leading-none text-forest-600"></i>
                        <span>{{ __('public.privacy.s5_google') }}</span></li>
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-dot mt-1 text-2xl leading-none text-forest-600"></i>
                        <span>{{ __('public.privacy.s5_links') }}</span></li>
                </ul>
            </section>

            <section id="penyimpanan" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">6. {{ __('public.privacy.s6') }}</h2>
                <p class="mt-4">
                    {{ __('public.privacy.s6_p1') }}
                </p>
            </section>

            <section id="hak" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">7. {{ __('public.privacy.s7') }}</h2>
                <p class="mt-4">{{ __('public.privacy.s7_p1') }}</p>
                <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                    @foreach ($hak as $item)
                        <li class="flex gap-4 rounded-2xl border border-forest-900/10 bg-white/60 p-5">
                            <i aria-hidden="true" class="bi {{ $item['icon'] }} text-2xl text-forest-700"></i>
                            <span>
                                <span class="block font-semibold text-ink">{{ $item['label'] }}</span>
                                <span class="mt-1 block text-base">{{ $item['text'] }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>
                <div class="mt-8 rounded-[2rem] bg-forest-900 p-8 text-white">
                    <h3 class="font-display text-2xl">{{ __('public.privacy.request_title') }}</h3>
                    <p class="mt-3 text-white/80">
                        {{ __('public.privacy.request_text') }}
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="mailto:{{ $email }}?subject={{ rawurlencode(__('public.privacy.request_subject')) }}" class="btn-primary">
                            <i aria-hidden="true" class="bi bi-envelope"></i> {{ __('public.privacy.request_button') }}
                        </a>
                        <a href="{{ route('kontak') }}" class="btn-ghost">{{ __('public.common.contact_form') }}</a>
                    </div>
                </div>
            </section>

            <section id="perubahan" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">8. {{ __('public.privacy.s8') }}</h2>
                <p class="mt-4">
                    {{ __('public.privacy.s8_p1') }}
                </p>
            </section>
        </div>
    </div>
@endsection
