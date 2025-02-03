ARG VERSION=8.2

FROM php:${VERSION}-fpm-alpine AS base

LABEL version="1.0.0" \
      authors="IDMarinas"

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

# Install system dependencies
RUN apk add --no-cache \
    unzip \
    git

RUN install-php-extensions  \
    bcmath \
    ctype \
    curl \
    exif \
    gd \
    iconv \
    intl \
    json \
    mbstring \
    opcache \
    pdo \
    pdo_mysql \
    pcntl \
    session \
    xsl \
    zip \
    @composer

#
# Imagen para desarrollo
#
FROM base AS dev

RUN mv "${PHP_INI_DIR}/php.ini-development" "${PHP_INI_DIR}/php.ini"

COPY --chmod=600 ./key_ssh/id_rsa /root/.ssh/id_rsa

# Install system dependencies
RUN apk add --no-cache \
    bash \
    zlib-dev \
    icu-dev \
    openssh \
    rsync \
    && echo 'alias sf="php bin/console"' >> /etc/bash/bashrc \
    && echo 'alias symfony="php bin/console"' >> /etc/bash/bashrc \
    && rm -rf /var/lib/apt/lists/*

RUN install-php-extensions xdebug

RUN composer global require deployer/deployer --no-interaction --no-progress \
    && composer global config --no-plugins allow-plugins.symfony/flex true \
    && composer global require --no-progress --no-scripts --no-plugins symfony/flex \
    && echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> /etc/bash/bashrc


#
# Imagen para producción
#
FROM base AS prod

RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"

RUN apk add --no-cache \
    acl \
    && rm -rf /var/lib/apt/lists/*