# Build stage
FROM php:8.4-fpm AS builder

# Install git terlebih dahulu
RUN apt-get update && apt-get install -y git

# Konfigurasi GitHub token menggunakan secrets (aman)
RUN --mount=type=secret,id=GITHUB_TOKEN \
    git config --global url."https://$(cat /run/secrets/GITHUB_TOKEN):x-oauth-basic@github.com/".insteadOf "https://github.com/"

WORKDIR /var/www/html

COPY . .

RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Sebelum RUN composer install, tambahkan:
RUN chown -R www-data:www-data /var/www/html \
&& su www-data -s /bin/sh -c "composer install --no-dev --no-interaction --optimize-autoloader"

# Production stage
FROM php:8.4-fpm

WORKDIR /var/www/html

COPY --from=builder /var/www/html /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage \
    && chmod -R 775 /var/www/html/storage