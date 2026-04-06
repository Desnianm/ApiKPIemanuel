#!/bin/bash

# Generate app key if not exists
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx -g "daemon off;"