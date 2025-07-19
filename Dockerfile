# syntax=docker/dockerfile:1.4

# ------------------------
# Builder Stage
# ------------------------
FROM php:8.4-fpm AS builder

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    curl libpng-dev libonig-dev libxml2-dev

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configure proper Composer cache permissions
RUN mkdir -p /var/www/.composer/cache && \
    chown -R www-data:www-data /var/www/.composer

# 4. Configure GitHub authentication
ARG GITHUB_TOKEN
RUN --mount=type=secret,id=GITHUB_TOKEN \
    git config --global url."https://x-access-token:$(cat /run/secrets/GITHUB_TOKEN)@github.com/".insteadOf "https://github.com/" && \
    su www-data -s /bin/sh -c "composer config -g github-oauth.github.com $(cat /run/secrets/GITHUB_TOKEN)"

WORKDIR /var/www/html

# 5. Copy only what's needed for composer install
COPY --chown=www-data:www-data composer.json composer.lock ./
COPY --chown=www-data:www-data composer-patches.json ./
COPY --chown=www-data:www-data patches/ ./patches/

# 7. Install dependencies as www-data
RUN su www-data -s /bin/sh -c "composer install --no-dev --no-interaction --optimize-autoloader --no-plugins"

# 8. Copy the rest of the application
COPY --chown=www-data:www-data . .

# 9. Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ------------------------
# Production Stage
# ------------------------
FROM php:8.4-fpm

# Install runtime dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Copy from builder with proper ownership
COPY --from=builder --chown=www-data:www-data /var/www/html /var/www/html
COPY --from=builder --chown=www-data:www-data /var/www/.composer /var/www/.composer

# Set directory permissions
RUN chmod -R 775 storage bootstrap/cache