FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
   git \
   libzip-dev \
   zip \
   libfreetype6-dev \
   libjpeg62-turbo-dev \
   libpng-dev

RUN docker-php-ext-install pdo_mysql && \
    docker-php-ext-install zip && \
    docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ && \
    docker-php-ext-install gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock /var/www/
RUN composer install

ADD ./docker/wait-for-it.sh /
ADD ./docker/entrypoint.sh /

ENTRYPOINT ["/entrypoint.sh"]
