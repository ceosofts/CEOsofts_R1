#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     CEOsofts R1 Verification Utility     ${NC}"
echo -e "${BLUE}================================================${NC}"

# ตรวจสอบว่าเป็นโปรเจค Laravel
if [ ! -f "artisan" ]; then
    echo -e "${RED}ไม่พบไฟล์ artisan - ไม่ใช่โปรเจกต์ Laravel${NC}"
    exit 1
fi

# 1. ตรวจสอบการตั้งค่าการเข้ารหัส
echo -e "${YELLOW}1. กำลังตรวจสอบการตั้งค่าการเข้ารหัส...${NC}"

# ตรวจสอบ APP_KEY ใน .env
APP_KEY=$(grep "^APP_KEY=" .env | cut -d '=' -f2)
if [[ -z "$APP_KEY" || "$APP_KEY" == "base64:" ]]; then
    echo -e "${RED}APP_KEY ไม่ถูกต้อง หรือไม่มีค่า${NC}"
    echo -e "${YELLOW}กำลังสร้าง APP_KEY ใหม่...${NC}"
    php artisan key:generate --ansi
else
    echo -e "${GREEN}APP_KEY มีอยู่และรูปแบบถูกต้อง${NC}"
fi

# ตรวจสอบ cipher ใน config/app.php
CIPHER=$(grep "'cipher' =>" config/app.php | sed "s/.*'cipher' => '\([^']*\)'.*/\1/")
if [ "$CIPHER" != "aes-256-cbc" ]; then
    echo -e "${RED}Cipher ไม่ถูกต้อง: $CIPHER${NC}"
    echo -e "${YELLOW}กำลังแก้ไข cipher เป็น aes-256-cbc...${NC}"
    
    # สำรองไฟล์
    cp config/app.php config/app.php.verify-bak
    
    # แก้ไขไฟล์
    sed -i '' "s/'cipher' => '.*'/'cipher' => 'aes-256-cbc'/g" config/app.php
    
    echo -e "${GREEN}แก้ไข cipher เป็น aes-256-cbc เรียบร้อยแล้ว${NC}"
else
    echo -e "${GREEN}Cipher ถูกต้อง: $CIPHER${NC}"
fi

# 2. เคลียร์แคชและคอมไพล์ใหม่
echo -e "\n${YELLOW}2. กำลังเคลียร์แคชและคอมไพล์ใหม่...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled
composer dump-autoload -o

echo -e "${GREEN}เคลียร์แคชและคอมไพล์ใหม่เรียบร้อยแล้ว${NC}"

# 3. ตรวจสอบการเข้าถึงฐานข้อมูล
echo -e "\n${YELLOW}3. กำลังตรวจสอบการเข้าถึงฐานข้อมูล...${NC}"

# ดึงข้อมูลการตั้งค่าฐานข้อมูล
DB_CONNECTION=$(grep "^DB_CONNECTION=" .env | cut -d '=' -f2)
echo -e "การเชื่อมต่อฐานข้อมูล: ${GREEN}$DB_CONNECTION${NC}"

if [ "$DB_CONNECTION" == "sqlite" ]; then
    DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
    
    # ตรวจสอบว่าไฟล์ฐานข้อมูล SQLite มีอยู่จริง
    if [ ! -f "$DB_DATABASE" ]; then
        echo -e "${RED}ไม่พบไฟล์ฐานข้อมูล SQLite: $DB_DATABASE${NC}"
        
        # สร้างไฟล์ SQLite
        touch "$DB_DATABASE"
        echo -e "${GREEN}สร้างไฟล์ฐานข้อมูล SQLite เรียบร้อยแล้ว${NC}"
    else
        echo -e "${GREEN}พบไฟล์ฐานข้อมูล SQLite: $DB_DATABASE${NC}"
    fi
else
    DB_HOST=$(grep "^DB_HOST=" .env | cut -d '=' -f2)
    DB_PORT=$(grep "^DB_PORT=" .env | cut -d '=' -f2)
    DB_DATABASE=$(grep "^DB_DATABASE=" .env | cut -d '=' -f2)
    
    echo -e "ฐานข้อมูล: ${GREEN}$DB_DATABASE${NC} ที่ ${GREEN}$DB_HOST:$DB_PORT${NC}"
    
    # ตรวจสอบว่าฐานข้อมูลมีอยู่จริง (อาจต้องใส่รหัสผ่าน)
    # สำหรับ MySQL เราใช้ mysqladmin ping
    if command -v mysqladmin &> /dev/null; then
        if mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u root --silent; then
            echo -e "${GREEN}เชื่อมต่อกับ MySQL server สำเร็จ${NC}"
        else
            echo -e "${RED}ไม่สามารถเชื่อมต่อกับ MySQL server ได้${NC}"
        fi
    else
        echo -e "${YELLOW}ไม่พบคำสั่ง mysqladmin สำหรับตรวจสอบการเชื่อมต่อ${NC}"
    fi
fi

# 4. ตรวจสอบสิทธิ์ของโฟลเดอร์
echo -e "\n${YELLOW}4. กำลังตรวจสอบสิทธิ์ของโฟลเดอร์...${NC}"

# สร้างโฟลเดอร์ที่จำเป็น
mkdir -p storage/framework/views
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/logs
mkdir -p bootstrap/cache

# ตั้งค่าสิทธิ์
chmod -R 775 storage
chmod -R 775 bootstrap/cache

echo -e "${GREEN}ตั้งค่าสิทธิ์ของโฟลเดอร์เรียบร้อยแล้ว${NC}"

# 5. ตรวจสอบการติดตั้ง Composer dependencies
echo -e "\n${YELLOW}5. กำลังตรวจสอบการติดตั้ง Composer dependencies...${NC}"

if [ -f "vendor/autoload.php" ]; then
    echo -e "${GREEN}Composer dependencies ติดตั้งเรียบร้อยแล้ว${NC}"
else
    echo -e "${RED}ไม่พบ vendor/autoload.php${NC}"
    echo -e "${YELLOW}กำลังติดตั้ง Composer dependencies...${NC}"
    
    composer install --no-interaction
fi

# 6. ตรวจสอบการติดตั้ง NPM dependencies
echo -e "\n${YELLOW}6. กำลังตรวจสอบการติดตั้ง NPM dependencies...${NC}"

if [ -d "node_modules" ]; then
    echo -e "${GREEN}NPM dependencies ติดตั้งเรียบร้อยแล้ว${NC}"
else
    echo -e "${RED}ไม่พบโฟลเดอร์ node_modules${NC}"
    echo -e "${YELLOW}กำลังติดตั้ง NPM dependencies...${NC}"
    
    npm install
fi

# 7. แนะนำการรัน PHP server
echo -e "\n${YELLOW}7. การรัน PHP server${NC}"
echo -e "คุณสามารถรัน PHP server ด้วยคำสั่ง:"
echo -e "${GREEN}php artisan serve${NC}"

# 8. วิธีตรวจสอบว่าการแก้ไขปัญหาสมบูรณ์
echo -e "\n${YELLOW}8. วิธีตรวจสอบว่าการแก้ไขปัญหาสมบูรณ์${NC}"
echo -e "1. รัน PHP server: ${GREEN}php artisan serve${NC}"
echo -e "2. เปิดเบราว์เซอร์ไปที่: ${GREEN}http://localhost:8000${NC}"
echo -e "3. ถ้าเว็บไซต์โหลดได้โดยไม่มีข้อผิดพลาด 'Unsupported cipher' แสดงว่าแก้ไขปัญหาสำเร็จแล้ว"
echo -e "4. หากยังมีปัญหา ให้ตรวจสอบ log: ${GREEN}tail -f storage/logs/laravel.log${NC}"

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}การตรวจสอบเสร็จสมบูรณ์${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x verify-app-functionality.sh
