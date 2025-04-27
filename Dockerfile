# Imagen oficial PHP con extensiones requeridas
FROM php:8.2-fpm

# Instalar dependencias de sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setear el directorio de trabajo
WORKDIR /var/www

# Copiar composer.json y package.json
COPY composer.json package.json ./
COPY package-lock.json ./
COPY webpack.mix.js ./

# Instalar dependencias
RUN composer install
RUN npm install

# Copiar todo el código (pero lo sobreescribimos por volumen en docker-compose para dev)
COPY . .

# Establecer permisos (opcional)
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
