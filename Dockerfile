FROM php:8.4-fpm-alpine
# install dependency
RUN apk add --no-cache git curl zip unzip libzip-dev nginx supervisor unzip libpng-dev libxml2-dev libjpeg-turbo-dev libwebp-dev freetype-dev oniguruma-dev postgresql-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install pdo pdo_pgsql mbstring gd

WORKDIR /var/www/html
# install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV COMPOSER_ROOT_VERSION=1.0.0
RUN composer create-project laravel/laravel laravel-demo
RUN chmod -R 777 laravel-demo && ls -al laravel-demo && ls -al laravel-demo/storage && ls -al laravel-demo/bootstrap/cache
RUN mkdir -p /run/nginx /var/log/supervisor
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf
EXPOSE 80
#
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]


