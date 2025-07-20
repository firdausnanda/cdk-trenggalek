# syntax=docker/dockerfile:1.4

# ------------------------
# Builder Stage
# ------------------------
FROM php:8.4-fpm AS builder

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev patch \
    curl libpng-dev libonig-dev libxml2-dev

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configure proper Composer cache permissions
RUN mkdir -p /var/www/.composer/cache && \
    chown -R www-data:www-data /var/www/.composer

WORKDIR /var/www/html

# 4. First copy only composer-related files
COPY --chown=www-data:www-data composer.json composer.lock ./
COPY --chown=www-data:www-data composer-patches.json ./
COPY --chown=www-data:www-data patches/ ./patches/

# 5. Create required directories and empty helper files
RUN mkdir -p app/Helper && \
    touch app/Helper/common.php app/Helper/database.php app/Helper/hook.php && \
    chown -R www-data:www-data app/Helper

# 6. Configure GitHub authentication (if needed)
ARG GITHUB_TOKEN
RUN --mount=type=secret,id=GITHUB_TOKEN \
    if [ -n "$GITHUB_TOKEN" ]; then \
    git config --global url."https://x-access-token:$(cat /run/secrets/GITHUB_TOKEN)@github.com/".insteadOf "https://github.com/" && \
    su www-data -s /bin/sh -c "composer config -g github-oauth.github.com $(cat /run/secrets/GITHUB_TOKEN)"; \
    fi

# 7. Update opis/closure to PHP 8.4 compatible version
RUN su www-data -s /bin/sh -c "composer require opis/closure:^3.7.0 --no-update"

# 8. Install dependencies (without running post-install scripts)
RUN su www-data -s /bin/sh -c "composer install --no-dev --no-interaction --no-scripts --no-plugins"

# 9. Now copy the rest of the application
COPY --chown=www-data:www-data . .

# 10. Run post-install scripts separately
RUN su www-data -s /bin/sh -c "composer run-script post-autoload-dump"

# 11. Install PHP extensions
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

# Set directory permissions
RUN chmod -R 775 storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache