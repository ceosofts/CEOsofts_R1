# ขั้นตอนต่อไปสำหรับโครงการ CEOsofts

## 1. การตั้งค่าสภาพแวดล้อมการพัฒนา (2-3 สัปดาห์)

### งานที่ต้องทำ:

-   [x] ติดตั้งและกำหนดค่า Docker สำหรับ local development
    -   [x] สร้างไฟล์ docker-compose.yml พร้อมกับบริการ (PHP 8.4, MySQL, Redis)
    -   [x] กำหนดค่า Laravel Sail เพื่อช่วยในการจัดการ Docker
    -   [x] ตรวจสอบความเข้ากันได้กับ PHP 8.4.4 และ extensions ที่จำเป็น
-   [x] สร้างไฟล์ .env.local สำหรับการพัฒนาบนเครื่อง host
    -   [x] กำหนดค่า DB_HOST=127.0.0.1 สำหรับเชื่อมต่อฐานข้อมูล local
    -   [x] ตั้งค่าให้ใช้ root user สำหรับการเชื่อมต่อ MySQL
-   [ ] สร้างโครงสร้างโปรเจคตามที่ออกแบบไว้ใน file-structure.md
    -   จัดเตรียมโครงสร้าง Domain-Driven Design
    -   สร้างโฟลเดอร์สำหรับแต่ละ Domain
-   [x] ตั้งค่า Git repository และ branching strategy
    -   [x] สร้าง main branch และ เชื่อมต่อกับ GitHub
    -   [x] สร้าง local-main branch สำหรับการพัฒนา
    -   ตั้งค่า Git hooks สำหรับ code linting
-   [ ] ติดตั้ง dependency packages ที่จำเป็น
    -   Laravel Sanctum สำหรับ authentication
    -   Spatie Permission สำหรับ RBAC
    -   Laravel Livewire หรือ Inertia.js (ขึ้นอยู่กับการตัดสินใจสุดท้าย)
    -   ตรวจสอบ compatibility กับ PHP 8.4
-   [ ] ตั้งค่า CI/CD pipeline เบื้องต้นด้วย GitHub Actions
    -   Automated testing
    -   Code quality checks
    -   Static analysis
    -   ตั้งค่า workflows ให้ใช้ PHP 8.4.4

## 2. การพัฒนาโครงสร้างพื้นฐาน (3-4 สัปดาห์)

### งานที่ต้องทำ:

-   [ ] สร้าง migrations สำหรับตาราง core tables
    -   Users
    -   Roles & Permissions
    -   Companies (สำหรับ multi-tenancy)
-   [ ] พัฒนาระบบ Multi-tenant
    -   สร้าง Global Scope สำหรับการกรองข้อมูลตาม company_id
    -   สร้าง Middleware สำหรับการตรวจสอบ company access
    -   พัฒนา trait HasCompanyScope สำหรับ models
-   [ ] พัฒนาระบบ Authentication
    -   สร้างหน้า login/register
    -   ตั้งค่า Laravel Sanctum
    -   พัฒนาระบบ user session management
-   [ ] พัฒนาระบบ Authorization
    -   ตั้งค่า Roles & Permissions
    -   สร้าง Policies สำหรับ resources
    -   พัฒนาระบบตรวจสอบสิทธิ์

## 3. การพัฒนาโดเมนแรก: Organization และ Settings (4-5 สัปดาห์)

### Organization Domain:

-   [ ] สร้าง migrations และ models
    -   Company
    -   Department
    -   Position
    -   Branch Office
-   [ ] พัฒนา repositories และ services
    -   CRUD operations สำหรับแต่ละ entity
    -   Business logic ที่เกี่ยวข้อง
-   [ ] พัฒนา UI components
    -   Forms สำหรับการจัดการ entities
    -   Data tables สำหรับการแสดงข้อมูล
    -   Detail views

### Settings Domain:

-   [ ] สร้าง migrations และ models
    -   Users
    -   Roles
    -   Permissions
    -   Settings
-   [ ] พัฒนา user management system
    -   User CRUD
    -   Role assignment
    -   Permission management
-   [ ] พัฒนาระบบการตั้งค่า
    -   Company settings
    -   System preferences
    -   Prefixes and numbering systems

## 4. การตั้งค่าการทดสอบ (2-3 สัปดาห์)

### งานที่ต้องทำ:

-   [ ] ติดตั้งและกำหนดค่า PHPUnit หรือ Pest
    -   ตั้งค่า test database
    -   สร้าง test helpers และ factories
    -   ตรวจสอบความเข้ากันได้กับ PHP 8.4.4
-   [ ] สร้าง unit tests สำหรับ core services
    -   Authentication tests
    -   Multi-tenant tests
    -   Permission tests
-   [ ] พัฒนา feature tests สำหรับ API endpoints
    -   Organization domain endpoints
    -   Settings domain endpoints
-   [ ] ตั้งค่า automated testing ใน CI pipeline
    -   Test reports
    -   Coverage reports

## 5. เริ่มต้นพัฒนาโดเมนถัดไป: Human Resources (4-5 สัปดาห์)

### งานที่ต้องทำ:

-   [ ] สร้าง migrations และ models
    -   Employee
    -   Work Shift
    -   Attendance
    -   Leave
-   [ ] พัฒนา repositories และ services
    -   Employee management
    -   Attendance tracking
    -   Leave management
-   [ ] พัฒนา UI components
    -   Employee forms
    -   Attendance recording interface
    -   Leave request system
    -   Calendar views

## Timeline โดยสังเขป

```
สัปดาห์ 1-3:   การตั้งค่าสภาพแวดล้อมการพัฒนา
สัปดาห์ 3-7:   การพัฒนาโครงสร้างพื้นฐาน
สัปดาห์ 7-12:  การพัฒนาโดเมนแรก (Organization และ Settings)
สัปดาห์ 12-15: การตั้งค่าการทดสอบ
สัปดาห์ 15-20: การพัฒนาโดเมน Human Resources
สัปดาห์ 20+:   การพัฒนาโดเมนที่เหลือตามลำดับความสำคัญ
```

## ปัญหาที่อาจเกิดขึ้นและการแก้ไข

1. **การออกแบบ multi-tenant อาจซับซ้อน**

    - พิจารณาใช้ package ช่วยเช่น stancl/tenancy หรือ spatie/laravel-multitenancy
    - เริ่มจากการทำ POC (Proof of Concept) ในขนาดเล็กก่อน

2. **การออกแบบ DDD มีความซับซ้อนสูงในช่วงเริ่มต้น**

    - เริ่มจากโครงสร้างพื้นฐาน แล้วค่อยๆ refactor
    - ใช้ resource-based controllers ในช่วงแรกแล้วค่อยมุ่งสู่ domain services

3. **การจัดการความซับซ้อนของ permissions**

    - พิจารณาการจัดกลุ่ม permissions เป็นหมวดหมู่
    - สร้าง UI ที่ใช้งานง่ายสำหรับการจัดการสิทธิ์

4. **ความเข้ากันได้กับ PHP 8.4.4**

    - ตรวจสอบว่า Laravel version ที่ใช้รองรับ PHP 8.4
    - ตรวจสอบ packages และ dependencies ทั้งหมด
    - อาจต้องอัพเดทบาง packages หรือหา alternatives หากไม่รองรับ PHP 8.4

5. **การเชื่อมต่อฐานข้อมูล**
    - พบปัญหาเชื่อมต่อฐานข้อมูล เมื่อรัน `php artisan db:show` โดยตรง
    - ต้องใช้ `php -d variables_order=EGPCS artisan db:show --env=local` เพื่อเชื่อมต่อกับ local MySQL
    - หรือ สร้าง alias `artisan-local="php -d variables_order=EGPCS artisan --env=local"` เพื่อความสะดวก
    - MySQL ในคอนเทนเนอร์ Docker ยังไม่สามารถเข้าถึง database 'ceosofts' ได้

## สิ่งที่ต้องตัดสินใจ

1. **Frontend framework**: ตัดสินใจระหว่าง Livewire + Alpine.js หรือ Inertia.js + Vue
2. **Database**: เลือกระหว่าง MySQL หรือ PostgreSQL (รองรับทั้ง MySQL และ PostgreSQL ตาม PHP extensions ที่ติดตั้ง)
3. **File storage**: ตัดสินใจเรื่อง storage provider (Local, S3, DigitalOcean Spaces)
4. **UI Component Library**: เลือก Tailwind component library ที่จะใช้
5. **PHP Version**: พิจารณาว่าจะใช้ PHP 8.4.4 ที่มีอยู่หรือลดเวอร์ชันลงเพื่อความเข้ากันได้กับ packages ที่ใช้

## สภาพแวดล้อมปัจจุบัน

### Docker Environment

-   **Docker Version:** 27.5.1
-   **Docker Compose Version:** v2.32.4
-   **Running Containers:**
    -   Laravel Application (sail-8.4/app)
    -   MySQL 8.0
    -   Redis (alpine)
    -   Minio (for object storage)
    -   Mailpit (for email testing)
-   **Platform:** OrbStack

### Git Environment

-   **Git Version:** 2.39.5 (Apple Git-154)
-   **Repository:** เชื่อมต่อกับ GitHub (ceosofts/CEOsofts_R1)
-   **Branches:**
    -   main (เชื่อมต่อกับ origin/main)
    -   local-main (เชื่อมต่อกับ origin/local-main)
-   **User Configuration:**
    -   user.name = ceosofts
    -   user.email = ceosofts@gmail.com
-   **Credentials:** Using credential.helper = store

### PHP Environment

-   PHP Version: 8.4.4
-   Composer Version: 2.8.6 (2025-02-25)

### PHP Modules ที่มีอยู่

-   **Database:** mysqli, mysqlnd, pdo_mysql, pgsql, pdo_pgsql, sqlite3, pdo_sqlite
-   **Web/API:** curl, soap, sockets, ftp
-   **Security:** openssl, sodium, hash
-   **Image Processing:** gd, exif
-   **Compression:** bz2, zip, zlib
-   **Internationalization:** intl, mbstring, iconv
-   **Caching:** opcache
-   **Other Key Modules:** json, xml, ldap, posix, pcntl

### Composer Configuration

-   **Optimization:** optimize-autoloader = true, sort-packages = true
-   **Security:** secure-http = true
-   **Cache Settings:** cache-ttl = 15552000 (180 days)
-   **Allowed Plugins:** pestphp/pest-plugin, php-http/discovery

### Node.js Environment

-   **NPM Version:** 10.9.2
-   **Global NPM Packages:**
    -   corepack@0.31.0
    -   npm@10.9.2

### Git Environment

-   **Git Version:** 2.39.5 (Apple Git-154)
-   **User Configuration:**
    -   user.name = ceosofts
    -   user.email = ceosofts@gmail.com
-   **Default Branch:** main
-   **Credentials:** Using macOS keychain and store helper

### Global Composer Packages ที่มีอยู่

-   **Development Tools:**
    -   friendsofphp/php-cs-fixer (3.75.0)
    -   vimeo/psalm (6.10.0)
    -   nikic/php-parser (5.4.0)
-   **Asynchronous Programming:**
    -   amphp/\* modules
    -   react/\* modules
    -   revolt/event-loop
-   **Utility Libraries:**
    -   symfony/\* components
    -   league/uri
    -   spatie/array-to-xml

### Laravel Framework

-   **Laravel Version:** 12.7.2
-   **Environment:** localw)
-   **Debug Mode:** ENABLEDd)
-   **Timezone:** UTC\*\*
-   **Locale:** enofts
    s_db

### Laravel Drivers & Configss_db

-   **Database:** MySQLsys)
-   **Cache:** database
-   **Session:** database
-   **Queue:** database
-   **Broadcasting:** log
-   **Mail:** SMTP

### Installed Packages & Features

-   **Spatie Permissions:** v6.16.0
-   **Laravel Horizon:** Queue monitoring dashboard
-   **Laravel Telescope:** Application debugging assistant
-   **Laravel Sanctum:** API token authentication
-   **Log Viewer:** Interface for viewing application logs

### Database Environment

-   **MySQL Version:** 9.2.0 for macOS 15.2 on arm64 (Homebrew)
-   **PostgreSQL:** ไม่ได้ติดตั้ง (Not installed)
-   **MySQL Databases:**
    -   **ceosofts** (36 ตาราง) - ข้อมูลพื้นฐานสำหรับการพัฒนา
    -   **ceosofts_db** (32 ตาราง) - ข้อมูลที่มีอยู่เดิมที่อาจต้องการ migration
    -   ชceosofts_db
    -   System databases (information_schema, mysql, performance_schema, sys)
-   **Database Access:**
    -   Local MySQL: ใช้ root user ผ่าน localhost (127.0.0.1)
    -   Docker MySQL: ใช้ sail user แต่ยังพบปัญหาเรื่องสิทธิ์การเข้าถึง

### Database Connection Methods

-   **Local Development:** `php -d variables_order=EGPCS artisan --env=local db:show`
-   **Docker Development:** `./vendor/bin/sail artisan db:show` (ต้องแก้ไขปัญหาสิทธิ์การเข้าถึง)
-   **Recommended Setup:** สร้าง alias `artisan-local="php -d variables_order=EGPCS artisan --env=local"`
