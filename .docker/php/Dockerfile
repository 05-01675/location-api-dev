FROM alpine:latest

WORKDIR /var/www/html/

# Essentials
RUN echo "UTC" > /etc/timezone
RUN apk add --no-cache zip unzip curl sqlite nginx supervisor
RUN apk add --no-cache git file re2c autoconf make g++
RUN apk add --no-cache vim zlib zlib-dev ca-certificates openssl libmemcached rabbitmq-c supervisor redis

# Installing bash
RUN apk add bash
RUN sed -i 's/bin\/ash/bin\/bash/g' /etc/passwd

# Install PHP Packages

RUN apk add --no-cache php8 \
    php8-common \
    php8-pear \
    php8-fpm \
    php8-pdo \
    php8-opcache \
    php8-zip \
    php8-phar \
    php8-iconv \
    php8-cli \
    php8-curl \
    php8-openssl \
    php8-mbstring \
    php8-tokenizer \
    php8-fileinfo \
    php8-json \
    php8-xml \
    php8-xmlwriter \
    php8-simplexml \
    php8-dom \
    php8-pdo_mysql \
    php8-pdo_sqlite \
    php8-tokenizer \
    php8-pecl-redis

# RUN ln -s /usr/bin/php8 /usr/bin/php

# Install Composer
# RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
# RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
# RUN rm -rf composer-setup.php
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

# Configure nginx
COPY .docker/config/nginx.conf /etc/nginx/nginx.conf

# Configure php-fpm
COPY .docker/config/fpm-pool.conf /etc/php8/php-fpm.d/custom.conf
COPY .docker/config/php.ini /etc/php8/conf.d/custom.ini

# Configure supervisord
COPY .docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN apk --update upgrade --no-cache
# Install PHP Driver
RUN pecl channel-update pecl.php.net
RUN pecl install -f mongodb; \
    echo 'extension=mongodb.so' > /etc/php8/conf.d/mongodb.ini;
RUN pecl install -f igbinary; \
    echo 'extension=igbinary.so' > /etc/php8/conf.d/igbinary.ini;
#RUN pecl install -f redis; \
#    echo 'extension=redis.so' > /etc/php7/conf.d/redis.ini;

# Add application
RUN mkdir -p /var/www/html

# add source
ADD . /var/www/html/Project-Mars-BE
WORKDIR /var/www/html/Project-Mars-BE
RUN chmod -R 777 storage

# set environment file
ADD .env /var/www/html/Project-Mars-BE

# composer update
RUN composer update
RUN php /usr/bin/composer clearcache
RUN php /usr/bin/composer install --no-cache
# RUN php /usr/bin/composer update sra/core
RUN php /usr/bin/composer dumpautoload
# Replace the directory after the /html/ with the project name
RUN cd /var/www/html/Project-Mars-BE
# Comment line if using nosql database
# RUN php artisan migrate
# Comment lines below if using lumen
RUN php artisan vendor:publish --all
RUN php artisan package:discover
# RUN php artisan passport:install
RUN php artisan config:cache
RUN php artisan config:clear
# RUN php artisan db:build
# RUN php artisan jwt:secret
RUN php artisan key:generate

EXPOSE 22 80 443
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
