@extends('layouts.main')

@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/quill/quill.min.css') }}" />
@endpush

@section('content')
    <!-- Hero Section -->
    <section class="hero-section mb-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Berita Terkini</h1>
            <p class="lead">Informasi terbaru seputar kehutanan di wilayah Trenggalek</p>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row">
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a class="text-success text-decoration-none"
                                href="{{ route('home') }}">Beranda</a>
                        </li>
                        <li class="breadcrumb-item"><a class="text-success text-decoration-none"
                                href="{{ route('berita') }}">Berita</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 60, '...') }}
                        </li>
                    </ol>
                </nav>

                <!-- Post Content -->
                <article>
                    <h1 class="post-title mb-3">{{ $post->title }}</h1>

                    <div class="post-meta mb-4">
                        <span class="me-3"><i class="far fa-calendar-alt me-1"></i>
                            {{ $post->published_at?->translatedFormat('d F Y') }}</span>
                        <span class="me-3"><i class="far fa-user me-1"></i> Admin</span>
                        <span class="me-3"><i class="far fa-eye me-1"></i> {{ $post->views()->count() }} Kali
                            Dilihat</span>
                    </div>

                    <div class="post-image">
                        <img src="{{ $post->featured_image ?? asset('images/not-found/image.jpeg') }}"
                            alt="{{ $post->title }}" class="img-fluid w-100">
                    </div>

                    <div class="post-content mt-4 ql-editor p-0" style="white-space: normal!important">
                        {!! $post->content !!}
                    </div>

                    <div class="post-tags mt-5">
                        <h5 class="mb-3">Tag:</h5>
                        @foreach ($post->tags as $tag)
                            <span class="badge bg-secondary me-1">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                    <div class="post-tags share-section mt-5">
                        <h5 class="share-title"><i class="fas fa-share-alt me-2"></i>Bagikan Artikel Ini</h5>

                        <div class="row g-3">
                            @foreach ($share as $item => $value)
                                <div class="col-lg-2">
                                    <a class="share-btn btn-{{ $item }} text-decoration-none social-button d-flex align-items-center justify-content-center"
                                        target="_blank" href="{{ $value }}">
                                        <i class="fab fa-{{ $item }} me-2"></i>{{ Str::title($item) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <div class="share-divider">
                            <span class="share-divider-text"></span>
                        </div>
                    </div>
                </article>

                @if (session('success'))
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            Swal.fire({
                                icon: 'success',
                                title: '<span style="font-size: 1.4rem; font-weight: 600;">Komentar Terkirim</span>',
                                html: '<p style="font-size: 1rem; color: #555;">{{ session('success') }}</p>',
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#3085d6',
                                background: '#fefefe',
                                customClass: {
                                    popup: 'swal-custom-popup',
                                    confirmButton: 'swal-custom-button'
                                },
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showClass: {
                                    popup: 'animate__animated animate__fadeInDown'
                                },
                                hideClass: {
                                    popup: 'animate__animated animate__fadeOutUp'
                                }
                            });
                        });
                    </script>
                @endif

                <!-- Comments Section -->
                <div class="comment-section mt-5 p-4">
                    <h5 class="mb-4"><i class="far fa-comments me-2"></i>Komentar ({{ $comment->count() }})</h5>

                    <!-- Comment List -->
                    <div class="comment-list">
                        @forelse ($comment as $c)
                            <div class="comment mb-4 pb-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="comment-author">{{ Str::ucfirst($c->name) }}</span>
                                    <span class="comment-date">{{ $c->created_at->translatedFormat('d F Y') }} -
                                        {{ $c->created_at->translatedFormat('H:i') }}</span>
                                </div>
                                <div class="comment-text">
                                    {!! $c->content !!}
                                </div>
                            </div>
                        @empty
                            <span class="text-secondary">
                                Postingan ini belum memiliki komentar
                            </span>
                        @endforelse
                    </div>

                    <!-- Comment Form -->
                    <div class="comment-form mt-5" id="comment-form">
                        <h5 class="mb-3">Tinggalkan Komentar</h5>
                        <form method="POST" action="{{ route('comments.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror" id="name"
                                    value="{{ old('name') }}">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="comment" class="form-label">Komentar <span class="text-danger">*</span></label>
                                <div class="form-control @error('comment') is-invalid @enderror" id="comment"
                                    style="height: 150px;"></div>
                                <input type="hidden" name="comment" id="comment-content" value="{{ old('comment') }}">
                                @error('comment')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="post_id" value="{{ $post->id }}">

                            <button type="submit" class="btn btn-primary">Kirim Komentar</button>
                        </form>
                    </div>

                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-item bg-white p-4 mb-4 rounded shadow-sm">
                    <h5 class="sidebar-title">Postingan Terkini</h5>
                    <ul class="list-unstyled mt-3">
                        @foreach ($posts as $item)
                            <li class="mb-3 pb-2 border-bottom">
                                <a href="{{ route('berita.show', $item->slug) }}"
                                    class="text-decoration-none text-success">{{ $item->title }}</a>
                                <div class="text-muted small">{{ $item->published_at?->translatedFormat('d F Y') }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sidebar-item bg-white p-4 mb-4 rounded shadow-sm">
                    <h5 class="sidebar-title">Kategori</h5>
                    <ul class="list-unstyled mt-3">
                        @foreach ($kategori as $item)
                            <li class="mb-2"><a href="{{ route('berita', ['kategori' => $item->slug]) }}"
                                    class="text-decoration-none text-success text-end">{{ $item->name }}
                                    ({{ $item->posts_count }})
                                </a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="sidebar-item bg-white p-4 rounded shadow-sm">
                    <h5 class="sidebar-title">Kontak Kami</h5>
                    <div class="mt-3">
                        <p><i class="fas fa-map-marker-alt me-2 text-muted"></i> Gg. Menak Sopal, Sukobanteng, Karangsoko,
                            Kec. Trenggalek, Kabupaten Trenggalek, Jawa Timur 66319</p>
                        <p><i class="fas fa-envelope me-2 text-muted"></i> cdkwilayahtrenggalek@jatimprov.go.id</p>
                        <p><i class="fas fa-clock me-2 text-muted"></i> Senin - Jumat: 08.00 - 16.00 WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.quilljs.com/1.3.6/quill.bubble.css" />
    <link href="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.css" rel="stylesheet">
@endpush

@push('js')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/quill-emoji@0.1.7/dist/quill-emoji.js"></script>

    <script>
        var quill = new Quill('#comment', {
            theme: 'bubble',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    ['emoji']
                ],
                'emoji-toolbar': true,
                'emoji-textarea': false,
                'emoji-shortname': true
            }
        });

        // Kirim isi komentar ke input hidden saat submit
        document.querySelector('form').addEventListener('submit', function() {
            document.querySelector('#comment-content').value = quill.root.innerHTML;
        });
    </script>
@endpush
