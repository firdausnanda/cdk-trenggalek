@extends('public.layouts.app')

@section('title', judul_halaman($post->seo('title') ?: $post->title))
@section('description', $post->seo('description') ?: deskripsi_meta($post->excerpt ?: $post->content))
@if ($post->seo('noindex'))
    @section('robots', 'noindex, follow')
@endif
@section('canonical', route('berita.show', $post->slug))
@section('og_type', 'article')
@if ($post->featured_image)
    @section('og_image', url($post->featured_image))
@endif

@push('head')
    @if ($post->published_at)
        <meta property="article:published_time" content="{{ $post->published_at->toIso8601String() }}">
    @endif
    <meta property="article:modified_time" content="{{ $post->updated_at->toIso8601String() }}">
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(
        \App\Support\StructuredData::newsArticle($post),
        \App\Support\StructuredData::breadcrumb([
            [__('public.nav.home'), route('home')],
            [__('public.nav.news'), route('berita')],
            [$post->title, route('berita.show', $post->slug)],
        ]),
    ) !!}</script>
@endpush

@php
    $placeholder = asset('images/not-found/image.jpeg');
    $menitBaca = max(1, (int) ceil(str_word_count(strip_tags($post->content ?? '')) / 200));
    $dilihat = $post->views()->count();
    $bagikan = [
        'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'bi-whatsapp'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'bi-facebook'],
        'twitter' => ['label' => 'X', 'icon' => 'bi-twitter-x'],
        'telegram' => ['label' => 'Telegram', 'icon' => 'bi-telegram'],
    ];
    $jumlahKomentar = $comments->where('status', 'published')->count();
@endphp

@section('content')
    <header class="mx-auto max-w-5xl px-5 pt-28 sm:px-8 sm:pt-32">
        <div class="mx-auto max-w-4xl text-center">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center justify-center gap-x-2 text-sm text-ink/60">
                    <li><a href="{{ route('home') }}" class="inline-flex min-h-11 items-center hover:text-forest-800">{{ __('public.nav.home') }}</a></li>
                    <li class="inline-flex items-center gap-2"><i aria-hidden="true" class="bi bi-chevron-right text-xs"></i><a href="{{ route('berita') }}" class="inline-flex min-h-11 items-center hover:text-forest-800">{{ __('public.nav.news') }}</a></li>
                    @foreach ($post->categories->take(1) as $item)
                        <li class="inline-flex items-center gap-2"><i aria-hidden="true" class="bi bi-chevron-right text-xs"></i><a href="{{ route('berita.kategori', $item->slug) }}" class="inline-flex min-h-11 items-center font-semibold text-forest-700 hover:text-forest-800">{{ $item->name }}</a></li>
                    @endforeach
                </ol>
            </nav>

            <h1 class="mt-3 font-display text-[1.75rem] leading-[1.25] font-bold tracking-[-0.02em] text-balance text-ink sm:text-3xl lg:text-[2.25rem] lg:leading-[1.2]">{{ $post->title }}</h1>
        </div>

        <div class="mt-8 flex flex-col gap-4 border-y border-forest-900/10 py-4 sm:mt-10 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ config('settings.site_icon') }}" alt="" width="40" height="40" class="size-10 shrink-0 rounded-full bg-white object-contain p-1 ring-1 ring-forest-900/10">
                <div class="leading-snug">
                    <p class="text-sm font-semibold text-ink">{{ config('cdk.nama_singkat') ?: __('public.common.org_line1') }}</p>
                    <p class="text-xs text-ink/60">
                        <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('l, d F Y') }}</time>
                    </p>
                </div>
            </div>
            <ul class="flex flex-wrap items-center gap-1.5 text-xs font-medium text-ink/70 sm:gap-2">
                <li class="inline-flex h-8 items-center gap-1.5 rounded-full bg-white px-2.5 ring-1 sm:h-9 sm:px-3.5 ring-forest-900/10" title="{{ __('public.news.reading_time', ['minutes' => $menitBaca]) }}">
                    <i aria-hidden="true" class="bi bi-clock"></i> {{ __('public.news.reading_time', ['minutes' => $menitBaca]) }}
                </li>
                <li class="inline-flex h-8 items-center gap-1.5 rounded-full bg-white px-2.5 ring-1 sm:h-9 sm:px-3.5 ring-forest-900/10">
                    <i aria-hidden="true" class="bi bi-eye"></i> {{ trans_choice('public.news.views', $dilihat, ['count' => angka($dilihat)]) }}
                </li>
                <li>
                    <a href="#komentar" class="inline-flex h-8 items-center gap-1.5 rounded-full bg-white px-2.5 ring-1 sm:h-9 sm:px-3.5 ring-forest-900/10 transition hover:text-forest-800 hover:ring-forest-700/40">
                        <i aria-hidden="true" class="bi bi-chat-left-text"></i> {{ $jumlahKomentar }}<span class="sr-only"> {{ __('public.news.comments') }}</span>
                    </a>
                </li>
                <li>
                    <a href="#bagikan" class="grid size-8 place-items-center rounded-full bg-white ring-1 sm:size-9 ring-forest-900/10 transition hover:text-forest-800 hover:ring-forest-700/40">
                        <i aria-hidden="true" class="bi bi-share"></i><span class="sr-only">{{ __('public.news.share_short') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    </header>

    <figure class="mx-auto mt-6 max-w-5xl px-5 sm:mt-8 sm:px-8">
        {{-- Uploaded images vary in size, so the image keeps its own aspect ratio (no letterbox);
             tall portraits are capped to the viewport height and centred. --}}
        <img src="{{ $post->featured_image ?? $placeholder }}" fetchpriority="high"
            @if ($srcset = image_srcset($post->featured_image)) srcset="{{ $srcset }}" sizes="(min-width: 1024px) 960px, 100vw" @endif
            alt="{{ $post->title }}"
            class="mx-auto block h-auto max-h-[80vh] w-auto max-w-full rounded-2xl shadow-[0_20px_50px_rgb(22_32_26/0.14)] ring-1 ring-forest-900/10">

    </figure>

    <div x-data="{ persen: 0, tampil: false }" aria-hidden="true"
        x-init="const update = () => { const r = document.getElementById('isi-berita').getBoundingClientRect(); const total = r.height - innerHeight * 0.6; persen = Math.round(Math.min(100, Math.max(0, (innerHeight * 0.4 - r.top) / Math.max(total, 1) * 100))); tampil = r.top < innerHeight * 0.5 && r.bottom > innerHeight * 0.5; }; update(); addEventListener('scroll', update, { passive: true }); addEventListener('resize', update)"
        x-cloak x-show="tampil" class="fixed inset-x-0 top-16 z-40 h-1 bg-forest-100">
        <span class="block h-full bg-forest-700" :style="`width: ${persen}%`"></span>
    </div>

    <article class="mx-auto max-w-5xl px-5 pt-12 pb-20 sm:px-8 sm:pt-16">
            <div id="isi-berita" class="konten-berita">
                {!! lazy_images($post->content, __('public.news.image_alt', ['title' => $post->title])) !!}
            </div>

        @if ($post->tags->isNotEmpty())
            <div class="mt-12 flex flex-wrap items-center gap-2">
                <span class="mr-1 text-sm font-semibold text-forest-800">{{ __('public.news.topics') }}</span>
                @foreach ($post->tags as $tag)
                    <a href="{{ route('berita.tag', $tag->slug) }}" class="inline-flex min-h-11 items-center border-b border-forest-900/25 text-sm text-ink/80 hover:border-forest-700 hover:text-forest-800">#{{ $tag->name }}</a>
                @endforeach
            </div>
        @endif

        <div id="bagikan" class="mt-12 flex scroll-mt-24 flex-col gap-5 border-y border-forest-900/20 py-7 sm:flex-row sm:items-center sm:justify-between"
            x-data="{ disalin: false, salin() { navigator.clipboard?.writeText(location.href).then(() => { this.disalin = true; setTimeout(() => this.disalin = false, 2500) }) } }">
            <div>
                <h2 class="font-display text-lg font-semibold">{{ __('public.news.share_title') }}</h2>
                <p class="mt-1 text-sm text-ink/70">{{ __('public.news.share_text') }}</p>
            </div>
            <ul class="flex flex-wrap gap-2">
                @foreach ($bagikan as $kunci => $item)
                    @continue(empty($share[$kunci]))
                    <li>
                        <a href="{{ $share[$kunci] }}" target="_blank" rel="noopener noreferrer"
                            class="grid size-11 place-items-center rounded-lg border border-forest-900/20 bg-white text-lg text-forest-800 transition hover:border-forest-900 hover:bg-forest-900 hover:text-white active:scale-95">
                            <i aria-hidden="true" class="bi {{ $item['icon'] }}"></i>
                            <span class="sr-only">{{ __('public.news.share_to', ['network' => $item['label']]) }} ({{ __('public.common.new_tab') }})</span>
                        </a>
                    </li>
                @endforeach
                <li>
                    <button type="button" @click="salin()"
                        class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-forest-900/20 bg-white px-4 text-sm font-semibold text-forest-800 transition hover:border-forest-900 hover:bg-forest-900 hover:text-white active:scale-95">
                        <i aria-hidden="true" class="bi" :class="disalin ? 'bi-check2' : 'bi-link-45deg'"></i>
                        <span x-text="disalin ? @js(__('public.news.link_copied')) : @js(__('public.news.copy_link'))">{{ __('public.news.copy_link') }}</span>
                    </button>
                    <span class="sr-only" role="status" x-text="disalin ? @js(__('public.news.link_copied_sr')) : ''"></span>
                </li>
            </ul>
        </div>

        @include('public.partials.comment-thread')
    </article>

    @if ($posts->isNotEmpty())
        <section class="border-t border-forest-900/10 bg-sand/45 py-16 sm:py-20" aria-labelledby="judul-lainnya">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-forest-700">{{ __('public.news.read_next') }}</p>
                        <h2 id="judul-lainnya" class="mt-2 font-display text-3xl font-semibold">{{ __('public.news.other_news') }}</h2>
                    </div>
                    <a href="{{ route('berita') }}" class="inline-flex min-h-11 items-center gap-2 font-semibold text-forest-800 hover:underline hover:underline-offset-4">{{ __('public.news.see_all') }} <i aria-hidden="true" class="bi bi-arrow-right"></i></a>
                </div>
                <ul class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $item)
                        <li>
                            <article class="group relative flex h-full flex-col overflow-hidden rounded-lg border border-forest-900/10 bg-cream transition-colors hover:border-forest-700/50">
                                <img src="{{ $item->featured_image ?? $placeholder }}"
                                    @if ($srcset = image_srcset($item->featured_image)) srcset="{{ $srcset }}" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" @endif
                                    alt="" loading="lazy" width="640" height="400" class="aspect-[16/10] w-full object-cover">
                                <div class="flex flex-1 flex-col p-5 sm:p-6">
                                    <time class="text-sm font-medium text-ink/70" datetime="{{ $item->published_at?->toDateString() }}">{{ $item->published_at?->translatedFormat('d F Y') }}</time>
                                    <h3 class="mt-3 font-display text-xl leading-snug font-semibold transition-colors group-hover:text-forest-700">
                                        <a href="{{ route('berita.show', $item->slug) }}" class="after:absolute after:inset-0">{{ $item->title }}</a>
                                    </h3>
                                    <span class="mt-auto inline-flex items-center gap-2 pt-6 text-sm font-semibold text-forest-800" aria-hidden="true">{{ __('public.news.read_article') }} <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </article>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
@endsection
