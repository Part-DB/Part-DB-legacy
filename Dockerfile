FROM php:7-apache

WORKDIR /var/www/html

RUN set -x && \
    apt-get update && apt-get install -y git unzip locales curl pkg-config libcurl4-openssl-dev zlib1g-dev libicu-dev g++ && \
    docker-php-ext-install mysqli pdo_mysql gettext curl intl mbstring && \
    apt-get remove -y pkg-config libcurl4-openssl-dev zlib1g-dev libicu-dev g++ && \
    apt-get clean && \
    echo "de_DE.UTF-8 UTF-8" >> /etc/locale.gen && \
    echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen && \
    locale-gen && \
    rm -rf /var/lib/apt/lists/*

USER www-data

COPY --chown=www-data:www-data . .

RUN php composer.phar install -o

VOLUME /var/www/html/data

