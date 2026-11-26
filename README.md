# Ship CMMS (MNA4003)

Laravel + PostgreSQL. Fictional vessel: Training Vessel Seri Ilmu.

## Setup
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve

## Docker
docker compose up --build -d
docker compose exec app php artisan migrate --seed

## Tests
php artisan test
