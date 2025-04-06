# คำสั่งสำหรับตั้งค่า Tailwind CSS ใน Laravel ด้วยมือ

## 1. สร้างไฟล์ tailwind.config.js

```bash
cat > tailwind.config.js << 'EOL'
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
          50: '#eef2ff',
          100: '#e0e7ff',
          200: '#c7d2fe',
          300: '#a5b4fc',
          400: '#818cf8',
          500: '#6366f1',
          600: '#4f46e5',
          700: '#4338ca',
          800: '#3730a3',
          900: '#312e81',
          950: '#1e1b4b',
        },
        secondary: {
          50: '#f0fdfa',
          100: '#ccfbf1',
          200: '#99f6e4',
          300: '#5eead4',
          400: '#2dd4bf',
          500: '#14b8a6',
          600: '#0d9488',
          700: '#0f766e',
          800: '#115e59',
          900: '#134e4a',
          950: '#042f2e',
        }
      },
      fontFamily: {
        sans: ['Sarabun', 'sans-serif'],
        heading: ['Prompt', 'sans-serif'],
      },
      spacing: {
        '72': '18rem',
        '84': '21rem',
        '96': '24rem',
      },
      borderRadius: {
        'xl': '1rem',
        '2xl': '2rem',
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
  ],
}
EOL
```

## 2. สร้างไฟล์ postcss.config.js

```bash
cat > postcss.config.js << 'EOL'
export default {
  plugins: {
    tailwindcss: {},
    autoprefixer: {},
  },
}
EOL
```

## 3. แก้ไขไฟล์ resources/css/app.css

```bash
cat > resources/css/app.css << 'EOL'
@tailwind base;
@tailwind components;
@tailwind utilities;

/* Custom CSS */
@layer components {
  .btn-primary {
    @apply px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors;
  }

  .btn-secondary {
    @apply px-4 py-2 bg-secondary-500 text-white rounded-lg hover:bg-secondary-600 transition-colors;
  }

  .form-input {
    @apply mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50;
  }

  .card {
    @apply bg-white dark:bg-gray-800 rounded-xl shadow-md p-6;
  }
}
EOL
```

## 4. แก้ไข package.json เพื่อเพิ่ม script สำหรับ Tailwind

```bash
# แบ็คอัพไฟล์เดิม
cp package.json package.json.bak

# สร้างไฟล์ใหม่
cat > package.json << 'EOL'
{
    "private": true,
    "type": "module",
    "scripts": {
        "dev": "vite",
        "build": "vite build",
        "init-tailwind": "./node_modules/.bin/tailwindcss init -p"
    },
    "devDependencies": {
        "autoprefixer": "^10.4.19",
        "axios": "^1.6.8",
        "laravel-vite-plugin": "^1.0.0",
        "postcss": "^8.4.38",
        "tailwindcss": "^3.4.1",
        "vite": "^5.0.0"
    },
    "dependencies": {
        "alpinejs": "^3.13.7",
        "apexcharts": "^3.48.0"
    }
}
EOL
```

## 5. ตรวจสอบไฟล์ vite.config.js

```bash
cat > vite.config.js << 'EOL'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
EOL
```

## 6. รัน development server

```bash
sail npm run dev
```

## 7. แก้ไข welcome.blade.php สำหรับตรวจสอบ

เพิ่มบรรทัดนี้ใน `<head>`:

```html
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

แก้ไขส่วนหนึ่งของ template เพื่อทดสอบ Tailwind:

```html
<div class="p-6 bg-primary-100 text-primary-800 rounded-xl shadow-lg">
    <h1 class="text-3xl font-bold text-primary-600">Laravel + Tailwind CSS</h1>
    <p class="mt-2">
        If you can see this styled text, Tailwind CSS is working!
    </p>
    <button class="btn-primary mt-4">Primary Button</button>
    <button class="btn-secondary mt-4 ml-2">Secondary Button</button>
</div>
```
