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

# Instalar dependencias
COPY composer.json composer.lock package.json package-lock.json ./
RUN composer install --no-dev --no-scripts --no-autoloader \
    && composer dump-autoload --optimize

# Copiar aplicación
COPY . .

# Configurar permisos (se asegura que el directorio storage exista)
RUN mkdir -p /var/www/storage/logs \
    && touch /var/www/storage/logs/laravel.log \
    && chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage \
    && cp .env.docker .env || true

