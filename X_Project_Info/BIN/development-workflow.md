# CEOsofts R1 - กระบวนการพัฒนา (Development Workflow)

## ภาพรวม

เอกสารนี้อธิบายกระบวนการพัฒนาซอฟต์แวร์ของโปรเจกต์ CEOsofts R1 ครอบคลุมตั้งแต่การเริ่มต้นพัฒนาฟีเจอร์ใหม่ไปจนถึงการ deploy ขึ้นระบบ production โดยมีวัตถุประสงค์เพื่อให้ทีมพัฒนามีมาตรฐานในการทำงานร่วมกัน

## สภาพแวดล้อมการพัฒนา (Development Environment)

### การติดตั้ง Local Development Environment

1. **ติดตั้ง Dependencies**

    - PHP 8.3+
    - Composer 2.5+
    - Node.js 18+ และ npm
    - MySQL 8.0+
    - Git

2. **Clone Repository และติดตั้ง Dependencies**

    ```bash
    git clone https://github.com/yourorganization/CEOsofts_R1.git
    cd CEOsofts_R1
    composer install
    npm install
    cp .env.example .env  # คัดลอกไฟล์ .env ตั้งต้น
    php artisan key:generate
    ```

3. **สร้างและตั้งค่าฐานข้อมูล**

    ```bash
    php artisan db:create ceosofts_db_R1  # สร้างฐานข้อมูลใหม่
    php artisan migrate  # รัน migrations
    php artisan db:seed  # เพิ่มข้อมูลทดสอบ
    ```

4. **รันเซิร์ฟเวอร์สำหรับการพัฒนา**

    ```bash
    # รัน backend server
    php artisan serve

    # รัน frontend assets (ในอีก terminal หนึ่ง)
    npm run dev
    ```

### การใช้งาน Docker (ทางเลือก)

สำหรับทีมที่ต้องการใช้ Docker เราได้เตรียม docker-compose.yml ไว้ให้:

```bash
# สร้างและรัน containers
docker-compose up -d

# รัน migrations และ seeders
docker-compose exec app php artisan migrate:fresh --seed
```

## กระบวนการ Git และแนวทางการสร้าง Branch

### โครงสร้าง Branches หลัก

-   **`main`**: branch หลักสำหรับ production (stable)
-   **`develop`**: branch หลักสำหรับการพัฒนา (integration)
-   **`feature/*`**: branches สำหรับการพัฒนาฟีเจอร์ใหม่
-   **`bugfix/*`**: branches สำหรับการแก้ไขบัก
-   **`hotfix/*`**: branches สำหรับการแก้ไขฉุกเฉินบน production
-   **`release/*`**: branches สำหรับการเตรียม release

### ขั้นตอนการพัฒนาฟีเจอร์ใหม่

1. **สร้าง Feature Branch ใหม่จาก `develop`**

    ```bash
    git checkout develop
    git pull origin develop
    git checkout -b feature/feature-name
    ```

2. **พัฒนาฟีเจอร์ และ Commit งาน**

    ```bash
    git add .
    git commit -m "feat: add new feature description"
    ```

3. **Push Branch ขึ้น Remote และสร้าง Pull Request**

    ```bash
    git push origin feature/feature-name
    ```

    จากนั้นสร้าง Pull Request ไปยัง `develop` branch ผ่าน GitHub

4. **การ Review Code และการ Merge**
    - อย่างน้อย 1 คนต้อง approve PR
    - CI workflows ต้องผ่านทั้งหมด
    - ใช้ Squash and Merge เพื่อรักษาความสะอาดของ commit history

### แนวทางการตั้งชื่อ Commit

ใช้ Conventional Commits format:

-   `feat: เพิ่มฟีเจอร์ใหม่` - สำหรับฟีเจอร์ใหม่
-   `fix: แก้ไขบัก X` - สำหรับการแก้บัก
-   `docs: อัปเดตเอกสาร` - สำหรับการอัปเดตเอกสาร
-   `refactor: ปรับโครงสร้างโค้ด` - สำหรับการ refactor
-   `test: เพิ่ม test cases` - สำหรับการเพิ่ม tests
-   `chore: อัปเดต dependencies` - สำหรับการอัปเดต dependencies

## การพัฒนาโค้ดตามแนวทาง Domain-Driven Design (DDD)

### แนวทางการเพิ่มฟีเจอร์ใหม่

1. **เลือก Domain ที่เหมาะสม**

    - ระบุว่าฟีเจอร์นี้อยู่ใน Domain ไหนของระบบ (Organization, HumanResources, Sales, Finance, Inventory, Settings)

2. **สร้างโมเดล (ถ้าจำเป็น)**

    - สร้างไฟล์โมเดลใน `app/Domain/[DomainName]/Models/`
    - สร้าง migration ไฟล์สำหรับตารางใหม่
    - ใส่ traits ที่จำเป็น เช่น `HasCompanyScope` สำหรับ multi-tenancy

3. **สร้าง Business Logic**

    - สร้าง Action class ใน `app/Domain/[DomainName]/Actions/`
    - สร้าง Service class ใน `app/Domain/[DomainName]/Services/` (ถ้าซับซ้อน)

4. **สร้าง Controller และ Routes**

    - สร้าง Controller ใน `app/Http/Controllers/`
    - เพิ่ม routes ในไฟล์ที่เหมาะสม (`web.php` หรือ `api.php`)

5. **สร้าง Views/Frontend**
    - สร้าง Blade templates ใน `resources/views/`
    - หรือสร้าง Livewire component ใน `app/Http/Livewire/`

### ตัวอย่างการเพิ่มฟีเจอร์ใหม่

ตัวอย่างการสร้างฟีเจอร์ "บันทึกการลางาน" ในโดเมน HumanResources:

1. **สร้างโมเดล**

```php
// app/Domain/HumanResources/Models/LeaveRequest.php
<?php

namespace App\Domain\HumanResources\Models;

use App\Domain\Shared\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasCompanyScope, SoftDeletes;

    protected $fillable = [
        'company_id',
        'employee_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status'
    ];

    // Relations...
}
```

2. **สร้าง Migration**

```bash
php artisan make:migration create_leave_requests_table
```

3. **สร้าง Action**

```php
// app/Domain/HumanResources/Actions/CreateLeaveRequestAction.php
<?php

namespace App\Domain\HumanResources\Actions;

class CreateLeaveRequestAction
{
    public function execute(array $data)
    {
        // Validation, business logic, etc.
        return LeaveRequest::create($data);
    }
}
```

4. **สร้าง Controller**

```php
// app/Http/Controllers/LeaveRequestController.php
<?php

namespace App\Http\Controllers;

use App\Domain\HumanResources\Actions\CreateLeaveRequestAction;

class LeaveRequestController extends Controller
{
    public function store(Request $request, CreateLeaveRequestAction $action)
    {
        $leaveRequest = $action->execute($request->validated());
        return redirect()->route('leave-requests.index')
            ->with('success', 'บันทึกการลาเรียบร้อยแล้ว');
    }
}
```

## การทดสอบ (Testing)

### ประเภทของการทดสอบ

1. **Unit Tests**

    - ทดสอบ business logic, models, actions
    - พาธ: `tests/Unit/`

2. **Feature Tests**

    - ทดสอบการทำงานของระบบโดยรวม, Controllers, Views
    - พาธ: `tests/Feature/`

3. **Browser Tests**
    - ทดสอบ UI และ user flows
    - พาธ: `tests/Browser/`

### การเขียน Tests

```bash
# สร้าง test class
php artisan make:test LeaveRequestTest --unit

# รัน tests ทั้งหมด
php artisan test

# รัน test เฉพาะไฟล์
php artisan test --filter=LeaveRequestTest
```

### แนวทางการทดสอบ Multi-tenancy

การทดสอบระบบ multi-tenant ต้องคำนึงถึงการแยกข้อมูลระหว่างบริษัท:

```php
// ตัวอย่างการทดสอบ HasCompanyScope
public function test_data_is_properly_scoped_to_company()
{
    // สร้างข้อมูลสำหรับบริษัท A
    $companyA = Company::factory()->create();
    $employeeA = Employee::factory()->create(['company_id' => $companyA->id]);

    // สร้างข้อมูลสำหรับบริษัท B
    $companyB = Company::factory()->create();
    $employeeB = Employee::factory()->create(['company_id' => $companyB->id]);

    // สลับบริษัทปัจจุบันเป็นบริษัท A
    CompanySession::setCurrentCompanyId($companyA->id);

    // ตรวจสอบว่าเห็นเฉพาะข้อมูลของบริษัท A เท่านั้น
    $this->assertEquals(1, Employee::count());
    $this->assertTrue(Employee::first()->is($employeeA));
}
```

## การ Review Code

### กระบวนการ Review

1. **การตั้งชื่อ PR**

    - ใช้รูปแบบ: `[Feature/Fix/etc]: Short description`
    - ตัวอย่าง: `Feature: Add employee leave request system`

2. **สิ่งที่ผู้ Review ควรตรวจสอบ**

    - ความถูกต้องของ business logic
    - แนวทางการเขียนโค้ดตาม coding standards
    - การทำ multi-tenancy อย่างถูกต้อง
    - การมีหรือไม่มี tests ที่เหมาะสม
    - ความสมบูรณ์ของ documentation

3. **เกณฑ์การ Approve PR**
    - ผ่านทุก CI checks
    - แก้ไขทุก comments หรือข้อเสนอแนะ
    - อย่างน้อย 1 คนต้อง approve

## Continuous Integration / Continuous Deployment

### การตั้งค่า Continuous Integration (CI)

เราใช้ GitHub Actions สำหรับ CI โดยมีขั้นตอนดังนี้:

1. **Static Analysis**

    - ตรวจสอบ code quality ด้วย PHP_CodeSniffer
    - ตรวจสอบ security issues ด้วย PHP Security Checker

2. **Unit & Feature Tests**

    - รัน PHPUnit tests

3. **Browser Testing**
    - รัน Laravel Dusk tests (เฉพาะ PRs ที่มีการเปลี่ยนแปลง frontend)

### กระบวนการ Deployment

1. **Staging Environment**

    - `develop` branch จะถูก deploy อัตโนมัติไปยัง staging server
    - QA จะทดสอบบน staging ก่อนที่จะอนุมัติให้ deploy ขึ้น production

2. **Production Environment**
    - สร้าง release branch จาก develop
    - เมื่อ release branch ผ่านการทดสอบแล้วจึง merge เข้า `main`
    - `main` branch จะถูก deploy อัตโนมัติไปยัง production server

## Coding Standards และ Best Practices

### PHP Coding Standards

-   ใช้ [PSR-12](https://www.php-fig.org/psr/psr-12/) เป็นมาตรฐานการเขียนโค้ด
-   จัดรูปแบบ namespace ตาม PSR-4 autoloading

### Laravel Best Practices

1. **Controllers**

    - ให้ controller เป็นเพียงตัวรับ request และส่ง response
    - Business logic ควรอยู่ใน Actions หรือ Services

2. **Models**

    - เก็บเฉพาะ properties และ relationships
    - ใช้ Query Scopes สำหรับการ query ที่ใช้บ่อย

3. **Multi-tenancy**

    - ใช้ HasCompanyScope trait กับทุกโมเดลที่ต้องแบ่งแยกตามบริษัท
    - ระวังการใช้งาน withoutCompanyScope() เพราะอาจทำให้ข้ามการแยกข้อมูล

4. **Authorization**

    - ใช้ Laravel Policies สำหรับการตรวจสอบสิทธิ์
    - ตรวจสอบทั้ง ownership และ permissions

5. **การจัดการข้อมูลสำหรับ Multi-tenancy**
    - ข้อมูลที่ผู้ใช้สร้างต้องมี company_id เสมอ
    - ตรวจสอบ company_id ทุกครั้งใน request validation

## การใช้งาน Commands ที่พัฒนาเพิ่มเติม

### คำสั่งเกี่ยวกับ Migrations

1. **ตรวจสอบไวยากรณ์ของไฟล์ migration**

    ```bash
    php artisan migrate:check-syntax
    ```

2. **แก้ไขไฟล์ migration ที่มีปัญหา**

    ```bash
    php artisan migrate:fix-files file_name_migration.php
    ```

3. **แก้ไขไฟล์ migration ทั้งหมดที่มีปัญหา**

    ```bash
    php artisan migrate:fix-all-files
    ```

4. **ข้าม migration ที่มีปัญหา**
    ```bash
    php artisan migrate:skip migration_name
    ```

### คำสั่งเกี่ยวกับฐานข้อมูล

1. **สร้างฐานข้อมูลใหม่**

    ```bash
    php artisan db:create database_name
    ```

2. **แก้ไขโครงสร้างตาราง**

    ```bash
    php artisan db:fix-schema table_name
    ```

3. **แก้ไข unique constraints ในตาราง translations**
    ```bash
    php artisan db:fix-translations-constraints
    ```

## การแก้ไขปัญหาที่พบบ่อย

### 1. ปัญหา Migration ซ้ำซ้อน

**ปัญหา**: มี migration ที่พยายามสร้างตารางที่มีอยู่แล้ว

**วิธีแก้ไข**:

```bash
# ข้าม migration ที่มีปัญหา
php artisan migrate:skip 2024_xx_xx_migration_name

# หรือทำเครื่องหมายว่าทุก migrations ที่รอดำเนินการเป็นเสร็จสิ้นแล้ว
php artisan migrate:cleanup --all-pending
```

### 2. ปัญหาเกี่ยวกับ Multi-tenancy

**ปัญหา**: ข้อมูลของบริษัทหนึ่งปรากฏให้อีกบริษัทหนึ่งเห็น

**วิธีแก้ไข**:

-   ตรวจสอบว่าโมเดลใช้ HasCompanyScope trait หรือไม่
-   ตรวจสอบว่ามีการใช้ withoutCompanyScope() โดยไม่จำเป็นหรือไม่
-   ตรวจสอบว่า Policy มีการตรวจสอบ company_id หรือไม่

### 3. ปัญหา PDOException เมื่อรัน Migration

**ปัญหา**: เกิด PDOException เมื่อรัน migration

**วิธีแก้ไข**:

```bash
# ลองแก้ไขโครงสร้างตาราง
php artisan db:fix-schema

# หรือตรวจสอบไฟล์ migration
php artisan migrate:check-syntax
```

## สรุป

การพัฒนา CEOsofts R1 ยึดหลักการ Domain-Driven Design และ Multi-tenancy เป็นสำคัญ โดยทีมควรปฏิบัติตามกระบวนการพัฒนานี้เพื่อรักษาคุณภาพของโค้ดและระบบโดยรวม

---

**อัปเดตล่าสุด**: 6 เมษายน 2568  
**จัดทำโดย**: ทีมพัฒนา CEOsofts
