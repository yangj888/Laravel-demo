FROM php:8.4-fpm-alpine

# install dependency
RUN apk add --no-cache git curl zip unzip libpng-dev oniguruma-dev postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring gd

WORKDIR /var/www/html

COPY . .

# install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

# start PHP-FPM
CMD ["php-fpm"]

