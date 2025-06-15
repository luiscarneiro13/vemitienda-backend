# Usa la imagen oficial de PHP-FPM con Alpine Linux
FROM php:8.2-fpm-alpine

# Instala dependencias necesarias, incluyendo libzip-dev
RUN apk add --no-cache \
    bash mysql-client zip unzip git nodejs npm \
    libjpeg-turbo-dev libpng-dev freetype-dev icu icu-dev \
    libzip-dev curl yt-dlp libwebp-dev

# Instala extensiones de PHP recomendadas para Laravel, incluyendo zip y gd
RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp && \
    docker-php-ext-install pdo pdo_mysql bcmath intl zip gd

# Instala Composer en la versión exacta (2.5.0)
RUN curl -sS https://getcomposer.org/download/2.5.0/composer.phar -o /usr/local/bin/composer && \
    chmod +x /usr/local/bin/composer

# Define el directorio de trabajo
WORKDIR /var/www

COPY ./vemitienda .

# Expone el puerto necesario
EXPOSE 9000
