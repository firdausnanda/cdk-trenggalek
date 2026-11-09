<?php

/*
|--------------------------------------------------------------------------
| CDK Wilayah Trenggalek profile and offices
|--------------------------------------------------------------------------
|
| Shown on the public pages (Tentang Kami, footer, homepage map) and in the
| structured data search engines read (App\Support\StructuredData). The
| first office is the main office. Addresses and map links come from each
| office's Google Maps listing.
|
*/

return [
    'nama' => 'Cabang Dinas Kehutanan Wilayah Trenggalek',
    'nama_singkat' => 'CDK Wilayah Trenggalek',
    'instansi_induk' => 'Dinas Kehutanan Provinsi Jawa Timur',
    'email' => 'cdkwilayahtrenggalek@jatimprov.go.id',
    'wilayah' => ['Kabupaten Trenggalek', 'Kabupaten Tulungagung', 'Kabupaten Kediri', 'Kota Kediri'],
    'sosial' => [
        'https://www.facebook.com/cdktrenggalekprogram',
        'https://www.instagram.com/cdktrenggalek/',
        'https://www.youtube.com/channel/UCzVEqqMfVnTHqbAKe-ememg',
    ],
    // Service hours on the Kontak page, Monday to Friday.
    'jam_layanan' => ['buka' => '08:00', 'tutup' => '16:00'],

    'kantor' => [
        [
            'nama' => 'Kantor Utama Trenggalek',
            'jenis' => 'Kantor utama',
            'alamat' => 'Gg. Menak Sopal, Sukobanteng, Karangsoko, Kec. Trenggalek, Kabupaten Trenggalek, Jawa Timur 66319',
            'kota' => 'Trenggalek',
            'kode_pos' => '66319',
            'maps' => 'https://maps.app.goo.gl/V2qh53qk7ZeLXtmr8',
        ],
        [
            'nama' => 'Kantor Wilayah Kerja Tulungagung',
            'jenis' => 'Kantor wilayah kerja',
            'alamat' => 'Jl. Sultan Agung No. 5-A, Dusun Kedungsingkal, Ketanon, Kec. Kedungwaru, Kabupaten Tulungagung, Jawa Timur 66229',
            'kota' => 'Tulungagung',
            'kode_pos' => '66229',
            'maps' => 'https://maps.app.goo.gl/VncRLckeJxeqCj4UA',
        ],
        [
            'nama' => 'Kantor Wilayah Kerja Kediri',
            'jenis' => 'Kantor wilayah kerja',
            'alamat' => 'Semampir, Kec. Kota, Kota Kediri, Jawa Timur 64129',
            'kota' => 'Kediri',
            'kode_pos' => '64129',
            'maps' => 'https://maps.app.goo.gl/XpNgpHwgcfRAP3YN6',
        ],
    ],
];
