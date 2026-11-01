# SEO Situs Publik — Laporan

Branch: `feature/public-redesign`

## Kondisi awal

- Tidak ada sitemap; `robots.txt` membuka semua path, termasuk `/admin` dan `/login`
- Tidak ada `canonical`, Open Graph, atau structured data (JSON-LD)
- Beranda tanpa judul sendiri; halaman berita ke-2, ke-3, dst. dan hasil filter memakai judul dan deskripsi yang sama
- Hasil pencarian `?search=` bisa terindeks; kategori hanya lewat query string `?kategori=`
- Admin tidak bisa mengatur judul/deskripsi SEO per berita

## Perubahan

| Fase | Isi |
|---|---|
| 1 | Partial `public/partials/seo.blade.php`: meta description, `robots`, `canonical` (default URL tanpa query string), Open Graph, Twitter Card. Gambar OG default `public/images/og-default.jpg` (1200×630). Judul beranda sendiri; judul/deskripsi per halaman dan kategori di `/berita`; `noindex, follow` untuk hasil pencarian dan halaman kosong. |
| 2 | `/sitemap.xml` (halaman statis, kategori, berita terbit + gambarnya; cache 1 jam, dibersihkan saat berita disimpan/dihapus). `/robots.txt` dilayani Laravel dengan baris `Sitemap:`. JSON-LD (`App\Support\StructuredData`): `GovernmentOrganization` + `WebSite` di semua halaman, `NewsArticle` di detail berita, `GovernmentOffice` (3 kantor, jam layanan) di Kontak, `BreadcrumbList`. Profil instansi dipusatkan di `config/cdk.php`. |
| 3 | URL bersih `/berita/kategori/{slug}` dan `/berita/tag/{slug}`; `?kategori=` di-redirect 301. Halaman tag `noindex, follow` (konten tipis). Kartu "Mesin pencari (SEO)" di form berita: judul, deskripsi, noindex — disimpan di `posts.meta['seo']`. Tag artikel jadi tautan; breadcrumb menampilkan judul berita; foto di isi artikel tanpa `alt` diberi alt fallback. |
| 4 | Helper `judul_halaman()` (akhiran pendek "· CDK Wilayah Trenggalek", dilepas bila judul lebih dari 65 karakter) dan `deskripsi_meta()` (maks. 160 karakter, dipotong per kata). Perintah `php artisan seo:audit`. |

## Audit

```bash
php artisan seo:audit https://domain-produksi.go.id
```

Membaca `robots.txt` dan sitemap, lalu memeriksa tiap URL: status, panjang judul (15–65) dan deskripsi
(50–160), canonical sama dengan URL sitemap, tidak `noindex`, satu `h1`, `img` tanpa atribut `alt`,
Open Graph (gambar harus URL absolut), JSON-LD valid dan field wajib `NewsArticle`, serta judul/deskripsi
ganda. Keluar dengan kode gagal bila ada error, jadi bisa dipasang di pipeline deploy.

Hasil di salinan lokal (31 URL): **0 error, 16 warning**. Seluruh warning adalah judul berita yang
memang panjang (79–129 karakter). Google akan memotongnya; isi kolom **Judul SEO** di admin untuk
berita penting (maks. 60 karakter).

Tanpa ekstensi `pdo_sqlite` di php.ini, test dijalankan dengan:

```bash
php -d extension=pdo_sqlite -d extension=sqlite3 vendor/phpunit/phpunit/phpunit
```

## Yang perlu dikerjakan di luar kode

1. **Domain dan HTTPS.** Set `APP_URL` produksi ke domain final dengan `https://`. Canonical, sitemap,
   dan Open Graph mengikuti host yang diakses; pastikan server me-redirect `http://`, `www.`, dan akses
   lewat IP ke satu domain itu (301).
2. **URL gambar lama.** `featured_image` dan `site_icon` tersimpan sebagai URL lengkap ke
   `http://103.183.92.79:8081/...` (hasil `asset()` saat upload). Akibatnya `og:image`, sitemap gambar,
   dan `NewsArticle.image` menunjuk ke alamat IP. Perbaiki sebelum go-live: simpan path relatif dan
   ganti host pada data lama.
3. **Nginx.** Konfigurasi bawaan Laravel/Forge berisi `location = /robots.txt { ... }` tanpa
   `try_files`; karena `public/robots.txt` sudah dihapus, hapus blok itu atau tambahkan
   `try_files $uri /index.php?$query_string;`. Apache (`.htaccess`) dan IIS (`web.config`) sudah aman.
4. **Google Search Console** (dan Bing Webmaster Tools): verifikasi domain, kirim
   `https://domain/sitemap.xml`, cek laporan *Pages* dan *Enhancements* setelah 1–2 minggu.
5. **Rich Results Test** (search.google.com/test/rich-results): uji beranda, Kontak, dan satu berita.
6. **Google Business Profile** untuk ketiga kantor (Trenggalek, Tulungagung, Kediri), dengan nama,
   alamat, dan jam layanan yang sama persis dengan `config/cdk.php`. Untuk pencarian lokal ("cabang
   dinas kehutanan trenggalek"), ini sering berdampak paling besar.
7. **Tautan masuk.** Minta situs Dinas Kehutanan Provinsi Jawa Timur dan pemda terkait menautkan ke
   domain ini; cantumkan juga di profil Facebook, Instagram, dan YouTube.
8. **Redirect URL lama.** Bila domain ini sebelumnya dipakai situs lain, petakan URL lama yang masih
   terindeks ke halaman baru dengan 301.

## Kebiasaan untuk editor

- Judul berita ringkas; kata kunci (kegiatan, lokasi) di depan.
- Isi **Kutipan** atau **Deskripsi SEO**: satu–dua kalimat, 120–160 karakter.
- Pilih kategori yang tepat; tag secukupnya (2–4) dan konsisten penulisannya.
- Gambar utama selalu diisi (dipakai Google, WhatsApp, Facebook).
- Centang **noindex** hanya untuk konten yang memang tidak perlu ditemukan (mis. pengumuman internal).
