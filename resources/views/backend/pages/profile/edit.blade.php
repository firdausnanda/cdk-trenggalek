@extends('backend.layouts.app')

@section('title')
    {{ $breadcrumbs['title'] }} | {{ config('app.name') }}
@endsection

@section('admin-content')
    <div class="p-4 mx-auto max-w-7xl md:p-6">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        {!! ld_apply_filters('profile_edit_breadcrumbs', '') !!}

        <div class="space-y-6">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-5 py-2.5 sm:px-6 sm:py-5">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white">{{ __('Edit Profile') }} -
                        {{ $user->name }}
                    </h3>
                </div>
                <div class="p-5 space-y-6 border-t border-gray-100 dark:border-gray-800 sm:p-6">
                    <x-messages />
                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                        @method('PUT')
                        @csrf
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Name') }}</label>
                                <input type="text" name="name" id="name" required value="{{ $user->name }}"
                                    class="form-control">
                            </div>
                            <div>
                                <label for="email"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Email') }}</label>
                                <input type="email" name="email" id="email" required value="{{ $user->email }}"
                                    class="form-control">
                            </div>
                            <div>
                                <label for="password"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Password (Optional)') }}</label>
                                <input type="password" name="password" id="password" class="form-control" minlength="8"
                                    pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&^_\-]).{8,}"
                                    title="Minimal 8 karakter, harus mengandung huruf besar, huruf kecil, angka, dan simbol">
                                <!-- Password Strength Indicator -->
                                <div id="password-requirements" class="mt-2 space-y-1 text-xs hidden">
                                    <p id="req-length" class="text-red-500"><span class="mr-1">✗</span> Minimal 8 karakter
                                    </p>
                                    <p id="req-lowercase" class="text-red-500"><span class="mr-1">✗</span> Huruf kecil (a-z)
                                    </p>
                                    <p id="req-uppercase" class="text-red-500"><span class="mr-1">✗</span> Huruf besar (A-Z)
                                    </p>
                                    <p id="req-number" class="text-red-500"><span class="mr-1">✗</span> Angka (0-9)</p>
                                    <p id="req-symbol" class="text-red-500"><span class="mr-1">✗</span> Simbol (@$!%*#?&^_-)
                                    </p>
                                </div>
                            </div>
                            <div>
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-400">{{ __('Confirm Password (Optional)') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                    class="form-control">
                            </div>
                            {!! ld_apply_filters('profile_edit_fields', '', $user) !!}
                        </div>
                        {!! ld_apply_filters('profile_edit_after_fields', '', $user) !!}
                        <div class="mt-6 flex justify-start gap-4">
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600">{{ __('Save') }}</button>
                            <a href="{{ route('admin.dashboard') }}"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 dark:bg-gray-700 dark:text-white">
                                <i class="bi bi-x-circle mr-1"></i>
                                {{ __('Cancel') }}</a>
                        </div>
                        {!! ld_apply_filters('profile_edit_fields', '', $user) !!}
                </div>
                {!! ld_apply_filters('profile_edit_after_fields', '', $user) !!}
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const requirements = document.getElementById('password-requirements');

            const rules = [
                { id: 'req-length', test: v => v.length >= 8 },
                { id: 'req-lowercase', test: v => /[a-z]/.test(v) },
                { id: 'req-uppercase', test: v => /[A-Z]/.test(v) },
                { id: 'req-number', test: v => /[0-9]/.test(v) },
                { id: 'req-symbol', test: v => /[@$!%*#?&^_\-]/.test(v) },
            ];

            passwordInput.addEventListener('input', function () {
                const val = this.value;
                requirements.classList.toggle('hidden', val.length === 0);

                rules.forEach(function (rule) {
                    const el = document.getElementById(rule.id);
                    const passed = rule.test(val);
                    el.className = passed ? 'text-green-500' : 'text-red-500';
                    el.querySelector('span').textContent = passed ? '✓' : '✗';
                });
            });
        });
    </script>
@endpush