@extends('errors.layout')

@section('code', '500')
@section('icon', 'bi-tools')
@section('title', 'Terjadi gangguan pada server')
@section('message', 'Maaf, ada kesalahan di sisi kami. Silakan muat ulang halaman ini beberapa saat lagi.')

@section('actions')
    <button type="button" onclick="location.reload()" class="btn-primary !bg-forest-900 !text-white hover:!bg-forest-700">
        <i aria-hidden="true" class="bi bi-arrow-clockwise"></i> Muat ulang
    </button>
    <a href="{{ url('/') }}" class="btn-ghost"><i aria-hidden="true" class="bi bi-house-door"></i> Ke beranda</a>
@endsection
