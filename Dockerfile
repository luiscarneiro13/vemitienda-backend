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

# Crear el directorio de logs de Laravel y asignar permisos
RUN mkdir -p /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage

# Instalar Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer
