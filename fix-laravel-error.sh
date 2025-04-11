#!/bin/bash

# สคริปต์แก้ไขปัญหาทั่วไปของ Laravel
# ================================================

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     Laravel Error Fixing Utility     ${NC}"
echo -e "${BLUE}================================================${NC}"

# ตรวจสอบว่าเป็นโปรเจกต์ Laravel หรือไม่
if [ ! -f "artisan" ]; then
    echo -e "${RED}ไม่พบไฟล์ artisan ไม่ใช่โปรเจกต์ Laravel${NC}"
    exit 1
fi

ERROR_TYPE=""

# แสดงเมนูตัวเลือก
echo -e "${GREEN}กรุณาเลือกประเภทของข้อผิดพลาดที่พบ:${NC}"
echo "1) Encryption key error (Unsupported cipher or incorrect key length)"
echo "2) Class not found error (Class 'App\...' not found)"
echo "3) Database connection error"
echo "4) View not found error"
echo "5) Permission denied error (storage/logs, bootstrap/cache)"
echo "6) Composer dependencies issues"
echo "7) JavaScript/NPM build issues"
echo "8) Migration issues"
echo "9) Run all fixes (recommended)"
echo "q) ออกจากสคริปต์"

# รับค่าจากผู้ใช้
read -p "> เลือกหมายเลข (1-9) หรือ q เพื่อออก: " ERROR_TYPE

# ฟังก์ชันสำหรับแก้ไขปัญหา Encryption Key
fix_encryption_key() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหา Encryption Key...${NC}"
    
    # ตรวจสอบไฟล์ .env
    if [ ! -f .env ]; then
        echo -e "${RED}ไม่พบไฟล์ .env${NC}"
        echo -e "${YELLOW}กำลังสร้างจาก .env.example...${NC}"
        cp .env.example .env
    fi
    
    # สร้าง APP_KEY ใหม่
    php artisan key:generate --ansi
    
    # แก้ไข cipher ใน config/app.php
    if [ -f config/app.php ]; then
        echo -e "${YELLOW}กำลังตรวจสอบและแก้ไข cipher ใน config/app.php...${NC}"
        sed -i '' "s/'cipher' => '.*'/'cipher' => 'aes-256-cbc'/g" config/app.php
    fi
    
    echo -e "${GREEN}แก้ไขปัญหา Encryption Key เสร็จสิ้น${NC}"
}

# ฟังก์ชันสำหรับแก้ไขปัญหา Class not found
fix_class_not_found() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหา Class not found...${NC}"
    
    # เคลียร์ cache และ regenerate autoload
    composer dump-autoload -o
    php artisan clear-compiled
    php artisan optimize:clear
    
    echo -e "${GREEN}แก้ไขปัญหา Class not found เสร็จสิ้น${NC}"
}

# ฟังก์ชันสำหรับแก้ไขปัญหาการเชื่อมต่อฐานข้อมูล
fix_database_connection() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหาการเชื่อมต่อฐานข้อมูล...${NC}"
    
    # ตรวจสอบการตั้งค่าฐานข้อมูลใน .env
    if grep -q "DB_CONNECTION=sqlite" .env; then
        echo -e "${YELLOW}ตรวจพบการใช้งาน SQLite database${NC}"
        
        # ตรวจสอบว่าไฟล์ SQLite มีอยู่หรือไม่
        sqlite_path=$(grep "DB_DATABASE" .env | sed 's/DB_DATABASE=//')
        
        if [[ "$sqlite_path" == "" || "$sqlite_path" == *"#"* ]]; then
            echo -e "${YELLOW}ไม่พบการกำหนด DB_DATABASE ที่ถูกต้อง${NC}"
            echo -e "${YELLOW}กำลังตั้งค่าเป็น database/database.sqlite...${NC}"
            
            # แก้ไข .env
            sed -i '' 's/DB_DATABASE=.*/DB_DATABASE=database\/database.sqlite/' .env
            
            # สร้างไฟล์ database.sqlite
            mkdir -p database
            touch database/database.sqlite
            
            echo -e "${GREEN}สร้างไฟล์ database/database.sqlite เรียบร้อยแล้ว${NC}"
        else
            # ตรวจสอบว่าไฟล์มีอยู่จริง
            if [ ! -f "$sqlite_path" ]; then
                echo -e "${RED}ไม่พบไฟล์ SQLite ที่ $sqlite_path${NC}"
                
                # สร้างไฟล์ SQLite
                mkdir -p $(dirname "$sqlite_path")
                touch "$sqlite_path"
                
                echo -e "${GREEN}สร้างไฟล์ SQLite ที่ $sqlite_path เรียบร้อยแล้ว${NC}"
            fi
        fi
    fi
    
    # เคลียร์ cache config
    php artisan config:clear
    
    echo -e "${GREEN}แก้ไขปัญหาการเชื่อมต่อฐานข้อมูลเสร็จสิ้น${NC}"
}

# ฟังก์ชันสำหรับแก้ไขปัญหา View not found
fix_view_not_found() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหา View not found...${NC}"
    
    # เคลียร์ view cache และ route cache
    php artisan view:clear
    php artisan route:clear
    
    echo -e "${GREEN}แก้ไขปัญหา View not found เสร็จสิ้น${NC}"
}

# ฟังก์ชันสำหรับแก้ไขปัญหา Permission denied
fix_permission_denied() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหา Permission denied...${NC}"
    
    # ตรวจสอบและสร้างโฟลเดอร์ที่จำเป็น
    mkdir -p storage/framework/views
    mkdir -p storage/framework/cache
    mkdir -p storage/framework/sessions
    mkdir -p storage/logs
    mkdir -p bootstrap/cache
    
    # ตั้งค่าสิทธิ์การเข้าถึง
    chmod -R 775 storage
    chmod -R 775 bootstrap/cache
    
    echo -e "${GREEN}แก้ไขปัญหา Permission denied เสร็จสิ้น${NC}"
}

# ฟังก์ชันสำหรับแก้ไขปัญหา Composer dependencies
fix_composer_dependencies() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหา Composer dependencies...${NC}"
    
    # ตรวจสอบว่า composer.lock มีอยู่หรือไม่
    if [ ! -f composer.lock ]; then
        echo -e "${RED}ไม่พบไฟล์ composer.lock${NC}"
        echo -e "${YELLOW}กำลังติดตั้ง dependencies ใหม่...${NC}"
        composer install
    else
        # ทดลองอัพเดตแพ็กเกจ
        composer update
    fi
    
    echo -e "${GREEN}แก้ไขปัญหา Composer dependencies เสร็จสิ้น${NC}"
}

# ฟังก์ชันสำหรับแก้ไขปัญหา JavaScript/NPM build
fix_npm_issues() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหา JavaScript/NPM build...${NC}"
    
    # ตรวจสอบว่ามี package.json หรือไม่
    if [ ! -f package.json ]; then
        echo -e "${RED}ไม่พบไฟล์ package.json${NC}"
        return
    fi
    
    # ล้าง node_modules และติดตั้งใหม่
    echo -e "${YELLOW}กำลังลบ node_modules และติดตั้งใหม่...${NC}"
    rm -rf node_modules
    rm -f package-lock.json
    
    # ติดตั้ง dependencies ใหม่
    npm install
    
    echo -e "${GREEN}แก้ไขปัญหา JavaScript/NPM build เสร็จสิ้น${NC}"
}

# ฟังก์ชันสำหรับแก้ไขปัญหา Migration
fix_migration_issues() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหา Migration...${NC}"
    
    # ตรวจสอบสถานะ migrations
    php artisan migrate:status
    
    # ถามว่าต้องการ fresh หรือไม่
    read -p "คุณต้องการรัน migrate:fresh หรือไม่? (จะลบข้อมูลทั้งหมด) (y/n): " run_fresh
    
    if [[ "$run_fresh" == "y" || "$run_fresh" == "Y" ]]; then
        php artisan migrate:fresh --seed
    else
        # ลองรัน migration ปกติ
        php artisan migrate
    fi
    
    echo -e "${GREEN}แก้ไขปัญหา Migration เสร็จสิ้น${NC}"
}

# ตรวจสอบและแก้ไขตามที่ผู้ใช้เลือก
case $ERROR_TYPE in
    1)
        fix_encryption_key
        ;;
    2)
        fix_class_not_found
        ;;
    3)
        fix_database_connection
        ;;
    4)
        fix_view_not_found
        ;;
    5)
        fix_permission_denied
        ;;
    6)
        fix_composer_dependencies
        ;;
    7)
        fix_npm_issues
        ;;
    8)
        fix_migration_issues
        ;;
    9)
        echo -e "${YELLOW}กำลังรันการแก้ไขทั้งหมด...${NC}"
        fix_encryption_key
        fix_class_not_found
        fix_database_connection
        fix_view_not_found
        fix_permission_denied
        fix_composer_dependencies
        fix_npm_issues
        
        # ถามก่อนทำ migration
        read -p "ต้องการแก้ไขปัญหา Migration ด้วยหรือไม่? (y/n): " fix_migration
        if [[ "$fix_migration" == "y" || "$fix_migration" == "Y" ]]; then
            fix_migration_issues
        fi
        
        echo -e "${GREEN}แก้ไขปัญหาทั้งหมดเสร็จสิ้น${NC}"
        ;;
    q)
        echo "ออกจากสคริปต์"
        exit 0
        ;;
    *)
        echo -e "${RED}ตัวเลือกไม่ถูกต้อง${NC}"
        ;;
esac

# สรุปผลการรัน
echo -e "\n${BLUE}================================================${NC}"
echo -e "${BLUE}     สรุปการดำเนินการ     ${NC}"
echo -e "${BLUE}================================================${NC}"
echo -e "${YELLOW}คำแนะนำเพิ่มเติม:${NC}"
echo -e "1. ลองรีสตาร์ท PHP server: ${GREEN}php artisan serve${NC}"
echo -e "2. ตรวจสอบล็อก Laravel: ${GREEN}tail -f storage/logs/laravel.log${NC}"
echo -e "3. ถ้าปัญหายังคงอยู่ ลองเปิด Debug mode ใน .env: ${GREEN}APP_DEBUG=true${NC}"

# ทำให้สามารถรันได้
chmod +x fix-laravel-error.sh
