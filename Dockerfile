ARG PHP_VERSION=7.4
FROM php:${PHP_VERSION}-cli

RUN apt-get update \
    && apt-get install -y \
        libzip-dev \
    && pecl install \
        mongodb \
        zip \
    && docker-php-ext-enable \
        mongodb \
        zip

COPY --from=composer /usr/bin/composer /usr/bin/composer
