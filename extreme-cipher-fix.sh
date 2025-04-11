#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Extreme Cipher Fix Utility     ${NC}"
echo -e "${BLUE}================================================${NC}"

# สร้างโฟลเดอร์สำรองข้อมูลถ้ายังไม่มี
if [ ! -d "backups" ]; then
    mkdir backups
    echo -e "${GREEN}สร้างโฟลเดอร์ backups สำเร็จ${NC}"
fi

# timestamp สำหรับการตั้งชื่อไฟล์สำรอง
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

# 1. สำรองไฟล์สำคัญ
echo -e "${YELLOW}\n1. กำลังสำรองไฟล์สำคัญ...${NC}"
if [ -f "config/app.php" ]; then
    cp config/app.php "backups/app.php.$TIMESTAMP"
    echo -e "${GREEN}สำรองไฟล์ config/app.php เรียบร้อยแล้ว${NC}"
fi

if [ -f ".env" ]; then
    cp .env "backups/.env.$TIMESTAMP"
    echo -e "${GREEN}สำรองไฟล์ .env เรียบร้อยแล้ว${NC}"
fi

# 2. สร้างไฟล์ config/app.php ใหม่
echo -e "${YELLOW}\n2. กำลังสร้างไฟล์ config/app.php ใหม่...${NC}"
cat > config/app.php << 'EOL'
<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'key' => env('APP_KEY'),
    'cipher' => 'aes-256-cbc',
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE'),
    ],
    'providers' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
    'aliases' => [],
];
EOL
echo -e "${GREEN}สร้างไฟล์ config/app.php ใหม่เรียบร้อยแล้ว${NC}"

# 3. สร้าง APP_KEY ใหม่
echo -e "${YELLOW}\n3. กำลังสร้าง APP_KEY ใหม่...${NC}"

# สร้างตัวแปร PHP เพื่อสร้าง key ใหม่
NEW_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
echo -e "${GREEN}สร้าง key ใหม่: $NEW_KEY${NC}"

# แก้ไขไฟล์ .env
if [ -f ".env" ]; then
    # ตรวจสอบว่ามี APP_KEY ในไฟล์หรือไม่
    grep -q "^APP_KEY=" .env
    if [ $? -eq 0 ]; then
        # แทนที่ APP_KEY ที่มีอยู่
        sed -i.bak "s/^APP_KEY=.*$/APP_KEY=$NEW_KEY/" .env
        echo -e "${GREEN}แก้ไข APP_KEY ในไฟล์ .env เรียบร้อยแล้ว${NC}"
    else
        # เพิ่ม APP_KEY ใหม่
        echo "APP_KEY=$NEW_KEY" >> .env
        echo -e "${GREEN}เพิ่ม APP_KEY ในไฟล์ .env เรียบร้อยแล้ว${NC}"
    fi
else
    echo -e "${RED}ไม่พบไฟล์ .env${NC}"
    echo -e "${YELLOW}กำลังสร้างไฟล์ .env ใหม่จาก .env.example...${NC}"
    
    if [ -f ".env.example" ]; then
        cp .env.example .env
        sed -i.bak "s/^APP_KEY=.*$/APP_KEY=$NEW_KEY/" .env
        echo -e "${GREEN}สร้างไฟล์ .env ใหม่พร้อม APP_KEY เรียบร้อยแล้ว${NC}"
    else
        # สร้างไฟล์ .env ขั้นต่ำ
        cat > .env << EOL
APP_NAME=Laravel
APP_ENV=local
APP_KEY=$NEW_KEY
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
EOL
        echo -e "${GREEN}สร้างไฟล์ .env ขั้นต่ำพร้อม APP_KEY เรียบร้อยแล้ว${NC}"
    fi
fi

# 4. ลบไฟล์ cache ทั้งหมด
echo -e "${YELLOW}\n4. กำลังลบไฟล์ cache ทั้งหมด...${NC}"

# ลบไฟล์ cache ใน bootstrap/cache
if [ -d "bootstrap/cache" ]; then
    rm -f bootstrap/cache/*.php
    echo -e "${GREEN}ลบไฟล์ cache ใน bootstrap/cache เรียบร้อยแล้ว${NC}"
else
    mkdir -p bootstrap/cache
    echo -e "${GREEN}สร้างโฟลเดอร์ bootstrap/cache เรียบร้อยแล้ว${NC}"
fi

# ลบไฟล์ cache ใน storage/framework
if [ -d "storage/framework/cache" ]; then
    rm -rf storage/framework/cache/data/*
    echo -e "${GREEN}ลบไฟล์ cache ใน storage/framework/cache เรียบร้อยแล้ว${NC}"
fi

if [ -d "storage/framework/views" ]; then
    rm -f storage/framework/views/*.php
    echo -e "${GREEN}ลบไฟล์ cache ใน storage/framework/views เรียบร้อยแล้ว${NC}"
fi

if [ -d "storage/framework/sessions" ]; then
    rm -f storage/framework/sessions/*
    echo -e "${GREEN}ลบไฟล์ sessions เรียบร้อยแล้ว${NC}"
fi

# 5. เคลียร์ cache ด้วย artisan
echo -e "${YELLOW}\n5. กำลังเคลียร์ cache ด้วย artisan...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled
echo -e "${GREEN}เคลียร์ cache ด้วย artisan เรียบร้อยแล้ว${NC}"

# 6. อัพเดต autoload
echo -e "${YELLOW}\n6. กำลังอัพเดต autoload...${NC}"
composer dump-autoload -o
echo -e "${GREEN}อัพเดต autoload เรียบร้อยแล้ว${NC}"

# 7. รีสตาร์ท PHP server (ถ้ามี)
echo -e "${YELLOW}\n7. กำลังรีสตาร์ท PHP server...${NC}"
# ฆ่า PHP process ที่รันอยู่
pkill -f "php artisan serve" || true

# รัน PHP server ใหม่ในแบ็คกราวด์
nohup php artisan serve > /dev/null 2>&1 &
echo -e "${GREEN}รีสตาร์ท PHP server เรียบร้อยแล้ว${NC}"

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}การแก้ไขปัญหา cipher เสร็จสมบูรณ์${NC}"
echo -e "${YELLOW}คุณสามารถเข้าถึงเว็บไซต์ได้ที่: http://localhost:8000${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x extreme-cipher-fix.sh
