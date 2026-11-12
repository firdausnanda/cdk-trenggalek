@extends('public.layouts.app')

@section('nav-overlay', true)
@section('title', __('public.home.meta_title'))
@section('description', __('public.home.meta_description'))

@php
    // Forestry figures from SMART-HUT (see App\Services\SmartHutService).
    $regions = $kehutanan['regions'];
    // Region names are proper nouns; only the long label ("Kabupaten …" / "… Regency") is translated.
    foreach ($regions as $slug => $region) {
        $regions[$slug]['label'] = \Illuminate\Support\Facades\Lang::has("public.regions.{$slug}") ? __("public.regions.{$slug}") : $region['label'];
    }
    $totals = $kehutanan['totals'];
    $capaian = $kehutanan['capaian'];
    // Capaian year (latest year with final data); the transaction figure carries its own month range.
    $tahun = $capaian['tahun'];
    $periodeTransaksi = \App\Services\SmartHutService::periodLabel($kehutanan['periode'], $kehutanan['tahun']);
    $sumberData = __('public.home.data_source')
        . ($kehutanan['diperbarui'] ? ' · ' . __('public.home.data_updated', ['date' => $kehutanan['diperbarui']->translatedFormat('d F Y')]) : '');

    $heroStats = [
        ['value' => $totals['kth'], 'label' => __('public.home.stat_kth')],
        ['value' => $totals['anggota_kth'], 'label' => __('public.home.kth_members')],
        ['value' => $totals['ps'], 'label' => __('public.home.ps_groups')],
        ['value' => $totals['nilai_transaksi_ekonomi'], 'label' => __('public.home.stat_economic', ['year' => $periodeTransaksi]), 'rupiah' => true],
    ];

    $services = [
        ['short' => __('public.home.service_permit_short'), 'icon' => 'bi-file-earmark-check', 'title' => __('public.home.service_permit_title'), 'text' => __('public.home.service_permit_text')],
        ['short' => __('public.home.service_rehab_short'), 'icon' => 'bi-flower2', 'title' => __('public.home.service_rehab_title'), 'text' => __('public.home.service_rehab_text')],
        ['short' => __('public.home.service_control_short'), 'icon' => 'bi-shield-check', 'title' => __('public.home.service_control_title'), 'text' => __('public.home.service_control_text')],
        ['short' => __('public.home.service_community_short'), 'icon' => 'bi-people', 'title' => __('public.home.service_community_title'), 'text' => __('public.home.service_community_text')],
    ];

    $map = json_decode(file_get_contents(resource_path('data/wilayah-map.json')), true);
    $placeholder = asset('images/not-found/image.jpeg');
@endphp

@section('content')
    {{-- Hero --}}
    <section x-data="parallax" @pointermove="move($event)"
        class="relative isolate flex min-h-[100svh] flex-col overflow-hidden bg-forest-950 text-white">
        <div class="absolute inset-0 -z-10 scale-[1.06] transition-transform duration-700 ease-out" :style="style">
            <img src="{{ asset('images/background/bg.avif') }}" alt="" fetchpriority="high"
                class="animate-kenburns size-full object-cover">
        </div>
        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-forest-950 via-forest-950/50 to-forest-950/40"></div>

        <div class="mx-auto flex w-full max-w-7xl flex-1 flex-col px-5 pt-28 pb-6 sm:px-8 lg:pb-10">
            <div class="flex flex-1 flex-col justify-center py-10">
                <h1 data-reveal
                    class="max-w-4xl font-display text-[2.6rem] leading-[1.02] font-bold tracking-[-0.035em] text-balance sm:text-6xl lg:text-7xl xl:text-[5rem]">
                    {!! __('public.home.hero_title') !!}
                </h1>
                <p data-reveal style="--reveal-delay: 120ms"
                    class="mt-6 max-w-xl text-base leading-relaxed text-white/80 sm:text-lg">
                    {{ __('public.home.hero_text') }}
                </p>
                <div data-reveal style="--reveal-delay: 200ms" class="mt-9 flex flex-wrap gap-3">
                    <a href="#layanan" class="btn-primary group">
                        {{ __('public.home.hero_cta') }} <i aria-hidden="true" class="bi bi-arrow-down transition group-hover:translate-y-0.5"></i>
                    </a>
                    <a href="#wilayah" class="btn-ghost">{{ __('public.home.hero_cta_regions') }}</a>
                </div>
            </div>

            <dl data-reveal style="--reveal-delay: 280ms"
                class="grid grid-cols-2 border-t border-white/15 lg:grid-cols-4">
                @foreach ($heroStats as $stat)
                    <div @class([
                        'flex flex-col py-5 sm:py-6 lg:px-8',
                        'lg:pl-0' => $loop->first,
                        'lg:border-l lg:border-white/15' => ! $loop->first,
                        'border-t border-white/15 lg:border-t-0' => $loop->index >= 2,
                        'border-l border-white/15 pl-5' => $loop->index % 2 === 1,
                    ])>
                        <dt class="mt-1 text-sm text-white/65">{{ $stat['label'] }}</dt>
                        <dd class="order-first font-display text-3xl whitespace-nowrap tabular-nums sm:text-4xl" x-count="{{ $stat['value'] }}"
                            @if ($stat['rupiah'] ?? false) data-format="rupiah" @endif>
                            {{ ($stat['rupiah'] ?? false) ? rupiah_singkat($stat['value']) : angka($stat['value']) }}
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    {{-- Berita --}}
    <section id="berita" class="bg-sand/60 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div data-reveal class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="eyebrow">{{ __('public.home.news_eyebrow') }}</p>
                    <h2 class="section-title">{!! __('public.home.news_title') !!}</h2>
                </div>
                <a href="{{ route('berita') }}" class="btn-ghost w-fit">{{ __('public.home.all_news') }} <i aria-hidden="true" class="bi bi-arrow-right"></i></a>
            </div>

            @if ($posts->isEmpty())
                <div class="mt-12 rounded-3xl border border-dashed border-forest-900/20 p-12 text-center text-ink/60">
                    <i aria-hidden="true" class="bi bi-newspaper text-4xl"></i>
                    <p class="mt-3">{{ __('public.home.no_news') }}</p>
                </div>
            @else
                @php
                    $featured = $posts->first();
                @endphp
                <div class="mt-12 grid gap-6 lg:grid-cols-12">
                    <article data-reveal
                        class="group relative isolate flex min-h-[26rem] flex-col justify-end overflow-hidden rounded-[2rem] bg-forest-950 p-7 text-white sm:p-10 lg:col-span-7 lg:min-h-[34rem]">
                        <img src="{{ $featured->featured_image ?? $placeholder }}"
                            @if ($srcset = image_srcset($featured->featured_image)) srcset="{{ $srcset }}" sizes="(min-width: 1024px) 58vw, 100vw" @endif
                            alt="" loading="lazy"
                            class="absolute inset-0 -z-10 size-full object-cover opacity-80 transition duration-700 ease-soft group-hover:scale-105">
                        <div class="absolute inset-0 -z-10 bg-gradient-to-t from-forest-950 via-forest-950/60 to-transparent"></div>

                        <span class="w-fit rounded-full bg-leaf px-3 py-1 text-xs font-semibold text-forest-950">{{ __('public.common.latest') }}</span>
                        <time class="mt-4 text-sm text-white/70" datetime="{{ $featured->published_at?->toDateString() }}">
                            {{ $featured->published_at?->translatedFormat('d F Y') }}
                        </time>
                        <h3 class="mt-2 max-w-2xl font-display text-3xl leading-tight text-balance sm:text-4xl">
                            <a href="{{ route('berita.show', $featured->slug) }}" class="after:absolute after:inset-0">
                                {{ $featured->title }}
                            </a>
                        </h3>
                        <p class="mt-3 line-clamp-2 max-w-2xl text-white/75">{{ Str::limit(strip_tags($featured->excerpt), 180) }}</p>
                    </article>

                    <div class="flex flex-col gap-4 lg:col-span-5">
                        @foreach ($posts->skip(1) as $post)
                            <article data-reveal style="--reveal-delay: {{ $loop->iteration * 100 }}ms"
                                class="group relative flex gap-5 rounded-3xl bg-cream p-4 transition duration-500 ease-soft hover:-translate-y-1 hover:shadow-xl hover:shadow-forest-950/10">
                                <img src="{{ $post->featured_image ?? $placeholder }}"
                                    @if ($srcset = image_srcset($post->featured_image)) srcset="{{ $srcset }}" sizes="8rem" @endif
                                    alt="" loading="lazy" class="size-28 shrink-0 rounded-2xl object-cover sm:size-32">
                                <div class="flex min-w-0 flex-col py-1">
                                    <time class="text-xs font-medium text-clay" datetime="{{ $post->published_at?->toDateString() }}">
                                        {{ $post->published_at?->translatedFormat('d F Y') }}
                                    </time>
                                    <h3 class="mt-2 line-clamp-3 font-display text-lg leading-snug transition group-hover:text-forest-600">
                                        <a href="{{ route('berita.show', $post->slug) }}" class="after:absolute after:inset-0">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    <span class="mt-auto inline-flex items-center gap-1 pt-2 text-sm font-semibold text-forest-700">
                                        {{ __('public.common.read') }} <i aria-hidden="true" class="bi bi-arrow-right transition group-hover:translate-x-1"></i>
                                    </span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Wilayah kerja (interactive map) --}}
    <section id="wilayah" class="scroll-mt-16 bg-forest-950 py-24 text-white sm:py-28"
        x-data="{
            regions: @js($regions),
            active: 'trenggalek',
            metric: 'kth',
            metrics: @js(['kth' => __('public.home.metric_kth'), 'ps' => __('public.home.metric_ps'), 'nilai_transaksi_ekonomi' => __('public.home.metric_value')]),
            get region() { return this.regions[this.active] },
            get min() { return Math.min(...Object.values(this.regions).map(r => r[this.metric])) },
            get max() { return Math.max(...Object.values(this.regions).map(r => r[this.metric])) },
            fill(slug) {
                const spread = this.max - this.min;
                const share = spread ? (this.regions[slug][this.metric] - this.min) / spread : 0.5;
                return `fill: color-mix(in oklab, #3d865c ${Math.round(share * 100)}%, #bad4a8)`;
            },
        }">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div data-reveal>
                <p class="eyebrow !text-leaf">{{ __('public.home.regions_eyebrow') }}</p>
                <h2 class="section-title max-w-2xl">{!! __('public.home.regions_title') !!}</h2>
            </div>

            @php
                $periode = fn (array $item) => \App\Services\SmartHutService::periodLabel($item['periode'], $item['tahun']);
                $catatan = fn (?string ...$parts) => implode(' · ', array_filter($parts)) ?: null;
                $rehab = $capaian['rehab_lahan'];
                $wisata = $capaian['wisata'];
                $capaianItems = [
                    [
                        'label' => __('public.home.ach_rehab'), 'value' => $rehab['realisasi'], 'decimals' => 1, 'unit' => 'ha',
                        'note' => $catatan(
                            $rehab['persen'] !== null
                                ? __('public.home.ach_rehab_note', ['percent' => angka(min(100, $rehab['persen']), 0), 'target' => angka($rehab['target'], 1)])
                                : __('public.home.ach_realised'),
                            $periode($rehab),
                        ),
                    ],
                    ['label' => __('public.home.ach_greening'), 'value' => $capaian['penghijauan']['realisasi'], 'decimals' => 1, 'unit' => 'ha', 'note' => $periode($capaian['penghijauan'])],
                    [
                        'label' => __('public.home.ach_tourism'), 'value' => $wisata['pengunjung'], 'decimals' => 0, 'unit' => __('public.home.unit_people'),
                        'note' => $catatan(
                            __('public.home.ach_tourism_note', ['amount' => rupiah_singkat($wisata['pendapatan'])]),
                            $periode($wisata),
                            $wisata['pengelola'] ? trans_choice('public.home.ach_tourism_reporters', count($wisata['pengelola']), ['names' => implode(', ', $wisata['pengelola'])]) : null,
                        ),
                    ],
                    ['label' => __('public.home.ach_fire'), 'value' => $capaian['kebakaran']['kejadian'], 'decimals' => 0, 'unit' => __('public.home.unit_incidents'), 'note' => $catatan(__('public.home.ach_fire_note', ['area' => angka($capaian['kebakaran']['luas'], 1)]), $periode($capaian['kebakaran']))],
                ];
            @endphp
            <div class="mt-12 grid gap-10 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,0.9fr)] lg:gap-12">
                <figure data-reveal class="min-w-0">
                    <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-4">
                        <div class="flex flex-wrap gap-x-5 gap-y-2" role="group" aria-label="{{ __('public.home.map_metric_label') }}">
                            <template x-for="(name, key) in metrics" :key="key">
                                <button type="button" @click="metric = key" :aria-pressed="metric === key"
                                    class="cursor-pointer border-b-2 px-0 py-3 text-sm font-medium transition-colors motion-reduce:transition-none"
                                    :class="metric === key ? 'border-leaf text-white' : 'border-transparent text-white/65 hover:text-white'"
                                    x-text="name"></button>
                            </template>
                        </div>
                    </div>

                    @include('public.partials.wilayah-map')
                </figure>

                @include('public.partials.wilayah-detail')
            </div>

        </div>
    </section>

    {{-- Layanan --}}
    <section id="layanan" class="scroll-mt-16 py-24 sm:py-32"
        x-data="{ active: 0, services: @js($services) }">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div data-reveal class="grid gap-6 lg:grid-cols-2 lg:items-end">
                <div>
                    <p class="eyebrow">{{ __('public.home.services_eyebrow') }}</p>
                    <h2 class="section-title">{!! __('public.home.services_title') !!}</h2>
                </div>
            </div>

            <div data-reveal style="--reveal-delay: 120ms"
                class="mt-12 grid gap-6 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:gap-10">
                <div class="grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-1" role="group" aria-label="{{ __('public.home.services_pick') }}">
                    @foreach ($services as $i => $service)
                        <button type="button" @click="active = {{ $i }}" :aria-pressed="active === {{ $i }}"
                            aria-controls="layanan-detail"
                            class="flex min-h-20 w-full cursor-pointer items-center gap-3 border-l-[3px] px-3 py-4 text-left transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-forest-700 motion-reduce:transition-none sm:gap-5 sm:px-5 lg:min-h-20"
                            :class="active === {{ $i }} ? 'border-forest-700 bg-forest-50 text-forest-950' : 'border-forest-900/15 text-ink/70 hover:border-forest-700/50 hover:bg-sand/50 hover:text-forest-950'">
                            <span class="shrink-0 self-start pt-0.5 font-display text-xs font-semibold tabular-nums text-forest-700/70 sm:text-sm" aria-hidden="true">0{{ $i + 1 }}</span>
                            <span class="font-display text-base font-semibold leading-tight sm:text-lg">
                                <span class="sm:hidden">{{ $service['short'] }}</span>
                                <span class="hidden sm:inline">{{ $service['title'] }}</span>
                            </span>
                        </button>
                    @endforeach
                </div>

                <div id="layanan-detail" class="flex min-h-80 flex-col rounded-2xl bg-forest-900 p-7 text-white sm:p-10 lg:min-h-full"
                    aria-live="polite">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <p class="font-display text-sm font-semibold tabular-nums text-leaf"
                                x-text="String(active + 1).padStart(2, '0') + ' / 04'">01 / 04</p>
                            <div class="mt-5 flex gap-1.5" aria-hidden="true">
                                @foreach ($services as $i => $service)
                                    <span class="h-1 w-7 rounded-full transition-colors duration-200 motion-reduce:transition-none"
                                        :class="active === {{ $i }} ? 'bg-leaf' : 'bg-white/20'"></span>
                                @endforeach
                            </div>
                        </div>
                        <div class="grid size-24 shrink-0 place-items-center rounded-xl bg-leaf text-5xl text-forest-950 sm:size-28 sm:text-6xl"
                            aria-hidden="true">
                            <i class="bi leading-none" :class="services[active].icon"></i>
                        </div>
                    </div>
                    <div class="mt-8 border-t border-white/20 pt-7 lg:mt-auto">
                        <h3 class="max-w-xl font-display text-3xl font-semibold leading-tight text-balance sm:text-4xl"
                            x-text="services[active].title">{{ $services[0]['title'] }}</h3>
                        <p class="mt-4 max-w-lg leading-relaxed text-white/75"
                            x-text="services[active].text">{{ $services[0]['text'] }}</p>
                        <a href="{{ route('kontak') }}"
                            class="mt-7 inline-flex min-h-11 items-center gap-2 border-b border-leaf pb-0.5 text-sm font-semibold text-leaf transition-colors hover:border-white hover:text-white focus-visible:rounded-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-leaf motion-reduce:transition-none">
                            {{ __('public.home.services_ask') }}
                            <i aria-hidden="true" class="bi bi-arrow-up-right text-base"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
