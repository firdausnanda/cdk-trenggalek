@extends('layouts.main')

@push('css')
    <link rel="stylesheet" href="{{ asset('vendor/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/all.min.css') }}">
@endpush

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
                    <button class="w-100 h-100 p-4 bg-white rounded shadow-sm border-0 stats-button" id="masy">
                        <h1 class="text-success display-4 fw-bold">{{ number_format($totalAnggota, 0, ',', '.') }}</h1>
                        <p class="mb-0">Masyarakat yang terlibat dalam program pemberdayaan</p>
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="w-100 h-100 p-4 bg-white rounded shadow-sm border-0 stats-button" id="kth">
                        <h1 class="text-success display-4 fw-bold">{{ number_format($kth, 0, ',', '.') }}</h1>
                        <p class="mb-0">Kelompok Tani Hutan yang dibina</p>
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="w-100 h-100 p-4 bg-white rounded shadow-sm border-0 stats-button" id="kep-masy">
                        <h1 class="text-success display-4 fw-bold">93.96%</h1>
                        <p class="mb-0">Tingkat kepuasan pelayanan publik</p>
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="w-100 h-100 p-4 bg-white rounded shadow-sm border-0 stats-button">
                        <h1 class="text-success display-4 fw-bold">85%</h1>
                        <p class="mb-0">Rehabilitasi Hutan dan Lahan di wilayah kerja</p>
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="w-100 h-100 p-4 bg-white rounded shadow-sm border-0 stats-button">
                        <h1 class="text-success display-4 fw-bold">4</h1>
                        <p class="mb-0">Penghargaan tingkat Provinsi</p>
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="w-100 h-100 p-4 bg-white rounded shadow-sm border-0 stats-button" id="pbphh">
                        <h1 class="text-success display-4 fw-bold">{{ $pbphh }}</h1>
                        <p class="mb-0">Jumlah PBPHH</p>
                    </button>
                </div>
                <div class="col-md-4">
                    <button class="w-100 h-100 p-4 bg-white rounded shadow-sm border-0 stats-button" id="ps">
                        <h1 class="text-success display-4 fw-bold">{{ $ps }}</h1>
                        <p class="mb-0">Perhutanan Sosial</p>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal KTH -->
    <div class="modal fade" id="modal-kth" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Data Kelompok Tani Hutan <span class="fw-bold" id="kth_title"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered w-100" id="table-kth">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Kelompok Tani Hutan</th>
                                <th class="text-center">Alamat</th>
                                <th>Nama Ketua Kelompok</th>
                                <th class="text-center">Jenis Usaha</th>
                                <th class="text-center">No. Registrasi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Masy. Terlibat -->
    <div class="modal fade" id="modal-masy-terlibat" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Data Masyarakat dalam Program Pemberdayaan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div style="display: flex; justify-content: center; align-items: center; height: 100%;">
                        <div style="width: 500px; height: 500px;">
                            <canvas id="pieChart" width="500" height="500"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Kepuasan Masy. -->
    <div class="modal fade" id="modal-kepuasan-masy" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Tingkat Kepuasan Masyarakat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe class="w-100" style="height: 600px" src="https://sukma.jatimprov.go.id/fe/dinas/upt/17"
                        frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal PBPHH -->
    <div class="modal fade" id="modal-pbphh" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Data Perizinan Berusaha Pengolahan Hasil Hutan <span class="fw-bold" id="pbphh_title"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered w-100" id="table-pbphh">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama PBPHH</th>
                                <th class="text-center">Kantor</th>
                                <th class="text-center">Pabrik</th>
                                <th class="text-center">No. Registrasi</th>
                                <th class="text-center">Direktur</th>
                                <th class="text-center">Skala</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal PS -->
    <div class="modal fade" id="modal-ps" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
        role="dialog" aria-labelledby="modalTitleId">
        <div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Data Perhutanan Sosial <span class="fw-bold" id="ps_title"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered w-100" id="table-ps">
                        <thead>
                            <tr>
                                <th class="text-center">No</th>
                                <th>Nama Kelompok</th>
                                <th class="text-center">Skema</th>
                                <th>KHDPK</th>
                                <th class="text-center">No. SK</th>
                                <th class="text-center">Alamat</th>
                                <th class="text-center">Jumlah KK</th>
                                <th class="text-center">Luas</th>
                                <th class="text-center">Ketua</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('vendor/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('vendor/chartjs/chart.js') }}"></script>
    <script>
        $(document).ready(function() {

            // KTH
            $('#kth').click(function() {

                const table = $('#table-kth').DataTable({
                    ajax: {
                        url: "{{ route('kth.index') }}",
                        type: "GET",
                        data: function(d) {
                            d.wilayah_filter = window
                                .currentWilayahFilter;
                        }
                    },
                    lengthChange: false,
                    info: true,
                    responsive: {
                        details: {
                            type: 'column',
                            target: 'tr'
                        }
                    },
                    buttons: [{
                        text: 'Trenggalek',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(1);
                            $('#kth_title').text('(Wilayah Trenggalek)')
                        }
                    }, {
                        text: 'Kediri',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(3);
                            $('#kth_title').text('(Wilayah Kediri)')
                        }
                    }, {
                        text: 'Tulungagung',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(2);
                            $('#kth_title').text('(Wilayah Tulungagung)')
                        }
                    }, {
                        text: '<i class="fa-solid fa-rotate"></i>',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            window.currentWilayahFilter = null;
                            table.ajax.reload()
                            $('#kth_title').text('')
                        }
                    }],
                    columnDefs: [{
                            targets: 0,
                            width: '10%',
                            className: 'align-middle text-center',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            targets: 1,
                            className: 'align-middle',
                            data: 'nama'
                        },
                        {
                            targets: 2,
                            className: 'align-middle',
                            data: 'alamat'
                        },
                        {
                            targets: 3,
                            className: 'align-middle',
                            data: 'ketua'
                        },
                        {
                            targets: 4,
                            className: 'align-middle',
                            data: 'jenis_usaha'
                        },
                        {
                            targets: 5,
                            className: 'align-middle',
                            data: 'no_registrasi'
                        }
                    ],
                    language: {
                        info: "Menampilkan _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data tersedia",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        emptyTable: "Tidak ada data tersedia dalam tabel",
                        zeroRecords: "Tidak ditemukan data yang sesuai",
                        paginate: {
                            first: '<i class="fas fa-angle-double-left"></i>',
                            previous: '<i class="fas fa-angle-left"></i>',
                            next: '<i class="fas fa-angle-right"></i>',
                            last: '<i class="fas fa-angle-double-right"></i>',
                        },
                    },
                    destroy: true,
                    processing: true,
                    initComplete: function() {
                        $('#table-kth').DataTable().buttons().container().appendTo(
                            '#table-kth_wrapper .col-md-6:eq(0)');
                        $('.btn-tambah').removeClass("btn-secondary");
                    }
                });

                function filterByWilayah(wilayahId) {
                    window.currentWilayahFilter = wilayahId;
                    table.ajax.reload();
                }

                $('#modal-kth').modal('show');
            });

            // Masy. Terlibat
            let pieChart;
            $('#masy').click(function(e) {
                e.preventDefault();

                // Destroy existing chart
                if (pieChart) {
                    pieChart.destroy();
                }

                // Initialize with empty data
                const ctx = document.getElementById('pieChart').getContext('2d');
                pieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: [],
                        datasets: [{
                            data: [],
                            backgroundColor: [],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: {
                            duration: 2000,
                            animateScale: true,
                            animateRotate: true
                        },
                        plugins: {
                            legend: {
                                position: 'bottom'
                            },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) =>
                                        `Wilker ${ctx.label}: ${ctx.raw.toLocaleString('id-ID')} Orang`
                                }
                            }
                        }
                    }
                });

                // Load data
                $.ajax({
                    url: "{{ route('masyarakat.index') }}",
                    success: function(data) {
                        pieChart.data.labels = data.labels;
                        pieChart.data.datasets[0].data = data.values;
                        pieChart.data.datasets[0].backgroundColor = data.colors;

                        // Force animation restart
                        setTimeout(() => {
                            pieChart.update();
                            pieChart.resetAnimation();
                            pieChart.update();
                        }, 300);
                    }
                });

                $('#modal-masy-terlibat').modal('show');
            });

            // Kepuasaan Masy.
            $("#kep-masy").click(function(e) {
                e.preventDefault();
                $('#modal-kepuasan-masy').modal('show')
            });

            // PBPHH
            $('#pbphh').click(function() {

                const table2 = $('#table-pbphh').DataTable({
                    ajax: {
                        url: "{{ route('pbphh.index') }}",
                        type: "GET",
                        data: function(d) {
                            d.wilayah_filter = window
                                .currentWilayahFilter;
                        }
                    },
                    lengthChange: false,
                    info: true,
                    responsive: {
                        details: {
                            type: 'column',
                            target: 'tr'
                        }
                    },
                    buttons: [{
                        text: 'Trenggalek',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(1);
                            $('#pbphh_title').text('(Wilayah Trenggalek)')
                        }
                    }, {
                        text: 'Kediri',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(3);
                            $('#pbphh_title').text('(Wilayah Kediri)')
                        }
                    }, {
                        text: 'Tulungagung',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(2);
                            $('#pbphh_title').text('(Wilayah Tulungagung)')
                        }
                    }, {
                        text: '<i class="fa-solid fa-rotate"></i>',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            window.currentWilayahFilter = null;
                            table2.ajax.reload()
                            $('#pbphh_title').text('')
                        }
                    }],
                    columnDefs: [{
                            targets: 0,
                            width: '10%',
                            className: 'align-middle text-center',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            targets: 1,
                            className: 'align-middle',
                            data: 'nama_pbphh'
                        },
                        {
                            targets: 2,
                            className: 'align-middle',
                            data: 'kantor'
                        },
                        {
                            targets: 3,
                            className: 'align-middle',
                            data: 'pabrik'
                        },
                        {
                            targets: 4,
                            className: 'align-middle',
                            data: 'no_izin'
                        },
                        {
                            targets: 5,
                            className: 'align-middle',
                            data: 'direktur'
                        },
                        {
                            targets: 6,
                            className: 'align-middle',
                            data: 'skala'
                        }
                    ],
                    language: {
                        info: "Menampilkan _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data tersedia",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        emptyTable: "Tidak ada data tersedia dalam tabel",
                        zeroRecords: "Tidak ditemukan data yang sesuai",
                        paginate: {
                            first: '<i class="fas fa-angle-double-left"></i>',
                            previous: '<i class="fas fa-angle-left"></i>',
                            next: '<i class="fas fa-angle-right"></i>',
                            last: '<i class="fas fa-angle-double-right"></i>',
                        },
                    },
                    destroy: true,
                    processing: true,
                    initComplete: function() {
                        $('#table-pbphh').DataTable().buttons().container().appendTo(
                            '#table-pbphh_wrapper .col-md-6:eq(0)');
                        $('.btn-tambah').removeClass("btn-secondary");
                    }
                });

                function filterByWilayah(wilayahId) {
                    window.currentWilayahFilter = wilayahId;
                    table2.ajax.reload();
                }

                $('#modal-pbphh').modal('show');
            });

            // PS
            $('#ps').click(function() {

                const table3 = $('#table-ps').DataTable({
                    ajax: {
                        url: "{{ route('ps.index') }}",
                        type: "GET",
                        data: function(d) {
                            d.wilayah_filter = window
                                .currentWilayahFilter;
                        }
                    },
                    lengthChange: false,
                    info: true,
                    responsive: {
                        details: {
                            type: 'column',
                            target: 'tr'
                        }
                    },
                    buttons: [{
                        text: 'Trenggalek',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(1);
                            $('#ps_title').text('(Wilayah Trenggalek)')
                        }
                    }, {
                        text: 'Kediri',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(3);
                            $('#ps_title').text('(Wilayah Kediri)')
                        }
                    }, {
                        text: 'Tulungagung',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            filterByWilayah(2);
                            $('#ps_title').text('(Wilayah Tulungagung)')
                        }
                    }, {
                        text: '<i class="fa-solid fa-rotate"></i>',
                        className: 'btn btn-sm btn-success',
                        action: function(e, dt, node, config) {
                            window.currentWilayahFilter = null;
                            table3.ajax.reload()
                            $('#ps_title').text('')
                        }
                    }],
                    columnDefs: [{
                            targets: 0,
                            width: '10%',
                            className: 'align-middle text-center',
                            render: function(data, type, row, meta) {
                                return meta.row + meta.settings._iDisplayStart + 1;
                            }
                        },
                        {
                            targets: 1,
                            className: 'align-middle',
                            data: 'nama'
                        },
                        {
                            targets: 2,
                            className: 'align-middle',
                            data: 'skema'
                        },
                        {
                            targets: 3,
                            className: 'align-middle',
                            data: 'khdpk'
                        },
                        {
                            targets: 4,
                            className: 'align-middle',
                            data: 'no_sk'
                        },
                        {
                            targets: 5,
                            className: 'align-middle',
                            data: 'alamat'
                        },
                        {
                            targets: 6,
                            className: 'align-middle',
                            data: 'jumlah_kk'
                        },
                        {
                            targets: 7,
                            className: 'align-middle',
                            data: 'luas'
                        },
                        {
                            targets: 8,
                            className: 'align-middle',
                            data: 'ketua'
                        }
                    ],
                    language: {
                        info: "Menampilkan _END_ dari _TOTAL_ data",
                        infoEmpty: "Tidak ada data tersedia",
                        infoFiltered: "(disaring dari _MAX_ total data)",
                        emptyTable: "Tidak ada data tersedia dalam tabel",
                        zeroRecords: "Tidak ditemukan data yang sesuai",
                        paginate: {
                            first: '<i class="fas fa-angle-double-left"></i>',
                            previous: '<i class="fas fa-angle-left"></i>',
                            next: '<i class="fas fa-angle-right"></i>',
                            last: '<i class="fas fa-angle-double-right"></i>',
                        },
                    },
                    destroy: true,
                    processing: true,
                    initComplete: function() {
                        $('#table-ps').DataTable().buttons().container().appendTo(
                            '#table-ps_wrapper .col-md-6:eq(0)');
                        $('.btn-tambah').removeClass("btn-secondary");
                    }
                });

                function filterByWilayah(wilayahId) {
                    window.currentWilayahFilter = wilayahId;
                    table3.ajax.reload();
                }

                $('#modal-ps').modal('show');
            });

        });
    </script>
@endpush
