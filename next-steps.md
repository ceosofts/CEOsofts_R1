# ขั้นตอนต่อไปในการพัฒนา CEOsofts R1

หลังจากที่คุณได้ติดตั้งและเริ่มต้นโปรเจคเรียบร้อยแล้ว นี่คือขั้นตอนที่แนะนำให้ทำต่อไปตามลำดับความสำคัญ:

## 1. วางแผนการพัฒนาตามโรดแมป

จากโรดแมปที่คุณมี คุณควรเริ่มต้นพัฒนาตามลำดับนี้:

### ระยะที่ 1: ระบบพื้นฐานและโครงสร้างองค์กร (8-20 เม.ย. 2568)

#### ขั้นตอนที่แนะนำ:

1. **เตรียมโครงสร้าง UI หลัก**:

    - สร้าง Layout หลัก (เสร็จแล้ว - `layouts/app.blade.php`)
    - สร้าง Sidebar และ Navbar Components (เสร็จแล้ว)
    - สร้าง UI Components เช่น card, button ที่จะใช้ทั่วทั้งแอป

2. **พัฒนาหน้า Dashboard หลัก**:

    - ปรับแต่ง `DashboardController` และ view
    - สร้าง Livewire Components สำหรับแสดงข้อมูลสรุป

3. **พัฒนาหน้าจอ Company Management**:

    - พัฒนาหน้า CRUD สำหรับบริษัท
    - เพิ่มฟีเจอร์อัปโหลดรูปโลโก้บริษัท
    - ทดสอบการทำงานของ CompanyScope

4. **พัฒนาหน้าจอ Department และ Position Management**:
    - เพิ่มการแสดงผลแบบ Tree View สำหรับโครงสร้างแผนก
    - เพิ่มฟีเจอร์ Drag & Drop สำหรับการปรับโครงสร้างองค์กร

## 2. พัฒนาหน้าจอ CRUD สำหรับ Department

โค้ดพื้นฐานสำหรับ Department มีอยู่แล้ว คุณควรเพิ่มส่วนประกอบต่อไปนี้:

1. **หน้า Index**:

    - เพิ่มตัวกรองแผนกตามบริษัท
    - เพิ่มการแสดงผลแบบ Tree View

2. **หน้า Create/Edit**:

    - ทดสอบฟอร์มการสร้าง/แก้ไข
    - เพิ่มตัวเลือกสำหรับเลือกแผนกหลัก (parent department)

3. **หน้า Show**:
    - แสดงโครงสร้างแผนกย่อย
    - แสดงรายชื่อตำแหน่งในแผนก
    - เพิ่มการเชื่อมโยงไปยังแผนกหลัก

## 3. พัฒนา Multi-tenancy

ระบบ Multi-tenancy พื้นฐานมีอยู่แล้ว (HasCompanyScope) แต่คุณควรพัฒนาเพิ่มเติม:

1. **ทดสอบ CompanyScope**:

    - ทดสอบว่าผู้ใช้เห็นเฉพาะข้อมูลของบริษัทตนเอง
    - ทดสอบการเปลี่ยนบริษัท (Company Selector)

2. **เพิ่ม User Management**:
    - จัดการผู้ใช้และสิทธิ์การเข้าถึงบริษัท
    - เพิ่ม Policy สำหรับควบคุมการเข้าถึงข้อมูลข้ามบริษัท

## 4. Setup ระบบ Testing

1. **เพิ่ม Unit Tests**:

    ```bash
    php artisan make:test CompanyTest --unit
    php artisan make:test DepartmentTest --unit
    ```

2. **เพิ่ม Feature Tests**:

    ```bash
    php artisan make:test CompanyManagementTest
    php artisan make:test DepartmentManagementTest
    ```

3. **เพิ่ม Browser Tests** (เลือกใช้ Laravel Dusk):
    ```bash
    composer require laravel/dusk --dev
    php artisan dusk:install
    php artisan make:test DepartmentBrowserTest --dusk
    ```

## 5. เพิ่ม Policies และ Authorization

```bash
# สร้าง policy สำหรับแต่ละ model
php artisan make:policy CompanyPolicy --model=Company
php artisan make:policy DepartmentPolicy --model=Department
php artisan make:policy PositionPolicy --model=Position
```

## 6. ตัวอย่างการใช้ Testing

เพิ่ม tests สำหรับ Department:

```php
// tests/Unit/DepartmentTest.php
public function test_department_belongs_to_company()
{
    $company = \App\Models\Company::factory()->create();
    $department = \App\Models\Department::factory()->create([
        'company_id' => $company->id
    ]);

    $this->assertEquals($company->id, $department->company->id);
}

// tests/Feature/DepartmentManagementTest.php
public function test_user_can_view_departments_list()
{
    $user = \App\Models\User::factory()->create();
    $company = \App\Models\Company::factory()->create();

    // Setup company access for user
    // ...

    $this->actingAs($user)
        ->get(route('department.index'))
        ->assertStatus(200)
        ->assertSee('แผนก');
}
```

## 7. เพิ่ม Components

### เพิ่ม Blade Components

```bash
php artisan make:component TreeView
php artisan make:component SearchFilter
php artisan make:component BreadcrumbNav
```

### เพิ่ม Livewire Components

```bash
php artisan make:livewire Organization/DepartmentTree
php artisan make:livewire Organization/CompanyStats
php artisan make:livewire Organization/OrgChart
```

## 8. การ Build และ Deploy

1. **Build Assets สำหรับ Production**:

    ```bash
    npm run build
    ```

2. **เตรียม Production Environment**:
    - ตั้งค่า `.env` สำหรับ production
    - เพิ่มการ optimize:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```

## คำแนะนำเพิ่มเติม

1. **จัดระเบียบโค้ด**:

    - ตรวจสอบให้แน่ใจว่าทุกไฟล์อยู่ในโฟลเดอร์ที่ถูกต้อง
    - ตรวจสอบการตั้งชื่อและรูปแบบโค้ดให้สอดคล้องกัน

2. **อัปเดต Documentation**:

    - อัปเดตไฟล์ README.md
    - เพิ่ม API Documentation ถ้ามี

3. **Performance Optimization**:
    - ตรวจสอบและปรับแต่ง Queries
    - เพิ่ม Eager Loading เพื่อลดปัญหา N+1
