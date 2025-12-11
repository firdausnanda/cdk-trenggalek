@extends('layouts.main')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section mb-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Berita Terkini</h1>
            <p class="lead">Informasi terbaru seputar kehutanan di wilayah Trenggalek</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Berita Terbaru</h2>
            </div>
            <div class="col-md-4">
                <form class="d-flex" action="{{ route('berita') }}" method="get">
                    @csrf
                    <input class="form-control me-2" type="search" placeholder="Cari berita..." name="search">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-3 mb-4">

            @forelse ($posts as $post)
                <div class="col-md-6 col-lg-4">
                    <div class="card card-news h-100">
                        <div class="position-relative">
                            <img loading="lazy" src="{{ $post->featured_image ? $post->featured_image : asset('images/not-found/image.jpeg') }}"
                                class="card-img-top news-img" alt="{{ $post->title }}">
                            <span class="news-category">{{ $post->categories?->first()?->name ?? 'Uncategorized' }}</span>
                        </div>
                        <div class="card-body">
                            <small class="news-date"><i class="far fa-calendar-alt"></i>
                                {{ $post->published_at?->translatedFormat('d F Y') }}</small>
                            <a href="{{ route('berita.show', $post->slug) }}" class="text-decoration-none text-dark">
                                <h5 class="card-title mt-2">{{ $post->title }}</h5>
                            </a>
                            <p class="card-text">{!! $post->excerpt !!}</p>
                            <a href="{{ route('berita.show', $post->slug) }}" class="btn btn-primary">Baca Selengkapnya</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Berita tidak ditemukan
                    </div>
                </div>
            @endforelse

        </div>

        <!-- Pagination -->
        {{ $posts->links('pagination::bootstrap-5') }}
    </div>
@endsection

@push('css')
    <style>
        .pagination .page-link {
            color: #0a5c36;
            /* warna teks */
        }

        .pagination .page-link:hover {
            color: white;
            background-color: #0a5c36;
            /* warna hover */
        }

        .pagination .page-item.active .page-link {
            background-color: #0a5c36;
            border-color: #0a5c36;
            color: white;
        }

        .pagination .page-link:focus {
            box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, .25);
        }
    </style>
@endpush
