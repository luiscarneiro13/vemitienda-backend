FROM php:8.2-fpm-alpine

WORKDIR /var/www/html

RUN apk add --no-cache zlib-dev libpng-dev \
    && docker-php-ext-install pdo pdo_mysql gd
