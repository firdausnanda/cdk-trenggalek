@php
    $ukuranTeks = [
        ['value' => 100, 'label' => __('public.a11y.size_normal'), 'sample' => 'text-base'],
        ['value' => 115, 'label' => __('public.a11y.size_large'), 'sample' => 'text-lg'],
        ['value' => 130, 'label' => __('public.a11y.size_xlarge'), 'sample' => 'text-xl'],
    ];
    $pilihan = [
        ['key' => 'kontras', 'icon' => 'bi-circle-half', 'label' => __('public.a11y.contrast'), 'text' => __('public.a11y.contrast_hint')],
        ['key' => 'spasi', 'icon' => 'bi-text-paragraph', 'label' => __('public.a11y.spacing'), 'text' => __('public.a11y.spacing_hint')],
        ['key' => 'garisTautan', 'icon' => 'bi-link-45deg', 'label' => __('public.a11y.underline'), 'text' => __('public.a11y.underline_hint')],
        ['key' => 'tanpaAnimasi', 'icon' => 'bi-pause-circle', 'label' => __('public.a11y.no_motion'), 'text' => __('public.a11y.no_motion_hint')],
        ['key' => 'fontBaca', 'icon' => 'bi-fonts', 'label' => __('public.a11y.readable_font'), 'text' => __('public.a11y.readable_font_hint')],
    ];
@endphp

{{-- Accessibility preferences (WCAG 2.2 AA support tools). Settings are kept in this browser only. --}}
<div x-data="aksesibilitas" @keydown.escape.window="open = false">
    {{-- Icon button whose label slides out on hover or keyboard focus (wider screens only). The label
         stays in the DOM while collapsed, so screen readers always get the full name. --}}
    <button type="button" @click="open = true" :aria-expanded="open" aria-controls="panel-aksesibilitas"
        class="group fixed bottom-4 left-4 z-[55] flex items-center rounded-full bg-forest-900 p-1 text-white shadow-lg shadow-forest-950/30 ring-2 ring-white transition-all duration-300 ease-soft hover:bg-forest-700 sm:bottom-5 sm:left-5 sm:hover:pr-4 sm:focus-visible:pr-4">
        <span class="relative grid size-9 shrink-0 place-items-center rounded-full bg-leaf text-xl text-forest-950 transition group-hover:scale-105">
            <i aria-hidden="true" class="bi bi-universal-access"></i>
            <span x-cloak x-show="aktif" aria-hidden="true"
                class="absolute -top-1 -right-1 grid size-4 place-items-center rounded-full bg-white text-[10px] font-bold text-forest-900 ring-2 ring-forest-900"
                x-text="aktif"></span>
        </span>
        <span class="sr-only">{{ __('public.a11y.open') }}</span>
        <span class="sr-only sm:not-sr-only sm:grid sm:grid-cols-[0fr] sm:transition-[grid-template-columns] sm:duration-300 sm:ease-soft sm:group-hover:grid-cols-[1fr] sm:group-focus-visible:grid-cols-[1fr]">
            <span class="overflow-hidden text-left leading-tight whitespace-nowrap">
                <span class="block pl-2.5 text-[13px] font-bold">{{ __('public.a11y.title') }}</span>
                <span class="block pl-2.5 text-[11px] text-white/75"
                    x-text="aktif ? aktif + ' ' + @js(__('public.a11y.active_suffix')) : @js(__('public.a11y.adjust'))">{{ __('public.a11y.adjust') }}</span>
            </span>
        </span>
    </button>

    <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[60] bg-forest-950/60" @click="open = false"></div>

    <div x-cloak x-show="open" x-trap.inert.noscroll="open"
        x-transition:enter="transition duration-300 ease-soft" x-transition:enter-start="translate-y-4 opacity-0"
        x-transition:leave="transition duration-200" x-transition:leave-end="translate-y-4 opacity-0"
        id="panel-aksesibilitas" role="dialog" aria-modal="true" aria-labelledby="judul-aksesibilitas"
        class="fixed inset-x-3 bottom-3 z-[61] max-h-[calc(100dvh-1.5rem)] overflow-y-auto rounded-[1.75rem] bg-cream text-ink shadow-2xl sm:inset-x-auto sm:bottom-24 sm:left-5 sm:max-h-[calc(100dvh-8rem)] sm:w-[25rem]">
        <div class="sticky top-0 flex items-start justify-between gap-4 border-b border-ink/10 bg-cream px-6 pt-6 pb-4">
            <div>
                <h2 id="judul-aksesibilitas" class="font-display text-2xl">{{ __('public.a11y.title') }}</h2>
                <p class="mt-1 text-sm text-ink/70">{{ __('public.a11y.intro') }}</p>
            </div>
            <button type="button" @click="open = false"
                class="grid size-11 shrink-0 place-items-center rounded-full text-xl transition hover:bg-ink/5">
                <i aria-hidden="true" class="bi bi-x-lg"></i>
                <span class="sr-only">{{ __('public.a11y.close') }}</span>
            </button>
        </div>

        <div class="px-6 py-5">
            <fieldset>
                <legend class="text-sm font-semibold">{{ __('public.a11y.text_size') }}</legend>
                <div class="mt-3 grid grid-cols-3 gap-2">
                    @foreach ($ukuranTeks as $ukuran)
                        <button type="button" @click="pref.teks = {{ $ukuran['value'] }}" :aria-pressed="pref.teks === {{ $ukuran['value'] }}"
                            class="flex min-h-16 flex-col items-center justify-center rounded-2xl border-2 px-2 py-2 transition"
                            :class="pref.teks === {{ $ukuran['value'] }} ? 'border-forest-700 bg-forest-700 text-white' : 'border-ink/15 hover:border-forest-700'">
                            <span aria-hidden="true" class="font-display font-bold leading-none {{ $ukuran['sample'] }}">Aa</span>
                            <span class="mt-1 text-xs font-medium">{{ $ukuran['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </fieldset>

            <ul class="mt-5 space-y-2">
                @foreach ($pilihan as $item)
                    <li>
                        <button type="button" role="switch" :aria-checked="pref.{{ $item['key'] }}.toString()"
                            @click="pref.{{ $item['key'] }} = !pref.{{ $item['key'] }}"
                            class="flex w-full items-center gap-4 rounded-2xl border-2 px-4 py-3 text-left transition"
                            :class="pref.{{ $item['key'] }} ? 'border-forest-700 bg-forest-700/5' : 'border-ink/10 hover:border-ink/30'">
                            <i aria-hidden="true" class="bi {{ $item['icon'] }} text-xl text-forest-700"></i>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-semibold">{{ $item['label'] }}</span>
                                <span class="block text-xs text-ink/70">{{ $item['text'] }}</span>
                            </span>
                            <span aria-hidden="true" class="relative h-6 w-11 shrink-0 rounded-full transition"
                                :class="pref.{{ $item['key'] }} ? 'bg-forest-700' : 'bg-ink/25'">
                                <span class="absolute top-0.5 left-0.5 size-5 rounded-full bg-white shadow transition"
                                    :class="pref.{{ $item['key'] }} && 'translate-x-5'"></span>
                            </span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-ink/10 px-6 py-4">
            <button type="button" @click="aturUlang()" :disabled="!aktif"
                class="inline-flex items-center gap-2 rounded-full border-2 border-ink/15 px-4 py-2 text-sm font-semibold transition hover:border-forest-700 disabled:opacity-50">
                <i aria-hidden="true" class="bi bi-arrow-counterclockwise"></i> {{ __('public.a11y.reset') }}
            </button>
            <a href="{{ route('aksesibilitas') }}" class="py-2 text-sm font-semibold text-forest-700 underline underline-offset-4">
                {{ __('public.a11y.statement') }}
            </a>
        </div>
    </div>
</div>
