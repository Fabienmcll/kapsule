FROM php:8.3-fpm-alpine AS php_builder

RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    zlib-dev

RUN docker-php-ext-install pdo_mysql exif zip

WORKDIR /app
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN composer install --no-dev --no-scripts --no-autoloader --ignore-platform-reqs

COPY . .
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY --from=php_builder /app /app
RUN npm install
RUN npm run build

FROM php:8.3-fpm-alpine
RUN apk add --no-cache nginx supervisor libzip libpng
RUN docker-php-ext-install pdo_mysql exif zip

WORKDIR /var/www/html
COPY --from=php_builder /app /var/www/html
COPY --from=frontend_builder /app/public/build ./public/build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
