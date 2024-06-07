FROM php:8.3.8RC1-zts-alpine3.20

RUN apk update && apk add --no-cache git autoconf g++ make

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN install-php-extensions \
    mbstring \
    sockets \
    && pecl install parallel \
    && docker-php-ext-enable parallel

WORKDIR /var/www/html

