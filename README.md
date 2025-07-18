# Aplikasi Cabang Dinas Kehutanan Wilayah Trenggalek

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![React](https://img.shields.io/badge/React-20232A?style=for-the-badge&logo=react&logoColor=61DAFB)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

## Deskripsi Aplikasi

Aplikasi ini dibangun untuk mendukung operasional Cabang Dinas Kehutanan Wilayah Trenggalek. Sistem ini menyediakan berbagai fitur untuk manajemen data kehutanan, pelaporan, dan administrasi terkait.

## Fitur Utama

-   Manajemen data kehutanan
-   Sistem pelaporan terintegrasi
-   Administrasi dokumen kehutanan
-   Sistem komentar dan kolaborasi
-   Manajemen pengguna dan izin
-   Monitoring aktivitas sistem

## Teknologi yang Digunakan

### Backend

-   PHP 8.3/8.4
-   Laravel 12
-   Modul Nwidart/laravel-modules
-   Sistem autentikasi Laravel UI
-   Spatie Laravel Permission untuk RBAC
-   Spatie Query Builder untuk query yang kompleks

### Frontend

-   React.js
-   Bootstrap 5
-   Laravel UI untuk integrasi React

### Library Utama

-   Eloquent Viewable untuk pelacakan view
-   Laravel Share untuk berbagi konten
-   Laravel Comments untuk sistem komentar
-   Laravel Pulse untuk monitoring
-   Log Viewer untuk manajemen log
-   Eventy untuk sistem event

## Persyaratan Sistem

-   PHP 8.3 atau 8.4
-   Composer 2.x
-   Node.js 16.x atau lebih baru
-   NPM/Yarn
-   Database MySQL 8.0+ atau MariaDB 10.3+
-   Web server (Apache/Nginx)

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

8. Compile assets:

    ```bash
    npm run dev
    # atau untuk production
    npm run build
    ```

9. Jalankan server development:
    ```bash
    php artisan serve
    ```

## Struktur Modul

Aplikasi menggunakan laravel-modules dengan struktur berikut:

```
Modules/
├── ModuleName/
│   ├── Config/
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
```

## Penggunaan

1. Akses aplikasi melalui browser
2. Login dengan kredensial yang diberikan
    - Admin: admin@example.com / password
    - User: user@example.com / password
3. Navigasi melalui menu sidebar

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

## Kontribusi

1. Fork project
2. Buat branch fitur (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -am 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

## Lisensi

Aplikasi ini dilisensikan di bawah [Lisensi MIT](LICENSE).

## Kontak

Cabang Dinas Kehutanan Wilayah Trenggalek  
Email: [email-protected]  
Telepon: [nomor-telepon]  
Alamat: [alamat-lengkap]