#!/bin/bash
set -e

echo "Running migrations..."
php artisan migrate --force

echo "Creating storage link..."
# এটি পাবলিক ফোল্ডারের সাথে স্টোরেজ ফোল্ডারের লিংক তৈরি করবে
php artisan storage:link || true

echo "Setting right permissions..."
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground