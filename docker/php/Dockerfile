FROM php:7.4-fpm-alpine

RUN apk add --update php-curl php-mbstring php-xml --no-cache
RUN docker-php-ext-install pdo pdo_mysql

# copy resources files
COPY ./res ./res
# copy application files
COPY ./src ./src
COPY ./index.php ./index.php
# copy application config file
COPY ./docker/php/config.php ./src/application/config/config.php

RUN chown www-data:www-data /var/www/html/res/driversforms
RUN chown www-data:www-data /var/www/html/res/userslogos
