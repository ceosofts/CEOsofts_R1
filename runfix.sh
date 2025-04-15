#!/bin/bash
echo "Clearing Laravel cache..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled
php artisan optimize:clear
echo "Regenerating composer autoloader..."
composer dump-autoload
echo "Done!"
