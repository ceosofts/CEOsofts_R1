# โครงสร้างฐานข้อมูลสำหรับระบบ CEOsofts (ปรับปรุง)

แผนภาพความสัมพันธ์ของฐานข้อมูลทั้งหมดแบ่งตาม Domain พร้อมปรับปรุงตามแนวทางของ Laravel ล่าสุด

## สารบัญ

1. [การปรับปรุงที่แนะนำ](#การปรับปรุงที่แนะนำ)
2. [โครงสร้างฐานข้อมูลแบ่งตาม Domain](#โครงสร้างฐานข้อมูลแบ่งตาม-domain)
3. [รายละเอียดตารางแยกตาม Domain](#รายละเอียดตารางแยกตาม-domain)
4. [ตารางระบบ และ Audit](#ตารางระบบ-และ-audit)
5. [ความสัมพันธ์ระหว่างตาราง](#ความสัมพันธ์ระหว่างตาราง)

## การปรับปรุงที่แนะนำ

1. **เพิ่ม UUID สำหรับตารางหลัก**:

    - เพิ่มฟิลด์ `uuid` (ใช้เป็น ULID) เพื่อความสะดวกในการซิงค์ข้อมูล
    - ใช้คู่กับ auto-increment `id` เพื่อประสิทธิภาพใน indexing

2. **เพิ่ม JSON columns**:

    - เพิ่มฟิลด์ `metadata` (JSON) ในตารางหลักสำหรับข้อมูลเพิ่มเติมที่ไม่ได้กำหนดล่วงหน้า
    - ปรับปรุงฟิลด์ `settings` เป็น JSON ในตารางที่เกี่ยวข้อง

3. **Foreign Keys & Indexes**:

    - ระบุ `onDelete` และ `onUpdate` actions ให้ชัดเจนทุกตาราง
    - เพิ่ม indexes สำหรับการค้นหาที่ใช้บ่อย (code, email, name, status)
    - เพิ่ม composite indexes สำหรับการกรองข้อมูลที่ซับซ้อน

4. **Multi-tenant แบบสมบูรณ์**:

    - เพิ่ม `company_id` ในทุกตารางที่ควรแยกตาม tenant
    - เพิ่ม unique constraints ที่รวม company_id
    - ยกเว้นตารางระบบที่ใช้ร่วมกัน

5. **ตารางเพิ่มเติม**:

    - `activity_logs`: สำหรับบันทึกการเปลี่ยนแปลงข้อมูล
    - `file_attachments`: สำหรับจัดการไฟล์แนบของทุกโมเดล
    - `comments`: สำหรับความคิดเห็นบนทุกโมเดล (polymorphic)
    - `translations`: สำหรับรองรับหลายภาษา
    - `model_versions`: สำหรับเก็บประวัติการเปลี่ยนแปลงข้อมูลสำคัญ

6. **Timestamps และ Soft Deletes**:
    - ใช้ `timestamps()` และ `softDeletes()` ในทุกตารางหลัก
    - เพิ่ม `deleted_by` ในตารางที่ใช้ softDeletes

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

| Column               | Type            | Properties                  | Description                              |
| -------------------- | --------------- | --------------------------- | ---------------------------------------- |
| id                   | bigint unsigned | primary key, auto-increment |                                          |
| uuid                 | ulid            | unique                      |                                          |
| company_id           | bigint unsigned | foreign key -> companies.id | onDelete: cascade                        |
| customer_id          | bigint unsigned | foreign key -> customers.id | onDelete: restrict                       |
| quotation_number     | varchar(50)     | not null                    |                                          |
| reference            | varchar(100)    | nullable                    |                                          |
| issue_date           | date            | not null                    |                                          |
| expiry_date          | date            | not null                    |                                          |
| status               | varchar(20)     | default: 'draft'            | draft, sent, accepted, rejected, expired |
| currency_code        | varchar(3)      | default: 'THB'              |                                          |
| exchange_rate        | decimal(15,5)   | default: 1.00000            |                                          |
| subtotal             | decimal(15,2)   | not null                    |                                          |
| tax_amount           | decimal(15,2)   | default: 0                  |                                          |
| discount_type        | varchar(10)     | default: 'percentage'       | percentage, fixed                        |
| discount_percentage  | decimal(5,2)    | default: 0                  |                                          |
| discount_amount      | decimal(15,2)   | default: 0                  |                                          |
| total                | decimal(15,2)   | not null                    |                                          |
| terms_and_conditions | text            | nullable                    |                                          |
| notes                | text            | nullable                    |                                          |
| created_by           | bigint unsigned | foreign key -> users.id     | onDelete: restrict                       |
| metadata             | json            | nullable                    |                                          |
| created_at           | timestamp       |                             |                                          |
| updated_at           | timestamp       |                             |                                          |
| deleted_at           | timestamp       | nullable                    |                                          |
| deleted_by           | bigint unsigned | nullable                    |                                          |

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

| Column               | Type            | Properties                             | Description                               |
| -------------------- | --------------- | -------------------------------------- | ----------------------------------------- |
| id                   | bigint unsigned | primary key, auto-increment            |                                           |
| uuid                 | ulid            | unique                                 |                                           |
| company_id           | bigint unsigned | foreign key -> companies.id            | onDelete: cascade                         |
| customer_id          | bigint unsigned | foreign key -> customers.id            | onDelete: restrict                        |
| quotation_id         | bigint unsigned | nullable, foreign key -> quotations.id | onDelete: set null                        |
| order_number         | varchar(50)     | not null                               |                                           |
| reference            | varchar(100)    | nullable                               |                                           |
| order_date           | date            | not null                               |                                           |
| shipping_date        | date            | nullable                               |                                           |
| due_date             | date            | nullable                               |                                           |
| status               | varchar(20)     | default: 'pending'                     | pending, processing, completed, cancelled |
| shipping_status      | varchar(20)     | default: 'pending'                     | pending, partial, shipped, delivered      |
| currency_code        | varchar(3)      | default: 'THB'                         |                                           |
| exchange_rate        | decimal(15,5)   | default: 1.00000                       |                                           |
| subtotal             | decimal(15,2)   | not null                               |                                           |
| tax_amount           | decimal(15,2)   | default: 0                             |                                           |
| shipping_amount      | decimal(15,2)   | default: 0                             |                                           |
| discount_type        | varchar(10)     | default: 'percentage'                  | percentage, fixed                         |
| discount_percentage  | decimal(5,2)    | default: 0                             |                                           |
| discount_amount      | decimal(15,2)   | default: 0                             |                                           |
| total                | decimal(15,2)   | not null                               |                                           |
| notes                | text            | nullable                               |                                           |
| terms_and_conditions | text            | nullable                               |                                           |
| created_by           | bigint unsigned | foreign key -> users.id                | onDelete: restrict                        |
| metadata             | json            | nullable                               |                                           |
| created_at           | timestamp       |                                        |                                           |
| updated_at           | timestamp       |                                        |                                           |
| deleted_at           | timestamp       | nullable                               |                                           |
| deleted_by           | bigint unsigned | nullable                               |                                           |

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

| Column               | Type            | Properties                             | Description                                   |
| -------------------- | --------------- | -------------------------------------- | --------------------------------------------- |
| id                   | bigint unsigned | primary key, auto-increment            |                                               |
| uuid                 | ulid            | unique                                 |                                               |
| company_id           | bigint unsigned | foreign key -> companies.id            | onDelete: cascade                             |
| customer_id          | bigint unsigned | foreign key -> customers.id            | onDelete: restrict                            |
| order_id             | bigint unsigned | nullable, foreign key -> orders.id     | onDelete: set null                            |
| quotation_id         | bigint unsigned | nullable, foreign key -> quotations.id | onDelete: set null                            |
| invoice_number       | varchar(50)     | not null                               |                                               |
| reference            | varchar(100)    | nullable                               |                                               |
| issue_date           | date            | not null                               |                                               |
| due_date             | date            | not null                               |                                               |
| status               | varchar(20)     | default: 'unpaid'                      | draft, sent, unpaid, partial, paid, cancelled |
| payment_status       | varchar(20)     | default: 'pending'                     | pending, partial, paid, overdue               |
| currency_code        | varchar(3)      | default: 'THB'                         |                                               |
| exchange_rate        | decimal(15,5)   | default: 1.00000                       |                                               |
| subtotal             | decimal(15,2)   | not null                               |                                               |
| tax_amount           | decimal(15,2)   | default: 0                             |                                               |
| shipping_amount      | decimal(15,2)   | default: 0                             |                                               |
| discount_type        | varchar(10)     | default: 'percentage'                  | percentage, fixed                             |
| discount_percentage  | decimal(5,2)    | default: 0                             |                                               |
| discount_amount      | decimal(15,2)   | default: 0                             |                                               |
| total                | decimal(15,2)   | not null                               |                                               |
| amount_paid          | decimal(15,2)   | default: 0                             |                                               |
| amount_due           | decimal(15,2)   | not null                               |                                               |
| terms_and_conditions | text            | nullable                               |                                               |
| notes                | text            | nullable                               |                                               |
| created_by           | bigint unsigned | foreign key -> users.id                | onDelete: restrict                            |
| metadata             | json            | nullable                               |                                               |
| created_at           | timestamp       |                                        |                                               |
| updated_at           | timestamp       |                                        |                                               |
| deleted_at           | timestamp       | nullable                               |                                               |
| deleted_by           | bigint unsigned | nullable                               |                                               |

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

| Column         | Type            | Properties                  | Description                 |
| -------------- | --------------- | --------------------------- | --------------------------- |
| id             | bigint unsigned | primary key, auto-increment |                             |
| uuid           | ulid            | unique                      |                             |
| company_id     | bigint unsigned | foreign key -> companies.id | onDelete: cascade           |
| customer_id    | bigint unsigned | foreign key -> customers.id | onDelete: restrict          |
| receipt_number | varchar(50)     | not null                    |                             |
| reference      | varchar(100)    | nullable                    |                             |
| receipt_date   | date            | not null                    |                             |
| status         | varchar(20)     | default: 'completed'        | draft, completed, cancelled |
| currency_code  | varchar(3)      | default: 'THB'              |                             |
| exchange_rate  | decimal(15,5)   | default: 1.00000            |                             |
| subtotal       | decimal(15,2)   | not null                    |                             |
| tax_amount     | decimal(15,2)   | default: 0                  |                             |
| total          | decimal(15,2)   | not null                    |                             |
| notes          | text            | nullable                    |                             |
| created_by     | bigint unsigned | foreign key -> users.id     | onDelete: restrict          |
| metadata       | json            | nullable                    |                             |
| created_at     | timestamp       |                             |                             |
| updated_at     | timestamp       |                             |                             |
| deleted_at     | timestamp       | nullable                    |                             |
| deleted_by     | bigint unsigned | nullable                    |                             |

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

| Column          | Type            | Properties                  | Description                            |
| --------------- | --------------- | --------------------------- | -------------------------------------- |
| id              | bigint unsigned | primary key, auto-increment |                                        |
| uuid            | ulid            | unique                      |                                        |
| company_id      | bigint unsigned | foreign key -> companies.id | onDelete: cascade                      |
| customer_id     | bigint unsigned | foreign key -> customers.id | onDelete: restrict                     |
| order_id        | bigint unsigned | foreign key -> orders.id    | onDelete: restrict                     |
| delivery_number | varchar(50)     | not null                    |                                        |
| delivery_date   | date            | not null                    |                                        |
| driver_name     | varchar(255)    | nullable                    |                                        |
| vehicle_info    | varchar(255)    | nullable                    |                                        |
| status          | varchar(20)     | default: 'draft'            | draft, dispatched, delivered, returned |
| notes           | text            | nullable                    |                                        |
| received_by     | varchar(255)    | nullable                    |                                        |
| received_at     | timestamp       | nullable                    |                                        |
| created_by      | bigint unsigned | foreign key -> users.id     | onDelete: restrict                     |
| metadata        | json            | nullable                    |                                        |
| created_at      | timestamp       |                             |                                        |
| updated_at      | timestamp       |                             |                                        |
| deleted_at      | timestamp       | nullable                    |                                        |
| deleted_by      | bigint unsigned | nullable                    |                                        |

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

### 4. comments

| Column           | Type            | Properties                           | Description        |
| ---------------- | --------------- | ------------------------------------ | ------------------ |
| id               | bigint unsigned | primary key, auto-increment          |                    |
| company_id       | bigint unsigned | foreign key -> companies.id          | onDelete: cascade  |
| commentable_type | varchar(255)    | not null                             | Model class name   |
| commentable_id   | bigint unsigned | not null                             | Model ID           |
| user_id          | bigint unsigned | nullable, foreign key -> users.id    | onDelete: set null |
| content          | text            | not null                             |                    |
| parent_id        | bigint unsigned | nullable, foreign key -> comments.id | onDelete: cascade  |
| created_at       | timestamp       |                                      |                    |
| updated_at       | timestamp       |                                      |                    |
| deleted_at       | timestamp       | nullable                             |                    |
| deleted_by       | bigint unsigned | nullable                             |                    |

**Indexes**: company_id, [commentable_type, commentable_id], user_id, parent_id, created_at

### 5. model_versions

| Column           | Type            | Properties                        | Description        |
| ---------------- | --------------- | --------------------------------- | ------------------ |
| id               | bigint unsigned | primary key, auto-increment       |                    |
| versionable_id   | bigint unsigned | not null                          |                    |
| versionable_type | varchar(255)    | not null                          |                    |
| user_id          | bigint unsigned | nullable, foreign key -> users.id | onDelete: set null |
| data             | json            | not null                          |                    |
| version          | int             | not null                          |                    |
| created_at       | timestamp       |                                   |                    |

**Indexes**: [versionable_type, versionable_id], user_id, version  
**Unique**: [versionable_type, versionable_id, version]

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

### 4. Inventory Domain

-   Product เชื่อมโยงกับ Product Category, Unit และ Tax
-   Stock Movement บันทึกการเคลื่อนไหวของสินค้าทั้งหมด

### 5. ตารางระบบ

-   Activity Logs ใช้ความสัมพันธ์แบบ Polymorphic เพื่อบันทึกกิจกรรม
-   File Attachments ใช้ความสัมพันธ์แบบ Polymorphic สำหรับไฟล์แนบ
-   Translations ใช้ความสัมพันธ์แบบ Polymorphic สำหรับข้อมูลหลายภาษา

## การเชื่อมโยงกับ Laravel

ในการพัฒนาระบบด้วย Laravel:

1. ใช้ Laravel Migration สำหรับสร้างโครงสร้างฐานข้อมูล
2. สร้าง Eloquent Models พร้อมความสัมพันธ์
3. ใช้ Laravel Observer สำหรับบันทึก Activity Logs
4. ใช้ Traits เช่น HasCompanyScope, HasTranslations, HasAttachments สำหรับความสามารถที่ใช้ร่วมกัน

## การประมวลผลด้านการขาย

1. **การจัดการเอกสารการขาย**:

    - ใช้ระบบ Prefixes และ Running Numbers สำหรับการสร้างเลขที่เอกสาร
    - เก็บประวัติทุกสถานะของเอกสาร (quotation, order, invoice)
    - รองรับหลายสกุลเงินผ่าน exchange_rate

2. **การคำนวณการขายและภาษี**:

    - คำนวณส่วนลด (percentage หรือ fixed amount)
    - คำนวณภาษีมูลค่าเพิ่ม (VAT)
    - คำนวณยอดค้างชำระและติดตามการชำระเงิน

3. **การออกรายงาน**:
    - รายงานยอดขาย (daily, monthly, yearly)
    - รายงานสินค้าขายดี
    - รายงานลูกหนี้และการชำระเงิน
    - รายงานภาษีขายสำหรับนำส่งสรรพากร
