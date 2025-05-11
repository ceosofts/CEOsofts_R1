#!/bin/bash
echo "ล้าง Route Cache..."
php artisan route:clear
echo "ล้าง Config Cache..."
php artisan config:clear
echo "ล้าง Cache ทั้งหมด..."
php artisan optimize:clear
echo "เสร็จสิ้น"
