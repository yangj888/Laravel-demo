FROM php:8.4-fpm-alpine

ARG user=www-data
# install dependency
RUN apk add --no-cache git curl zip libzip-dev nginx unzip libpng-dev libonig-dev libxml2-dev libjpeg-turbo-dev libwebp-dev freetype-dev oniguruma-dev postgresql-dev \
    && docker-php-ext-configure gd --with-jpeg --with-webp --with-freetype \
    && docker-php-ext-install pdo pdo_pgsql mbstring gd zip bcmath pcntl exif

WORKDIR /var/www
COPY composer.json ./
# install Composer
COPY --from=public.ecr.aws/composer/composer:latest-bin /usr/bin/composer /usr/bin/composer
RUN mkdir -p /home/$user/.composer && chown -R $user:$user /home/$user
COPY ./docker/default.conf /etc/nginx/sites-enabled/default
COPY ./docker/entrypoint.sh /etc/entrypoint.sh
RUN chmod +x /etc/entrypoint.sh

RUN composer install --no-dev --optimize-autoloader
COPY --chown=www-data:www-data . /var/www
EXPOSE 80
# start
ENTRYPOINT ["/etc/entrypoint.sh"]

