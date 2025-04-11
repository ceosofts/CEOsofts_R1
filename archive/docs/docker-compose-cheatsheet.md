# คำสั่ง Docker และ Laravel Sail ที่ใช้บ่อย

## คำสั่ง Laravel Sail พื้นฐาน

```bash
# เริ่มการทำงานของ containers
./vendor/bin/sail up

# เริ่มการทำงานในโหมด detached (ทำงานในพื้นหลัง)
./vendor/bin/sail up -d

# หยุดการทำงานของ containers
./vendor/bin/sail down

# แสดงสถานะของ containers ทั้งหมด
./vendor/bin/sail ps

# รัน PHP Artisan commands
./vendor/bin/sail artisan <command>

# รัน Composer commands
./vendor/bin/sail composer <command>

# รัน NPM commands
./vendor/bin/sail npm <command>

# เข้าสู่ shell ของ container PHP
./vendor/bin/sail shell

# เข้าสู่ MySQL console
./vendor/bin/sail mysql

# รัน tests
./vendor/bin/sail test

# แสดง logs
./vendor/bin/sail logs

# แสดง logs ของ service เฉพาะ
./vendor/bin/sail logs mysql
```

## คำสั่ง Artisan พื้นฐานที่ใช้กับ Sail

```bash
# สร้าง migrations
./vendor/bin/sail artisan make:migration create_table_name_table

# รัน migrations
./vendor/bin/sail artisan migrate

# ย้อน migrations
./vendor/bin/sail artisan migrate:rollback

# สร้าง Model
./vendor/bin/sail artisan make:model ModelName

# สร้าง Controller
./vendor/bin/sail artisan make:controller ControllerName

# สร้าง Service Provider
./vendor/bin/sail artisan make:provider ProviderName

# ล้าง cache
./vendor/bin/sail artisan cache:clear
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan route:clear
./vendor/bin/sail artisan view:clear

# สร้าง symbolic link สำหรับ storage
./vendor/bin/sail artisan storage:link
```

## การจัดการ Docker โดยตรง

```bash
# ดูรายการ containers ที่กำลังทำงาน
docker ps

# ดูรายการ containers ทั้งหมด (รวมที่ไม่ได้ทำงาน)
docker ps -a

# ลบ containers ที่ไม่ได้ใช้
docker container prune

# ลบ volumes ที่ไม่ได้ใช้
docker volume prune

# ลบ networks ที่ไม่ได้ใช้
docker network prune

# ลบทุกอย่างที่ไม่ได้ใช้ (containers, volumes, networks, images)
docker system prune -a

# ดูการใช้ resources
docker stats
```

## การปรับแต่งการตั้งค่า

### การเปลี่ยนพอร์ตที่ใช้ (ถ้าเกิด port conflict)

แก้ไขไฟล์ `docker-compose.yml` ในส่วน ports:

```yaml
# จาก
ports:
    - '${APP_PORT:-80}:80'

# เป็น
ports:
    - '${APP_PORT:-8080}:80'
```

จากนั้นรัน `./vendor/bin/sail down` และ `./vendor/bin/sail up -d`

### การเพิ่ม services ใหม่

สำหรับการเพิ่ม services เช่น phpmyadmin หรือ mongodb ให้แก้ไขไฟล์ `docker-compose.yml`

## ตัวอย่างการใช้งานแบบต่อเนื่อง

```bash
# 1. สร้างโปรเจคใหม่และตั้งค่า Sail
composer create-project laravel/laravel example-app
cd example-app
composer require laravel/sail --dev
php artisan sail:install

# 2. เริ่มการทำงานของ containers
./vendor/bin/sail up -d

# 3. รัน migrations และ seeders
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed

# 4. สร้างโครงสร้าง DDD
mkdir -p app/Domains/Organization/{Models,Services,Http}
./vendor/bin/sail artisan make:model -m app/Domains/Organization/Models/Company

# 5. ติดตั้ง npm packages และรัน dev
./vendor/bin/sail npm install
./vendor/bin/sail npm run dev
```
