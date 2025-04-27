# Etapa 1: Build de dependencias
FROM php:8.2-fpm AS builder

# Instalar dependencias del sistema
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
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar archivos para dependencias
COPY composer.json  package.json  webpack.mix.js ./

# Instalar dependencias backend (PHP)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Instalar dependencias frontend (Node)
RUN npm install

# Copiar todo el proyecto
COPY . .

# Etapa 2: Imagen final (runtime)
FROM php:8.2-fpm

# Instalar dependencias mínimas para producción
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
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer manualmente en producción también
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar solo lo necesario del builder
COPY --from=builder /var/www /var/www

# Establecer permisos correctos
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
