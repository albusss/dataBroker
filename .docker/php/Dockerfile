FROM php:8.1-fpm

# install google-chrome dependencies
RUN apt update && \
    apt upgrade -y && \
    apt install -y wget zip unzip

# install php extensions
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod uga+x /usr/local/bin/install-php-extensions && sync

RUN install-php-extensions @composer \
    pdo \
    pdo_mysql \
    pcntl \
    opcache \
    zip \
    redis

WORKDIR /var/www/
