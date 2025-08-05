FROM composer:2 AS vendor
WORKDIR /app
COPY src/composer.json .
RUN composer install --no-dev --prefer-dist --no-progress --no-scripts --optimize-autoloader

FROM php:8.1-fpm
# системные зависимости
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libpng-dev libonig-dev libxml2-dev libzip-dev \
 && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
 && apt-get purge -y --auto-remove \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
# готовые зависимости
COPY --from=vendor /app/vendor/ ./vendor/
# исходники
COPY src/ .
# пустая runtime-директория
RUN mkdir -p runtime && chown -R www-data:www-data /var/www/html

EXPOSE 9000
CMD ["php-fpm"]