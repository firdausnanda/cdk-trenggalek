{{--
    One message in the news comment thread.
    $c: Comment, $viewer: ?Commenter, $visibleIds: array<int, true> of comment ids shown on the page
    (a reply quotes its parent only when that parent is visible to the viewer).
--}}
@php
    $milikSaya = $c->isOwnedBy($viewer);
    $dariAdmin = $c->isFromAdmin();
    $bisaBalas = $viewer && ! $viewer->isBlocked() && $c->isPublished();
    $bisaLapor = $viewer && ! $milikSaya && ! $dariAdmin && $c->isPublished();
    $induk = $c->parent && isset($visibleIds[$c->parent_id]) ? $c->parent : null;
    $cuplikan = fn ($k) => Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $k->content), ENT_QUOTES | ENT_HTML5, 'UTF-8'))), 90);
@endphp
<li id="komentar-{{ $c->id }}" data-komentar="{{ $c->id }}" data-status="{{ $c->status }}" data-tanggal="{{ $c->created_at->toDateString() }}"
    class="komentar-item group/pesan flex scroll-mt-28 scroll-mb-44 items-end gap-2.5 {{ $milikSaya ? 'flex-row-reverse' : '' }}"
    x-data="{ menu: false }" @keydown.escape="menu = false">

    {{-- Avatar --}}
    @if ($dariAdmin)
        <span aria-hidden="true" class="grid size-9 shrink-0 place-items-center rounded-full bg-forest-900 text-leaf">
            <i class="bi bi-patch-check-fill"></i>
        </span>
    @elseif (! $milikSaya)
        @if ($c->authorAvatar())
            <img src="{{ $c->authorAvatar() }}" alt="" width="36" height="36" loading="lazy" referrerpolicy="no-referrer"
                class="size-9 shrink-0 rounded-full bg-sand object-cover">
        @else
            <span aria-hidden="true" class="grid size-9 shrink-0 place-items-center rounded-full bg-sand font-semibold text-forest-800">
                {{ $c->authorInitial() }}
            </span>
        @endif
    @endif

    <div class="flex max-w-[85%] min-w-0 flex-col sm:max-w-[75%] {{ $milikSaya ? 'items-end' : 'items-start' }}">
        <p class="mb-1 flex flex-wrap items-center gap-x-2 px-1 text-xs text-ink/70">
            @if ($milikSaya)
                <span class="font-semibold text-forest-800">{{ __('public.comments.you') }}</span>
            @else
                <span class="font-semibold text-ink">{{ Str::ucfirst($c->authorName()) }}</span>
                @if ($dariAdmin)
                    <span class="rounded-full bg-leaf px-2 py-0.5 text-[0.7rem] font-semibold text-forest-950">{{ __('public.comments.admin_badge') }}</span>
                @endif
            @endif
        </p>

        <div class="flex items-center gap-1 {{ $milikSaya ? 'flex-row-reverse' : '' }}">
            <div @class([
                'relative min-w-0 rounded-2xl px-4 py-2.5 shadow-sm',
                'rounded-br-md bg-forest-900 text-white' => $milikSaya && $c->isPublished(),
                'rounded-br-md border border-dashed border-forest-900/40 bg-forest-50 text-ink' => $milikSaya && ! $c->isPublished(),
                'rounded-bl-md border border-leaf/60 bg-[#f3f8e6] text-ink' => $dariAdmin,
                'rounded-bl-md border border-forest-900/10 bg-white text-ink' => ! $milikSaya && ! $dariAdmin,
            ])>
                @if ($c->parent_id)
                    <a href="{{ $induk ? '#komentar-'.$induk->id : '#komentar' }}"
                        @class([
                            'mb-2 block rounded-lg border-l-[3px] px-3 py-1.5 text-xs',
                            'border-leaf bg-white/10 text-white/85 hover:bg-white/15' => $milikSaya && $c->isPublished(),
                            'border-forest-600 bg-forest-900/5 text-ink/75 hover:bg-forest-900/10' => ! ($milikSaya && $c->isPublished()),
                        ])>
                        @if ($induk)
                            <span class="block font-semibold">{{ $induk->isOwnedBy($viewer) ? __('public.comments.you') : Str::ucfirst($induk->authorName()) }}</span>
                            <span class="line-clamp-2">{{ $cuplikan($induk) }}</span>
                        @else
                            <span class="italic">{{ __('public.comments.reply_unavailable') }}</span>
                        @endif
                    </a>
                @endif

                <p class="leading-relaxed break-words [overflow-wrap:anywhere]">{!! komentar_teks($c->content) !!}</p>

                <p class="mt-1 flex items-center justify-end gap-1.5 text-[0.7rem] {{ $milikSaya && $c->isPublished() ? 'text-white/70' : 'text-ink/60' }}">
                    <time datetime="{{ $c->created_at->toIso8601String() }}" title="{{ $c->created_at->translatedFormat('d F Y, H:i') }}">{{ $c->created_at->format('H:i') }}</time>
                    @if ($milikSaya)
                        @if ($c->isPublished())
                            <i class="bi bi-check2-all text-leaf" aria-hidden="true"></i><span class="sr-only">{{ __('public.comments.status_published') }}</span>
                        @endif
                    @endif
                </p>
            </div>

            {{-- Message actions --}}
            @if ($bisaBalas || $bisaLapor || $milikSaya)
                <div class="relative shrink-0">
                    <button type="button" @click="menu = !menu" :aria-expanded="menu.toString()" aria-haspopup="true"
                        class="grid size-9 place-items-center rounded-full text-ink/55 transition hover:bg-forest-900/10 hover:text-ink focus-visible:opacity-100 sm:opacity-0 sm:group-hover/pesan:opacity-100 sm:group-focus-within/pesan:opacity-100"
                        :class="menu && '!opacity-100 bg-forest-900/10'">
                        <i class="bi bi-three-dots" aria-hidden="true"></i>
                        <span class="sr-only">{{ __('public.comments.actions_for', ['name' => $milikSaya ? __('public.comments.you') : $c->authorName()]) }}</span>
                    </button>
                    <ul x-cloak x-show="menu" x-transition.origin.bottom @click.outside="menu = false"
                        class="absolute bottom-full z-20 mb-1 w-44 overflow-hidden rounded-xl border border-forest-900/10 bg-white py-1 text-sm shadow-lg {{ $milikSaya ? 'right-0' : 'left-0' }}">
                        @if ($bisaBalas)
                            <li>
                                <button type="button" class="flex min-h-11 w-full items-center gap-2.5 px-4 text-left hover:bg-forest-50"
                                    @click="menu = false; balas({{ $c->id }}, @js($milikSaya ? __('public.comments.you') : Str::ucfirst($c->authorName())), @js($cuplikan($c)))">
                                    <i class="bi bi-reply" aria-hidden="true"></i> {{ __('public.comments.reply') }}
                                </button>
                            </li>
                        @endif
                        @if ($bisaLapor)
                            <li>
                                <button type="button" class="flex min-h-11 w-full items-center gap-2.5 px-4 text-left hover:bg-forest-50"
                                    @click="menu = false; bukaLaporan({{ $c->id }})">
                                    <i class="bi bi-flag" aria-hidden="true"></i> {{ __('public.comments.report') }}
                                </button>
                            </li>
                        @endif
                        @if ($milikSaya)
                            <li>
                                <form method="POST" action="{{ route('komentar.destroy', $c) }}" @submit.prevent="menu = false; hapus($el)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex min-h-11 w-full items-center gap-2.5 px-4 text-left text-red-700 hover:bg-red-50">
                                        <i class="bi bi-trash3" aria-hidden="true"></i> {{ __('public.comments.delete') }}
                                    </button>
                                </form>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif
        </div>

        @if ($milikSaya && ! $c->isPublished())
            <p class="mt-1 flex items-start gap-1.5 px-1 text-xs text-clay">
                <i class="bi {{ $c->status === \App\Models\Comment::STATUS_HIDDEN ? 'bi-eye-slash' : 'bi-hourglass-split' }} mt-px" aria-hidden="true"></i>
                <span>{{ $c->status === \App\Models\Comment::STATUS_HIDDEN ? __('public.comments.status_hidden') : __('public.comments.status_pending') }}</span>
            </p>
        @endif
    </div>
</li>
