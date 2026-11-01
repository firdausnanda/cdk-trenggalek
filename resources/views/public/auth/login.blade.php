@extends('public.layouts.auth')

@section('title', 'Masuk · Panel Admin · ' . config('settings.app_name'))

@php
    $field = 'w-full rounded-xl border-0 bg-white px-4 py-3 text-ink ring-1 ring-forest-900/15 placeholder:text-ink/45 focus:ring-[3px] focus:ring-forest-700 focus:outline-none';
@endphp

@section('content')
    <h1 class="font-display text-3xl font-bold tracking-[-0.025em] sm:text-4xl">Masuk</h1>
    <p class="mt-2 text-ink/70">Masukkan email dan kata sandi akun admin Anda.</p>

    @if (session('error') || $errors->any())
        <div role="alert" class="mt-6 flex items-start gap-3 rounded-xl bg-red-50 px-4 py-3 text-sm text-red-800 ring-1 ring-red-200">
            <i aria-hidden="true" class="bi bi-exclamation-triangle-fill mt-0.5"></i>
            <div>
                @if (session('error'))
                    <p>{{ session('error') }}</p>
                @endif
                @foreach ($errors->all() as $pesan)
                    <p>{{ $pesan }}</p>
                @endforeach
            </div>
        </div>
    @endif

    @if (session('success'))
        <p role="status" class="mt-6 flex items-start gap-3 rounded-xl bg-forest-900 px-4 py-3 text-sm text-white">
            <i aria-hidden="true" class="bi bi-check-circle-fill mt-0.5 text-leaf"></i> {{ session('success') }}
        </p>
    @endif

    <form action="{{ route('admin.login.submit') }}" method="POST" class="mt-8 space-y-5" novalidate>
        @csrf

        <div>
            <label for="email" class="text-sm font-semibold">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                placeholder="nama@jatimprov.go.id" maxlength="50"
                @error('email') aria-invalid="true" @enderror class="mt-2 {{ $field }}">
        </div>

        <div x-data="{ tampil: false }">
            <div class="flex items-center justify-between gap-4">
                <label for="password" class="text-sm font-semibold">Kata sandi</label>
                <a href="{{ route('admin.password.request') }}" class="py-1 text-sm font-semibold text-forest-800 underline-offset-4 hover:underline">Lupa kata sandi?</a>
            </div>
            <div class="relative mt-2">
                <input id="password" :type="tampil ? 'text' : 'password'" type="password" name="password" required autocomplete="current-password"
                    @error('password') aria-invalid="true" @enderror class="{{ $field }} pr-14">
                <button type="button" @click="tampil = !tampil" :aria-pressed="tampil.toString()"
                    class="absolute inset-y-0 right-1.5 my-auto grid size-11 place-items-center rounded-lg text-lg text-ink/60 transition hover:text-forest-800">
                    <i aria-hidden="true" class="bi" :class="tampil ? 'bi-eye-slash' : 'bi-eye'"></i>
                    <span class="sr-only">Tampilkan kata sandi</span>
                </button>
            </div>
        </div>

        <label class="flex min-h-11 w-fit cursor-pointer items-center gap-3 text-sm">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))
                class="size-5 rounded accent-forest-700">
            Ingat saya di perangkat ini
        </label>

        <button type="submit" class="btn-primary w-full justify-center !bg-forest-900 !py-3.5 !text-white hover:!bg-forest-700">
            Masuk <i aria-hidden="true" class="bi bi-box-arrow-in-right"></i>
        </button>
    </form>

    @if (config('app.demo_mode', false))
        <div class="mt-8 rounded-xl bg-sand/70 p-5 text-sm ring-1 ring-forest-900/10">
            <p class="font-semibold">Akun demo</p>
            <dl class="mt-3 grid grid-cols-[auto_1fr] gap-x-4 gap-y-1 text-ink/80">
                <dt>Email</dt><dd><code>superadmin@example.com</code></dd>
                <dt>Kata sandi</dt><dd><code>12345678</code></dd>
            </dl>
            <button type="button" id="isi-akun-demo" class="btn-ghost mt-4 !px-4 !py-2 text-sm">Masuk dengan akun demo</button>
        </div>
    @endif
@endsection

@if (config('app.demo_mode', false))
    @push('scripts')
        <script>
            document.getElementById('isi-akun-demo').addEventListener('click', function () {
                document.getElementById('email').value = 'superadmin@example.com';
                document.getElementById('password').value = '12345678';
                this.closest('main').querySelector('form').submit();
            });
        </script>
    @endpush
@endif
