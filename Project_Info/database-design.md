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

2. \*\*การตั้งชื่อ
