FROM php:7.3-fpm-alpine
# install required extension
RUN apk add libxml2-dev

# install mysql pdo driver
RUN docker-php-ext-install pdo_mysql \
    bcmath \
    ctype \
    fileinfo \
    json \
    mbstring \
    tokenizer \
    xml

# composer
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_HOME /composer
RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/bin --filename=composer
