# การแก้ไขปัญหาการเชื่อมต่อฐานข้อมูล

## ข้อผิดพลาดที่พบ

เมื่อรันคำสั่ง `php artisan db:show` พบข้อผิดพลาด:

```
PDOException

SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for mysql failed: nodename nor servname provided, or not known
```

## สาเหตุของปัญหา

ข้อความผิดพลาดนี้หมายความว่า Laravel ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ MySQL ได้ ซึ่งอาจเกิดจาก:

1. การตั้งค่าในไฟล์ `.env` ไม่ถูกต้อง
2. MySQL server ไม่ได้ทำงานอยู่
3. Hostname ที่ระบุไม่สามารถแปลงเป็น IP address ได้
4. Firewall หรือการตั้งค่าเครือข่ายปิดกั้นการเชื่อมต่อ
5. Docker container ไม่ได้ทำงานหรือไม่สามารถเข้าถึงได้

## วิธีการแก้ไข

### 1. ตรวจสอบการตั้งค่าในไฟล์ .env

ตรวจสอบว่าการตั้งค่าฐานข้อมูลในไฟล์ `.env` ถูกต้องหรือไม่:

```bash
cat .env | grep DB_
```

ควรมีค่าประมาณนี้:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceosofts
DB_USERNAME=root
DB_PASSWORD=your_password
```

ถ้าใช้งานผ่าน Laravel Sail (Docker), โดยปกติค่าควรเป็น:

```
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=ceosofts
DB_USERNAME=sail
DB_PASSWORD=password
```

### 2. ตรวจสอบ MySQL Server

#### กรณีใช้ MySQL ที่ติดตั้งบนเครื่อง

```bash
# ตรวจสอบว่า MySQL service กำลังทำงานอยู่
sudo systemctl status mysql   # Linux
brew services list            # macOS

# หรือลองเชื่อมต่อด้วย command line
mysql -u root -p -h 127.0.0.1
```

#### กรณีใช้ MySQL ใน Docker (Laravel Sail)

```bash
# ตรวจสอบว่า container กำลังทำงานอยู่
docker ps | grep mysql

# ตรวจสอบ logs ของ container
docker logs ceosofts_r1-mysql-1

# เชื่อมต่อกับ MySQL ใน container
docker exec -it ceosofts_r1-mysql-1 mysql -u root -p
```

### 3. แก้ไขการตั้งค่า host ใน .env

ถ้าคุณใช้ Docker:

-   เปลี่ยน `DB_HOST=127.0.0.1` เป็น `DB_HOST=mysql` (ชื่อ service ใน docker-compose.yml)

ถ้าคุณใช้ MySQL ที่ติดตั้งบนเครื่อง:

-   เปลี่ยน `DB_HOST=mysql` เป็น `DB_HOST=127.0.0.1`

### 4. สร้างฐานข้อมูล (ถ้ายังไม่มี)

```bash
# เชื่อมต่อกับ MySQL
mysql -u root -p

# สร้างฐานข้อมูล
CREATE DATABASE ceosofts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. ทดสอบการเชื่อมต่อด้วย PHP

สร้างไฟล์ `test_db_connection.php` ด้วยเนื้อหา:

```php
<?php
try {
    $conn = new PDO(
        "mysql:host=127.0.0.1;port=3306;dbname=ceosofts",
        "root",
        "your_password"
    );
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
```

แล้วรัน:

```bash
php test_db_connection.php
```

### 6. ล้าง Cache ของ Laravel

```bash
php artisan cache:clear
php artisan config:clear
```

### 7. ตรวจสอบการตั้งค่า Network ใน Docker

ถ้าคุณใช้ Docker:

```bash
# ตรวจสอบว่า docker network ถูกสร้างขึ้นและ containers อยู่ในเครือข่ายเดียวกัน
docker network ls
docker network inspect ceosofts_r1_default
```

### 8. เช็คว่า MySQL รับการเชื่อมต่อจากภายนอกได้

```bash
# ตรวจสอบ MySQL configuration
sudo nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

ตรวจสอบบรรทัด `bind-address` ควรเป็น `0.0.0.0` ถ้าต้องการให้รับการเชื่อมต่อจากทุกที่

### 9. ตรวจสอบการรันคำสั่งผ่าน Laravel Sail

หากใช้ Sail ให้ลองใช้คำสั่งผ่าน Sail:

```bash
./vendor/bin/sail artisan db:show
```

## การทำงานกับ Laravel และ Docker แบบถูกต้อง

1. รัน Laravel Sail:

```bash
./vendor/bin/sail up -d
```

2. ใช้คำสั่ง Artisan ผ่าน Sail:

```bash
./vendor/bin/sail artisan db:show
```

3. ตรวจสอบการตั้งค่าฐานข้อมูลใน Docker environment:

```bash
./vendor/bin/sail exec laravel.test php artisan tinker --execute="DB::connection()->getPdo();"
```

## การทำ Backup และ Restore ฐานข้อมูล

### Backup

```bash
mysqldump -u root -p ceosofts > ceosofts_backup.sql
```

### Restore

```bash
mysql -u root -p ceosofts < ceosofts_backup.sql
```

## ข้อมูลเพิ่มเติมเกี่ยวกับ Laravel Sail และ Docker

-   [Official Laravel Sail Documentation](https://laravel.com/docs/10.x/sail)
-   [Docker Networking](https://docs.docker.com/network/)
-   [MySQL Docker Image](https://hub.docker.com/_/mysql)
