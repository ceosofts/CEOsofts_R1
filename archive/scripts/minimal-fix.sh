#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Minimal Laravel Encryption Fix     ${NC}"
echo -e "${BLUE}================================================${NC}"

# สร้างโฟลเดอร์สำหรับ backup
if [ ! -d "backups" ]; then
    mkdir backups
    echo -e "${GREEN}สร้างโฟลเดอร์ backups สำเร็จ${NC}"
fi

# สำรองไฟล์สำคัญ
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")

if [ -f "config/app.php" ]; then
    cp config/app.php "backups/app.php.$TIMESTAMP"
    echo -e "${GREEN}สำรองไฟล์ config/app.php เรียบร้อยแล้ว${NC}"
fi

if [ -f ".env" ]; then
    cp .env "backups/.env.$TIMESTAMP"
    echo -e "${GREEN}สำรองไฟล์ .env เรียบร้อยแล้ว${NC}"
fi

# 1. สร้างไฟล์ config สำหรับแก้ไขปัญหา
mkdir -p config
echo -e "${YELLOW}1. กำลังสร้าง app.php ใหม่...${NC}"

cat > config/app.php << 'EOL'
<?php

return [
    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',
    'faker_locale' => 'en_US',
    'key' => env('APP_KEY'),
    'cipher' => 'aes-256-cbc',
    'maintenance' => [
        'driver' => 'file',
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

echo -e "${GREEN}สร้าง config/app.php ใหม่เรียบร้อยแล้ว${NC}"

# 2. สร้าง app key ใหม่และอัปเดต .env
echo -e "\n${YELLOW}2. กำลังสร้าง APP_KEY ใหม่...${NC}"

NEW_KEY=$(php -r "echo 'base64:' . base64_encode(random_bytes(32));")
echo -e "APP_KEY ใหม่: ${GREEN}$NEW_KEY${NC}"

# อัปเดตไฟล์ .env
if [ -f ".env" ]; then
    sed -i.tmp "s/^APP_KEY=.*$/APP_KEY=$NEW_KEY/" .env
    rm -f .env.tmp
    echo -e "${GREEN}อัปเดต APP_KEY ใน .env เรียบร้อยแล้ว${NC}"
else
    # สร้างไฟล์ .env ใหม่
    cat > .env << EOL
APP_NAME=Laravel
APP_ENV=local
APP_KEY=$NEW_KEY
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceosofts_db_r1
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
EOL
    echo -e "${GREEN}สร้างไฟล์ .env ใหม่พร้อม APP_KEY เรียบร้อยแล้ว${NC}"
fi

# 3. ลบไฟล์ cache ทั้งหมด
echo -e "\n${YELLOW}3. กำลังลบไฟล์ cache ทั้งหมด...${NC}"

rm -f bootstrap/cache/*.php
echo -e "${GREEN}ลบไฟล์ cache ใน bootstrap/cache แล้ว${NC}"

mkdir -p storage/framework/cache
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions
rm -rf storage/framework/cache/*
rm -f storage/framework/views/*.php
rm -f storage/framework/sessions/*
echo -e "${GREEN}ลบไฟล์ cache ใน storage/framework แล้ว${NC}"

# 4. เคลียร์ cache ด้วย PHP
echo -e "\n${YELLOW}4. กำลังเคลียร์ cache ด้วย PHP...${NC}"

cat > clear_caches.php << 'EOL'
<?php
echo "กำลังเคลียร์ cache...\n";
@unlink(__DIR__ . '/bootstrap/cache/config.php');
@unlink(__DIR__ . '/bootstrap/cache/routes.php');
@unlink(__DIR__ . '/bootstrap/cache/services.php');
@unlink(__DIR__ . '/bootstrap/cache/packages.php');
echo "เสร็จสิ้น\n";
EOL

php clear_caches.php
rm clear_caches.php

# 5. รันคำสั่ง artisan เพื่อเคลียร์ cache
echo -e "\n${YELLOW}5. กำลังรันคำสั่ง artisan เพื่อเคลียร์ cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php -r "if(function_exists('opcache_reset')) { opcache_reset(); echo \"Cleared opcache\n\"; }"

# 6. ตรวจสอบและแก้ไขสิทธิ์
echo -e "\n${YELLOW}6. กำลังตรวจสอบและแก้ไขสิทธิ์...${NC}"
chmod -R 755 storage bootstrap/cache
echo -e "${GREEN}แก้ไขสิทธิ์แล้ว${NC}"

# 7. สร้าง bootstrap file อย่างง่าย
echo -e "\n${YELLOW}7. กำลังสร้าง test bootstrap file...${NC}"
cat > test_app.php << 'EOL'
<?php
require __DIR__.'/vendor/autoload.php';

$app = new Illuminate\Foundation\Application(__DIR__);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// กำหนดค่า cipher และ key โดยตรง
$app['config']->set('app.cipher', 'aes-256-cbc');
$app['config']->set('app.key', getenv('APP_KEY'));

return $app;
EOL

echo -e "${GREEN}สร้าง test bootstrap file เรียบร้อยแล้ว${NC}"

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}การแก้ไขเสร็จสมบูรณ์!${NC}"
echo -e "${YELLOW}โปรดรีสตาร์ท PHP server:${NC}"
echo -e "${GREEN}php artisan serve --port=8001${NC}"
echo -e "${YELLOW}(ใช้พอร์ตที่แตกต่างจากเดิม เพื่อหลีกเลี่ยง cache ของเบราว์เซอร์)${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x minimal-fix.sh
