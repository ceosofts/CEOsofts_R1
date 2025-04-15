#!/bin/bash

echo "=== CEOsofts R1 - System Repair Tool ==="
echo "Repairing system files and directories..."

# Create necessary directories
echo "Creating directories..."
mkdir -p resources/views/organization/employees
mkdir -p resources/views/debug
chmod -R 775 resources/views/organization
chmod -R 775 resources/views/debug

# Clear cache thoroughly
echo "Clearing cache..."
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan optimize:clear
composer dump-autoload

# Check if blade files exist
if [ ! -f "resources/views/organization/employees/index.blade.php" ]; then
  echo "Employee index view not found, creating fallback..."
  echo "<x-app-layout>
  <x-slot name=\"header\">
    <h2 class=\"font-semibold text-xl text-gray-800 leading-tight\">
      {{ __('พนักงาน (Simple Fallback)') }}
    </h2>
  </x-slot>

  <div class=\"py-12\">
    <div class=\"max-w-7xl mx-auto sm:px-6 lg:px-8\">
      <div class=\"bg-white overflow-hidden shadow-sm sm:rounded-lg\">
        <div class=\"p-6 text-gray-900\">
          <p>หน้าพนักงาน (ทำงานได้)</p>
          <p>นี่เป็นหน้า fallback ที่สร้างโดยสคริปต์ซ่อมแซมระบบ</p>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>" > resources/views/organization/employees/index.blade.php
fi

# Create a test HTML file
echo "Creating a basic HTML test file..."
echo "<!DOCTYPE html>
<html>
<head>
  <title>Basic Test Page</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; }
  </style>
</head>
<body>
  <h1>Basic HTML Test</h1>
  <p>This is a simple HTML page to test if the web server is working correctly.</p>
  <p>If you can see this page, your web server is working!</p>
  <p>Time: <?php echo date('Y-m-d H:i:s'); ?></p>
</body>
</html>" > public/test.html

# Check permissions of storage directory
echo "Setting storage directory permissions..."
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Create symbolic link if it doesn't exist
echo "Creating storage symbolic link if needed..."
php artisan storage:link --force

echo "Repair process complete! Please try accessing your routes now."
