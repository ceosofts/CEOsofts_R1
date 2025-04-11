#!/bin/bash

# สีสำหรับการแสดงผล
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Laravel Encryption Reset Tool     ${NC}"
echo -e "${BLUE}================================================${NC}"

# สำรองไฟล์สำคัญ
echo -e "${YELLOW}1. กำลังสำรองไฟล์สำคัญ...${NC}"

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="backups/reset_encryption_$TIMESTAMP"
mkdir -p $BACKUP_DIR

cp .env $BACKUP_DIR/ 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ .env${NC}"
cp config/app.php $BACKUP_DIR/ 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ config/app.php${NC}"
echo -e "${GREEN}สำรองไฟล์เรียบร้อยแล้ว${NC}"

# สร้างไฟล์ .env ใหม่จาก .env.example
echo -e "\n${YELLOW}2. กำลังสร้างไฟล์ .env ใหม่...${NC}"

if [ -f ".env" ]; then
  echo "สำรองไฟล์ .env เดิม"
  cp .env $BACKUP_DIR/.env.old
fi

if [ -f ".env.example" ]; then
  echo "คัดลอกจาก .env.example"
  cp .env.example .env
else
  echo -e "${RED}ไม่พบไฟล์ .env.example สร้างไฟล์ .env ใหม่${NC}"
  cat > .env << EOL
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceosofts_db_r1
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1
EOL
fi

# สร้าง APP_KEY ใหม่ด้วย artisan
echo -e "\n${YELLOW}3. กำลังสร้าง APP_KEY ใหม่...${NC}"
php artisan key:generate --ansi
echo -e "${GREEN}สร้าง APP_KEY เรียบร้อยแล้ว${NC}"

# แสดง APP_KEY ที่สร้างใหม่
echo -e "\n${YELLOW}4. APP_KEY ที่สร้างใหม่:${NC}"
grep APP_KEY .env

# ตรวจสอบไฟล์ app.php
echo -e "\n${YELLOW}5. กำลังตรวจสอบไฟล์ config/app.php...${NC}"

if [ ! -d "config" ]; then
  mkdir -p config
  echo "สร้างโฟลเดอร์ config"
fi

if [ ! -f "config/app.php" ] || grep -q "cipher.*AES-256-CBC" config/app.php; then
  echo "สร้างไฟล์ config/app.php ใหม่"
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
  echo -e "${GREEN}สร้างไฟล์ config/app.php ใหม่เรียบร้อยแล้ว${NC}"
else
  echo -e "${GREEN}ไฟล์ config/app.php มีอยู่แล้วและมี cipher ที่ถูกต้อง${NC}"
fi

# ล้าง cache
echo -e "\n${YELLOW}6. กำลังล้าง cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled
composer dump-autoload -o

# ลบไฟล์ cache โดยตรง
echo -e "\n${YELLOW}7. กำลังลบไฟล์ cache โดยตรง...${NC}"
rm -f bootstrap/cache/*.php 2>/dev/null
rm -rf storage/framework/cache/data/* 2>/dev/null
rm -f storage/framework/views/*.php 2>/dev/null
rm -f storage/framework/sessions/* 2>/dev/null
echo -e "${GREEN}ลบไฟล์ cache เรียบร้อยแล้ว${NC}"

# ตรวจสอบสิทธิ์
echo -e "\n${YELLOW}8. กำลังตรวจสอบสิทธิ์...${NC}"
chmod -R 755 storage bootstrap/cache
echo -e "${GREEN}ตรวจสอบสิทธิ์เรียบร้อยแล้ว${NC}"

# ทดสอบเบื้องต้นว่า encryption ทำงาน
echo -e "\n${YELLOW}9. กำลังทดสอบ encryption...${NC}"
php -r "
try {
    require __DIR__.'/vendor/autoload.php';
    \$app = require_once __DIR__.'/bootstrap/app.php';
    \$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    
    \$encrypted = encrypt('test message');
    \$decrypted = decrypt(\$encrypted);
    
    if (\$decrypted === 'test message') {
        echo \"Encryption test PASSED\n\";
        exit(0);
    } else {
        echo \"Encryption test FAILED: Decrypted value doesn't match\n\";
        exit(1);
    }
} catch (Exception \$e) {
    echo \"Encryption test FAILED: {\$e->getMessage()}\n\";
    exit(1);
}
"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}ระบบเข้ารหัสทำงานได้ถูกต้อง${NC}"
else
    echo -e "${RED}ระบบเข้ารหัสยังมีปัญหา กรุณาลองวิธีอื่น${NC}"
fi

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}การรีเซ็ตการเข้ารหัสเสร็จสมบูรณ์!${NC}"
echo -e "${YELLOW}โปรดรันคำสั่งต่อไปนี้เพื่อเริ่มเซิร์ฟเวอร์:${NC}"
echo -e "${GREEN}php artisan serve${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x reset-laravel-encryption.sh
