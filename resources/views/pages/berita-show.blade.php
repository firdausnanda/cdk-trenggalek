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
                            {{ $post->published_at?->format('d M Y') }}</span>
                        <span class="me-3"><i class="far fa-user me-1"></i> Admin</span>
                        <span class="me-3"><i class="far fa-eye me-1"></i> {{ $post->views()->count() }} Kali
                            Dilihat</span>
                    </div>

                    <div class="post-image">
                        <img src="{{ $post->featured_image ?? asset('images/not-found/image.jpeg') }}"
                            alt="{{ $post->title }}" class="img-fluid w-100">
                    </div>

                    <div class="post-content mt-4 ql-editor" style="white-space: normal!important">
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

                <!-- Comments Section -->
                {{-- <div class="comment-section mt-5 p-4">
                    <h5 class="mb-4"><i class="far fa-comments me-2"></i>Komentar (3)</h5>

                    <!-- Comment List -->
                    <div class="comment-list">
                        <!-- Comment 1 -->
                        <div class="comment mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="comment-author">Supriyadi</span>
                                <span class="comment-date">12 Juni 2023 - 14:30</span>
                            </div>
                            <div class="comment-text">
                                Program yang sangat bermanfaat untuk lingkungan kita. Kapan akan dilakukan lagi di daerah
                                lain?
                            </div>
                        </div>

                        <!-- Comment 2 -->
                        <div class="comment mb-4 pb-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="comment-author">Karyono</span>
                                <span class="comment-date">13 Juni 2023 - 09:15</span>
                            </div>
                            <div class="comment-text">
                                Saya sebagai warga Pule sangat mendukung kegiatan ini. Semoga bisa berkelanjutan dan
                                memberikan dampak positif bagi lingkungan kami.
                            </div>
                        </div>

                        <!-- Comment 3 -->
                        <div class="comment mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="comment-author">Rahayu</span>
                                <span class="comment-date">14 Juni 2023 - 16:45</span>
                            </div>
                            <div class="comment-text">
                                Apakah ada program untuk adopsi pohon? Saya ingin ikut berkontribusi dalam perawatan pohon
                                yang telah ditanam.
                            </div>
                        </div>
                    </div>

                    <!-- Comment Form -->
                    <div class="comment-form mt-5">
                        <h5 class="mb-3">Tinggalkan Komentar</h5>
                        <form>
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="comment" class="form-label">Komentar</label>
                                <textarea class="form-control" id="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Kirim Komentar</button>
                        </form>
                    </div>
                </div> --}}
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
                                <div class="text-muted small">{{ $item->published_at?->format('d M Y') }}</div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sidebar-item bg-white p-4 mb-4 rounded shadow-sm">
                    <h5 class="sidebar-title">Kategori</h5>
                    <ul class="list-unstyled mt-3">
                        @foreach ($kategori as $item)
                            <li class="mb-2"><a href="{{ route('berita', ['kategori' => $item->slug]) }}"
                                    class="text-decoration-none text-success text-end">{{ $item->name }}</a></li>
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
