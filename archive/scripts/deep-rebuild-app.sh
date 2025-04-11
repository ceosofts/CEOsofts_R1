#!/bin/bash

# สีสำหรับการแสดงผล
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Laravel Deep Rebuild Tool     ${NC}"
echo -e "${BLUE}================================================${NC}"
echo -e "${YELLOW}คำเตือน: กระบวนการนี้จะลบไฟล์ .env และตั้งค่าใหม่ทั้งหมด${NC}"
echo -e "${YELLOW}ข้อมูลปัจจุบันจะถูกสำรอง แต่กรุณาตรวจสอบให้แน่ใจว่าคุณมีข้อมูลสำรองสำคัญ${NC}"

# ถามก่อนดำเนินการต่อ
read -p "คุณแน่ใจหรือไม่ว่าต้องการดำเนินการต่อ? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${RED}ยกเลิกการทำงาน${NC}"
    exit 1
fi

# 1. สำรองไฟล์สำคัญ
echo -e "\n${YELLOW}1. กำลังสำรองไฟล์สำคัญ...${NC}"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="backups/deep_rebuild_$TIMESTAMP"
mkdir -p $BACKUP_DIR

# สำรองไฟล์สำคัญ
cp .env $BACKUP_DIR/ 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ .env${NC}"
cp .env.example $BACKUP_DIR/ 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ .env.example${NC}"
cp -r config $BACKUP_DIR/ 2>/dev/null || echo -e "${RED}ไม่พบโฟลเดอร์ config${NC}"
cp composer.json $BACKUP_DIR/ 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ composer.json${NC}"
cp composer.lock $BACKUP_DIR/ 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ composer.lock${NC}"

echo -e "${GREEN}สำรองไฟล์ไปยัง $BACKUP_DIR เรียบร้อยแล้ว${NC}"

# 2. ลบไฟล์ที่อาจมีปัญหา
echo -e "\n${YELLOW}2. กำลังลบไฟล์ที่อาจมีปัญหา...${NC}"
rm -f .env
rm -rf bootstrap/cache/*.php 2>/dev/null
rm -rf storage/framework/cache/data/* 2>/dev/null
rm -rf storage/framework/views/*.php 2>/dev/null
rm -rf storage/framework/sessions/* 2>/dev/null
echo -e "${GREEN}ลบไฟล์ที่อาจมีปัญหาเรียบร้อยแล้ว${NC}"

# 3. สร้างไฟล์ .env ใหม่จาก .env.example
echo -e "\n${YELLOW}3. กำลังสร้างไฟล์ .env ใหม่...${NC}"
if [ -f ".env.example" ]; then
    cp .env.example .env
    echo -e "${GREEN}คัดลอกไฟล์ .env จาก .env.example เรียบร้อยแล้ว${NC}"
else
    # สร้างไฟล์ .env ใหม่
    cat > .env << EOL
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

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
    echo -e "${GREEN}สร้างไฟล์ .env ใหม่เรียบร้อยแล้ว${NC}"
fi

# 4. ตรวจสอบและสร้างโฟลเดอร์ที่จำเป็น
echo -e "\n${YELLOW}4. กำลังตรวจสอบโครงสร้างโฟลเดอร์...${NC}"
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
chmod -R 755 storage bootstrap/cache
echo -e "${GREEN}ตรวจสอบและปรับปรุงโครงสร้างโฟลเดอร์เรียบร้อยแล้ว${NC}"

# 5. หากจำเป็น ติดตั้ง dependencies ใหม่
echo -e "\n${YELLOW}5. คุณต้องการติดตั้ง dependencies ใหม่หรือไม่? (จะใช้เวลานาน)${NC}"
read -p "ติดตั้ง dependencies ใหม่ทั้งหมด? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo -e "${YELLOW}กำลังติดตั้ง dependencies ใหม่...${NC}"
    rm -rf vendor
    composer install
    echo -e "${GREEN}ติดตั้ง dependencies เรียบร้อยแล้ว${NC}"
else
    echo -e "${YELLOW}ข้ามการติดตั้ง dependencies${NC}"
fi

# 6. สร้าง APP_KEY ใหม่
echo -e "\n${YELLOW}6. กำลังสร้าง APP_KEY ใหม่...${NC}"
php artisan key:generate --ansi
echo -e "${GREEN}สร้าง APP_KEY ใหม่เรียบร้อยแล้ว${NC}"

# 7. ตรวจสอบ cipher ใน config/app.php
echo -e "\n${YELLOW}7. กำลังตรวจสอบการตั้งค่า cipher...${NC}"
if [ -f "config/app.php" ]; then
    if grep -q "'cipher' => 'aes-256-cbc'" config/app.php; then
        echo -e "${GREEN}การตั้งค่า cipher ถูกต้องแล้ว${NC}"
    else
        echo -e "${RED}ไม่พบการตั้งค่า cipher ที่ถูกต้องในไฟล์ config/app.php${NC}"
        echo -e "${YELLOW}กำลังสร้างไฟล์ config/app.php ใหม่...${NC}"
        
        mkdir -p config
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
    fi
else
    echo -e "${RED}ไม่พบไฟล์ config/app.php${NC}"
    echo -e "${YELLOW}กำลังสร้างไฟล์ config/app.php ใหม่...${NC}"
    
    mkdir -p config
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
fi

# 8. ล้าง cache
echo -e "\n${YELLOW}8. กำลังล้าง cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled
composer dump-autoload -o
echo -e "${GREEN}ล้าง cache เรียบร้อยแล้ว${NC}"

# 9. ทดสอบระบบเข้ารหัส
echo -e "\n${YELLOW}9. กำลังทดสอบระบบเข้ารหัส...${NC}"
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
    echo -e "${RED}ระบบเข้ารหัสยังมีปัญหา${NC}"
    echo -e "${YELLOW}ลองรันคำสั่ง: php artisan key:generate --ansi${NC}"
fi

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}การรีบิวด์ระบบเสร็จสมบูรณ์!${NC}"
echo -e "${YELLOW}โปรดรันคำสั่งต่อไปนี้เพื่อเริ่มเซิร์ฟเวอร์:${NC}"
echo -e "${GREEN}php artisan serve${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x deep-rebuild-app.sh
