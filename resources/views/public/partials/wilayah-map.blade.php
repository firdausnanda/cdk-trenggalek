<div class="mt-5 overflow-hidden rounded-2xl border border-white/15 bg-[#dce8e4]">
    <svg viewBox="{{ $map['viewBox'] }}" class="block w-full" role="group" aria-label="{{ __('public.home.map_label') }}">
        <defs>
            <linearGradient id="wilayah-sea" x2="0" y2="1">
                <stop stop-color="#e3eeeb" />
                <stop offset="1" stop-color="#bed5d4" />
            </linearGradient>
        </defs>
        <rect width="640" height="490" fill="url(#wilayah-sea)" />
        @foreach ($map['context'] as $path)
            <path d="{{ $path }}" fill="#e4e9dc" fill-rule="evenodd" stroke="#b9c7b7"
                stroke-width="1" vector-effect="non-scaling-stroke" />
        @endforeach
        @foreach ($map['regions'] as $slug => $path)
            <path d="{{ $path }}" role="button" tabindex="0" fill-rule="evenodd"
                aria-label="{{ __('public.home.map_show_region', ['region' => $regions[$slug]['label']]) }}"
                :aria-pressed="active === '{{ $slug }}'" @click="active = '{{ $slug }}'"
                @keydown.enter.prevent="active = '{{ $slug }}'" @keydown.space.prevent="active = '{{ $slug }}'"
                :style="fill('{{ $slug }}')" class="wilayah-map-region" />
        @endforeach
        <path d="{{ $map['kediriCity'] }}" role="button" tabindex="0" fill-rule="evenodd"
            aria-label="{{ __('public.home.map_show_region', ['region' => $regions['kediri']['label']]) }}"
            :aria-pressed="active === 'kediri'" @click="active = 'kediri'"
            @keydown.enter.prevent="active = 'kediri'" @keydown.space.prevent="active = 'kediri'"
            :style="fill('kediri')" class="wilayah-map-region" />
        @foreach ($map['regions'] as $slug => $path)
            <path d="{{ $path }}" x-show="active === '{{ $slug }}'" x-cloak fill="none" fill-rule="evenodd"
                stroke="#fffdf4" stroke-width="3" vector-effect="non-scaling-stroke"
                class="pointer-events-none" />
        @endforeach
        <path d="{{ $map['kediriCity'] }}" x-show="active === 'kediri'" x-cloak fill="none"
            stroke="#fffdf4" stroke-width="3" vector-effect="non-scaling-stroke"
            class="pointer-events-none" />
        <g class="pointer-events-none fill-[#596f62] text-[12px] font-medium" aria-hidden="true">
            <text x="160" y="160" text-anchor="middle">Ponorogo</text>
            <text x="370" y="45" text-anchor="middle">Nganjuk</text>
            <text x="540" y="355" text-anchor="middle">Blitar</text>
            <text x="320" y="474" text-anchor="middle" class="fill-[#456f75] text-[11px] font-semibold tracking-[0.18em] uppercase">{{ __('public.home.indian_ocean') }}</text>
        </g>
        <g class="pointer-events-none fill-[#173a2a] text-[18px] font-bold" stroke="#f5f9ed" stroke-width="4"
            stroke-linejoin="round" paint-order="stroke" aria-hidden="true">
            <text x="170" y="355" text-anchor="middle">Trenggalek</text>
            <text x="343" y="365" text-anchor="middle">Tulungagung</text>
            <text x="445" y="177" text-anchor="middle">Kediri</text>
        </g>
    </svg>
    <div class="grid grid-cols-3 gap-2 border-t border-[#c6d6d0] bg-[#f3f6ee] p-2 sm:hidden">
        @foreach ($map['regions'] as $slug => $path)
            <button type="button" @click="active = '{{ $slug }}'" :aria-pressed="active === '{{ $slug }}'"
                class="flex min-h-11 cursor-pointer items-center justify-center rounded-lg px-2 py-2 text-center text-xs font-semibold focus-visible:outline-forest-700"
                :class="active === '{{ $slug }}' ? 'bg-forest-800 text-white' : 'bg-white text-forest-900 hover:bg-forest-100'">
                {{ $regions[$slug]['name'] }}
            </button>
        @endforeach
    </div>
</div>
<figcaption class="mt-4 flex flex-wrap items-end justify-between gap-x-5 gap-y-3 text-xs text-white/75">
    <div class="flex items-center gap-3">
        <span>{{ __('public.home.map_legend_low') }}</span>
        <span class="h-2.5 w-28 rounded-full bg-[linear-gradient(90deg,#bad4a8,#3d865c)]" aria-hidden="true"></span>
        <span>{{ __('public.home.map_legend_high') }}</span>
    </div>
    <p>{{ __('public.home.map_boundary_source') }} <a href="{{ $map['source'] }}" target="_blank" rel="noopener noreferrer"
        class="font-semibold text-white underline decoration-white/45 underline-offset-2 hover:decoration-white">{{ __('public.home.map_boundary_name') }}</a></p>
    <p class="w-full text-white/50">{{ __('public.common.source', ['source' => $sumberData]) }}.</p>
</figcaption>
