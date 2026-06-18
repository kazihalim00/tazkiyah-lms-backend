#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Creating storage link..."
php artisan storage:link || true

echo "Clearing caches to prevent env errors..."
# cache এর বদলে optimize:clear ব্যবহার করা হলো, এটি সবচেয়ে নিরাপদ
php artisan optimize:clear

echo "Setting right permissions for Apache..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Starting Apache..."
exec apache2-foreground