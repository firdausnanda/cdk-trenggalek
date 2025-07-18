# Aplikasi Cabang Dinas Kehutanan Wilayah Trenggalek

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![React](https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

## Deskripsi Aplikasi

Aplikasi ini dibangun untuk mendukung operasional Cabang Dinas Kehutanan Wilayah Trenggalek dengan sistem manajemen konten berbasis Laravel Dashboard CMS. Sistem ini menyediakan berbagai fitur untuk manajemen data kehutanan, pelaporan, dan administrasi terkait.

## Fitur Utama

- **Dashboard Admin** berbasis Laravel Dashboard CMS
- Manajemen data kehutanan terpusat
- Sistem pelaporan terintegrasi
- Administrasi dokumen kehutanan
- Sistem komentar dan kolaborasi
- Manajemen pengguna dan izin berbasis peran
- Monitoring aktivitas sistem real-time
- Manajemen konten dan halaman statis

## Teknologi yang Digunakan

### Backend
- PHP 8.3/8.4
- Laravel 12
- Laravel Dashboard CMS
- Modul Nwidart/laravel-modules
- Sistem autentikasi Laravel UI
- Spatie Laravel Permission untuk RBAC
- Spatie Query Builder untuk query yang kompleks

### Frontend
- React.js
- Bootstrap 5
- Laravel UI untuk integrasi React
- Laravel Dashboard CMS Admin Panel

### Library Utama
- Eloquent Viewable untuk pelacakan view
- Laravel Share untuk berbagi konten
- Laravel Comments untuk sistem komentar
- Laravel Pulse untuk monitoring
- Log Viewer untuk manajemen log
- Laravel Dashboard CMS untuk manajemen konten

## Persyaratan Sistem

- PHP 8.3 atau 8.4
- Composer 2.x
- Node.js 16.x atau lebih baru
- NPM/Yarn
- Database MySQL 8.0+ atau MariaDB 10.3+
- Web server (Apache/Nginx)
- Ekstensi PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML

## Instalasi

1. Clone repository:
   ```bash
   git clone [repository-url]
   cd nama-folder-project
   ```

2. Install dependensi PHP:
   ```bash
   composer install
   ```

3. Install dependensi JavaScript:
   ```bash
   npm install
   ```

4. Buat file `.env` dari template:
   ```bash
   cp .env.example .env
   ```

5. Generate key aplikasi:
   ```bash
   php artisan key:generate
   ```

6. Konfigurasi database di file `.env`

7. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```

8. Install Laravel Dashboard CMS:
   ```bash
   php artisan dashboard:install
   ```

9. Publish assets CMS:
   ```bash
   php artisan vendor:publish --tag=laravel-dashboard-assets
   ```

10. Compile assets:
    ```bash
    npm run dev
    # atau untuk production
    npm run build
    ```

11. Jalankan server development:
    ```bash
    php artisan serve
    ```

## Struktur Modul dengan Laravel Dashboard CMS

```
app/
├── Dashboard/
│   ├── Components/       # Komponen dashboard kustom
│   ├── Filters/          # Filter untuk dashboard
│   └── Widgets/          # Widget dashboard
Modules/
├── ModuleName/
│   ├── Config/
│   ├── Dashboard/        # Modul-specific dashboard components
│   ├── Database/
│   │   ├── Factories/
│   │   ├── Migrations/
│   │   └── Seeders/
│   ├── Entities/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Providers/
│   ├── Resources/
│   │   ├── assets/
│   │   ├── lang/
│   │   ├── views/
│   └── Routes/
│       ├── api.php
│       └── web.php
resources/
├── views/
│   ├── dashboard/        # Template dashboard CMS
│   └── vendor/
│       └── dashboard/    # Override view dashboard
```

## Fitur Laravel Dashboard CMS

1. **Admin Dashboard**:
   - Statistik pengunjung real-time
   - Grafik aktivitas pengguna
   - Notifikasi sistem

2. **Manajemen Konten**:
   - Buat/edit halaman statis
   - Manajemen menu navigasi
   - Sistem widget yang dapat disesuaikan

3. **Manajemen Media**:
   - Upload dan organisasi file
   - Integrasi dengan penyimpanan cloud

4. **Custom Widgets**:
   - Widget data kehutanan
   - Widget laporan bulanan
   - Widget aktivitas terbaru

## Penggunaan CMS

1. Akses dashboard admin di `/admin`
2. Login dengan kredensial admin
3. Navigasi melalui menu admin sidebar
4. Gunakan builder konten untuk membuat halaman baru
5. Kelola widget melalui panel administrasi

## Pengembangan

Untuk mode pengembangan:
```bash
npm run dev
php artisan serve
```

Untuk memantau aktivitas sistem:
```bash
php artisan pulse:check
php artisan pulse:work
```

Untuk mengembangkan komponen dashboard:
```bash
php artisan make:dashboard-component NamaKomponen
```

## Kontribusi

1. Fork project
2. Buat branch fitur (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -am 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Lisensi

Aplikasi ini dilisensikan di bawah [Lisensi MIT](LICENSE).