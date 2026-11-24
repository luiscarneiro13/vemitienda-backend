FROM php:8.3-fpm-alpine AS base

RUN apk add --no-cache \
        git \
        unzip \
        libpng-dev \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        gd \
        zip \
        exif \
        pcntl \
        intl \
        opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/zz-fpm-tuning.conf /usr/local/etc/php-fpm.d/zz-fpm-tuning.conf

WORKDIR /var/www

# ---------------------------------------------------------------------------
FROM base AS dev

RUN apk add --no-cache nodejs npm

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

# ---------------------------------------------------------------------------
FROM node:20-alpine AS node_builder

WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------------------------------------------------------------------------
FROM base AS prod

RUN { \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'opcache.memory_consumption=192'; \
    } > /usr/local/etc/php/conf.d/opcache-prod.ini

COPY . .
COPY --from=node_builder /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/entrypoint.prod.sh /usr/local/bin/entrypoint.prod.sh
RUN chmod +x /usr/local/bin/entrypoint.prod.sh

ENTRYPOINT ["entrypoint.prod.sh"]
CMD ["php-fpm"]
