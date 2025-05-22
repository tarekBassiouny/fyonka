#!/bin/bash

set -e
echo "🚀 Starting deployment..."

cd /var/www/fyonka
git pull origin main

docker exec app composer install --no-dev --optimize-autoloader
docker exec app php artisan migrate --force

docker exec app php artisan config:clear
docker exec app php artisan cache:clear
docker exec app php artisan route:clear
docker exec app php artisan view:clear

docker exec node npm ci
docker exec node npm run build

docker exec app php artisan db:seed --force

echo "✅ Deployment complete."
