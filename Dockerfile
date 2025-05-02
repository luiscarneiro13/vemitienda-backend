FROM php:8.2-fpm-alpine

# Instalar dependencias
RUN apk update && apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    unzip \
    nodejs \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar archivos antes de instalar dependencias
COPY composer.json composer.lock package.json package-lock.json ./

# Copiar el código de la aplicación
COPY . .

# Configurar permisos y estructura necesaria
RUN mkdir -p /var/www/storage/logs \
    && touch /var/www/storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/storage bootstrap/cache \
    && chmod -R 775 /var/www/storage bootstrap/cache \
    && cp .env.docker .env || true

# Instalar dependencias y ejecutar migraciones
RUN cd /var/www/html \
    && composer install \
    && php artisan migrate --force

