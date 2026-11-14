<div class="h-full rounded-2xl border border-sand bg-white p-5 dark:border-white/10 dark:bg-forest-900/60 sm:p-6">
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h2 class="font-display text-lg font-semibold text-forest-950 dark:text-white">{{ __('Post Activity') }}</h2>
            <p class="text-sm text-ink/60 dark:text-white/60">{{ __('Posts created over time') }}</p>
        </div>
    </div>
    <div id="post-activity-chart" class="mt-2 min-h-[300px]"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.querySelector('#post-activity-chart');
        if (!el || typeof ApexCharts === 'undefined') return;

        const postData = @json($post_stats);
        const t = window.dashboardChartTheme();

        new ApexCharts(el, {
            series: [
                { name: @json(__('Published')), data: postData.published },
                { name: @json(__('Draft')), data: postData.draft },
            ],
            chart: {
                type: 'bar',
                height: 300,
                stacked: true,
                fontFamily: t.font,
                foreColor: t.text,
                background: 'transparent',
                toolbar: { show: false },
            },
            colors: [t.dark ? '#b5d86a' : '#1f5f3d', t.dark ? 'rgba(255,255,255,0.25)' : '#d8cfb6'],
            plotOptions: { bar: { columnWidth: '42%', borderRadius: 6, borderRadiusApplication: 'end', borderRadiusWhenStacked: 'last' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: postData.labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
            },
            yaxis: { labels: { formatter: (v) => Math.round(v) } },
            grid: { borderColor: t.grid, strokeDashArray: 4 },
            legend: { position: 'top', horizontalAlign: 'right', markers: { size: 6, shape: 'circle' } },
            tooltip: {
                theme: t.dark ? 'dark' : 'light',
                y: { formatter: (v) => v + ' ' + @json(__('posts')) },
            },
        }).render();
    });
</script>
@endpush
