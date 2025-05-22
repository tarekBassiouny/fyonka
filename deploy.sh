#!/bin/bash

set -e
echo "Starting deployment..."

# 1. Pull latest code
git pull origin main

# 2. Create .env if not present
if [ ! -f .env ]; then
  echo "🔐 .env not found — running setup-env.sh"
  bash ./setup-env.sh
fi

# 3. Install PHP dependencies
docker exec app composer install --no-dev --optimize-autoloader

# 4. Build frontend (Vite)
docker run --rm \
  -v $(pwd):/var/www/html \
  -w /var/www/html \
  node:20 \
  sh -c "npm ci && npm run build"

# 5. Set proper permissions
docker exec app chown -R www-data:www-data storage bootstrap/cache
docker exec app chmod -R 775 storage bootstrap/cache

# 6. Laravel cache + migrate
docker exec app php artisan config:clear
docker exec app php artisan route:clear
docker exec app php artisan view:clear
docker exec app php artisan migrate --force
docker exec app php artisan config:cache
docker exec app php artisan route:cache

# 7. Seed only if admin not present
docker exec app php artisan db:seed --force

echo "Deployment complete."
