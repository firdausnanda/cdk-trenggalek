@php
    $socials = [
        ['icon' => 'bi-facebook', 'label' => 'Facebook CDK Wilayah Trenggalek', 'href' => 'https://www.facebook.com/cdktrenggalekprogram'],
        ['icon' => 'bi-instagram', 'label' => 'Instagram CDK Wilayah Trenggalek', 'href' => 'https://www.instagram.com/cdktrenggalek/'],
        ['icon' => 'bi-youtube', 'label' => 'YouTube CDK Wilayah Trenggalek', 'href' => 'https://www.youtube.com/channel/UCzVEqqMfVnTHqbAKe-ememg'],
        ['icon' => 'bi-envelope', 'label' => 'Kirim email ke CDK Wilayah Trenggalek', 'href' => 'mailto:cdkwilayahtrenggalek@jatimprov.go.id'],
    ];
    $links = [
        ['label' => 'Beranda', 'href' => route('home')],
        ['label' => 'Tentang Kami', 'href' => route('tentang-kami')],
        ['label' => 'Berita', 'href' => route('berita')],
        ['label' => 'Kontak', 'href' => route('kontak')],
        ['label' => 'Pernyataan Aksesibilitas', 'href' => route('aksesibilitas')],
        ['label' => 'Kebijakan Privasi & Cookie', 'href' => route('kebijakan-privasi')],
    ];
    $agencies = [
        ['label' => 'Pemerintah Provinsi Jawa Timur', 'href' => 'https://jatimprov.go.id/'],
        ['label' => 'Dinas Kehutanan Provinsi Jawa Timur', 'href' => 'https://dishut.jatimprov.go.id/'],
    ];
@endphp

<footer class="relative overflow-hidden bg-forest-950 text-white/70">
    <i class="bi bi-tree-fill pointer-events-none absolute -right-16 -bottom-24 text-[26rem] leading-none text-white/[0.03]"
        aria-hidden="true"></i>

    <div class="relative mx-auto max-w-7xl px-5 pt-16 pb-10 sm:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-12">
            <div class="lg:col-span-4">
                <div class="flex items-center gap-3 text-white">
                    <img src="{{ config('settings.site_icon') }}" alt="" class="size-12 object-contain">
                    <p class="font-display text-lg leading-tight">Cabang Dinas Kehutanan<br>Wilayah Trenggalek</p>
                </div>
                <p class="mt-5 max-w-sm text-sm leading-relaxed">
                    Berkomitmen melindungi, mengelola, dan melestarikan sumber daya hutan secara berkelanjutan.
                </p>
                <ul class="mt-6 flex gap-2">
                    @foreach ($socials as $social)
                        @php($external = str_starts_with($social['href'], 'http'))
                        <li>
                            <a href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}{{ $external ? ' (membuka tab baru)' : '' }}"
                                @if ($external) target="_blank" rel="noopener noreferrer" @endif
                                class="grid size-11 place-items-center rounded-full border border-white/15 text-white transition hover:border-leaf hover:bg-leaf hover:text-forest-950">
                                <i class="bi {{ $social['icon'] }}" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <nav class="lg:col-span-2" aria-labelledby="footer-tautan">
                <h2 id="footer-tautan" class="text-sm font-semibold tracking-widest text-white uppercase">Tautan</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    @foreach ($links as $link)
                        <li><a href="{{ $link['href'] }}" class="inline-block py-1.5 transition hover:text-leaf">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <div class="lg:col-span-3">
                <h2 class="text-sm font-semibold tracking-widest text-white uppercase">Website dinas</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    @foreach ($agencies as $agency)
                        <li>
                            <a href="{{ $agency['href'] }}" target="_blank" rel="noopener noreferrer" class="inline-block py-1.5 transition hover:text-leaf">
                                {{ $agency['label'] }} <i class="bi bi-arrow-up-right text-xs" aria-hidden="true"></i>
                                <span class="sr-only">(membuka tab baru)</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-sm font-semibold tracking-widest text-white uppercase">Kontak</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach (config('cdk.kantor') as $kantor)
                        <li class="flex gap-3">
                            <i class="bi bi-geo-alt text-leaf" aria-hidden="true"></i>
                            <div>
                                <p class="font-semibold text-white">{{ $kantor['nama'] }}</p>
                                <a href="{{ $kantor['maps'] }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 py-1 font-semibold text-leaf transition-colors hover:text-white focus-visible:outline-offset-4 motion-reduce:transition-none">
                                    Petunjuk arah <i class="bi bi-arrow-up-right text-xs" aria-hidden="true"></i>
                                    <span class="sr-only">ke {{ $kantor['nama'] }} (membuka tab baru)</span>
                                </a>
                            </div>
                        </li>
                    @endforeach
                    <li><a href="{{ route('tentang-kami') }}#kantor" class="inline-block py-1 underline underline-offset-4 transition hover:text-leaf">Alamat lengkap semua kantor</a></li>
                    <li class="flex gap-3"><i class="bi bi-envelope text-leaf" aria-hidden="true"></i> <span class="break-all">cdkwilayahtrenggalek@jatimprov.go.id</span></li>
                    <li class="flex gap-3"><i class="bi bi-clock text-leaf" aria-hidden="true"></i> Senin – Jumat, 08.00 – 16.00 WIB</li>
                </ul>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t border-white/10 pt-8 text-xs sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} Cabang Dinas Kehutanan Wilayah Trenggalek. Semua hak dilindungi.</p>
            <a href="#top" class="inline-flex items-center gap-2 py-1.5 transition hover:text-leaf">Kembali ke atas <i class="bi bi-arrow-up" aria-hidden="true"></i></a>
        </div>
    </div>
</footer>
