# Upgrade ke Laravel 13 — Catatan & Checklist Deploy

Branch: `upgrade/laravel-13`

## Ringkasan perubahan

| Komponen | Sebelum | Sesudah |
|---|---|---|
| laravel/framework | 12.21 | 13.39 |
| PHP minimum | 8.2 | **8.3** (lock dikunci ke platform PHP 8.3, jalan juga di 8.4) |
| Symfony components | 7.3 | 7.4 |
| nwidart/laravel-modules | 12 | 13 |
| spatie/laravel-permission | 6.21 | 8.3 |
| tormjens/eventy | 0.8 | 0.9.5 (tanpa `opis/closure`) |
| laravel/tinker / phpunit | 2 / 11 | 3 / 12 |
| Livewire (via Pulse) | 3 | 4 |
| Vite / laravel-vite-plugin | 6 / 1 | 8 / 3 |
| Flowbite / ApexCharts | 3 / 4 | 4 / 8 |

Dihapus karena tidak dipakai: `lakm/laravel-comments`, `spatie/laravel-query-builder`,
`cweagans/composer-patches` (+ folder `patches/`), React, `quill-emoji`, `simple-datatables`,
`tailwind-merge`, FullCalendar, jsVectorMap, serta komponen JS template yang tidak dirender.

Struktur aplikasi dimigrasikan ke gaya Laravel 11+: `bootstrap/app.php` + `bootstrap/providers.php`
menggantikan `Http/Kernel`, `Console/Kernel`, `Exceptions/Handler` dan `RouteServiceProvider`.
Daftar route (110) dan urutan middleware sudah diverifikasi identik.

Tidak ada migrasi database baru. Session tetap memakai serialisasi `php` dan prefix cache/cookie
tidak berubah, jadi pengguna yang sedang login tidak ter-logout.

## Prasyarat server

- PHP **8.4** direkomendasikan (minimal 8.3) dengan ekstensi: pdo_mysql, mbstring, openssl,
  tokenizer, xml, ctype, fileinfo, gd, intl, zip, curl, bcmath
- Composer >= 2.8
- Node **>= 20.19 atau >= 22.12** (hanya jika build aset dilakukan di server)

## Docker (pengganti Laradock)

Laradock (submodule) sudah dihapus dan diganti setup ringan di repo ini:

- `docker/php/Dockerfile` — `php:8.4.27-fpm-alpine` + bcmath, gd, intl, opcache, pdo_mysql, zip, Composer
- `docker/php/php.ini` — memory 256M, upload 20M, opcache
- `docker/nginx/default.conf` — konfigurasi Laravel standar
- `docker-compose.yml` — service `app`, `scheduler` (`schedule:work`), `nginx` (port 8081),
  `mysql` 8.4 (port 3307), dan `node` (profil `tools`, untuk build aset)

Port dan nama service sama dengan Laradock (`mysql`, 8081, 3307), jadi `.env` di server
tidak perlu diubah selama `DB_HOST=mysql`. Variabel compose dibaca dari `.env` Laravel.

### Pindah dari Laradock di server (sekali saja)

1. Backup database: `docker exec <container mysql laradock> mysqldump -uroot -p --all-databases > backup.sql`
2. Catat isi `laradock/.env` (password MySQL, port). `MYSQL_ROOT_PASSWORD` hanya dipakai saat
   database pertama kali dibuat; data lama tetap memakai user/password yang sudah ada.
3. Matikan Laradock: `cd laradock && docker compose down` (data di `~/.laradock/data/mysql` tetap ada)
4. Matikan cron `schedule:run` lama jika ada di host (scheduler kini jalan di service `scheduler`).
5. Dari root proyek: `git pull`. Folder `laradock/` akan tersisa sebagai folder biasa; pindahkan
   ke luar proyek (simpan `.env`-nya) lalu jalankan `rm -rf .git/modules/laradock`.
6. Jika user host bukan UID/GID 1000, set `DOCKER_UID`/`DOCKER_GID` di `.env`.
7. `docker compose up -d --build` — MySQL otomatis memakai data lama dari
   `~/.laradock/data/mysql` (ubah lewat `DOCKER_MYSQL_DATA` bila lokasinya lain).
8. Cek: `docker compose ps` dan buka `http://<server>:8081`.

## Langkah deploy (staging dulu, lalu production)

Di Docker, jalankan perintah `php`/`composer` lewat `docker compose exec app ...` dan build aset
dengan `docker compose run --rm node sh -c "npm ci && npm run build"`. Setelah update, jalankan
`docker compose restart app scheduler` (pengganti restart PHP-FPM).

1. Backup database dan folder `storage/app`.
2. Aktifkan maintenance mode: `php artisan down`
3. Ambil kode: `git fetch && git checkout <commit/branch hasil merge>`
4. Install dependency: `composer install --no-dev --optimize-autoloader`
5. Build aset (atau upload hasil `public/build` dari mesin lain):
   `npm ci && npm run build`
6. Bersihkan cache lama (penting: paket yang dihapus masih tercatat di cache bootstrap):
   `php artisan optimize:clear`
7. Migrasi (seharusnya "Nothing to migrate"): `php artisan migrate --force`
8. Cache ulang: `php artisan optimize`
9. Restart PHP-FPM (agar opcache baru) dan queue worker jika ada: `php artisan queue:restart`
10. Matikan maintenance mode: `php artisan up`

Tanpa Docker, cron scheduler tetap `* * * * * php artisan schedule:run`. Dengan Docker,
service `scheduler` sudah menjalankannya — jangan pasang cron kedua.

## Optimasi gambar artikel (sekali jalan, setelah deploy branch `feature/image-optimization`)

Gambar lama di isi artikel tersimpan sebagai base64 di kolom `posts.content`. Command berikut
memindahkannya ke file WebP (maks. 1600 px) di `public/uploads/posts/YYYY/MM/`.

1. Pastikan `APP_URL` di `.env` benar (tanpa path tambahan): URL gambar yang dihasilkan memakai nilai ini.
2. Backup database (command juga menulis backup sendiri ke `storage/backups/posts-images-*.json`).
3. Lihat dulu tanpa mengubah apa pun: `php artisan posts:optimize-images --dry-run --featured`
4. Jalankan: `php artisan posts:optimize-images --featured`
   (`--featured` juga mengubah gambar utama lama ke WebP + varian 800/400 px; file asli tetap disimpan.)
5. Kecilkan tabel: `OPTIMIZE TABLE posts;` di MySQL.
6. Cek beberapa halaman berita. Jika ada masalah, kembalikan isi lama:
   `php artisan posts:optimize-images --restore=storage/backups/posts-images-<waktu>.json`

Hasil di salinan database lokal: 14 artikel, 39 gambar; kolom `content` 38,3 MB → 34 KB, tabel
`posts` 39,6 MB → 0,13 MB, file gambar total 7,5 MB, halaman berita terbesar 12,5 MB → 27 KB HTML.

## Smoke test setelah deploy

- Halaman publik: `/`, `/berita`, satu detail berita (cek tombol share), `/kontak`, `/prestasi`
- Login admin, lalu logout
- Dashboard (grafik pertumbuhan pengguna tampil)
- Buat/edit artikel (editor Quill, upload gambar, kategori)
- Users & Roles: buka daftar, buka form edit
- Pengaturan, Translations, Action Log
- `/pulse` dan `/log-viewer` sebagai Superadmin
- Ganti bahasa (EN/ID)

## Rollback

Tidak ada perubahan skema database, jadi rollback cukup dengan kode:

1. `git checkout <commit sebelum upgrade>` (contoh: `5b52d30`)
2. `composer install --no-dev --optimize-autoloader`
3. `npm ci && npm run build`
4. `php artisan optimize:clear && php artisan optimize`
5. Restart PHP-FPM

## Catatan lanjutan (bukan bagian upgrade)

- Gaya kode sudah dirapikan dengan Pint + Rector, dan `.gitattributes` memaksa line ending LF.
  Di Windows, set `git config core.autocrlf input` agar `composer pint` tidak gagal karena CRLF,
  dan `git config blame.ignoreRevsFile .git-blame-ignore-revs` agar blame melewati commit format.
- `npm run lint` butuh file `eslint.config.js` (belum ada sejak sebelum upgrade).
- Test memakai SQLite: aktifkan `extension=pdo_sqlite` di php.ini lokal.
- Login dengan username tidak jalan karena `LoginRequest` mewajibkan format email.
- `UserChartService` memakai `DATE_FORMAT` (khusus MySQL).
- Markup tooltip di `dashboard/partials/user-history.blade.php` dan halaman settings menunjuk
  ke elemen yang tidak ada (peringatan Flowbite di console).
- `jorenvanhocht/laravel-share` tidak dirawat sejak 2022; masih berfungsi di Laravel 13.
- `npm audit` menyisakan 10 temuan low/moderate di dependency dev/build (ESLint,
  flowbite-datepicker); tidak ikut ke bundle browser.
