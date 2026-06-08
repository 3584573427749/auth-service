FROM php:8.5-fpm-alpine

RUN apk add --no-cache bash git curl mariadb-client oniguruma-dev \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction

COPY . .

# Entrypoint för att köra migration
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
EXPOSE 8080
