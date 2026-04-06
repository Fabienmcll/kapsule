FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY . .
RUN npm install && npm run build

FROM php:8.3-fpm-alpine

# Installation des outils nécessaires
RUN apk add --no-cache nginx supervisor
RUN docker-php-ext-install pdo_mysql

WORKDIR /var/www/html
COPY . .

# On récupère seulement le résultat du build JS
COPY --from=frontend_builder /app/public/build ./public/build

# On installe les dépendances PHP sans les outils de test
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader

# On donne les droits à Laravel pour écrire dans les logs
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
