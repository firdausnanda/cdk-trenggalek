@if (\App\Support\StatisticsConsent::choice(request()) === null)
    <aside aria-labelledby="judul-cookie" class="cookie-banner fixed right-4 bottom-20 left-4 z-[54] max-h-[calc(100dvh-6rem)] overflow-y-auto rounded-xl border border-forest-900/15 bg-cream p-5 text-ink shadow-[0_12px_35px_rgb(11_33_24/0.18)] sm:right-6 sm:bottom-6 sm:left-20 sm:p-6 lg:left-1/2 lg:w-[min(68rem,calc(100vw-8rem))] lg:-translate-x-1/2">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between lg:gap-10">
            <div class="min-w-0">
                <h2 id="judul-cookie" class="font-display text-lg font-semibold">Pilihan Cookie</h2>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-ink/80">
                    Situs ini menggunakan cookie yang diperlukan agar formulir berfungsi. Cookie tambahan untuk menghitung pembaca berita hanya digunakan dengan persetujuan Anda. Anda dapat menerima atau menolaknya.
                    <a href="{{ route('kebijakan-privasi') }}#cookie" class="font-semibold text-forest-800 underline underline-offset-4">Kebijakan cookie</a>.
                </p>
            </div>
            <form method="POST" action="{{ route('cookie-consent.store') }}" class="grid shrink-0 grid-cols-2 gap-2">
                @csrf
                <input type="hidden" name="return_to" value="{{ request()->getRequestUri() }}">
                <button type="submit" name="choice" value="reject" class="min-h-11 cursor-pointer rounded-lg border border-forest-900 px-4 py-2 text-sm font-semibold text-forest-900 transition hover:bg-forest-50 active:bg-forest-100">Tolak Cookie</button>
                <button type="submit" name="choice" value="accept" class="min-h-11 cursor-pointer rounded-lg bg-forest-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-forest-700 active:bg-forest-800">Terima Cookie</button>
            </form>
        </div>
    </aside>
@endif
