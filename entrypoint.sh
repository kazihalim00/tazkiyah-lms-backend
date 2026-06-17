#!/bin/bash
set -e
echo "Running migrations..."
php artisan migrate --force

echo "Linking storage..."
php artisan storage:link

echo "Starting Apache..."
exec apache2-foreground