@extends('public.layouts.app')

@section('nav-overlay', true)

@section('title', 'Pernyataan Aksesibilitas · ' . config('settings.app_name'))
@section('description', 'Komitmen Cabang Dinas Kehutanan Wilayah Trenggalek terhadap aksesibilitas situs web sesuai WCAG 2.2 dan ISO/IEC 40500.')

@php
    $fitur = [
        ['icon' => 'bi-fonts', 'label' => 'Ukuran teks', 'text' => 'Perbesar teks hingga 130% tanpa merusak tata letak.'],
        ['icon' => 'bi-circle-half', 'label' => 'Kontras tinggi', 'text' => 'Latar hitam-putih dan teks tanpa transparansi.'],
        ['icon' => 'bi-text-paragraph', 'label' => 'Spasi teks lega', 'text' => 'Jarak baris, huruf, dan kata sesuai kriteria WCAG 1.4.12.'],
        ['icon' => 'bi-link-45deg', 'label' => 'Garis bawahi tautan', 'text' => 'Tautan tidak hanya dibedakan dengan warna.'],
        ['icon' => 'bi-pause-circle', 'label' => 'Hentikan animasi', 'text' => 'Mematikan gerakan latar, hitungan angka, dan efek transisi.'],
        ['icon' => 'bi-eyeglasses', 'label' => 'Font mudah dibaca', 'text' => 'Atkinson Hyperlegible, dirancang untuk pembaca dengan penglihatan rendah.'],
    ];
    $penerapan = [
        'Navigasi penuh dengan keyboard, termasuk peta wilayah kerja, dengan penanda fokus yang jelas.',
        'Tautan "Langsung ke konten" di awal setiap halaman.',
        'Struktur judul berurutan dan penanda wilayah halaman (header, navigasi, konten utama, footer).',
        'Teks alternatif pada gambar yang bermakna; ikon dekoratif disembunyikan dari pembaca layar.',
        'Kontras warna teks minimal 4,5:1, atau 3:1 untuk teks besar dan komponen antarmuka.',
        'Mengikuti pengaturan "kurangi gerakan" dari sistem operasi.',
        'Tampilan menyesuaikan layar ponsel tanpa perlu menggulir ke samping.',
        'Tautan yang membuka tab baru diberi keterangan untuk pembaca layar.',
    ];
    $keterbatasan = [
        'Sebagian gambar pada berita lama mungkin belum memiliki teks alternatif yang deskriptif.',
        'Peta wilayah kerja bersifat skematis; data setiap wilayah juga tersedia dalam bentuk teks di panel sampingnya.',
        'Dokumen unduhan (mis. PDF) dari pihak lain belum tentu memenuhi standar aksesibilitas.',
    ];
@endphp

@section('content')
    <section class="bg-forest-950 pt-36 pb-16 text-white sm:pb-20">
        <div class="mx-auto max-w-4xl px-5 sm:px-8">
            <p class="eyebrow !text-leaf">Aksesibilitas</p>
            <h1 class="section-title">Pernyataan aksesibilitas</h1>
            <p class="mt-6 max-w-2xl text-lg text-white/80">
                Kami berupaya agar situs Cabang Dinas Kehutanan Wilayah Trenggalek dapat digunakan oleh semua orang,
                termasuk penyandang disabilitas dan pengguna teknologi bantu.
            </p>
        </div>
    </section>

    <div class="mx-auto max-w-4xl space-y-16 px-5 py-16 sm:px-8 sm:py-20">
        <section aria-labelledby="standar">
            <h2 id="standar" class="font-display text-3xl">Standar yang dituju</h2>
            <p class="mt-4 text-lg leading-relaxed text-ink/80">
                Situs ini dirancang untuk memenuhi <strong>Web Content Accessibility Guidelines (WCAG) 2.2 tingkat AA</strong>
                dari W3C. WCAG kompatibel ke belakang dengan <strong>ISO/IEC 40500:2012</strong> (WCAG 2.0), sehingga
                pemenuhan WCAG 2.2 AA juga mencakup standar tersebut.
            </p>
            <p class="mt-4 text-lg leading-relaxed text-ink/80">
                Status saat ini: <strong>sebagian memenuhi</strong>. Artinya sebagian besar konten sudah sesuai standar,
                dengan beberapa keterbatasan yang dijelaskan di bawah.
            </p>
        </section>

        <section aria-labelledby="menu">
            <h2 id="menu" class="font-display text-3xl">Menu aksesibilitas</h2>
            <p class="mt-4 text-lg leading-relaxed text-ink/80">
                Tombol <i aria-hidden="true" class="bi bi-universal-access"></i> <strong>Aksesibilitas</strong> di pojok kiri
                bawah setiap halaman membuka pengaturan tampilan berikut. Pengaturan tersimpan di peramban Anda dan tidak
                dikirim ke server.
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
                Menu ini melengkapi, bukan menggantikan, teknologi bantu yang Anda gunakan seperti pembaca layar, pembesar
                layar, atau pengaturan aksesibilitas pada sistem operasi dan peramban.
            </p>
        </section>

        <section aria-labelledby="penerapan">
            <h2 id="penerapan" class="font-display text-3xl">Yang sudah kami terapkan</h2>
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
            <h2 id="keterbatasan" class="font-display text-3xl">Keterbatasan yang diketahui</h2>
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
            <h2 id="umpan-balik" class="font-display text-3xl">Laporkan kendala aksesibilitas</h2>
            <p class="mt-4 text-lg text-white/80">
                Jika Anda menemui bagian situs yang sulit diakses, beri tahu kami beserta alamat halaman dan kendalanya.
                Kami akan menindaklanjuti dan, bila perlu, menyediakan informasi dalam format lain.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="mailto:cdkwilayahtrenggalek@jatimprov.go.id?subject=Kendala%20aksesibilitas%20situs" class="btn-primary">
                    <i aria-hidden="true" class="bi bi-envelope"></i> cdkwilayahtrenggalek@jatimprov.go.id
                </a>
                <a href="{{ route('kontak') }}" class="btn-ghost">Formulir kontak</a>
            </div>
        </section>

        <p class="text-sm text-ink/70">Pernyataan ini terakhir ditinjau pada 31 Oktober 2026.</p>
    </div>
@endsection
