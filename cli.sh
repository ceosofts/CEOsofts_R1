#!/bin/bash

# สี
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# ตัวแปร
PROJECT_NAME="CEOsofts R1"

# แสดงข้อความสวยงาม
show_header() {
  echo -e "${BLUE}================================================"
  echo -e "           ${PROJECT_NAME} CLI Tool"
  echo -e "================================================${NC}"
  echo -e "พัฒนาโดย: ทีมพัฒนา CEOsofts"
}

# แสดงเมนูหลัก
show_menu() {
  echo -e "\n${YELLOW}โปรดเลือกการดำเนินการ:${NC}"
  echo -e " ${GREEN}1)${NC} เริ่มการพัฒนา (serve + vite)"
  echo -e " ${GREEN}2)${NC} ล้าง Cache ทั้งหมด"
  echo -e " ${GREEN}3)${NC} จัดการฐานข้อมูล"
  echo -e " ${GREEN}4)${NC} อัปเดตโปรเจ็กต์ (dependencies)"
  echo -e " ${GREEN}5)${NC} สร้างไฟล์/คอมโพเนนต์ใหม่"
  echo -e " ${GREEN}6)${NC} โครงสร้างโปรเจ็กต์ (Project Structure)"
  echo -e " ${GREEN}7)${NC} Deployment Tools"
  echo -e " ${GREEN}8)${NC} ตรวจสอบและซ่อมแซมระบบ"
  echo -e " ${GREEN}9)${NC} เกี่ยวกับระบบ"
  echo -e " ${GREEN}0)${NC} ออกจากโปรแกรม"
}

# เริ่มการพัฒนา
start_development() {
  echo -e "\n${YELLOW}เลือกโหมดการพัฒนา:${NC}"
  echo -e " ${GREEN}1)${NC} รัน Laravel Server"
  echo -e " ${GREEN}2)${NC} รัน Vite (Frontend)"
  echo -e " ${GREEN}3)${NC} รันทั้ง Laravel Server และ Vite (พร้อมกัน)"
  echo -e " ${GREEN}4)${NC} กลับไปเมนูหลัก"
  
  read -p "เลือกตัวเลือก [1-4]: " dev_choice
  
  case $dev_choice in
    1)
      echo -e "${YELLOW}กำลังเริ่ม Laravel Server...${NC}"
      php artisan serve
      ;;
    2)
      echo -e "${YELLOW}กำลังเริ่ม Vite Development Server...${NC}"
      npm run dev
      ;;
    3)
      echo -e "${YELLOW}กำลังเริ่ม Laravel Server และ Vite พร้อมกัน...${NC}"
      # ใช้ tmux หรือ screen เพื่อรันหลายคำสั่งพร้อมกัน ในที่นี้ใช้ & เพื่อรันในพื้นหลัง
      php artisan serve &
      npm run dev
      # หยุดการรัน Laravel Server เมื่อออกจาก npm
      kill $!
      ;;
    4)
      return
      ;;
    *)
      echo -e "${RED}ตัวเลือกไม่ถูกต้อง โปรดลองอีกครั้ง${NC}"
      start_development
      ;;
  esac
}

# ล้าง Cache ทั้งหมด
clear_all_cache() {
  echo -e "\n${YELLOW}กำลังล้าง Cache ทั้งหมด...${NC}"
  
  php artisan cache:clear
  echo -e "${GREEN}✓ ล้าง Cache แล้ว${NC}"
  
  php artisan config:clear
  echo -e "${GREEN}✓ ล้าง Config Cache แล้ว${NC}"
  
  php artisan route:clear
  echo -e "${GREEN}✓ ล้าง Route Cache แล้ว${NC}"
  
  php artisan view:clear
  echo -e "${GREEN}✓ ล้าง View Cache แล้ว${NC}"
  
  php artisan clear-compiled
  echo -e "${GREEN}✓ ล้าง Compiled Services แล้ว${NC}"
  
  echo -e "${BLUE}✓ ล้าง Cache ทั้งหมดเรียบร้อยแล้ว!${NC}"
}

# จัดการฐานข้อมูล
manage_database() {
  echo -e "\n${YELLOW}เลือกการจัดการฐานข้อมูล:${NC}"
  echo -e " ${GREEN}1)${NC} รัน Migration"
  echo -e " ${GREEN}2)${NC} รัน Migration พร้อม Seed"
  echo -e " ${GREEN}3)${NC} Refresh Database (ล้างและสร้างใหม่)"
  echo -e " ${GREEN}4)${NC} สร้างฐานข้อมูล SQLite (ถ้าใช้ SQLite)"
  echo -e " ${GREEN}5)${NC} แสดงสถานะ Migration"
  echo -e " ${GREEN}6)${NC} ตรวจสอบและแก้ไขปัญหาในไฟล์ Migration"
  echo -e " ${GREEN}7)${NC} ปรับแต่ง Schema (เพิ่ม Index, แก้ไข Column)"
  echo -e " ${GREEN}8)${NC} Seed ข้อมูลทดสอบ"
  echo -e " ${GREEN}9)${NC} กลับไปเมนูหลัก"
  
  read -p "เลือกตัวเลือก [1-9]: " db_choice
  
  case $db_choice in
    1)
      echo -e "${YELLOW}กำลังรัน Migration...${NC}"
      php artisan migrate
      ;;
    2)
      echo -e "${YELLOW}กำลังรัน Migration พร้อม Seed...${NC}"
      php artisan migrate --seed
      ;;
    3)
      echo -e "${YELLOW}กำลัง Refresh Database...${NC}"
      php artisan migrate:fresh --seed
      ;;
    4)
      echo -e "${YELLOW}กำลังสร้างฐานข้อมูล SQLite...${NC}"
      touch database/database.sqlite
      echo -e "${GREEN}✓ สร้างไฟล์ database.sqlite แล้ว${NC}"
      echo -e "${YELLOW}กำลังแก้ไขสิทธิ์ของไฟล์...${NC}"
      chmod 666 database/database.sqlite
      echo -e "${GREEN}✓ แก้ไขสิทธิ์เรียบร้อย${NC}"
      ;;
    5)
      echo -e "${YELLOW}แสดงสถานะ Migration...${NC}"
      php artisan migrate:status
      ;;
    6)
      echo -e "${YELLOW}ตรวจสอบและแก้ไขปัญหาในไฟล์ Migration...${NC}"
      php artisan migrate:check-syntax
      echo -e "${YELLOW}ต้องการแก้ไขไฟล์ที่มีปัญหาอัตโนมัติหรือไม่? (y/n)${NC}"
      read fix_migrations
      if [ "$fix_migrations" == "y" ]; then
        php artisan migrate:fix-all-files
      fi
      ;;
    7)
      echo -e "${YELLOW}กำลังปรับแต่ง Schema...${NC}"
      php artisan db:fix-schema
      ;;
    8)
      echo -e "${YELLOW}เลือก Seeder ที่ต้องการรัน:${NC}"
      echo -e " ${GREEN}1)${NC} รันทุก Seeders"
      echo -e " ${GREEN}2)${NC} CompanySeeder"
      echo -e " ${GREEN}3)${NC} UserSeeder"
      echo -e " ${GREEN}4)${NC} DepartmentSeeder"
      echo -e " ${GREEN}5)${NC} PositionSeeder"
      echo -e " ${GREEN}6)${NC} EmployeeSeeder"
      echo -e " ${GREEN}7)${NC} ProductSeeder"
      echo -e " ${GREEN}8)${NC} CustomerSeeder"
      read -p "เลือกตัวเลือก [1-8]: " seeder_choice
      
      case $seeder_choice in
        1) php artisan db:seed ;;
        2) php artisan db:seed --class=CompanySeeder ;;
        3) php artisan db:seed --class=UserSeeder ;;
        4) php artisan db:seed --class=DepartmentSeeder ;;
        5) php artisan db:seed --class=PositionSeeder ;;
        6) php artisan db:seed --class=EmployeeSeeder ;;
        7) php artisan db:seed --class=ProductSeeder ;;
        8) php artisan db:seed --class=CustomerSeeder ;;
        *) echo -e "${RED}ตัวเลือกไม่ถูกต้อง${NC}" ;;
      esac
      ;;
    9)
      return
      ;;
    *)
      echo -e "${RED}ตัวเลือกไม่ถูกต้อง โปรดลองอีกครั้ง${NC}"
      manage_database
      ;;
  esac
}

# อัปเดตโปรเจ็กต์
update_project() {
  echo -e "\n${YELLOW}เลือกการอัปเดต:${NC}"
  echo -e " ${GREEN}1)${NC} อัปเดต Composer (PHP packages)"
  echo -e " ${GREEN}2)${NC} อัปเดต NPM (JavaScript packages)"
  echo -e " ${GREEN}3)${NC} อัปเดตทั้งหมด (Composer + NPM)"
  echo -e " ${GREEN}4)${NC} อัปเดต Laravel Framework"
  echo -e " ${GREEN}5)${NC} ตรวจสอบ Vulnerabilities"
  echo -e " ${GREEN}6)${NC} กลับไปเมนูหลัก"
  
  read -p "เลือกตัวเลือก [1-6]: " update_choice
  
  case $update_choice in
    1)
      echo -e "${YELLOW}กำลังอัปเดต Composer Packages...${NC}"
      composer update
      ;;
    2)
      echo -e "${YELLOW}กำลังอัปเดต NPM Packages...${NC}"
      npm update
      ;;
    3)
      echo -e "${YELLOW}กำลังอัปเดตทั้ง Composer และ NPM Packages...${NC}"
      composer update
      npm update
      ;;
    4)
      echo -e "${YELLOW}กำลังอัปเดต Laravel Framework...${NC}"
      composer update laravel/framework
      ;;
    5)
      echo -e "${YELLOW}กำลังตรวจสอบ Vulnerabilities...${NC}"
      echo -e "${BLUE}Composer Security Check:${NC}"
      composer audit
      echo -e "\n${BLUE}NPM Security Check:${NC}"
      npm audit
      ;;
    6)
      return
      ;;
    *)
      echo -e "${RED}ตัวเลือกไม่ถูกต้อง โปรดลองอีกครั้ง${NC}"
      update_project
      ;;
  esac
}

# สร้างไฟล์/คอมโพเนนต์ใหม่
create_new_files() {
  echo -e "\n${YELLOW}เลือกสิ่งที่ต้องการสร้าง:${NC}"
  echo -e " ${GREEN}1)${NC} สร้าง Controller"
  echo -e " ${GREEN}2)${NC} สร้าง Model"
  echo -e " ${GREEN}3)${NC} สร้าง Migration"
  echo -e " ${GREEN}4)${NC} สร้าง Seeder"
  echo -e " ${GREEN}5)${NC} สร้าง Middleware"
  echo -e " ${GREEN}6)${NC} สร้าง Component (Blade)"
  echo -e " ${GREEN}7)${NC} สร้าง Service"
  echo -e " ${GREEN}8)${NC} สร้าง Repository"
  echo -e " ${GREEN}9)${NC} สร้าง Resource ครบชุด (Model, Controller, Migration)"
  echo -e " ${GREEN}0)${NC} กลับไปเมนูหลัก"
  
  read -p "เลือกตัวเลือก [0-9]: " create_choice
  
  if [ "$create_choice" == "0" ]; then
    return
  fi
  
  # รับชื่อ
  read -p "ระบุชื่อ (ไม่มี suffix): " name
  
  case $create_choice in
    1)
      echo -e "${YELLOW}เลือกประเภท Controller:${NC}"
      echo -e " ${GREEN}1)${NC} Controller พื้นฐาน"
      echo -e " ${GREEN}2)${NC} Controller แบบ Resource (CRUD)"
      echo -e " ${GREEN}3)${NC} Controller แบบ API Resource"
      
      read -p "เลือกประเภท [1-3]: " controller_type
      
      case $controller_type in
        1) php artisan make:controller "${name}Controller" ;;
        2) php artisan make:controller "${name}Controller" --resource ;;
        3) php artisan make:controller "Api/${name}Controller" --api ;;
      esac
      ;;
    2)
      echo -e "${YELLOW}สร้าง Model: ${name}${NC}"
      echo -e "${YELLOW}ต้องการสร้าง Migration ด้วยหรือไม่? (y/n)${NC}"
      read with_migration
      
      if [ "$with_migration" == "y" ]; then
        php artisan make:model $name -m
      else
        php artisan make:model $name
      fi
      ;;
    3)
      echo -e "${YELLOW}สร้าง Migration สำหรับตาราง: ${name}s${NC}"
      read -p "ชื่อไฟล์ Migration (เช่น create_${name}s_table): " migration_name
      php artisan make:migration $migration_name
      ;;
    4)
      echo -e "${YELLOW}สร้าง Seeder: ${name}Seeder${NC}"
      php artisan make:seeder "${name}Seeder"
      ;;
    5)
      echo -e "${YELLOW}สร้าง Middleware: ${name}Middleware${NC}"
      php artisan make:middleware "${name}Middleware"
      ;;
    6)
      echo -e "${YELLOW}สร้าง Component: ${name}${NC}"
      php artisan make:component $name
      ;;
    7)
      echo -e "${YELLOW}สร้าง Service: ${name}Service${NC}"
      mkdir -p app/Services
      
      cat > "app/Services/${name}Service.php" << EOL
<?php

namespace App\Services;

class ${name}Service
{
    /**
     * สร้าง instance ของ service.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * ฟังก์ชันตัวอย่าง
     *
     * @return mixed
     */
    public function handle()
    {
        return true;
    }
}
EOL
      echo -e "${GREEN}✓ สร้างไฟล์ app/Services/${name}Service.php เรียบร้อย${NC}"
      ;;
    8)
      echo -e "${YELLOW}สร้าง Repository: ${name}Repository${NC}"
      mkdir -p app/Repositories
      
      cat > "app/Repositories/${name}Repository.php" << EOL
<?php

namespace App\Repositories;

class ${name}Repository
{
    /**
     * สร้าง instance ของ repository.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * ดึงข้อมูลทั้งหมด
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        // ตัวอย่างโค้ด
        // return \App\Models\\${name}::all();
    }
    
    /**
     * ดึงข้อมูลตาม ID
     *
     * @param int \$id
     * @return \App\Models\\${name}|null
     */
    public function find(\$id)
    {
        // ตัวอย่างโค้ด
        // return \App\Models\\${name}::find(\$id);
    }
}
EOL
      echo -e "${GREEN}✓ สร้างไฟล์ app/Repositories/${name}Repository.php เรียบร้อย${NC}"
      ;;
    9)
      echo -e "${YELLOW}สร้าง Resource ครบชุดสำหรับ: ${name}${NC}"
      php artisan make:model $name -mc --resource
      echo -e "${GREEN}✓ สร้าง Model, Controller และ Migration เรียบร้อย${NC}"
      ;;
    *)
      echo -e "${RED}ตัวเลือกไม่ถูกต้อง โปรดลองอีกครั้ง${NC}"
      create_new_files
      ;;
  esac
}

# โครงสร้างโปรเจ็กต์
project_structure() {
  echo -e "\n${YELLOW}เลือกตัวเลือกโครงสร้างโปรเจ็กต์:${NC}"
  echo -e " ${GREEN}1)${NC} แสดงโครงสร้างโปรเจ็กต์ (text)"
  echo -e " ${GREEN}2)${NC} สร้างไฟล์ Markdown โครงสร้างโปรเจ็กต์"
  echo -e " ${GREEN}3)${NC} สร้างไฟล์ JSON โครงสร้างโปรเจ็กต์"
  echo -e " ${GREEN}4)${NC} แสดงไฟล์แก้ไขล่าสุด"
  echo -e " ${GREEN}5)${NC} สร้างเอกสารจากโค้ด (PHPDoc)"
  echo -e " ${GREEN}6)${NC} กลับไปเมนูหลัก"
  
  read -p "เลือกตัวเลือก [1-6]: " structure_choice
  
  case $structure_choice in
    1)
      echo -e "${YELLOW}กำลังแสดงโครงสร้างโปรเจ็กต์...${NC}"
      php artisan project:structure --depth=2
      ;;
    2)
      echo -e "${YELLOW}กำลังสร้างไฟล์ Markdown โครงสร้างโปรเจ็กต์...${NC}"
      php artisan project:structure --format=markdown --output=project_structure.md
      echo -e "${GREEN}✓ สร้างไฟล์ project_structure.md เรียบร้อย${NC}"
      ;;
    3)
      echo -e "${YELLOW}กำลังสร้างไฟล์ JSON โครงสร้างโปรเจ็กต์...${NC}"
      php artisan project:structure --format=json --output=project_structure.json
      echo -e "${GREEN}✓ สร้างไฟล์ project_structure.json เรียบร้อย${NC}"
      ;;
    4)
      echo -e "${YELLOW}กำลังแสดงไฟล์แก้ไขล่าสุด...${NC}"
      find . -type f -not -path "./vendor/*" -not -path "./node_modules/*" -not -path "./storage/*" -not -path "./.git/*" | xargs stat --format '%Y :%y %n' 2>/dev/null | sort -nr | head -n 20 | cut -d: -f2-
      ;;
    5)
      echo -e "${YELLOW}ต้องการติดตั้ง phpDocumentor หรือไม่? (y/n)${NC}"
      read install_phpdoc
      
      if [ "$install_phpdoc" == "y" ]; then
        echo -e "${YELLOW}กำลังติดตั้ง phpDocumentor...${NC}"
        composer require --dev phpdocumentor/phpdocumentor
      fi
      
      echo -e "${YELLOW}กำลังสร้างเอกสารจากโค้ด...${NC}"
      if [ -f "./vendor/bin/phpdoc" ]; then
        ./vendor/bin/phpdoc -d app -t docs/api
        echo -e "${GREEN}✓ สร้างเอกสาร API ที่ docs/api เรียบร้อย${NC}"
      else
        echo -e "${RED}ไม่พบ phpDocumentor กรุณาติดตั้งก่อน${NC}"
      fi
      ;;
    6)
      return
      ;;
    *)
      echo -e "${RED}ตัวเลือกไม่ถูกต้อง โปรดลองอีกครั้ง${NC}"
      project_structure
      ;;
  esac
}

# Deployment Tools
deployment_tools() {
  echo -e "\n${YELLOW}เลือก Deployment Tools:${NC}"
  echo -e " ${GREEN}1)${NC} Optimize สำหรับ Production"
  echo -e " ${GREEN}2)${NC} สร้าง .env.production"
  echo -e " ${GREEN}3)${NC} Export Database (SQL dump)"
  echo -e " ${GREEN}4)${NC} Backup Project (zip)"
  echo -e " ${GREEN}5)${NC} แสดงคำแนะนำสำหรับ Deployment"
  echo -e " ${GREEN}6)${NC} กลับไปเมนูหลัก"
  
  read -p "เลือกตัวเลือก [1-6]: " deploy_choice
  
  case $deploy_choice in
    1)
      echo -e "${YELLOW}กำลัง Optimize สำหรับ Production...${NC}"
      php artisan config:cache
      php artisan route:cache
      php artisan view:cache
      php artisan optimize
      npm run build
      echo -e "${GREEN}✓ Optimize เรียบร้อย${NC}"
      ;;
    2)
      echo -e "${YELLOW}กำลังสร้าง .env.production...${NC}"
      cp .env .env.production
      echo -e "${YELLOW}กรุณาแก้ไขไฟล์ .env.production เพื่อตั้งค่าสำหรับ Production Server${NC}"
      echo -e "${GREEN}✓ สร้างไฟล์ .env.production เรียบร้อย${NC}"
      ;;
    3)
      echo -e "${YELLOW}กำลัง Export Database...${NC}"
      db_connection=$(php artisan tinker --execute="echo config('database.default');")
      
      if [[ $db_connection == *"sqlite"* ]]; then
        echo -e "${YELLOW}ใช้ SQLite - กำลังคัดลอกไฟล์ฐานข้อมูล...${NC}"
        cp database/database.sqlite database/database.sqlite.backup
        echo -e "${GREEN}✓ คัดลอกไฟล์ database.sqlite ไปยัง database.sqlite.backup เรียบร้อย${NC}"
      else
        echo -e "${YELLOW}กรุณาป้อนข้อมูลสำหรับ MySQL export:${NC}"
        read -p "Database: " db_name
        read -p "Username: " db_user
        read -s -p "Password: " db_pass
        echo ""
        
        mysqldump -u $db_user -p$db_pass $db_name > database/database_export.sql
        echo -e "${GREEN}✓ Export ไปยังไฟล์ database_export.sql เรียบร้อย${NC}"
      fi
      ;;
    4)
      echo -e "${YELLOW}กำลัง Backup Project...${NC}"
      backup_date=$(date +"%Y%m%d_%H%M%S")
      backup_name="ceosofts_r1_backup_$backup_date.zip"
      
      zip -r $backup_name . -x "vendor/*" -x "node_modules/*" -x ".git/*" -x "storage/logs/*"
      echo -e "${GREEN}✓ Backup เรียบร้อยที่ไฟล์ $backup_name${NC}"
      ;;
    5)
      echo -e "${BLUE}คำแนะนำสำหรับ Deployment:${NC}"
      echo -e " ${YELLOW}1. ในโหมด Production ให้ตั้งค่าต่อไปนี้ใน .env:${NC}"
      echo -e "    APP_ENV=production"
      echo -e "    APP_DEBUG=false"
      echo -e "    APP_URL=https://yourdomain.com"
      
      echo -e "\n ${YELLOW}2. คำสั่งที่ควรรันหลังจาก Deploy:${NC}"
      echo -e "    composer install --no-dev --optimize-autoloader"
      echo -e "    php artisan migrate --force"
      echo -e "    php artisan optimize"
      echo -e "    php artisan storage:link"
      
      echo -e "\n ${YELLOW}3. ต้องตั้งค่าสิทธิ์:${NC}"
      echo -e "    chmod -R 755 storage bootstrap/cache"
      echo -e "    chown -R www-data:www-data /path/to/project"
      
      echo -e "\n ${YELLOW}4. ตั้งค่า Web Server:${NC}"
      echo -e "    - ใช้ NGINX หรือ Apache พร้อมตั้งค่า Virtual Host"
      echo -e "    - ตั้งค่า Document Root ไปยัง public/"
      echo -e "    - เปิดใช้งาน HTTPS"
      ;;
    6)
      return
      ;;
    *)
      echo -e "${RED}ตัวเลือกไม่ถูกต้อง โปรดลองอีกครั้ง${NC}"
      deployment_tools
      ;;
  esac
}

# ตรวจสอบและซ่อมแซมระบบ
system_check_repair() {
  echo -e "\n${YELLOW}เลือกตัวเลือกตรวจสอบและซ่อมแซม:${NC}"
  echo -e " ${GREEN}1)${NC} ตรวจสอบระบบ (System Check)"
  echo -e " ${GREEN}2)${NC} ซ่อมแซมสิทธิ์ไฟล์และโฟลเดอร์"
  echo -e " ${GREEN}3)${NC} แก้ไข Database Schema อัตโนมัติ"
  echo -e " ${GREEN}4)${NC} แก้ไขปัญหา Migration"
  echo -e " ${GREEN}5)${NC} ตรวจสอบและซ่อมแซมในโหมดเร่งด่วน (All-in-one fix)"
  echo -e " ${GREEN}6)${NC} Optimize Database (เพิ่มประสิทธิภาพฐานข้อมูล)"
  echo -e " ${GREEN}7)${NC} กลับไปเมนูหลัก"
  
  read -p "เลือกตัวเลือก [1-7]: " check_choice
  
  case $check_choice in
    1)
      echo -e "\n${YELLOW}กำลังตรวจสอบระบบ...${NC}"
      
      # ตรวจสอบ PHP
      echo -e "\n${BLUE}[PHP] เวอร์ชัน:${NC}"
      php -v
      
      # ตรวจสอบ Laravel
      echo -e "\n${BLUE}[Laravel] เวอร์ชัน:${NC}"
      php artisan --version
      
      # ตรวจสอบ Permissions
      echo -e "\n${BLUE}[Permissions] ตรวจสอบสิทธิ์:${NC}"
      if [ -w "storage" ] && [ -w "bootstrap/cache" ]; then
        echo -e "${GREEN}✓ สิทธิ์ของโฟลเดอร์ storage และ bootstrap/cache ถูกต้อง${NC}"
      else
        echo -e "${RED}✗ มีปัญหากับสิทธิ์ของโฟลเดอร์ storage หรือ bootstrap/cache${NC}"
      fi
      
      # ตรวจสอบ .env
      echo -e "\n${BLUE}[Environment] ตรวจสอบไฟล์ .env:${NC}"
      if [ -f ".env" ]; then
        if grep -q "APP_KEY=" .env && ! grep -q "APP_KEY=$" .env; then
          echo -e "${GREEN}✓ พบ APP_KEY ใน .env${NC}"
        else
          echo -e "${RED}✗ ไม่พบ APP_KEY หรือ APP_KEY ว่างเปล่า${NC}"
        fi
      else
        echo -e "${RED}✗ ไม่พบไฟล์ .env${NC}"
      fi
      
      # ตรวจสอบฐานข้อมูล
      echo -e "\n${BLUE}[Database] ตรวจสอบการเชื่อมต่อฐานข้อมูล:${NC}"
      php artisan migrate:status > /dev/null 2>&1
      if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ เชื่อมต่อฐานข้อมูลสำเร็จ${NC}"
      else
        echo -e "${RED}✗ มีปัญหาในการเชื่อมต่อฐานข้อมูล${NC}"
      fi
      ;;
    2)
      echo -e "${YELLOW}กำลังซ่อมแซมสิทธิ์ไฟล์และโฟลเดอร์...${NC}"
      chmod -R 755 .
      chmod -R 775 storage
      chmod -R 775 bootstrap/cache
      echo -e "${GREEN}✓ ซ่อมแซมสิทธิ์เรียบร้อยแล้ว${NC}"
      ;;
    3)
      echo -e "${YELLOW}กำลังแก้ไข Database Schema อัตโนมัติ...${NC}"
      php artisan db:fix-schema
      ;;
    4)
      echo -e "${YELLOW}กำลังแก้ไขปัญหา Migration...${NC}"
      php artisan migrate:fix-all-files
      ;;
    5)
      echo -e "${YELLOW}กำลังรัน All-in-one fix...${NC}"
      bash all-in-one-fix.sh
      ;;
    6)
      echo -e "${YELLOW}กำลัง Optimize Database...${NC}"
      php artisan db:optimize --clean-old-data
      ;;
    7)
      return
      ;;
    *)
      echo -e "${RED}ตัวเลือกไม่ถูกต้อง โปรดลองอีกครั้ง${NC}"
      system_check_repair
      ;;
  esac
}

# เกี่ยวกับระบบ
about_system() {
  echo -e "\n${BLUE}เกี่ยวกับระบบ${NC}"
  echo -e "\n${YELLOW}CEOsofts R1 - ระบบบริหารจัดการองค์กรแบบครบวงจร${NC}"
  
  echo -e "\n${BLUE}เวอร์ชันซอฟต์แวร์:${NC}"
  echo -e "Laravel: $(php artisan --version)"
  echo -e "PHP: $(php -v | head -n 1)"
  echo -e "Node.js: $(node -v)"
  echo -e "NPM: $(npm -v)"
  
  echo -e "\n${BLUE}โมดูลในระบบ:${NC}"
  echo -e "✅ การจัดการองค์กร (Companies, Departments)"
  echo -e "✅ การจัดการพนักงาน"
  echo -e "✅ การจัดการสินค้าและบริการ"
  echo -e "✅ การจัดการลูกค้า"
  echo -e "✅ การจัดการใบเสนอราคาและใบสั่งซื้อ"
  echo -e "✅ การจัดการใบแจ้งหนี้และใบเสร็จ"
  
  echo -e "\n${BLUE}ติดต่อสอบถามข้อมูลเพิ่มเติมได้ที่:${NC}"
  echo -e "อีเมล: support@ceosofts.com"
  echo -e "เว็บไซต์: https://www.ceosofts.com"
}

# Main script
clear
show_header

while true; do
  show_menu
  read -p "เลือกตัวเลือก [0-9]: " choice
  
  case $choice in
    1) start_development ;;
    2) clear_all_cache ;;
    3) manage_database ;;
    4) update_project ;;
    5) create_new_files ;;
    6) project_structure ;;
    7) deployment_tools ;;
    8) system_check_repair ;;
    9) about_system ;;
    0)
      echo -e "\n${GREEN}ขอบคุณที่ใช้งาน ${PROJECT_NAME} CLI Tool${NC}"
      break
      ;;
    *)
      echo -e "${RED}ตัวเลือกไม่ถูกต้อง โปรดลองอีกครั้ง${NC}"
      ;;
  esac
  
  echo -e "\n${YELLOW}กด Enter เพื่อดำเนินการต่อ...${NC}"
  read
  clear
  show_header
done

# ตั้งค่าให้ไฟล์เป็น executable
chmod +x cli.sh
