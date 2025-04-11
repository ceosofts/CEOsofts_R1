# ขั้นตอนต่อไปหลังจากติดตั้ง Docker และ Sail

## 1. ติดตั้ง Docker Desktop

ขั้นตอนแรก คุณจำเป็นต้องติดตั้งและเปิดใช้งาน Docker Desktop (หรือ Docker Engine) บนเครื่องของคุณก่อน:

1. สำหรับ macOS: [ดาวน์โหลด Docker Desktop สำหรับ Mac](https://www.docker.com/products/docker-desktop)
2. หลังจากติดตั้งแล้ว เปิดแอป Docker Desktop และรอให้ไอคอนที่ taskbar/menu bar แสดงสถานะว่า "Running"

## 2. เริ่มใช้งาน Laravel Sail

หลังจาก Docker เริ่มทำงานแล้ว:

```bash
# เริ่ม containers
./vendor/bin/sail up -d

# ตรวจสอบสถานะ containers
./vendor/bin/sail ps

# รัน migrations
./vendor/bin/sail artisan migrate
```

## 3. สร้างโครงสร้าง DDD

หลังจาก Laravel Sail ทำงานได้แล้ว ให้สร้างโครงสร้างโปรเจคตามแบบ Domain-Driven Design:

```bash
# สร้างโฟลเดอร์ Note สำหรับเก็บเอกสาร
mkdir -p Note

# สร้างโครงสร้างไฟล์ตาม Domain-Driven Design
mkdir -p app/Domains/Organization/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/HumanResources/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Sales/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Inventory/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Finance/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Domains/Settings/{Models,Repositories,Services,Http/{Controllers,Requests,Resources},Policies,Events,Listeners}
mkdir -p app/Shared/{Services,Traits,Exceptions,Enums,Interfaces}
```

## 4. ติดตั้ง Dependencies ที่จำเป็น

```bash
# ติดตั้ง PHP dependencies
sail composer require spatie/laravel-permission
sail composer require laravel/sanctum
sail composer require laravel/telescope --dev
sail composer require laravel/horizon
sail composer require opcodesio/log-viewer

# ติดตั้ง JavaScript dependencies
sail npm install
sail npm install -D tailwindcss postcss autoprefixer
sail npm install alpinejs
sail npm install apexcharts

# Initialize Tailwind
sail npx tailwindcss init -p
```

## 5. สร้าง Service Provider สำหรับแต่ละ Domain

```bash
sail php artisan make:provider OrganizationServiceProvider
sail php artisan make:provider HumanResourcesServiceProvider
sail php artisan make:provider SalesServiceProvider
sail php artisan make:provider InventoryServiceProvider
sail php artisan make:provider FinanceServiceProvider
sail php artisan make:provider SettingsServiceProvider
```

## 6. ตั้งค่า Routes และ Multi-tenancy

```bash
# สร้างไฟล์ routes สำหรับแต่ละ Domain
mkdir -p routes/domains
touch routes/domains/organization.php
touch routes/domains/human-resources.php
touch routes/domains/sales.php
touch routes/domains/inventory.php
touch routes/domains/finance.php
touch routes/domains/settings.php
touch routes/admin.php

# สร้าง Middleware สำหรับ Multi-tenancy
sail php artisan make:middleware EnsureCompanyAccess

# สร้างไฟล์ Trait สำหรับ Multi-tenancy
mkdir -p app/Shared/Traits
touch app/Shared/Traits/HasCompanyScope.php
```

## 7. ตั้งค่า Tailwind CSS

แก้ไขไฟล์ `tailwind.config.js` ให้มีค่าตามที่กำหนดไว้ในแผนการพัฒนา:

```javascript
/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: "#eef2ff",
                    100: "#e0e7ff",
                    200: "#c7d2fe",
                    300: "#a5b4fc",
                    400: "#818cf8",
                    500: "#6366f1",
                    600: "#4f46e5",
                    700: "#4338ca",
                    800: "#3730a3",
                    900: "#312e81",
                    950: "#1e1b4b",
                },
                secondary: {
                    50: "#f0fdfa",
                    100: "#ccfbf1",
                    200: "#99f6e4",
                    300: "#5eead4",
                    400: "#2dd4bf",
                    500: "#14b8a6",
                    600: "#0d9488",
                    700: "#0f766e",
                    800: "#115e59",
                    900: "#134e4a",
                    950: "#042f2e",
                },
            },
            fontFamily: {
                sans: ["Sarabun", "sans-serif"],
                heading: ["Prompt", "sans-serif"],
            },
            spacing: {
                72: "18rem",
                84: "21rem",
                96: "24rem",
            },
            borderRadius: {
                xl: "1rem",
                "2xl": "2rem",
            },
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
};
```

## 8. ตรวจสอบว่าทุกอย่างทำงานได้ถูกต้อง

```bash
# เช็คสถานะของ Sail containers
sail ps

# ตรวจสอบ Laravel
sail artisan about

# รัน basic tests
sail artisan test

# เริ่มพัฒนา front-end
sail npm run dev
```

## 9. เข้าถึงแอปพลิเคชันและเครื่องมือ

-   **Laravel Application**: http://localhost
-   **MySQL**: `sail mysql`
-   **Redis**: `sail redis`
-   **Mailpit**: http://localhost:8025
-   **MinIO Console**: http://localhost:8900 (username: `sail`, password: `password`)

## 10. เริ่มการพัฒนา

คุณสามารถเริ่มการพัฒนาตามแผนงานที่วางไว้ใน roadmap เริ่มจากเฟส 1 และ 2 ตามลำดับ:

1. เริ่มพัฒนา Models สำหรับ Organization Domain
2. สร้าง Migrations สำหรับ Organization Domain
3. พัฒนา Controllers และ Views สำหรับ Organization Domain
4. ทำซ้ำกับ Domain อื่นๆ
