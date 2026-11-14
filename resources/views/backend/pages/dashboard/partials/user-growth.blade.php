@php
    $currentFilter = strtolower(request()->get('chart_filter_period', 'last_12_months'));
    $periods = [
        'last_12_months' => __('Last 12 months'),
        'this_year' => __('This year'),
        'last_year' => __('Last year'),
        'last_30_days' => __('Last 30 days'),
        'last_7_days' => __('Last 7 days'),
        'this_month' => __('This month'),
    ];
@endphp

<div class="h-full rounded-2xl border border-sand bg-white p-5 dark:border-white/10 dark:bg-forest-900/60 sm:p-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="font-display text-lg font-semibold text-forest-950 dark:text-white">{{ __('User Growth') }}</h2>
            <p class="text-sm text-ink/60 dark:text-white/60">{{ __('New accounts registered') }}</p>
        </div>

        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
            <button type="button" @click="open = !open" :aria-expanded="open"
                class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-sand bg-cream px-3 text-sm font-medium text-forest-900 transition-colors hover:bg-forest-50 dark:border-white/10 dark:bg-white/5 dark:text-white dark:hover:bg-white/10">
                <i class="bi bi-calendar-range" aria-hidden="true"></i>
                {{ $periods[$currentFilter] ?? $periods['last_12_months'] }}
                <i class="bi bi-chevron-down text-xs transition-transform" :class="open && 'rotate-180'" aria-hidden="true"></i>
            </button>
            <ul x-show="open" x-transition.origin.top.right style="display: none"
                class="absolute right-0 z-20 mt-2 w-48 rounded-xl border border-sand bg-white p-1.5 text-sm shadow-theme-lg dark:border-white/10 dark:bg-forest-900">
                @foreach ($periods as $key => $label)
                    <li>
                        <a href="{{ route('admin.dashboard', ['chart_filter_period' => $key]) }}"
                            @class([
                                'flex items-center justify-between rounded-lg px-3 py-2 transition-colors',
                                'bg-forest-50 font-semibold text-forest-800 dark:bg-white/10 dark:text-leaf' => $currentFilter === $key,
                                'text-ink/80 hover:bg-cream dark:text-white/80 dark:hover:bg-white/5' => $currentFilter !== $key,
                            ])>
                            {{ $label }}
                            @if ($currentFilter === $key)
                                <i class="bi bi-check2" aria-hidden="true"></i>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <div id="area-chart" class="mt-2 min-h-[300px]"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('area-chart');
        if (!el || typeof ApexCharts === 'undefined') return;

        const t = window.dashboardChartTheme();
        const stroke = t.dark ? '#b5d86a' : '#1f5f3d';

        new ApexCharts(el, {
            series: [{ name: @json(__('Users')), data: window.userGrowthData || [] }],
            chart: {
                type: 'area',
                height: 300,
                fontFamily: t.font,
                foreColor: t.text,
                background: 'transparent',
                toolbar: { show: false },
                zoom: { enabled: false },
            },
            colors: [stroke],
            stroke: { width: 3, curve: 'smooth', lineCap: 'round' },
            fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0, stops: [0, 95] } },
            markers: { size: 0, hover: { size: 5 } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: window.userGrowthLabels || [],
                axisBorder: { show: false },
                axisTicks: { show: false },
                tooltip: { enabled: false },
            },
            yaxis: { min: 0, labels: { formatter: (v) => Math.round(v) } },
            grid: { borderColor: t.grid, strokeDashArray: 4 },
            tooltip: { theme: t.dark ? 'dark' : 'light' },
        }).render();
    });
</script>
@endpush
