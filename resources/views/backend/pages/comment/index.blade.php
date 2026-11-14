@extends('backend.layouts.app')

@section('title')
    {{ $breadcrumbs['title'] }} | {{ config('app.name') }}
@endsection

@php
    $statusLabel = [
        'published' => ['label' => __('Live'), 'class' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300', 'icon' => 'bi-broadcast'],
        'hidden' => ['label' => __('Hidden'), 'class' => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-200', 'icon' => 'bi-eye-slash'],
        'pending_review' => ['label' => __('Held by filter'), 'class' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300', 'icon' => 'bi-funnel'],
    ];
    $reasonLabel = [
        'spam' => __('Spam'),
        'abusive' => __('Abusive'),
        'sara' => __('SARA / hate'),
        'other' => __('Other'),
    ];
    $tabs = [
        null => ['label' => __('All'), 'count' => null],
        'needs_review' => ['label' => __('Needs review'), 'count' => $counts['needs_review']],
        'reported' => ['label' => __('Reported'), 'count' => $counts['reported']],
        'pending_review' => ['label' => __('Held by filter'), 'count' => $counts['pending_review']],
        'hidden' => ['label' => __('Hidden'), 'count' => $counts['hidden']],
        'published' => ['label' => __('Live'), 'count' => null],
    ];
    $input = 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30';
@endphp

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6" x-data="{ selectedComments: [], selectAll: false, bulkDeleteModalOpen: false, settingsOpen: {{ $errors->has('report_threshold') || $errors->has('blocked_words') ? 'true' : 'false' }} }">

        <x-breadcrumbs :breadcrumbs="$breadcrumbs"></x-breadcrumbs>

        {!! ld_apply_filters('comments_after_breadcrumbs', '') !!}

        <div class="space-y-6">
            {{-- How moderation works + settings --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p class="font-medium text-gray-800 dark:text-white/90">{{ __('Comments go live right away') }}</p>
                        <p class="mt-1">{{ __('Visitors sign in with Google. A comment is hidden automatically after :count reports, and comments with links or filtered words wait for review.', ['count' => $reportThreshold]) }}</p>
                    </div>
                    @can('comment.edit')
                        <button type="button" class="btn-default shrink-0 flex items-center gap-2 text-sm" @click="settingsOpen = !settingsOpen" :aria-expanded="settingsOpen.toString()">
                            <i class="bi bi-sliders"></i> {{ __('Moderation settings') }}
                        </button>
                    @endcan
                </div>
                @can('comment.edit')
                    <form x-cloak x-show="settingsOpen" method="POST" action="{{ route('admin.comment.settings') }}"
                        class="grid gap-6 border-t border-gray-100 p-5 sm:p-6 md:grid-cols-3 dark:border-gray-800">
                        @csrf
                        @method('PUT')
                        <div class="md:col-span-2">
                            <label for="blocked_words" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Filtered words') }}</label>
                            <textarea id="blocked_words" name="blocked_words" rows="6" class="{{ $input }}" placeholder="{{ __('One word or phrase per line') }}">{{ old('blocked_words', $blockedWords) }}</textarea>
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('A new comment containing one of these (whole word, any letter case) waits for review instead of going live.') }}</p>
                            @error('blocked_words')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="report_threshold" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Reports before hiding') }}</label>
                            <input id="report_threshold" type="number" name="report_threshold" min="1" max="50" required value="{{ old('report_threshold', $reportThreshold) }}" class="{{ $input }}">
                            <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Reports from different visitors needed to hide a comment until you review it.') }}</p>
                            @error('report_threshold')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            <button type="submit" class="btn-primary mt-5 w-full justify-center">{{ __('Save') }}</button>
                        </div>
                    </form>
                @endcan
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                {{-- Header --}}
                <div class="px-5 py-4 sm:px-6 sm:py-5 flex gap-3 md:gap-1 flex-col md:flex-row justify-between items-center">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white/90 hidden md:block">
                        {{ __('Comment') }}
                    </h3>

                    @include('backend.partials.search-form', [
                        'placeholder' => __('Search by commenter name or email'),
                    ])

                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center" x-show="selectedComments.length > 0">
                            <button type="button" class="btn-danger flex items-center justify-center gap-2 text-sm" @click="bulkDeleteModalOpen = true">
                                <i class="bi bi-trash"></i>
                                <span>{{ __('Delete Selected') }} (<span x-text="selectedComments.length"></span>)</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Filter tabs --}}
                <nav class="flex gap-1 overflow-x-auto border-t border-gray-100 px-4 sm:px-5 dark:border-gray-800" aria-label="{{ __('Filter comments') }}">
                    @foreach ($tabs as $key => $tab)
                        @php $active = ($filter ?? '') === ($key ?? ''); @endphp
                        <a href="{{ route('admin.comment.index', array_filter(['status' => $key ?: null, 'search' => request('search')])) }}"
                            @if ($active) aria-current="page" @endif
                            class="inline-flex shrink-0 items-center gap-2 border-b-2 px-3 py-3 text-sm font-medium {{ $active ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                            {{ $tab['label'] }}
                            @if ($tab['count'])
                                <span class="rounded-full px-2 py-0.5 text-xs {{ $key === 'needs_review' ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}">{{ $tab['count'] }}</span>
                            @endif
                        </a>
                    @endforeach
                </nav>

                {{-- Table --}}
                <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto overflow-y-visible">
                    <table id="dataTable" class="w-full dark:text-gray-400">
                        <thead class="bg-light text-capitalize">
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th width="4%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <input type="checkbox" aria-label="{{ __('Select all') }}"
                                        class="form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary"
                                        x-model="selectAll"
                                        @click="selectAll = !selectAll; selectedComments = selectAll ? [...document.querySelectorAll('.comment-checkbox')].map(cb => cb.value) : [];">
                                </th>
                                <th width="22%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Author') }}</th>
                                <th width="38%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Comment') }}</th>
                                <th width="14%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Status') }}</th>
                                <th width="22%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($comments as $comment)
                                @php
                                    $status = $statusLabel[$comment->status] ?? $statusLabel['hidden'];
                                    $reasons = $comment->reports->countBy('reason');
                                    $blocked = $comment->commenter?->isBlocked();
                                @endphp
                                <tr class="border-b border-gray-100 align-top dark:border-gray-800 {{ $comment->status !== 'published' || $comment->reports_count ? 'bg-amber-50/40 dark:bg-amber-900/5' : '' }}">
                                    <td class="px-5 py-4 sm:px-6">
                                        <input type="checkbox" aria-label="{{ __('Select') }}"
                                            class="comment-checkbox form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary"
                                            value="{{ $comment->id }}" x-model="selectedComments">
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex items-start gap-3">
                                            @if ($comment->authorAvatar())
                                                <img src="{{ $comment->authorAvatar() }}" alt="" referrerpolicy="no-referrer" class="size-9 shrink-0 rounded-full object-cover">
                                            @else
                                                <span class="grid size-9 shrink-0 place-items-center rounded-full bg-gray-100 text-sm font-semibold text-gray-700 dark:bg-gray-800 dark:text-gray-200">
                                                    @if ($comment->isFromAdmin())<i class="bi bi-patch-check-fill text-primary"></i>@else{{ $comment->authorInitial() }}@endif
                                                </span>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-800 dark:text-white/90">{{ Str::limit($comment->authorName(), 28) }}</p>
                                                <p class="truncate text-xs text-gray-500">{{ $comment->authorEmail() }}</p>
                                                <p class="mt-1 flex flex-wrap gap-1 text-[0.7rem]">
                                                    @if ($comment->isFromAdmin())
                                                        <span class="rounded bg-blue-100 px-1.5 py-0.5 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">{{ __('Admin reply') }} · {{ $comment->user?->name }}</span>
                                                    @elseif ($comment->commenter)
                                                        <span class="rounded bg-gray-100 px-1.5 py-0.5 text-gray-700 dark:bg-gray-800 dark:text-gray-300"><i class="bi bi-google"></i> Google</span>
                                                    @else
                                                        <span class="rounded bg-gray-100 px-1.5 py-0.5 text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ __('Old form') }}</span>
                                                    @endif
                                                    @if ($blocked)
                                                        <span class="rounded bg-red-100 px-1.5 py-0.5 text-red-800 dark:bg-red-900/40 dark:text-red-300"><i class="bi bi-slash-circle"></i> {{ __('Blocked') }}</span>
                                                    @endif
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500">{{ $comment->created_at->format('d M Y H:i') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="min-w-64 px-5 py-4 sm:px-6">
                                        @if ($comment->parent)
                                            <p class="mb-1.5 border-l-2 border-gray-300 pl-2 text-xs text-gray-500 line-clamp-1 dark:border-gray-600">
                                                <i class="bi bi-reply"></i> {{ $comment->parent->authorName() }}: {{ Str::limit(strip_tags($comment->parent->content), 70) }}
                                            </p>
                                        @endif
                                        <p class="text-sm text-gray-800 line-clamp-4 dark:text-gray-300">{!! komentar_teks($comment->content) !!}</p>
                                        @if ($comment->post)
                                            <a href="{{ route('berita.show', $comment->post->slug) }}#komentar-{{ $comment->id }}" target="_blank" rel="noopener" class="mt-1 inline-block text-xs text-blue-500 hover:underline">{{ Str::limit($comment->post->title, 60) }} <i class="bi bi-box-arrow-up-right"></i></a>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium {{ $status['class'] }}">
                                            <i class="bi {{ $status['icon'] }}"></i> {{ $status['label'] }}
                                        </span>
                                        @if ($comment->reports_count)
                                            <p class="mt-2 text-xs font-medium text-red-700 dark:text-red-400"><i class="bi bi-flag-fill"></i> {{ trans_choice(':count report|:count reports', $comment->reports_count, ['count' => $comment->reports_count]) }}</p>
                                            <ul class="mt-1 space-y-0.5 text-xs text-gray-500">
                                                @foreach ($reasons as $reason => $total)
                                                    <li>{{ $reasonLabel[$reason] ?? $reason }} ({{ $total }})</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex flex-wrap gap-2" x-data="{ replyOpen: false, deleteModalOpen: false }">
                                            @can('comment.edit')
                                                @if ($comment->status !== 'published' || $comment->reports_count)
                                                    <form action="{{ route('admin.comment.publish', $comment) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn-action btn-approve" title="{{ $comment->reports_count ? __('Keep it live and dismiss the reports') : '' }}">
                                                            <i class="bi bi-check-circle"></i> {{ $comment->status === 'published' ? __('Keep') : __('Show') }}
                                                        </button>
                                                    </form>
                                                @endif
                                                @if ($comment->status === 'published')
                                                    <form action="{{ route('admin.comment.hide', $comment) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="btn-action btn-unapprove"><i class="bi bi-eye-slash"></i> {{ __('Hide') }}</button>
                                                    </form>
                                                @endif
                                            @endcan

                                            @can('comment.create')
                                                @if ($comment->status === 'published' && $comment->post)
                                                    <button type="button" class="btn-action btn-default" @click="replyOpen = true"><i class="bi bi-reply"></i> {{ __('Reply') }}</button>
                                                @endif
                                            @endcan

                                            @can('comment.edit')
                                                @if ($comment->commenter)
                                                    <form action="{{ route($blocked ? 'admin.commenters.unblock' : 'admin.commenters.block', $comment->commenter) }}" method="POST"
                                                        @if (! $blocked) onsubmit="return confirm(@js(__('Block :name? They will not be able to comment, and their comments will no longer be shown.', ['name' => $comment->commenter->name])))" @endif>
                                                        @csrf
                                                        <button type="submit" class="btn-action btn-default"><i class="bi {{ $blocked ? 'bi-unlock' : 'bi-slash-circle' }}"></i> {{ $blocked ? __('Unblock') : __('Block') }}</button>
                                                    </form>
                                                @endif
                                            @endcan

                                            @can('comment.delete')
                                                <x-buttons.action-item type="modal-trigger" modal-target="deleteModalOpen"
                                                    icon="trash" :label="__('Delete')" class="btn-action btn-delete" />

                                                <x-modals.confirm-delete id="delete-modal-{{ $comment->id }}"
                                                    title="{{ __('Delete Comment') }}"
                                                    content="{{ __('Are you sure you want to delete this comment?') }}"
                                                    formId="delete-form-{{ $comment->id }}"
                                                    formAction="{{ route('admin.comment.destroy', $comment->id) }}"
                                                    modalTrigger="deleteModalOpen" cancelButtonText="{{ __('No, cancel') }}"
                                                    confirmButtonText="{{ __('Yes, Confirm') }}" />
                                            @endcan

                                            {{-- Reply modal --}}
                                            <div x-cloak x-show="replyOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="replyOpen"
                                                x-on:keydown.esc.window="replyOpen = false" x-on:click.self="replyOpen = false"
                                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md" role="dialog" aria-modal="true">
                                                <form method="POST" action="{{ route('admin.comment.reply', $comment) }}" class="w-full max-w-lg rounded-lg border border-gray-100 bg-white dark:border-gray-600 dark:bg-gray-700">
                                                    @csrf
                                                    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3 dark:border-gray-600">
                                                        <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('Reply as :name', ['name' => config('cdk.nama_singkat')]) }}</h3>
                                                        <button type="button" @click="replyOpen = false" class="text-gray-400 hover:text-gray-900 dark:hover:text-white"><i class="bi bi-x-lg"></i></button>
                                                    </div>
                                                    <div class="space-y-3 p-4">
                                                        <p class="rounded-lg border-l-4 border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300">
                                                            <strong>{{ $comment->authorName() }}:</strong> {{ Str::limit(strip_tags($comment->content), 200) }}
                                                        </p>
                                                        <textarea name="content" rows="4" maxlength="1000" required class="{{ $input }}" placeholder="{{ __('Write a reply…') }}"></textarea>
                                                        <p class="text-xs text-gray-500">{{ __('The reply is published right away with an official badge.') }}</p>
                                                    </div>
                                                    <div class="flex justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-600">
                                                        <button type="button" @click="replyOpen = false" class="btn-default">{{ __('Cancel') }}</button>
                                                        <button type="submit" class="btn-primary"><i class="bi bi-send"></i> {{ __('Post reply') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8">
                                        <p class="text-gray-500">{{ $filter === 'needs_review' ? __('Nothing needs review.') : __('No comments found') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="my-4 px-4 sm:px-6">
                        {{ $comments->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Bulk Delete Modal --}}
        <div x-cloak x-show="bulkDeleteModalOpen" x-transition.opacity.duration.200ms
            x-trap.inert.noscroll="bulkDeleteModalOpen" x-on:keydown.esc.window="bulkDeleteModalOpen = false"
            x-on:click.self="bulkDeleteModalOpen = false"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md" role="dialog">
            <div class="flex max-w-md flex-col gap-4 overflow-hidden rounded-lg border border-gray-100 bg-white dark:bg-gray-700">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2 dark:border-gray-600">
                    <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('Delete Selected Comments') }}</h3>
                    <button x-on:click="bulkDeleteModalOpen = false" class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="px-4 text-center">
                    <p class="text-gray-500">
                        {{ __('Are you sure you want to delete the selected comments? This action cannot be undone.') }}
                    </p>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-gray-100 p-4 dark:border-gray-600">
                    <form id="bulk-delete-form" action="{{ route('admin.comment.bulk-delete') }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <template x-for="id in selectedComments" :key="id">
                            <input type="hidden" name="ids[]" :value="id">
                        </template>

                        <button type="button" x-on:click="bulkDeleteModalOpen = false" class="btn-default">
                            {{ __('No, Cancel') }}
                        </button>

                        <button type="submit" class="btn-danger">
                            {{ __('Yes, Delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
