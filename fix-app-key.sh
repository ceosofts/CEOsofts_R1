#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     แก้ไขปัญหา App Key ใน CEOsofts R1     ${NC}"
echo -e "${BLUE}================================================${NC}"

# ตรวจสอบไฟล์ .env
echo -e "${YELLOW}กำลังตรวจสอบไฟล์ .env...${NC}"

if [ ! -f ".env" ]; then
    echo -e "${RED}ไม่พบไฟล์ .env${NC}"
    echo -e "${YELLOW}กำลังสร้างไฟล์ .env จาก .env.example...${NC}"
    
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}สร้างไฟล์ .env เรียบร้อยแล้ว${NC}"
    else
        echo -e "${RED}ไม่พบไฟล์ .env.example กรุณาสร้างไฟล์ .env ด้วยตนเอง${NC}"
        exit 1
    fi
fi

# ตรวจสอบ App Key
echo -e "${YELLOW}กำลังตรวจสอบ App Key...${NC}"

APP_KEY=$(grep "^APP_KEY=" .env | sed 's/APP_KEY=//')

if [ -z "$APP_KEY" ] || [ "$APP_KEY" == "base64:" ] || [ "$APP_KEY" == "" ]; then
    echo -e "${RED}ไม่พบ App Key หรือ App Key ไม่สมบูรณ์${NC}"
    
    # ลบ APP_KEY ที่ไม่สมบูรณ์
    sed -i '' '/^APP_KEY=/d' .env
    
    # เพิ่ม APP_KEY=
    echo "APP_KEY=" >> .env
    
    # สร้าง App Key
    echo -e "${YELLOW}กำลังสร้าง App Key...${NC}"
    php artisan key:generate
else
    echo -e "${GREEN}มี App Key อยู่แล้ว: $APP_KEY${NC}"
    
    # ถามว่าต้องการสร้าง App Key ใหม่หรือไม่
    read -p "ต้องการสร้าง App Key ใหม่หรือไม่? (y/n):" answer
    
    if [ "$answer" = "y" ] || [ "$answer" = "Y" ]; then
        echo -e "${YELLOW}กำลังสร้าง App Key ใหม่...${NC}"
        php artisan key:generate
        echo -e "${GREEN}สร้าง App Key ใหม่เรียบร้อยแล้ว${NC}"
    fi
fi

# เคลียร์แคช
echo -e "${YELLOW}กำลังเคลียร์แคช...${NC}"
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
echo -e "${GREEN}เคลียร์แคชเรียบร้อยแล้ว${NC}"

# ตรวจสอบค่า cipher ใน config/app.php
if [ -f "config/app.php" ]; then
    # จับคู่ค่า cipher ในไฟล์
    CIPHER=$(grep "'cipher' =>" config/app.php | sed "s/.*'cipher' => '\([^']*\)'.*/\1/")
    
    # ตรวจสอบว่าเป็นค่าที่ถูกต้องหรือไม่
    if [ "$CIPHER" != "aes-256-cbc" ] && [ "$CIPHER" != "aes-128-cbc" ] && [ "$CIPHER" != "aes-256-gcm" ] && [ "$CIPHER" != "aes-128-gcm" ]; then
        echo -e "${RED}ค่า cipher ไม่ถูกต้อง: $CIPHER${NC}"
        echo -e "${YELLOW}กำลังแก้ไขเป็น aes-256-cbc...${NC}"
        
        # สำรองไฟล์
        cp config/app.php config/app.php.bak
        
        # แก้ไข cipher
        sed -i '' "s/'cipher' => '.*'/'cipher' => 'aes-256-cbc'/g" config/app.php
        
        echo -e "${GREEN}แก้ไข cipher เป็น aes-256-cbc เรียบร้อยแล้ว${NC}"
    fi
fi

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}การแก้ไขเสร็จสมบูรณ์${NC}"
echo -e "${YELLOW}ขั้นตอนต่อไป:${NC}"
echo -e "${YELLOW}1. รีสตาร์ท PHP Server: ${GREEN}php artisan serve${NC}"
echo -e "${YELLOW}2. เข้าสู่เว็บไซต์อีกครั้ง: ${GREEN}http://localhost:8000${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x fix-app-key.sh
