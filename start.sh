#!/bin/bash
set -e

echo "=== Sophisticate Intensive Classes — Railway Startup ==="

# Wait for MySQL to be ready (max 60s)
echo "Waiting for database connection..."
MAX_RETRIES=30
COUNT=0
until php artisan db:monitor --max=1 2>/dev/null || [ $COUNT -ge $MAX_RETRIES ]; do
    echo "  DB not ready yet... ($COUNT/$MAX_RETRIES)"
    sleep 2
    COUNT=$((COUNT+1))
done

echo "Running migrations..."
php artisan migrate --force

echo "Seeding admin account..."
php artisan db:seed --class=AdminSeeder --force

echo "Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting PHP server on port ${PORT:-8080}..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
