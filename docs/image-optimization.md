# Optimasi Gambar Artikel — Laporan

Branch: `feature/image-optimization` (di atas `upgrade/laravel-13`)

## Masalah awal

Editor Quill menyimpan gambar sebagai **base64 di dalam kolom `posts.content`**, dan gambar utama
diunggah apa adanya (hingga 5 MB, tanpa resize).

- 14 dari 16 artikel berisi gambar base64: 39 gambar, 28,7 MB, lebar sampai 6000 px
- Kolom `content` 38,3 MB; tabel `posts` 39,6 MB (bagian terbesar database)
- Satu halaman berita bisa berukuran **12,5 MB HTML**; gambar tidak bisa di-cache atau di-lazy-load
- Halaman daftar berita memuat seluruh `content` dan menjalankan query kategori per artikel (N+1)

## Perubahan

| Fase | Isi |
|---|---|
| A | `ImageService` (intervention/image 3, GD): orientasi EXIF, hapus metadata/GPS, maks. 1600 px, WebP q80. Upload gambar dari toolbar Quill ke `admin.editor.upload` (izin `post.create`/`post.edit`). Base64 sisa (paste/drag) diekstrak otomatis saat artikel disimpan. Gambar utama + varian 800/400 px. |
| B | `php artisan posts:optimize-images` (`--dry-run`, `--featured`, `--restore=`) untuk memindahkan gambar lama; backup JSON otomatis di `storage/backups`. |
| C | Query halaman publik tanpa kolom `content`, eager-load kategori (`/berita`: 3 query), `srcset`/`sizes` untuk gambar utama bervarian, `loading="lazy"` untuk gambar di isi artikel, `fetchpriority="high"` untuk gambar utama halaman detail, header `Cache-Control` untuk `/build/assets` (1 tahun) dan `/uploads` (30 hari). |
| D | Pengukuran (di bawah) dan test otomatis (15 test baru). |

## Hasil (salinan database lokal)

| Ukuran | Sebelum | Sesudah |
|---|---|---|
| Kolom `posts.content` | 38,3 MB | 37 KB |
| Tabel `posts` (setelah `OPTIMIZE TABLE`) | 39,6 MB | 0,13 MB |
| Dump database | 41 MB | 2,7 MB (2,4 MB di antaranya data Pulse) |
| Total unduhan semua artikel (HTML + gambar) | 38,3 MB | 7,4 MB (−81%) |
| HTML halaman berita terbesar | 12,5 MB | 27 KB |
| Unduhan total halaman berita terbesar | 12,5 MB | 0,57 MB (−95%) |
| Foto HP 4032×3024 | 2,9 MB | 157 KB (1600 px), 57 KB (800 px), 20 KB (400 px) |
| Query di `/berita` | 2 + 1 per artikel | 3 |

Waktu respons server di Laragon tetap ~0,6–0,7 detik (didominasi overhead lingkungan lokal);
keuntungan utamanya ada di ukuran unduhan. Sebagai gambaran, 12,5 MB di koneksi seluler
2 Mbps butuh ±50 detik, sedangkan HTML 27 KB tampil hampir seketika dan gambar dimuat
bertahap saat pembaca menggulir.

## Menjalankan di server

Lihat bagian "Optimasi gambar artikel" di `docs/upgrade-laravel-13.md`. Ringkasnya: pastikan
`APP_URL` benar, backup, `php artisan posts:optimize-images --dry-run --featured`, lalu jalankan
tanpa `--dry-run`, dan `OPTIMIZE TABLE posts;`.

## Belum termasuk

- Atribut `width`/`height` untuk gambar utama halaman detail dan gambar di isi artikel
  (kartu berita sudah bertinggi tetap lewat CSS).
- Editor deskripsi kategori (`terms/edit`) masih menyimpan gambar sebagai base64.
- File gambar lama (jpg asli gambar utama, gambar yang dihapus dari artikel) tidak dibersihkan
  otomatis.
- Konversi `--featured` baru teruji lewat test otomatis; file gambar utama asli hanya ada di server.
