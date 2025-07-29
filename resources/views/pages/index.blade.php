@extends('layouts.main')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-3 fw-bold mb-4">Cabang Dinas Kehutanan Wilayah Trenggalek</h1>
            <p class="lead mb-5">Melindungi, Mengelola, dan Melestarikan Hutan untuk Masa Depan</p>
            <a href="#layanan" class="btn btn-primary btn-lg px-4">Pelayanan Kami</a>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section id="tentang" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="section-title text-center    ">Tentang Kami</h2>
                    <p>Cabang Dinas Kehutanan Wilayah Trenggalek berdasarkan Pergub No. 48 Tahun 2018 memiliki 4 wilayah
                        kerja
                        meliputi Kabupaten Trenggalek, Kabupaten Tulungagung, Kabupaten Kediri dan Kota Kediri. Kami
                        memiliki tugas
                        melaksanakan pengelolaan hutan, rehabilitasi lahan, perlindungan kawasan hutan, serta
                        pemberdayaan
                        masyarakat desa hutan.</p>
                    <p>Kami menyediakan layanan perizinan kehutanan, pengawasan, dan penanganan pengaduan masyarakat.
                        Bersama
                        seluruh pemangku kepentingan, kami berkomitmen mewujudkan pengelolaan hutan berkelanjutan untuk
                        kesejahteraan masyarakat.</p>
                    <a href="{{ route('tentang-kami') }}" class="btn btn-outline-success">Selengkapnya</a>
                </div>
                <div class="col-lg-6">
                    <img src="{{ asset('images/background/bg-1.jpg') }}"
                        alt="Team Dinas Kehutanan Wilayah Trenggalek sedang bekerja di lapangan"
                        class="img-fluid rounded shadow news-card">
                </div>
            </div>
        </div>
    </section>

    <!-- Layanan Section -->
    <section id="layanan" class="py-5 bg-light">
        <div class="container mt-5">
            <h2 class="section-title text-center">Layanan Kami</h2>
            <p class="text-center mb-5">Berbagai layanan yang kami sediakan untuk masyarakat Trenggalek</p>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card card-service h-100 text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-tree"></i>
                        </div>
                        <h4>Izin Pemanfaatan Hutan</h4>
                        <p class="mb-3">Pelayanan perizinan pemanfaatan hasil hutan kayu dan bukan kayu sesuai
                            peraturan yang
                            berlaku.</p>
                        <a href="{{ route('kontak') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            Hubungi Kami
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-service h-100 text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-seedling"></i>
                        </div>
                        <h4>Rehabilitasi Kawasan Hutan</h4>
                        <p class="mb-3">Program rehabilitasi kawasan hutan dan lahan kritis untuk pemulihan ekosistem.
                        </p>
                        <a href="{{ route('kontak') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            Hubungi Kami
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-service h-100 text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h4>Pengawasan & Pengendalian</h4>
                        <p class="mb-3">Pengawasan dan pengendalian terhadap pemanfaatan hutan secara berkelanjutan.
                        </p>
                        <a href="{{ route('kontak') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            Hubungi Kami
                        </a>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-service h-100 text-center p-4">
                        <div class="service-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h4>Pemberdayaan Masyarakat</h4>
                        <p class="mb-3">Program pemberdayaan masyarakat sekitar hutan untuk kesejahteraan bersama.
                        </p>
                        <a href="{{ route('kontak') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Berita Section -->
    <section id="berita" class="py-5">
        <div class="container">
            <h2 class="section-title text-center">Berita Terkini</h2>
            <p class="text-center mb-5">Update terbaru seputar Dinas Kehutanan Wilayah Trenggalek</p>

            <div class="row g-4">
                @forelse ($posts as $post)
                    <div class="col-md-6 col-lg-4">
                        <div class="card news-card h-100">
                            <img src="{{ $post->featured_image ?? asset('images/not-found/image.jpeg') }}"
                                class="card-img-top img-fluid" style="height: 400px; object-fit: cover; width: 100%;"
                                alt="{{ $post->title }}">
                            <div class="card-body">
                                <a href="{{ route('berita.show', $post->slug) }}" class="text-decoration-none text-dark">
                                    <h5 class="card-title">{{ $post->title }}</h5>
                                </a>
                                <p class="card-text">{!! $post->excerpt !!}</p>
                                <a href="{{ route('berita.show', $post->slug) }}" class="btn btn-sm btn-primary">Baca
                                    Selengkapnya</a>
                            </div>
                            <div class="card-footer text-muted">
                                <small>Diposting: {{ $post->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Tidak ada berita terkini
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($posts->count() > 0)
                <div class="text-center mt-4">
                    <a href="{{ route('berita') }}" class="btn btn-outline-success">Lihat Semua Berita</a>
                </div>
            @endif
        </div>
    </section>

    <!-- Alamat Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="section-title text-center">Lokasi Kami</h2>
                    <p><strong>Alamat:</strong> Gg. Menak Sopal, Sukobanteng, Karangsoko, Kec. Trenggalek, Kabupaten
                        Trenggalek,
                        Jawa Timur 66319</p>
                    <p><strong>Jam Operasional:</strong> Senin - Jumat, 08:00 - 16:00 WIB</p>
                    <!-- <p><strong>Telepon:</strong> (0355) 421123</p> -->
                    <p><strong>Email:</strong> cdkwilayahtrenggalek@jatimprov.go.id</p>
                </div>
                <div class="col-lg-6">
                    <div class="map-container shadow news-card">
                        <img src="{{ asset('images/background/gedung-cdk.jpg') }}"
                            alt="Lokasi Cabang Dinas Kehutanan Wilayah Trenggalek" class="img-fluid w-100 h-100">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
