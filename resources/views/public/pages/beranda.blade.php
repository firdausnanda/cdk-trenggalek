@extends('public.layouts.app')

@section('nav-overlay', true)
@section('title', 'Cabang Dinas Kehutanan Wilayah Trenggalek, Jawa Timur')
@section('description', 'Layanan, data kehutanan, dan berita kegiatan Cabang Dinas Kehutanan Wilayah Trenggalek di Kabupaten Trenggalek, Tulungagung, Kediri, dan Kota Kediri.')

@php
    // Forestry figures from SMART-HUT (see App\Services\SmartHutService).
    $regions = $kehutanan['regions'];
    $totals = $kehutanan['totals'];
    $tahun = $kehutanan['tahun'];
    $capaian = $kehutanan['capaian'];
    $sumberData = 'SMART-HUT, Sistem Informasi Data Kehutanan'
        . ($kehutanan['diperbarui'] ? ' · diperbarui ' . $kehutanan['diperbarui']->translatedFormat('d F Y') : '');

    $heroStats = [
        ['value' => $totals['kth'], 'label' => 'Kelompok Tani Hutan dibina'],
        ['value' => $totals['anggota_kth'], 'label' => 'Anggota KTH'],
        ['value' => $totals['ps'], 'label' => 'Kelompok Perhutanan Sosial'],
        ['value' => $totals['nilai_transaksi_ekonomi'], 'label' => 'Nilai transaksi ekonomi ' . $tahun, 'rupiah' => true],
    ];

    $services = [
        ['short' => 'Perizinan', 'icon' => 'bi-file-earmark-check', 'title' => 'Izin Pemanfaatan Hutan', 'text' => 'Pelayanan perizinan pemanfaatan hasil hutan kayu dan bukan kayu sesuai peraturan yang berlaku.'],
        ['short' => 'Rehabilitasi', 'icon' => 'bi-flower2', 'title' => 'Rehabilitasi Kawasan Hutan', 'text' => 'Program rehabilitasi kawasan hutan dan lahan kritis untuk pemulihan ekosistem.'],
        ['short' => 'Pengawasan', 'icon' => 'bi-shield-check', 'title' => 'Pengawasan & Pengendalian', 'text' => 'Pengawasan dan pengendalian terhadap pemanfaatan hutan secara berkelanjutan.'],
        ['short' => 'Pemberdayaan', 'icon' => 'bi-people', 'title' => 'Pemberdayaan Masyarakat', 'text' => 'Program pemberdayaan masyarakat sekitar hutan untuk kesejahteraan bersama.'],
    ];

    // Schematic (not to scale) map of the working areas, viewBox 0 0 480 400. Shared borders are defined
    // once and reused (a leading "-" reverses one) so neighbouring regions line up exactly.
    $borders = [
        'tg_tl' => [[220, 190], [214, 212], [218, 232], [208, 252], [214, 276], [222, 298], [226, 318]],
        'kd_tl' => [[220, 190], [242, 196], [262, 192], [290, 206], [318, 200], [344, 210], [370, 212]],
        'kd_tg' => [[212, 168], [218, 180], [220, 190]],
        'tl_east' => [[370, 212], [382, 236], [374, 262], [380, 288], [362, 306], [350, 326]],
        'tl_coast' => [[226, 318], [252, 326], [280, 322], [312, 330], [350, 326]],
        'tg_coast' => [[70, 322], [98, 316], [128, 326], [160, 320], [194, 328], [226, 318]],
        'tg_west' => [[70, 322], [62, 298], [66, 272], [54, 248], [70, 222], [78, 196], [90, 168]],
        'tg_north' => [[90, 168], [118, 160], [140, 164], [160, 150], [186, 158], [212, 168]],
        'kd_outer' => [[370, 212], [392, 190], [408, 150], [396, 118], [402, 92], [380, 70], [352, 60], [326, 44], [300, 38], [272, 50], [240, 48], [205, 62], [200, 90], [188, 108], [196, 120], [204, 146], [212, 168]],
    ];
    $points = function (array $segments) use ($borders) {
        $points = [];
        foreach ($segments as $segment) {
            $line = $borders[ltrim($segment, '-')];
            $line = str_starts_with($segment, '-') ? array_reverse($line) : $line;
            $points = array_merge($points, $points ? array_slice($line, 1) : $line);
        }

        return implode(' L', array_map(fn ($p) => "{$p[0]} {$p[1]}", $points));
    };
    $mapShapes = [
        'trenggalek' => ['d' => 'M' . $points(['tg_coast', '-tg_tl', '-kd_tg', '-tg_north', '-tg_west']) . ' Z', 'x' => 140, 'y' => 238],
        'tulungagung' => ['d' => 'M' . $points(['kd_tl', 'tl_east', '-tl_coast', '-tg_tl']) . ' Z', 'x' => 298, 'y' => 262],
        'kediri' => ['d' => 'M' . $points(['kd_tl', 'kd_outer', 'kd_tg']) . ' Z', 'x' => 318, 'y' => 128],
    ];
    // Coastline from the west edge to the east edge, passing along both coastal regions.
    $coast = 'M0 330 L40 326 L' . $points(['tg_coast', 'tl_coast']) . ' L392 332 L436 328 L480 336';
    $neighbours = [
        ['name' => 'Ponorogo', 'x' => 100, 'y' => 96],
        ['name' => 'Pacitan', 'x' => 30, 'y' => 300],
        ['name' => 'Nganjuk', 'x' => 250, 'y' => 22],
        ['name' => 'Jombang', 'x' => 420, 'y' => 40],
        ['name' => 'Malang', 'x' => 444, 'y' => 140],
        ['name' => 'Blitar', 'x' => 432, 'y' => 262],
    ];
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
                    Lestari hutan, <em class="text-leaf not-italic">sejahtera</em> masyarakat.
                </h1>
                <p data-reveal style="--reveal-delay: 120ms"
                    class="mt-6 max-w-xl text-base leading-relaxed text-white/80 sm:text-lg">
                    Cabang Dinas Kehutanan Wilayah Trenggalek melindungi, mengelola, dan melestarikan hutan di
                    Wilayah Kabupaten Trenggalek, Kabupaten Tulungagung, Kabupaten Kediri, dan Kota Kediri.
                </p>
                <div data-reveal style="--reveal-delay: 200ms" class="mt-9 flex flex-wrap gap-3">
                    <a href="#layanan" class="btn-primary group">
                        Jelajahi layanan <i aria-hidden="true" class="bi bi-arrow-down transition group-hover:translate-y-0.5"></i>
                    </a>
                    <a href="#wilayah" class="btn-ghost">Lihat wilayah kerja</a>
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
                            {{ ($stat['rupiah'] ?? false) ? rupiah_singkat($stat['value']) : number_format($stat['value'], 0, ',', '.') }}
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
                    <p class="eyebrow">Informasi terbaru</p>
                    <h2 class="section-title">Berita dan <em>kegiatan</em>.</h2>
                </div>
                <a href="{{ route('berita') }}" class="btn-ghost w-fit">Semua berita <i aria-hidden="true" class="bi bi-arrow-right"></i></a>
            </div>

            @if ($posts->isEmpty())
                <div class="mt-12 rounded-3xl border border-dashed border-forest-900/20 p-12 text-center text-ink/60">
                    <i aria-hidden="true" class="bi bi-newspaper text-4xl"></i>
                    <p class="mt-3">Belum ada berita yang diterbitkan.</p>
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

                        <span class="w-fit rounded-full bg-leaf px-3 py-1 text-xs font-semibold text-forest-950">Terbaru</span>
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
                                        Baca <i aria-hidden="true" class="bi bi-arrow-right transition group-hover:translate-x-1"></i>
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
            metrics: { kth: 'KTH', ps: 'Perhutanan Sosial', nilai_transaksi_ekonomi: 'Nilai Ekonomi' },
            get region() { return this.regions[this.active] },
            get min() { return Math.min(...Object.values(this.regions).map(r => r[this.metric])) },
            get max() { return Math.max(...Object.values(this.regions).map(r => r[this.metric])) },
            fill(slug) {
                const spread = this.max - this.min;
                const share = spread ? (this.regions[slug][this.metric] - this.min) / spread : 0.5;
                return `fill: color-mix(in oklab, #b5d86a ${Math.round(share * 100)}%, #346a48)`;
            },
            format(value) {
                return {
                    kth: this.$angka(value) + ' KTH',
                    ps: this.$angka(value) + ' kelompok PS',
                    nilai_transaksi_ekonomi: this.$rupiah(value),
                }[this.metric];
            },
            label(slug) { return this.format(this.regions[slug][this.metric]) },
        }">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div data-reveal>
                <p class="eyebrow !text-leaf">Wilayah kerja</p>
                <h2 class="section-title max-w-2xl">Data kehutanan di <em class="!text-leaf">tiap wilayah</em>.</h2>
            </div>

            @php
                $rehabPersen = $capaian['rehab_lahan']['target'] > 0
                    ? min(100, round($capaian['rehab_lahan']['realisasi'] / $capaian['rehab_lahan']['target'] * 100))
                    : null;
                $capaianItems = [
                    [
                        'label' => 'Rehabilitasi lahan', 'value' => $capaian['rehab_lahan']['realisasi'], 'decimals' => 1, 'unit' => 'ha',
                        'note' => $rehabPersen !== null
                            ? $rehabPersen . '% dari target ' . number_format($capaian['rehab_lahan']['target'], 1, ',', '.') . ' ha'
                            : 'Realisasi',
                    ],
                    ['label' => 'Penghijauan lingkungan', 'value' => $capaian['penghijauan']['realisasi'], 'decimals' => 1, 'unit' => 'ha', 'note' => null],
                    ['label' => 'Pengunjung wisata alam', 'value' => $capaian['wisata']['pengunjung'], 'decimals' => 0, 'unit' => 'orang', 'note' => 'Pendapatan ' . rupiah_singkat($capaian['wisata']['pendapatan'])],
                    ['label' => 'Kebakaran hutan', 'value' => $capaian['kebakaran']['kejadian'], 'decimals' => 0, 'unit' => 'kejadian', 'note' => number_format($capaian['kebakaran']['luas'], 1, ',', '.') . ' ha terdampak'],
                ];
            @endphp
            <div class="mt-12 grid gap-10 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,0.9fr)] lg:gap-12">
                <figure data-reveal class="min-w-0">
                    <div class="flex flex-wrap items-end justify-between gap-x-8 gap-y-4">
                        <div class="flex flex-wrap gap-x-5 gap-y-2" role="group" aria-label="Pilih indikator peta">
                            <template x-for="(name, key) in metrics" :key="key">
                                <button type="button" @click="metric = key" :aria-pressed="metric === key"
                                    class="cursor-pointer border-b-2 px-0 py-3 text-sm font-medium transition-colors motion-reduce:transition-none"
                                    :class="metric === key ? 'border-leaf text-white' : 'border-transparent text-white/65 hover:text-white'"
                                    x-text="name"></button>
                            </template>
                        </div>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-2xl bg-forest-900">
                        <svg viewBox="0 0 480 400" class="block w-full" role="group" aria-label="Peta skematis wilayah kerja">
                            <path d="{{ $coast }} L480 0 L0 0 Z" fill="#174a31" />
                            <path d="{{ $coast }} L480 400 L0 400 Z" fill="#0b2118" />
                            <path d="{{ $coast }}" fill="none" stroke="#7ca885" stroke-opacity="0.65" />
                            @foreach ($neighbours as $neighbour)
                                <text x="{{ $neighbour['x'] }}" y="{{ $neighbour['y'] }}" text-anchor="middle" aria-hidden="true"
                                    class="fill-white/80 text-[8.5px] font-semibold tracking-[0.2em] uppercase">{{ $neighbour['name'] }}</text>
                            @endforeach
                            <text x="240" y="376" text-anchor="middle" aria-hidden="true"
                                class="fill-white/80 text-[9px] font-semibold tracking-[0.5em] uppercase">Samudra Hindia</text>

                            {{-- Working areas, shaded by the selected indicator. --}}
                            @foreach ($mapShapes as $slug => $shape)
                                <path d="{{ $shape['d'] }}" role="button" tabindex="0"
                                    aria-label="Tampilkan data {{ $regions[$slug]['label'] }}"
                                    :aria-pressed="active === '{{ $slug }}'" @click="active = '{{ $slug }}'"
                                    @keydown.enter.prevent="active = '{{ $slug }}'" @keydown.space.prevent="active = '{{ $slug }}'"
                                    stroke="#0b2118" stroke-width="2" stroke-linejoin="round" :style="fill('{{ $slug }}')"
                                    class="cursor-pointer outline-none transition-[fill] duration-200 motion-reduce:transition-none hover:brightness-110 focus-visible:stroke-leaf focus-visible:[stroke-width:4]" />
                            @endforeach

                            {{-- Active outline drawn last so it sits above the neighbouring regions. --}}
                            @foreach ($mapShapes as $slug => $shape)
                                <path d="{{ $shape['d'] }}" x-show="active === '{{ $slug }}'" fill="none" stroke="white"
                                    stroke-width="2.5" stroke-linejoin="round" class="pointer-events-none" />
                            @endforeach

                            {{-- Kota Kediri sits inside Kabupaten Kediri; both share one data set. --}}
                            <g class="pointer-events-none" aria-hidden="true">
                                <path d="M228 104 L240 98 L252 104 L254 116 L244 124 L230 120 Z" fill="white" fill-opacity="0.18"
                                    stroke="white" stroke-opacity="0.7" stroke-dasharray="2 2" />
                                <text x="241" y="138" text-anchor="middle" paint-order="stroke" stroke="#0b2118" stroke-width="3"
                                    stroke-opacity="0.6" class="fill-white text-[8.5px] font-semibold">Kota Kediri</text>
                            </g>

                            @foreach ($mapShapes as $slug => $shape)
                                <g class="pointer-events-none" aria-hidden="true">
                                    <text x="{{ $shape['x'] }}" y="{{ $shape['y'] }}" text-anchor="middle" paint-order="stroke"
                                        stroke="#0b2118" stroke-width="4" stroke-opacity="0.55" stroke-linejoin="round"
                                        class="fill-white text-[14px] font-extrabold">{{ $regions[$slug]['name'] }}</text>
                                    <text x="{{ $shape['x'] }}" y="{{ $shape['y'] + 16 }}" text-anchor="middle" paint-order="stroke"
                                        stroke="#0b2118" stroke-width="3" stroke-opacity="0.55" stroke-linejoin="round"
                                        class="fill-white/85 text-[10px] font-semibold" x-text="label('{{ $slug }}')"></text>
                                </g>
                            @endforeach

                            {{-- Office locations (config/cdk.php), placed by hand on the schematic map. --}}
                            @foreach ([
                                ['x' => 178, 'y' => 206, 'label' => 'Kantor utama'],
                                ['x' => 262, 'y' => 228, 'label' => 'Kantor wilker'],
                                ['x' => 241, 'y' => 111, 'label' => 'Kantor wilker'],
                            ] as $pin)
                                <g class="pointer-events-none" aria-hidden="true">
                                    <circle cx="{{ $pin['x'] }}" cy="{{ $pin['y'] }}" r="4.5" class="fill-clay" stroke="#f7f3e8" stroke-width="1.5" />
                                    <text x="{{ $pin['x'] }}" y="{{ $pin['y'] - 10 }}" text-anchor="middle" paint-order="stroke" stroke="#0b2118" stroke-width="3"
                                        stroke-opacity="0.6" class="fill-cream text-[8px] font-bold">{{ $pin['label'] }}</text>
                                </g>
                            @endforeach
                        </svg>
                    </div>
                </figure>

                @include('public.partials.wilayah-detail')
            </div>

            <p class="mt-4 text-xs text-white/50">Sumber: {{ $sumberData }}.</p>
        </div>
    </section>

    {{-- Layanan --}}
    <section id="layanan" class="scroll-mt-16 py-24 sm:py-32"
        x-data="{ active: 0, services: @js($services) }">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div data-reveal class="grid gap-6 lg:grid-cols-2 lg:items-end">
                <div>
                    <p class="eyebrow">Layanan kami</p>
                    <h2 class="section-title">Melayani masyarakat dan pelaku usaha <em>kehutanan</em>.</h2>
                </div>
            </div>

            <div data-reveal style="--reveal-delay: 120ms"
                class="mt-12 grid gap-6 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:gap-10">
                <div class="grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-1" role="group" aria-label="Pilih layanan">
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
                            Tanyakan layanan
                            <i aria-hidden="true" class="bi bi-arrow-up-right text-base"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>


@endsection
