FROM php:7.1-fpm

RUN usermod -u 1000 www-data

COPY conf/php.ini /usr/local/etc/php/conf.d/
COPY conf/pool.conf /usr/local/etc/php/

WORKDIR /app

RUN apt-get update
RUN apt-get install -y \
     apt-utils \
     zlib1g-dev \
     libicu-dev \
     g++ \
     less \
     libssl-dev \
     libzip-dev \
     git \
     zip \
     wget

RUN wget -O /usr/local/bin/composer https://getcomposer.org/download/1.10.5/composer.phar

COPY bin/* /usr/local/bin/
RUN chmod +x -R /usr/local/bin/

RUN mkdir -p /tmp

RUN docker-php-ext-install \
     pdo \
     pdo_mysql \
     pcntl

RUN pecl install \
     xdebug-2.5.5 \
     zip \
     opcache

RUN docker-php-ext-enable xdebug \
    && sed -i '1 a xdebug.remote_autostart=true' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_mode=req' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_handler=dbgp' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_connect_back=1 ' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_port=9000' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_host=127.0.0.1' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_enable=1' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENTRYPOINT ["/usr/local/bin/entrypoint.sh", "php-fpm"]
