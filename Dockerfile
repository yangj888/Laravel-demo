FROM php:8.4-fpm-alpine

# install dependency
RUN apk add --no-cache git curl zip unzip libpng-dev oniguruma-dev postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring gd --with-jpeg --with-webp --with-freetype docker-php-ext-configure

WORKDIR /var/www/html
COPY composer.json ./


# install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader
COPY . .
# start PHP-FPM
CMD ["php-fpm"]

