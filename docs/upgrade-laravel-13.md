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

- PHP **>= 8.3** dengan ekstensi: pdo_mysql, mbstring, openssl, tokenizer, xml, ctype, fileinfo,
  gd, intl, zip, curl, bcmath
- Composer >= 2.8
- Node **>= 20.19 atau >= 22.12** (hanya jika build aset dilakukan di server)

## Langkah deploy (staging dulu, lalu production)

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

Cron scheduler tidak berubah (`* * * * * php artisan schedule:run`).

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

- Pint 1.32 menandai ~39 file karena aturan gaya baru (`method_argument_space`, dll.);
  jalankan `composer format` dalam commit terpisah bila ingin dirapikan.
- `npm run lint` butuh file `eslint.config.js` (belum ada sejak sebelum upgrade).
- Test memakai SQLite: aktifkan `extension=pdo_sqlite` di php.ini lokal.
- Login dengan username tidak jalan karena `LoginRequest` mewajibkan format email.
- `UserChartService` memakai `DATE_FORMAT` (khusus MySQL).
- Markup tooltip di `dashboard/partials/user-history.blade.php` dan halaman settings menunjuk
  ke elemen yang tidak ada (peringatan Flowbite di console).
- `jorenvanhocht/laravel-share` tidak dirawat sejak 2022; masih berfungsi di Laravel 13.
- `npm audit` menyisakan 10 temuan low/moderate di dependency dev/build (ESLint,
  flowbite-datepicker); tidak ikut ke bundle browser.
