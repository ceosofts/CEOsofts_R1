# คู่มือการใช้งาน Tinker สำหรับโปรเจค CEOsofts R1

Laravel Tinker เป็นเครื่องมือ REPL (Read-Eval-Print Loop) ที่ช่วยให้คุณโต้ตอบกับแอพพลิเคชัน Laravel ได้ผ่าน command line โดยตรง ทำให้สามารถทดสอบโมเดล, ค้นหาข้อมูล, และจัดการกับระบบได้อย่างยืดหยุ่น

## การเริ่มต้นใช้งาน Tinker

เปิด Terminal และนำทางไปที่โฟลเดอร์โปรเจค CEOsofts R1 จากนั้นใช้คำสั่ง:

```bash
php artisan tinker
```

## คำสั่งพื้นฐานใน Tinker

### 1. นำเข้า Class ที่จำเป็น

```php
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
```

### 2. ดูรายการตารางทั้งหมด

```php
DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
```

### 3. ตรวจสอบจำนวนข้อมูลในตาราง

```php
DB::table('employees')->count();
DB::table('companies')->count();
```

### 4. ดูข้อมูลแบบมีเงื่อนไข

```php
Employee::where('status', 'active')->get();
```

### 5. ดูความสัมพันธ์ระหว่างตาราง

```php
// ดูพนักงานทั้งหมดของบริษัทแรก
Company::first()->employees;

// ดูแผนกทั้งหมดของบริษัท
Company::with('departments')->first();
```

### 6. สร้างข้อมูลใหม่

```php
$employee = new Employee;
$employee->first_name = 'สมชาย';
$employee->last_name = 'ใจดี';
$employee->company_id = 1;
$employee->department_id = 1;
$employee->position_id = 1;
$employee->employee_code = 'EMP999';
$employee->status = 'active';
$employee->save();
```

### 7. อัพเดทข้อมูล

```php
$employee = Employee::find(1);
$employee->status = 'inactive';
$employee->save();
```

### 8. ลบข้อมูล

```php
Employee::find(5)->delete();
```

## เคล็ดลับการใช้งาน Tinker

1. **การออกจาก Tinker**: พิมพ์ `exit` หรือกด Ctrl+C
2. **การล้างหน้าจอ**: พิมพ์ `clear-screen` หรือ `clear`
3. **ดูคำสั่งที่ใช้ก่อนหน้า**: กด up arrow
4. **ดูรายละเอียดของ Object**: ใช้ `dump()` หรือ `dd()` เช่น `dump(User::first())`

## Command เพิ่มเติม

โปรเจคมี command พิเศษที่ช่วยในการตรวจสอบฐานข้อมูล:

```bash
php artisan db:tables
php artisan db:tables --count
php artisan db:tables --structure
```

## การใช้งานเพื่อแก้ไขปัญหา

### ตรวจสอบการเชื่อมต่อฐานข้อมูล

```php
DB::connection()->getPdo();
```

### ดูค่า config ของฐานข้อมูลปัจจุบัน

```php
config('database.default');
config('database.connections.sqlite');
```

### ล้างแคชของ Laravel

```php
app()->make('cache')->clear();
```

### ตรวจสอบ Schema ของตาราง

```php
Schema::getColumnListing('employees');
```
