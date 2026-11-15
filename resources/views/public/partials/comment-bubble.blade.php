{{--
    One comment in the news comment list.
    $c: Comment, $viewer: ?Commenter, $visibleIds: array<int, true> of comment ids shown on the page
    (a reply references its parent only when that parent is visible to the viewer).
--}}
@php
    $milikSaya = $c->isOwnedBy($viewer);
    $dariAdmin = $c->isFromAdmin();
    $terbit = $c->isPublished();
    $bisaBalas = $viewer && ! $viewer->isBlocked() && $terbit;
    $bisaLapor = $viewer && ! $milikSaya && ! $dariAdmin && $terbit;
    $induk = $c->parent && isset($visibleIds[$c->parent_id]) ? $c->parent : null;
    $cuplikan = fn ($k) => Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $k->content), ENT_QUOTES | ENT_HTML5, 'UTF-8'))), 90);
    $nama = Str::ucfirst($c->authorName());
@endphp
<li id="komentar-{{ $c->id }}" data-komentar="{{ $c->id }}" data-status="{{ $c->status }}" data-tanggal="{{ $c->created_at->toDateString() }}"
    class="komentar-item flex scroll-mt-28 scroll-mb-44 gap-4 py-6 first:pt-0 last:pb-0">

    {{-- Avatar --}}
    @if ($dariAdmin)
        <span aria-hidden="true" class="grid size-10 shrink-0 place-items-center rounded-full bg-forest-900 text-leaf">
            <i class="bi bi-patch-check-fill"></i>
        </span>
    @elseif ($c->authorAvatar())
        <img src="{{ $c->authorAvatar() }}" alt="" width="40" height="40" loading="lazy" referrerpolicy="no-referrer"
            class="size-10 shrink-0 rounded-full bg-sand object-cover ring-1 ring-forest-900/10">
    @else
        <span aria-hidden="true" class="grid size-10 shrink-0 place-items-center rounded-full bg-forest-50 font-semibold text-forest-800 ring-1 ring-forest-900/10">
            {{ $c->authorInitial() }}
        </span>
    @endif

    <article class="min-w-0 flex-1" aria-labelledby="komentar-{{ $c->id }}-nama">
        {{-- Header --}}
        <header class="flex flex-wrap items-center gap-x-2 gap-y-1">
            <h3 id="komentar-{{ $c->id }}-nama" class="text-[0.95rem] font-semibold text-ink">{{ $nama }}</h3>
            @if ($dariAdmin)
                <span class="inline-flex items-center gap-1 rounded-md bg-forest-900 px-1.5 py-0.5 text-[0.7rem] font-semibold tracking-wide text-leaf uppercase">
                    {{ __('public.comments.admin_badge') }}
                </span>
            @endif
            @if ($milikSaya)
                <span class="rounded-md border border-forest-900/15 px-1.5 py-0.5 text-[0.7rem] font-medium text-forest-800">{{ __('public.comments.you') }}</span>
            @endif
            <span aria-hidden="true" class="text-ink/30">&middot;</span>
            <time datetime="{{ $c->created_at->toIso8601String() }}" title="{{ $c->created_at->translatedFormat('d F Y, H:i') }}" class="text-sm text-ink/60">
                {{ komentar_tanggal($c->created_at) }}, {{ $c->created_at->format('H:i') }}
            </time>
        </header>

        {{-- Reference to the comment being replied to --}}
        @if ($c->parent_id)
            <a href="{{ $induk ? '#komentar-'.$induk->id : '#komentar' }}"
                class="mt-2 block rounded-md border-l-2 border-forest-900/25 bg-forest-900/[0.03] px-3 py-2 text-sm text-ink/70 transition hover:border-forest-700 hover:bg-forest-900/[0.06]">
                @if ($induk)
                    <span class="flex items-center gap-1.5 text-xs font-semibold text-ink/80">
                        <i class="bi bi-reply text-ink/50" aria-hidden="true"></i>
                        {{ __('public.comments.replying_to') }} {{ $induk->isOwnedBy($viewer) ? __('public.comments.you') : Str::ucfirst($induk->authorName()) }}
                    </span>
                    <span class="mt-0.5 line-clamp-1">{{ $cuplikan($induk) }}</span>
                @else
                    <span class="italic">{{ __('public.comments.reply_unavailable') }}</span>
                @endif
            </a>
        @endif

        {{-- Body --}}
        <p @class([
            'mt-2 leading-relaxed break-words text-ink/90 [overflow-wrap:anywhere]',
            'opacity-70' => ! $terbit,
        ])>{!! komentar_teks($c->content) !!}</p>

        {{-- Moderation status (only the author sees unpublished comments) --}}
        @if ($milikSaya && ! $terbit)
            <p class="mt-3 flex items-start gap-2 rounded-md border border-clay/30 bg-clay/5 px-3 py-2 text-xs text-clay">
                <i class="bi {{ $c->status === \App\Models\Comment::STATUS_HIDDEN ? 'bi-eye-slash' : 'bi-hourglass-split' }} mt-px" aria-hidden="true"></i>
                <span>{{ $c->status === \App\Models\Comment::STATUS_HIDDEN ? __('public.comments.status_hidden') : __('public.comments.status_pending') }}</span>
            </p>
        @endif

        {{-- Actions --}}
        @if ($bisaBalas || $bisaLapor || $milikSaya)
            <div class="mt-2 -ml-2 flex flex-wrap items-center text-sm font-medium text-ink/60">
                @if ($bisaBalas)
                    <button type="button" class="inline-flex min-h-9 items-center gap-1.5 rounded-md px-2 transition hover:bg-forest-900/5 hover:text-forest-800"
                        @click="balas({{ $c->id }}, @js($milikSaya ? __('public.comments.you') : $nama), @js($cuplikan($c)))">
                        <i class="bi bi-reply" aria-hidden="true"></i> {{ __('public.comments.reply') }}
                    </button>
                @endif
                @if ($bisaLapor)
                    <button type="button" class="inline-flex min-h-9 items-center gap-1.5 rounded-md px-2 transition hover:bg-forest-900/5 hover:text-forest-800"
                        @click="bukaLaporan({{ $c->id }})">
                        <i class="bi bi-flag" aria-hidden="true"></i> {{ __('public.comments.report') }}
                    </button>
                @endif
                @if ($milikSaya)
                    <form method="POST" action="{{ route('komentar.destroy', $c) }}" @submit.prevent="hapus($el)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-9 items-center gap-1.5 rounded-md px-2 transition hover:bg-red-50 hover:text-red-700">
                            <i class="bi bi-trash3" aria-hidden="true"></i> {{ __('public.comments.delete') }}
                        </button>
                    </form>
                @endif
            </div>
        @endif
    </article>
</li>
