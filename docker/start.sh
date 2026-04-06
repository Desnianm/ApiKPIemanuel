#!/bin/bash

# Copy .env.example to .env if not exists
if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

# Set environment variables dari Railway ke .env
cat > /var/www/.env << EOF
APP_NAME="${APP_NAME:-Laravel}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stderr
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"

CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

L5_SWAGGER_GENERATE_ALWAYS=true
EOF

# Generate app key if not set
php artisan key:generate --force

# Run migrations
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Generate swagger
php artisan l5-swagger:generate

# Start PHP-FPM
php-fpm -D

# Start Nginx
nginx -g "daemon off;"