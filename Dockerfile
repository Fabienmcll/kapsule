FROM php:8.3-fpm-alpine AS php_builder
RUN apk add --no-cache libzip-dev libpng-dev zlib-dev autoconf g++ make
RUN docker-php-ext-install pdo_mysql exif zip
WORKDIR /app
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY . .
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY --from=php_builder /app /app
RUN npm install && npm run build

FROM php:8.3-fpm-alpine
RUN apk add --no-cache nginx supervisor libzip-dev libpng-dev
RUN docker-php-ext-install pdo_mysql exif zip

WORKDIR /var/www/html
COPY --from=frontend_builder /app /var/www/html

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

COPY ./docker/nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
