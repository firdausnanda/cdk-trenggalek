@php
    $socials = [
        ['icon' => 'bi-facebook', 'label' => 'Facebook ' . config('cdk.nama_singkat'), 'href' => 'https://www.facebook.com/cdktrenggalekprogram'],
        ['icon' => 'bi-instagram', 'label' => 'Instagram ' . config('cdk.nama_singkat'), 'href' => 'https://www.instagram.com/cdktrenggalek/'],
        ['icon' => 'bi-youtube', 'label' => 'YouTube ' . config('cdk.nama_singkat'), 'href' => 'https://www.youtube.com/channel/UCzVEqqMfVnTHqbAKe-ememg'],
        ['icon' => 'bi-envelope', 'label' => __('public.footer.email_us'), 'href' => 'mailto:cdkwilayahtrenggalek@jatimprov.go.id'],
    ];
    $links = [
        ['label' => __('public.nav.home'), 'href' => route('home')],
        ['label' => __('public.nav.about'), 'href' => route('tentang-kami')],
        ['label' => __('public.nav.news'), 'href' => route('berita')],
        ['label' => __('public.nav.contact'), 'href' => route('kontak')],
        ['label' => __('public.nav.accessibility'), 'href' => route('aksesibilitas')],
        ['label' => __('public.nav.privacy'), 'href' => route('kebijakan-privasi')],
    ];
    $agencies = [
        ['label' => __('public.footer.agency_province'), 'href' => 'https://jatimprov.go.id/'],
        ['label' => __('public.footer.agency_forestry'), 'href' => 'https://dishut.jatimprov.go.id/'],
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
                    <p class="font-display text-lg leading-tight">{{ __('public.common.org_line1') }}<br>{{ __('public.common.org_line2') }}</p>
                </div>
                <p class="mt-5 max-w-sm text-sm leading-relaxed">
                    {{ __('public.footer.tagline') }}
                </p>
                <ul class="mt-6 flex gap-2">
                    @foreach ($socials as $social)
                        @php($external = str_starts_with($social['href'], 'http'))
                        <li>
                            <a href="{{ $social['href'] }}" aria-label="{{ $social['label'] }}{{ $external ? ' (' . __('public.common.new_tab') . ')' : '' }}"
                                @if ($external) target="_blank" rel="noopener noreferrer" @endif
                                class="grid size-11 place-items-center rounded-full border border-white/15 text-white transition hover:border-leaf hover:bg-leaf hover:text-forest-950">
                                <i class="bi {{ $social['icon'] }}" aria-hidden="true"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <nav class="lg:col-span-2" aria-labelledby="footer-tautan">
                <h2 id="footer-tautan" class="text-sm font-semibold tracking-widest text-white uppercase">{{ __('public.footer.links') }}</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    @foreach ($links as $link)
                        <li><a href="{{ $link['href'] }}" class="inline-block py-1.5 transition hover:text-leaf">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </nav>

            <div class="lg:col-span-3">
                <h2 class="text-sm font-semibold tracking-widest text-white uppercase">{{ __('public.footer.agencies') }}</h2>
                <ul class="mt-4 space-y-1 text-sm">
                    @foreach ($agencies as $agency)
                        <li>
                            <a href="{{ $agency['href'] }}" target="_blank" rel="noopener noreferrer" class="inline-block py-1.5 transition hover:text-leaf">
                                {{ $agency['label'] }} <i class="bi bi-arrow-up-right text-xs" aria-hidden="true"></i>
                                <span class="sr-only">({{ __('public.common.new_tab') }})</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="text-sm font-semibold tracking-widest text-white uppercase">{{ __('public.nav.contact') }}</h2>
                <ul class="mt-5 space-y-3 text-sm">
                    @foreach (kantor_cdk() as $kantor)
                        <li class="flex gap-3">
                            <i class="bi bi-geo-alt text-leaf" aria-hidden="true"></i>
                            <div>
                                <p class="font-semibold text-white">{{ $kantor['nama'] }}</p>
                                <a href="{{ $kantor['maps'] }}" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 py-1 font-semibold text-leaf transition-colors hover:text-white focus-visible:outline-offset-4 motion-reduce:transition-none">
                                    {{ __('public.common.directions') }} <i class="bi bi-arrow-up-right text-xs" aria-hidden="true"></i>
                                    <span class="sr-only">{{ __('public.common.directions_to', ['place' => $kantor['nama']]) }} ({{ __('public.common.new_tab') }})</span>
                                </a>
                            </div>
                        </li>
                    @endforeach
                    <li><a href="{{ route('tentang-kami') }}#kantor" class="inline-block py-1 underline underline-offset-4 transition hover:text-leaf">{{ __('public.footer.all_offices') }}</a></li>
                    <li class="flex gap-3"><i class="bi bi-envelope text-leaf" aria-hidden="true"></i> <span class="break-all">cdkwilayahtrenggalek@jatimprov.go.id</span></li>
                    <li class="flex gap-3"><i class="bi bi-clock text-leaf" aria-hidden="true"></i> {{ __('public.common.service_hours') }}</li>
                </ul>
            </div>
        </div>

        <div class="mt-14 flex flex-col gap-4 border-t border-white/10 pt-8 text-xs sm:flex-row sm:items-center sm:justify-between">
            <p>{{ __('public.footer.copyright', ['year' => date('Y'), 'name' => __('public.common.org_name')]) }}</p>
            <a href="#top" class="inline-flex items-center gap-2 py-1.5 transition hover:text-leaf">{{ __('public.footer.back_to_top') }} <i class="bi bi-arrow-up" aria-hidden="true"></i></a>
        </div>
    </div>
</footer>
