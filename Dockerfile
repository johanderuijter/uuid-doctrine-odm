FROM php:7.2-cli

RUN pecl install mongodb && docker-php-ext-enable mongodb
