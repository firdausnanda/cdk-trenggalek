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
                    <h2 class="section-title text-center text-lg-start">Tentang Kami</h2>
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
                    <a href="#" class="btn btn-outline-success">Selengkapnya</a>
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
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            Hubungi Kami
                        </button>
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
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            Hubungi Kami
                        </button>
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
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            Hubungi Kami
                        </button>
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
                        <button class="btn btn-outline-success btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            Hubungi Kami
                        </button>
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
                <div class="col-md-6 col-lg-4">
                    <div class="card news-card h-100">
                        <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/8c42fcce-b7d5-4389-82af-a07adccbf3ca.png"
                            class="card-img-top" alt="Penanaman pohon bersama masyarakat di Desa Wonocoyo">
                        <div class="card-body">
                            <h5 class="card-title">Penanaman 10.000 Pohon di Desa Wonocoyo</h5>
                            <p class="card-text">Dinas Kehutanan bersama masyarakat melakukan penanaman pohon dalam
                                rangka
                                rehabilitasi lahan kritis...</p>
                            <a href="#" class="btn btn-sm btn-primary">Baca Selengkapnya</a>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Diposting: 12 Juni 2023</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card news-card h-100">
                        <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/80783122-2f38-4aad-9214-759d05e56fdf.png"
                            class="card-img-top" alt="Pelatihan pengolahan hasil hutan untuk masyarakat Desa Panggul">
                        <div class="card-body">
                            <h5 class="card-title">Pelatihan Pengolahan Hasil Hutan untuk Masyarakat</h5>
                            <p class="card-text">Dinas Kehutanan menyelenggarakan pelatihan pengolahan hasil hutan
                                bukan kayu untuk
                                meningkatkan ekonomi masyarakat...</p>
                            <a href="#" class="btn btn-sm btn-primary">Baca Selengkapnya</a>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Diposting: 28 Mei 2023</small>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card news-card h-100">
                        <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/60c9a1e3-2913-42ff-af01-2ccbbb0cd484.png"
                            class="card-img-top" alt="Monitoring satwa liar di kawasan hutan Trenggalek">
                        <div class="card-body">
                            <h5 class="card-title">Monitoring Populasi Satwa Liar di Kawasan Hutan</h5>
                            <p class="card-text">Tim Dinas Kehutanan melakukan monitoring rutin terhadap populasi satwa
                                liar untuk
                                menjaga keseimbangan ekosistem...</p>
                            <a href="#" class="btn btn-sm btn-primary">Baca Selengkapnya</a>
                        </div>
                        <div class="card-footer text-muted">
                            <small>Diposting: 15 Mei 2023</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="#" class="btn btn-outline-success">Lihat Semua Berita</a>
            </div>
        </div>
    </section>

    <!-- Alamat Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h2 class="section-title text-center text-lg-start">Lokasi Kami</h2>
                    <p><strong>Alamat:</strong> Gg. Menak Sopal, Sukobanteng, Karangsoko, Kec. Trenggalek, Kabupaten
                        Trenggalek,
                        Jawa Timur 66319</p>
                    <p><strong>Jam Operasional:</strong> Senin - Jumat, 08:00 - 16:00 WIB</p>
                    <!-- <p><strong>Telepon:</strong> (0355) 421123</p> -->
                    <p><strong>Email:</strong> cdkwilayahtrenggalek@jatimprov.go.id</p>
                </div>
                <div class="col-lg-6">
                    <div class="map-container shadow news-card">
                        <img src="{{ asset('images/background/gedung-cdk.jpg') }}" alt="Lokasi Cabang Dinas Kehutanan Wilayah Trenggalek"
                            class="img-fluid w-100 h-100">
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
