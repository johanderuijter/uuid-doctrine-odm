FROM php:5.6-cli

RUN apt-get update && apt-get install -y libssl-dev

RUN pecl install mongo && docker-php-ext-enable mongo
