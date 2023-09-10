FROM alpine:latest

# Arguments defined in docker-compose.yml
ARG USER_NAME=www
ARG GROUP_NAME=laravel
ARG USER_ID
ARG GROUP_ID

WORKDIR /var/www

RUN apk --no-cache update \
    && apk --no-cache upgrade \
    && apk --no-cache add \
    --repository https://dl-cdn.alpinelinux.org/alpine/edge/main \
    --repository https://dl-cdn.alpinelinux.org/alpine/edge/community \
    net-tools \
    nginx \
    curl \
    supervisor \
    php82 \
    php82-common \
    php82-fpm \
    php82-curl \
    php82-fileinfo \
    php82-mbstring \
    php82-pdo \
    php82-pdo_mysql \
    php82-xml \
    php82-zip \
    php82-session \
    php82-tokenizer \
    php82-dom \
    php82-xmlwriter \
    php82-openssl \
    php82-gd \
    php82-phar \
    php82-pcntl \
    php82-posix \
    php82-ctype \
    php82-pecl-xdebug \
    && ln -sf /usr/bin/php82 /usr/bin/php

# Install PHP Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Remove Cache
RUN rm -rf /var/cache/apk/* /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY ./docker/app/php/xdebug.ini /etc/php82/conf.d/50_xdebug.ini

RUN sed -i 's/memory_limit = 128M/memory_limit = 64M/g' /etc/php82/php.ini
RUN sed -i 's/;opcache.enable=1/opcache.enable=1/g' /etc/php82/php.ini

RUN addgroup --gid $GROUP_ID --system $GROUP_NAME
RUN adduser -G root --system -D --shell /bin/sh -u $USER_ID $USER_NAME

RUN sed -i "s/user = nobody/user = www/g" /etc/php82/php-fpm.d/www.conf
RUN sed -i "s/group = nobody/group = laravel/g" /etc/php82/php-fpm.d/www.conf

#COPY . /var/www --chown=$USER_NAME:$GROUP_NAME

RUN chown -R $USER_NAME:$GROUP_NAME /var/www
#USER $USER_NAME

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
