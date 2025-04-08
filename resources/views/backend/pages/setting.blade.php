@extends('backend.layouts.app')

@section('title')
    {{ __('Settings - ' . config('app.name')) }}
@endsection

@php
    $isActionLogExist = false;
@endphp
@section('admin-content')
    <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
        <div x-data="{ pageName: 'Settings' }">
            <!-- Page Header -->
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90" x-text="pageName">Settings</h2>

                <nav>
                    <ol class="flex items-center gap-1.5">
                        <li>
                            <a class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400"
                                href="{{ route('admin.dashboard') }}">
                                Home
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                        <li class="text-sm text-gray-800 dark:text-white/90" x-text="pageName">Settings</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Action Logs Table -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5">
                    @include('components.tabs', [
                        'tabs' => [
                            'profile' => [
                                'title' => 'General Tab',
                                'view' => 'backend.pages.settings.general-tab',
                                'data' => $settings,
                            ],
                            'settings' => [
                                'title' => 'Content Tab',
                                'view' => 'backend.pages.settings.content-settings',
                                'data' => $settings,
                            ],
                            'contacts' => [
                                'title' => 'Contacts',
                                'content' => '<p>This is contacts content.</p>', // Static HTML content
                            ],
                        ],
                    ])

                </div>
            </div>
            
        </div>

    </div>
@endsection


@if ($isActionLogExist)
@endif

@push('scripts')
@endpush
