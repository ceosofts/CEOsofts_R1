#!/bin/bash
echo "Clearing Laravel cache..."
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan optimize:clear
composer dump-autoload
echo "All cache cleared!"
