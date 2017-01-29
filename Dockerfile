FROM php:5.5-cli

RUN docker-php-ext-install pdo pdo_mysql
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt-get -qq update && \
    apt-get install -y \
        netcat-openbsd \
        && \
    apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

WORKDIR /opt/project
