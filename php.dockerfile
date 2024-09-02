FROM php:8.2-fpm-alpine

RUN touch /var/log/error_log

ADD ./php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN addgroup -g 1000 wp && adduser -G wp -g wp -s /bin/sh -D wp

RUN mkdir -p /var/www/html

RUN chown wp:wp /var/www/html

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Aggiorna i pacchetti e installa le dipendenze necessarie per GD, ZIP, EXIF, Imagick e Intl
RUN apk update && apk add --no-cache \
	libpng-dev \
	libjpeg-turbo-dev \
	freetype-dev \
	libzip-dev \
	oniguruma-dev \
	icu-dev \
	imagemagick \
	imagemagick-libs \
	imagemagick-dev \
	libtool \
	autoconf \
	g++ \
	make \
	&& docker-php-ext-configure gd \
		--with-freetype \
		--with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd zip exif intl \
	&& pecl install imagick \
	&& docker-php-ext-enable imagick \
	&& apk del oniguruma-dev libtool autoconf g++ imagemagick-dev

# Aggiorna i pacchetti e installa cURL e altre dipendenze necessarie
RUN apk update && apk add --no-cache \
	curl \
	libcurl \
	curl-dev \
	busybox-suid \
	&& docker-php-ext-install curl opcache \
	&& rm -rf /var/cache/apk/*

# Copia il file di configurazione di OPcache
COPY ./nginx/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Pulizia finale
RUN rm -rf /var/cache/apk/*

RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar

RUN chmod +x wp-cli.phar && mv wp-cli.phar /usr/local/bin/wp



