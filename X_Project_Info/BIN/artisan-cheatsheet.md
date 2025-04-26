# Laravel Artisan Cheat Sheet สำหรับ CEOsofts R1

คำสั่งพื้นฐานและคำสั่งที่สร้างขึ้นเฉพาะสำหรับ CEOsofts R1

## 🛠️ คำสั่งทั่วไป

### Server & Cache

```bash
# เริ่ม Development Server
php artisan serve

# เคลียร์ Cache ทั้งหมด
php artisan optimize:clear

# เคลียร์แต่ละประเภท
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database

```bash
# รัน Migration
php artisan migrate

# รีเซ็ตและรัน Migration ใหม่
php artisan migrate:fresh --seed

# รัน Seeder
php artisan db:seed

# ดูสถานะ Migration
php artisan migrate:status
```

### โมเดลและ Controller

```bash
# สร้าง Model พร้อม Migration
php artisan make:model ModelName -m

# สร้าง Controller แบบมี Resource Methods
php artisan make:controller ControllerName --resource

# สร้าง Policy
php artisan make:policy PolicyName --model=ModelName
```

### การทดสอบ

```bash
# รัน Tests ทั้งหมด
php artisan test

# สร้าง Unit Test
php artisan make:test TestName --unit

# สร้าง Feature Test
php artisan make:test TestName
```

## 🧙 คำสั่งเฉพาะของ CEOsofts R1

### การจัดการ Migration

```bash
# ตรวจสอบไวยากรณ์ของ Migration
php artisan migrate:check-syntax

# แก้ไขไฟล์ Migration ที่มีปัญหา
php artisan migrate:fix-files

# แก้ไขทั้งหมดในขั้นตอนเดียว
php artisan migrate:fix-all-files

# ข้าม Migration ที่มีปัญหา
php artisan migrate:skip migration_name
```

### การจัดการฐานข้อมูล

```bash
# สร้างฐานข้อมูล
php artisan db:create database_name

# ตรวจสอบและแก้ไขโครงสร้างตาราง
php artisan db:fix-schema table_name

# รีเซ็ตสถานะ Migration
php artisan migrate:reset-status
```

### คำสั่งเกี่ยวกับองค์กร

```bash
# สร้างบริษัทใหม่
php artisan organization:create-company "ชื่อบริษัท"

# สร้างแผนกใหม่
php artisan organization:create-department "ชื่อแผนก" --company=1

# นำเข้าข้อมูลองค์กรจาก Excel
php artisan organization:import-structure path/to/file.xlsx
```

### คำสั่งเพื่อการพัฒนา

```bash
# ส่งออกโปรเจกต์สำหรับ AI
php artisan project:export-for-ai --format=markdown

# แสดงโครงสร้างของโปรเจกต์
php artisan project:structure

# สร้าง Domain Class
php artisan make:domain-class Company/Actions/CreateCompanyAction
```

### การตั้งค่าและความปลอดภัย

```bash
# ตั้งค่า Application Key ใหม่
php artisan key:generate

# ตรวจสอบความปลอดภัย
php artisan security:check
```

## 🔍 คำสั่งดูข้อมูล

```bash
# แสดงเส้นทาง (Routes)
php artisan route:list

# แสดงข้อมูลแอปพลิเคชัน
php artisan about

# แสดงข้อมูลโมเดล
php artisan model:show ModelName

# แสดงข้อมูลตาราง
php artisan db:table table_name

# แสดงข้อมูล Config
php artisan config:show config_name

# แสดง Schema ของฐานข้อมูล
php artisan schema:dump
```

## 🚀 คำสั่งสำหรับ Production

```bash
# Optimize สำหรับ Production
php artisan optimize

# แคช Routes
php artisan route:cache

# แคช Config
php artisan config:cache

# แคช Views
php artisan view:cache
```

## 📊 Laravel Telescope & Debugging

```bash
# เปิด/ปิดการบันทึก Telescope
php artisan telescope:toggle

# ล้างข้อมูล Telescope
php artisan telescope:clear

# ล้างข้อมูล Telescope ที่เก่าเกินไป
php artisan telescope:prune --hours=48
```

## 🗄️ คำสั่งอื่น ๆ ที่มีประโยชน์

```bash
# เปิด Tinker (REPL)
php artisan tinker

# สร้าง Symlink สำหรับ storage
php artisan storage:link

# แสดงรายการ Jobs
php artisan queue:list

# แสดงสถานะของ Schedule
php artisan schedule:list
```

หากต้องการดูรายละเอียดเพิ่มเติมของคำสั่งใดๆ ให้ใช้ `php artisan help command-name`
