<div class="flex h-full flex-col rounded-2xl border border-sand bg-white p-5 dark:border-white/10 dark:bg-forest-900/60 sm:p-6">
    <div class="flex items-start justify-between gap-3">
        <div>
            <h2 class="font-display text-lg font-semibold text-forest-950 dark:text-white">{{ __('Most read news') }}</h2>
            <p class="text-sm text-ink/60 dark:text-white/60">{{ __('Based on all-time views') }}</p>
        </div>
        <a href="{{ route('admin.posts.index', 'post') }}"
            class="shrink-0 text-sm font-semibold text-forest-700 hover:text-forest-600 dark:text-leaf">{{ __('All') }}</a>
    </div>

    @if ($popular_posts->isEmpty())
        <div class="flex flex-1 flex-col items-center justify-center py-10 text-center">
            <span class="flex size-12 items-center justify-center rounded-full bg-forest-50 text-forest-700 dark:bg-white/5 dark:text-leaf">
                <i class="bi bi-newspaper text-xl" aria-hidden="true"></i>
            </span>
            <p class="mt-3 text-sm text-ink/60 dark:text-white/60">{{ __('No published news yet.') }}</p>
        </div>
    @else
        @php $maxViews = max(1, $popular_posts->max('views_count')); @endphp
        <ol class="mt-5 flex flex-1 flex-col gap-1">
            @foreach ($popular_posts as $i => $post)
                <li>
                    <a href="{{ route('admin.posts.edit', [$post->post_type, $post->id]) }}"
                        class="group flex items-center gap-3 rounded-xl p-2 -mx-2 transition-colors hover:bg-cream dark:hover:bg-white/5">
                        <span class="w-5 shrink-0 text-center font-display text-sm font-semibold text-ink/40 tabular-nums dark:text-white/40">{{ $i + 1 }}</span>
                        @if ($post->featured_image)
                            <img src="{{ asset($post->featured_image) }}" alt="" loading="lazy"
                                class="size-11 shrink-0 rounded-lg object-cover bg-sand">
                        @else
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-lg bg-forest-50 text-forest-700 dark:bg-white/5 dark:text-leaf">
                                <i class="bi bi-image" aria-hidden="true"></i>
                            </span>
                        @endif
                        <span class="min-w-0 flex-1">
                            <span class="line-clamp-2 text-sm font-medium leading-snug text-forest-950 group-hover:text-forest-700 dark:text-white dark:group-hover:text-leaf">{{ $post->title }}</span>
                            <span class="mt-1.5 flex items-center gap-2">
                                <span class="h-1 flex-1 overflow-hidden rounded-full bg-sand dark:bg-white/10">
                                    <span class="block h-full rounded-full bg-forest-600 dark:bg-leaf" style="width: {{ round($post->views_count / $maxViews * 100) }}%"></span>
                                </span>
                                <span class="shrink-0 text-xs text-ink/55 tabular-nums dark:text-white/55">{{ number_format($post->views_count) }} <i class="bi bi-eye" aria-label="{{ __('views') }}"></i></span>
                            </span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ol>
    @endif
</div>
