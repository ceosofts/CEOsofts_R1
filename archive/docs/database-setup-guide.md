# คู่มือการสร้างและตั้งค่าฐานข้อมูล ceosofts_db_R1

## การสร้างฐานข้อมูลใหม่

มีสองวิธีในการสร้างฐานข้อมูลใหม่ ขึ้นอยู่กับว่าคุณต้องการใช้ MySQL ตัวไหนในการพัฒนา

### 1. สร้างฐานข้อมูลใน Local MySQL

ถ้าคุณต้องการพัฒนาบนเครื่อง host โดยตรงและใช้ MySQL ที่ติดตั้งบนเครื่อง:

```bash
# เข้าสู่ MySQL
mysql -u root -p

# ป้อนรหัสผ่าน (ถ้ามี) หรือกด Enter ถ้าไม่มีรหัสผ่าน
```

เมื่อเข้าสู่ MySQL console แล้ว ให้รันคำสั่งต่อไปนี้:

```sql
-- สร้างฐานข้อมูลใหม่
CREATE DATABASE ceosofts_db_R1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- ตรวจสอบว่าสร้างสำเร็จ
SHOW DATABASES;

-- ออกจาก MySQL
EXIT;
```

### 2. สร้างฐานข้อมูลใน Docker MySQL (Laravel Sail)

ถ้าคุณต้องการพัฒนาผ่าน Docker และใช้ MySQL ใน container:

```bash
# เข้าสู่ MySQL ใน Docker container
./vendor/bin/sail mysql

# หรือเข้าผ่าน docker exec (ถ้า sail ไม่ทำงาน)
docker exec -it ceosofts_r1-mysql-1 mysql -u root
```

เมื่อเข้าสู่ MySQL console แล้ว ให้รันคำสั่งต่อไปนี้:

```sql
-- สร้างฐานข้อมูลใหม่
CREATE DATABASE ceosofts_db_R1 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- กำหนดสิทธิ์ให้ user 'sail' สามารถเข้าถึงฐานข้อมูลได้
GRANT ALL PRIVILEGES ON ceosofts_db_R1.* TO 'sail'@'%';
FLUSH PRIVILEGES;

-- ตรวจสอบว่าสร้างสำเร็จ
SHOW DATABASES;

-- ออกจาก MySQL
EXIT;
```

## การอัพเดตไฟล์ .env และ .env.local

หลังจากสร้างฐานข้อมูลแล้ว คุณต้องอัพเดตไฟล์ `.env` และ `.env.local` เพื่อให้ Laravel ใช้ฐานข้อมูลใหม่

### แก้ไข .env (สำหรับ Docker/Sail)

```bash
# เปิดไฟล์ .env ด้วย editor
nano .env
```

แก้ไขบรรทัด DB_DATABASE:

```
DB_DATABASE=ceosofts_db_R1
```

### แก้ไข .env.local (สำหรับ Local Development)

```bash
# เปิดไฟล์ .env.local ด้วย editor
nano .env.local
```

แก้ไขบรรทัด DB_DATABASE:

```
DB_DATABASE=ceosofts_db_R1
```

## การโอนย้ายข้อมูลจากฐานข้อมูลเดิม (ถ้าต้องการ)

ถ้าต้องการคัดลอกข้อมูลจากฐานข้อมูลเดิม (`ceosofts` หรือ `ceosofts_db`) ไปยังฐานข้อมูลใหม่ (`ceosofts_db_R1`) สามารถทำได้ดังนี้:

### วิธีที่ 1: Export และ Import

```bash
# Export ข้อมูลจากฐานข้อมูลเดิม
mysqldump -u root -p ceosofts > ceosofts_backup.sql

# Import ข้อมูลเข้าฐานข้อมูลใหม่
mysql -u root -p ceosofts_db_R1 < ceosofts_backup.sql
```

### วิธีที่ 2: คัดลอกตารางผ่าน MySQL Query

เข้า MySQL และรันคำสั่ง:

```sql
-- เลือกฐานข้อมูลต้นทาง
USE ceosofts;

-- รายชื่อตารางในฐานข้อมูล
SHOW TABLES;

-- แสดงโครงสร้างตาราง (ตัวอย่างกับตาราง 'users')
DESCRIBE users;

-- คัดลอกตาราง users จาก ceosofts ไปยัง ceosofts_db_R1
CREATE TABLE ceosofts_db_R1.users LIKE ceosofts.users;
INSERT INTO ceosofts_db_R1.users SELECT * FROM ceosofts.users;

-- ทำซ้ำกับตารางอื่นๆ ที่ต้องการ
```

## การรัน Migrations บนฐานข้อมูลใหม่

หลังจากสร้างฐานข้อมูลและอัพเดต `.env` และ `.env.local` แล้ว คุณต้องรัน migrations เพื่อสร้างโครงสร้างตาราง:

### สำหรับการพัฒนาบน Local:

```bash
# รัน migrations
php -d variables_order=EGPCS artisan migrate --env=local
```

### สำหรับการพัฒนาผ่าน Docker/Sail:

```bash
# รัน migrations
./vendor/bin/sail artisan migrate
```

## การตรวจสอบการเชื่อมต่อกับฐานข้อมูลใหม่

เพื่อตรวจสอบว่า Laravel สามารถเชื่อมต่อกับฐานข้อมูลใหม่ได้:

### สำหรับการพัฒนาบน Local:

```bash
php -d variables_order=EGPCS artisan db:show --env=local
```

### สำหรับการพัฒนาผ่าน Docker/Sail:

```bash
./vendor/bin/sail artisan db:show
```

ผลลัพธ์ที่ได้ควรแสดง `Database: ceosofts_db_R1`

## การเปลี่ยน Connection ในไฟล์ config/database.php (ถ้าจำเป็น)

หากคุณมีการเปลี่ยนแปลงการตั้งค่าเพิ่มเติม คุณอาจต้องแก้ไขไฟล์ `config/database.php` โดยค้นหาส่วน MySQL connections และตรวจสอบการตั้งค่า:

```php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    // ... other settings
],
```

ไม่จำเป็นต้องแก้ไขไฟล์นี้ถ้าคุณได้อัพเดตไฟล์ `.env` และ `.env.local` แล้ว แต่ให้ตรวจสอบว่ามีการตั้งค่า connection อื่นๆ เพิ่มเติมหรือไม่

## ความแตกต่างระหว่างฐานข้อมูลเดิมและฐานข้อมูลใหม่

ฐานข้อมูลใหม่ (ceosofts_db_R1) จะเป็นฐานข้อมูลที่สะอาดและใหม่ ซึ่งจะมีเฉพาะตารางที่ถูกสร้างโดย migrations ของ Laravel เท่านั้น หากคุณต้องการข้อมูลตั้งต้นจากฐานข้อมูลเดิม คุณต้องคัดลอกข้อมูลตามที่อธิบายไว้ข้างต้น
