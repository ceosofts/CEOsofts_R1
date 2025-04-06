# คำแนะนำการติดตั้ง Laravel และ Laravel Sail ใหม่

## 1. ติดตั้ง Laravel ใหม่

ออกไปที่โฟลเดอร์หลักและลบโฟลเดอร์ CEOsofts_R1 ที่มีปัญหา จากนั้นสร้างใหม่:

```bash
# ออกไปที่โฟลเดอร์หลัก
cd /Users/iwasbornforthis/MyProject

# สำรองเอกสารวางแผนก่อนลบ (ถ้ายังไม่ได้สำรอง)
mkdir -p CEOsofts_R1_backup/Note
cp -r CEOsofts_R1/Note/* CEOsofts_R1_backup/Note/

# ลบโฟลเดอร์เดิมที่มีปัญหา
rm -rf CEOsofts_R1

# สร้างโปรเจค Laravel ใหม่
composer create-project laravel/laravel CEOsofts_R1

# เข้าไปในโปรเจค
cd CEOsofts_R1

# คืนค่าเอกสารวางแผน
mkdir -p Note
cp -r ../CEOsofts_R1_backup/Note/* Note/
```

## 2. ติดตั้ง Laravel Sail

เมื่อสร้างโปรเจค Laravel ใหม่เรียบร้อยแล้ว ให้ติดตั้ง Laravel Sail:

```bash
# ติดตั้ง Laravel Sail
composer require laravel/sail --dev

# ตั้งค่า Sail พร้อมกับ services ที่ต้องการ
php artisan sail:install --with=mysql,redis,mailhog,minio

# สร้าง alias เพื่อความสะดวก (เพิ่มในไฟล์ .bashrc หรือ .zshrc เพื่อให้ใช้ได้ถาวร)
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

# เริ่มการทำงาน Docker containers
./vendor/bin/sail up -d
```

## 3. สร้างโครงสร้างโปรเจคตาม DDD

หลังจาก Laravel Sail ทำงานแล้ว ให้ดำเนินการสร้างโครงสร้างตาม Domain-Driven Design:

```bash
# ติดตั้ง Dependencies หลัก
sail composer require spatie/laravel-permission # สำหรับ RBAC
sail composer require laravel/sanctum # สำหรับ Authentication
sail composer require laravel/telescope --dev # สำหรับ Debugging
sail composer require laravel/horizon # สำหรับ Queue Management
sail composer require opcodesio/log-viewer # สำหรับดู logs ใน browser

# ติดตั้ง JavaScript dependencies
sail npm install
sail npm install -D tailwindcss postcss autoprefixer
sail npm install alpinejs
sail npm install apexcharts

# Initialize Tailwind
sail npx tailwindcss init -p

# สร้างโครงสร้างไฟล์ตาม Domain-Driven Design
mkdir -p app/Domains/Organization/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/HumanResources/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Sales/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Inventory/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Finance/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Settings/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Shared/{Services,Traits,Exceptions,Enums,Interfaces}

# สร้าง Service Providers สำหรับแต่ละ Domain
sail php artisan make:provider OrganizationServiceProvider
sail php artisan make:provider HumanResourcesServiceProvider
sail php artisan make:provider SalesServiceProvider
sail php artisan make:provider InventoryServiceProvider
sail php artisan make:provider FinanceServiceProvider
sail php artisan make:provider SettingsServiceProvider

# สร้างไฟล์ routes สำหรับแต่ละ Domain
mkdir -p routes/domains
touch routes/domains/organization.php
touch routes/domains/human-resources.php
touch routes/domains/sales.php
touch routes/domains/inventory.php
touch routes/domains/finance.php
touch routes/domains/settings.php
touch routes/admin.php

# สร้าง Middleware สำหรับ multi-tenancy
sail php artisan make:middleware EnsureCompanyAccess
touch app/Shared/Traits/HasCompanyScope.php

# รัน migrations เริ่มต้น
sail php artisan migrate
```

## 4. ตรวจสอบการติดตั้ง

หลังจากการตั้งค่าทั้งหมดเสร็จสิ้น ให้ตรวจสอบว่าทุกอย่างทำงานได้ถูกต้อง:

```bash
# ตรวจสอบสถานะ containers
sail ps

# ตรวจสอบการเข้าถึงเว็บแอปพลิเคชัน (เปิดในเบราว์เซอร์)
# URL: http://localhost

# ตรวจสอบว่า migrations ทำงานได้ถูกต้อง
sail php artisan migrate:status
```

## 5. การแก้ไขปัญหาทั่วไป

1. **ถ้า containers ไม่ทำงาน:**

   ```bash
   # หยุดการทำงานและเริ่มใหม่
   sail down
   sail up -d
   ```

2. **ถ้าเกิด port conflicts:**

   ```bash
   # แก้ไขไฟล์ docker-compose.yml เพื่อเปลี่ยน port
   # และรันใหม่
   sail down
   sail up -d
   ```

3. **ถ้า dependencies ติดตั้งไม่สำเร็จ:**
   ```bash
   sail composer dump-autoload
   sail composer update
   ```
