FROM php:8.3-fpm

RUN docker-php-ext-install pdo pdo_mysql

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/php/php.ini /usr/local/etc/php/

WORKDIR /var/www/html

COPY ./src /var/www/html

RUN chown -R www-data:www-data /var/www/html

EXPOSE 9000