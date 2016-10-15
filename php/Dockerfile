FROM php:5.6-fpm

RUN usermod -u 1000 www-data

COPY bin/* /usr/local/bin/
RUN chmod +x -R /usr/local/bin/

COPY conf/php.ini /usr/local/etc/php/conf.d/
COPY conf/pool.conf /usr/local/etc/php/

WORKDIR /app

RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN apt-install \
    apt-utils \
    less \
    libssl-dev \
    git \
    zip \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng12-dev

RUN mkdir -p /tmp \
    && docker-php-ext-configure gd \
    --with-freetype-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install \
    gd \
    pdo \
    pdo_mysql \
    pcntl \
    && docker-php-pecl-install zip memcached opcache

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && sed -i '1 a xdebug.remote_autostart=true' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_mode=req' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_handler=dbgp' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_connect_back=1 ' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_port=9000' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_host=127.0.0.1' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && sed -i '1 a xdebug.remote_enable=1' /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ENTRYPOINT ["/usr/local/bin/entrypoint.sh", "php-fpm"]
