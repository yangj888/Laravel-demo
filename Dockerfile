FROM php:8.4-fpm-alpine
# install dependency
RUN apk add --no-cache git curl zip unzip libzip-dev nginx supervisor unzip libpng-dev libxml2-dev libjpeg-turbo-dev libwebp-dev freetype-dev oniguruma-dev postgresql-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install pdo pdo_pgsql mbstring gd

WORKDIR /var/www/html
COPY composer.json ./
# install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader
COPY . .
RUN mkdir -p /run/nginx /var/log/supervisor
COPY nginx.conf /etc/nginx/nginx.conf
COPY supervisord.conf /etc/supervisord.conf
EXPOSE 80
RUN ls -l /var/www/html && chown -R nginx:nginx /var/www/html && chomd -R 755 /var/www/html && ls -l /var/www/html
#
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]


