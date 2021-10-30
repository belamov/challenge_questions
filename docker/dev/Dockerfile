FROM php:8-cli-alpine

# Install modules
RUN set -xe && apk update && apk add --no-cache \
    shadow \
    libzip \
    bash \
    curl \
    icu-dev \
    libmcrypt-dev \
    libxml2-dev \
    libzip-dev \
    pcre-dev \
    autoconf \
    g++ \
    make \
    jpegoptim \
    unrar \
    freetype \
    freetype-dev \
    zlib \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-install intl zip sockets exif

# Logs
RUN mkdir -p /var/log/php8-fpm && \
    chown -R www-data:www-data /var/log/php8-fpm

# Install composer && global asset plugin
ENV COMPOSER_HOME $HOME/.composer
ENV PATH $HOME/.composer/vendor/bin:$PATH
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Xdebug
RUN pecl install xdebug \
    &&  docker-php-ext-enable xdebug

# composer require checker
RUN composer global require maglnet/composer-require-checker \
    && ln -s /.composer/vendor/bin/composer-require-checker /usr/local/bin/composer-require-checker

# composer unused checker
RUN curl -L https://github.com/icanhazstring/composer-unused/releases/latest/download/composer-unused.phar -o composer-unused
RUN chmod a+x composer-unused
RUN mv composer-unused /usr/local/bin/composer-unused

WORKDIR /app

EXPOSE 8000

CMD php -S 0.0.0.0:8000 -t /app/public