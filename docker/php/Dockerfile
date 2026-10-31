FROM php:8.4.27-fpm-alpine

# Match www-data to the host user that owns the bind-mounted project,
# so storage/ and bootstrap/cache/ stay writable.
ARG UID=1000
ARG GID=1000

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN install-php-extensions bcmath gd intl opcache pdo_mysql zip \
    && apk add --no-cache shadow \
    && groupmod -o -g "${GID}" www-data \
    && usermod -o -u "${UID}" -g www-data www-data \
    && apk del shadow

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini

WORKDIR /var/www

USER www-data
