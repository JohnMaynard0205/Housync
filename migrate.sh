#!/bin/bash
echo "Starting database migration..."
php artisan config:clear
php artisan migrate --force --no-interaction
php artisan config:cache
echo "Migration completed!"
