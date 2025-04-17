# โครงสร้างไฟล์ CEOsofts R1

## ภาพรวม

CEOsofts R1 ใช้แนวทาง Domain-Driven Design (DDD) ในการจัดองค์ประกอบของโค้ด ทำให้โค้ดมีความเป็นระเบียบและบำรุงรักษาง่าย โดยแบ่งโค้ดออกเป็นส่วนต่างๆ ตาม Domain ทางธุรกิจ

## โครงสร้างไดเร็กทอรีหลัก

```
CEOsofts_R1/
├── app/                      # โค้ดหลักของระบบ
│   ├── Console/              # คำสั่ง Artisan
│   ├── Domain/               # โค้ดแยกตาม Domain (DDD)
│   ├── Exceptions/           # Exception Handlers
│   ├── Http/                 # Controllers, Middleware, Requests
│   ├── Providers/            # Service Providers
│   └── Support/              # Utilities & Helper functions
├── bootstrap/                # โค้ด Bootstrap ของ Laravel
├── config/                   # ไฟล์การตั้งค่าต่างๆ
├── database/                 # Migrations, Seeds, Factories
│   ├── migrations/           # ไฟล์ Database Migrations
│   └── seeders/              # ไฟล์ Database Seeders
├── lang/                     # ข้อความภาษาต่างๆ
├── public/                   # Assets สาธารณะ, index.php
├── resources/                # Views, Assets ต้นฉบับ
│   ├── css/                  # CSS ต้นฉบับ
│   ├── js/                   # JavaScript ต้นฉบับ
│   └── views/                # Blade Templates
├── routes/                   # ไฟล์กำหนด Routes
├── storage/                  # ไฟล์ที่ระบบสร้างขึ้น
├── tests/                    # Test Cases
├── vendor/                   # Dependencies (Composer)
└── Project_Info/             # เอกสารโครงการ
```

## โครงสร้าง DDD (Domain-Driven Design)

โครงสร้าง Domain-Driven Design ถูกจัดอยู่ในโฟลเดอร์ `app/Domain/` โดยแต่ละ Domain มีโครงสร้างภายในคล้ายกัน:

```
app/Domain/
├── Organization/             # Domain: การจัดการองค์กร
│   ├── Actions/              # Business Actions/Use Cases
│   ├── DataTransferObjects/  # DTOs
│   ├── Events/               # Domain Events
│   ├── Exceptions/           # Domain-specific Exceptions
│   ├── Models/               # Eloquent Models
│   ├── Policies/             # Authorization Policies
│   ├── Repositories/         # Repository Pattern
│   └── Services/             # Domain Services
│
├── HumanResources/           # Domain: ทรัพยากรบุคคล
│   ├── Actions/
│   ├── DataTransferObjects/
│   ├── Events/
│   ├── Exceptions/
│   ├── Models/
│   ├── Policies/
│   ├── Repositories/
│   └── Services/
│
├── Sales/                    # Domain: การขาย
│   ├── Actions/
│   ├── DataTransferObjects/
│   ├── Events/
│   ├── Exceptions/
│   ├── Models/
│   ├── Policies/
│   ├── Repositories/
│   └── Services/
│
├── Finance/                  # Domain: การเงิน
│   ├── Actions/
│   ├── DataTransferObjects/
│   ├── Events/
│   ├── Exceptions/
│   ├── Models/
│   ├── Policies/
│   ├── Repositories/
│   └── Services/
│
├── Inventory/                # Domain: คลังสินค้า
│   ├── Actions/
│   ├── DataTransferObjects/
│   ├── Events/
│   ├── Exceptions/
│   ├── Models/
│   ├── Policies/
│   ├── Repositories/
│   └── Services/
│
├── Settings/                 # Domain: การตั้งค่าระบบ
│   ├── Actions/
│   ├── DataTransferObjects/
│   ├── Events/
│   ├── Exceptions/
│   ├── Models/
│   ├── Policies/
│   ├── Repositories/
│   └── Services/
│
└── Shared/                   # Shared Components
    ├── Actions/
    ├── DataTransferObjects/
    ├── Events/
    ├── Exceptions/
    ├── Models/
    ├── Policies/
    ├── Repositories/
    ├── Services/
    └── Traits/               # Shared Traits (เช่น HasCompanyScope)
```

## คำอธิบายโครงสร้างภายใน Domain

แต่ละ Domain จะมีโครงสร้างภายในที่แบ่งตามหน้าที่ ดังนี้:

### Actions

ส่วนนี้ประกอบด้วยคลาสที่จัดการ business logic เฉพาะ โดยออกแบบให้เป็นคลาสเดียว มีหน้าที่เดียว (Single Responsibility)

**ตัวอย่าง:**

-   `CreateCompanyAction.php` - สร้างบริษัทใหม่
-   `UpdateEmployeeAction.php` - อัปเดตข้อมูลพนักงาน
-   `CreateInvoiceAction.php` - สร้างใบแจ้งหนี้ใหม่

### Models

แบบจำลองข้อมูลที่ใช้ Eloquent ORM ของ Laravel

**ตัวอย่าง:**

-   `Company.php` - โมเดลบริษัท
-   `Employee.php` - โมเดลพนักงาน
-   `Product.php` - โมเดลสินค้า

### Repositories

ส่วนที่จัดการการเข้าถึงข้อมูล แยกการทำงานกับฐานข้อมูลออกจาก business logic

**ตัวอย่าง:**

-   `CompanyRepository.php` - เข้าถึงข้อมูลบริษัท
-   `EmployeeRepository.php` - เข้าถึงข้อมูลพนักงาน
-   `ProductRepository.php` - เข้าถึงข้อมูลสินค้า

### Services

บริการที่ทำงานกับหลาย entity หรือมีความซับซ้อนมากกว่า Actions

**ตัวอย่าง:**

-   `CompanyService.php` - บริการจัดการบริษัท
-   `PayrollService.php` - บริการคำนวณเงินเดือน
-   `InvoicingService.php` - บริการออกใบแจ้งหนี้

### Events

เหตุการณ์ที่เกิดขึ้นในระบบ ใช้สำหรับการสื่อสารระหว่าง Domain

**ตัวอย่าง:**

-   `CompanyCreated.php` - เหตุการณ์เมื่อสร้างบริษัทใหม่
-   `InvoiceIssued.php` - เหตุการณ์เมื่อออกใบแจ้งหนี้
-   `EmployeeHired.php` - เหตุการณ์เมื่อจ้างพนักงานใหม่

### Policies

ใช้สำหรับกำหนดสิทธิ์การเข้าถึงทรัพยากร

**ตัวอย่าง:**

-   `CompanyPolicy.php` - นโยบายการเข้าถึงข้อมูลบริษัท
-   `EmployeePolicy.php` - นโยบายการเข้าถึงข้อมูลพนักงาน
-   `InvoicePolicy.php` - นโยบายการเข้าถึงใบแจ้งหนี้

## คำอธิบาย Domain หลัก

### Organization

จัดการข้อมูลพื้นฐานขององค์กร เช่น บริษัท สาขา แผนก ตำแหน่งงาน

**โมเดลหลัก:** Company, BranchOffice, Department, Position

### HumanResources

จัดการข้อมูลพนักงาน การลางาน การเข้างาน กะการทำงาน

**โมเดลหลัก:** Employee, Leave, Attendance, WorkShift

### Sales

จัดการการขาย ลูกค้า ใบเสนอราคา ใบสั่งซื้อ ใบแจ้งหนี้

**โมเดลหลัก:** Customer, Quotation, Order, Invoice

### Finance

จัดการการเงิน การชำระเงิน ใบเสร็จ

**โมเดลหลัก:** Payment, Receipt, BankAccount, PaymentMethod

### Inventory

จัดการสินค้าและคลังสินค้า

**โมเดลหลัก:** Product, ProductCategory, StockMovement, Unit

### Settings

จัดการการตั้งค่าระบบ

**โมเดลหลัก:** Setting, ScheduledEvent, Translation

### Shared

คอมโพเนนต์ที่ใช้ร่วมกันระหว่าง Domain ต่างๆ

**โมเดลหลัก:** ActivityLog, FileAttachment

## Traits สำคัญ

### HasCompanyScope

Trait หลักสำหรับการทำ multi-tenancy โดยจะกรองข้อมูลตาม company_id โดยอัตโนมัติ

**ตัวอย่างการใช้งาน:**

```php
use App\Domain\Shared\Traits\HasCompanyScope;

class Employee extends Model
{
    use HasCompanyScope;

    // ...model definition...
}
```

### HasUlid

Trait สำหรับการใช้งาน ULID (Universally Unique Lexicographically Sortable Identifier)

**ตัวอย่างการใช้งาน:**

```php
use App\Domain\Shared\Traits\HasUlid;

class Product extends Model
{
    use HasUlid;

    // ...model definition...
}
```

## คำสั่ง Artisan ที่เพิ่มเข้ามา

คำสั่ง Artisan เพิ่มเติมทั้งหมดอยู่ในโฟลเดอร์ `app/Console/Commands/`:

### FixTranslationsUniqueConstraints

แก้ไข unique constraints ในตาราง translations

### FixTranslationsTable

แก้ไขปัญหากับตาราง translations โดยรวม

### FixMigrationFiles

แก้ไขไวยากรณ์ของไฟล์ migration

### FixDatabaseSchema

ตรวจสอบและแก้ไขโครงสร้างตารางในฐานข้อมูล

### FixAllMigrationFiles

แก้ไขไฟล์ migration ทั้งหมดที่มีปัญหา

### CreateDatabase

สร้างฐานข้อมูล MySQL ใหม่

### CheckMigrationSyntax

ตรวจสอบไวยากรณ์ของไฟล์ migration

## ไฟล์การตั้งค่าหลัก

### .env

ไฟล์การตั้งค่าสภาพแวดล้อม สำหรับเก็บค่า configuration ต่างๆ

### .env.local

ไฟล์การตั้งค่าสภาพแวดล้อมสำหรับการใช้งานในเครื่อง local

## การติดตั้งและใช้งานโครงการ

1. Clone repository:

```bash
git clone https://github.com/yourrepository/CEOsofts_R1.git
cd CEOsofts_R1
```

2. ติดตั้ง dependencies:

```bash
composer install
npm install
```

3. สร้างฐานข้อมูล:

```bash
php artisan db:create ceosofts_db_R1
```

4. รัน migrations:

```bash
php artisan migrate
```

5. Seed ข้อมูลเริ่มต้น:

```bash
php artisan db:seed
```

6. รัน server:

```bash
php artisan serve
```

## แนวทางการพัฒนาต่อ

1. **การเพิ่ม Model ใหม่**

    - สร้างโมเดลในโฟลเดอร์ Models ของ Domain ที่เกี่ยวข้อง
    - เพิ่ม migration สำหรับสร้างตาราง
    - ใช้ traits ที่จำเป็น เช่น HasCompanyScope, HasUlid

2. **การเพิ่มฟีเจอร์ใหม่**

    - สร้าง Action ในโฟลเดอร์ Actions ของ Domain ที่เกี่ยวข้อง
    - สร้าง Service ถ้าจำเป็น
    - สร้าง Events และ Listeners ถ้ามีการทำงานแบบ async

3. **การสร้าง API**
    - สร้าง Controller ใน `app/Http/Controllers/Api/`
    - สร้าง Resource ใน `app/Http/Resources/` สำหรับ response format
    - เพิ่ม routes ใน `routes/api.php`

---

**อัปเดตล่าสุด**: 6 เมษายน 2568  
**จัดทำโดย**: ทีมพัฒนา CEOsofts
