FROM php:7-apache

RUN apt-get update && apt-get install -y git unzip locales curl pkg-config libcurl4-openssl-dev zlib1g-dev libicu-dev g++
RUN docker-php-ext-install mysqli pdo_mysql gettext curl intl mbstring

WORKDIR /var/www/html
COPY . .

RUN php composer.phar install -o

RUN chown -R www-data:www-data . && \
  find . -type d -print0 | xargs -0 chmod 555 && \
  find . -type f -print0 | xargs -0 chmod 444 && \
  find data -type d -print0 | xargs -0 chmod 755 && \
  find data -type f -print0 | xargs -0 chmod 644 && \
  chmod 755 .

RUN echo "de_DE.UTF-8 UTF-8" >> /etc/locale.gen && \
  echo "en_US.UTF-8 UTF-8" >> /etc/locale.gen && \
  locale-gen
