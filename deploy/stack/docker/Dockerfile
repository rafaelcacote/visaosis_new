FROM composer:2 AS vendor

WORKDIR /app
COPY . /app/
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts --ignore-platform-reqs

FROM node:22-bookworm AS assets

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund
COPY . /app/
RUN npm run build

FROM php:8.3-fpm-bookworm

RUN printf '#!/bin/sh\nexit 101\n' > /usr/sbin/policy-rc.d && chmod +x /usr/sbin/policy-rc.d \
    && apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        supervisor \
        curl \
        libpq-dev \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libwebp-dev \
        libicu-dev \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" pdo_pgsql bcmath intl opcache zip gd pcntl \
    && rm -rf /var/lib/apt/lists/* \
    && rm -f /etc/nginx/sites-enabled/default \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

COPY deploy/stack/docker/php.ini /usr/local/etc/php/conf.d/visaosis.ini
COPY deploy/stack/docker/www.conf /usr/local/etc/php-fpm.d/zz-visaosis.conf
COPY deploy/stack/docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY deploy/stack/docker/supervisord.conf /etc/supervisor/supervisord.conf
COPY deploy/stack/docker/entrypoint.sh /entrypoint.sh
COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

RUN chmod +x /entrypoint.sh \
    && mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

WORKDIR /var/www/html
EXPOSE 80
ENTRYPOINT ["/entrypoint.sh"]
