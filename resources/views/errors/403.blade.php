@extends('errors.layout')

@section('code', '403')
@section('icon', 'bi-shield-lock')
@section('title', 'Akses ditolak')
@section('message')
    @auth
        Akun Anda tidak memiliki izin untuk membuka halaman ini. Hubungi administrator bila Anda memerlukan akses.
    @else
        Halaman ini hanya dapat dibuka oleh pengguna yang berwenang. Silakan masuk dengan akun yang sesuai.
    @endauth
@endsection

@section('actions')
    @auth
        @if (Route::has('admin.dashboard'))
            <a href="{{ route('admin.dashboard') }}" class="btn-primary !bg-forest-900 !text-white hover:!bg-forest-700">
                <i aria-hidden="true" class="bi bi-speedometer2"></i> Ke dashboard
            </a>
        @endif
    @else
        @if (Route::has('admin.login'))
            <a href="{{ route('admin.login') }}" class="btn-primary !bg-forest-900 !text-white hover:!bg-forest-700">
                <i aria-hidden="true" class="bi bi-box-arrow-in-right"></i> Masuk
            </a>
        @endif
    @endauth
    <a href="{{ url('/') }}" class="btn-ghost"><i aria-hidden="true" class="bi bi-house-door"></i> Ke beranda</a>
@endsection
