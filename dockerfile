FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    zip \
    libzip-dev \
    oniguruma-dev

RUN docker-php-ext-install zip

WORKDIR /app

COPY . .

RUN chown -R www-data:www-data /app

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
