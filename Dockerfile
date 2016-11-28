FROM php:5.5-cli

RUN docker-php-ext-install pdo pdo_mysql

WORKDIR /opt/project
