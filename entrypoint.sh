#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground