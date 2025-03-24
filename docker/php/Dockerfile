FROM composer:latest AS composer-builder
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    libzip-dev \
    && docker-php-ext-install zip pdo pdo_mysql \
    && docker-php-ext-enable zip pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --from=composer-builder /app/vendor /var/www/html/vendor
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

ENV PATH="/var/www/html/vendor/bin:$PATH"

EXPOSE 9000