#!/bin/bash

echo "Fixing Organization Chart issues..."

# Clear Laravel cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear

# Clear relationship caches
php artisan model:clear

# Clear composer's autoload files
composer dump-autoload

echo "Fix completed. Try accessing the organization chart again."
