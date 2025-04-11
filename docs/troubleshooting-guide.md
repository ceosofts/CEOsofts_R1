# คู่มือการแก้ไขปัญหา CEOsofts R1

คู่มือนี้รวบรวมปัญหาที่พบบ่อยในการพัฒนาและการใช้งาน CEOsofts R1 พร้อมวิธีแก้ไข

## 1. ปัญหาเกี่ยวกับ Encryption Key

### ข้อความผิดพลาด: "Unsupported cipher or incorrect key length"

**สาเหตุ**:

-   ไม่มีการตั้งค่า Application key
-   ค่า Cipher ในการเข้ารหัสไม่ถูกต้อง
-   มีการแก้ไข key โดยไม่ได้ตั้งใจ

**วิธีแก้ไข**:

```bash
# สร้างคีย์ใหม่
php artisan key:generate

# แก้ไข cipher ในไฟล์ config/app.php ให้เป็น
'cipher' => 'aes-256-cbc',
```

หรือใช้สคริปต์อัตโนมัติ:

```bash
bash fix-encryption-key.sh
```

## 2. ปัญหาเกี่ยวกับฐานข้อมูล

### 2.1 "SQLSTATE[HY000] [2002] Connection refused"

**สาเหตุ**:

-   ไม่สามารถเชื่อมต่อกับฐานข้อมูล MySQL ได้
-   MySQL Server ไม่ได้เปิดให้บริการ

**วิธีแก้ไข**:

ตรวจสอบการตั้งค่าในไฟล์ `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceosofts
DB_USERNAME=root
DB_PASSWORD=
```

หรือเปลี่ยนไปใช้ SQLite สำหรับการพัฒนา:

```
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

และสร้างไฟล์ฐานข้อมูล:

```bash
touch database/database.sqlite
```

### 2.2 "Base table or view already exists"

**สาเหตุ**:

-   พยายามสร้างตารางที่มีอยู่แล้ว

**วิธีแก้ไข**:

แก้ไขไฟล์ migrations เพื่อตรวจสอบการมีอยู่ของตารางก่อนสร้าง:

```php
if (!Schema::hasTable('table_name')) {
    Schema::create('table_name', function (Blueprint $table) {
        // ...
    });
}
```

หรือใช้คำสั่ง:

```bash
php artisan migrate:fix-files
```

## 3. ปัญหาเกี่ยวกับ Composer และ Packages

### 3.1 "Class not found"

**สาเหตุ**:

-   มีการเพิ่มหรือแก้ไข class แต่ autoload ยังไม่อัปเดต

**วิธีแก้ไข**:

```bash
composer dump-autoload
```

### 3.2 ปัญหาเกี่ยวกับ Dependencies

**วิธีแก้ไข**:

```bash
composer install --no-scripts
composer update
```

## 4. ปัญหาเกี่ยวกับ Assets และ Vite

### 4.1 "vite: command not found"

**วิธีแก้ไข**:

```bash
# ลบและติดตั้งใหม่
rm -rf node_modules
rm package-lock.json
npm install

# ติดตั้ง vite โดยตรง
npm install --save-dev vite
```

### 4.2 ปัญหา Build ล้มเหลว

**สาเหตุ**:

-   ไฟล์ Tailwind CSS มีปัญหา
-   มีการใช้ class ที่ไม่ได้กำหนดใน Tailwind

**วิธีแก้ไข**:

ตรวจสอบไฟล์ `tailwind.config.js` และ `resources/css/app.css`

```bash
# แก้ไขและรันใหม่
npm run build

# แก้ไขอัตโนมัติ
bash fix-tailwind.sh
```

## 5. ปัญหาเกี่ยวกับสิทธิ์ในการเข้าถึงไฟล์

**สาเหตุ**:

-   ไม่สามารถเขียนไฟล์ log หรือ cache ได้

**วิธีแก้ไข**:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 6. ปัญหา "Unable to locate a class or view for component"

**สาเหตุ**:

-   ไม่พบ Component ที่เรียกใช้ใน blade template

**วิธีแก้ไข**:

1. ตรวจสอบว่าสร้างไฟล์ Component ถูกต้อง:

    - `app/View/Components/ComponentName.php`
    - `resources/views/components/component-name.blade.php`

2. เคลียร์ View cache:

```bash
php artisan view:clear
```

3. ตรวจสอบการใช้งานใน Blade:

```blade
<x-component-name />
```

## 7. ปัญหา "Command make:livewire is not defined"

**สาเหตุ**:

-   Livewire ยังไม่ได้ติดตั้ง

**วิธีแก้ไข**:

```bash
composer require livewire/livewire
php artisan package:discover
```

หรือใช้:

```bash
bash composer-require-livewire.sh
```

## 8. วิธีการ Debug ปัญหาอื่นๆ

1. ตรวจสอบ Log:

```bash
tail -f storage/logs/laravel.log
```

2. เปิด Debug mode ใน `.env`:

```
APP_DEBUG=true
```

3. ใช้ Laravel Telescope:

```
http://your-app-url/telescope
```

4. ใช้ Laravel Debugbar (มองที่ด้านล่างของหน้าเว็บ)

## 9. คำสั่งที่มีประโยชน์สำหรับการแก้ไขปัญหา

```bash
# ล้าง Cache ทั้งหมด
php artisan optimize:clear

# แสดงเส้นทาง (routes) ทั้งหมด
php artisan route:list

# แสดงข้อมูลการตั้งค่าแอปพลิเคชัน
php artisan about

# ตรวจสอบความถูกต้องของไฟล์ ENV
php artisan env:check
```

## 10. ติดต่อขอความช่วยเหลือ

หากทำตามขั้นตอนทั้งหมดแล้วยังแก้ปัญหาไม่ได้ กรุณาติดต่อ:

-   Email: support@ceosofts.com
-   Line ID: @ceosofts
-   เบอร์โทร: 02-XXX-XXXX
