#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Laravel Core Packages Reinstaller     ${NC}"
echo -e "${BLUE}================================================${NC}"

# สร้างโฟลเดอร์สำรองข้อมูล
if [ ! -d "backups" ]; then
    mkdir -p backups
    echo -e "${GREEN}สร้างโฟลเดอร์ backups สำเร็จ${NC}"
fi

# สำรองไฟล์ composer.json
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
if [ -f "composer.json" ]; then
    cp composer.json "backups/composer.json.$TIMESTAMP"
    echo -e "${GREEN}สำรองไฟล์ composer.json เรียบร้อยแล้ว${NC}"
fi

echo -e "${YELLOW}\n1. กำลังตรวจสอบเวอร์ชัน Laravel...${NC}"
php artisan --version || echo -e "${YELLOW}ไม่สามารถตรวจสอบเวอร์ชัน Laravel${NC}"

echo -e "${YELLOW}\n2. กำลังทำความสะอาด Cache ทั้งหมด...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled

echo -e "${YELLOW}\n3. กำลังล้าง Caches ของ Composer...${NC}"
composer clear-cache

echo -e "${YELLOW}\n4. กำลังถอดการติดตั้งและติดตั้ง Encryption Package ใหม่...${NC}"

# ลอง update package encryption โดยตรง
echo -e "${YELLOW}กำลัง update illuminate/encryption เป็นเวอร์ชันล่าสุด...${NC}"
composer update illuminate/encryption

# ถ้าไม่ได้ผล ลองติดตั้งใหม่
if [ $? -ne 0 ]; then
    echo -e "${YELLOW}ลองติดตั้ง illuminate/encryption ใหม่...${NC}"
    composer remove illuminate/encryption
    composer require illuminate/encryption
fi

echo -e "${YELLOW}\n5. กำลัง Update Laravel Framework...${NC}"
composer update --with-all-dependencies

echo -e "${YELLOW}\n6. ตรวจสอบที่อยู่ของ Laravel Encrypter...${NC}"
vendor_path=$(composer config vendor-dir)
encryption_path="$vendor_path/laravel/framework/src/Illuminate/Encryption"

if [ -d "$encryption_path" ]; then
    echo -e "${GREEN}พบ Encryption Package ที่: $encryption_path${NC}"
    
    # ตรวจสอบ EncryptionServiceProvider.php
    if [ -f "$encryption_path/EncryptionServiceProvider.php" ]; then
        echo -e "${GREEN}พบ EncryptionServiceProvider.php${NC}"
        
        # ค้นหา supportedCiphers
        grep -n "supportedCiphers" "$encryption_path/EncryptionServiceProvider.php"
        
        # ค้นหา aes-256-cbc
        grep -n "aes-256-cbc" "$encryption_path/EncryptionServiceProvider.php"
    fi
    
    # ตรวจสอบไฟล์ Encrypter.php
    if [ -f "$encryption_path/Encrypter.php" ]; then
        echo -e "${GREEN}พบ Encrypter.php${NC}"
        
        # ค้นหา supportedCiphers
        grep -n "supportedCiphers" "$encryption_path/Encrypter.php"
    fi
else
    echo -e "${RED}ไม่พบ Encryption Package ที่ $encryption_path${NC}"
fi

echo -e "${YELLOW}\n7. กำลังล้างและ Update Autoloader...${NC}"
composer dump-autoload -o

echo -e "${YELLOW}\n8. กำลังสร้าง APP_KEY ใหม่...${NC}"
php artisan key:generate --ansi

echo -e "${YELLOW}\n9. กำลังตรวจสอบค่าที่ถูกต้อง...${NC}"
echo -e "APP_KEY ใน .env: "
grep APP_KEY .env || echo -e "${RED}ไม่พบ APP_KEY ใน .env${NC}"

echo -e "\nCipher ใน config/app.php:"
grep -n "cipher" config/app.php || echo -e "${RED}ไม่พบ cipher ใน config/app.php${NC}"

echo -e "\n${GREEN}ดำเนินการเสร็จสิ้น!${NC}"
echo -e "${YELLOW}โปรดรีสตาร์ท PHP server:\n  php artisan serve${NC}"

chmod +x reinstall-core-packages.sh
