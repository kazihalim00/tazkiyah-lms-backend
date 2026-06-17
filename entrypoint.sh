#!/bin/bash

# ডাটাবেজ মাইগ্রেশন রান করা
echo "Running migrations..."
php artisan migrate --force

# সার্ভার স্টার্ট করা
echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=$PORT