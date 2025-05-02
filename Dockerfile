FROM php:8.2-fpm

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libonig-dev \
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

# Configurar permisos
RUN chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage \
    && cp .env.docker .env

CMD ["php-fpm"]
