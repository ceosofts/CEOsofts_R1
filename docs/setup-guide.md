# คู่มือการติดตั้งและแก้ไขปัญหา CEOsofts R1

## การติดตั้งโปรเจค

### 1. ติดตั้ง Dependencies

```bash
# ติดตั้ง PHP dependencies
composer install

# ติดตั้ง Node.js dependencies
npm install
```

### 2. แก้ไขปัญหา "vite: command not found"

ปัญหานี้เกิดจากการไม่ได้ติดตั้ง dependencies ของ Node.js หรือติดตั้งไม่สมบูรณ์ สามารถแก้ไขได้ด้วยวิธีต่อไปนี้:

#### วิธีที่ 1: ติดตั้ง dependencies ใหม่

```bash
# ลบโฟลเดอร์ node_modules และไฟล์ package-lock.json
rm -rf node_modules package-lock.json

# ติดตั้ง dependencies ใหม่
npm install
```

#### วิธีที่ 2: ติดตั้ง Vite โดยตรง

```bash
# ติดตั้ง Vite แบบ global
npm install -g vite

# หรือติดตั้งเฉพาะโปรเจค
npm install vite --save-dev
```

#### วิธีที่ 3: รันผ่าน npx

```bash
npx vite
```

### 3. ตั้งค่าไฟล์ .env

```bash
# คัดลอกไฟล์ตัวอย่าง
cp .env.example .env

# สร้าง application key
php artisan key:generate
```

### 4. การเตรียมฐานข้อมูล

```bash
# สร้างฐานข้อมูล SQLite
touch database/database.sqlite

# อัปเดตไฟล์ .env เพื่อใช้ SQLite
# DB_CONNECTION=sqlite
# DB_DATABASE=database/database.sqlite

# รัน migration
php artisan migrate

# เพิ่มข้อมูลเริ่มต้น (ถ้ามี)
php artisan db:seed
```

## การแก้ไขปัญหาทั่วไป

### 1. ล้างแคชของ Laravel

บางครั้งอาจมีปัญหาเนื่องจากแคชของ Laravel ให้ลองใช้คำสั่งต่อไปนี้:

```bash
php artisan optimize:clear
```

หรือลองล้างแคชทีละส่วน:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 2. ปัญหาเกี่ยวกับ Assets

หากมีปัญหาเกี่ยวกับ CSS หรือ JavaScript:

```bash
# ลบและติดตั้งใหม่
rm -rf node_modules
npm install

# ลองรันในโหมด production
npm run build
```

### 3. ปัญหาการเข้าถึงไฟล์หรือโฟลเดอร์

```bash
# ตั้งสิทธิ์การเข้าถึง
chmod -R 775 storage bootstrap/cache
```

## การรัน Development Server

```bash
# เปิด Terminal สองหน้าต่าง และรันคำสั่งพร้อมกัน

# Terminal 1: PHP server
php artisan serve

# Terminal 2: Vite server (asset compilation)
npm run dev
```

เข้าถึงเว็บไซต์ได้ที่ http://127.0.0.1:8000

## คำสั่งที่มีประโยชน์สำหรับการพัฒนา

```bash
# สร้าง Controller
php artisan make:controller NameController --resource

# สร้าง Model พร้อม Migration
php artisan make:model Name -m

# สร้าง Livewire Component
php artisan make:livewire Path/ComponentName

# สร้าง Policy
php artisan make:policy NamePolicy --model=Name

# ตรวจสอบเส้นทาง
php artisan route:list

# รัน Tests
php artisan test
```

## คำแนะนำในการ Debug

1. ตรวจสอบไฟล์ log: `storage/logs/laravel.log`
2. เปิดใช้งาน Debug bar: `composer require barryvdh/laravel-debugbar --dev`
3. ตรวจสอบ JavaScript console ใน browser
4. ใช้ `dd()` หรือ `dump()` เพื่อดูค่าตัวแปร

## การบันทึก Log

Laravel มีระบบ logging ที่ดีมาก คุณสามารถใช้งานได้ดังนี้:

```php
use Illuminate\Support\Facades\Log;

Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);
```

## การใช้งาน CompanyScope

ระบบ CEOsofts R1 ใช้ Multi-tenancy โดยมี `HasCompanyScope` trait เป็นกลไกหลัก:

```php
use App\Infrastructure\MultiTenancy\HasCompanyScope;

class YourModel extends Model
{
    use HasCompanyScope;

    // ...
}
```

## การใช้งาน Actions

เราใช้ Single Action Classes เพื่อแยกโลจิกทางธุรกิจ:

```php
$action = new CreateDepartmentAction();
$department = $action->execute([
    'name' => 'ฝ่ายขาย',
    'code' => 'SALE',
    'is_active' => true
]);
```
