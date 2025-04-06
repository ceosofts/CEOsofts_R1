# โครงสร้างฐานข้อมูลสำหรับระบบ CEOsofts

แผนภาพความสัมพันธ์ของฐานข้อมูลทั้งหมดแบ่งตาม Domain

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
```

## 1. Organization Domain

### 1.1. companies

-   `id` - int, primary key, auto-increment
-   `name` - varchar(255), not null
-   `code` - varchar(50), nullable, unique
-   `address` - text, nullable
-   `phone` - varchar(20), nullable
-   `email` - varchar(255), nullable
-   `tax_id` - varchar(50), nullable
-   `website` - varchar(255), nullable
-   `logo` - varchar(255), nullable
-   `is_active` - boolean, default: true
-   `status` - varchar(20), default: 'active'
-   `settings` - json, nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 1.2. departments

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `code` - varchar(50), nullable, unique
-   `description` - text, nullable
-   `is_active` - boolean, default: true
-   `status` - varchar(20), default: 'active'
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 1.3. positions

-   `id` - int, primary key, auto-increment
-   `department_id` - int, foreign key -> departments.id
-   `name` - varchar(255), not null
-   `code` - varchar(50), nullable
-   `description` - text, nullable
-   `is_active` - boolean, default: true
-   `level` - int, nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 1.4. branch_offices

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `code` - varchar(50), nullable
-   `address` - text, nullable
-   `phone` - varchar(20), nullable
-   `email` - varchar(255), nullable
-   `is_headquarters` - boolean, default: false
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

## 2. Human Resources Domain

### 2.1. employees

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `department_id` - int, foreign key -> departments.id
-   `position_id` - int, foreign key -> positions.id
-   `branch_office_id` - int, foreign key -> branch_offices.id, nullable
-   `employee_code` - varchar(50), unique
-   `first_name` - varchar(255), not null
-   `last_name` - varchar(255), not null
-   `email` - varchar(255), nullable, unique
-   `phone` - varchar(20), nullable
-   `address` - text, nullable
-   `birthdate` - date, nullable
-   `hire_date` - date, not null
-   `termination_date` - date, nullable
-   `id_card_number` - varchar(50), nullable
-   `tax_id` - varchar(50), nullable
-   `bank_account` - varchar(50), nullable
-   `bank_name` - varchar(100), nullable
-   `emergency_contact_name` - varchar(255), nullable
-   `emergency_contact_phone` - varchar(20), nullable
-   `profile_image` - varchar(255), nullable
-   `status` - varchar(20), default: 'active'
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 2.2. work_shifts

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `start_time` - time, not null
-   `end_time` - time, not null
-   `break_start` - time, nullable
-   `break_end` - time, nullable
-   `is_night_shift` - boolean, default: false
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 2.3. employee_work_shifts

-   `id` - int, primary key, auto-increment
-   `employee_id` - int, foreign key -> employees.id
-   `work_shift_id` - int, foreign key -> work_shifts.id
-   `effective_date` - date, not null
-   `end_date` - date, nullable
-   `is_current` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 2.4. attendances

-   `id` - int, primary key, auto-increment
-   `employee_id` - int, foreign key -> employees.id
-   `date` - date, not null
-   `clock_in` - time, nullable
-   `clock_out` - time, nullable
-   `late_minutes` - int, default: 0
-   `overtime_minutes` - int, default: 0
-   `status` - varchar(20), default: 'present'
-   `notes` - text, nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 2.5. leaves

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `employee_id` - int, foreign key -> employees.id
-   `leave_type_id` - int, foreign key -> leave_types.id
-   `start_date` - date, not null
-   `end_date` - date, not null
-   `days` - decimal(5,2), not null
-   `reason` - text, nullable
-   `status` - varchar(20), default: 'pending'
-   `approved_by` - int, foreign key -> users.id, nullable
-   `approved_at` - timestamp, nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 2.6. leave_types

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `days_allowed` - decimal(5,2), not null
-   `is_paid` - boolean, default: true
-   `color_code` - varchar(20), nullable
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 2.7. payrolls

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `employee_id` - int, foreign key -> employees.id
-   `month` - int, not null
-   `year` - int, not null
-   `basic_salary` - decimal(15,2), not null
-   `allowances` - decimal(15,2), default: 0
-   `deductions` - decimal(15,2), default: 0
-   `overtime_pay` - decimal(15,2), default: 0
-   `tax` - decimal(15,2), default: 0
-   `social_security` - decimal(15,2), default: 0
-   `net_salary` - decimal(15,2), not null
-   `payment_date` - date, nullable
-   `payment_method` - varchar(50), nullable
-   `reference_no` - varchar(100), nullable
-   `status` - varchar(20), default: 'pending'
-   `created_by` - int, foreign key -> users.id
-   `approved_by` - int, foreign key -> users.id, nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 2.8. company_holidays

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `date` - date, not null
-   `is_recurring` - boolean, default: false
-   `description` - text, nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp

## 3. Sales Domain

### 3.1. customers

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `code` - varchar(50), nullable, unique
-   `name` - varchar(255), not null
-   `contact_person` - varchar(255), nullable
-   `email` - varchar(255), nullable
-   `phone` - varchar(20), nullable
-   `address` - text, nullable
-   `tax_id` - varchar(50), nullable
-   `credit_limit` - decimal(15,2), nullable
-   `payment_terms` - int, nullable
-   `discount_percent` - decimal(5,2), default: 0
-   `status` - varchar(20), default: 'active'
-   `notes` - text, nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 3.2. quotations

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `customer_id` - int, foreign key -> customers.id
-   `quotation_number` - varchar(50), unique
-   `reference` - varchar(100), nullable
-   `issue_date` - date, not null
-   `expiry_date` - date, not null
-   `status` - varchar(20), default: 'draft'
-   `subtotal` - decimal(15,2), not null
-   `tax_amount` - decimal(15,2), default: 0
-   `discount_amount` - decimal(15,2), default: 0
-   `total` - decimal(15,2), not null
-   `terms_and_conditions` - text, nullable
-   `notes` - text, nullable
-   `created_by` - int, foreign key -> users.id
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 3.3. quotation_items

-   `id` - int, primary key, auto-increment
-   `quotation_id` - int, foreign key -> quotations.id
-   `product_id` - int, foreign key -> products.id
-   `description` - text, nullable
-   `quantity` - decimal(15,2), not null
-   `unit_price` - decimal(15,2), not null
-   `tax_percentage` - decimal(5,2), default: 0
-   `discount_percentage` - decimal(5,2), default: 0
-   `subtotal` - decimal(15,2), not null
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 3.4. orders

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `customer_id` - int, foreign key -> customers.id
-   `quotation_id` - int, foreign key -> quotations.id, nullable
-   `order_number` - varchar(50), unique
-   `reference` - varchar(100), nullable
-   `order_date` - date, not null
-   `shipping_date` - date, nullable
-   `due_date` - date, nullable
-   `status` - varchar(20), default: 'pending'
-   `subtotal` - decimal(15,2), not null
-   `tax_amount` - decimal(15,2), default: 0
-   `shipping_amount` - decimal(15,2), default: 0
-   `discount_amount` - decimal(15,2), default: 0
-   `total` - decimal(15,2), not null
-   `notes` - text, nullable
-   `created_by` - int, foreign key -> users.id
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 3.5. order_items

-   `id` - int, primary key, auto-increment
-   `order_id` - int, foreign key -> orders.id
-   `product_id` - int, foreign key -> products.id
-   `description` - text, nullable
-   `quantity` - decimal(15,2), not null
-   `unit_price` - decimal(15,2), not null
-   `tax_percentage` - decimal(5,2), default: 0
-   `discount_percentage` - decimal(5,2), default: 0
-   `subtotal` - decimal(15,2), not null
-   `status` - varchar(20), default: 'pending'
-   `created_at` - timestamp
-   `updated_at` - timestamp

## 4. Inventory Domain

### 4.1. products

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `category_id` - int, foreign key -> product_categories.id, nullable
-   `code` - varchar(50), nullable, unique
-   `name` - varchar(255), not null
-   `description` - text, nullable
-   `unit_id` - int, foreign key -> units.id, nullable
-   `selling_price` - decimal(15,2), not null
-   `buying_price` - decimal(15,2), nullable
-   `tax_id` - int, foreign key -> taxes.id, nullable
-   `min_stock` - decimal(15,2), default: 0
-   `opening_stock` - decimal(15,2), default: 0
-   `current_stock` - decimal(15,2), default: 0
-   `image` - varchar(255), nullable
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 4.2. product_categories

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `parent_id` - int, foreign key -> product_categories.id, nullable
-   `name` - varchar(255), not null
-   `description` - text, nullable
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 4.3. units

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(50), not null
-   `abbreviation` - varchar(10), not null
-   `description` - text, nullable
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 4.4. stock_movements

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `product_id` - int, foreign key -> products.id
-   `reference_type` - varchar(50), not null
-   `reference_id` - int, not null
-   `movement_type` - varchar(20), not null
-   `quantity` - decimal(15,2), not null
-   `balance` - decimal(15,2), not null
-   `notes` - text, nullable
-   `created_by` - int, foreign key -> users.id
-   `created_at` - timestamp
-   `updated_at` - timestamp

## 5. Finance Domain

### 5.1. invoices

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `customer_id` - int, foreign key -> customers.id
-   `order_id` - int, foreign key -> orders.id, nullable
-   `quotation_id` - int, foreign key -> quotations.id, nullable
-   `invoice_number` - varchar(50), unique
-   `reference` - varchar(100), nullable
-   `issue_date` - date, not null
-   `due_date` - date, not null
-   `status` - varchar(20), default: 'unpaid'
-   `payment_status` - varchar(20), default: 'pending'
-   `subtotal` - decimal(15,2), not null
-   `tax_amount` - decimal(15,2), default: 0
-   `shipping_amount` - decimal(15,2), default: 0
-   `discount_amount` - decimal(15,2), default: 0
-   `total` - decimal(15,2), not null
-   `terms_and_conditions` - text, nullable
-   `notes` - text, nullable
-   `created_by` - int, foreign key -> users.id
-   `created_at` - timestamp
-   `updated_at` - timestamp
-   `deleted_at` - timestamp, nullable

### 5.2. invoice_items

-   `id` - int, primary key, auto-increment
-   `invoice_id` - int, foreign key -> invoices.id
-   `product_id` - int, foreign key -> products.id
-   `description` - text, nullable
-   `quantity` - decimal(15,2), not null
-   `unit_price` - decimal(15,2), not null
-   `tax_percentage` - decimal(5,2), default: 0
-   `discount_percentage` - decimal(5,2), default: 0
-   `subtotal` - decimal(15,2), not null
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 5.3. payments

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `invoice_id` - int, foreign key -> invoices.id
-   `amount` - decimal(15,2), not null
-   `payment_date` - date, not null
-   `payment_method` - varchar(50), not null
-   `reference` - varchar(100), nullable
-   `notes` - text, nullable
-   `created_by` - int, foreign key -> users.id
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 5.4. expenses

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `expense_category_id` - int, foreign key -> expense_categories.id
-   `amount` - decimal(15,2), not null
-   `payment_date` - date, not null
-   `payment_method` - varchar(50), not null
-   `reference` - varchar(100), nullable
-   `description` - text, nullable
-   `created_by` - int, foreign key -> users.id
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 5.5. expense_categories

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `description` - text, nullable
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 5.6. taxes

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `rate` - decimal(5,2), not null
-   `description` - text, nullable
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp

## 6. Settings Domain

### 6.1. users

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id, nullable
-   `name` - varchar(255), not null
-   `email` - varchar(255), unique, not null
-   `password` - varchar(255), not null
-   `employee_id` - int, foreign key -> employees.id, nullable
-   `email_verified_at` - timestamp, nullable
-   `remember_token` - varchar(100), nullable
-   `is_active` - boolean, default: true
-   `last_login_at` - timestamp, nullable
-   `last_login_ip` - varchar(45), nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 6.2. roles

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id, nullable
-   `name` - varchar(255), not null
-   `guard_name` - varchar(255), not null
-   `description` - text, nullable
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 6.3. permissions

-   `id` - int, primary key, auto-increment
-   `name` - varchar(255), not null
-   `guard_name` - varchar(255), not null
-   `description` - text, nullable
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 6.4. role_has_permissions

-   `permission_id` - int, foreign key -> permissions.id
-   `role_id` - int, foreign key -> roles.id

### 6.5. model_has_roles

-   `role_id` - int, foreign key -> roles.id
-   `model_type` - varchar(255), not null
-   `model_id` - int, not null

### 6.6. model_has_permissions

-   `permission_id` - int, foreign key -> permissions.id
-   `model_type` - varchar(255), not null
-   `model_id` - int, not null

### 6.7. settings

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id, nullable
-   `key` - varchar(255), not null
-   `value` - text, nullable
-   `type` - varchar(50), default: 'string'
-   `is_public` - boolean, default: false
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 6.8. job_statuses

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `color_code` - varchar(20), nullable
-   `description` - text, nullable
-   `type` - varchar(50), default: 'general'
-   `sort_order` - int, default: 0
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 6.9. payment_statuses

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `color_code` - varchar(20), nullable
-   `description` - text, nullable
-   `sort_order` - int, default: 0
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp

### 6.10. prefixes

-   `id` - int, primary key, auto-increment
-   `company_id` - int, foreign key -> companies.id
-   `name` - varchar(255), not null
-   `value` - varchar(50), not null
-   `type` - varchar(50), not null
-   `description` - text, nullable
-   `is_active` - boolean, default: true
-   `created_at` - timestamp
-   `updated_at` - timestamp
