@extends('backend.layouts.app')

@section('title')
    {{ $breadcrumbs['title'] }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6" x-data="{ selectedComments: [], selectAll: false, bulkDeleteModalOpen: false }">

        {{-- Breadcrumb --}}
        <x-breadcrumbs :breadcrumbs="$breadcrumbs"></x-breadcrumbs>

        {!! ld_apply_filters('comments_after_breadcrumbs', '') !!}

        <div class="space-y-6">
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
                        {{-- Bulk Actions --}}
                        <div class="flex items-center justify-center" x-show="selectedComments.length > 0">
                            <button id="bulkActionsButton" data-dropdown-toggle="bulkActionsDropdown"
                                class="btn-danger flex items-center justify-center gap-2 text-sm" type="button">
                                <i class="bi bi-trash"></i>
                                <span>{{ __('Bulk Actions') }} (<span x-text="selectedComments.length"></span>)</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>

                            {{-- Bulk Dropdown --}}
                            <div id="bulkActionsDropdown"
                                class="z-10 hidden w-48 p-3 bg-white rounded-lg shadow dark:bg-gray-700">
                                <h6 class="mb-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('Bulk Actions') }}
                                </h6>
                                <ul class="space-y-2">
                                    <li class="cursor-pointer text-sm text-red-600 dark:text-red-400 hover:bg-gray-200 dark:hover:bg-gray-600 px-2 py-1 rounded"
                                        @click="bulkDeleteModalOpen = true">
                                        <i class="bi bi-trash mr-1"></i> {{ __('Delete Selected') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="space-y-3 border-t border-gray-100 dark:border-gray-800 overflow-x-auto overflow-y-visible">
                    <table id="dataTable" class="w-full dark:text-gray-400">
                        <thead class="bg-light text-capitalize">
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th width="5%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary"
                                            x-model="selectAll"
                                            @click="
                                        selectAll = !selectAll;
                                        selectedComments = selectAll ? 
                                            [...document.querySelectorAll('.comment-checkbox')].map(cb => cb.value) : 
                                            [];
                                    ">
                                    </div>
                                </th>
                                <th width="20%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    {{ __('Author') }}
                                </th>
                                <th width="20%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    {{ __('Email') }}
                                </th>
                                <th width="35%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    {{ __('Comment') }}
                                </th>
                                <th width="20%" class="p-2 bg-gray-50 dark:bg-gray-800 dark:text-white text-left px-5">
                                    {{ __('Action') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($comments as $comment)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4 sm:px-6">
                                        <input type="checkbox"
                                            class="comment-checkbox form-checkbox h-4 w-4 text-primary border-gray-300 rounded focus:ring-primary"
                                            value="{{ $comment->id }}" x-model="selectedComments">
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ Str::limit($comment->name, 20) }}</span>
                                            <span
                                                class="text-xs text-gray-500">{{ $comment->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        {{ $comment->email }}
                                    </td>
                                    <td class="px-5 py-4 sm:px-6">
                                        <span class="line-clamp-2">{!! $comment->content !!}</span>
                                        <a href="{{ route('berita.show', ['slug' => $comment->post->slug]) }}" target="_blank" class="text-xs text-blue-500">{{ Str::limit($comment->post->title, 50) }}</a>
                                    </td>
                                    <td class="px-5 py-4 sm:px-6 flex gap-2">
                                        @if (!$comment->is_approved)
                                            <form action="{{ route('admin.comment.approve', $comment->id) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="btn-action btn-approve">
                                                    <i class="bi bi-check-circle"></i> Approve
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.comment.unapprove', $comment->id) }}"
                                                method="POST" class="inline-block">
                                                @csrf
                                                <button type="submit" class="btn-action btn-unapprove">
                                                    <i class="bi bi-x-circle"></i> Unapprove
                                                </button>
                                            </form>
                                        @endif

                                        <div x-data="{ deleteModalOpen: false }">
                                            <x-buttons.action-item type="modal-trigger" modal-target="deleteModalOpen"
                                                icon="trash" :label="__('Delete')" class="btn-action btn-delete" />

                                            <x-modals.confirm-delete id="delete-modal-{{ $comment->id }}"
                                                title="{{ __('Delete Comment') }}"
                                                content="{{ __('Are you sure you want to delete this comment?') }}"
                                                formId="delete-form-{{ $comment->id }}"
                                                formAction="{{ route('admin.comment.destroy', $comment->id) }}"
                                                modalTrigger="deleteModalOpen" cancelButtonText="{{ __('No, cancel') }}"
                                                confirmButtonText="{{ __('Yes, Confirm') }}" />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-gray-500">{{ __('No comments found') }}</p>
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
            <div
                class="flex max-w-md flex-col gap-4 overflow-hidden rounded-lg border border-gray-100 bg-white dark:bg-gray-700">
                <div class="flex items-center justify-between border-b border-gray-100 px-4 py-2 dark:border-gray-600">
                    <h3 class="font-semibold text-gray-800 dark:text-white">{{ __('Delete Selected Comments') }}</h3>
                    <button x-on:click="bulkDeleteModalOpen = false"
                        class="text-gray-400 hover:text-gray-900 dark:hover:text-white">
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

    @push('scripts')
        <script>
            function handleRoleFilter(value) {
                let currentUrl = new URL(window.location.href);

                // Preserve sort parameter if it exists.
                const sortParam = currentUrl.searchParams.get('sort');

                // Reset the search params but keep the sort if it exists.
                currentUrl.search = '';

                if (value) {
                    currentUrl.searchParams.set('role', value);
                }

                // Re-add sort parameter if it existed.
                if (sortParam) {
                    currentUrl.searchParams.set('sort', sortParam);
                }

                window.location.href = currentUrl.toString();
            }
        </script>
    @endpush
@endsection
