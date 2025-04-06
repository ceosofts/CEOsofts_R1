# วิธีแก้ไขการติดตั้ง Laravel Sail

## 1. ลองใช้ service mailpit แทน mailhog

```bash
php artisan sail:install --with=mysql,redis,mailpit,minio
```

หากต้องการดูรายการ services ทั้งหมดที่รองรับ:

```bash
php artisan sail:install --help
```

## 2. เริ่มใช้งาน Sail หลังจากติดตั้งสำเร็จ

```bash
# สร้าง alias สำหรับ Sail (เพื่อความสะดวก)
alias sail='[ -f sail ] && sh sail || sh vendor/bin/sail'

# เริ่มการทำงานของ containers
./vendor/bin/sail up -d

# ตรวจสอบสถานะ
./vendor/bin/sail ps
```

## 3. ดำเนินการติดตั้งโครงสร้างโปรเจคตามแผน

```bash
# สร้างโฟลเดอร์สำหรับ Note
mkdir -p Note

# สร้างโครงสร้างไฟล์ตาม Domain-Driven Design
mkdir -p app/Domains/Organization/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/HumanResources/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Sales/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Inventory/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Finance/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Settings/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Shared/{Services,Traits,Exceptions,Enums,Interfaces}

# ติดตั้ง Dependencies หลัก (หลังจาก Sail ทำงาน)
sail composer require spatie/laravel-permission
sail composer require laravel/sanctum
sail composer require laravel/telescope --dev
sail composer require laravel/horizon
sail composer require opcodesio/log-viewer

# ติดตั้ง JavaScript dependencies
sail npm install
sail npm install -D tailwindcss postcss autoprefixer
sail npm install alpinejs
sail npm install apexcharts

# Initialize Tailwind
sail npx tailwindcss init -p

# สร้าง Service Provider สำหรับแต่ละ Domain
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
```
