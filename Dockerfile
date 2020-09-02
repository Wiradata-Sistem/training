FROM php:7.3-fpm
RUN apt-get update -y \
    && apt-get install -y wget curl libxml2-dev libssl-dev zlib1g-dev apt-transport-https lsb-release ca-certificates libpng-dev libturbojpeg0 libjpeg-dev libzip-dev librdkafka-dev \
    && wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
    && echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list \
    && pecl install -o -f redis &&  rm -rf /tmp/pear &&  docker-php-ext-enable redis \
    && pecl install rdkafka  &&  rm -rf /tmp/pear && docker-php-ext-enable rdkafka \
    && docker-php-ext-configure gd --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install mbstring iconv xml pdo_mysql phar gd exif intl zip \
    && wget https://getcomposer.org/composer.phar -O /usr/local/bin/composer \
    && chmod a+rx /usr/local/bin/composer

COPY ./* /var/www/
