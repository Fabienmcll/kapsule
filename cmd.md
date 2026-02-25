- Build du projet : `docker run --rm -v ${PWD}:/app -w /app laravelsail/php84-composer:latest composer install` (windows)
  `docker compose build`
    - Géneration de la clé Laravel `docker compose exec laravel.test php artisan key:generate`
- Lancement du projet
