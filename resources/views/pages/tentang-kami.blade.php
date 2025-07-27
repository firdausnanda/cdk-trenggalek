@extends('layouts.main')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section mb-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Tentang Kami</h1>
            <p class="lead">Cabang Dinas Kehutanan Wilayah Trenggalek</p>
        </div>
    </section>

    <!-- Tentang Kami Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="{{ asset('images/background/gedung-cdk.jpg') }}" class="img-fluid rounded"
                        style="width: 600px; height: 400px;"
                        alt="Kantor Dinas Kehutanan Wilayah Trenggalek dengan tampak depan gedung yang dikelilingi pepohonan rindang">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title text-center">Profil Singkat</h2>
                    <p>Cabang Dinas Kehutanan Wilayah Trenggalek berdasarkan Pergub No. 48 Tahun 2018 memiliki tugas
                        melaksanakan pengelolaan hutan, rehabilitasi lahan, perlindungan kawasan hutan, serta
                        pemberdayaan masyarakat desa hutan.</p>
                    <p>Kami menyediakan layanan perizinan kehutanan, pengawasan, dan penanganan pengaduan masyarakat.
                        Bersama seluruh pemangku kepentingan, kami berkomitmen mewujudkan pengelolaan hutan berkelanjutan
                        untuk kesejahteraan masyarakat.</p>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="about-card p-4 h-100 text-center">
                                <div class="card-icon">
                                    <i class="fas fa-tree"></i>
                                </div>
                                <h4>Luas Kawasan</h4>
                                <p class="mb-0">Lebih dari 100.000 hektar hutan lindung dan produksi</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="about-card p-4 h-100 text-center">
                                <div class="card-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h4>Tim Kami</h4>
                                <p class="mb-0">Lebih dari 50 profesional kehutanan yang berdedikasi</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tugas dan Fungsi -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Tugas dan Fungsi</h2>
            <div class="row mt-4">
                <div class="col-md-3 mb-4">
                    <div class="about-card p-4 h-100">
                        <h5 class="mb-3"><i class="fas fa-tasks me-2"></i>Perencanaan & Administrasi</h5>
                        <p>Menyusun program, anggaran, dan pengawasan administrasi kehutanan, serta pelayanan masyarakat dan
                            evaluasi kinerja untuk tata kelola hutan berkelanjutan.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="about-card p-4 h-100">
                        <h5 class="mb-3"><i class="fas fa-shield-alt me-2"></i>Pengelolaan & Rehabilitasi Hutan</h5>
                        <p>Melakukan pembinaan hutan hak, rehabilitasi lahan, pendampingan sertifikasi, dan pengolahan hasil
                            hutan untuk kelestarian ekosistem.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="about-card p-4 h-100">
                        <h5 class="mb-3"><i class="fas fa-hands-helping me-2"></i>Konservasi dan Ekosistem</h5>
                        <p>Melaksanakan pembinaan konservasi tanah dan air, pengendalian satwa liar non-CITES, dan
                            pengelolaan
                            kawasan penyangga untuk kelestarian lingkungan.</p>
                    </div>
                </div>
                <div class="col-md-3 mb-4">
                    <div class="about-card p-4 h-100">
                        <h5 class="mb-3"><i class="fas fa-hands-helping me-2"></i>Pemberdayaan Masyarakat & Penyuluhan
                        </h5>
                        <p>Melakukan penyuluhan kehutanan, memberdayakan masyarakat, dan mengembangkan perhutanan sosial
                            untuk kesejahteraan berkelanjutan.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tim Kami -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="section-title text-center">Struktur Organisasi</h2>
            <p class="text-center mb-5">Berikut adalah struktur organisasi di Cabang Dinas Kehutanan Wilayah Trenggalek</p>
            <div class="org-chart">
                <ul>
                    <li>
                        <div>
                            <span class="position">Kepala CDK</span>
                            <span class="name">Kepala Cabang Dinas Kehutanan Wil. Trenggalek</span>
                        </div>
                        <ul>
                            <li>
                                <div>
                                    <span class="position">Kepala Subag TU</span>
                                    <span class="name">Kepala Sub Bagian Tata Usaha</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <span class="position">Kepala Seksi TKUK</span>
                                    <span class="name">Kepala Seksi Tata Kelola dan Usaha Hutan</span>
                                </div>
                            </li>
                            <li>
                                <div>
                                    <span class="position">Kepala Seksi RLPM</span>
                                    <span class="name">Kepala Seksi Rehabilitasi Lahan dan Pemberdayaan Masyarakat</span>
                                </div>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Pencapaian Kami -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center section-title">Pencapaian Kami</h2>
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <h1 class="text-success display-4 fw-bold">125.299</h1>
                        <p>Hektar kawasan hutan dikelola</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <h1 class="text-success display-4 fw-bold">85%</h1>
                        <p>Tingkat tutupan vegetasi hutan di wilayah kerja</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <h1 class="text-success display-4 fw-bold">5.000+</h1>
                        <p>Masyarakat yang terlibat dalam program pemberdayaan</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <h1 class="text-success display-4 fw-bold">4.448</h1>
                        <p>Kelompok Tani Hutan yang dibina</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <h1 class="text-success display-4 fw-bold">3</h1>
                        <p>Penghargaan tingkat nasional</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 bg-white rounded shadow-sm">
                        <h1 class="text-success display-4 fw-bold">89%</h1>
                        <p>Tingkat kepuasan pelayanan publik</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
