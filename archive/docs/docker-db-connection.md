# แก้ไขปัญหาการเชื่อมต่อฐานข้อมูลเมื่อใช้งาน Docker

## ปัญหาที่พบ

เมื่อรันคำสั่ง `php artisan db:show` บนเครื่อง host จะพบข้อผิดพลาด:

```
PDOException
SQLSTATE[HY000] [2002] php_network_getaddresses: getaddrinfo for mysql failed: nodename nor servname provided, or not known
```

## สาเหตุของปัญหา

เกิดจากการใช้ hostname `mysql` ในการเชื่อมต่อฐานข้อมูล ซึ่งจะใช้ได้เฉพาะเมื่ออยู่ใน Docker network เท่านั้น แต่เมื่อรันคำสั่งบนเครื่อง host จะไม่สามารถแปลง hostname `mysql` เป็น IP address ได้

## วิธีแก้ไข

มี 4 วิธีในการแก้ไขปัญหานี้:

### 1. รันคำสั่งผ่าน Laravel Sail (แนะนำ)

แทนที่จะรัน `php artisan command` โดยตรง ให้รันผ่าน Sail:

```bash
./vendor/bin/sail artisan db:show
```

ข้อดี: ไม่ต้องแก้ไขไฟล์ใดๆ และเป็นวิธีที่แนะนำใน Laravel Sail documentation

### 2. ใช้ .env.local สำหรับการพัฒนาบนเครื่อง host

1. สร้างไฟล์ `.env.local` จากไฟล์ `.env` แล้วแก้ไขการตั้งค่าฐานข้อมูล:

```properties
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceosofts
DB_USERNAME=sail
DB_PASSWORD=password
```

2. ใช้ไฟล์ `.env.local` เมื่อรันคำสั่งบนเครื่อง host:

```bash
php -d variables_order=EGPCS artisan db:show --env=local
```

### 3. สร้าง alias สำหรับสลับการใช้งาน

เพิ่ม alias ต่อไปนี้ในไฟล์ `~/.bashrc` หรือ `~/.zshrc`:

```bash
# เพิ่ม alias สำหรับรันคำสั่ง Laravel ในโหมด host
alias artisan-local="DB_HOST=127.0.0.1 php artisan"
```

แล้วรันคำสั่ง:

```bash
artisan-local db:show
```

### 4. แก้ไข hosts file เพื่อเชื่อมต่อถึง MySQL ในลักษณะเดียวกับ Docker

1. ค้นหา IP address ของ MySQL container:

```bash
docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' ceosofts_r1-mysql-1
```

2. เพิ่มบรรทัดต่อไปนี้ในไฟล์ `/etc/hosts`:

```
172.xx.xx.xx mysql
```

(แทนที่ 172.xx.xx.xx ด้วย IP address ที่ได้จากคำสั่งข้างต้น)

## สรุป

วิธีที่แนะนำที่สุดคือ **วิธีที่ 1** - ใช้ Laravel Sail สำหรับการรันคำสั่งทั้งหมด เนื่องจากเป็นวิธีที่เป็นมาตรฐาน และไม่ต้องแก้ไขการตั้งค่าใดๆ

ถ้าจำเป็นต้องรันคำสั่งบนเครื่อง host บ่อยๆ โดยไม่ใช้ Sail วิธีที่ 3 (สร้าง alias) น่าจะเป็นวิธีที่สะดวกที่สุด

## ตัวอย่างคำสั่งเพิ่มเติม

```bash
# เชื่อมต่อกับ MySQL ใน container
./vendor/bin/sail mysql

# รัน migrations ผ่าน Sail
./vendor/bin/sail artisan migrate

# รัน tinker ผ่าน Sail
./vendor/bin/sail tinker
```
