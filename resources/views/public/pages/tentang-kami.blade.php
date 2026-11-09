@extends('public.layouts.app')

@section('nav-overlay', true)

@section('title', judul_halaman('Tentang Kami'))
@section('description', 'Profil, tugas dan fungsi, serta struktur organisasi Cabang Dinas Kehutanan Wilayah Trenggalek, Dinas Kehutanan Provinsi Jawa Timur.')

@push('head')
    <script type="application/ld+json">{!! \App\Support\StructuredData::toJson(
        \App\Support\StructuredData::breadcrumb([['Beranda', route('home')], ['Tentang Kami', route('tentang-kami')]]),
    ) !!}</script>
@endpush

@php
    $fakta = [
        ['icon' => 'bi-journal-text', 'label' => 'Dasar hukum', 'value' => 'Pergub Jatim No. 48 Tahun 2018'],
        ['icon' => 'bi-bank', 'label' => 'Instansi induk', 'value' => 'Dinas Kehutanan Provinsi Jawa Timur'],
        ['icon' => 'bi-map', 'label' => 'Wilayah kerja', 'value' => '3 kabupaten, 1 kota'],
        ['icon' => 'bi-buildings', 'label' => 'Kantor', 'value' => 'Trenggalek, Tulungagung, Kediri'],
    ];

    $kantor = config('cdk.kantor');

    $bagian = [
        ['href' => '#profil', 'label' => 'Profil'],
        ['href' => '#tugas', 'label' => 'Tugas & fungsi'],
        ['href' => '#struktur', 'label' => 'Struktur organisasi'],
        ['href' => '#kantor', 'label' => 'Kantor'],
    ];

    $wilayah = [
        ['nama' => 'Kabupaten Trenggalek', 'catatan' => 'Kantor utama'],
        ['nama' => 'Kabupaten Tulungagung', 'catatan' => 'Kantor wilayah kerja'],
        ['nama' => 'Kabupaten Kediri', 'catatan' => null],
        ['nama' => 'Kota Kediri', 'catatan' => 'Kantor wilayah kerja'],
    ];

    $tugas = [
        ['icon' => 'bi-clipboard-check', 'title' => 'Perencanaan & Administrasi', 'text' => 'Menyusun program, anggaran, dan pengawasan administrasi kehutanan, serta pelayanan masyarakat dan evaluasi kinerja untuk tata kelola hutan berkelanjutan.'],
        ['icon' => 'bi-tree', 'title' => 'Pengelolaan & Rehabilitasi Hutan', 'text' => 'Melakukan pembinaan hutan hak, rehabilitasi lahan, pendampingan sertifikasi, dan pengolahan hasil hutan untuk kelestarian ekosistem.'],
        ['icon' => 'bi-droplet-half', 'title' => 'Konservasi & Ekosistem', 'text' => 'Melaksanakan pembinaan konservasi tanah dan air, pengendalian satwa liar non-CITES, dan pengelolaan kawasan penyangga untuk kelestarian lingkungan.'],
        ['icon' => 'bi-people', 'title' => 'Pemberdayaan Masyarakat & Penyuluhan', 'text' => 'Melakukan penyuluhan kehutanan, memberdayakan masyarakat, dan mengembangkan perhutanan sosial untuk kesejahteraan berkelanjutan.'],
    ];

    $unit = [
        ['jabatan' => 'Kepala Subbagian Tata Usaha', 'nama' => 'Subbagian Tata Usaha'],
        ['jabatan' => 'Kepala Seksi TKUK', 'nama' => 'Seksi Tata Kelola dan Usaha Hutan'],
        ['jabatan' => 'Kepala Seksi RLPM', 'nama' => 'Seksi Rehabilitasi Lahan dan Pemberdayaan Masyarakat'],
    ];
@endphp

@section('content')
    {{-- Hero --}}
    {{-- Page header: full-bleed team photo under a green gradient. --}}
    <section class="relative isolate overflow-hidden bg-forest-950 text-white">
        <img src="{{ asset('images/background/bg-1.webp') }}" alt="" fetchpriority="high"
            class="absolute inset-0 -z-30 size-full object-cover object-[70%_40%]">
        <div class="absolute inset-0 -z-20 bg-forest-950/85 lg:bg-transparent lg:bg-gradient-to-r lg:from-forest-950 lg:via-forest-950/85 lg:to-forest-950/25"></div>
        <div class="absolute inset-0 -z-20 bg-gradient-to-t from-forest-950/90 via-transparent to-transparent"></div>

        <div class="mx-auto max-w-7xl px-5 pt-32 pb-36 sm:px-8 lg:pt-40 lg:pb-44">
            <nav data-reveal aria-label="Breadcrumb">
                <ol class="flex flex-wrap items-center gap-2 text-sm text-white/75">
                    <li><a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 py-1 hover:text-white">
                            <i aria-hidden="true" class="bi bi-house-door"></i> Beranda</a></li>
                    <li aria-hidden="true"><i class="bi bi-chevron-right text-xs"></i></li>
                    <li aria-current="page" class="font-semibold text-white">Tentang Kami</li>
                </ol>
            </nav>

            <h1 data-reveal style="--reveal-delay: 80ms"
                class="mt-6 max-w-3xl font-display text-[2.4rem] leading-[1.04] font-bold tracking-[-0.035em] text-balance sm:text-6xl lg:text-[4.25rem]">
                Cabang Dinas Kehutanan <span class="text-leaf">Wilayah Trenggalek</span>
            </h1>
            <p data-reveal style="--reveal-delay: 160ms" class="mt-6 max-w-xl text-base leading-relaxed text-white/85 sm:text-lg">
                Cabang Dinas Kehutanan Provinsi Jawa Timur yang mengelola, merehabilitasi, dan melindungi hutan
                bersama masyarakat di Wilayah Kabupaten Trenggalek, Kabupaten Tulungagung, Kabupaten Kediri, dan Kota Kediri.
            </p>

            <nav data-reveal style="--reveal-delay: 240ms" aria-label="Bagian halaman" class="mt-9">
                <ul class="flex flex-wrap gap-2">
                    @foreach ($bagian as $item)
                        <li>
                            <a href="{{ $item['href'] }}"
                                class="inline-flex min-h-10 items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 text-sm font-semibold backdrop-blur transition hover:border-leaf hover:bg-leaf hover:text-forest-950">
                                {{ $item['label'] }} <i aria-hidden="true" class="bi bi-arrow-down-short text-base"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>
        </div>
    </section>

    {{-- Key facts, floating over the bottom edge of the header. --}}
    <div class="relative z-10 mx-auto -mt-24 max-w-7xl px-5 sm:px-8">
        <dl data-reveal style="--reveal-delay: 300ms"
            class="grid grid-cols-1 overflow-hidden rounded-2xl bg-white shadow-xl shadow-forest-950/10 ring-1 ring-forest-900/10 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($fakta as $item)
                <div @class([
                    'flex items-start gap-4 p-6',
                    'border-t border-forest-900/10' => ! $loop->first,
                    'sm:border-t-0' => $loop->index === 1,
                    'sm:border-l' => $loop->index % 2 === 1,
                    'lg:border-t-0 lg:border-l' => ! $loop->first,
                ])>
                    <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-forest-50 text-lg text-forest-700">
                        <i aria-hidden="true" class="bi {{ $item['icon'] }}"></i>
                    </span>
                    <div class="flex flex-col">
                        <dt class="text-sm text-ink/70">{{ $item['label'] }}</dt>
                        <dd class="order-first font-display text-base leading-snug font-semibold text-ink">{{ $item['value'] }}</dd>
                    </div>
                </div>
            @endforeach
        </dl>
    </div>

    {{-- Profil --}}
    <section id="profil" class="scroll-mt-20 pt-20 pb-24 sm:pt-24 sm:pb-32">
        <div class="mx-auto grid max-w-7xl gap-14 px-5 sm:px-8 lg:grid-cols-12 lg:items-center">
            <div data-reveal class="lg:col-span-6">
                <p class="eyebrow">Profil</p>
                <h2 class="section-title">Empat wilayah, satu komitmen untuk hutan yang <em>lestari</em>.</h2>
                <div class="mt-6 space-y-4 text-lg leading-relaxed text-ink/75">
                    <p>
                        Berdasarkan Peraturan Gubernur Jawa Timur Nomor 48 Tahun 2018, Cabang Dinas Kehutanan Wilayah
                        Trenggalek bertugas melaksanakan pengelolaan hutan, rehabilitasi lahan, perlindungan kawasan hutan,
                        serta pemberdayaan masyarakat desa hutan.
                    </p>
                    <p>
                        Kami menyediakan layanan perizinan kehutanan, pengawasan, dan penanganan pengaduan masyarakat.
                        Bersama seluruh pemangku kepentingan, kami berkomitmen mewujudkan pengelolaan hutan berkelanjutan
                        untuk kesejahteraan masyarakat.
                    </p>
                </div>
            </div>

            <div data-reveal style="--reveal-delay: 120ms" class="lg:col-span-6">
                <h3 class="text-xs font-semibold tracking-[0.18em] text-ink/70 uppercase">Wilayah kerja</h3>
                <ul class="mt-4 divide-y divide-forest-900/10 border-y border-forest-900/10">
                    @foreach ($wilayah as $item)
                        <li class="flex items-center gap-4 py-4">
                            <span class="grid size-11 shrink-0 place-items-center rounded-xl bg-forest-50 text-lg text-forest-700">
                                <i aria-hidden="true" class="bi bi-geo-alt-fill"></i>
                            </span>
                            <span class="font-display text-xl font-semibold">{{ $item['nama'] }}</span>
                            @if ($item['catatan'])
                                <span class="ml-auto rounded-full bg-sand px-3 py-1 text-xs font-semibold text-ink/80">{{ $item['catatan'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('home') }}#wilayah"
                    class="group mt-6 inline-flex min-h-11 items-center gap-3 font-semibold text-forest-800">
                    <span class="border-b-2 border-leaf pb-0.5">Lihat data kehutanan tiap wilayah</span>
                    <i aria-hidden="true" class="bi bi-arrow-right transition group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Tugas dan fungsi --}}
    <section id="tugas" class="scroll-mt-16 bg-sand/60 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div data-reveal>
                <p class="eyebrow">Tugas dan fungsi</p>
                <h2 class="section-title">Tugas utama <em>CDK Wilayah Trenggalek</em>.</h2>
            </div>

            <ol class="mt-12 grid gap-4 sm:grid-cols-2">
                @foreach ($tugas as $i => $item)
                    <li data-reveal style="--reveal-delay: {{ $i * 80 }}ms"
                        class="flex flex-col rounded-2xl border-l-[3px] border-forest-700 bg-cream p-7 sm:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <span class="font-display text-sm font-semibold tabular-nums text-forest-700" aria-hidden="true">0{{ $i + 1 }}</span>
                            <span class="grid size-12 shrink-0 place-items-center rounded-xl bg-forest-900 text-xl text-leaf">
                                <i aria-hidden="true" class="bi {{ $item['icon'] }}"></i>
                            </span>
                        </div>
                        <h3 class="mt-6 font-display text-2xl leading-tight font-semibold">{{ $item['title'] }}</h3>
                        <p class="mt-3 leading-relaxed text-ink/75">{{ $item['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- Struktur organisasi --}}
    <section id="struktur" class="scroll-mt-16 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div data-reveal class="mx-auto max-w-2xl text-center">
                <p class="eyebrow justify-center">Struktur organisasi</p>
                <h2 class="section-title">Pimpinan dan <em>unit kerja</em>.</h2>
            </div>

            <ul data-reveal style="--reveal-delay: 120ms" class="mt-14" aria-label="Struktur organisasi">
                <li>
                    <div class="mx-auto flex max-w-md items-center gap-4 rounded-2xl bg-forest-900 p-6 text-center text-white shadow-xl shadow-forest-950/15">
                        <span class="w-full">
                            <span class="block text-sm font-semibold text-leaf">Kepala Cabang Dinas</span>
                            <span class="mt-0.5 block font-display text-xl leading-snug font-semibold">Cabang Dinas Kehutanan Wilayah Trenggalek</span>
                        </span>
                    </div>

                    {{-- Connector lines are decorative; the nested list carries the hierarchy for assistive tech. --}}
                    <div aria-hidden="true" class="mx-auto h-10 w-0.5 bg-forest-900/25"></div>
                    <div class="relative">
                        <span aria-hidden="true" class="absolute top-0 right-[16.67%] left-[16.67%] hidden h-0.5 bg-forest-900/25 md:block"></span>
                    <ul class="grid gap-4 md:grid-cols-3 md:gap-6">
                        @foreach ($unit as $item)
                            <li class="relative md:pt-10">
                                <span aria-hidden="true" class="absolute top-0 left-1/2 hidden h-10 w-0.5 -translate-x-1/2 bg-forest-900/25 md:block"></span>
                                <div class="flex h-full items-start gap-4 rounded-2xl border border-forest-900/10 bg-white/60 p-6 text-center">
                                    <span class="w-full">
                                        <span class="block text-sm font-semibold text-clay">{{ $item['jabatan'] }}</span>
                                        <span class="mt-1 block font-display text-lg leading-snug font-semibold">{{ $item['nama'] }}</span>
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    {{-- Kantor (config/cdk.php) --}}
    <section id="kantor" class="scroll-mt-16 bg-sand/60 py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-5 sm:px-8">
            <div data-reveal>
                <p class="eyebrow">Kantor kami</p>
                <h2 class="section-title">Alamat kantor dan <em>jam layanan</em>.</h2>
            </div>

            <ul class="mt-12 grid gap-5 lg:grid-cols-3">
                @foreach ($kantor as $item)
                    <li data-reveal style="--reveal-delay: {{ $loop->index * 80 }}ms"
                        class="flex flex-col overflow-hidden rounded-2xl bg-cream shadow-sm ring-1 ring-forest-900/10">
                        @if ($loop->first)
                            <img src="{{ asset('images/background/gedung-cdk.webp') }}" loading="lazy"
                                alt="Gedung {{ $item['nama'] }}" class="aspect-[16/9] w-full object-cover">
                        @else
                            <div aria-hidden="true"
                                class="relative grid aspect-[16/9] place-items-center overflow-hidden bg-forest-900 text-6xl text-leaf [background-image:repeating-radial-gradient(circle_at_85%_120%,rgb(181_216_106/0.12)_0_1px,transparent_1.5px_26px)]">
                                <i class="bi bi-buildings"></i>
                            </div>
                        @endif
                        <div class="flex flex-1 flex-col p-6 sm:p-7">
                            <span @class([
                                'w-fit rounded-full px-3 py-1 text-xs font-semibold',
                                'bg-forest-900 text-leaf' => $loop->first,
                                'bg-forest-50 text-forest-800' => ! $loop->first,
                            ])>{{ $item['jenis'] }}</span>
                            <h3 class="mt-4 font-display text-xl leading-snug font-semibold">{{ $item['nama'] }}</h3>
                            <p class="mt-4 flex gap-3 text-ink/75">
                                <i aria-hidden="true" class="bi bi-geo-alt mt-0.5 text-forest-700"></i>
                                <span>{{ $item['alamat'] }}</span>
                            </p>
                            <a href="{{ $item['maps'] }}" target="_blank" rel="noopener noreferrer"
                                class="group mt-auto inline-flex min-h-11 w-fit items-center gap-2 pt-6 font-semibold text-forest-800">
                                <span class="border-b-2 border-leaf pb-0.5">Buka di Google Maps</span>
                                <i aria-hidden="true" class="bi bi-arrow-up-right transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5"></i>
                                <span class="sr-only">untuk {{ $item['nama'] }} (membuka tab baru)</span>
                            </a>
                        </div>
                    </li>
                @endforeach
            </ul>

            <div data-reveal class="mt-5 flex flex-col gap-6 rounded-2xl bg-forest-900 p-7 text-white sm:p-8 lg:flex-row lg:items-center lg:justify-between">
                <dl class="flex flex-col gap-4 sm:flex-row sm:gap-10">
                    <div class="flex gap-3">
                        <dt><i aria-hidden="true" class="bi bi-clock text-xl text-leaf"></i><span class="sr-only">Jam layanan</span></dt>
                        <dd class="text-white/85">Senin – Jumat, 08.00 – 16.00 WIB</dd>
                    </div>
                    <div class="flex gap-3">
                        <dt><i aria-hidden="true" class="bi bi-envelope text-xl text-leaf"></i><span class="sr-only">Email</span></dt>
                        <dd><a href="mailto:cdkwilayahtrenggalek@jatimprov.go.id" class="break-all text-white/85 underline decoration-leaf underline-offset-4 hover:text-white">cdkwilayahtrenggalek@jatimprov.go.id</a></dd>
                    </div>
                </dl>
                <a href="{{ route('kontak') }}" class="btn-primary w-fit shrink-0">Hubungi kami <i aria-hidden="true" class="bi bi-send"></i></a>
            </div>
        </div>
    </section>
@endsection
