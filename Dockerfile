FROM php:5.5-cli

RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

WORKDIR /opt/project
