{{--
    Chat-style comment thread for a news post.
    $post: Post, $comments: Collection<Comment> visible to the viewer (oldest first), $viewer: ?Commenter,
    $jumlahKomentar: number of live comments.
--}}
@php
    $visibleIds = $comments->pluck('id')->flip()->map(fn () => true)->all();
    $perHari = $comments->groupBy(fn ($c) => $c->created_at->toDateString());
    $halamanIni = '/'.ltrim(request()->path(), '/');
    $loginAktif = \App\Models\Commenter::signInAvailable();
    $alasan = [
        'spam' => __('public.comments.reason_spam'),
        'abusive' => __('public.comments.reason_abusive'),
        'sara' => __('public.comments.reason_sara'),
        'other' => __('public.comments.reason_other'),
    ];
@endphp

<section id="komentar" class="mt-16 scroll-mt-24" aria-labelledby="judul-komentar"
    x-data="komentarChat({
        jumlah: {{ $jumlahKomentar }},
        token: @js(csrf_token()),
        urlLapor: @js(route('komentar.report', ['comment' => '__ID__'])),
        t: {
            gagal: @js(__('public.comments.failed')),
            sesiHabis: @js(__('public.comments.session_expired')),
            konfirmasiHapus: @js(__('public.comments.confirm_delete')),
        },
    })">
    <div class="flex flex-wrap items-end justify-between gap-4 border-b border-forest-900/20 pb-5">
        <div>
            <h2 id="judul-komentar" class="font-display text-2xl font-semibold sm:text-3xl">
                {{ __('public.news.comments') }} <span class="text-forest-700" x-text="jumlah">{{ $jumlahKomentar }}</span>
            </h2>
            <p class="mt-2 text-sm text-ink/70">{{ __('public.comments.intro') }}</p>
        </div>

        @if ($viewer)
            <div class="flex items-center gap-2.5 text-sm">
                @if ($viewer->avatar)
                    <img src="{{ $viewer->avatar }}" alt="" width="32" height="32" referrerpolicy="no-referrer" class="size-8 rounded-full bg-sand object-cover">
                @else
                    <span aria-hidden="true" class="grid size-8 place-items-center rounded-full bg-forest-900 text-xs font-semibold text-leaf">{{ $viewer->initial() }}</span>
                @endif
                <span class="text-ink/75">{!! __('public.comments.signed_in_as', ['name' => '<strong class="font-semibold text-ink">'.e($viewer->name).'</strong>']) !!}</span>
                <form method="POST" action="{{ route('komentar.logout') }}">
                    @csrf
                    <input type="hidden" name="kembali" value="{{ $halamanIni }}#komentar">
                    <button type="submit" class="inline-flex min-h-11 items-center px-1 font-semibold text-forest-800 underline-offset-4 hover:underline">{{ __('public.comments.logout') }}</button>
                </form>
            </div>
        @elseif ($loginAktif)
            @include('public.partials.comment-google-button', ['kembali' => $halamanIni.'#komentar-tulis', 'kecil' => true])
        @endif
    </div>

    @if (session('komentar_success'))
        <p role="status" class="mt-5 flex items-start gap-3 rounded-xl bg-forest-900 px-4 py-3 text-sm text-white">
            <i aria-hidden="true" class="bi bi-check-circle-fill mt-0.5 text-leaf"></i> {{ session('komentar_success') }}
        </p>
    @endif
    @if (session('komentar_error'))
        <p role="alert" class="mt-5 flex items-start gap-3 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800">
            <i aria-hidden="true" class="bi bi-exclamation-circle-fill mt-0.5"></i> {{ session('komentar_error') }}
        </p>
    @endif

    {{-- Thread --}}
    <div class="mt-6 rounded-2xl bg-sand/45 px-3 py-5 sm:px-6 sm:py-6">
        <div x-show="kosong" @if ($comments->isNotEmpty()) x-cloak @endif class="flex flex-col items-center py-10 text-center">
            <span aria-hidden="true" class="grid size-14 place-items-center rounded-full bg-white text-2xl text-forest-700 shadow-sm">
                <i class="bi bi-chat-heart"></i>
            </span>
            <p class="mt-4 font-semibold">{{ __('public.comments.empty') }}</p>
            <p class="mt-1 text-sm text-ink/70">{{ __('public.comments.empty_hint') }}</p>
        </div>

        <ol x-ref="thread" class="space-y-4" aria-label="{{ __('public.news.comments') }}">
            @foreach ($perHari as $tanggal => $isiHari)
                <li class="komentar-pemisah flex justify-center" role="presentation">
                    <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-medium text-ink/70 shadow-sm">{{ komentar_tanggal($isiHari->first()->created_at) }}</span>
                </li>
                @foreach ($isiHari as $c)
                    @include('public.partials.comment-bubble', ['c' => $c, 'viewer' => $viewer, 'visibleIds' => $visibleIds])
                @endforeach
            @endforeach
        </ol>
        <template x-ref="pemisah">
            <li class="komentar-pemisah flex justify-center" role="presentation">
                <span class="rounded-full bg-white/80 px-3 py-1 text-xs font-medium text-ink/70 shadow-sm"></span>
            </li>
        </template>
    </div>

    {{-- Status for screen readers and a short visible confirmation --}}
    <p class="sr-only" aria-live="polite" x-text="pengumuman"></p>

    {{-- Composer --}}
    {{-- Only the message box sticks to the screen bottom, like a chat; the sign-in card stays in place. --}}
    <div id="komentar-tulis" @class([
        'relative z-10 mt-4 scroll-mt-24',
        'sticky bottom-3' => $viewer && ! $viewer->isBlocked(),
    ])>
        @if (! $viewer)
            <div class="flex flex-col items-center gap-4 rounded-2xl border border-forest-900/15 bg-white px-5 py-5 text-center shadow-sm sm:flex-row sm:text-left">
                <span aria-hidden="true" class="grid size-11 shrink-0 place-items-center rounded-full bg-forest-50 text-xl text-forest-700"><i class="bi bi-chat-dots"></i></span>
                <div class="flex-1">
                    <p class="font-semibold">{{ __('public.comments.signin_prompt') }}</p>
                    @if ($loginAktif)
                        <p class="mt-0.5 text-sm text-ink/70">{{ __('public.comments.signin_note') }}</p>
                    @else
                        <p id="komentar-login-nonaktif" class="mt-0.5 text-sm text-clay">{{ __('public.comments.unavailable') }}</p>
                    @endif
                </div>
                @include('public.partials.comment-google-button', ['kembali' => $halamanIni.'#komentar-tulis', 'aktif' => $loginAktif])
            </div>
        @elseif ($viewer->isBlocked())
            <p role="status" class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800 shadow-sm">
                <i class="bi bi-slash-circle mr-1" aria-hidden="true"></i> {{ __('public.comments.blocked') }}
            </p>
        @else
            <form x-ref="form" method="POST" action="{{ route('komentar.store', $post->id) }}" @submit.prevent="kirim()"
                class="rounded-2xl border border-forest-900/15 bg-white p-2 shadow-lg shadow-forest-950/10 focus-within:border-forest-700">
                @csrf
                <input type="hidden" name="parent_id" :value="balasan ? balasan.id : ''">

                {{-- Reply preview --}}
                <div x-cloak x-show="balasan" x-transition class="mx-1 mt-1 mb-2 flex items-start gap-3 rounded-xl border-l-[3px] border-forest-600 bg-forest-50 px-3 py-2 text-sm">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold text-forest-800">{{ __('public.comments.replying_to') }} <span x-text="balasan?.nama"></span></p>
                        <p class="truncate text-ink/70" x-text="balasan?.cuplikan"></p>
                    </div>
                    <button type="button" @click="balasan = null; $refs.isi.focus()" class="grid size-8 shrink-0 place-items-center rounded-full text-ink/60 hover:bg-white hover:text-ink">
                        <i class="bi bi-x-lg" aria-hidden="true"></i><span class="sr-only">{{ __('public.comments.cancel_reply') }}</span>
                    </button>
                </div>

                <div class="flex items-end gap-2">
                    <label for="komentar-isi" class="sr-only">{{ __('public.comments.placeholder') }}</label>
                    <textarea id="komentar-isi" x-ref="isi" name="comment" rows="1" maxlength="1000" required
                        x-model="isi" @input="tumbuh($el)" @keydown.enter="tombolEnter($event)"
                        :aria-invalid="galat ? 'true' : null" aria-describedby="komentar-galat komentar-catatan"
                        placeholder="{{ __('public.comments.placeholder') }}"
                        class="max-h-48 min-h-11 flex-1 resize-none border-0 bg-transparent px-3 py-2.5 text-ink placeholder:text-ink/50 focus:ring-0 focus:outline-none">{{ old('comment') }}</textarea>
                    <button type="submit" :disabled="mengirim || !isi.trim()"
                        class="grid size-11 shrink-0 place-items-center rounded-full bg-forest-900 text-lg text-white transition hover:bg-forest-700 active:scale-95 disabled:cursor-not-allowed disabled:bg-forest-900/30">
                        <i class="bi" :class="mengirim ? 'bi-arrow-repeat animate-spin' : 'bi-send-fill'" aria-hidden="true"></i>
                        <span class="sr-only" x-text="mengirim ? @js(__('public.comments.sending')) : @js(__('public.comments.send'))">{{ __('public.comments.send') }}</span>
                    </button>
                </div>

                <p id="komentar-galat" x-cloak x-show="galat" x-text="galat" role="alert" class="mx-3 mb-1 text-sm font-medium text-red-700">
                    @error('comment'){{ $message }}@enderror
                </p>
                @error('comment')<span x-init="galat = @js($message)"></span>@enderror

                <div id="komentar-catatan" class="flex flex-wrap items-center justify-between gap-x-4 gap-y-1 px-3 pt-1 pb-1 text-xs text-ink/60">
                    <p>
                        {{ __('public.comments.notice') }}
                        <a href="{{ route('kebijakan-privasi') }}" class="font-semibold text-forest-800 underline underline-offset-2">{{ __('public.form.privacy_link') }}</a>
                    </p>
                    <p x-show="sisa <= 200" x-cloak :class="sisa <= 50 && 'font-semibold text-clay'" x-text="@js(__('public.comments.chars_left', ['count' => '__N__'])).replace('__N__', sisa)"></p>
                </div>
            </form>
        @endif

        <p x-cloak x-show="pesanTerlihat" x-transition.opacity
            class="pointer-events-none absolute -top-12 left-1/2 flex -translate-x-1/2 items-center gap-2 rounded-full bg-forest-950 px-4 py-2 text-sm whitespace-nowrap text-white shadow-lg">
            <i class="bi bi-check-circle-fill text-leaf" aria-hidden="true"></i> <span x-text="pengumuman"></span>
        </p>
    </div>

    {{-- Report dialog --}}
    @if ($viewer && ! $viewer->isBlocked())
        <div x-cloak x-show="laporan.buka" x-transition.opacity.duration.150ms
            class="fixed inset-0 z-50 flex items-end justify-center bg-forest-950/40 p-4 backdrop-blur-sm sm:items-center"
            @keydown.escape.window="laporan.buka = false" @click.self="laporan.buka = false">
            <form x-show="laporan.buka" x-trap.inert.noscroll="laporan.buka" @submit.prevent="kirimLaporan()"
                role="dialog" aria-modal="true" aria-labelledby="laporan-judul"
                class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 id="laporan-judul" class="font-display text-xl font-semibold">{{ __('public.comments.report_title') }}</h3>
                        <p class="mt-1 text-sm text-ink/70">{{ __('public.comments.report_text') }}</p>
                    </div>
                    <button type="button" @click="laporan.buka = false" class="grid size-9 shrink-0 place-items-center rounded-full text-ink/60 hover:bg-forest-50 hover:text-ink">
                        <i class="bi bi-x-lg" aria-hidden="true"></i><span class="sr-only">{{ __('public.comments.cancel') }}</span>
                    </button>
                </div>
                <fieldset class="mt-5 space-y-2">
                    <legend class="sr-only">{{ __('public.comments.report_title') }}</legend>
                    @foreach ($alasan as $kunci => $label)
                        <label class="flex min-h-11 cursor-pointer items-center gap-3 rounded-xl border border-forest-900/15 px-4 py-2 has-[:checked]:border-forest-700 has-[:checked]:bg-forest-50">
                            <input type="radio" name="reason" value="{{ $kunci }}" x-model="laporan.alasan" class="size-4 text-forest-700 focus:ring-forest-700">
                            <span>{{ $label }}</span>
                        </label>
                    @endforeach
                </fieldset>
                <p x-show="laporan.galat" x-text="laporan.galat" role="alert" class="mt-3 text-sm font-medium text-red-700"></p>
                <div class="mt-6 flex justify-end gap-2">
                    <button type="button" @click="laporan.buka = false" class="inline-flex min-h-11 items-center rounded-full px-5 font-semibold text-ink/75 hover:bg-forest-50">{{ __('public.comments.cancel') }}</button>
                    <button type="submit" :disabled="laporan.mengirim" class="inline-flex min-h-11 items-center gap-2 rounded-full bg-forest-900 px-5 font-semibold text-white hover:bg-forest-700 disabled:opacity-60">
                        <i class="bi bi-flag-fill" aria-hidden="true"></i> {{ __('public.comments.report_send') }}
                    </button>
                </div>
            </form>
        </div>
    @endif
</section>
