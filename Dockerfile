FROM php:8.2-fpm

# Stage 1: Instalación de dependencias
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    unzip \
    nodejs \
    cron \
    supervisor \
    && docker-php-ext-configure gd --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    && pecl install redis \
    && docker-php-ext-enable redis

# Stage 2: Configuración de Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Stage 3: Optimización de dependencias
COPY composer.json composer.lock package.json package-lock.json /var/www/
WORKDIR /var/www
RUN composer install --no-dev --no-scripts --no-autoloader \
    && composer dump-autoload --optimize

# Stage 4: Copia de la aplicación
COPY . .

# Stage 5: Configuración final
RUN chown -R www-data:www-data /var/www/storage \
    && chmod -R 775 /var/www/storage \
    && cp .env.docker .env

CMD ["php-fpm"]
