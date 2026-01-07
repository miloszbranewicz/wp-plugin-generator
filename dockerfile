FROM php:8.2-fpm-alpine

RUN apk add --no-cache zip libzip-dev \
 && docker-php-ext-install zip

WORKDIR /app
COPY . .
USER www-data

CMD ["php-fpm"]
