@extends('public.layouts.app')

@section('nav-overlay', true)

@section('title', 'Berita · ' . config('settings.app_name'))
@section('description', 'Kabar terbaru kegiatan Cabang Dinas Kehutanan Wilayah Trenggalek di Trenggalek, Tulungagung, dan Kediri.')

@php
    $placeholder = asset('images/not-found/image.jpeg');
    $kategoriNama = $kategoriAktif ? optional($kategori->firstWhere('slug', $kategoriAktif))->name : null;
    $difilter = $cari !== '' || $kategoriAktif;
    // The first post on the unfiltered first page is shown as a large lead story.
    $sorotan = ! $difilter && $posts->onFirstPage() ? $posts->first() : null;
    $daftar = $sorotan ? $posts->getCollection()->slice(1) : $posts->getCollection();
@endphp

@section('content')
    {{-- Page header --}}
    <section class="relative isolate overflow-hidden bg-forest-950 text-white">
        <img src="{{ asset('images/background/bg.avif') }}" alt="" fetchpriority="high"
            class="absolute inset-0 -z-20 size-full object-cover">
        <div class="absolute inset-0 -z-10 bg-forest-950/85 lg:bg-transparent lg:bg-gradient-to-r lg:from-forest-950 lg:via-forest-950/90 lg:to-forest-950/40"></div>

        <div class="mx-auto max-w-7xl px-5 pt-32 pb-14 sm:px-8 lg:pt-40 lg:pb-16">
            <nav data-reveal aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-white/75">
                    <li><a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 py-1 hover:text-white">
                            <i aria-hidden="true" class="bi bi-house-door"></i> Beranda</a></li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                    <li aria-current="page" class="font-semibold text-white">Berita</li>
                </ol>
            </nav>

            <div class="mt-6 grid gap-8 lg:grid-cols-12 lg:items-end">
                <div data-reveal style="--reveal-delay: 80ms" class="lg:col-span-7">
                    <h1 class="font-display text-[2.4rem] leading-[1.04] font-bold tracking-[-0.035em] text-balance sm:text-6xl">
                        Kabar dari <span class="text-leaf">lapangan</span>
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-relaxed text-white/85 sm:text-lg">
                        Kegiatan, program, dan informasi terbaru Cabang Dinas Kehutanan Wilayah Trenggalek.
                    </p>
                </div>

                <form data-reveal style="--reveal-delay: 160ms" action="{{ route('berita') }}" method="get" role="search"
                    class="lg:col-span-5">
                    @if ($kategoriAktif)
                        <input type="hidden" name="kategori" value="{{ $kategoriAktif }}">
                    @endif
                    <label for="cari-berita" class="text-sm font-semibold">Cari berita</label>
                    <div class="mt-2 flex rounded-full bg-white p-1.5 shadow-xl shadow-black/20 focus-within:ring-[3px] focus-within:ring-leaf">

                        <input id="cari-berita" type="search" name="search" value="{{ $cari }}" placeholder="Contoh: perhutanan sosial"
                            class="min-w-0 flex-1 rounded-full bg-transparent px-4 text-ink placeholder:text-ink/50 focus:outline-none">
                        <button type="submit" class="btn-primary !px-5 !py-2.5">
                            <i aria-hidden="true" class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- Category filter --}}
    <div class="sticky top-16 z-30 border-b border-forest-900/10 bg-cream/90 backdrop-blur-xl">
        <nav aria-label="Kategori berita" class="mx-auto max-w-7xl px-5 sm:px-8">
            <ul class="flex gap-2 overflow-x-auto py-3 [scrollbar-width:none]">
                <li class="shrink-0">
                    <a href="{{ route('berita', array_filter(['search' => $cari])) }}" @if (! $kategoriAktif) aria-current="page" @endif
                        @class([
                            'inline-flex min-h-10 items-center rounded-full px-4 text-sm font-semibold whitespace-nowrap transition',
                            'bg-forest-900 text-white' => ! $kategoriAktif,
                            'text-ink/75 ring-1 ring-forest-900/15 hover:bg-forest-50' => $kategoriAktif,
                        ])>Semua</a>
                </li>
                @foreach ($kategori as $item)
                    @php($aktif = $kategoriAktif === $item->slug)
                    <li class="shrink-0">
                        <a href="{{ route('berita', array_filter(['kategori' => $item->slug, 'search' => $cari])) }}" @if ($aktif) aria-current="page" @endif
                            @class([
                                'inline-flex min-h-10 items-center gap-2 rounded-full px-4 text-sm font-semibold whitespace-nowrap transition',
                                'bg-forest-900 text-white' => $aktif,
                                'text-ink/75 ring-1 ring-forest-900/15 hover:bg-forest-50' => ! $aktif,
                            ])>
                            {{ $item->name }}
                            <span @class(['rounded-full px-1.5 text-xs tabular-nums', 'bg-white/20' => $aktif, 'bg-forest-900/5' => ! $aktif])>{{ $item->posts_count }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>

    <section class="mx-auto max-w-7xl px-5 py-14 sm:px-8 sm:py-20" aria-labelledby="judul-daftar">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 id="judul-daftar" class="font-display text-2xl font-semibold sm:text-3xl">
                    @if ($cari !== '')
                        Hasil pencarian “{{ $cari }}”
                    @elseif ($kategoriNama)
                        {{ $kategoriNama }}
                    @else
                        Semua berita
                    @endif
                </h2>
                <p class="mt-1 text-sm text-ink/70" role="status">{{ number_format($posts->total(), 0, ',', '.') }} berita
                    {{ $kategoriNama && $cari !== '' ? 'di kategori ' . $kategoriNama : '' }}</p>
            </div>
            @if ($difilter)
                <a href="{{ route('berita') }}" class="inline-flex min-h-10 items-center gap-2 text-sm font-semibold text-forest-800">
                    <i aria-hidden="true" class="bi bi-x-circle"></i> Hapus filter
                </a>
            @endif
        </div>

        @if ($posts->isEmpty())
            <div class="mt-10 rounded-2xl border border-dashed border-forest-900/20 p-12 text-center">
                <i aria-hidden="true" class="bi bi-newspaper text-4xl text-forest-700"></i>
                <p class="mt-3 font-semibold">Berita tidak ditemukan</p>
                <p class="mt-1 text-ink/70">Coba kata kunci lain atau lihat semua berita.</p>
            </div>
        @else
            @if ($sorotan)
                <article data-reveal class="group relative mt-10 grid overflow-hidden rounded-2xl bg-forest-900 text-white lg:grid-cols-2">
                    <img src="{{ $sorotan->featured_image ?? $placeholder }}"
                        @if ($srcset = image_srcset($sorotan->featured_image)) srcset="{{ $srcset }}" sizes="(min-width: 1024px) 50vw, 100vw" @endif
                        alt="" class="aspect-[16/10] size-full object-cover transition duration-700 ease-soft group-hover:scale-[1.03] lg:aspect-auto lg:min-h-96">
                    <div class="flex flex-col justify-center p-7 sm:p-10">
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            <span class="rounded-full bg-leaf px-3 py-1 text-xs font-semibold text-forest-950">Terbaru</span>
                            @if ($sorotan->categories->first())
                                <span class="text-white/75">{{ $sorotan->categories->first()->name }}</span>
                            @endif
                        </div>
                        <h3 class="mt-4 font-display text-3xl leading-tight font-semibold text-balance sm:text-4xl">
                            <a href="{{ route('berita.show', $sorotan->slug) }}" class="after:absolute after:inset-0">{{ $sorotan->title }}</a>
                        </h3>
                        <p class="mt-4 line-clamp-3 text-white/80">{{ Str::limit(strip_tags($sorotan->excerpt), 220) }}</p>
                        <p class="mt-6 flex items-center gap-2 text-sm text-white/75">
                            <i aria-hidden="true" class="bi bi-calendar3"></i>
                            <time datetime="{{ $sorotan->published_at?->toDateString() }}">{{ $sorotan->published_at?->translatedFormat('d F Y') }}</time>
                        </p>
                    </div>
                </article>
            @endif

            <ul class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($daftar as $post)
                    <li data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 80 }}ms">
                        <article class="group relative flex h-full flex-col overflow-hidden rounded-2xl bg-white/70 ring-1 ring-forest-900/10 transition duration-500 ease-soft hover:-translate-y-1 hover:shadow-xl hover:shadow-forest-950/10">
                            <img src="{{ $post->featured_image ?? $placeholder }}"
                                @if ($srcset = image_srcset($post->featured_image)) srcset="{{ $srcset }}" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 50vw, 100vw" @endif
                                alt="" loading="lazy" class="aspect-[16/10] w-full object-cover">
                            <div class="flex flex-1 flex-col p-6">
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs font-medium">
                                    @if ($post->categories->first())
                                        <span class="text-forest-700">{{ $post->categories->first()->name }}</span>
                                        <span aria-hidden="true" class="text-ink/30">•</span>
                                    @endif
                                    <time class="text-ink/70" datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('d F Y') }}</time>
                                </div>
                                <h3 class="mt-3 font-display text-xl leading-snug font-semibold transition group-hover:text-forest-700">
                                    <a href="{{ route('berita.show', $post->slug) }}" class="after:absolute after:inset-0">{{ $post->title }}</a>
                                </h3>
                                <p class="mt-3 line-clamp-3 text-sm leading-relaxed text-ink/75">{{ Str::limit(strip_tags($post->excerpt), 160) }}</p>
                                <span class="mt-auto inline-flex items-center gap-1 pt-5 text-sm font-semibold text-forest-800" aria-hidden="true">
                                    Baca selengkapnya <i class="bi bi-arrow-right transition group-hover:translate-x-1"></i>
                                </span>
                            </div>
                        </article>
                    </li>
                @endforeach
            </ul>

            <div class="mt-14">
                {{ $posts->links('public.partials.pagination') }}
            </div>
        @endif
    </section>
@endsection
