    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-green navbar-dark navbar-shadow py-2 fixed-top" id="mainNavbar">
        <div class="container gap-3">
            <!-- Brand/Logo -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ config('settings.site_icon') }}" alt="Logo Dinas Kehutanan Wilayah Trenggalek" class="me-2"
                    style="width: 50px; height: 50px;">
                <span class="lh-1 d-none d-sm-block">
                    <div class="fw-bold">Cabang Dinas Kehutanan</div>
                    <div class="">Wilayah Trenggalek</div>
                </span>
                <span class="lh-1 d-block d-sm-none">
                    <div class="fw-bold fs-6">CDK Trenggalek</div>
                </span>
            </a>

            <!-- Toggle Button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Nav Items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto my-2">
                    <li class="nav-item mx-2">
                        <a class="nav-link active" aria-current="page" href="#">
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="#">
                            Tentang Kami
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="#">
                            Layanan
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="#">
                            Berita
                        </a>
                    </li>
                    <li class="nav-item mx-2">
                        <a class="nav-link" href="#">
                            Galeri
                        </a>
                    </li>

                    <li class="nav-item mx-2">
                        <a class="nav-link" href="#">
                            Kontak
                        </a>
                    </li>
                </ul>


            </div>
        </div>
    </nav>
