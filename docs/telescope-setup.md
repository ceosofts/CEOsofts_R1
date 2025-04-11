# การติดตั้งและแก้ไขปัญหา Laravel Telescope

Laravel Telescope เป็นเครื่องมือที่ทรงพลังสำหรับการ Debug Laravel Application โดยช่วยให้คุณสามารถตรวจสอบ Request, Exception, Log, Database Queries และอื่นๆ ได้

## การติดตั้ง Telescope

1. **ติดตั้ง Telescope ผ่าน Composer**

```bash
composer require laravel/telescope --dev
```

2. **สร้างไฟล์ Configuration และ Assets**

```bash
php artisan telescope:install
```

3. **รัน Migrations**

```bash
php artisan migrate
```

## การแก้ไขปัญหาที่พบบ่อย

### ปัญหา: มีตารางอยู่แล้ว

หากพบข้อความผิดพลาด `SQLSTATE[42S01]: Base table or view already exists: 1050 Table 'xxx' already exists` เมื่อรัน migrations มีวิธีแก้ไขดังนี้:

#### วิธีที่ 1: รัน Migration เฉพาะ Telescope

```bash
php artisan migrate --path=vendor/laravel/telescope/database/migrations
```

#### วิธีที่ 2: ข้าม Migrations ที่มีปัญหา

```bash
# ดูรายการ migrations ที่ยังไม่ได้รัน
php artisan migrate:status

# ข้าม migration ที่มีปัญหา
php artisan migrate:skip [migration_name]

# รันส่วนที่เหลือ
php artisan migrate
```

#### วิธีที่ 3: แก้ไขไฟล์ Migration

เปิดไฟล์ migration ที่มีปัญหาและแก้ไขให้มีเช็คเงื่อนไขก่อนสร้างตาราง:

```php
// แก้จาก
Schema::create('companies', function (Blueprint $table) {
    // ...
});

// เป็น
if (!Schema::hasTable('companies')) {
    Schema::create('companies', function (Blueprint $table) {
        // ...
    });
}
```

### ปัญหา: การเข้าถึงหน้า Telescope

โดยค่าเริ่มต้น ใน Production Telescope จะเข้าถึงได้เฉพาะ Local Environment เท่านั้น หากต้องการให้เข้าถึงได้ในสภาพแวดล้อมอื่น คุณต้องปรับแต่งไฟล์ `app/Providers/TelescopeServiceProvider.php`:

```php
/**
 * Register the Telescope gate.
 *
 * This gate determines who can access Telescope in non-local environments.
 *
 * @return void
 */
protected function gate()
{
    Gate::define('viewTelescope', function ($user) {
        return in_array($user->email, [
            'admin@example.com',
            // เพิ่มอีเมล์ผู้ใช้ที่สามารถเข้าถึง Telescope ได้
        ]);
    });
}
```

## การใช้งาน Telescope

1. เข้าถึง Telescope Dashboard ได้ที่: `http://your-app-url/telescope`

2. ใน Dashboard คุณสามารถตรวจสอบข้อมูลต่างๆ ได้:
    - Requests & Commands
    - Scheduled Tasks
    - Database Queries
    - Exceptions & Logs
    - Mail
    - Notifications
    - Cache Operations
    - Redis Operations

## เทคนิคการใช้งาน Telescope

### 1. การเก็บ Log แบบเจาะจง

ใน `.env` เพิ่มตัวแปรต่อไปนี้:

```
TELESCOPE_ENABLED=true
TELESCOPE_RECORD_ONLY_PATHS=api/*,admin/*
```

### 2. ปิด Telescope ใน Testing Environment

เพิ่มใน `phpunit.xml`:

```xml
<server name="TELESCOPE_ENABLED" value="false"/>
```

### 3. จำกัดข้อมูลที่เก็บ

ใน `config/telescope.php` สามารถปรับแต่งการเก็บข้อมูลได้:

```php
'storage' => [
    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
        'chunk' => 1000,
        // จำนวนวันที่จะเก็บข้อมูล
        'prune' => [
            'hours' => 24,
        ],
    ],
],
```

### 4. การตั้งค่าการตรวจจับ Slow Queries

```php
'watchers' => [
    // ...

    Watchers\QueryWatcher::class => [
        'enabled' => env('TELESCOPE_QUERY_WATCHER', true),
        'slow' => 100, // จับ query ที่ใช้เวลามากกว่า 100ms
    ],

    // ...
],
```

## คำสั่ง Artisan ที่เกี่ยวข้อง

```bash
# สร้าง assets ใหม่
php artisan telescope:publish

# ล้างข้อมูลเก่าใน Telescope
php artisan telescope:prune

# หรือระบุจำนวนชั่วโมง
php artisan telescope:prune --hours=48
```

## ข้อแนะนำสำหรับ Production

1. ติดตั้ง Telescope เป็น Development Dependency เท่านั้น
2. จำกัดการเข้าถึงด้วย Gate ที่เหมาะสม
3. ตั้งค่า Prune เพื่อไม่ให้ข้อมูลเก็บมากเกินไป
4. พิจารณาปิด Watchers ที่ไม่จำเป็น
