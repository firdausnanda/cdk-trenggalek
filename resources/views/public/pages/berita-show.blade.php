@extends('public.layouts.app')

@section('nav-overlay', true)
@section('title', $post->title . ' · ' . config('settings.app_name'))
@section('description', Str::limit(trim(strip_tags($post->excerpt ?: $post->content)), 160))

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
    $bagikan = [
        'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'bi-whatsapp'],
        'facebook' => ['label' => 'Facebook', 'icon' => 'bi-facebook'],
        'twitter' => ['label' => 'X', 'icon' => 'bi-twitter-x'],
        'telegram' => ['label' => 'Telegram', 'icon' => 'bi-telegram'],
    ];
    $field = 'w-full rounded-lg border border-forest-900/20 bg-white px-4 py-3 text-ink placeholder:text-ink/50 focus:border-forest-700 focus:ring-[3px] focus:ring-forest-700/20 focus:outline-none';
@endphp

@section('content')
    <header class="bg-forest-950 text-white">
        <div class="mx-auto max-w-7xl px-5 pt-32 pb-14 sm:px-8 sm:pt-36 sm:pb-16 lg:pt-24 lg:pb-10">
            <nav aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-white/70">
                    <li><a href="{{ route('home') }}" class="inline-flex min-h-11 items-center hover:text-white">Beranda</a></li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                    <li><a href="{{ route('berita') }}" class="inline-flex min-h-11 items-center hover:text-white">Berita</a></li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                    <li aria-current="page" class="text-white">Detail berita</li>
                </ol>
            </nav>

            <div class="mt-6 lg:mt-4 lg:grid lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] lg:items-center lg:gap-10 xl:gap-12">
                <div class="min-w-0">
                    @if ($post->categories->isNotEmpty())
                        <ul class="flex flex-wrap gap-x-3 gap-y-1 text-sm font-semibold text-leaf">
                            @foreach ($post->categories as $item)
                                <li><a href="{{ route('berita', ['kategori' => $item->slug]) }}" class="inline-flex min-h-11 items-center underline-offset-4 hover:underline">{{ $item->name }}</a></li>
                            @endforeach
                        </ul>
                    @endif

                    <h1 class="mt-3 font-display text-[clamp(2.35rem,5vw,4.75rem)] leading-[1.08] font-bold tracking-[-0.035em] text-balance lg:text-[clamp(2.6rem,3.4vw,3.3rem)]">{{ $post->title }}</h1>
                    @if ($ringkasan !== '')
                        <p class="mt-5 max-w-2xl text-base leading-relaxed text-white/80 lg:text-lg">{{ $ringkasan }}</p>
                    @endif

                    <div class="mt-7 flex flex-wrap items-center gap-x-6 gap-y-4 border-t border-white/20 pt-5 text-sm">
                        <div class="flex items-center gap-3">
                            <img src="{{ config('settings.site_icon') }}" alt="" width="44" height="44" class="size-11 rounded-full bg-white object-contain p-1">
                            <div class="leading-snug">
                                <p class="font-semibold text-white">Cabang Dinas Kehutanan</p>
                                <p class="text-white/70">Wilayah Trenggalek</p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-white/75">
                            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('d F Y') }}</time>
                            <span aria-hidden="true" class="hidden size-1 rounded-full bg-leaf sm:block"></span>
                            <span>{{ $menitBaca }} menit baca</span>
                            <span aria-hidden="true" class="hidden size-1 rounded-full bg-leaf sm:block"></span>
                            <span>{{ number_format($dilihat, 0, ',', '.') }} kali dibaca</span>
                        </div>
                    </div>
                </div>

                <figure class="relative -mb-28 mt-9 lg:mb-0 lg:mt-0">
                    <div class="overflow-hidden rounded-lg border border-white/15 bg-forest-900 shadow-[0_16px_45px_rgb(0_0_0/0.12)] lg:rounded-none lg:border-0 lg:bg-cream lg:p-2 lg:shadow-[0_20px_45px_rgb(0_0_0/0.22)]">
                        <img src="{{ $post->featured_image ?? $placeholder }}" fetchpriority="high" width="1280" height="720"
                            @if ($srcset = image_srcset($post->featured_image)) srcset="{{ $srcset }}" sizes="(min-width: 1280px) 480px, (min-width: 1024px) 40vw, 100vw" @endif
                            alt="{{ $post->title }}" class="mx-auto aspect-[2/1] w-full object-contain lg:aspect-[13/6] lg:bg-cream">
                    </div>
                </figure>
            </div>
        </div>
    </header>

    <div x-data="{ persen: 0, tampil: false }" aria-hidden="true"
        x-init="const update = () => { const r = document.getElementById('isi-berita').getBoundingClientRect(); const total = r.height - innerHeight * 0.6; persen = Math.round(Math.min(100, Math.max(0, (innerHeight * 0.4 - r.top) / Math.max(total, 1) * 100))); tampil = r.top < innerHeight * 0.5 && r.bottom > innerHeight * 0.5; }; update(); addEventListener('scroll', update, { passive: true }); addEventListener('resize', update)"
        x-cloak x-show="tampil" class="fixed inset-x-0 top-16 z-40 h-1 bg-forest-100">
        <span class="block h-full bg-forest-700" :style="`width: ${persen}%`"></span>
    </div>

    <article class="mx-auto grid max-w-7xl gap-10 px-5 pt-14 pb-20 sm:px-8 sm:pt-20 lg:grid-cols-[11rem_minmax(0,45rem)_1fr] lg:gap-14">
        <aside class="hidden lg:block">
            <div class="sticky top-28 border-t-2 border-forest-700 pt-4 text-sm">
                <p class="font-semibold text-forest-800">Tentang berita ini</p>
                <p class="mt-3 text-ink/70">{{ $menitBaca }} menit baca</p>
                <a href="#bagikan" class="mt-4 inline-flex min-h-11 items-center gap-2 font-semibold text-forest-800 hover:underline hover:underline-offset-4">
                    <i aria-hidden="true" class="bi bi-share"></i> Bagikan berita
                </a>
                <a href="#komentar" class="mt-1 inline-flex min-h-11 items-center gap-2 font-semibold text-forest-800 hover:underline hover:underline-offset-4">
                    <i aria-hidden="true" class="bi bi-chat-left-text"></i> Komentar ({{ $comment->count() }})
                </a>
            </div>
        </aside>

        <div class="mx-auto w-full min-w-0 max-w-[45rem]">
            <div id="isi-berita" class="konten-berita">
                {!! lazy_images($post->content) !!}
            </div>

        @if ($post->tags->isNotEmpty())
            <div class="mt-12 flex flex-wrap items-center gap-2">
                <span class="mr-1 text-sm font-semibold text-forest-800">Topik</span>
                @foreach ($post->tags as $tag)
                    <span class="border-b border-forest-900/25 py-1 text-sm text-ink/80">#{{ $tag->name }}</span>
                @endforeach
            </div>
        @endif

        <div id="bagikan" class="mt-12 flex scroll-mt-24 flex-col gap-5 border-y border-forest-900/20 py-7 sm:flex-row sm:items-center sm:justify-between"
            x-data="{ disalin: false, salin() { navigator.clipboard?.writeText(location.href).then(() => { this.disalin = true; setTimeout(() => this.disalin = false, 2500) }) } }">
            <div>
                <h2 class="font-display text-lg font-semibold">Bagikan berita ini</h2>
                <p class="mt-1 text-sm text-ink/70">Kirim ke rekan atau simpan tautannya.</p>
            </div>
            <ul class="flex flex-wrap gap-2">
                @foreach ($bagikan as $kunci => $item)
                    @continue(empty($share[$kunci]))
                    <li>
                        <a href="{{ $share[$kunci] }}" target="_blank" rel="noopener noreferrer"
                            class="grid size-11 place-items-center rounded-lg border border-forest-900/20 bg-white text-lg text-forest-800 transition hover:border-forest-900 hover:bg-forest-900 hover:text-white active:scale-95">
                            <i aria-hidden="true" class="bi {{ $item['icon'] }}"></i>
                            <span class="sr-only">Bagikan ke {{ $item['label'] }} (membuka tab baru)</span>
                        </a>
                    </li>
                @endforeach
                <li>
                    <button type="button" @click="salin()"
                        class="inline-flex min-h-11 items-center gap-2 rounded-lg border border-forest-900/20 bg-white px-4 text-sm font-semibold text-forest-800 transition hover:border-forest-900 hover:bg-forest-900 hover:text-white active:scale-95">
                        <i aria-hidden="true" class="bi" :class="disalin ? 'bi-check2' : 'bi-link-45deg'"></i>
                        <span x-text="disalin ? 'Tautan disalin' : 'Salin tautan'">Salin tautan</span>
                    </button>
                    <span class="sr-only" role="status" x-text="disalin ? 'Tautan berita disalin' : ''"></span>
                </li>
            </ul>
        </div>

        <section id="komentar" class="mt-16 scroll-mt-24" aria-labelledby="judul-komentar">
            <div class="border-b border-forest-900/20 pb-5">
                <h2 id="judul-komentar" class="font-display text-2xl font-semibold sm:text-3xl">Komentar <span class="text-forest-700">{{ $comment->count() }}</span></h2>
                <p class="mt-2 text-sm text-ink/70">Tanggapan pembaca terhadap berita ini.</p>
            </div>

            @if ($comment->isEmpty())
                <p class="mt-4 text-ink/70">Belum ada komentar. Jadilah yang pertama menanggapi berita ini.</p>
            @else
                <ul class="divide-y divide-forest-900/15">
                    @foreach ($comment as $c)
                        <li class="flex gap-4 py-6">
                            <span aria-hidden="true" class="grid size-11 shrink-0 place-items-center rounded-lg bg-forest-900 font-semibold text-leaf">
                                {{ Str::upper(Str::substr(trim($c->name), 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <p class="font-semibold">{{ Str::ucfirst($c->name) }}</p>
                                <p class="text-xs text-ink/70">
                                    <time datetime="{{ $c->created_at->toIso8601String() }}">{{ $c->created_at->translatedFormat('d F Y, H:i') }}</time>
                                </p>
                                <p class="mt-2 leading-relaxed text-ink/85">{!! komentar_teks($c->content) !!}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            <div id="comment-form" class="mt-10 scroll-mt-24 rounded-lg border border-forest-900/15 bg-white/70 p-6 sm:p-8">
                <h3 class="font-display text-2xl font-semibold">Tulis komentar</h3>

                @if (session('success'))
                    <p role="status" class="mt-4 flex items-start gap-3 rounded-xl bg-forest-900 px-4 py-3 text-sm text-white">
                        <i aria-hidden="true" class="bi bi-check-circle-fill mt-0.5 text-leaf"></i> {{ session('success') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('comments.store') }}" class="mt-6 grid gap-5 sm:grid-cols-2" novalidate>
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">

                    <div>
                        <label for="komentar-nama" class="text-sm font-semibold">Nama <span aria-hidden="true" class="text-clay">*</span></label>
                        <input id="komentar-nama" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                            @error('name') aria-invalid="true" aria-describedby="galat-nama" @enderror class="mt-2 {{ $field }}">
                        @error('name')<p id="galat-nama" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="komentar-email" class="text-sm font-semibold">Email <span aria-hidden="true" class="text-clay">*</span></label>
                        <input id="komentar-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                            aria-describedby="catatan-email @error('email') galat-email @enderror" @error('email') aria-invalid="true" @enderror
                            class="mt-2 {{ $field }}">
                        <p id="catatan-email" class="mt-1.5 text-xs text-ink/70">Tidak ditampilkan.</p>
                        @error('email')<p id="galat-email" class="mt-1 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="komentar-isi" class="text-sm font-semibold">Komentar <span aria-hidden="true" class="text-clay">*</span></label>
                        <textarea id="komentar-isi" name="comment" rows="5" maxlength="1000" required
                            @error('comment') aria-invalid="true" aria-describedby="galat-komentar" @enderror
                            class="mt-2 {{ $field }}">{{ old('comment') }}</textarea>
                        @error('comment')<p id="galat-komentar" class="mt-1.5 text-sm font-medium text-red-700">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col gap-4 sm:col-span-2 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-sm text-ink/70">
                            Komentar ditampilkan setelah disetujui admin. Nama dan komentar Anda akan terlihat publik.
                            <a href="{{ route('kebijakan-privasi') }}" class="font-semibold text-forest-800 underline underline-offset-4">Kebijakan Privasi</a>
                        </p>
                        <button type="submit" class="btn-primary shrink-0 !bg-forest-900 !text-white hover:!bg-forest-700">
                            Kirim komentar <i aria-hidden="true" class="bi bi-send"></i>
                        </button>
                    </div>
                </form>
            </div>
        </section>
        </div>
    </article>

    @if ($posts->isNotEmpty())
        <section class="border-t border-forest-900/10 bg-sand/45 py-16 sm:py-20" aria-labelledby="judul-lainnya">
            <div class="mx-auto max-w-7xl px-5 sm:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-forest-700">Baca selanjutnya</p>
                        <h2 id="judul-lainnya" class="mt-2 font-display text-3xl font-semibold">Berita lainnya</h2>
                    </div>
                    <a href="{{ route('berita') }}" class="inline-flex min-h-11 items-center gap-2 font-semibold text-forest-800 hover:underline hover:underline-offset-4">Lihat semua berita <i aria-hidden="true" class="bi bi-arrow-right"></i></a>
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
                                    <span class="mt-auto inline-flex items-center gap-2 pt-6 text-sm font-semibold text-forest-800" aria-hidden="true">Baca berita <i class="bi bi-arrow-right"></i></span>
                                </div>
                            </article>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
    @endif
@endsection
