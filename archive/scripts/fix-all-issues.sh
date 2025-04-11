#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Comprehensive Laravel Fix Utility     ${NC}"
echo -e "${BLUE}================================================${NC}"

echo -e "${YELLOW}กำลังตรวจสอบโครงสร้างของโปรเจกต์...${NC}"

# ตรวจสอบว่าเป็นโปรเจกต์ Laravel หรือไม่
if [ ! -f "artisan" ]; then
    echo -e "${RED}ไม่พบไฟล์ artisan - ไม่ใช่โปรเจกต์ Laravel${NC}"
    exit 1
fi

# ขั้นตอนที่ 1: แก้ไขปัญหา encryption key
echo -e "\n${YELLOW}ขั้นตอนที่ 1: กำลังแก้ไขปัญหา Encryption Key...${NC}"

# ตรวจสอบและแก้ไขไฟล์ config/app.php
if [ -f "config/app.php" ]; then
    cp config/app.php config/app.php.bak
    echo -e "${GREEN}สำรองไฟล์ config/app.php ไว้ที่ config/app.php.bak${NC}"
    
    # แก้ไข cipher เป็น aes-256-cbc ด้วยตัวพิมพ์เล็ก
    sed -i '' "s/'cipher' => '.*'/'cipher' => 'aes-256-cbc'/g" config/app.php
    echo -e "${GREEN}แก้ไข cipher เป็น aes-256-cbc${NC}"
fi

# สร้าง APP_KEY ใหม่
echo -e "${YELLOW}กำลังสร้าง APP_KEY ใหม่...${NC}"
php artisan key:generate --ansi

# ขั้นตอนที่ 2: ตรวจสอบและแก้ไขไฟล์ .env
echo -e "\n${YELLOW}ขั้นตอนที่ 2: กำลังตรวจสอบไฟล์ .env...${NC}"

if [ ! -f ".env" ]; then
    echo -e "${RED}ไม่พบไฟล์ .env${NC}"
    echo -e "${YELLOW}กำลังสร้างจาก .env.example...${NC}"
    cp .env.example .env
    # อัพเดท APP_KEY
    php artisan key:generate --ansi
else
    echo -e "${GREEN}พบไฟล์ .env${NC}"
    
    # ตรวจสอบการตั้งค่า session και cache drivers
    if grep -q "SESSION_DRIVER=database" .env && [ ! -f "database/database.sqlite" ]; then
        echo -e "${YELLOW}SESSION_DRIVER เป็น database แต่ไม่พบฐานข้อมูล SQLite${NC}"
        echo -e "${YELLOW}กำลังเปลี่ยน SESSION_DRIVER เป็น file...${NC}"
        sed -i '' "s/SESSION_DRIVER=database/SESSION_DRIVER=file/g" .env
    fi
    
    # ตรวจสอบ CACHE_DRIVER
    if grep -q "CACHE_DRIVER=database" .env && [ ! -f "database/database.sqlite" ]; then
        echo -e "${YELLOW}CACHE_DRIVER เป็น database แต่ไม่พบฐานข้อมูล SQLite${NC}"
        echo -e "${YELLOW}กำลังเปลี่ยน CACHE_DRIVER เป็น file...${NC}"
        sed -i '' "s/CACHE_DRIVER=database/CACHE_DRIVER=file/g" .env
    fi
fi

# ขั้นตอนที่ 3: แก้ไขปัญหา permission
echo -e "\n${YELLOW}ขั้นตอนที่ 3: กำลังแก้ไขปัญหา Permission...${NC}"

# ตรวจสอบและสร้างโฟลเดอร์ที่จำเป็น
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p bootstrap/cache

# ตั้งค่าสิทธิ์การเข้าถึง
chmod -R 775 storage
chmod -R 775 bootstrap/cache
echo -e "${GREEN}ตั้งค่า permissions สำหรับโฟลเดอร์ storage และ bootstrap/cache${NC}"

# ขั้นตอนที่ 4: เคลียร์ cache ทั้งหมด
echo -e "\n${YELLOW}ขั้นตอนที่ 4: กำลังเคลียร์ cache ทั้งหมด...${NC}"

php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan clear-compiled
echo -e "${GREEN}เคลียร์ cache เรียบร้อยแล้ว${NC}"

# ขั้นตอนที่ 5: อัพเดต Composer
echo -e "\n${YELLOW}ขั้นตอนที่ 5: กำลังอัพเดต Composer...${NC}"

composer dump-autoload -o
echo -e "${GREEN}อัพเดต Composer เรียบร้อยแล้ว${NC}"

echo -e "\n${GREEN}การแก้ไขปัญหาทั้งหมดเสร็จสิ้น!${NC}"

# ขั้นตอนสุดท้าย: ขอให้ผู้ใช้รีสตาร์ท server
echo -e "\n${YELLOW}ขั้นตอนสุดท้าย: รีสตาร์ท PHP server${NC}"
echo -e "คุณต้องการรีสตาร์ท PHP server เลยหรือไม่? (y/n)"
read -p "> " restart_server

if [[ "$restart_server" == "y" || "$restart_server" == "Y" ]]; then
    echo -e "${YELLOW}กำลังรีสตาร์ท PHP server...${NC}"
    killall php 2>/dev/null
    php artisan serve &
    echo -e "${GREEN}รีสตาร์ท PHP server เรียบร้อยแล้ว${NC}"
    echo -e "${GREEN}คุณสามารถเข้าใช้งานแอปพลิเคชันได้ที่ http://localhost:8000${NC}"
else
    echo -e "${YELLOW}โปรดรีสตาร์ท PHP server ด้วยตนเอง:${NC}"
    echo -e "${GREEN}php artisan serve${NC}"
fi

chmod +x fix-all-issues.sh
