#!/bin/bash

# สคริปต์สำหรับเริ่มต้นการพัฒนา CEOsofts R1
# ================================================

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     CEOsofts R1 Development Environment     ${NC}"
echo -e "${BLUE}================================================${NC}"

# ตรวจสอบว่า npm run dev สามารถทำงานได้หรือไม่
echo -e "${YELLOW}กำลังตรวจสอบสถานะของ Vite...${NC}"
if ! npm run dev -- --help > /dev/null 2>&1; then
    echo -e "${RED}ไม่พบหรือไม่สามารถรัน Vite ได้ กำลังแก้ไขปัญหา...${NC}"
    
    # ลบโฟลเดอร์ node_modules และ package-lock.json
    echo -e "${YELLOW}กำลังลบ node_modules และ package-lock.json...${NC}"
    rm -rf node_modules package-lock.json
    
    # ติดตั้ง dependencies ใหม่
    echo -e "${YELLOW}กำลังติดตั้ง dependencies ใหม่...${NC}"
    npm install
    
    # ตรวจสอบอีกครั้ง
    if ! npm run dev -- --help > /dev/null 2>&1; then
        echo -e "${RED}ยังไม่สามารถรัน Vite ได้ กำลังติดตั้ง Vite เพิ่มเติม...${NC}"
        npm install --save-dev vite
    fi
else
    echo -e "${GREEN}Vite พร้อมใช้งาน!${NC}"
fi

# ตรวจสอบไฟล์ .env
echo -e "${YELLOW}กำลังตรวจสอบไฟล์ .env...${NC}"
if [ ! -f .env ]; then
    echo -e "${YELLOW}ไม่พบไฟล์ .env กำลังสร้างจาก .env.example...${NC}"
    cp .env.example .env
    echo -e "${YELLOW}กำลังสร้าง application key...${NC}"
    php artisan key:generate
else
    echo -e "${GREEN}พบไฟล์ .env แล้ว${NC}"
fi

# เคลียร์แคช
echo -e "${YELLOW}กำลังเคลียร์แคชของ Laravel...${NC}"
php artisan optimize:clear

# ตรวจสอบสถานะ migrations
echo -e "${YELLOW}กำลังตรวจสอบสถานะ migrations...${NC}"
php artisan migrate:status

# ถามว่าต้องการรัน migration หรือไม่
read -p "ต้องการรัน migrations หรือไม่? (y/n): " run_migration
if [[ $run_migration == "y" || $run_migration == "Y" ]]; then
    echo -e "${YELLOW}กำลังรัน migrations...${NC}"
    php artisan migrate --force
fi

# ถามว่าต้องการรัน seed หรือไม่
read -p "ต้องการรัน database seeding หรือไม่? (y/n): " run_seed
if [[ $run_seed == "y" || $run_seed == "Y" ]]; then
    echo -e "${YELLOW}กำลังรัน database seeding...${NC}"
    php artisan db:seed
fi

# แสดงรายละเอียดการเริ่มต้นการพัฒนา
echo -e "${GREEN}=============================================${NC}"
echo -e "${GREEN}  การเตรียมพร้อมเสร็จสิ้น!${NC}"
echo -e "${GREEN}  ขั้นตอนต่อไปในการพัฒนา:${NC}"
echo -e "${BLUE}  1. เปิด Terminal อีกหน้าต่างและรัน:${NC} php artisan serve"
echo -e "${BLUE}  2. ในหน้าต่างปัจจุบัน รัน:${NC} npm run dev"
echo -e "${BLUE}  3. เข้าถึงเว็บไซต์ได้ที่:${NC} http://127.0.0.1:8000"
echo -e "${GREEN}=============================================${NC}"

# ถามว่าต้องการรัน npm run dev หรือไม่
read -p "ต้องการรัน 'npm run dev' เลยหรือไม่? (y/n): " run_dev
if [[ $run_dev == "y" || $run_dev == "Y" ]]; then
    echo -e "${YELLOW}กำลังรัน npm run dev...${NC}"
    npm run dev
else
    echo -e "${YELLOW}คุณสามารถรัน 'npm run dev' ด้วยตัวเองได้ในภายหลัง${NC}"
fi

chmod +x dev-start.sh
