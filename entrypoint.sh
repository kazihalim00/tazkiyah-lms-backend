#!/bin/bash
set -e

echo "Setting right permissions..."
# স্টোরেজ এবং ক্যাশ ফোল্ডারের পারমিশন রানটাইমে দেওয়া হলো
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache

echo "Running migrations..."
php artisan migrate --force

echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Apache..."
exec apache2-foreground