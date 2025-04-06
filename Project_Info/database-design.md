# การออกแบบฐานข้อมูล CEOsofts

## สารบัญ

1. [ภาพรวมการออกแบบฐานข้อมูล](#ภาพรวมการออกแบบฐานข้อมูล)
2. [โครงสร้างฐานข้อมูลแบ่งตาม Domain](#โครงสร้างฐานข้อมูลแบ่งตาม-domain)
3. [รายละเอียดตารางแยกตาม Domain](#รายละเอียดตารางแยกตาม-domain)
4. [ตารางระบบ และ Audit](#ตารางระบบ-และ-audit)
5. [ความสัมพันธ์ระหว่างตาราง](#ความสัมพันธ์ระหว่างตาราง)
6. [การจัดการเอกสาร PDF](#การจัดการเอกสาร-pdf)
7. [แนวทางการสร้าง Migrations](#แนวทางการสร้าง-migrations)

## ภาพรวมการออกแบบฐานข้อมูล

### แนวทางหลักในการออกแบบ

1. **Multi-tenancy แบบสมบูรณ์**:

    - เพิ่ม `company_id` ในทุกตารางที่ควรแยกตาม tenant
    - เพิ่ม unique constraints ที่รวม company_id
    - ยกเว้นตารางระบบที่ใช้ร่วมกัน

2. **เทคนิค Indexing**:

    - ระบุ indexes อย่างชัดเจนในทุกตาราง
    - เพิ่ม indexes สำหรับฟิลด์ที่ใช้ค้นหาบ่อย และ foreign keys
    - สร้าง composite indexes สำหรับการค้นหาที่ซับซ้อน

3. **UUID/ULID สำหรับตารางหลัก**:

    - เพิ่มฟิลด์ `uuid` (ใช้ ULID) ในตารางหลัก
    - ใช้คู่กับ auto-increment `id` เพื่อรองรับการซิงค์ข้อมูล

4. **การออกแบบเชิงสัมพันธ์**:

    - ระบุ `onDelete` และ `onUpdate` actions อย่างชัดเจน
    - กำหนด relationships ในทุกตาราง

5. **การใช้ JSON Columns**:

    - เพิ่ม `metadata` และ `settings` ในรูปแบบ JSON
    - รองรับข้อมูลที่มีโครงสร้างไม่แน่นอน

6. **การบันทึกประวัติและ Audit**:
    - ใช้ `timestamps` และ `softDeletes` ในทุกตารางหลัก
    - เพิ่มตาราง `activity_logs` สำหรับบันทึกการเปลี่ยนแปลง
    - ระบุผู้ดำเนินการ (`created_by`, `updated_by`, `deleted_by`)

## โครงสร้างฐานข้อมูลแบ่งตาม Domain

php artisan migrate:status

php artisan migrate:fresh

php artisan migrate

php artisan db:seed

```
+-------------------+         +----------------------+         +--------------------+
|   Organization    |<------->|    HumanResources    |<------->|        Sales       |
+-------------------+         +----------------------+         +--------------------+
         ^                              ^                              ^
         |                              |                              |
         v                              v                              v
+-------------------+         +----------------------+         +--------------------+
|     Settings      |<------->|       Finance        |<------->|      Inventory     |
+-------------------+         +----------------------+         +--------------------+
                                        ^
                                        |
                                        v
                              +--------------------+
                              |   System Tables    |
                              +--------------------+
```

## รายละเอียดตารางแยกตาม Domain

### 1. Organization Domain

#### 1.1. companies

| Column     | Type            | Properties                  | Description      |
| ---------- | --------------- | --------------------------- | ---------------- |
| id         | bigint unsigned | primary key, auto-increment |                  |
| uuid       | ulid            | unique                      | จัดเก็บเป็น ULID |
| name       | varchar(255)    | not null                    |                  |
| code       | varchar(50)     | nullable, unique            |                  |
| address    | text            | nullable                    |                  |
| phone      | varchar(20)     | nullable                    |                  |
| email      | varchar(255)    | nullable                    |                  |
| tax_id     | varchar(50)     | nullable                    |                  |
| website    | varchar(255)    | nullable                    |                  |
| logo       | varchar(255)    | nullable                    |                  |
| is_active  | boolean         | default: true               |                  |
| status     | varchar(20)     | default: 'active'           |                  |
| settings   | json            | nullable                    |                  |
| metadata   | json            | nullable                    |                  |
| created_at | timestamp       |                             |                  |
| updated_at | timestamp       |                             |                  |
| deleted_at | timestamp       | nullable                    |                  |

**Indexes**: name, code, email, phone, status, is_active

#### 1.2. departments

| Column      | Type            | Properties                              | Description                 |
| ----------- | --------------- | --------------------------------------- | --------------------------- |
| id          | bigint unsigned | primary key, auto-increment             |                             |
| company_id  | bigint unsigned | foreign key -> companies.id             | onDelete: cascade           |
| name        | varchar(255)    | not null                                |                             |
| code        | varchar(50)     | nullable                                |                             |
| description | text            | nullable                                |                             |
| is_active   | boolean         | default: true                           |                             |
| status      | varchar(20)     | default: 'active'                       |                             |
| parent_id   | bigint unsigned | nullable, foreign key -> departments.id | สำหรับโครงสร้างแบบลำดับชั้น |
| metadata    | json            | nullable                                |                             |
| created_at  | timestamp       |                                         |                             |
| updated_at  | timestamp       |                                         |                             |
| deleted_at  | timestamp       | nullable                                |                             |

**Indexes**: company_id, parent_id, name, code, is_active, status  
**Unique**: [company_id, code]

#### 1.3. positions

| Column        | Type            | Properties                    | Description       |
| ------------- | --------------- | ----------------------------- | ----------------- |
| id            | bigint unsigned | primary key, auto-increment   |                   |
| company_id    | bigint unsigned | foreign key -> companies.id   | onDelete: cascade |
| department_id | bigint unsigned | foreign key -> departments.id | onDelete: cascade |
| name          | varchar(255)    | not null                      |                   |
| code          | varchar(50)     | nullable                      |                   |
| description   | text            | nullable                      |                   |
| is_active     | boolean         | default: true                 |                   |
| level         | int             | nullable                      |                   |
| min_salary    | decimal(15,2)   | nullable                      |                   |
| max_salary    | decimal(15,2)   | nullable                      |                   |
| metadata      | json            | nullable                      |                   |
| created_at    | timestamp       |                               |                   |
| updated_at    | timestamp       |                               |                   |
| deleted_at    | timestamp       | nullable                      |                   |

**Indexes**: company_id, department_id, name, code, level, is_active  
**Unique**: [company_id, code]

#### 1.4. branch_offices

| Column          | Type            | Properties                  | Description       |
| --------------- | --------------- | --------------------------- | ----------------- |
| id              | bigint unsigned | primary key, auto-increment |                   |
| company_id      | bigint unsigned | foreign key -> companies.id | onDelete: cascade |
| name            | varchar(255)    | not null                    |                   |
| code            | varchar(50)     | nullable                    |                   |
| address         | text            | nullable                    |                   |
| phone           | varchar(20)     | nullable                    |                   |
| email           | varchar(255)    | nullable                    |                   |
| is_headquarters | boolean         | default: false              |                   |
| is_active       | boolean         | default: true               |                   |
| metadata        | json            | nullable                    |                   |
| created_at      | timestamp       |                             |                   |
| updated_at      | timestamp       |                             |                   |
| deleted_at      | timestamp       | nullable                    |                   |

**Indexes**: company_id, name, code, is_headquarters, is_active, email  
**Unique**: [company_id, code]

### 2. Human Resources Domain

#### 2.1. employees

| Column                  | Type            | Properties                                 | Description        |
| ----------------------- | --------------- | ------------------------------------------ | ------------------ |
| id                      | bigint unsigned | primary key, auto-increment                |                    |
| uuid                    | ulid            | unique                                     |                    |
| company_id              | bigint unsigned | foreign key -> companies.id                | onDelete: cascade  |
| department_id           | bigint unsigned | foreign key -> departments.id              | onDelete: restrict |
| position_id             | bigint unsigned | foreign key -> positions.id                | onDelete: restrict |
| branch_office_id        | bigint unsigned | nullable, foreign key -> branch_offices.id | onDelete: set null |
| employee_code           | varchar(50)     |                                            |                    |
| first_name              | varchar(255)    | not null                                   |                    |
| last_name               | varchar(255)    | not null                                   |                    |
| email                   | varchar(255)    | nullable, unique                           |                    |
| phone                   | varchar(20)     | nullable                                   |                    |
| address                 | text            | nullable                                   |                    |
| birthdate               | date            | nullable                                   |                    |
| hire_date               | date            | not null                                   |                    |
| termination_date        | date            | nullable                                   |                    |
| id_card_number          | varchar(50)     | nullable                                   |                    |
| tax_id                  | varchar(50)     | nullable                                   |                    |
| bank_account            | varchar(50)     | nullable                                   |                    |
| bank_name               | varchar(100)    | nullable                                   |                    |
| emergency_contact_name  | varchar(255)    | nullable                                   |                    |
| emergency_contact_phone | varchar(20)     | nullable                                   |                    |
| profile_image           | varchar(255)    | nullable                                   |                    |
| status                  | varchar(20)     | default: 'active'                          |                    |
| metadata                | json            | nullable                                   |                    |
| created_at              | timestamp       |                                            |                    |
| updated_at              | timestamp       |                                            |                    |
| deleted_at              | timestamp       | nullable                                   |                    |
| deleted_by              | bigint unsigned | nullable                                   |                    |

**Indexes**: company_id, department_id, position_id, employee_code, email, phone, status, hire_date, [first_name, last_name]  
**Unique**: [company_id, employee_code], [company_id, email]

#### 2.2. work_shifts

| Column         | Type            | Properties                  | Description       |
| -------------- | --------------- | --------------------------- | ----------------- |
| id             | bigint unsigned | primary key, auto-increment |                   |
| company_id     | bigint unsigned | foreign key -> companies.id | onDelete: cascade |
| name           | varchar(255)    | not null                    |                   |
| start_time     | time            | not null                    |                   |
| end_time       | time            | not null                    |                   |
| break_start    | time            | nullable                    |                   |
| break_end      | time            | nullable                    |                   |
| is_night_shift | boolean         | default: false              |                   |
| is_active      | boolean         | default: true               |                   |
| metadata       | json            | nullable                    |                   |
| created_at     | timestamp       |                             |                   |
| updated_at     | timestamp       |                             |                   |
| deleted_at     | timestamp       | nullable                    |                   |

**Indexes**: company_id, name, is_active, is_night_shift  
**Unique**: [company_id, name]

#### 2.3. employee_work_shifts

| Column         | Type            | Properties                    | Description       |
| -------------- | --------------- | ----------------------------- | ----------------- |
| id             | bigint unsigned | primary key, auto-increment   |                   |
| employee_id    | bigint unsigned | foreign key -> employees.id   | onDelete: cascade |
| work_shift_id  | bigint unsigned | foreign key -> work_shifts.id | onDelete: cascade |
| effective_date | date            | not null                      |                   |
| end_date       | date            | nullable                      |                   |
| is_current     | boolean         | default: true                 |                   |
| created_at     | timestamp       |                               |                   |
| updated_at     | timestamp       |                               |                   |

**Indexes**: employee_id, work_shift_id, effective_date, is_current  
**Unique**: [employee_id, work_shift_id, effective_date]

#### 2.4. attendances

| Column           | Type            | Properties                  | Description       |
| ---------------- | --------------- | --------------------------- | ----------------- |
| id               | bigint unsigned | primary key, auto-increment |                   |
| company_id       | bigint unsigned | foreign key -> companies.id | onDelete: cascade |
| employee_id      | bigint unsigned | foreign key -> employees.id | onDelete: cascade |
| date             | date            | not null                    |                   |
| clock_in         | time            | nullable                    |                   |
| clock_out        | time            | nullable                    |                   |
| late_minutes     | int             | default: 0                  |                   |
| overtime_minutes | int             | default: 0                  |                   |
| status           | varchar(20)     | default: 'present'          |                   |
| notes            | text            | nullable                    |                   |
| metadata         | json            | nullable                    |                   |
| created_at       | timestamp       |                             |                   |
| updated_at       | timestamp       |                             |                   |

**Indexes**: company_id, employee_id, date, status  
**Unique**: [employee_id, date]

#### 2.5. leaves

| Column        | Type            | Properties                    | Description                 |
| ------------- | --------------- | ----------------------------- | --------------------------- |
| id            | bigint unsigned | primary key, auto-increment   |                             |
| company_id    | bigint unsigned | foreign key -> companies.id   | onDelete: cascade           |
| employee_id   | bigint unsigned | foreign key -> employees.id   | onDelete: cascade           |
| leave_type_id | bigint unsigned | foreign key -> leave_types.id | onDelete: restrict          |
| start_date    | date            | not null                      |                             |
| end_date      | date            | not null                      |                             |
| days          | decimal(5,2)    | not null                      |                             |
| reason        | text            | nullable                      |                             |
| status        | varchar(20)     | default: 'pending'            | pending, approved, rejected |
| approved_by   | bigint unsigned | foreign key -> users.id       |                             |
| approved_at   | timestamp       | nullable                      |                             |
| metadata      | json            | nullable                      |                             |
| created_at    | timestamp       |                               |                             |
| updated_at    | timestamp       |                               |                             |

**Indexes**: company_id, employee_id, leave_type_id, start_date, end_date, status, approved_by

### 3. Sales Domain

#### 3.1. customers

| Column           | Type            | Properties                  | Description                   |
| ---------------- | --------------- | --------------------------- | ----------------------------- |
| id               | bigint unsigned | primary key, auto-increment |                               |
| uuid             | ulid            | unique                      |                               |
| company_id       | bigint unsigned | foreign key -> companies.id | onDelete: cascade             |
| code             | varchar(50)     | nullable                    |                               |
| name             | varchar(255)    | not null                    |                               |
| contact_person   | varchar(255)    | nullable                    |                               |
| email            | varchar(255)    | nullable                    |                               |
| phone            | varchar(20)     | nullable                    |                               |
| address          | text            | nullable                    |                               |
| tax_id           | varchar(50)     | nullable                    |                               |
| credit_limit     | decimal(15,2)   | nullable                    |                               |
| payment_terms    | int             | nullable                    |                               |
| discount_percent | decimal(5,2)    | default: 0                  |                               |
| status           | varchar(20)     | default: 'active'           | active, inactive, blacklisted |
| notes            | text            | nullable                    |                               |
| metadata         | json            | nullable                    |                               |
| created_at       | timestamp       |                             |                               |
| updated_at       | timestamp       |                             |                               |
| deleted_at       | timestamp       | nullable                    |                               |

**Indexes**: company_id, code, name, email, phone, status, tax_id  
**Unique**: [company_id, code], [company_id, email]

#### 3.2. quotations

| Column                     | Type            | Properties                                      | Description                              |
| -------------------------- | --------------- | ----------------------------------------------- | ---------------------------------------- |
| id                         | bigint unsigned | primary key, auto-increment                     |                                          |
| uuid                       | ulid            | unique                                          |                                          |
| company_id                 | bigint unsigned | foreign key -> companies.id                     | onDelete: cascade                        |
| customer_id                | bigint unsigned | foreign key -> customers.id                     | onDelete: restrict                       |
| quotation_number           | varchar(50)     | not null                                        |                                          |
| reference                  | varchar(100)    | nullable                                        |                                          |
| issue_date                 | date            | not null                                        |                                          |
| expiry_date                | date            | not null                                        |                                          |
| status                     | varchar(20)     | default: 'draft'                                | draft, sent, accepted, rejected, expired |
| currency_code              | varchar(3)      | default: 'THB'                                  |                                          |
| exchange_rate              | decimal(15,5)   | default: 1.00000                                |                                          |
| subtotal                   | decimal(15,2)   | not null                                        |                                          |
| tax_amount                 | decimal(15,2)   | default: 0                                      |                                          |
| discount_type              | varchar(10)     | default: 'percentage'                           | percentage, fixed                        |
| discount_percentage        | decimal(5,2)    | default: 0                                      |                                          |
| discount_amount            | decimal(15,2)   | default: 0                                      |                                          |
| total                      | decimal(15,2)   | not null                                        |                                          |
| terms_and_conditions       | text            | nullable                                        |                                          |
| notes                      | text            | nullable                                        |                                          |
| created_by                 | bigint unsigned | foreign key -> users.id                         | onDelete: restrict                       |
| metadata                   | json            | nullable                                        |                                          |
| created_at                 | timestamp       |                                                 |                                          |
| updated_at                 | timestamp       |                                                 |                                          |
| deleted_at                 | timestamp       | nullable                                        |                                          |
| deleted_by                 | bigint unsigned | nullable                                        |                                          |
| last_generated_document_id | bigint unsigned | nullable, foreign key -> generated_documents.id | onDelete: set null                       |
| last_pdf_generated_at      | timestamp       | nullable                                        |                                          |
| needs_pdf_regeneration     | boolean         | default: false                                  |                                          |

**Indexes**: company_id, customer_id, quotation_number, issue_date, expiry_date, status, created_by  
**Unique**: [company_id, quotation_number]

#### 3.3. quotation_items

| Column              | Type            | Properties                        | Description              |
| ------------------- | --------------- | --------------------------------- | ------------------------ |
| id                  | bigint unsigned | primary key, auto-increment       |                          |
| quotation_id        | bigint unsigned | foreign key -> quotations.id      | onDelete: cascade        |
| product_id          | bigint unsigned | foreign key -> products.id        | onDelete: restrict       |
| name                | varchar(255)    | not null                          | Product name at creation |
| description         | text            | nullable                          |                          |
| quantity            | decimal(15,2)   | not null                          |                          |
| unit_id             | bigint unsigned | foreign key -> units.id           | onDelete: restrict       |
| unit_price          | decimal(15,2)   | not null                          |                          |
| tax_id              | bigint unsigned | nullable, foreign key -> taxes.id | onDelete: restrict       |
| tax_percentage      | decimal(5,2)    | default: 0                        |                          |
| discount_type       | varchar(10)     | default: 'percentage'             | percentage, fixed        |
| discount_percentage | decimal(5,2)    | default: 0                        |                          |
| discount_amount     | decimal(15,2)   | default: 0                        |                          |
| subtotal            | decimal(15,2)   | not null                          |                          |
| tax_amount          | decimal(15,2)   | default: 0                        |                          |
| total               | decimal(15,2)   | not null                          |                          |
| sort_order          | int             | default: 0                        |                          |
| metadata            | json            | nullable                          |                          |
| created_at          | timestamp       |                                   |                          |
| updated_at          | timestamp       |                                   |                          |

**Indexes**: quotation_id, product_id, tax_id, unit_id

#### 3.4. orders

| Column                     | Type            | Properties                                      | Description                               |
| -------------------------- | --------------- | ----------------------------------------------- | ----------------------------------------- |
| id                         | bigint unsigned | primary key, auto-increment                     |                                           |
| uuid                       | ulid            | unique                                          |                                           |
| company_id                 | bigint unsigned | foreign key -> companies.id                     | onDelete: cascade                         |
| customer_id                | bigint unsigned | foreign key -> customers.id                     | onDelete: restrict                        |
| quotation_id               | bigint unsigned | nullable, foreign key -> quotations.id          | onDelete: set null                        |
| order_number               | varchar(50)     | not null                                        |                                           |
| reference                  | varchar(100)    | nullable                                        |                                           |
| order_date                 | date            | not null                                        |                                           |
| shipping_date              | date            | nullable                                        |                                           |
| due_date                   | date            | nullable                                        |                                           |
| status                     | varchar(20)     | default: 'pending'                              | pending, processing, completed, cancelled |
| shipping_status            | varchar(20)     | default: 'pending'                              | pending, partial, shipped, delivered      |
| currency_code              | varchar(3)      | default: 'THB'                                  |                                           |
| exchange_rate              | decimal(15,5)   | default: 1.00000                                |                                           |
| subtotal                   | decimal(15,2)   | not null                                        |                                           |
| tax_amount                 | decimal(15,2)   | default: 0                                      |                                           |
| shipping_amount            | decimal(15,2)   | default: 0                                      |                                           |
| discount_type              | varchar(10)     | default: 'percentage'                           | percentage, fixed                         |
| discount_percentage        | decimal(5,2)    | default: 0                                      |                                           |
| discount_amount            | decimal(15,2)   | default: 0                                      |                                           |
| total                      | decimal(15,2)   | not null                                        |                                           |
| notes                      | text            | nullable                                        |                                           |
| terms_and_conditions       | text            | nullable                                        |                                           |
| created_by                 | bigint unsigned | foreign key -> users.id                         | onDelete: restrict                        |
| metadata                   | json            | nullable                                        |                                           |
| created_at                 | timestamp       |                                                 |                                           |
| updated_at                 | timestamp       |                                                 |                                           |
| deleted_at                 | timestamp       | nullable                                        |                                           |
| deleted_by                 | bigint unsigned | nullable                                        |                                           |
| last_generated_document_id | bigint unsigned | nullable, foreign key -> generated_documents.id | onDelete: set null                        |
| last_pdf_generated_at      | timestamp       | nullable                                        |                                           |
| needs_pdf_regeneration     | boolean         | default: false                                  |                                           |

**Indexes**: company_id, customer_id, quotation_id, order_number, order_date, due_date, status, shipping_status, created_by  
**Unique**: [company_id, order_number]

#### 3.5. order_items

| Column              | Type            | Properties                        | Description                    |
| ------------------- | --------------- | --------------------------------- | ------------------------------ |
| id                  | bigint unsigned | primary key, auto-increment       |                                |
| order_id            | bigint unsigned | foreign key -> orders.id          | onDelete: cascade              |
| product_id          | bigint unsigned | foreign key -> products.id        | onDelete: restrict             |
| name                | varchar(255)    | not null                          | Product name at creation       |
| description         | text            | nullable                          |                                |
| quantity            | decimal(15,2)   | not null                          |                                |
| unit_id             | bigint unsigned | foreign key -> units.id           | onDelete: restrict             |
| unit_price          | decimal(15,2)   | not null                          |                                |
| tax_id              | bigint unsigned | nullable, foreign key -> taxes.id | onDelete: restrict             |
| tax_percentage      | decimal(5,2)    | default: 0                        |                                |
| discount_type       | varchar(10)     | default: 'percentage'             | percentage, fixed              |
| discount_percentage | decimal(5,2)    | default: 0                        |                                |
| discount_amount     | decimal(15,2)   | default: 0                        |                                |
| subtotal            | decimal(15,2)   | not null                          |                                |
| tax_amount          | decimal(15,2)   | default: 0                        |                                |
| total               | decimal(15,2)   | not null                          |                                |
| status              | varchar(20)     | default: 'pending'                | pending, processing, delivered |
| sort_order          | int             | default: 0                        |                                |
| metadata            | json            | nullable                          |                                |
| created_at          | timestamp       |                                   |                                |
| updated_at          | timestamp       |                                   |                                |

**Indexes**: order_id, product_id, tax_id, unit_id, status

#### 3.6. invoices

| Column                     | Type            | Properties                                      | Description                                   |
| -------------------------- | --------------- | ----------------------------------------------- | --------------------------------------------- |
| id                         | bigint unsigned | primary key, auto-increment                     |                                               |
| uuid                       | ulid            | unique                                          |                                               |
| company_id                 | bigint unsigned | foreign key -> companies.id                     | onDelete: cascade                             |
| customer_id                | bigint unsigned | foreign key -> customers.id                     | onDelete: restrict                            |
| order_id                   | bigint unsigned | nullable, foreign key -> orders.id              | onDelete: set null                            |
| quotation_id               | bigint unsigned | nullable, foreign key -> quotations.id          | onDelete: set null                            |
| invoice_number             | varchar(50)     | not null                                        |                                               |
| reference                  | varchar(100)    | nullable                                        |                                               |
| issue_date                 | date            | not null                                        |                                               |
| due_date                   | date            | not null                                        |                                               |
| status                     | varchar(20)     | default: 'unpaid'                               | draft, sent, unpaid, partial, paid, cancelled |
| payment_status             | varchar(20)     | default: 'pending'                              | pending, partial, paid, overdue               |
| currency_code              | varchar(3)      | default: 'THB'                                  |                                               |
| exchange_rate              | decimal(15,5)   | default: 1.00000                                |                                               |
| subtotal                   | decimal(15,2)   | not null                                        |                                               |
| tax_amount                 | decimal(15,2)   | default: 0                                      |                                               |
| shipping_amount            | decimal(15,2)   | default: 0                                      |                                               |
| discount_type              | varchar(10)     | default: 'percentage'                           | percentage, fixed                             |
| discount_percentage        | decimal(5,2)    | default: 0                                      |                                               |
| discount_amount            | decimal(15,2)   | default: 0                                      |                                               |
| total                      | decimal(15,2)   | not null                                        |                                               |
| amount_paid                | decimal(15,2)   | default: 0                                      |                                               |
| amount_due                 | decimal(15,2)   | not null                                        |                                               |
| terms_and_conditions       | text            | nullable                                        |                                               |
| notes                      | text            | nullable                                        |                                               |
| created_by                 | bigint unsigned | foreign key -> users.id                         | onDelete: restrict                            |
| metadata                   | json            | nullable                                        |                                               |
| created_at                 | timestamp       |                                                 |                                               |
| updated_at                 | timestamp       |                                                 |                                               |
| deleted_at                 | timestamp       | nullable                                        |                                               |
| deleted_by                 | bigint unsigned | nullable                                        |                                               |
| last_generated_document_id | bigint unsigned | nullable, foreign key -> generated_documents.id | onDelete: set null                            |
| last_pdf_generated_at      | timestamp       | nullable                                        |                                               |
| needs_pdf_regeneration     | boolean         | default: false                                  |                                               |

**Indexes**: company_id, customer_id, order_id, quotation_id, invoice_number, issue_date, due_date, status, payment_status, created_by  
**Unique**: [company_id, invoice_number]

#### 3.7. invoice_items

| Column              | Type            | Properties                              | Description              |
| ------------------- | --------------- | --------------------------------------- | ------------------------ |
| id                  | bigint unsigned | primary key, auto-increment             |                          |
| invoice_id          | bigint unsigned | foreign key -> invoices.id              | onDelete: cascade        |
| order_item_id       | bigint unsigned | nullable, foreign key -> order_items.id | onDelete: set null       |
| product_id          | bigint unsigned | foreign key -> products.id              | onDelete: restrict       |
| name                | varchar(255)    | not null                                | Product name at creation |
| description         | text            | nullable                                |                          |
| quantity            | decimal(15,2)   | not null                                |                          |
| unit_id             | bigint unsigned | foreign key -> units.id                 | onDelete: restrict       |
| unit_price          | decimal(15,2)   | not null                                |                          |
| tax_id              | bigint unsigned | nullable, foreign key -> taxes.id       | onDelete: restrict       |
| tax_percentage      | decimal(5,2)    | default: 0                              |                          |
| discount_type       | varchar(10)     | default: 'percentage'                   | percentage, fixed        |
| discount_percentage | decimal(5,2)    | default: 0                              |                          |
| discount_amount     | decimal(15,2)   | default: 0                              |                          |
| subtotal            | decimal(15,2)   | not null                                |                          |
| tax_amount          | decimal(15,2)   | default: 0                              |                          |
| total               | decimal(15,2)   | not null                                |                          |
| sort_order          | int             | default: 0                              |                          |
| metadata            | json            | nullable                                |                          |
| created_at          | timestamp       |                                         |                          |
| updated_at          | timestamp       |                                         |                          |

**Indexes**: invoice_id, order_item_id, product_id, tax_id, unit_id

#### 3.8. receipts

| Column                     | Type            | Properties                                      | Description                 |
| -------------------------- | --------------- | ----------------------------------------------- | --------------------------- |
| id                         | bigint unsigned | primary key, auto-increment                     |                             |
| uuid                       | ulid            | unique                                          |                             |
| company_id                 | bigint unsigned | foreign key -> companies.id                     | onDelete: cascade           |
| customer_id                | bigint unsigned | foreign key -> customers.id                     | onDelete: restrict          |
| receipt_number             | varchar(50)     | not null                                        |                             |
| reference                  | varchar(100)    | nullable                                        |                             |
| receipt_date               | date            | not null                                        |                             |
| status                     | varchar(20)     | default: 'completed'                            | draft, completed, cancelled |
| currency_code              | varchar(3)      | default: 'THB'                                  |                             |
| exchange_rate              | decimal(15,5)   | default: 1.00000                                |                             |
| subtotal                   | decimal(15,2)   | not null                                        |                             |
| tax_amount                 | decimal(15,2)   | default: 0                                      |                             |
| total                      | decimal(15,2)   | not null                                        |                             |
| notes                      | text            | nullable                                        |                             |
| created_by                 | bigint unsigned | foreign key -> users.id                         | onDelete: restrict          |
| metadata                   | json            | nullable                                        |                             |
| created_at                 | timestamp       |                                                 |                             |
| updated_at                 | timestamp       |                                                 |                             |
| deleted_at                 | timestamp       | nullable                                        |                             |
| deleted_by                 | bigint unsigned | nullable                                        |                             |
| last_generated_document_id | bigint unsigned | nullable, foreign key -> generated_documents.id | onDelete: set null          |
| last_pdf_generated_at      | timestamp       | nullable                                        |                             |
| needs_pdf_regeneration     | boolean         | default: false                                  |                             |

**Indexes**: company_id, customer_id, receipt_number, receipt_date, status, created_by  
**Unique**: [company_id, receipt_number]

#### 3.9. receipt_items

| Column      | Type            | Properties                  | Description        |
| ----------- | --------------- | --------------------------- | ------------------ |
| id          | bigint unsigned | primary key, auto-increment |                    |
| receipt_id  | bigint unsigned | foreign key -> receipts.id  | onDelete: cascade  |
| invoice_id  | bigint unsigned | foreign key -> invoices.id  | onDelete: restrict |
| amount      | decimal(15,2)   | not null                    |                    |
| description | text            | nullable                    |                    |
| sort_order  | int             | default: 0                  |                    |
| metadata    | json            | nullable                    |                    |
| created_at  | timestamp       |                             |                    |
| updated_at  | timestamp       |                             |                    |

**Indexes**: receipt_id, invoice_id

#### 3.10. payments

| Column         | Type            | Properties                           | Description                           |
| -------------- | --------------- | ------------------------------------ | ------------------------------------- |
| id             | bigint unsigned | primary key, auto-increment          |                                       |
| uuid           | ulid            | unique                               |                                       |
| company_id     | bigint unsigned | foreign key -> companies.id          | onDelete: cascade                     |
| customer_id    | bigint unsigned | foreign key -> customers.id          | onDelete: restrict                    |
| receipt_id     | bigint unsigned | nullable, foreign key -> receipts.id | onDelete: set null                    |
| invoice_id     | bigint unsigned | foreign key -> invoices.id           | onDelete: restrict                    |
| payment_number | varchar(50)     | not null                             |                                       |
| amount         | decimal(15,2)   | not null                             |                                       |
| payment_date   | date            | not null                             |                                       |
| payment_method | varchar(50)     | not null                             | cash, bank_transfer, credit_card, etc |
| status         | varchar(20)     | default: 'completed'                 | pending, completed, failed, refunded  |
| reference      | varchar(100)    | nullable                             |                                       |
| transaction_id | varchar(100)    | nullable                             |                                       |
| notes          | text            | nullable                             |                                       |
| created_by     | bigint unsigned | foreign key -> users.id              | onDelete: restrict                    |
| metadata       | json            | nullable                             |                                       |
| created_at     | timestamp       |                                      |                                       |
| updated_at     | timestamp       |                                      |                                       |
| deleted_at     | timestamp       | nullable                             |                                       |
| deleted_by     | bigint unsigned | nullable                             |                                       |

**Indexes**: company_id, customer_id, receipt_id, invoice_id, payment_number, payment_date, payment_method, status, created_by  
**Unique**: [company_id, payment_number]

#### 3.11. payment_methods

| Column              | Type            | Properties                  | Description                |
| ------------------- | --------------- | --------------------------- | -------------------------- |
| id                  | bigint unsigned | primary key, auto-increment |                            |
| company_id          | bigint unsigned | foreign key -> companies.id | onDelete: cascade          |
| name                | varchar(255)    | not null                    |                            |
| code                | varchar(50)     | not null                    |                            |
| description         | text            | nullable                    |                            |
| instructions        | text            | nullable                    |                            |
| is_active           | boolean         | default: true               |                            |
| is_online           | boolean         | default: false              |                            |
| requires_processing | boolean         | default: false              |                            |
| config              | json            | nullable                    | Configuration for gateways |
| metadata            | json            | nullable                    |                            |
| created_at          | timestamp       |                             |                            |
| updated_at          | timestamp       |                             |                            |

**Indexes**: company_id, code, is_active, is_online  
**Unique**: [company_id, code]

#### 3.12. delivery_notes

| Column                     | Type            | Properties                                      | Description                            |
| -------------------------- | --------------- | ----------------------------------------------- | -------------------------------------- |
| id                         | bigint unsigned | primary key, auto-increment                     |                                        |
| uuid                       | ulid            | unique                                          |                                        |
| company_id                 | bigint unsigned | foreign key -> companies.id                     | onDelete: cascade                      |
| customer_id                | bigint unsigned | foreign key -> customers.id                     | onDelete: restrict                     |
| order_id                   | bigint unsigned | foreign key -> orders.id                        | onDelete: restrict                     |
| delivery_number            | varchar(50)     | not null                                        |                                        |
| delivery_date              | date            | not null                                        |                                        |
| driver_name                | varchar(255)    | nullable                                        |                                        |
| vehicle_info               | varchar(255)    | nullable                                        |                                        |
| status                     | varchar(20)     | default: 'draft'                                | draft, dispatched, delivered, returned |
| notes                      | text            | nullable                                        |                                        |
| received_by                | varchar(255)    | nullable                                        |                                        |
| received_at                | timestamp       | nullable                                        |                                        |
| created_by                 | bigint unsigned | foreign key -> users.id                         | onDelete: restrict                     |
| metadata                   | json            | nullable                                        |                                        |
| created_at                 | timestamp       |                                                 |                                        |
| updated_at                 | timestamp       |                                                 |                                        |
| deleted_at                 | timestamp       | nullable                                        |                                        |
| deleted_by                 | bigint unsigned | nullable                                        |                                        |
| last_generated_document_id | bigint unsigned | nullable, foreign key -> generated_documents.id | onDelete: set null                     |
| last_pdf_generated_at      | timestamp       | nullable                                        |                                        |
| needs_pdf_regeneration     | boolean         | default: false                                  |                                        |

**Indexes**: company_id, customer_id, order_id, delivery_number, delivery_date, status, created_by  
**Unique**: [company_id, delivery_number]

#### 3.13. delivery_items

| Column           | Type            | Properties                       | Description                  |
| ---------------- | --------------- | -------------------------------- | ---------------------------- |
| id               | bigint unsigned | primary key, auto-increment      |                              |
| delivery_note_id | bigint unsigned | foreign key -> delivery_notes.id | onDelete: cascade            |
| order_item_id    | bigint unsigned | foreign key -> order_items.id    | onDelete: restrict           |
| product_id       | bigint unsigned | foreign key -> products.id       | onDelete: restrict           |
| name             | varchar(255)    | not null                         | Product name at creation     |
| quantity         | decimal(15,2)   | not null                         |                              |
| unit_id          | bigint unsigned | foreign key -> units.id          | onDelete: restrict           |
| status           | varchar(20)     | default: 'pending'               | pending, delivered, returned |
| notes            | text            | nullable                         |                              |
| metadata         | json            | nullable                         |                              |
| created_at       | timestamp       |                                  |                              |
| updated_at       | timestamp       |                                  |                              |

**Indexes**: delivery_note_id, order_item_id, product_id, unit_id, status

### 4. Inventory Domain

#### 4.1. products

| Column        | Type            | Properties                                     | Description                       |
| ------------- | --------------- | ---------------------------------------------- | --------------------------------- |
| id            | bigint unsigned | primary key, auto-increment                    |                                   |
| uuid          | ulid            | unique                                         |                                   |
| company_id    | bigint unsigned | foreign key -> companies.id                    | onDelete: cascade                 |
| category_id   | bigint unsigned | nullable, foreign key -> product_categories.id | onDelete: set null                |
| code          | varchar(50)     | nullable                                       |                                   |
| name          | varchar(255)    | not null                                       |                                   |
| description   | text            | nullable                                       |                                   |
| unit_id       | bigint unsigned | nullable, foreign key -> units.id              | onDelete: set null                |
| selling_price | decimal(15,2)   | not null                                       |                                   |
| buying_price  | decimal(15,2)   | nullable                                       |                                   |
| tax_id        | bigint unsigned | nullable, foreign key -> taxes.id              | onDelete: set null                |
| min_stock     | decimal(15,2)   | default: 0                                     |                                   |
| opening_stock | decimal(15,2)   | default: 0                                     |                                   |
| current_stock | decimal(15,2)   | default: 0                                     | Computed based on stock movements |
| image         | varchar(255)    | nullable                                       |                                   |
| is_active     | boolean         | default: true                                  |                                   |
| barcode       | varchar(100)    | nullable                                       |                                   |
| metadata      | json            | nullable                                       |                                   |
| created_at    | timestamp       |                                                |                                   |
| updated_at    | timestamp       |                                                |                                   |
| deleted_at    | timestamp       | nullable                                       |                                   |

**Indexes**: company_id, category_id, name, code, is_active, barcode  
**Unique**: [company_id, code], [company_id, barcode]  
**Full Text Index**: [name, description]

## รายละเอียดตารางเพิ่มเติม

### 1. ตารางหน่วยวัด (units)

| Column            | Type            | Properties                  | Description                           |
| ----------------- | --------------- | --------------------------- | ------------------------------------- |
| id                | bigint unsigned | primary key, auto-increment |                                       |
| company_id        | bigint unsigned | foreign key -> companies.id | onDelete: cascade                     |
| name              | varchar(50)     | not null                    | ชื่อหน่วย (เช่น ชิ้น, กล่อง)          |
| code              | varchar(10)     | not null                    | รหัสหน่วย (เช่น PCS, BOX)             |
| symbol            | varchar(10)     | nullable                    | สัญลักษณ์ (เช่น kg, m)                |
| base_unit_id      | bigint unsigned | nullable, FK -> units.id    | หน่วยพื้นฐาน (เช่น 1 กล่อง = 12 ชิ้น) |
| conversion_factor | decimal(15,5)   | default: 1.00000            | ตัวคูณสำหรับการแปลงหน่วย              |
| is_active         | boolean         | default: true               |                                       |
| created_at        | timestamp       |                             |                                       |
| updated_at        | timestamp       |                             |                                       |
| deleted_at        | timestamp       | nullable                    |                                       |

**Indexes**: company_id, name, code, is_active  
**Unique**: [company_id, code]

### 2. ตารางภาษี (taxes)

| Column         | Type            | Properties                  | Description                   |
| -------------- | --------------- | --------------------------- | ----------------------------- |
| id             | bigint unsigned | primary key, auto-increment |                               |
| company_id     | bigint unsigned | foreign key -> companies.id | onDelete: cascade             |
| name           | varchar(100)    | not null                    | ชื่อภาษี (เช่น VAT 7%)        |
| code           | varchar(20)     | not null                    | รหัสภาษี (เช่น VAT7)          |
| rate           | decimal(5,2)    | not null                    | อัตราภาษี (เช่น 7.00)         |
| type           | varchar(20)     | default: 'percentage'       | percentage, fixed             |
| is_compound    | boolean         | default: false              | คำนวณทับภาษีอื่นหรือไม่       |
| apply_to       | varchar(50)     | default: 'all'              | all, specific_items           |
| is_recoverable | boolean         | default: true               | เป็นภาษีที่เรียกคืนได้หรือไม่ |
| is_active      | boolean         | default: true               |                               |
| metadata       | json            | nullable                    |                               |
| created_at     | timestamp       |                             |                               |
| updated_at     | timestamp       |                             |                               |
| deleted_at     | timestamp       | nullable                    |                               |

**Indexes**: company_id, name, code, rate, is_active  
**Unique**: [company_id, code]

### 3. ตารางประเภทการลา (leave_types) - รายละเอียดเพิ่มเติม

| Column               | Type            | Properties                  | Description                              |
| -------------------- | --------------- | --------------------------- | ---------------------------------------- |
| id                   | bigint unsigned | primary key, auto-increment |                                          |
| company_id           | bigint unsigned | foreign key -> companies.id | onDelete: cascade                        |
| name                 | varchar(100)    | not null                    | ชื่อประเภทการลา (เช่น ลาป่วย, ลาพักร้อน) |
| code                 | varchar(20)     | nullable                    | รหัสประเภทการลา (เช่น SICK, VAC)         |
| description          | text            | nullable                    |                                          |
| color                | varchar(7)      | default: '#cccccc'          | สีที่ใช้แสดงในปฏิทิน (Hex code)          |
| annual_allowance     | decimal(8,2)    | default: 0                  | จำนวนวันต่อปี (0 = ไม่จำกัด)             |
| max_consecutive_days | int             | default: 0                  | จำนวนวันติดต่อสูงสุด (0 = ไม่จำกัด)      |
| min_advance_notice   | int             | default: 0                  | จำนวนวันล่วงหน้าขั้นต่ำ                  |
| requires_approval    | boolean         | default: true               | ต้องได้รับการอนุมัติหรือไม่              |
| requires_document    | boolean         | default: false              | ต้องแนบเอกสารหรือไม่                     |
| is_paid              | boolean         | default: true               | เป็นวันลาแบบได้รับค่าจ้างหรือไม่         |
| count_as_work_day    | boolean         | default: false              | นับเป็นวันทำงานหรือไม่                   |
| is_active            | boolean         | default: true               |                                          |
| metadata             | json            | nullable                    |                                          |
| created_at           | timestamp       |                             |                                          |
| updated_at           | timestamp       |                             |                                          |
| deleted_at           | timestamp       | nullable                    |                                          |

**Indexes**: company_id, name, code, is_active, is_paid  
**Unique**: [company_id, code]

### 4. ตารางการตั้งค่าระบบ (settings)

| Column      | Type            | Properties                            | Description                          |
| ----------- | --------------- | ------------------------------------- | ------------------------------------ |
| id          | bigint unsigned | primary key, auto-increment           |                                      |
| company_id  | bigint unsigned | nullable, foreign key -> companies.id | onDelete: cascade                    |
| group       | varchar(50)     | not null                              | กลุ่มการตั้งค่า (เช่น system, email) |
| key         | varchar(100)    | not null                              | คีย์สำหรับการตั้งค่า                 |
| value       | text            | nullable                              | ค่าของการตั้งค่า                     |
| type        | varchar(20)     | default: 'string'                     | string, boolean, integer, json, etc. |
| is_public   | boolean         | default: false                        | เป็นการตั้งค่าสาธารณะหรือไม่         |
| description | text            | nullable                              | คำอธิบายการตั้งค่า                   |
| sort_order  | int             | default: 0                            | ลำดับการแสดงผล                       |
| created_at  | timestamp       |                                       |                                      |
| updated_at  | timestamp       |                                       |                                      |

**Indexes**: company_id, [group, key], is_public  
**Unique**: [company_id, group, key]

### 5. ตารางผู้ใช้งาน (users) - รายละเอียดเพิ่มเติม

| Column                    | Type            | Properties                  | Description                        |
| ------------------------- | --------------- | --------------------------- | ---------------------------------- |
| id                        | bigint unsigned | primary key, auto-increment |                                    |
| uuid                      | ulid            | unique                      |                                    |
| name                      | varchar(255)    | not null                    |                                    |
| email                     | varchar(255)    | unique, not null            |                                    |
| email_verified_at         | timestamp       | nullable                    | เวลาที่ยืนยันอีเมลแล้ว             |
| password                  | varchar(255)    | not null                    |                                    |
| two_factor_secret         | text            | nullable                    | สำหรับการยืนยันตัวตน 2 ขั้นตอน     |
| two_factor_recovery_codes | text            | nullable                    | รหัสสำรองสำหรับการยืนยัน 2 ขั้นตอน |
| remember_token            | varchar(100)    | nullable                    |                                    |
| is_active                 | boolean         | default: true               |                                    |
| is_system_admin           | boolean         | default: false              | เป็นผู้ดูแลระบบสูงสุดหรือไม่       |
| language                  | varchar(5)      | default: 'th'               | ภาษาที่ใช้งาน (th, en, etc.)       |
| timezone                  | varchar(50)     | default: 'Asia/Bangkok'     | เขตเวลาที่ใช้                      |
| last_login_at             | timestamp       | nullable                    | การเข้าสู่ระบบล่าสุด               |
| profile_photo_path        | varchar(2048)   | nullable                    | ที่อยู่รูปโปรไฟล์                  |
| settings                  | json            | nullable                    | การตั้งค่าส่วนตัว                  |
| created_at                | timestamp       |                             |                                    |
| updated_at                | timestamp       |                             |                                    |
| deleted_at                | timestamp       | nullable                    |                                    |

**Indexes**: email, is_active, is_system_admin, last_login_at

### 6. ตารางเหตุการณ์แบบตั้งเวลา (scheduled_events)

| Column           | Type            | Properties                            | Description                          |
| ---------------- | --------------- | ------------------------------------- | ------------------------------------ |
| id               | bigint unsigned | primary key, auto-increment           |                                      |
| company_id       | bigint unsigned | nullable, foreign key -> companies.id | onDelete: cascade                    |
| title            | varchar(255)    | not null                              | ชื่อเหตุการณ์                        |
| description      | text            | nullable                              |                                      |
| event_type       | varchar(50)     | not null                              | report, notification, invoice, etc.  |
| frequency        | varchar(20)     | not null                              | once, daily, weekly, monthly, yearly |
| start_date       | date            | not null                              | วันที่เริ่มต้น                       |
| end_date         | date            | nullable                              | วันที่สิ้นสุด (ถ้ามี)                |
| time             | time            | nullable                              | เวลาที่จะทำงาน                       |
| day_of_week      | varchar(10)     | nullable                              | วันในสัปดาห์ (1-7)                   |
| day_of_month     | int             | nullable                              | วันที่ในเดือน (1-31)                 |
| month            | int             | nullable                              | เดือน (1-12)                         |
| timezone         | varchar(50)     | default: 'Asia/Bangkok'               | เขตเวลา                              |
| is_active        | boolean         | default: true                         |                                      |
| last_run         | timestamp       | nullable                              | เวลาที่ทำงานล่าสุด                   |
| next_run         | timestamp       | nullable                              | เวลาที่จะทำงานถัดไป                  |
| action           | varchar(255)    | not null                              | Action class หรือ command ที่จะเรียก |
| parameters       | json            | nullable                              | พารามิเตอร์สำหรับ action             |
| output           | text            | nullable                              | ผลลัพธ์ล่าสุด                        |
| created_by       | bigint unsigned | nullable, foreign key -> users.id     | onDelete: set null                   |
| notifications_to | json            | nullable                              | ผู้ที่จะได้รับการแจ้งเตือน           |
| metadata         | json            | nullable                              |                                      |
| created_at       | timestamp       |                                       |                                      |
| updated_at       | timestamp       |                                       |                                      |

**Indexes**: company_id, event_type, frequency, is_active, next_run, created_by

### 7. ตารางหมวดหมู่สินค้า (product_categories)

| Column      | Type            | Properties                            | Description                       |
| ----------- | --------------- | ------------------------------------- | --------------------------------- |
| id          | bigint unsigned | primary key, auto-increment           |                                   |
| company_id  | bigint unsigned | foreign key -> companies.id           | onDelete: cascade                 |
| name        | varchar(255)    | not null                              | ชื่อหมวดหมู่                      |
| code        | varchar(30)     | nullable                              | รหัสหมวดหมู่                      |
| description | text            | nullable                              | รายละเอียดหมวดหมู่                |
| is_active   | boolean         | default: true                         |                                   |
| parent_id   | bigint unsigned | nullable, FK -> product_categories.id | หมวดหมู่หลัก (สำหรับหมวดหมู่ย่อย) |
| image       | varchar(255)    | nullable                              | รูปภาพประกอบหมวดหมู่              |
| sort_order  | int             | default: 0                            | ลำดับการแสดงผล                    |
| metadata    | json            | nullable                              | ข้อมูลเพิ่มเติม                   |
| created_at  | timestamp       |                                       |                                   |
| updated_at  | timestamp       |                                       |                                   |
| deleted_at  | timestamp       | nullable                              |                                   |

**Indexes**: company_id, parent_id, name, code, is_active  
**Unique**: [company_id, code]

### 8. ตารางสินค้า/บริการ (products) - รายละเอียดเพิ่มเติม

| Column        | Type            | Properties                            | Description                        |
| ------------- | --------------- | ------------------------------------- | ---------------------------------- |
| id            | bigint unsigned | primary key, auto-increment           |                                    |
| uuid          | ulid            | unique                                | Universal Unique ID (ULID)         |
| company_id    | bigint unsigned | foreign key -> companies.id           | onDelete: cascade                  |
| category_id   | bigint unsigned | nullable, FK -> product_categories.id | onDelete: set null                 |
| code          | varchar(50)     | nullable                              | รหัสสินค้า                         |
| sku           | varchar(50)     | nullable                              | Stock Keeping Unit                 |
| barcode       | varchar(100)    | nullable                              | บาร์โค้ดสินค้า                     |
| name          | varchar(255)    | not null                              | ชื่อสินค้า                         |
| description   | text            | nullable                              | รายละเอียด                         |
| unit_id       | bigint unsigned | nullable, FK -> units.id              | หน่วยสินค้า                        |
| tax_id        | bigint unsigned | nullable, FK -> taxes.id              | ภาษี                               |
| selling_price | decimal(15,2)   | not null                              | ราคาขาย                            |
| buying_price  | decimal(15,2)   | nullable                              | ราคาซื้อ/ต้นทุน                    |
| is_inventory  | boolean         | default: true                         | เป็นสินค้าคงคลังหรือไม่            |
| is_service    | boolean         | default: false                        | เป็นบริการหรือไม่                  |
| min_stock     | decimal(15,2)   | default: 0                            | สต๊อกขั้นต่ำ                       |
| opening_stock | decimal(15,2)   | default: 0                            | สต๊อกเริ่มต้น                      |
| current_stock | decimal(15,2)   | default: 0                            | สต๊อกปัจจุบัน (คำนวณจาก movements) |
| weight        | decimal(10,3)   | nullable                              | น้ำหนัก (kg)                       |
| length        | decimal(10,2)   | nullable                              | ความยาว (cm)                       |
| width         | decimal(10,2)   | nullable                              | ความกว้าง (cm)                     |
| height        | decimal(10,2)   | nullable                              | ความสูง (cm)                       |
| image         | varchar(255)    | nullable                              | รูปภาพหลัก                         |
| is_active     | boolean         | default: true                         | สถานะการใช้งาน                     |
| is_featured   | boolean         | default: false                        | แสดงเป็นสินค้าแนะนำ                |
| tags          | json            | nullable                              | แท็กสินค้า                         |
| attributes    | json            | nullable                              | คุณลักษณะเพิ่มเติม                 |
| metadata      | json            | nullable                              | ข้อมูลเพิ่มเติม                    |
| created_at    | timestamp       |                                       |                                    |
| updated_at    | timestamp       |                                       |                                    |
| deleted_at    | timestamp       | nullable                              |                                    |
| created_by    | bigint unsigned | nullable, FK -> users.id              | ผู้สร้าง                           |
| updated_by    | bigint unsigned | nullable, FK -> users.id              | ผู้แก้ไขล่าสุด                     |

**Indexes**: company_id, category_id, name, code, sku, barcode, is_active, is_inventory, is_service  
**Unique**: [company_id, code], [company_id, sku], [company_id, barcode]  
**Full Text Index**: [name, description]

### 9. ตารางการเคลื่อนไหวสินค้า (stock_movements)

| Column          | Type            | Properties                        | Description                         |
| --------------- | --------------- | --------------------------------- | ----------------------------------- |
| id              | bigint unsigned | primary key, auto-increment       |                                     |
| company_id      | bigint unsigned | foreign key -> companies.id       | onDelete: cascade                   |
| product_id      | bigint unsigned | foreign key -> products.id        | onDelete: cascade                   |
| reference_type  | varchar(50)     | not null                          | order, invoice, adjustment, etc     |
| reference_id    | bigint unsigned | not null                          | ID ของเอกสารอ้างอิง                 |
| quantity        | decimal(15,2)   | not null                          | จำนวนที่เปลี่ยนแปลง (+ เข้า, - ออก) |
| before_quantity | decimal(15,2)   | not null                          | จำนวนก่อนเปลี่ยนแปลง                |
| after_quantity  | decimal(15,2)   | not null                          | จำนวนหลังเปลี่ยนแปลง                |
| unit_id         | bigint unsigned | nullable, FK -> units.id          | หน่วยสินค้า                         |
| unit_cost       | decimal(15,2)   | nullable                          | ต้นทุนต่อหน่วย                      |
| note            | text            | nullable                          | บันทึกเพิ่มเติม                     |
| created_by      | bigint unsigned | nullable, foreign key -> users.id | ผู้บันทึกรายการ                     |
| created_at      | timestamp       |                                   |                                     |
| updated_at      | timestamp       |                                   |                                     |

**Indexes**: company_id, product_id, [reference_type, reference_id], created_at, created_by

### 10. ตารางวิธีการชำระเงิน (payment_methods) - รายละเอียดเพิ่มเติม

| Column                | Type            | Properties                  | Description                       |
| --------------------- | --------------- | --------------------------- | --------------------------------- |
| id                    | bigint unsigned | primary key, auto-increment |                                   |
| company_id            | bigint unsigned | foreign key -> companies.id | onDelete: cascade                 |
| name                  | varchar(100)    | not null                    | ชื่อวิธีการชำระเงิน               |
| code                  | varchar(30)     | not null                    | รหัสวิธีการชำระเงิน               |
| description           | text            | nullable                    | รายละเอียดเพิ่มเติม               |
| instructions          | text            | nullable                    | คำแนะนำสำหรับลูกค้า               |
| account_number        | varchar(50)     | nullable                    | เลขที่บัญชี (ถ้ามี)               |
| account_name          | varchar(100)    | nullable                    | ชื่อบัญชี (ถ้ามี)                 |
| bank_name             | varchar(100)    | nullable                    | ชื่อธนาคาร (ถ้ามี)                |
| bank_branch           | varchar(100)    | nullable                    | สาขาธนาคาร (ถ้ามี)                |
| is_online             | boolean         | default: false              | เป็นการชำระเงินออนไลน์หรือไม่     |
| requires_verification | boolean         | default: true               | ต้องตรวจสอบการชำระเงินก่อนหรือไม่ |
| gateway_code          | varchar(50)     | nullable                    | รหัสการเชื่อมต่อ payment gateway  |
| config                | json            | nullable                    | การตั้งค่าสำหรับ payment gateway  |
| is_active             | boolean         | default: true               |                                   |
| sort_order            | int             | default: 0                  | ลำดับการแสดงผล                    |
| metadata              | json            | nullable                    |                                   |
| created_at            | timestamp       |                             |                                   |
| updated_at            | timestamp       |                             |                                   |
| deleted_at            | timestamp       | nullable                    |                                   |

**Indexes**: company_id, code, is_active, is_online  
**Unique**: [company_id, code]

### 11. ตารางบทบาทผู้ใช้งาน (roles)

| Column         | Type            | Properties                   | Description                            |
| -------------- | --------------- | ---------------------------- | -------------------------------------- |
| id             | bigint unsigned | primary key, auto-increment  |                                        |
| company_id     | bigint unsigned | nullable, FK -> companies.id | onDelete: cascade (null = system role) |
| name           | varchar(100)    | not null                     | ชื่อบทบาท                              |
| guard_name     | varchar(100)    | default: 'web'               | guard name (web, api, etc)             |
| display_name   | varchar(100)    | nullable                     | ชื่อแสดงผลภาษาไทย                      |
| description    | text            | nullable                     | คำอธิบาย                               |
| is_system_role | boolean         | default: false               | เป็นบทบาทระดับระบบหรือไม่              |
| is_default     | boolean         | default: false               | เป็นบทบาทเริ่มต้นหรือไม่               |
| level          | int             | default: 0                   | ระดับความสำคัญ (สูงกว่า = มากกว่า)     |
| metadata       | json            | nullable                     |                                        |
| created_at     | timestamp       |                              |                                        |
| updated_at     | timestamp       |                              |                                        |

**Indexes**: company_id, name, guard_name, is_system_role, is_default, level  
**Unique**: [name, guard_name, company_id]

### 12. ตารางสิทธิ์การใช้งาน (permissions)

| Column       | Type            | Properties                  | Description                  |
| ------------ | --------------- | --------------------------- | ---------------------------- |
| id           | bigint unsigned | primary key, auto-increment |                              |
| name         | varchar(100)    | not null                    | ชื่อสิทธิ์                   |
| guard_name   | varchar(100)    | default: 'web'              | guard name (web, api, etc)   |
| display_name | varchar(100)    | nullable                    | ชื่อแสดงผลภาษาไทย            |
| description  | text            | nullable                    | คำอธิบาย                     |
| group        | varchar(50)     | nullable                    | กลุ่มของสิทธิ์               |
| is_core      | boolean         | default: false              | เป็นสิทธิ์หลักของระบบหรือไม่ |
| created_at   | timestamp       |                             |                              |
| updated_at   | timestamp       |                             |                              |

**Indexes**: name, guard_name, group, is_core  
**Unique**: [name, guard_name]

### 13. ความสัมพันธ์ระหว่างบทบาทและสิทธิ์ (role_has_permissions)

| Column        | Type            | Properties           | Description       |
| ------------- | --------------- | -------------------- | ----------------- |
| permission_id | bigint unsigned | FK -> permissions.id | onDelete: cascade |
| role_id       | bigint unsigned | FK -> roles.id       | onDelete: cascade |

**Primary Key**: [permission_id, role_id]  
**Indexes**: permission_id, role_id

### 14. ความสัมพันธ์ระหว่างผู้ใช้และบทบาท (model_has_roles)

| Column     | Type            | Properties     | Description                        |
| ---------- | --------------- | -------------- | ---------------------------------- |
| role_id    | bigint unsigned | FK -> roles.id | onDelete: cascade                  |
| model_type | varchar(255)    | not null       | โมเดลของผู้ใช้ (App\\Models\\User) |
| model_id   | bigint unsigned | not null       | ID ของผู้ใช้                       |

**Primary Key**: [role_id, model_id, model_type]  
**Indexes**: [model_id, model_type]

### 15. ความสัมพันธ์ระหว่างผู้ใช้และบริษัท (company_user)

| Column           | Type            | Properties                  | Description                        |
| ---------------- | --------------- | --------------------------- | ---------------------------------- |
| id               | bigint unsigned | primary key, auto-increment |                                    |
| company_id       | bigint unsigned | FK -> companies.id          | onDelete: cascade                  |
| user_id          | bigint unsigned | FK -> users.id              | onDelete: cascade                  |
| is_default       | boolean         | default: false              | เป็นบริษัทเริ่มต้นของผู้ใช้หรือไม่ |
| status           | varchar(20)     | default: 'active'           | active, suspended, pending         |
| role             | varchar(50)     | nullable                    | บทบาทในบริษัท (CEO, CFO, etc)      |
| invitation_token | varchar(100)    | nullable                    | โทเค็นคำเชิญ                       |
| invited_at       | timestamp       | nullable                    | วันเวลาที่เชิญ                     |
| accepted_at      | timestamp       | nullable                    | วันเวลาที่ยอมรับคำเชิญ             |
| created_at       | timestamp       |                             |                                    |
| updated_at       | timestamp       |                             |                                    |

**Indexes**: company_id, user_id, is_default, status  
**Unique**: [company_id, user_id]

## ตารางระบบ และ Audit

### 1. activity_logs

| Column         | Type            | Properties                            | Description                     |
| -------------- | --------------- | ------------------------------------- | ------------------------------- |
| id             | bigint unsigned | primary key, auto-increment           |                                 |
| user_id        | bigint unsigned | nullable, foreign key -> users.id     | onDelete: set null              |
| company_id     | bigint unsigned | nullable, foreign key -> companies.id | onDelete: cascade               |
| auditable_type | varchar(255)    | not null                              | Model class name                |
| auditable_id   | bigint unsigned | not null                              | Model ID                        |
| event          | varchar(50)     | not null                              | created, updated, deleted, etc. |
| old_values     | json            | nullable                              |                                 |
| new_values     | json            | nullable                              |                                 |
| url            | text            | nullable                              |                                 |
| ip_address     | varchar(45)     | nullable                              |                                 |
| user_agent     | text            | nullable                              |                                 |
| created_at     | timestamp       | nullable                              |                                 |

**Indexes**: user_id, company_id, [auditable_type, auditable_id], event, created_at

### 2. file_attachments

| Column            | Type            | Properties                        | Description        |
| ----------------- | --------------- | --------------------------------- | ------------------ |
| id                | bigint unsigned | primary key, auto-increment       |                    |
| company_id        | bigint unsigned | foreign key -> companies.id       | onDelete: cascade  |
| attachable_type   | varchar(255)    | not null                          | Model class name   |
| attachable_id     | bigint unsigned | not null                          | Model ID           |
| path              | varchar(255)    | not null                          | Storage path       |
| filename          | varchar(255)    | not null                          | Stored filename    |
| original_filename | varchar(255)    | not null                          | Original filename  |
| mime_type         | varchar(100)    | not null                          |                    |
| size              | int             | not null                          | In bytes           |
| disk              | varchar(50)     | default: 'local'                  |                    |
| created_by        | bigint unsigned | nullable, foreign key -> users.id | onDelete: set null |
| created_at        | timestamp       |                                   |                    |
| updated_at        | timestamp       |                                   |                    |

**Indexes**: company_id, [attachable_type, attachable_id], created_by, mime_type

### 3. translations

| Column            | Type            | Properties                  | Description       |
| ----------------- | --------------- | --------------------------- | ----------------- |
| id                | bigint unsigned | primary key, auto-increment |                   |
| company_id        | bigint unsigned | foreign key -> companies.id | onDelete: cascade |
| translatable_type | varchar(255)    | not null                    | Model class name  |
| translatable_id   | bigint unsigned | not null                    | Model ID          |
| locale            | varchar(10)     | not null                    |                   |
| field             | varchar(50)     | not null                    |                   |
| value             | text            | not null                    |                   |
| created_at        | timestamp       |                             |                   |
| updated_at        | timestamp       |                             |                   |

**Indexes**: company_id, [translatable_type, translatable_id], locale, field  
**Unique**: [translatable_type, translatable_id, locale, field]

## การจัดการเอกสาร PDF

### 1. document_templates

| Column      | Type            | Properties                  | Description                                |
| ----------- | --------------- | --------------------------- | ------------------------------------------ |
| id          | bigint unsigned | primary key, auto-increment |                                            |
| company_id  | bigint unsigned | foreign key -> companies.id | onDelete: cascade                          |
| name        | varchar(255)    | not null                    | ชื่อเทมเพลต                                |
| type        | varchar(50)     | not null                    | invoice, receipt, quotation, order, etc.   |
| layout      | json            | not null                    | เก็บโครงสร้าง layout ของเอกสาร             |
| header      | json            | nullable                    | เก็บข้อมูล header ของเอกสาร                |
| footer      | json            | nullable                    | เก็บข้อมูล footer ของเอกสาร                |
| css         | text            | nullable                    | CSS สำหรับ styling เอกสาร                  |
| orientation | varchar(10)     | default: 'portrait'         | portrait, landscape                        |
| paper_size  | varchar(10)     | default: 'a4'               | a4, letter, legal                          |
| is_default  | boolean         | default: false              | กำหนดเป็นเทมเพลตเริ่มต้นสำหรับประเภทเอกสาร |
| is_active   | boolean         | default: true               |                                            |
| created_by  | bigint unsigned | foreign key -> users.id     | onDelete: restrict                         |
| metadata    | json            | nullable                    |                                            |
| created_at  | timestamp       |                             |                                            |
| updated_at  | timestamp       |                             |                                            |
| deleted_at  | timestamp       | nullable                    |                                            |

**Indexes**: company_id, type, is_default, is_active, created_by  
**Unique**: [company_id, name, type]

### 2. generated_documents

| Column         | Type            | Properties                           | Description                              |
| -------------- | --------------- | ------------------------------------ | ---------------------------------------- |
| id             | bigint unsigned | primary key, auto-increment          |                                          |
| company_id     | bigint unsigned | foreign key -> companies.id          | onDelete: cascade                        |
| document_type  | varchar(50)     | not null                             | invoice, receipt, quotation, order, etc. |
| document_id    | bigint unsigned | not null                             | ID ของเอกสารต้นฉบับ                      |
| template_id    | bigint unsigned | foreign key -> document_templates.id | onDelete: set null                       |
| filename       | varchar(255)    | not null                             | ชื่อไฟล์ที่จัดเก็บ                       |
| disk           | varchar(50)     | default: 'local'                     | disk ที่จัดเก็บ (local, s3, etc.)        |
| path           | varchar(255)    | not null                             | path ที่จัดเก็บบน disk                   |
| is_signed      | boolean         | default: false                       | มีการเซ็นเอกสารหรือไม่                   |
| signature_data | json            | nullable                             | ข้อมูลลายเซ็น (ถ้ามี)                    |
| created_by     | bigint unsigned | foreign key -> users.id              | onDelete: restrict                       |
| metadata       | json            | nullable                             |                                          |
| created_at     | timestamp       |                                      |                                          |
| updated_at     | timestamp       |                                      |                                          |

**Indexes**: company_id, document_type, [document_type, document_id], template_id, created_by  
**Unique**: [document_type, document_id, created_at] (เพื่อให้สามารถสร้างใหม่ได้หลายครั้ง)

### 3. document_sendings

| Column                | Type            | Properties                            | Description                      |
| --------------------- | --------------- | ------------------------------------- | -------------------------------- |
| id                    | bigint unsigned | primary key, auto-increment           |                                  |
| company_id            | bigint unsigned | foreign key -> companies.id           | onDelete: cascade                |
| generated_document_id | bigint unsigned | foreign key -> generated_documents.id | onDelete: cascade                |
| recipient_email       | varchar(255)    | not null                              |                                  |
| recipient_name        | varchar(255)    | not null                              |                                  |
| subject               | varchar(255)    | not null                              |                                  |
| message               | text            | nullable                              |                                  |
| status                | varchar(20)     | default: 'pending'                    | pending, sent, delivered, failed |
| sent_at               | timestamp       | nullable                              |                                  |
| error                 | text            | nullable                              | กรณีส่งไม่สำเร็จ                 |
| sent_by               | bigint unsigned | foreign key -> users.id               | onDelete: set null               |
| metadata              | json            | nullable                              |                                  |
| created_at            | timestamp       |                                       |                                  |
| updated_at            | timestamp       |                                       |                                  |

**Indexes**: company_id, generated_document_id, recipient_email, status, sent_at, sent_by

## ความสัมพันธ์ระหว่างตาราง

### 1. Organization Domain

-   Company เป็นแกนหลักของระบบ Multi-tenant
-   Department มีความสัมพันธ์แบบ parent-child กับตัวมันเอง
-   Position ขึ้นอยู่กับ Department
-   Branch Office ขึ้นอยู่กับ Company

### 2. Human Resources Domain

-   Employee ขึ้นอยู่กับ Company, Department, Position, และ Branch Office
-   Work Shift ขึ้นอยู่กับ Company และถูกเชื่อมโยงกับ Employee ผ่านตาราง employee_work_shifts
-   Attendance เชื่อมโยงกับ Employee และจัดเก็บข้อมูลการเข้างาน
-   Leave เชื่อมโยงกับ Employee และ Leave Type

### 3. Sales Domain

-   **Customer** เป็นส่วนหนึ่งของ Company
-   **Quotation -> Order -> Invoice -> Payment** เป็นกระบวนการขายแบบสมบูรณ์:
    -   Quotation (ใบเสนอราคา) เชื่อมกับ Customer
    -   Order (คำสั่งซื้อ) เชื่อมกับ Customer และอาจอ้างอิงถึง Quotation
    -   Invoice (ใบแจ้งหนี้) เชื่อมกับ Customer และอาจอ้างอิงถึง Order
    -   Receipt (ใบเสร็จรับเงิน) ออกเมื่อมีการชำระเงินสำหรับ Invoice ครบถ้วน
    -   Payment (การชำระเงิน) เชื่อมกับ Invoice และบันทึกรายละเอียดการชำระ
-   **Delivery Notes** เชื่อมกับ Order และบันทึกการส่งมอบสินค้า
-   **ตารางรายการ (Items)** เชื่อมกับตารางหลักและ Products:
    -   quotation_items เชื่อมกับ quotations และ products
    -   order_items เชื่อมกับ orders และ products
    -   invoice_items เชื่อมกับ invoices และ products
    -   receipt_items เชื่อมกับ receipts และ invoices

### 4. เอกสาร PDF

-   เอกสารทุกประเภท (quotations, orders, invoices, receipts, delivery_notes) สามารถสร้าง PDF ได้
-   แต่ละประเภทเอกสารมีความสัมพันธ์กับ document_templates และ generated_documents

## แนวทางการสร้าง Migrations

1. **ลำดับการสร้าง Migrations**:

    - ตารางพื้นฐาน (ไม่มี foreign keys): companies, settings
    - ตารางระดับกลาง: departments, positions, branch_offices, roles, permissions
    - ตารางอื่นๆ ตามลำดับความสัมพันธ์

2. **การตั้งชื่อ Migration**:

    - ใช้รูปแบบ `YYYY_MM_DD_NNNNNN_<action>_<table_name>_table.php`
    - เช่น: `2024_08_01_000001_create_companies_table.php`
    - เรียงลำดับตามการพึ่งพา (dependencies)

3. **วิธีการเขียน Migration**:

    - กำหนด foreign keys อย่างชัดเจนพร้อม `onDelete` และ `onUpdate`
    - กำหนด indexes ทุกครั้ง
    - เพิ่ม `softDeletes()` ในตารางที่จำเป็น
    - ใช้ `timestamps()` ในทุกตาราง
    - ระบุ character set และ collation ให้ชัดเจนถ้าจำเป็น

4. **Checkpoint Migrations**:

    - สร้าง migration สำหรับ seed data สำคัญ
    - กำหนด migration เพื่อ update โครงสร้างในอนาคต
    - แยกการสร้างตารางและการเพิ่ม foreign keys ในกรณีที่มีความซับซ้อน

## การจัดการ Multi-tenancy

ระบบนี้ใช้แนวทาง multi-tenancy แบบ **Single Database, Shared Schema** โดยมีหลักการดังนี้:

1. **แยกข้อมูลด้วย Company ID**:

    - ทุกตารางที่มีข้อมูลของลูกค้ามี column `company_id`
    - ใช้ Global Scope ใน Laravel เพื่อกรองข้อมูลตาม company_id โดยอัตโนมัติ

2. **สิทธิ์การเข้าถึง**:

    - ผู้ใช้สามารถเข้าถึงได้เฉพาะข้อมูลของบริษัทที่ตนเองมีสิทธิ์
    - มี pivot table `company_user` เพื่อเชื่อมโยงผู้ใช้กับบริษัท

3. **Real-time Tenant Switching**:

    - ผู้ใช้สามารถสลับระหว่างบริษัทต่างๆ ที่ตนเองมีสิทธิ์ได้
    - จัดเก็บ current company ID ใน session

4. **Shared Resources**:
    - ตารางบางอย่างเช่น users, jobs เป็น shared resources ไม่แยกตาม company
    - ตารางอื่นๆ เช่น settings, document_templates มีการแยกตาม company

## แนวปฏิบัติในการพัฒนา

1. **กฎการใช้งาน Domain Scope**:

    - ไฟล์ migrations ควรอยู่ใน `database/migrations`
    - Models ควรอยู่ใน domain ที่เหมาะสมตามหลัก DDD
    - สร้าง interfaces และ repositories ตามแนวทาง DDD

2. **ข้อกำหนดในการเขียน Model**:

    - ใช้ trait `HasCompanyScope` สำหรับ model ที่แยกตาม company
    - กำหนด relations อย่างชัดเจน
    - ใช้ UUID/ULID สำหรับตารางสำคัญ
    - ใช้ soft deletes เมื่อเหมาะสม

3. **แนวทางการอัพเกรดฐานข้อมูล**:
    - สร้าง migration ใหม่ทุกครั้งที่มีการเปลี่ยนแปลงโครงสร้าง
    - หลีกเลี่ยงการแก้ไข migration ที่ถูก deploy แล้ว
    - ให้ migration ทำงานได้ทั้งกับฐานข้อมูลเปล่าและฐานข้อมูลที่มีข้อมูลอยู่แล้ว
    - มีการทดสอบการ rollback

## สรุปและการนำไปใช้

การออกแบบฐานข้อมูลนี้มีจุดมุ่งหมายเพื่อสร้างระบบที่มีความยืดหยุ่น ปรับขยายได้ และรองรับการทำงานแบบ multi-tenant อย่างสมบูรณ์ โดยใช้แนวคิด DDD (Domain-Driven Design) เพื่อแบ่งส่วนความรับผิดชอบอย่างชัดเจน

ในการพัฒนาระบบ CEOsofts นี้ เราจะยึดตามโครงสร้างฐานข้อมูลที่ออกแบบไว้นี้ พร้อมทั้งปรับปรุงเมื่อมีความต้องการใหม่เกิดขึ้น โดยคำนึงถึงการรักษาความสมบูรณ์ของข้อมูลและประสิทธิภาพในการทำงาน

## การจัดการสิทธิ์และการเข้าถึงข้อมูล

### แนวทางการจัดการสิทธิ์แบบ RBAC & ABAC

CEOsofts ใช้แนวทางผสมผสานระหว่าง Role-Based Access Control (RBAC) และ Attribute-Based Access Control (ABAC):

1. **Role-Based Access Control**:

    - กำหนดสิทธิ์ตามบทบาท (Roles) เช่น admin, manager, accountant, employee
    - บทบาทประกอบด้วยชุดสิทธิ์ (Permissions) ที่กำหนดว่าทำอะไรได้บ้าง
    - บทบาทเชื่อมโยงกับผู้ใช้งานผ่านตาราง `model_has_roles`

2. **Attribute-Based Access Control**:

    - กำหนดการเข้าถึงโดยพิจารณาจากแอตทริบิวต์อื่นๆ เช่น company_id, user_id, is_owner
    - ใช้ Policies และ Gates ของ Laravel เพื่อตรวจสอบการเข้าถึง
    - มีการนำ HasCompanyScope มาใช้เพื่อกรองข้อมูลตามบริษัท

3. **Multi-Level Access Control**:
    - สิทธิ์ระดับระบบ (System Level) - จัดการโดย superadmin
    - สิทธิ์ระดับบริษัท (Company Level) - จัดการโดย company admin
    - สิทธิ์ระดับแผนก (Department Level) - จัดการโดยหัวหน้าแผนก
    - สิทธิ์ระดับผู้ใช้ (User Level) - สิทธิ์เฉพาะตัวพนักงาน

## การพัฒนา Multi-tenancy กับ Laravel

### 1. การใช้งาน HasCompanyScope Trait

เราได้สร้าง trait `HasCompanyScope` เพื่อนำไปใช้กับทุก Model ที่ต้องการแบ่งแยกข้อมูลตามบริษัท:

```php
// ตัวอย่างการใช้งาน HasCompanyScope ใน Model
use App\Domain\Shared\Traits\HasCompanyScope;

class Invoice extends Model
{
    use HasCompanyScope;

    // ...model code...
}
```

Trait นี้ทำงานดังนี้:

-   เพิ่ม global scope ให้กับทุกคำสั่ง query โดยอัตโนมัติ
-   กรองข้อมูลเฉพาะบริษัทปัจจุบันที่ผู้ใช้กำลังใช้งานอยู่
-   มีเมธอดพิเศษสำหรับทำงานข้ามบริษัท เช่น `withoutCompanyScope()` และ `forCompany()`

### 2. การจัดการข้อมูลบริษัทปัจจุบันด้วย CompanySessionService

เราได้สร้าง service `CompanySessionService` เพื่อจัดการกับการเลือกและเปลี่ยนบริษัทปัจจุบัน:

```php
// ตัวอย่างการใช้งาน
$companyService = app(CompanySessionService::class);
$currentCompanyId = $companyService->getCurrentCompanyId();

// เปลี่ยนบริษัท
$companyService->setCurrentCompanyId($newCompanyId);
```

Service นี้ช่วยจัดการ:

-   การเก็บ company_id ปัจจุบันใน session
-   การตรวจสอบสิทธิ์การเข้าถึงบริษัท
-   การดึงรายชื่อบริษัททั้งหมดที่ผู้ใช้มีสิทธิ์เข้าถึง

### 3. รูปแบบ Domain-Driven Design (DDD)

เพื่อให้โค้ดมีความเป็นระเบียบและบำรุงรักษาได้ง่าย เราได้จัดโครงสร้างโปรเจคตามแนวทาง DDD:

```
app/
├── Domain/
│   ├── DocumentGeneration/
│   │   ├── Commands/
│   │   ├── Events/
│   │   ├── Listeners/
│   │   ├── Models/
│   │   ├── Repositories/
│   │   └── Services/
│   ├── HumanResources/
│   ├── Inventory/
│   ├── Organization/
│   ├── Sales/
│   ├── Settings/
│   └── Shared/
│       └── Traits/
│           └── HasCompanyScope.php
└── Http/
```

## การสร้างและจัดการฐานข้อมูล

### 1. สร้างฐานข้อมูลด้วย Custom Command

เราได้สร้าง command `db:create` สำหรับการสร้างฐานข้อมูลโดยง่าย:

```bash
php artisan db:create ceosofts_db_R1
```

### 2. การใช้งาน Migration

หลังจากสร้างฐานข้อมูลแล้ว ใช้คำสั่งต่อไปนี้เพื่อสร้างตาราง:

```bash
# สร้างฐานข้อมูลใหม่ (ใช้เมื่อต้องการเริ่มต้นใหม่)
php artisan migrate:fresh

# เพิ่ม migration ใหม่
php artisan migrate

# ตรวจสอบสถานะ migration
php artisan migrate:status
```

### 3. การเตรียมข้อมูลตั้งต้น (Seeding)

เพื่อเตรียมข้อมูลพื้นฐานสำหรับการเริ่มใช้งานระบบ:

```bash
# seed ข้อมูลพื้นฐานทั้งหมด
php artisan db:seed

# seed ข้อมูลเฉพาะ class
php artisan db:seed --class=RolesAndPermissionsSeeder
```

## ข้อควรระวังและคำแนะนำในการพัฒนา

1. **ความปลอดภัยของข้อมูล**:

    - ตรวจสอบ `company_id` ในทุกคำสั่ง query ที่เขียนเอง
    - ใช้ HasCompanyScope กับทุก model ที่แบ่งแยกตามบริษัท
    - ระวังการใช้ `withoutCompanyScope()` เพราะจะข้ามการตรวจสอบบริษัท

2. **ประสิทธิภาพ**:

    - ออกแบบ index ของตารางให้เหมาะสมกับรูปแบบการค้นหา
    - ใช้ eager loading เมื่อดึงข้อมูลที่มีความสัมพันธ์กัน
    - ระวังการใช้ json columns ในการค้นหาข้อมูลปริมาณมาก

3. **การสร้าง Models**:

    - ใช้ trait `HasCompanyScope` กับทุกโมเดลที่ต้องแยกตามบริษัท
    - กำหนด fillable, casts, relationships ให้ครบถ้วนและชัดเจน
    - เพิ่ม method scopes ที่จำเป็นเพื่อช่วยในการ query

4. **การ Deployment**:
    - ระวังการแก้ไข migration ที่ deploy แล้ว
    - ใช้ schema checks ในทุก migration เพื่อป้องกันข้อผิดพลาด
    - จัดการ index ที่มีอยู่แล้วด้วยความระมัดระวัง
