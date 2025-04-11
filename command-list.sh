#!/bin/bash

# คำสั่งที่ใช้งานบ่อยสำหรับ CEOsofts R1
# ================================================

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# ฟังก์ชันสำหรับแสดงคำสั่งต่างๆ
show_commands() {
  clear
  echo -e "${BLUE}================================================${NC}"
  echo -e "${BLUE}         CEOsofts R1 - คำสั่งที่ใช้บ่อย         ${NC}"
  echo -e "${BLUE}================================================${NC}"
  echo ""
  echo -e "${GREEN}1) คำสั่งพื้นฐาน${NC}"
  echo "   1.1) php artisan serve                     - เริ่ม PHP Server"
  echo "   1.2) npm run dev                           - เริ่ม Vite Dev Server"
  echo "   1.3) npm run build                         - Build Assets สำหรับ Production"
  echo "   1.4) php artisan optimize:clear            - เคลียร์แคชทั้งหมด"
  echo ""
  echo -e "${GREEN}2) คำสั่งฐานข้อมูล${NC}"
  echo "   2.1) php artisan migrate                   - รัน Migration"
  echo "   2.2) php artisan migrate:fresh --seed      - รีเซ็ตฐานข้อมูลและ Seed ข้อมูล"
  echo "   2.3) php artisan db:seed                   - Seed ข้อมูลลงฐานข้อมูล"
  echo "   2.4) php artisan migrate:status            - ตรวจสอบสถานะ Migration"
  echo ""
  echo -e "${GREEN}3) คำสั่งสร้างไฟล์${NC}"
  echo "   3.1) php artisan make:controller Name --resource  - สร้าง Controller แบบมี CRUD"
  echo "   3.2) php artisan make:model Name -m               - สร้าง Model พร้อม Migration"
  echo "   3.3) php artisan make:livewire Path/Name          - สร้าง Livewire Component"
  echo "   3.4) php artisan make:policy NamePolicy --model=Name  - สร้าง Policy"
  echo ""
  echo -e "${GREEN}4) คำสั่งเกี่ยวกับ Testing${NC}"
  echo "   4.1) php artisan test                      - รัน PHPUnit Tests"
  echo "   4.2) php artisan make:test NameTest --unit  - สร้าง Unit Test"
  echo "   4.3) php artisan make:test NameTest        - สร้าง Feature Test"
  echo ""
  echo -e "${GREEN}5) คำสั่งอรรถประโยชน์${NC}"
  echo "   5.1) php artisan route:list                - แสดงเส้นทาง Route ทั้งหมด"
  echo "   5.2) php artisan about                     - แสดงข้อมูลเกี่ยวกับระบบ"
  echo "   5.3) php artisan tinker                    - เปิด REPL สำหรับทดสอบโค้ด"
  echo ""
  echo -e "${GREEN}6) คำสั่งเฉพาะ CEOsofts R1${NC}"
  echo "   6.1) php artisan db:create database_name   - สร้างฐานข้อมูล"
  echo "   6.2) php artisan migrate:check-syntax file - ตรวจสอบ syntax ของไฟล์ migration"
  echo "   6.3) php artisan db:fix-schema table_name  - แก้ไขโครงสร้างตาราง"
  echo ""
}

# แสดงหน้าหลัก
show_commands

# บรรทัดที่แสดงตัวเลือก
echo -e "${YELLOW}กรุณาเลือกคำสั่งที่ต้องการใช้ (เช่น 1.1, 2.3) หรือ 'q' เพื่อออก:${NC} "

# รับคำสั่งจากผู้ใช้
read -p "> " choice

# ประมวลผลตัวเลือกของผู้ใช้
case $choice in
    1.1)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan serve"
        php artisan serve
        ;;
    1.2)
        echo -e "${BLUE}กำลังรัน:${NC} npm run dev"
        npm run dev
        ;;
    1.3)
        echo -e "${BLUE}กำลังรัน:${NC} npm run build"
        npm run build
        ;;
    1.4)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan optimize:clear"
        php artisan optimize:clear
        ;;
    2.1)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan migrate"
        php artisan migrate
        ;;
    2.2)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan migrate:fresh --seed"
        php artisan migrate:fresh --seed
        ;;
    2.3)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan db:seed"
        php artisan db:seed
        ;;
    2.4)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan migrate:status"
        php artisan migrate:status
        ;;
    3.1)
        read -p "ชื่อ Controller: " controller_name
        echo -e "${BLUE}กำลังรัน:${NC} php artisan make:controller $controller_name --resource"
        php artisan make:controller $controller_name --resource
        ;;
    3.2)
        read -p "ชื่อ Model: " model_name
        echo -e "${BLUE}กำลังรัน:${NC} php artisan make:model $model_name -m"
        php artisan make:model $model_name -m
        ;;
    3.3)
        read -p "ชื่อ Livewire Component (Path/Name): " livewire_name
        echo -e "${BLUE}กำลังรัน:${NC} php artisan make:livewire $livewire_name"
        php artisan make:livewire $livewire_name
        ;;
    3.4)
        read -p "ชื่อ Policy: " policy_name
        read -p "ชื่อ Model: " model_for_policy
        echo -e "${BLUE}กำลังรัน:${NC} php artisan make:policy $policy_name --model=$model_for_policy"
        php artisan make:policy $policy_name --model=$model_for_policy
        ;;
    4.1)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan test"
        php artisan test
        ;;
    4.2)
        read -p "ชื่อ Unit Test: " unit_test_name
        echo -e "${BLUE}กำลังรัน:${NC} php artisan make:test $unit_test_name --unit"
        php artisan make:test $unit_test_name --unit
        ;;
    4.3)
        read -p "ชื่อ Feature Test: " feature_test_name
        echo -e "${BLUE}กำลังรัน:${NC} php artisan make:test $feature_test_name"
        php artisan make:test $feature_test_name
        ;;
    5.1)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan route:list"
        php artisan route:list
        ;;
    5.2)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan about"
        php artisan about
        ;;
    5.3)
        echo -e "${BLUE}กำลังรัน:${NC} php artisan tinker"
        php artisan tinker
        ;;
    6.1)
        read -p "ชื่อฐานข้อมูล: " db_name
        echo -e "${BLUE}กำลังรัน:${NC} php artisan db:create $db_name"
        php artisan db:create $db_name
        ;;
    6.2)
        read -p "ชื่อไฟล์ migration (ถ้าไม่ระบุจะเช็คทั้งหมด): " migration_file
        echo -e "${BLUE}กำลังรัน:${NC} php artisan migrate:check-syntax $migration_file"
        php artisan migrate:check-syntax $migration_file
        ;;
    6.3)
        read -p "ชื่อตาราง (ถ้าไม่ระบุจะเช็คทั้งหมด): " table_name
        echo -e "${BLUE}กำลังรัน:${NC} php artisan db:fix-schema $table_name"
        php artisan db:fix-schema $table_name
        ;;
    q)
        echo "ขอบคุณที่ใช้งาน"
        exit 0
        ;;
    *)
        echo -e "${YELLOW}ตัวเลือกไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง${NC}"
        ;;
esac

chmod +x command-list.sh
