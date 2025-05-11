<div class="{{ $class ?? 'bg-white' }} dark:bg-slate-800 w-full h-[152px] rounded-2xl border border-light-purple dark:border-slate-800 relative overflow-hidden z-10">
    <div class="p-6 pb-0">
        <p class="text-[#090909] dark:text-gray-100 text-sm font-medium">{{ $label }}</p>
    </div>

    <div class="absolute top-6 right-6">
        <button type="button" data-tooltip-target="data-tooltip" data-tooltip-placement="bottom"
            onclick="window.location.href='{{ $url ?? '#' }}'"
            class="hidden sm:inline-flex items-center justify-center text-gray-500 w-8 h-8 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm">
            <img src="{{ asset('/images/icons/move.svg') }}" class="dark:invert">
        </button>
    </div>
    <div class="absolute bottom-6 left-6">
        <div class="bg-white rounded-lg border border-[#EFEFFF] p-2.5 shadow-sm">
            <img src="{{ $icon ?? asset('images/icons/user.svg') }}" alt="">
        </div>
    </div>

    <div class="absolute bottom-6 left-20 text-[#090909] dark:text-gray-100 text-3xl font-medium">
        {{ $number ?? 0 }}
    </div>

    <div class="absolute -bottom-16 -right-16 w-40 h-40 rounded-full blur-3xl" style="background: {{ $bg ?? '' }};">
    </div>
</div>
