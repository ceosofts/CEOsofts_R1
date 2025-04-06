# ขั้นตอนถัดไปที่สำคัญสำหรับ CEOsofts R1

## งานเร่งด่วน (1-2 สัปดาห์)

1. **ตั้งค่าฐานข้อมูล ceosofts_db_R1**

    - รัน `chmod +x setup-database.sh && ./setup-database.sh` เพื่อสร้างฐานข้อมูล
    - รีสตาร์ท Docker containers: `./vendor/bin/sail down && ./vendor/bin/sail up -d`
    - ตรวจสอบการเชื่อมต่อกับ `./vendor/bin/sail artisan db:show`

2. **สร้าง migrations สำหรับตารางหลัก**

    - สร้าง migration ตามลำดับความสัมพันธ์ที่ระบุใน database-schema-improvements.md
    - เริ่มจากตารางพื้นฐาน: companies, units, taxes
    - จากนั้นสร้างตารางระดับกลาง: departments, positions, roles, users

3. **สร้างโครงสร้างโปรเจคตาม DDD (Domain-Driven Design)**

    - จัดทำโฟลเดอร์สำหรับแต่ละโดเมนตามที่ระบุไว้ใน file-structure.md
    - เตรียมโครงสร้างพื้นฐานสำหรับ services, repositories, และ interfaces

4. **ตัดสินใจเลือก stack เทคโนโลยีที่จะใช้**

    - ตัดสินใจระหว่าง Livewire + Alpine.js หรือ Inertia.js + Vue
    - เลือก UI Component Library (Tailwind-based)
    - ยืนยันการใช้ PHP 8.4.4 หรือพิจารณาเวอร์ชันที่เหมาะสมกว่า

5. **ติดตั้ง dependency packages ที่จำเป็น**
    - Laravel Sanctum สำหรับ authentication
    - Spatie Permission สำหรับ RBAC
    - Frontend framework ตามที่ตัดสินใจ
    - ตรวจสอบความเข้ากันได้กับ PHP 8.4

## งานต่อเนื่อง (2-4 สัปดาห์)

1. **พัฒนาระบบ Multi-tenant**

    - สร้าง Global Scope สำหรับ company_id
    - พัฒนา middleware สำหรับตรวจสอบการเข้าถึง

2. **พัฒนาระบบ Authentication & Authorization**

    - ตั้งค่าและทดสอบ Laravel Sanctum
    - สร้างหน้า login/register

3. **ตั้งค่า CI/CD pipeline**
    - ตั้งค่า GitHub Actions สำหรับ automated testing และ code quality

## แนวทางการแก้ไขปัญหาที่พบ

1. **การเชื่อมต่อฐานข้อมูล ceosofts_db_R1**

    - ใช้สคริปต์ setup-database.sh เพื่อสร้างฐานข้อมูลใหม่
    - ตรวจสอบว่าไฟล์ docker/mysql/create-database.sql ถูกต้องและรันเมื่อ container เริ่มทำงาน
    - สร้าง alias ถาวรใน shell profile สำหรับคำสั่ง `artisan-local` ที่ใช้บ่อย

2. **ความเข้ากันได้ของ packages กับ PHP 8.4.4**
    - จัดทำรายการ packages ที่ต้องการใช้และตรวจสอบความเข้ากันได้
    - พิจารณาทางเลือกสำหรับ packages ที่ไม่รองรับเวอร์ชันปัจจุบัน
