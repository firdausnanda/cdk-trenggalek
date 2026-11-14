@extends('backend.layouts.app')

@section('title')
   {{ $breadcrumbs['title'] }} | {{ config('app.name') }}
@endsection

@section('before_vite_build')
    <script>
        var userGrowthData = @json($user_growth_data['data']);
        var userGrowthLabels = @json($user_growth_data['labels']);
    </script>
@endsection

@php
    $hour = (int) now('Asia/Jakarta')->format('G'); // app timezone is UTC; greet in local (WIB) time
    $greeting = match (true) {
        $hour < 11 => __('Good morning'),
        $hour < 15 => __('Good afternoon'),
        $hour < 18 => __('Good evening'),
        default => __('Good night'),
    };
    $firstName = \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->first();
    $canCreatePost = auth()->user()->can('post.create');
@endphp

@section('admin-content')
    <div class="mx-auto max-w-(--breakpoint-2xl) space-y-6 p-4 md:p-6">
        {!! ld_apply_filters('dashboard_after_breadcrumbs', '') !!}

        {{-- Welcome banner --}}
        <section class="relative isolate overflow-hidden rounded-3xl bg-forest-950 px-6 py-8 text-white sm:px-8 md:py-10">
            <img src="{{ asset('images/background/bg.avif') }}" alt="" class="absolute inset-0 -z-20 size-full object-cover opacity-60">
            <div aria-hidden="true" class="absolute inset-0 -z-10 bg-gradient-to-r from-forest-950 via-forest-950/85 to-forest-900/40"></div>

            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold tracking-[0.18em] text-leaf uppercase">{{ __('Admin panel') }}</p>
                    <h1 class="mt-3 font-display text-3xl font-bold tracking-[-0.02em] text-balance md:text-4xl">
                        {{ $greeting }}, {{ $firstName }}.
                    </h1>
                    <p class="mt-2 text-white/75">
                        {{ __('Here is a summary of activity on the Cabang Dinas Kehutanan Wilayah Trenggalek website.') }}
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @if ($canCreatePost)
                        <a href="{{ route('admin.posts.create', 'post') }}"
                            class="inline-flex min-h-11 items-center gap-2 rounded-full bg-leaf px-5 text-sm font-semibold text-forest-950 transition hover:bg-[#c6e486]">
                            <i class="bi bi-pencil-square" aria-hidden="true"></i> {{ __('Write news') }}
                        </a>
                    @endif
                    <a href="{{ route('home') }}" target="_blank" rel="noopener"
                        class="inline-flex min-h-11 items-center gap-2 rounded-full border border-white/25 bg-white/10 px-5 text-sm font-semibold text-white backdrop-blur transition hover:bg-white/20">
                        <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i> {{ __('View website') }}
                    </a>
                </div>
            </div>
        </section>

        {{-- Content stats --}}
        <section aria-label="{{ __('Content summary') }}" class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
            @include('backend.pages.dashboard.partials.card', [
                'icon' => 'bi bi-newspaper',
                'tone' => 'forest',
                'label' => __('Published news'),
                'value' => number_format($content_stats['published_posts']),
                'hint' => __('+:count this month', ['count' => number_format($content_stats['posts_this_month'])]),
                'url' => route('admin.posts.index', 'post'),
            ])
            @include('backend.pages.dashboard.partials.card', [
                'icon' => 'bi bi-eye',
                'tone' => 'leaf',
                'label' => __('News views'),
                'value' => number_format($content_stats['total_views']),
                'hint' => __(':count this month', ['count' => number_format($content_stats['views_this_month'])]),
            ])
            @include('backend.pages.dashboard.partials.card', [
                'icon' => 'bi bi-chat-left-text',
                'tone' => 'clay',
                'label' => __('Comments needing review'),
                'value' => number_format($content_stats['pending_comments']),
                'hint' => __('of :count comments', ['count' => number_format($content_stats['total_comments'])]),
                'url' => route('admin.comment.index', ['status' => 'needs_review']),
            ])
            @include('backend.pages.dashboard.partials.card', [
                'icon' => 'bi bi-envelope',
                'tone' => 'sky',
                'label' => __('Contact messages'),
                'value' => number_format($content_stats['contact_messages']),
                'hint' => __(':count this month', ['count' => number_format($content_stats['messages_this_month'])]),
            ])
        </section>

        {{-- System stats (kept compact; module hooks still attach here) --}}
        <section aria-label="{{ __('System summary') }}" class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-4">
            {!! ld_apply_filters('dashboard_cards_before_users', '') !!}
            @include('backend.pages.dashboard.partials.card', [
                'icon' => 'bi bi-people',
                'label' => __('Users'),
                'value' => $total_users,
                'url' => route('admin.users.index'),
                'class' => 'p-4! gap-3!',
            ])
            {!! ld_apply_filters('dashboard_cards_after_users', '') !!}
            @include('backend.pages.dashboard.partials.card', [
                'icon' => 'bi bi-person-badge',
                'label' => __('Roles'),
                'value' => $total_roles,
                'url' => route('admin.roles.index'),
                'class' => 'p-4! gap-3!',
            ])
            {!! ld_apply_filters('dashboard_cards_after_roles', '') !!}
            @include('backend.pages.dashboard.partials.card', [
                'icon' => 'bi bi-shield-check',
                'label' => __('Permissions'),
                'value' => $total_permissions,
                'url' => route('admin.permissions.index'),
                'class' => 'p-4! gap-3!',
            ])
            {!! ld_apply_filters('dashboard_cards_after_permissions', '') !!}
            @include('backend.pages.dashboard.partials.card', [
                'icon' => 'bi bi-translate',
                'label' => __('Languages (total / active)'),
                'value' => $languages['total'] . ' / ' . $languages['active'],
                'url' => route('admin.translations.index'),
                'class' => 'p-4! gap-3!',
            ])
            {!! ld_apply_filters('dashboard_cards_after_translations', '') !!}
        </section>

        {!! ld_apply_filters('dashboard_cards_after', '') !!}

        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12 xl:col-span-8">
                @include('backend.pages.dashboard.partials.post-chart')
            </div>
            <div class="col-span-12 xl:col-span-4">
                @include('backend.pages.dashboard.partials.popular-posts')
            </div>
        </div>

        <div class="grid grid-cols-12 gap-4 md:gap-6">
            <div class="col-span-12 xl:col-span-8">
                @include('backend.pages.dashboard.partials.user-growth')
            </div>
            <div class="col-span-12 xl:col-span-4">
                @include('backend.pages.dashboard.partials.user-history')
            </div>
        </div>

        {!! ld_apply_filters('dashboard_after', '') !!}
    </div>
@endsection

@push('scripts')
    <script>
        // Shared chart look for the dashboard (forest palette, follows light/dark mode).
        window.dashboardChartTheme = function () {
            // The layout applies the `dark` class on DOMContentLoaded, possibly after charts render, so also read the saved preference.
            let saved = null;
            try { saved = localStorage.getItem('darkMode'); } catch (e) {}
            const dark = document.documentElement.classList.contains('dark') || saved === 'true';
            return {
                dark,
                text: dark ? 'rgba(255,255,255,0.6)' : 'rgba(22,32,26,0.6)',
                grid: dark ? 'rgba(255,255,255,0.08)' : '#ebe3cf',
                font: '"Inter Variable", ui-sans-serif, system-ui, sans-serif',
            };
        };
    </script>
@endpush
