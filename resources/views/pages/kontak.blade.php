@extends('layouts.main')

@section('content')
    <!-- Hero Section -->
    <section class="hero-section mb-5">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Hubungi Kami</h1>
            <p class="lead">Cabang Dinas Kehutanan Wilayah Trenggalek</p>
        </div>
    </section>

    <div class="container my-5">
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="form-section p-4 h-100">
                    <h4 class="mb-4"><i class="bi bi-envelope-paper"></i> Kirim Pesan</h4>

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('kontak_store') }}" id="contactForm">
                        @csrf
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="nama"
                                name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="pesan" class="form-label">Pesan <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="pesan" name="message" rows="5"
                                required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-forest w-100">Kirim Pesan</button>
                    </form>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="col-lg-6">

                <div class="office-hours p-4">
                    <h4 class="mb-4"><i class="bi bi-clock"></i> Jam Pelayanan</h4>
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th>Senin - Jumat</th>
                                <td>08:00 - 16:00 WIB</td>
                            </tr>
                            <tr>
                                <th>Sabtu - Minggu</th>
                                <td>Tutup</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle"></i> Untuk layanan diluar jam kerja, silahkan hubungi
                        <a href="mailto:cdkwilayahtrenggalek@jatimprov.go.id">
                            cdkwilayahtrenggalek@jatimprov.go.id
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
