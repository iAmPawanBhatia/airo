FROM php:8.3-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libpng-dev libzip-dev libonig-dev \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring gd zip opcache \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

WORKDIR /var/www/html

EXPOSE 9000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
