@extends('errors.layout')

@section('code', '404')
@section('icon', 'bi-signpost-split')
@section('title', 'Halaman tidak ditemukan')
@section('message', 'Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau alamatnya salah ketik.')

@section('actions')
    <a href="{{ url('/') }}" class="btn-primary !bg-forest-900 !text-white hover:!bg-forest-700">
        <i aria-hidden="true" class="bi bi-house-door"></i> Ke beranda
    </a>
    <a href="{{ route('berita') }}" class="btn-ghost">Lihat berita</a>
@endsection

@section('extra')
    <form action="{{ route('berita') }}" method="get" role="search" class="mx-auto mt-10 max-w-md text-left">
        <label for="cari-404" class="text-sm font-semibold">Atau cari berita</label>
        <div class="mt-2 flex rounded-full bg-white p-1.5 shadow-sm ring-1 ring-forest-900/15 focus-within:ring-[3px] focus-within:ring-forest-700">
            <input id="cari-404" type="search" name="search" placeholder="Kata kunci"
                class="min-w-0 flex-1 rounded-full bg-transparent px-4 text-ink placeholder:text-ink/50 focus:outline-none">
            <button type="submit" class="btn-primary !px-5 !py-2.5">
                <i aria-hidden="true" class="bi bi-search"></i> Cari
            </button>
        </div>
    </form>

    <nav aria-label="Tautan bantuan" class="mt-8">
        <ul class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm font-semibold text-forest-800">
            <li><a href="{{ route('tentang-kami') }}" class="inline-block py-1 underline-offset-4 hover:underline">Tentang Kami</a></li>
            <li><a href="{{ url('/') }}#layanan" class="inline-block py-1 underline-offset-4 hover:underline">Layanan</a></li>
            <li><a href="{{ route('kontak') }}" class="inline-block py-1 underline-offset-4 hover:underline">Kontak</a></li>
        </ul>
    </nav>
@endsection
