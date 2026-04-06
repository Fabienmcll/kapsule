FROM php:8.3-fpm-alpine AS php_builder
WORKDIR /app
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader
COPY . .
RUN composer install --no-dev --optimize-autoloader


FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY --from=php_builder /app /app
RUN npm install
RUN npm run build

FROM php:8.3-fpm-alpine
RUN apk add --no-cache nginx supervisor
RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html
COPY --from=php_builder /app /var/www/html
COPY --from=frontend_builder /app/public/build ./public/build

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
