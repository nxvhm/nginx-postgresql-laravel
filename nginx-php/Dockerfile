FROM php:8.3-fpm-alpine3.20

ARG APP_NAME
ARG POSTGRES_PASSWORD
ARG POSTGRES_USER

ENV php_conf /usr/local/etc/php-fpm.conf
ENV fpm_conf /usr/local/etc/php-fpm.d/www.conf
ENV php_vars /usr/local/etc/php/conf.d/docker-vars.ini

RUN apk update && apk upgrade &&\
	apk add --no-cache \
	bash \
	openssh-client \
	wget \
	supervisor \
	curl \
	nginx \
	libcurl \
	libpq \
	dialog \
	autoconf \
	make \
	libzip-dev \
	bzip2-dev \
	icu-dev \
	tzdata \
	libpng-dev \
	libjpeg-turbo-dev \
	freetype-dev \
	libxslt-dev \
	gcc \
	g++

RUN apk add --no-cache --virtual .sys-deps \
	linux-headers \
	libmcrypt-dev \
	sqlite-dev \
	imap-dev \
	postgresql-dev \
	libwebp-dev \
	zlib-dev \
	libxpm-dev \
	libpng \
	libpng-dev && \
# Install PHP modules
	docker-php-ext-configure gd \
		--enable-gd \
		--with-freetype \
		--with-jpeg && \
	docker-php-ext-install gd && \
	docker-php-ext-install pdo_mysql mysqli pdo_sqlite pgsql pdo_pgsql exif intl xsl soap zip && \
	mkdir -p /var/www/app && \
	mkdir -p /var/log/supervisor && \
	php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
	php composer-setup.php --quiet --install-dir=/usr/bin --filename=composer && \
	rm composer-setup.php

RUN apk add --no-cache --virtual .build-deps php83-dev && \
  pecl install xdebug && \
  docker-php-ext-enable xdebug && \
  apk del .build-deps && \
	apk del gcc linux-headers make autoconf && \
	apk del .sys-deps

ADD conf/supervisord.conf /etc/supervisord.conf

RUN rm -Rf /etc/nginx/nginx.conf
ADD conf/nginx.conf /etc/nginx/nginx.conf

# nginx site conf
RUN mkdir -p /etc/nginx/sites-available/ && \
mkdir -p /etc/nginx/sites-enabled/ && \
mkdir -p /etc/nginx/ssl/ && \
rm -Rf /var/www/* && \
mkdir /var/www/html/
ADD conf/nginx-default.conf /etc/nginx/sites-available/default.conf
RUN ln -s /etc/nginx/sites-available/default.conf /etc/nginx/sites-enabled/default.conf

# tweak php-fpm config
RUN echo "cgi.fix_pathinfo=0" > ${php_vars} &&\
    echo "upload_max_filesize = 1G"  >> ${php_vars} &&\
    echo "post_max_size = 1G"  >> ${php_vars} &&\
    echo "variables_order = \"EGPCS\""  >> ${php_vars} && \
    echo "memory_limit = 512M"  >> ${php_vars} && \
    sed -i \
        -e "s/;catch_workers_output\s*=\s*yes/catch_workers_output = yes/g" \
        -e "s/pm.max_children = 5/pm.max_children = 4/g" \
        -e "s/pm.start_servers = 2/pm.start_servers = 3/g" \
        -e "s/pm.min_spare_servers = 1/pm.min_spare_servers = 2/g" \
        -e "s/pm.max_spare_servers = 3/pm.max_spare_servers = 4/g" \
        -e "s/;pm.max_requests = 500/pm.max_requests = 200/g" \
        -e "s/user = www-data/user = nginx/g" \
        -e "s/group = www-data/group = nginx/g" \
        -e "s/;listen.mode = 0660/listen.mode = 0666/g" \
        -e "s/;listen.owner = www-data/listen.owner = nginx/g" \
        -e "s/;listen.group = www-data/listen.group = nginx/g" \
        -e "s/listen = 127.0.0.1:9000/listen = \/var\/run\/php-fpm.sock/g" \
        -e "s/^;clear_env = no$/clear_env = no/" \
        ${fpm_conf}

RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini && \
	sed -i \
	    -e "s/;opcache/opcache/g" \
	    -e "s/;zend_extension=opcache/zend_extension=opcache/g" \
            /usr/local/etc/php/php.ini

RUN composer global require laravel/installer
ENV PATH="$PATH:/root/.composer/vendor/bin"
ENV APP_NAME ${APP_NAME}
ENV POSTGRES_USER ${POSTGRES_USER}
ENV POSTGRES_PASSWORD ${POSTGRES_PASSWORD}

ADD scripts/start.sh /start.sh
RUN chmod 755 /start.sh

WORKDIR "/var/www/html"


EXPOSE 443 80
