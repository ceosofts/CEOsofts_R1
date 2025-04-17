# CEOsofts R1 - Roadmap การพัฒนาระบบใหม่

กฎการทำงานร่วมกันระหว่างฉันกับ AI
1.ให้คุยเป็นภาษาไทย
2.อนุญาตให้ เพิ่ม ลบ แก้ไข ย้าย ไฟล์ทั้งหมดได้
3.migration file สามารถแก้ไขได้ ไม่จำเป็นไม่ต้องสร้างใหม่ เพราะอยู่ในช่วงเริ่มทำเท่านั้น
4.หลังจากนี้คือ update migration+seed file เสร็จแล้ว ขอ งด การแก้ไขทั้ง2ไฟล์นี้ หรือแก้ให้น้อยที่สุด
5.หากมีการแก้อะไร แล้วมีผลกระทบไฟล์อื่น ควรแก้ให้ด้วยหรือแจ้งให้ทราบไว้ว่าจะมีผลกับไฟล์ไหนบ้าง
6.ให้คุณ ศึกษา project_structure , Project_Status , roadmap-new-system , ui-ux และ ตารางฐานข้อมูล ของฉันเพื่อจะได้เข้าใจใน บริบท

เนื่องจากมีปัญหาเครื่องช้า ต้องเปลี่ยนมาใช้ valet แทน http://ceosofts.test
# ดูรายการ site ทั้งหมดที่ link ไว้กับ Valet
valet links

# รีสตาร์ท Valet (หากมีปัญหา)
valet restart

# หยุดการทำงานของ Valet ชั่วคราว
valet stop

# เริ่มการทำงานของ Valet
valet start

# เปิดใช้งาน SSL สำหรับโปรเจคนี้ (https)
valet secure ceosofts

# ยกเลิก SSL
valet unsecure ceosofts


## วิสัยทัศน์
CEOsofts R1 มุ่งสู่การเป็นระบบบริหารจัดการองค์กรครบวงจรที่ใช้งานง่าย ปรับแต่งได้ตามความต้องการ และช่วยเพิ่มประสิทธิภาพการทำงานสำหรับธุรกิจทุกขนาด

## แผนการพัฒนาระบบ (ปี 2025-2026)

### เฟส 1: พื้นฐานระบบและการยืนยันตัวตน (เมษายน 2025) ✓
- ✅ โครงสร้างพื้นฐานระบบ
- ✅ การออกแบบฐานข้อมูล
- ✅ ระบบยืนยันตัวตน (Login/Register)
- ✅ ระบบรีเซ็ตรหัสผ่าน
- ✅ การออกแบบ UI/UX หลัก
- ✅ โครงสร้างแผนการทำงาน (Project Structure)

### เฟส 2: บริหารจัดการผู้ใช้และบริษัท (พฤษภาคม 2025) - ความคืบหน้า 100%
- ✅ welcome page
- ✅ การจัดการบริษัท/สาขา (Company/Branch Management)
- ✅ การจัดการแผนก (Department Management)
- ✅ การจัดการตำแหน่ง (Position Management) 
- ✅ การจัดการพนักงาน (Employee Management) 
- ✅ การกำหนดโครงสร้างองค์กร (Organization Structure) 
- ✅ Dashboard สำหรับผู้บริหาร (Executive Dashboard) 

### เฟส 3: ระบบการขาย (มิถุนายน-กรกฎาคม 2025) - ความคืบหน้า 5%
- ✅ การจัดการลูกค้า (Customer Management) 
- ✅ การจัดการสินค้าและบริการ (Product/Service Management)
- ✅ การจัดการ หมวดหมู่ สินค้าและบริการ (Product Catalogries)

- 🔄 เช็คช่องค้นหาว่าทำงานหรือเปล่าในทุก เพจที่ทำไปแล้ว -กำลังดำเนินการ ( Empolyees finish)
- 🔄 การจัดการ หน่วยนับ
- 🔄 การจัดการ การเคลื่อนไหวของสินค้า

- 📝 ระบบใบเสนอราคา (Quotation System)
- 📝 ระบบใบสั่งขาย (Sales Order)
- 📝 ระบบใบส่งสินค้า (Delivery Order)
- 📝 ระบบใบแจ้งหนี้ (Invoice System)
- 📝 ระบบใบเสร็จรับเงิน (Receipt System)
- 📝 รายงานการขาย (Sales Report)

### เฟส 4: ระบบสินค้าคงคลัง (สิงหาคม-กันยายน 2025)
- 📝 การจัดการคลังสินค้า (Warehouse Management)
- 📝 การรับสินค้า (Goods Receipt)
- 📝 การเบิกจ่ายสินค้า (Goods Issue)
- 📝 การโอนย้ายสินค้า (Stock Transfer)
- 📝 การตรวจนับสินค้า (Stock Count)
- 📝 การปรับปรุงสินค้า (Stock Adjustment)
- 📝 การจัดการสินค้าใกล้หมดอายุ (Expiry Management)
- 📝 รายงานสินค้าคงคลัง (Inventory Report)

### เฟส 5: ระบบการเงินและบัญชี (ตุลาคม-พฤศจิกายน 2025)
- 📝 ระบบบัญชีแยกประเภท (General Ledger)
- 📝 ระบบบัญชีเจ้าหนี้ (Account Payable)
- 📝 ระบบบัญชีลูกหนี้ (Account Receivable)
- 📝 การบันทึกรายรับ-รายจ่าย (Cash Flow)
- 📝 การจัดการภาษี (Tax Management)
- 📝 การปิดงวดบัญชี (Accounting Period Close)
- 📝 รายงานทางการเงิน (Financial Report)

### เฟส 6: ระบบทรัพยากรบุคคล (ธันวาคม 2025 - มกราคม 2026)
- 🔄 การจัดการข้อมูลพนักงาน (Employee Information) - เริ่มต้นการพัฒนา
- 📝 การลงเวลาทำงาน (Time Attendance)
- 📝 การจัดการวันลาและวันหยุด (Leave Management)
- 📝 การคำนวณเงินเดือนและค่าตอบแทน (Payroll)
- 📝 การประเมินผลการปฏิบัติงาน (Performance Evaluation)
- 📝 การฝึกอบรมและพัฒนา (Training & Development)
- 📝 รายงานทรัพยากรบุคคล (HR Reports)

### เฟส 7: การบูรณาการระบบและการปรับแต่ง (กุมภาพันธ์ 2026)
- 📝 การเชื่อมต่อกับระบบภายนอก (External System Integration)
- 📝 การสร้างรายงานแบบกำหนดเอง (Custom Report Generator)
- 📝 ระบบแจ้งเตือนอัตโนมัติ (Automated Alert System)
- 📝 Mobile Application สำหรับผู้บริหารและพนักงาน
- 📝 การ Import/Export ข้อมูล (Data Import/Export)

### เฟส 8: การปรับปรุงและทดสอบระบบ (มีนาคม 2026)
- 📝 การทดสอบประสิทธิภาพ (Performance Testing)
- 📝 การทดสอบความปลอดภัย (Security Testing)
- 📝 การทดสอบการใช้งานจริง (User Acceptance Testing)
- 📝 การปรับแต่งประสิทธิภาพ (Performance Optimization)
- 📝 การจัดทำเอกสารคู่มือการใช้งาน (User Documentation)

## ความคืบหน้าปัจจุบัน
- เฟส 1: เสร็จสมบูรณ์ 100%
- เฟส 2: อยู่ระหว่างดำเนินการ 35%
- เฟส 3: อยู่ระหว่างดำเนินการ 5%
- เฟส 6: เริ่มพัฒนาบางส่วน 5%

## อัพเดทล่าสุด (16 เมษายน 2568)

### การพัฒนาระบบจัดการแผนก (Department Management)
- ✅ หน้าแสดงรายการแผนก (Department Index)
- ✅ หน้าแสดงรายละเอียดแผนก (Department Details)
- ✅ หน้าเพิ่ม/แก้ไขแผนก (Department Create/Edit)
- ✅ ปรับปรุงการแสดงผล dropdown เลือกบริษัทในหน้าแก้ไข
- ✅ แก้ไขปัญหา error จากการเรียกใช้ activity log
- ✅ ปรับปรุง redirect หลังจาก update เพื่อประสบการณ์ผู้ใช้ที่ดีขึ้น
- ✅ ปรับปรุงโมเดล Department ให้สอดคล้องกับโครงสร้างฐานข้อมูลจริง

### แผนงานระยะสั้น (17-30 เมษายน 2568)
1. **พัฒนาระบบจัดการตำแหน่ง** (17-24 เม.ย.):
   - การแสดงรายการตำแหน่งทั้งหมด
   - การแสดงรายละเอียดตำแหน่ง
   - การเพิ่ม/แก้ไข/ลบข้อมูลตำแหน่ง

2. **พัฒนาระบบจัดการพนักงาน** (25-30 เม.ย.):
   - การแสดงรายการพนักงานทั้งหมด
   - การแสดงรายละเอียดพนักงาน
   - การเพิ่ม/แก้ไข/ลบข้อมูลพนักงาน

## ตัวชี้วัดความสำเร็จ (KPIs)
1. ลดระยะเวลาในกระบวนการทำงานลง 30%
2. ลดข้อผิดพลาดในการทำงานลง 50%
3. เพิ่มประสิทธิภาพการรายงานผลลัพธ์ 40%
4. ลดต้นทุนการดำเนินงาน 25%
5. เพิ่มความพึงพอใจของผู้ใช้งาน 80%

## ทรัพยากรที่ใช้ในโครงการ
1. ทีมพัฒนา: 5 คน
   - 1 Project Manager
   - 2 Full-stack Developers
   - 1 UX/UI Designer
   - 1 QA Engineer

2. เทคโนโลยี:
   - PHP 8.4.4
   - Laravel 12.8.1
   - Tailwind CSS
   - Alpine.js
   - SQLite (พัฒนา) / MySQL (Production)
   - Redis
   - Docker

3. เครื่องมือ:
   - GitHub (Version Control)
   - Jira (Task Management)
   - Figma (UI/UX Design)
   - Laravel Telescope (Debug)
   - PHPUnit (Testing)

## ความคืบหน้าโดยรวม: 15%
- วันที่อัพเดท: 16 เมษายน 2568
- สถานะ: เสร็จสิ้นเฟส 1 และส่วนสำคัญของเฟส 2
- ดำเนินการตามแผนและเป้าหมายอย่างต่อเนื่อง



## Data base table list
http://ceosofts.test/db-explorer.php ดูฐานข้อมูลในเว็บ

CEOsofts R1 System Check Report

Generated at: 2025-04-15 05:08:56

Database Connection

Status: Connected
Database: /Users/iwasbornforthis/MyProject/CEOsofts_R1/database/ceosofts_db_R1.sqlite
Database Tables

Tables Found: Yes
Total Tables: 56
Table List

activity_logs, attendances, branch_offices, cache, cache_locks, companies, company_user, customers, delivery_note_items, delivery_notes, departments, document_sendings, document_templates, employee_work_shifts, employees, failed_jobs, file_attachments, generated_documents, invoice_items, invoices, job_batches, jobs, leave_types, leaves, migrations, model_has_permissions, model_has_roles, order_items, orders, password_reset_tokens, payment_methods, payments, permissions, permissions_tables, positions, product_categories, products, quotation_items, quotations, receipt_items, receipts, role_has_permissions, roles, scheduled_events, sessions, settings, sqlite_sequence, stock_movements, taxes, telescope_entries, telescope_entries_tags, telescope_monitoring, translations, units, users, work_shifts
Record Counts

Table	Count
employees	9
companies	3
departments	15
positions	15
View Files

Views Directory: Writable
employees/index.blade.php: Exists
employees/create.blade.php: Exists
debug/employee-status.blade.php: Exists
View Paths

/Users/iwasbornforthis/MyProject/CEOsofts_R1/resources/views
Directory Permissions

Storage Directory: Writable
Environment

Laravel Version: 12.8.1
PHP Version: 8.4.6
Environment: local
Actions

Test Employee View
Debug Employees
Employees Page
Home
Laravel Artisan Commands

Run these commands in terminal to fix common issues:

php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
php artisan storage:link
56.78ms
2MB
GET system-check
5 statements were executed6.94ms
database/ceosofts_db_R1.sqlite6.12msSELECT name FROM sqlite_master WHERE type='table' ORDER BY name
database/ceosofts_db_R1.sqlite130μsselect count(*) as aggregate from "employees"
database/ceosofts_db_R1.sqlite250μsselect count(*) as aggregate from "companies"
database/ceosofts_db_R1.sqlite220μsselect count(*) as aggregate from "departments"
database/ceosofts_db_R1.sqlite220μsselect count(*) as aggregate from "positions"


## Dfile structure
php artisan project:structure --output=project_structure_$(date +%Y_%m_%d).txt
หรือ file ที่ export
update 2025-04-16

├── CONTRIBUTING.md (4.06 KB)
├── Dockerfile (857 B)
├── README.md (6.53 KB)
├── all-in-one-fix.sh (16.12 KB)
├── artisan (425 B)
├── artisan-cheatsheet.md (4.97 KB)
├── bootstrap-db.php (1.02 KB)
├── check-companies-structure.php (957 B)
├── check-env.php (1.5 KB)
├── clear-cache.sh (217 B)
├── composer.json (2.37 KB)
├── composer.lock (323.78 KB)
├── create-db.sh (525 B)
├── create-fix-middleware.sh (105 B)
├── docker-compose.debug.yml (307 B)
├── docker-compose.yml (2.9 KB)
├── fix-folders.sh (1.24 KB)
├── fix-org-chart.sh (355 B)
├── intelephense.neon (170 B)
├── middleware-fix-status.md (1.04 KB)
├── optimize-node.sh (1.06 KB)
├── package-lock.json (131.38 KB)
├── package.json (740 B)
├── phpunit.xml (1.09 KB)
├── postcss.config.js (81 B)
├── project_structure.txt (36.71 KB)
├── project_structure_2025_04_12.txt (35.95 KB)
├── project_structure_2025_04_13.txt (20.41 KB)
├── project_structure_2025_04_14.txt (25.65 KB)
├── project_structure_2025_04_15.txt (28.27 KB)
├── project_structure_2025_04_16.txt (29.17 KB)
├── repair-system.sh (2.21 KB)
├── runfix.sh (274 B)
├── tailwind.config.js (3.36 KB)
├── test-data-organization.php (3.85 KB)
├── tinker-commands.php (1.38 KB)
├── vite.config.js (2.26 KB)
├── vscode-settings.json (352 B)
├── คำสั่ง.txt (13.52 KB)
├── Note/
├── X_Project_Info/
│   ├── Roadmap.md (43.1 KB)
│   ├── product_management_guide.md (6.29 KB)
│   └── BIN/
│       ├── Next_Actions.md (3.73 KB)
│       ├── Next_Steps.md (13.61 KB)
│       ├── Project_Status.md (6.27 KB)
│       ├── database-design.md (142.81 KB)
│       ├── database-schema-improvements.md (10.32 KB)
│       ├── database-schema-updated.md (67.02 KB)
│       ├── database-schema.md (18.65 KB)
│       ├── development-workflow.md (16.46 KB)
│       ├── file-structure.md (13.09 KB)
│       ├── migration-guidelines.md (2.29 KB)
│       ├── migration-strategy.md (22.23 KB)
│       ├── pdf-support-structure.md (12.56 KB)
│       ├── technical-specifications.md (24.53 KB)
│       ├── test-plan.md (23.66 KB)
│       └── พัฒนา UI/
│           └── UI-UX.md (27.04 KB)
├── app/
│   ├── helpers.php (1.06 KB)
│   ├── Console/
│   │   ├── Kernel.php (2.51 KB)
│   │   └── Commands/
│   │       ├── CheckDatabaseCommand.php (4.45 KB)
│   │       ├── CheckDatabaseTables.php (1.93 KB)
│   │       ├── CheckDuplicatesCommand.php (4.88 KB)
│   │       ├── CheckMigrationSyntax.php (2.79 KB)
│   │       ├── CheckMigrationsCommand.php (7.55 KB)
│   │       ├── ClearAllCacheCommand.php (5.56 KB)
│   │       ├── ClearDebugbarCommand.php (1.95 KB)
│   │       ├── ClearViewCacheCommand.php (1.89 KB)
│   │       ├── DebugCompaniesCommand.php (3.4 KB)
│   │       ├── FixDuplicateEmployeeFields.php (3.86 KB)
│   │       ├── FixDuplicateEmployeeRecords.php (6.91 KB)
│   │       ├── FixDuplicateImportsCommand.php (4.39 KB)
│   │       ├── FixEmployeeFields.php (1.43 KB)
│   │       ├── FixEmployeeMetadata.php (1.86 KB)
│   │       ├── FixMigrationsCommand.php (4.24 KB)
│   │       ├── FixSQLiteCompatibilityCommand.php (7.26 KB)
│   │       ├── FixSQLiteMigrationCommand.php (8.47 KB)
│   │       ├── ImportProductCategoriesCommand.php (3.33 KB)
│   │       ├── ListAllTablesCommand.php (3.13 KB)
│   │       ├── MigrateSkipCommand.php (2.52 KB)
│   │       ├── MigrateSkipMultipleCommand.php (2.63 KB)
│   │       └── ProjectStructureCommand.php (8.09 KB)
│   ├── Domain/
│   │   ├── DocumentGeneration/
│   │   │   └── Models/
│   │   ├── FileStorage/
│   │   │   └── Models/
│   │   ├── HumanResources/
│   │   │   └── Models/
│   │   ├── Inventory/
│   │   │   └── Models/
│   │   ├── Organization/
│   │   │   ├── Actions/
│   │   │   ├── Models/
│   │   │   └── Services/
│   │   ├── Sales/
│   │   │   └── Models/
│   │   ├── Settings/
│   │   │   ├── Models/
│   │   │   └── Services/
│   │   └── Shared/
│   │       └── Traits/
│   ├── Encryption/
│   │   ├── CustomEncrypter.php (7.37 KB)
│   │   └── SimpleEncrypter.php (6.16 KB)
│   ├── Http/
│   │   ├── Kernel.php (2.71 KB)
│   │   ├── Controllers/
│   │   │   ├── ComingSoonController.php (1.66 KB)
│   │   │   ├── CompaniesController.php (9.57 KB)
│   │   │   ├── CompanyController.php (1.28 KB)
│   │   │   ├── Controller.php (77 B)
│   │   │   ├── CustomerController.php (10.86 KB)
│   │   │   ├── DashboardController.php (654 B)
│   │   │   ├── DebugCompanyController.php (2.99 KB)
│   │   │   ├── DebugController.php (2.23 KB)
│   │   │   ├── DepartmentController.php (8.01 KB)
│   │   │   ├── EmployeeController.php (18.54 KB)
│   │   │   ├── ExecutiveDashboardController.php (5.83 KB)
│   │   │   ├── HomeController.php (522 B)
│   │   │   ├── OrderController.php (2.02 KB)
│   │   │   ├── OrganizationStructureController.php (14.61 KB)
│   │   │   ├── PositionController.php (7.3 KB)
│   │   │   ├── PositionsController.php (5.66 KB)
│   │   │   ├── ProductCategoryController.php (11.87 KB)
│   │   │   ├── ProductController.php (10.09 KB)
│   │   │   ├── QuotationController.php (2.13 KB)
│   │   │   ├── SystemCheckController.php (9.37 KB)
│   │   │   ├── TestController.php (808 B)
│   │   │   ├── Auth/
│   │   │   └── Organization/
│   │   ├── Livewire/
│   │   │   ├── CompanySelector.php (0 B)
│   │   │   ├── Company/
│   │   │   ├── Components/
│   │   │   ├── Dashboard/
│   │   │   └── Department/
│   │   ├── Middleware/
│   │   │   ├── EncryptCookies.php (2.34 KB)
│   │   │   ├── EnsureCompanyAccess.php (1.24 KB)
│   │   │   ├── RedirectIfAuthenticated.php (754 B)
│   │   │   └── SetDefaultCompany.php (770 B)
│   │   └── Requests/
│   │       ├── StoreEmployeeRequest.php (6.17 KB)
│   │       ├── UpdateEmployeeRequest.php (4.96 KB)
│   │       └── Organization/
│   ├── Infrastructure/
│   │   ├── MultiTenancy/
│   │   │   ├── HasCompanyScope.php (1.4 KB)
│   │   │   └── Exceptions/
│   │   └── Support/
│   │       └── Services/
│   ├── Livewire/
│   │   └── Dashboard/
│   │       └── StatsOverview.php (0 B)
│   ├── Models/
│   │   ├── BranchOffice.php (1.79 KB)
│   │   ├── Company.php (4.43 KB)
│   │   ├── Customer.php (5.2 KB)
│   │   ├── Department.php (3.23 KB)
│   │   ├── Employee.php (4.89 KB)
│   │   ├── LeaveType.php (2.19 KB)
│   │   ├── Order.php (1.82 KB)
│   │   ├── Position.php (1.45 KB)
│   │   ├── Product.php (2.27 KB)
│   │   ├── ProductCategory.php (2.19 KB)
│   │   ├── Quotation.php (1.66 KB)
│   │   ├── StockMovement.php (1.2 KB)
│   │   ├── Unit.php (1013 B)
│   │   ├── User.php (1.16 KB)
│   │   └── WorkShift.php (5.27 KB)
│   ├── Policies/
│   │   ├── CompanyPolicy.php (1.27 KB)
│   │   ├── DepartmentPolicy.php (1.31 KB)
│   │   └── PositionPolicy.php (3.3 KB)
│   ├── Providers/
│   │   ├── AppServiceProvider.php (991 B)
│   │   ├── AuthServiceProvider.php (686 B)
│   │   ├── CustomEncryptionProvider.php (1.49 KB)
│   │   ├── CustomEncryptionServiceProvider.php (558 B)
│   │   ├── EventServiceProvider.php (884 B)
│   │   ├── OrganizationServiceProvider.php (338 B)
│   │   ├── RouteServiceProvider.php (2.22 KB)
│   │   ├── SimpleEncryptionServiceProvider.php (887 B)
│   │   └── TelescopeServiceProvider.php (2.23 KB)
│   ├── Scopes/
│   │   └── CompanyScope.php (800 B)
│   ├── Services/
│   ├── Shared/
│   │   └── Traits/
│   │       └── HasCompanyScope.php (1.46 KB)
│   ├── Traits/
│   │   └── CompanyScope.php (1.4 KB)
│   └── View/
│       └── Components/
│           ├── AppLayout.php (296 B)
│           ├── BreadcrumbNav.php (1.41 KB)
│           ├── GuestLayout.php (310 B)
│           ├── SearchFilter.php (463 B)
│           └── TreeView.php (455 B)
├── backups/
├── bootstrap/
│   ├── app.php (513 B)
│   ├── providers.php (64 B)
│   └── cache/
│       ├── packages.php (2.02 KB)
│       └── services.php (21.9 KB)
├── config/
│   ├── app-fix.php (7.57 KB)
│   ├── app.local.php (84 B)
│   ├── app.php (2.18 KB)
│   ├── app.php.bak (4.16 KB)
│   ├── app.php.persistent-fix-backup (4.16 KB)
│   ├── auth.php (3.93 KB)
│   ├── cache.php (3.39 KB)
│   ├── database.php (6.02 KB)
│   ├── debugbar.php (2.67 KB)
│   ├── filesystems.php (2.44 KB)
│   ├── logging.php (4.22 KB)
│   ├── mail.php (3.46 KB)
│   ├── queue.php (3.73 KB)
│   ├── services.php (1.01 KB)
│   ├── session.php (7.67 KB)
│   └── telescope.php (6.67 KB)
├── database/
│   ├── ceosofts_db_R1 (0 B)
│   ├── ceosofts_db_R1.sqlite (1.79 MB)
│   ├── check-companies.php (2.17 KB)
│   ├── check-db-connection.php (3.58 KB)
│   ├── create-database.sh (928 B)
│   ├── create-docker-db.sh (1.39 KB)
│   ├── database.sqlite (92 KB)
│   ├── reset-database.php (2.03 KB)
│   ├── setup-local-db.sh (0 B)
│   ├── factories/
│   │   ├── CompanyFactory.php (1013 B)
│   │   ├── ModelNameFactory.php (416 B)
│   │   ├── UserFactory.php (1.05 KB)
│   │   └── Domain/
│   │       └── Organization/
│   ├── legacy/
│   │   └── 2024_08_04_000001_fix_duplicate_migration_issues.php (6 KB)
│   ├── migrations/
│   │   ├── 0001_01_01_00001_create_users_table.php (5.31 KB)
│   │   ├── 0001_01_01_00002_create_cache_table.php (849 B)
│   │   ├── 0001_01_01_00003_create_jobs_table.php (1.77 KB)
│   │   ├── 0001_01_01_00004_create_companies_table.php (7.92 KB)
│   │   ├── 0001_01_01_00005_create_activity_logs_table.php (1.42 KB)
│   │   ├── 0001_01_01_00006_create_customers_table.php (5.9 KB)
│   │   ├── 0001_01_01_00007_create_departments_table.php (1.38 KB)
│   │   ├── 0001_01_01_00008_create_positions_table.php (1.48 KB)
│   │   ├── 0001_01_01_00009_create_branch_offices_table.php (1.4 KB)
│   │   ├── 0001_01_01_00010_create_employees_table.php (9.9 KB)
│   │   ├── 0001_01_01_00011_create_leave_types_table.php (8.33 KB)
│   │   ├── 0001_01_01_00012_create_work_shifts_table.php (7.95 KB)
│   │   ├── 0001_01_01_00013_create_quotations_table.php (6.05 KB)
│   │   ├── 0001_01_01_00014_create_employee_work_shifts_table.php (5.54 KB)
│   │   ├── 0001_01_01_00015_create_document_templates_table.php (6.63 KB)
│   │   ├── 0001_01_01_00016_create_orders_table.php (1.34 KB)
│   │   ├── 0001_01_01_00017_create_generated_documents_table.php (5.09 KB)
│   │   ├── 0001_01_01_00018_create_invoices_table.php (1.57 KB)
│   │   ├── 0001_01_01_00019_create_products_table.php (11 KB)
│   │   ├── 0001_01_01_00020_create_receipts_table.php (6.26 KB)
│   │   ├── 0001_01_01_00021_create_permissions_tables.php (1.82 KB)
│   │   ├── 0001_01_01_00022_create_roles_tables.php (4.41 KB)
│   │   ├── 0001_01_01_00023_create_stock_movements_table.php (9.33 KB)
│   │   ├── 0001_01_01_00024_create_translations_table.php (1.89 KB)
│   │   ├── 0001_01_01_00025_create_units_table.php (4.96 KB)
│   │   ├── 0001_01_01_00026_create_document_sendings_table.php (5.72 KB)
│   │   ├── 0001_01_01_00027_create_file_attachments_table.php (5.27 KB)
│   │   ├── 0001_01_01_00028_create_settings_table.php (5.79 KB)
│   │   ├── 0001_01_01_00029_create_receipt_items_table.php (3.29 KB)
│   │   ├── 0001_01_01_00030_create_taxes_table.php (1.52 KB)
│   │   ├── 0001_01_01_00031_create_scheduled_events_table.php (7.86 KB)
│   │   ├── 0001_01_01_00032_update_company_user_table.php (3.86 KB)
│   │   ├── 0001_01_01_00033_add_missing_columns_to_product_categories_table.php (1.54 KB)
│   │   ├── 0001_01_01_00034_modify_orders_quotation_constraint.php (5.46 KB)
│   │   ├── 0001_01_01_00035_add_unit_to_order_items_table.php (776 B)
│   │   ├── 0001_01_01_00036_add_price_to_order_items_table.php (1.38 KB)
│   │   ├── 0001_01_01_00037_create_document_sendings_table_if_not_exists.php (1.8 KB)
│   │   ├── 0001_01_01_00038_create_translations_table_clean.php (3.83 KB)
│   │   ├── 0001_01_01_00040_create_missing_tables.php (1.68 KB)
│   │   ├── 0001_01_01_00041_create_permissions_tables.php (2.83 KB)
│   │   ├── 0001_01_01_00042_create_telescope_entries_table.php (2.02 KB)
│   │   ├── 0001_01_01_00043_create_permissions_tables.php (553 B)
│   │   └── 0001_01_01_00044_add_parent_id_to_departments_table.php (2.79 KB)
│   ├── seeders/
│   │   ├── ActivityLogSeeder.php (2.16 KB)
│   │   ├── AdminUserSeeder.php (1.69 KB)
│   │   ├── BranchOfficeSeeder.php (2.98 KB)
│   │   ├── CompanySeeder.php (4.86 KB)
│   │   ├── CustomerSeeder.php (6.15 KB)
│   │   ├── DatabaseSeeder.php (1.95 KB)
│   │   ├── DepartmentSeeder.php (3.66 KB)
│   │   ├── DocumentSendingSeeder.php (5.83 KB)
│   │   ├── DocumentTemplateSeeder.php (4.31 KB)
│   │   ├── EmployeeSeeder.php (13.44 KB)
│   │   ├── EmployeeWorkShiftSeeder.php (2.32 KB)
│   │   ├── FileAttachmentSeeder.php (1.81 KB)
│   │   ├── GeneratedDocumentSeeder.php (3.41 KB)
│   │   ├── InventorySeeder.php (0 B)
│   │   ├── InvoiceSeeder.php (3.59 KB)
│   │   ├── JobSeeder.php (1.85 KB)
│   │   ├── LeaveTypeSeeder.php (3.43 KB)
│   │   ├── OrderSeeder.php (10.38 KB)
│   │   ├── OrganizationSeeder.php (0 B)
│   │   ├── PermissionSeeder.php (444 B)
│   │   ├── PositionSeeder.php (4.28 KB)
│   │   ├── ProductCategorySeeder.php (4.33 KB)
│   │   ├── ProductSeeder.php (20.95 KB)
│   │   ├── QuotationSeeder.php (5.07 KB)
│   │   ├── ReceiptItemSeeder.php (2.47 KB)
│   │   ├── ReceiptSeeder.php (1.89 KB)
│   │   ├── RoleAndPermissionSeeder.php (5.24 KB)
│   │   ├── RoleSeeder.php (374 B)
│   │   ├── RolesAndPermissionsSeeder.php (0 B)
│   │   ├── ScheduledEventSeeder.php (16 KB)
│   │   ├── SettingSeeder.php (4.41 KB)
│   │   ├── SettingsSeeder.php (0 B)
│   │   ├── SimpleCompanySeeder.php (4.44 KB)
│   │   ├── StockMovementSeeder.php (3.71 KB)
│   │   ├── SystemSettingSeeder.php (3.65 KB)
│   │   ├── TaxSeeder.php (1.96 KB)
│   │   ├── TestCompanySeeder.php (5.8 KB)
│   │   ├── TranslationSeeder.php (7.29 KB)
│   │   ├── UnitSeeder.php (4.16 KB)
│   │   ├── UserSeeder.php (1.08 KB)
│   │   ├── UsersSeeder.php (0 B)
│   │   └── WorkShiftSeeder.php (6.1 KB)
│   └── sql/
├── docker/
│   └── mysql/
│       └── create-database.sql (438 B)
├── docs/
│   ├── cache-management.md (3.16 KB)
│   ├── encryption-troubleshooting.md (7 KB)
│   ├── error-solutions.md (0 B)
│   ├── installation-troubleshooting.md (0 B)
│   ├── laravel-11-livewire-guide.md (8.66 KB)
│   ├── manual-fix-livewire.md (0 B)
│   ├── performance-optimization.md (5.26 KB)
│   ├── project-structure.md (3.27 KB)
│   ├── setup-guide.md (5.13 KB)
│   ├── telescope-setup.md (5.39 KB)
│   ├── tinker-guide.md (3.65 KB)
│   ├── troubleshooting-guide.md (6.13 KB)
│   └── vscode-optimization.md (4.99 KB)
├── public/
│   ├── check-db.php (1.86 KB)
│   ├── db-check.php (2.84 KB)
│   ├── favicon.ico (0 B)
│   ├── hot (17 B)
│   ├── index.php (543 B)
│   ├── robots.txt (24 B)
│   ├── test.html (399 B)
│   ├── db-explorer.php (47.59 KB)
│   ├── build/
│   │   ├── manifest.json (658 B)
│   │   └── assets/
│   │       ├── app-BJOqzKiO.js (78.39 KB)
│   │       ├── app-BycLiu1R.css (51.49 KB)
│   │       ├── index-DW5s5VCp.js (15.63 KB)
│   │       ├── package-BDqD1zQI.json (4.78 KB)
│   │       └── vendor-l0sNRNKZ.js (1 B)
│   ├── img/
│   │   ├── ceo_logo9.ico (14.73 KB)
│   │   ├── logo-sm.svg (258 B)
│   │   ├── logo.svg (269 B)
│   │   ├── undraw_profile.svg (2.75 KB)
│   │   ├── undraw_profile_1.svg (2.11 KB)
│   │   ├── undraw_profile_2.svg (2.57 KB)
│   │   └── undraw_profile_3.svg (2.67 KB)
│   └── storage/
│       ├── company_1/
│       │   └── files/
│       ├── company_2/
│       │   └── files/
│       └── company_3/
│           └── files/
├── resources/
│   ├── css/
│   │   ├── app-fix.css (1.25 KB)
│   │   └── app.css (1.72 KB)
│   ├── docs/
│   ├── js/
│   │   ├── app.js (999 B)
│   │   ├── auth.js (698 B)
│   │   ├── bootstrap.js (127 B)
│   │   └── company-selector.js (3.34 KB)
│   ├── templates/
│   │   └── migration_template.php (2.23 KB)
│   └── views/
│       ├── coming-soon.blade.php (1.34 KB)
│       ├── company-switch-test.blade.php (1.97 KB)
│       ├── home.blade.php (0 B)
│       ├── test-employee-view.blade.php (1021 B)
│       ├── welcome.blade.php (26.81 KB)
│       ├── auth/
│       │   ├── forgot-password.blade.php (5.5 KB)
│       │   ├── login.blade.php (6.82 KB)
│       │   ├── register.blade.php (8.21 KB)
│       │   └── reset-password.blade.php (6.61 KB)
│       ├── coming-soon/
│       ├── components/
│       │   ├── app-layout.blade.php (5.2 KB)
│       │   ├── application-logo.blade.php (83 B)
│       │   ├── auth-session-status.blade.php (147 B)
│       │   ├── auth-validation-errors.blade.php (518 B)
│       │   ├── breadcrumb-nav.blade.php (1.96 KB)
│       │   ├── button.blade.php (1.2 KB)
│       │   ├── card.blade.php (400 B)
│       │   ├── checkbox.blade.php (263 B)
│       │   ├── company-card.blade.php (3.95 KB)
│       │   ├── company-filter.blade.php (1.88 KB)
│       │   ├── company-selector.blade.php (1.27 KB)
│       │   ├── dropdown-link.blade.php (204 B)
│       │   ├── dropdown.blade.php (1.22 KB)
│       │   ├── employee-company-selector.blade.php (2.41 KB)
│       │   ├── employee-metadata.blade.php (714 B)
│       │   ├── flash-messages.blade.php (2.24 KB)
│       │   ├── footer.blade.php (947 B)
│       │   ├── guest-layout.blade.php (1.08 KB)
│       │   ├── input-error.blade.php (234 B)
│       │   ├── input-label.blade.php (161 B)
│       │   ├── input.blade.php (231 B)
│       │   ├── label.blade.php (142 B)
│       │   ├── nav-group.blade.php (0 B)
│       │   ├── nav-item.blade.php (0 B)
│       │   ├── navbar.blade.php (6.27 KB)
│       │   ├── primary-button.blade.php (435 B)
│       │   ├── radio.blade.php (252 B)
│       │   ├── search-filter.blade.php (87 B)
│       │   ├── secondary-button.blade.php (393 B)
│       │   ├── select.blade.php (338 B)
│       │   ├── sidebar.blade.php (9.34 KB)
│       │   ├── text-input.blade.php (311 B)
│       │   ├── textarea.blade.php (336 B)
│       │   ├── tree-view.blade.php (62 B)
│       │   ├── dropdown/
│       │   ├── form/
│       │   ├── icons/
│       │   ├── sidebar/
│       │   └── ui/
│       ├── customers/
│       │   ├── create.blade.php (36.21 KB)
│       │   ├── edit.blade.php (40.18 KB)
│       │   ├── index.blade.php (14.77 KB)
│       │   └── show.blade.php (46.78 KB)
│       ├── dashboard/
│       │   ├── index.blade.php (4.61 KB)
│       │   └── test-view.blade.php (1.36 KB)
│       ├── debug/
│       │   ├── company-debug.blade.php (2.46 KB)
│       │   └── employee-status.blade.php (5.87 KB)
│       ├── errors/
│       │   └── company-required.blade.php (0 B)
│       ├── executive/
│       │   └── dashboard.blade.php (15.77 KB)
│       ├── layouts/
│       │   ├── app.blade.php (1.22 KB)
│       │   ├── basic.blade.php (792 B)
│       │   ├── guest.blade.php (840 B)
│       │   └── navigation.blade.php (24.05 KB)
│       ├── livewire/
│       │   ├── company-selector.blade.php (0 B)
│       │   ├── components/
│       │   ├── dashboard/
│       │   └── department/
│       ├── orders/
│       │   └── index.blade.php (736 B)
│       ├── organization/
│       │   ├── companies/
│       │   ├── departments/
│       │   ├── employees/
│       │   ├── partials/
│       │   ├── positions/
│       │   └── structure/
│       ├── products/
│       │   ├── create.blade.php (49.19 KB)
│       │   ├── edit.blade.php (37.89 KB)
│       │   ├── index.blade.php (16.34 KB)
│       │   ├── show.blade.php (53.18 KB)
│       │   └── categories/
│       └── quotations/
│           └── index.blade.php (742 B)
├── routes/
│   ├── admin.php (0 B)
│   ├── api.php (876 B)
│   ├── auth.php (937 B)
│   ├── channels.php (558 B)
│   ├── console.php (592 B)
│   ├── web.php (15.07 KB)
│   └── domains/
│       ├── finance.php (0 B)
│       ├── human-resources.php (0 B)
│       ├── inventory.php (0 B)
│       ├── organization.php (1.93 KB)
│       ├── sales.php (0 B)
│       └── settings.php (0 B)
├── storage/
│   ├── app/
│   │   ├── private/
│   │   └── public/
│   │       ├── company_1/
│   │       ├── company_2/
│   │       └── company_3/
│   ├── debugbar/
│   ├── framework/
│   │   ├── cache/
│   │   │   └── data/
│   │   ├── sessions/
│   │   │   └── ANjGBeEN1Rw5CVbbElpUBOnRYo5Ji9BpwWcMveWJ (341 B)
│   │   ├── testing/
│   │   └── views/
│   │       ├── 04a14b7442f6fc338ab7939f272e43a8.php (1.38 KB)
│   │       ├── 2d82d6f51f043e617f681965f9eb1388.php (515 B)
│   │       ├── 30baac441ee757f7eedec098c02ea9ce.php (2.76 KB)
│   │       ├── 3343465d2ab428739d74303f16f2dfaf.php (707 B)
│   │       ├── 3c1b680d61bc7a7bb7df14c8a678a9de.php (10.36 KB)
│   │       ├── 4de3d57ff3c17c3aa4d930e2c2cbfbf0.php (531 B)
│   │       ├── 57f9c964972f01fe748c5866ed076e5d.php (10.88 KB)
│   │       ├── 6558acd15011832cd6aa15743960d38b.php (3.02 KB)
│   │       ├── 6ee15c368368456900bf99d26300129e.php (1.27 KB)
│   │       ├── 70991d3b6e4e4e196e729160767e6c75.php (9.42 KB)
│   │       ├── 7aaef36b4137424490c562d155e40830.php (1.11 KB)
│   │       ├── 7f6cf0ade3c425d8e7e502e196345c4b.php (5.01 KB)
│   │       ├── 820f920f42080c93c6ed0871e644885b.php (8.12 KB)
│   │       ├── 8453bc743b2f98c83e9732ab4ed71306.php (2.24 KB)
│   │       ├── 858fdf59bcc122a2851e83ab7a9bb93c.php (73.54 KB)
│   │       ├── 89ed97ccaf061226ee275a1d70bddb21.php (24.7 KB)
│   │       ├── 8d9a78a1a85c6160f76f39ae8493572b.php (1.27 KB)
│   │       ├── 979c67e77d89f33ea3d5716dee402eee.php (1.24 KB)
│   │       ├── a19789af8775f146a579fc6d6e1766e3.php (352 B)
│   │       ├── a26d536b72e080b9ea65f354c518c765.php (2.04 KB)
│   │       ├── af3a3652b0b2098e4ebc1694d7db7475.php (584 B)
│   │       ├── b453ba8120863d30a079c75808bfa61a.php (2.89 KB)
│   │       ├── bd8e8ff4c1d48a5496397a972c7cbdcf.php (20.66 KB)
│   │       ├── c315ed2f896bc7b209c276d2926f2b6c.php (509 B)
│   │       ├── cbfecf69c34656be6f3e1f0c2c48dd5f.php (1.09 KB)
│   │       ├── da6c417ae5ff0124e4d3116a0c93d1f7.php (11.39 KB)
│   │       ├── f2d98475be4395e00d133e49a1ca34cc.php (7.02 KB)
│   │       ├── f9bf975275e39779d2fa08c87d4e5e49.php (616 B)
│   │       ├── fc3fc22c8e542b24b08dace9c21cc510.php (632 B)
│   │       └── fcb1329077faf0f5f28904d12d5cbd3f.php (37.02 KB)
│   └── logs/
│       ├── encryption-debug.log (704 B)
│       ├── encryption-error.log (0 B)
│       └── laravel.log (14.04 MB)
├── tests/
│   ├── DuskTestCase.php (1.41 KB)
│   ├── TestCase.php (142 B)
│   ├── Browser/
│   │   ├── ExampleTest.php (432 B)
│   │   ├── Components/
│   │   ├── Pages/
│   │   │   ├── HomePage.php (576 B)
│   │   │   └── Page.php (358 B)
│   │   ├── console/
│   │   ├── screenshots/
│   │   └── source/
│   ├── Feature/
│   │   ├── CompanyManagementTest.php (381 B)
│   │   ├── DepartmentManagementTest.php (384 B)
│   │   └── ExampleTest.php (359 B)
│   └── Unit/
│       ├── DepartmentTest.php (0 B)
│       └── ExampleTest.php (243 B)
└── tools/
    ├── README.md (75 B)
    ├── auto-fix-laravel.php (5.17 KB)
    ├── bootstrap-fix-minimal.php (3.64 KB)
    ├── bootstrap-fix.php (3.79 KB)
    ├── check-migrations.php (161 B)
    ├── cleanup-cache.sh (2.06 KB)
    ├── cleanup-debugbar.sh (1.26 KB)
    ├── cleanup.sh (9.51 KB)
    ├── clear-all-cache.sh (718 B)
    ├── cli.sh (27.09 KB)
    ├── complete-reset.php (116 B)
    ├── create-permission-tables.php (4.7 KB)
    ├── db-explorer.php (47.59 KB)
    ├── debug-company-seeder.php (1.94 KB)
    ├── find-models.php (2.17 KB)
    ├── fix-autoload-issues.sh (1.26 KB)
    ├── fix-permissions-tables.php (4.63 KB)
    ├── fix-permissions.php (116 B)
    ├── fix-sqlite-migrations.sh (852 B)
    ├── fix-vite-build.sh (2.22 KB)
    ├── rename-migrations.sh (4.86 KB)
    ├── renumber-all-migrations.sh (8.13 KB)
    ├── reset-system.php (131 B)
    ├── session-cache-fix.php (3.75 KB)
    └── test_app.php (589 B)
