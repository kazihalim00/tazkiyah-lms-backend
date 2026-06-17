#!/bin/bash
set -e
echo "Running migrations..."
php artisan migrate --force

echo "Linking storage..."
if [ ! -L /var/www/html/public/storage ]; then
    php artisan storage:link
else
    echo "Storage link already exists, skipping."
fi

echo "Starting Apache..."
exec apache2-foreground