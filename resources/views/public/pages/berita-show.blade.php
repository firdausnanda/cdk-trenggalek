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
    $ringkasan = trim(strip_tags($post->excerpt ?? ''));
    if ($ringkasan === '' || Str::endsWith($ringkasan, ['...', '…'])) {
        $teksAwal = trim(preg_replace('/\s+/u', ' ', strip_tags($post->content ?? '')));
        if (preg_match('/^(.+?[.!?])(?:\s|$)/u', $teksAwal, $kalimat)) {
            $ringkasan = $kalimat[1];
        }
    }
    // Keep the lead under the title short; long excerpts are cut at a word boundary.
    $ringkasan = Str::limit($ringkasan, 160, '…', preserveWords: true);
    $bagikan = [
        'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'bi-whatsapp'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'bi-facebook'],
        'twitter' => ['label' => 'X', 'icon' => 'bi-twitter-x'],
        'telegram' => ['label' => 'Telegram', 'icon' => 'bi-telegram'],
    ];
    $jumlahKomentar = $comments->where('status', 'published')->count();
@endphp

@section('content')
    <header class="relative overflow-hidden bg-gradient-to-b from-forest-50 via-forest-50/60 to-cream">
        <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-full bg-[radial-gradient(ellipse_at_top,rgb(181_216_106/0.22),transparent_60%)]"></div>
        <div class="relative mx-auto max-w-4xl px-5 pt-28 pb-12 text-center sm:px-8 sm:pt-32 sm:pb-16">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center justify-center gap-x-2 text-sm text-ink/60">
                    <li><a href="{{ route('home') }}" class="inline-flex min-h-11 items-center hover:text-forest-800">{{ __('public.nav.home') }}</a></li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                    <li><a href="{{ route('berita') }}" class="inline-flex min-h-11 items-center hover:text-forest-800">{{ __('public.nav.news') }}</a></li>
                    <li aria-hidden="true" class="hidden sm:block"><i class="bi bi-chevron-right text-xs"></i></li>
                    <li aria-current="page" class="hidden max-w-xs truncate text-ink/80 sm:block">{{ Str::limit($post->title, 50) }}</li>
                </ol>
            </nav>

            @if ($post->categories->isNotEmpty())
                <ul class="mt-4 flex flex-wrap justify-center gap-2">
                    @foreach ($post->categories as $item)
                        <li><a href="{{ route('berita.kategori', $item->slug) }}" class="inline-flex min-h-9 items-center rounded-full bg-forest-700/10 px-4 text-sm font-semibold text-forest-800 transition hover:bg-forest-700 hover:text-white">{{ $item->name }}</a></li>
                    @endforeach
                </ul>
            @endif

            <h1 class="mt-5 font-display text-[clamp(2.1rem,4.6vw,3.6rem)] leading-[1.1] font-extrabold tracking-[-0.03em] text-balance text-ink">{{ $post->title }}</h1>

            <div class="mt-7 flex flex-wrap items-center justify-center gap-x-5 gap-y-3 text-sm">
                <div class="flex items-center gap-3 text-left">
                    <img src="{{ config('settings.site_icon') }}" alt="" width="44" height="44" class="size-11 rounded-full bg-white object-contain p-1 shadow-sm ring-1 ring-forest-900/10">
                    <div class="leading-snug">
                        <p class="font-semibold text-ink">{{ __('public.common.org_line1') }}</p>
                        <p class="text-ink/65">
                            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('d F Y') }}</time>
                        </p>
                    </div>
                </div>
                <span aria-hidden="true" class="hidden h-8 w-px bg-forest-900/15 sm:block"></span>
                <ul class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-ink/70">
                    <li class="inline-flex items-center gap-1.5"><i aria-hidden="true" class="bi bi-clock"></i> {{ __('public.news.reading_time', ['minutes' => $menitBaca]) }}</li>
                    <li class="inline-flex items-center gap-1.5"><i aria-hidden="true" class="bi bi-eye"></i> {{ trans_choice('public.news.views', $dilihat, ['count' => angka($dilihat)]) }}</li>
                    <li><a href="#komentar" class="inline-flex min-h-11 items-center gap-1.5 hover:text-forest-800 hover:underline hover:underline-offset-4"><i aria-hidden="true" class="bi bi-chat-left-text"></i> {{ __('public.news.comments') }} ({{ $jumlahKomentar }})</a></li>
                </ul>
            </div>

            @if ($ringkasan !== '')
                <p class="mx-auto mt-7 max-w-2xl text-lg leading-relaxed text-ink/75 sm:text-xl">{{ $ringkasan }}</p>
            @endif
        </div>
    </header>

    <figure class="mx-auto max-w-5xl px-5 sm:px-8">
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
