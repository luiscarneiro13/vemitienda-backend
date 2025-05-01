# Usa como base PHP 8.2 con FPM sobre Alpine Linux
FROM php:8.2-fpm-alpine

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia el archivo .env.example al contenedor (asegúrate de que esté en el contexto de construcción)
COPY .env.example .env.example

# Instala dependencias del sistema y extensiones PHP necesarias para Laravel 10
RUN apk add --no-cache \
      bash \
      curl \
      libpng-dev \
      libjpeg-turbo-dev \
      freetype-dev \
      libzip-dev \
      oniguruma-dev \
      icu-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd intl zip

# Instala Composer (copiándolo desde la imagen oficial de Composer)
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Crea los directorios que Laravel requiere y ajusta permisos:
RUN mkdir -p storage bootstrap/cache storage/logs && \
    cp .env.example .env && \
    chown -R www-data:www-data storage bootstrap/cache storage/logs && \
    chmod -R 775 storage bootstrap/cache storage/logs && \
    touch storage/logs/laravel.log && \
    chown www-data:www-data storage/logs/laravel.log
