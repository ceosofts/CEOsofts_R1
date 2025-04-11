#!/bin/bash

# สีสำหรับการแสดงผล
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Laravel Downgrade Tool     ${NC}"
echo -e "${BLUE}================================================${NC}"

# สร้างโฟลเดอร์สำรองข้อมูล
if [ ! -d "backups" ]; then
    mkdir backups
    echo -e "${GREEN}สร้างโฟลเดอร์ backups สำเร็จ${NC}"
fi

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="backups/downgrade_$TIMESTAMP"
mkdir -p $BACKUP_DIR

# สำรองไฟล์สำคัญ
echo -e "${YELLOW}\n1. กำลังสำรองไฟล์สำคัญ...${NC}"
cp composer.json "$BACKUP_DIR/" 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ composer.json${NC}"
cp composer.lock "$BACKUP_DIR/" 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ composer.lock${NC}"
cp .env "$BACKUP_DIR/" 2>/dev/null || echo -e "${RED}ไม่พบไฟล์ .env${NC}"

# แสดงเวอร์ชันปัจจุบันของ Laravel
echo -e "${YELLOW}\n2. เวอร์ชันปัจจุบันของ Laravel:${NC}"
php artisan --version

# แก้ไขไฟล์ composer.json เพื่อกำหนดเวอร์ชันที่ต้องการดาวน์เกรด
echo -e "${YELLOW}\n3. กำลังแก้ไขไฟล์ composer.json เพื่อดาวน์เกรด Laravel...${NC}"
# ใช้ sed เพื่อแทนที่เวอร์ชัน Laravel เป็น 12.7.2 (ไม่ใช่ 12.8.1)
sed -i.bak 's/"laravel\/framework": "\^12.[0-9]\.[0-9]"/"laravel\/framework": "^12.7.2"/' composer.json
echo -e "${GREEN}แก้ไขไฟล์ composer.json เรียบร้อยแล้ว${NC}"

# อัพเดต dependencies
echo -e "${YELLOW}\n4. กำลังอัพเดต dependencies...${NC}"
composer update laravel/framework

# ตรวจสอบเวอร์ชันหลังจากดาวน์เกรด
echo -e "${YELLOW}\n5. เวอร์ชันหลังจากดาวน์เกรด:${NC}"
php artisan --version

# ล้าง cache ต่างๆ
echo -e "${YELLOW}\n6. กำลังล้าง cache ต่างๆ...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan clear-compiled

# สร้าง APP_KEY ใหม่
echo -e "${YELLOW}\n7. กำลังสร้าง APP_KEY ใหม่...${NC}"
php artisan key:generate --ansi

echo -e "\n${BLUE}================================================${NC}"
echo -e "${GREEN}ดาวน์เกรด Laravel เสร็จสมบูรณ์!${NC}"
echo -e "${YELLOW}ทดสอบแอพพลิเคชันด้วยคำสั่ง:${NC}"
echo -e "${GREEN}php artisan serve${NC}"
echo -e "${BLUE}================================================${NC}"

chmod +x downgrade-laravel.sh
