{{--
    Stat card. Params (all optional except label/value):
    label, value, url, icon (bootstrap-icons class) or icon_svg (image URL), hint (small text under the value),
    tone: forest | leaf | clay | sky (accent colour), bg (legacy: hex accent colour, used when no tone given).
--}}
@php
    $tones = [
        'forest' => 'bg-forest-100 text-forest-800 dark:bg-forest-700/40 dark:text-forest-100',
        'leaf' => 'bg-leaf/35 text-forest-900 dark:bg-leaf/20 dark:text-leaf',
        'clay' => 'bg-[#f4e4d4] text-clay dark:bg-clay/30 dark:text-[#f0c9a4]',
        'sky' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
    ];
    // No tone and no legacy hex colour -> forest.
    $toneClass = $tones[$tone ?? (isset($bg) ? '' : 'forest')] ?? null;
    $url = $url ?? null;
    $tag = $url ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($url) href="{{ $url }}" @endif
    class="group relative flex h-full flex-col justify-between gap-5 overflow-hidden rounded-2xl border border-sand bg-white p-4 sm:p-5 transition-all duration-200 dark:border-white/10 dark:bg-forest-900/60 {{ $url ? 'hover:-translate-y-0.5 hover:border-forest-200 hover:shadow-theme-md dark:hover:border-white/20' : '' }} {{ $class ?? '' }}">
    <div class="flex items-start justify-between gap-3">
        <p class="text-sm font-medium text-ink/65 dark:text-white/65">{{ $label }}</p>
        <span class="flex size-10 shrink-0 items-center justify-center rounded-xl {{ $toneClass ?? '' }}"
            @if (!$toneClass) style="background: {{ ($bg ?? '#1f5f3d') }}1f; color: {{ $bg ?? '#1f5f3d' }};" @endif>
            @if (!empty($icon))
                <i class="{{ $icon }} text-lg" aria-hidden="true"></i>
            @elseif (!empty($icon_svg))
                <img src="{{ $icon_svg }}" alt="" class="size-5 dark:invert">
            @endif
        </span>
    </div>

    <div>
        <p class="font-display text-2xl sm:text-3xl font-semibold tracking-tight text-forest-950 tabular-nums dark:text-white">
            {!! $value ?? 0 !!}
        </p>
        @if (!empty($hint))
            <p class="mt-1 text-xs text-ink/55 dark:text-white/55">{!! $hint !!}</p>
        @endif
    </div>

    @if ($url)
        <i class="bi bi-arrow-up-right absolute right-5 bottom-5 text-sm text-ink/30 transition-colors group-hover:text-forest-700 dark:text-white/30 dark:group-hover:text-leaf" aria-hidden="true"></i>
    @endif
</{{ $tag }}>
