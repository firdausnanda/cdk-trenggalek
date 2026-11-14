<div class="h-full rounded-2xl border border-sand bg-white p-5 dark:border-white/10 dark:bg-forest-900/60 sm:p-6">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="font-display text-lg font-semibold text-forest-950 dark:text-white">{{ __('Users History') }}</h2>
            <p class="text-sm text-ink/60 dark:text-white/60">{{ __('New vs. existing accounts') }}</p>
        </div>
        <a href="{{ route('admin.users.index') }}"
            class="shrink-0 text-sm font-semibold text-forest-700 hover:text-forest-600 dark:text-leaf">{{ __('All') }}</a>
    </div>

    <div id="donut-chart" class="mt-2"></div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const el = document.getElementById('donut-chart');
        if (!el || typeof ApexCharts === 'undefined') return;

        const newUsers = @json($user_history_data['new_users'] ?? 0);
        const oldUsers = @json($user_history_data['old_users'] ?? 0);
        const usersLabel = @json(__('users'));
        const t = window.dashboardChartTheme();

        new ApexCharts(el, {
            series: [oldUsers, newUsers],
            labels: [@json(__('Old Users (before 1 month)')), @json(__('New Users (last 30 days)'))],
            colors: [t.dark ? '#2f7a50' : '#1f5f3d', '#b5d86a'],
            chart: { type: 'donut', height: 300, fontFamily: t.font, foreColor: t.text, background: 'transparent' },
            stroke: { width: 3, colors: [t.dark ? '#123b28' : '#ffffff'] },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', markers: { size: 6, shape: 'circle' } },
            tooltip: { theme: t.dark ? 'dark' : 'light', y: { formatter: (v) => v + ' ' + usersLabel } },
            plotOptions: {
                pie: {
                    donut: {
                        size: '74%',
                        labels: {
                            show: true,
                            name: { offsetY: 22, color: t.text },
                            value: {
                                offsetY: -14,
                                fontSize: '28px',
                                fontWeight: 600,
                                color: t.dark ? '#ffffff' : '#0b2118',
                                formatter: (v) => v,
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: @json(__('Total')) + ' ' + usersLabel,
                                color: t.text,
                                formatter: (w) => w.globals.seriesTotals.reduce((a, b) => a + b, 0),
                            },
                        },
                    },
                },
            },
        }).render();
    });
</script>
@endpush
