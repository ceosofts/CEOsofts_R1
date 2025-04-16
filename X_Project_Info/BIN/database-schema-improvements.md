# ข้อเสนอแนะการปรับปรุงโครงสร้างฐานข้อมูลก่อนสร้าง Migrations

หลังจากตรวจสอบโครงสร้างฐานข้อมูลที่ออกแบบไว้ใน `database-schema.md` มีข้อเสนอแนะต่อไปนี้ก่อนดำเนินการสร้าง migration files:

## 1. การเพิ่ม Indexes และ Foreign Key Constraints

ทุกตารางควรเพิ่มการระบุ indexes อย่างชัดเจน โดยเฉพาะกับฟิลด์ต่อไปนี้:

-   Foreign keys ทั้งหมด (เช่น `company_id`, `department_id`)
-   ฟิลด์ที่ใช้ค้นหาบ่อยๆ (เช่น `email`, `phone`, `code`)
-   ฟิลด์ที่ใช้จัดเรียง (เช่น `created_at`, `order_date`)

ตัวอย่างสำหรับตาราง `employees`:

```php
// เพิ่ม indices
$table->index('company_id');
$table->index('department_id');
$table->index('position_id');
$table->index('employee_code');
$table->index('email');
$table->index('status');
$table->index(['first_name', 'last_name']);
```

## 2. การระบุ Foreign Key Constraints

ควรระบุ on delete และ on update actions สำหรับ foreign keys เช่น:

-   CASCADE: เมื่อลบ/อัปเดตข้อมูลหลัก ให้ลบ/อัปเดตข้อมูลที่เกี่ยวข้องด้วย
-   RESTRICT/NO ACTION: ป้องกันการลบข้อมูลหลักหากยังมีข้อมูลที่เกี่ยวข้อง
-   SET NULL: เมื่อลบข้อมูลหลัก ให้ตั้งค่า foreign key เป็น NULL

ตัวอย่าง:

```php
$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
$table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
```

## 3. เพิ่มตารางสำหรับ Audit และ Logging

### 3.1. activity_logs

```
- id - int, primary key, auto-increment
- user_id - int, foreign key -> users.id, nullable
- company_id - int, foreign key -> companies.id, nullable
- auditable_type - varchar(255), not null (model class name)
- auditable_id - int, not null (model id)
- event - varchar(50), not null (created, updated, deleted)
- old_values - json, nullable
- new_values - json, nullable
- url - text, nullable
- ip_address - varchar(45), nullable
- user_agent - text, nullable
- created_at - timestamp
```

### 3.2. file_attachments

```
- id - int, primary key, auto-increment
- company_id - int, foreign key -> companies.id
- attachable_type - varchar(255), not null (model class name)
- attachable_id - int, not null (model id)
- path - varchar(255), not null
- filename - varchar(255), not null
- original_filename - varchar(255), not null
- mime_type - varchar(100), not null
- size - int, not null (in bytes)
- disk - varchar(50), default: 'local'
- created_by - int, foreign key -> users.id
- created_at - timestamp
- updated_at - timestamp
```

## 4. JSON Columns สำหรับข้อมูลที่ยืดหยุ่น

พิจารณาการใช้ JSON columns สำหรับข้อมูลที่มีโครงสร้างไม่แน่นอน เช่น:

### 4.1. ปรับปรุงตาราง settings

```php
$table->json('value')->change(); // แทนที่จะเป็น text
$table->json('metadata')->nullable(); // เพิ่มฟิลด์ metadata
```

### 4.2. เพิ่ม metadata ในตารางต่างๆ

```php
$table->json('metadata')->nullable(); // สำหรับเก็บข้อมูลเพิ่มเติมโดยไม่ต้องเปลี่ยนโครงสร้างตาราง
```

## 5. การสนับสนุน Internationalization

### 5.1. เพิ่มตาราง translations

```
- id - int, primary key, auto-increment
- company_id - int, foreign key -> companies.id
- translatable_type - varchar(255), not null (model class name)
- translatable_id - int, not null (model id)
- locale - varchar(10), not null
- field - varchar(50), not null
- value - text, not null
- created_at - timestamp
- updated_at - timestamp
```

## 6. การปรับปรุงฟิลด์สำหรับ Multi-tenancy

ตรวจสอบให้แน่ใจว่ามีฟิลด์ `company_id` ในทุกตารางที่ควรแยกตาม tenant
ยกเว้นตารางที่ควรเป็น global เช่น:

-   permissions (global permissions)
-   some settings (global settings)

## 7. การเพิ่ม UUID

พิจารณาการใช้ UUID แทน Auto-increment integers สำหรับตารางหลัก โดยเฉพาะหากมีการซิงค์ข้อมูลระหว่างระบบ:

```php
$table->uuid('id')->primary();
// หรือ
$table->ulid('id')->primary();
```

## 8. ฟิลด์และข้อจำกัดเพิ่มเติม

### 8.1. ข้อจำกัด Unique ที่รวม company_id

```php
$table->unique(['company_id', 'code']);
$table->unique(['company_id', 'email']);
```

### 8.2. เพิ่มฟิลด์ enabled/disabled

```php
$table->boolean('is_active')->default(true);
```

## 9. การจัดลำดับการสร้าง Migrations

ควรสร้าง migrations ตามลำดับการพึ่งพา (dependency order) ดังนี้:

1. ตารางพื้นฐาน (ไม่มี foreign keys)
    - companies
    - units
    - product_categories
    - expense_categories
    - taxes
2. ตารางระดับกลาง (มี foreign keys ไปยังตารางพื้นฐาน)

    - departments
    - positions
    - branch_offices
    - roles
    - permissions

3. ตารางที่เหลือตามลำดับความสัมพันธ์

## 10. Polymorphic Relationships

พิจารณาการใช้ Polymorphic Relationships สำหรับฟีเจอร์ที่ใช้ร่วมกันหลายตาราง เช่น:

-   comments
-   notes
-   attachments
-   likes

ตัวอย่าง:

```
comments:
- id - int, primary key, auto-increment
- company_id - int, foreign key -> companies.id
- commentable_type - varchar(255)
- commentable_id - int
- content - text
- user_id - int, foreign key -> users.id
- created_at - timestamp
- updated_at - timestamp
```

## 11. แนวทางการสร้าง Migrations

1. ใช้ Laravel Schema ที่รองรับการ rollback
2. เพิ่ม docblock ที่อธิบายวัตถุประสงค์ของการ migration
3. แยก migrations ออกเป็นไฟล์ย่อยๆ เพื่อให้จัดการง่าย
4. ใช้ timestamps ในชื่อไฟล์เพื่อระบุลำดับการทำงาน
5. สร้าง seed data สำหรับข้อมูลที่จำเป็น เช่น roles, permissions

## 12. เพิ่มระบบ Versioning

พิจารณาการใช้ versioning สำหรับข้อมูลที่มีการเปลี่ยนแปลงบ่อยและสำคัญ:

```
model_versions:
- id - int, primary key, auto-increment
- versionable_id - int, not null
- versionable_type - varchar(255), not null
- user_id - int, foreign key -> users.id, nullable
- data - json, not null
- version - int, not null
- created_at - timestamp
```

## 13. Full-Text Search

สำหรับตารางที่ต้องการค้นหาข้อความเต็มรูปแบบ:

```php
// สำหรับ MySQL
$table->fullText(['name', 'description']);

// หรือใช้ database-agnostic approach ด้วย Laravel Scout
```

```

</file>

การสร้าง migrations ที่ดีต้องพิจารณาหลายปัจจัยนอกเหนือจากโครงสร้างฐานข้อมูลพื้นฐาน จากการตรวจสอบไฟล์ database-schema.md ผมมีข้อเสนอแนะสำคัญก่อนที่คุณจะเริ่มสร้าง migrations:

1. **เพิ่มการกำหนด Indexes** - ยังไม่มีการระบุ indexes อย่างชัดเจนในฐานข้อมูลปัจจุบัน ซึ่งจะส่งผลต่อประสิทธิภาพการค้นหา

2. **กำหนด Foreign Key Constraints** - ควรระบุ actions เมื่อข้อมูลหลักถูกลบ (onDelete) หรืออัปเดต (onUpdate)

3. **เพิ่มตารางสำหรับบันทึกกิจกรรม** - ควรมีตาราง activity_logs และ file_attachments เพื่อเก็บประวัติการทำงาน

4. **พิจารณาใช้ UUID** - สำหรับตารางหลักที่อาจต้องมีการซิงค์ข้อมูลระหว่างระบบ

5. **การจัดลำดับการสร้าง Migrations** - ควรสร้างตามลำดับการพึ่งพา เริ่มจากตารางพื้นฐานก่อน

6. **Multi-tenancy ที่สมบูรณ์** - ตรวจสอบว่าทุกตารางที่ควรแยกตาม tenant มีฟิลด์ company_id

ข้อเสนอแนะทั้งหมดนี้จะช่วยให้การสร้าง migrations มีประสิทธิภาพและลดความเสี่ยงในการต้องแก้ไขโครงสร้างฐานข้อมูลในอนาคต
```
