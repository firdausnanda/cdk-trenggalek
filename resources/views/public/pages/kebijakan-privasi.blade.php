@extends('public.layouts.app')

@section('nav-overlay', true)

@section('title', 'Kebijakan Privasi & Cookie · ' . config('settings.app_name'))
@section('description', 'Data pribadi dan cookie yang digunakan situs Cabang Dinas Kehutanan Wilayah Trenggalek, tujuan penggunaannya, dan hak Anda sebagai pemilik data.')

@php
    $email = 'cdkwilayahtrenggalek@jatimprov.go.id';
    $pilihanCookie = \App\Support\StatisticsConsent::choice(request());

    $daftarIsi = [
        'pengelola' => 'Pengelola data',
        'data' => 'Data yang kami kumpulkan',
        'tujuan' => 'Dasar dan tujuan pemrosesan',
        'cookie' => 'Cookie dan penyimpanan lokal',
        'pihak-ketiga' => 'Pihak ketiga',
        'penyimpanan' => 'Penyimpanan dan keamanan',
        'hak' => 'Hak Anda',
        'perubahan' => 'Perubahan kebijakan',
    ];

    $dataDikumpulkan = [
        [
            'sumber' => 'Formulir kontak',
            'data' => 'Nama, alamat email, dan isi pesan',
            'tujuan' => 'Menanggapi pertanyaan, permohonan, atau pengaduan Anda',
            'publik' => 'Tidak ditampilkan',
        ],
        [
            'sumber' => 'Komentar berita',
            'data' => 'Nama, alamat email, dan isi komentar',
            'tujuan' => 'Menampilkan komentar dan menghubungi Anda bila perlu',
            'publik' => 'Nama dan isi komentar ditampilkan; email tidak',
        ],
        [
            'sumber' => 'Penghitung pembaca berita',
            'data' => 'Kode acak di cookie, tanpa nama atau alamat IP',
            'tujuan' => 'Menghitung jumlah pembaca setiap berita',
            'publik' => 'Hanya jumlah pembaca',
        ],
        [
            'sumber' => 'Log server',
            'data' => 'Alamat IP, jenis peramban, waktu akses',
            'tujuan' => 'Menjaga keamanan dan menangani gangguan situs',
            'publik' => 'Tidak ditampilkan',
        ],
    ];

    $cookie = [
        ['nama' => config('session.cookie'), 'jenis' => 'Wajib', 'fungsi' => 'Menjaga sesi kunjungan agar formulir dapat dikirim', 'masa' => config('session.lifetime') . ' menit'],
        ['nama' => 'XSRF-TOKEN', 'jenis' => 'Wajib', 'fungsi' => 'Melindungi formulir dari penyalahgunaan (CSRF)', 'masa' => config('session.lifetime') . ' menit'],
        ['nama' => config('eloquent-viewable.visitor_cookie_key'), 'jenis' => 'Penghitung pembaca (opsional)', 'fungsi' => 'Kode acak agar satu pembaca tidak dihitung berulang; dibuat setelah Anda menerima', 'masa' => '5 tahun'],
        ['nama' => \App\Support\StatisticsConsent::COOKIE_NAME, 'jenis' => 'Pilihan cookie', 'fungsi' => 'Menyimpan pilihan Terima Cookie atau Tolak Cookie', 'masa' => '1 tahun'],
        ['nama' => 'cdk-aksesibilitas', 'jenis' => 'Preferensi (penyimpanan lokal)', 'fungsi' => 'Menyimpan pengaturan menu aksesibilitas; tidak dikirim ke server', 'masa' => 'Sampai Anda menghapusnya'],
    ];

    $hak = [
        ['icon' => 'bi-eye', 'label' => 'Mengakses', 'text' => 'Meminta salinan data pribadi Anda yang kami simpan.'],
        ['icon' => 'bi-pencil-square', 'label' => 'Memperbaiki', 'text' => 'Meminta perbaikan data yang tidak akurat atau tidak lengkap.'],
        ['icon' => 'bi-trash3', 'label' => 'Menghapus', 'text' => 'Meminta penghapusan data, termasuk komentar yang pernah Anda kirim.'],
        ['icon' => 'bi-hand-index', 'label' => 'Menarik persetujuan', 'text' => 'Menarik persetujuan atas pemrosesan yang didasarkan pada persetujuan Anda.'],
        ['icon' => 'bi-exclamation-octagon', 'label' => 'Mengajukan keberatan', 'text' => 'Menyampaikan keberatan atas cara kami memproses data Anda.'],
    ];
@endphp

@section('content')
    <section class="bg-forest-950 pt-36 pb-16 text-white sm:pb-20">
        <div class="mx-auto max-w-4xl px-5 sm:px-8">
            <p class="eyebrow !text-leaf">Privasi</p>
            <h1 class="section-title">Kebijakan privasi &amp; cookie</h1>
            <p class="mt-6 max-w-2xl text-lg text-white/80">
                Halaman ini menjelaskan data pribadi apa yang dikumpulkan situs ini, untuk apa digunakan, berapa lama
                disimpan, dan hak Anda atas data tersebut sesuai Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan
                Data Pribadi.
            </p>
            <p class="mt-6 text-sm text-white/70">Berlaku sejak 31 Oktober 2026</p>
        </div>
    </section>

    <div class="mx-auto grid max-w-6xl gap-12 px-5 py-16 sm:px-8 sm:py-20 lg:grid-cols-[14rem_1fr]">
        <nav aria-labelledby="daftar-isi" class="lg:sticky lg:top-24 lg:self-start">
            <h2 id="daftar-isi" class="text-xs font-semibold tracking-[0.18em] text-ink/70 uppercase">Daftar isi</h2>
            <ol class="mt-4 space-y-1 text-sm">
                @foreach ($daftarIsi as $id => $judul)
                    <li><a href="#{{ $id }}" class="inline-block py-1 text-ink/80 transition hover:text-forest-700">{{ $loop->iteration }}. {{ $judul }}</a></li>
                @endforeach
            </ol>
        </nav>

        <div class="min-w-0 space-y-16 text-lg leading-relaxed text-ink/80">
            <section id="pengelola" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">1. Pengelola data</h2>
                <p class="mt-4">
                    Situs ini dikelola oleh <strong>Cabang Dinas Kehutanan Wilayah Trenggalek</strong>, bagian dari Dinas
                    Kehutanan Provinsi Jawa Timur, yang bertindak sebagai pengendali data pribadi untuk data yang dikumpulkan
                    melalui situs ini.
                </p>
                <p class="mt-4">
                    Gg. Menak Sopal, Sukobanteng, Karangsoko, Kec. Trenggalek, Kabupaten Trenggalek, Jawa Timur 66319 ·
                    <a href="mailto:{{ $email }}" class="font-semibold text-forest-700 underline underline-offset-4">{{ $email }}</a>
                </p>
            </section>

            <section id="data" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">2. Data yang kami kumpulkan</h2>
                <p class="mt-4">
                    Anda dapat membaca seluruh isi situs tanpa memberikan data pribadi. Kami hanya menerima data pribadi
                    ketika Anda mengirimkannya sendiri, serta data teknis minimum yang diperlukan agar situs berjalan.
                </p>
                <div class="mt-6 overflow-x-auto rounded-2xl border border-forest-900/10">
                    <table class="w-full min-w-[40rem] text-left text-base">
                        <caption class="sr-only">Data pribadi yang dikumpulkan situs</caption>
                        <thead class="bg-sand text-sm text-ink">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">Sumber</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Data</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Tujuan</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Ditampilkan publik</th>
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
                <h2 class="font-display text-3xl text-ink">3. Dasar dan tujuan pemrosesan</h2>
                <ul class="mt-4 space-y-3">
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-check-circle-fill mt-1 text-forest-600"></i>
                        <span><strong>Persetujuan Anda</strong> ketika mengirim formulir kontak atau komentar.</span></li>
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-check-circle-fill mt-1 text-forest-600"></i>
                        <span><strong>Pelaksanaan tugas pelayanan publik</strong> di bidang kehutanan, misalnya menindaklanjuti
                            permohonan informasi dan pengaduan.</span></li>
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-check-circle-fill mt-1 text-forest-600"></i>
                        <span><strong>Kepentingan yang sah</strong> untuk menjaga keamanan situs dan mengetahui jumlah pembaca
                            berita secara agregat.</span></li>
                </ul>
                <p class="mt-6">
                    Kami <strong>tidak</strong> menjual data pribadi, tidak menampilkan iklan, tidak melakukan pelacakan
                    lintas situs, dan tidak memasang alat analitik pihak ketiga di halaman publik.
                </p>
            </section>

            <section id="cookie" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">4. Cookie dan penyimpanan lokal</h2>
                <p class="mt-4">
                    Cookie adalah berkas kecil yang disimpan peramban Anda. Cookie wajib digunakan agar formulir berfungsi.
                    Cookie penghitung pembaca berita hanya dibuat setelah Anda memilih Terima Cookie.
                </p>
                <div class="mt-6 overflow-x-auto rounded-2xl border border-forest-900/10">
                    <table class="w-full min-w-[40rem] text-left text-base">
                        <caption class="sr-only">Cookie dan penyimpanan lokal yang digunakan situs</caption>
                        <thead class="bg-sand text-sm text-ink">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-semibold">Nama</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Jenis</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Fungsi</th>
                                <th scope="col" class="px-4 py-3 font-semibold">Masa berlaku</th>
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
                    <h3 class="font-display text-xl font-semibold text-ink">Pilihan Cookie</h3>
                    <p class="mt-2 text-base" role="status">
                        @if ($pilihanCookie === \App\Support\StatisticsConsent::ACCEPT)
                            Pilihan Anda saat ini: Terima Cookie.
                        @elseif ($pilihanCookie === \App\Support\StatisticsConsent::REJECT)
                            Pilihan Anda saat ini: Tolak Cookie.
                        @else
                            Anda belum menentukan pilihan.
                        @endif
                    </p>
                    <form method="POST" action="{{ route('cookie-consent.store') }}" class="mt-4 flex flex-wrap gap-3">
                        @csrf
                        <input type="hidden" name="return_to" value="{{ route('kebijakan-privasi', absolute: false) }}#cookie">
                        <button type="submit" name="choice" value="reject" class="min-h-11 cursor-pointer rounded-lg border border-forest-900 px-4 py-2 text-sm font-semibold text-forest-900 transition hover:bg-forest-50 active:bg-forest-100">Tolak Cookie</button>
                        <button type="submit" name="choice" value="accept" class="min-h-11 cursor-pointer rounded-lg bg-forest-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-forest-700 active:bg-forest-800">Terima Cookie</button>
                    </form>
                </div>
                <p class="mt-6">
                    Anda dapat mengubah pilihan di atas kapan saja. Jika Anda memilih Tolak Cookie, cookie penghitung pembaca dihapus dari peramban
                    dan kunjungan berikutnya tidak dicatat untuk penghitung pembaca unik. Memblokir cookie wajib melalui
                    pengaturan peramban akan membuat formulir kontak dan komentar tidak dapat dikirim.
                </p>
            </section>

            <section id="pihak-ketiga" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">5. Pihak ketiga</h2>
                <ul class="mt-4 space-y-3">
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-dot mt-1 text-2xl leading-none text-forest-600"></i>
                        <span>Huruf, ikon, dan skrip situs disajikan langsung dari server kami, tanpa layanan pihak
                            ketiga.</span></li>
                    <li class="flex gap-3"><i aria-hidden="true" class="bi bi-dot mt-1 text-2xl leading-none text-forest-600"></i>
                        <span>Tautan ke Facebook, Instagram, YouTube, dan situs pemerintah lain baru menghubungi layanan
                            tersebut ketika Anda mengekliknya. Data yang mereka kumpulkan diatur oleh kebijakan privasi
                            masing-masing.</span></li>
                </ul>
            </section>

            <section id="penyimpanan" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">6. Penyimpanan dan keamanan</h2>
                <p class="mt-4">
                    Data disimpan di server yang dikelola instansi dan hanya dapat diakses oleh petugas yang berwenang.
                    Pesan kontak disimpan selama diperlukan untuk menindaklanjutinya dan sesuai ketentuan kearsipan yang
                    berlaku. Komentar disimpan selama berita terkait masih diterbitkan, atau sampai Anda meminta
                    penghapusannya.
                </p>
            </section>

            <section id="hak" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">7. Hak Anda</h2>
                <p class="mt-4">Sebagai pemilik data pribadi, Anda berhak untuk:</p>
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
                    <h3 class="font-display text-2xl">Cara mengajukan permintaan</h3>
                    <p class="mt-3 text-white/80">
                        Kirim email berisi nama, alamat email yang Anda gunakan di situs ini, dan permintaan Anda. Kami
                        mungkin meminta informasi tambahan untuk memastikan permintaan berasal dari pemilik data.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="mailto:{{ $email }}?subject=Permintaan%20terkait%20data%20pribadi" class="btn-primary">
                            <i aria-hidden="true" class="bi bi-envelope"></i> Kirim permintaan
                        </a>
                        <a href="{{ route('kontak') }}" class="btn-ghost">Formulir kontak</a>
                    </div>
                </div>
            </section>

            <section id="perubahan" class="scroll-mt-24">
                <h2 class="font-display text-3xl text-ink">8. Perubahan kebijakan</h2>
                <p class="mt-4">
                    Kebijakan ini dapat diperbarui bila ada perubahan layanan atau peraturan. Versi terbaru selalu tersedia
                    di halaman ini beserta tanggal berlakunya.
                </p>
            </section>
        </div>
    </div>
@endsection
