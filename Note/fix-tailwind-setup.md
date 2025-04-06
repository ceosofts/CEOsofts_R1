# วิธีแก้ไขปัญหาการตั้งค่า Tailwind CSS

## แก้ไขปัญหา "could not determine executable to run"

คุณสามารถลองวิธีต่อไปนี้เพื่อแก้ไขปัญหา:

### วิธีที่ 1: ติดตั้ง tailwindcss แบบ global

```bash
# เข้าสู่ shell ของ container
sail shell

# ติดตั้ง tailwindcss แบบ global
npm install -g tailwindcss

# ลองสร้างไฟล์ config อีกครั้ง
tailwindcss init -p
```

### วิธีที่ 2: สร้างไฟล์ configuration ด้วยตัวเอง

คุณสามารถสร้างไฟล์ Tailwind configuration ด้วยตัวเอง:

1. สร้างไฟล์ `tailwind.config.js` ในโฟลเดอร์หลักของโปรเจค:

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

2. สร้างไฟล์ `postcss.config.js` ในโฟลเดอร์หลักของโปรเจค:

```javascript
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
```

### วิธีที่ 3: ปรับ package.json

แก้ไขไฟล์ `package.json` เพื่อเพิ่ม script สำหรับสร้างไฟล์ config:

```json
"scripts": {
    "dev": "vite",
    "build": "vite build",
    "init-tailwind": "tailwindcss init -p"
}
```

จากนั้นรัน:

```bash
sail npm run init-tailwind
```

## การตั้งค่า Tailwind ใน Laravel Vite

1. แก้ไขไฟล์ `resources/css/app.css` ให้มีข้อมูลดังนี้:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

2. แก้ไขไฟล์ `vite.config.js` (ถ้ายังไม่มีการตั้งค่า):

```javascript
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
```

3. ตรวจสอบว่าได้เพิ่ม Vite directive ใน blade template หลัก (`resources/views/layouts/app.blade.php` หรือ template ที่ใช้):

```php
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

## การรัน Vite

หลังจากตั้งค่าแล้ว คุณสามารถพัฒนา CSS ด้วย:

```bash
# ใช้ development server
sail npm run dev

# หรือสร้าง production build
sail npm run build
```

## การตรวจสอบการติดตั้ง

สร้างไฟล์ Blade เพื่อทดสอบว่า Tailwind CSS ทำงานได้ถูกต้อง:

```php
<!-- resources/views/welcome.blade.php (ปรับปรุงจากไฟล์ที่มีอยู่) -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
        <h1 class="text-4xl font-bold text-primary-600">Tailwind CSS is working!</h1>
    </div>
</body>
</html>
```
