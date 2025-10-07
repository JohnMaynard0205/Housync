web: sh -c 'php artisan optimize:clear && php artisan migrate --force --no-interaction && php -d variables_order=EGPCS -S 0.0.0.0:${PORT:-8080} -t public public/index.php'
