FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

# Instalar dependencias
RUN apk add --no-cache \
    libpng libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    zip \
    zlib-dev \
    bash \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd zip

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
