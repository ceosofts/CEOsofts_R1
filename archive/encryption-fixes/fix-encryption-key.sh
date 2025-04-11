#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Fix Laravel Encryption Key Utility     ${NC}"
echo -e "${BLUE}================================================${NC}"

# ตรวจสอบว่าไฟล์ .env มีอยู่หรือไม่
if [ ! -f .env ]; then
    echo -e "${YELLOW}ไม่พบไฟล์ .env กำลังสร้างจาก .env.example...${NC}"
    cp .env.example .env
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}ไม่สามารถสร้างไฟล์ .env ได้${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}สร้างไฟล์ .env เรียบร้อยแล้ว${NC}"
fi

# แสดงข้อมูล APP_KEY ปัจจุบัน
CURRENT_APP_KEY=$(grep "^APP_KEY=" .env | sed 's/APP_KEY=//')
echo -e "${YELLOW}APP_KEY ปัจจุบัน: ${CURRENT_APP_KEY}${NC}"

if [[ -z "$CURRENT_APP_KEY" || "$CURRENT_APP_KEY" == "base64:" ]]; then
    echo -e "${RED}APP_KEY ปัจจุบันไม่ถูกต้องหรือว่างเปล่า${NC}"
else
    echo -e "${YELLOW}คุณต้องการสร้าง APP_KEY ใหม่หรือไม่?${NC}"
    read -p "สร้าง APP_KEY ใหม่? (y/n): " generate_new_key
    
    if [[ "$generate_new_key" != "y" && "$generate_new_key" != "Y" ]]; then
        echo -e "${YELLOW}กำลังตรวจสอบค่า encryption ปัจจุบัน...${NC}"
    else
        echo -e "${YELLOW}กำลังสร้าง APP_KEY ใหม่...${NC}"
        php artisan key:generate --ansi
    fi
fi

if [[ -z "$CURRENT_APP_KEY" || "$CURRENT_APP_KEY" == "base64:" || "$generate_new_key" == "y" || "$generate_new_key" == "Y" ]]; then
    # สร้าง APP_KEY ใหม่
    echo -e "${YELLOW}กำลังสร้าง APP_KEY ใหม่...${NC}"
    php artisan key:generate --ansi
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}ไม่สามารถสร้าง APP_KEY ใหม่ได้${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}สร้าง APP_KEY ใหม่เรียบร้อยแล้ว${NC}"
fi

# ตรวจสอบค่า cipher ใน config/app.php
CURRENT_CIPHER=$(grep "'cipher' => " config/app.php | sed "s/.*'cipher' => '\([^']*\)'.*/\1/")
echo -e "${YELLOW}Cipher ปัจจุบัน: ${CURRENT_CIPHER}${NC}"

# ตรวจสอบว่า cipher ที่ใช้อยู่อยู่ในรายการที่รองรับหรือไม่
SUPPORTED_CIPHERS=("aes-128-cbc" "aes-256-cbc" "aes-128-gcm" "aes-256-gcm")
CIPHER_SUPPORTED=false

for cipher in "${SUPPORTED_CIPHERS[@]}"; do
    if [[ "$CURRENT_CIPHER" == "$cipher" ]]; then
        CIPHER_SUPPORTED=true
        break
    fi
done

if [[ "$CIPHER_SUPPORTED" == false ]]; then
    echo -e "${RED}Cipher ปัจจุบันไม่รองรับ${NC}"
    echo -e "${YELLOW}กำลังแก้ไข cipher เป็น aes-256-cbc...${NC}"
    
    # สำรองไฟล์ config/app.php
    cp config/app.php config/app.php.bak
    
    # แก้ไข cipher เป็น aes-256-cbc
    sed -i '' "s/'cipher' => '.*'/'cipher' => 'aes-256-cbc'/g" config/app.php
    
    echo -e "${GREEN}แก้ไข cipher เรียบร้อยแล้ว${NC}"
fi

# เคลียร์ cache
echo -e "${YELLOW}กำลังเคลียร์ cache...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo -e "${GREEN}เสร็จสิ้น! กรุณาลองเข้าใช้งานแอปพลิเคชันอีกครั้ง${NC}"
echo -e "${YELLOW}หากยังพบปัญหาอีก โปรดลอง restart PHP server${NC}"

# ทำให้สามารถรันได้
chmod +x fix-encryption-key.sh
