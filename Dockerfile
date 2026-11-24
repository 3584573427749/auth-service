#
# Builder stage
#
FROM composer:2 AS builder

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-scripts \
    --optimize-autoloader

COPY . .

RUN composer dump-autoload \
    --no-dev \
    --classmap-authoritative


#
# Runtime stage
#
FROM php:8.5.12-fpm-alpine

RUN apk add --no-cache \
    bash \
    curl \
    mariadb-client \
    oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql

WORKDIR /var/www

COPY --from=builder /app /var/www

COPY docker/*.sh /

RUN chmod +x /*.sh

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 8080

CMD ["php-fpm"]