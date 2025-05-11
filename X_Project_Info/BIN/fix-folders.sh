#!/bin/bash

echo "=== Checking and fixing directory structure ==="

# Create organization directory if it doesn't exist
if [ ! -d "resources/views/organization" ]; then
  echo "Creating resources/views/organization directory..."
  mkdir -p resources/views/organization
  chmod -R 775 resources/views/organization
  echo "Directory created!"
else
  echo "resources/views/organization already exists."
fi

# Create employees directory if it doesn't exist
if [ ! -d "resources/views/organization/employees" ]; then
  echo "Creating resources/views/organization/employees directory..."
  mkdir -p resources/views/organization/employees
  chmod -R 775 resources/views/organization/employees
  echo "Directory created!"
else
  echo "resources/views/organization/employees already exists."
fi

# Create debug directory if it doesn't exist
if [ ! -d "resources/views/debug" ]; then
  echo "Creating resources/views/debug directory..."
  mkdir -p resources/views/debug
  chmod -R 775 resources/views/debug
  echo "Directory created!"
else
  echo "resources/views/debug already exists."
fi

echo "=== Clearing Laravel cache ==="
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan optimize:clear

echo "=== Done! ==="
