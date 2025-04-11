#!/bin/bash

echo "กำลังแก้ไขปัญหา Tailwind CSS..."

# สำรองไฟล์ app.css
echo "สำรองไฟล์ CSS เดิม..."
cp resources/css/app.css resources/css/app.css.bak

# แทนที่ด้วยไฟล์ใหม่
echo "แทนที่ด้วยไฟล์ CSS ใหม่..."
cp resources/css/app-fix.css resources/css/app.css

# ตรวจสอบและแก้ไข tailwind.config.js
echo "ตรวจสอบไฟล์ tailwind.config.js..."

# สำรองไฟล์ tailwind.config.js
cp tailwind.config.js tailwind.config.js.bak

# แก้ไข content paths ใน tailwind config
cat > tailwind.config.js << 'EOL'
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './app/View/Components/**/*.php',
        './app/Http/Livewire/**/*.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                heading: ['Prompt', 'sans-serif'],
            },
            colors: {
                primary: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    300: '#7dd3fc',
                    400: '#38bdf8',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    700: '#0369a1',
                    800: '#075985',
                    900: '#0c4a6e',
                    950: '#082f49',
                },
                secondary: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                },
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

    plugins: [forms, typography],
};
EOL

# แสดงการอัพเดต postcss.config.js
echo "ตรวจสอบไฟล์ postcss.config.js..."
if [ -f "postcss.config.js" ]; then
    cp postcss.config.js postcss.config.js.bak
    
    cat > postcss.config.js << 'EOL'
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
EOL
else
    cat > postcss.config.js << 'EOL'
export default {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
    },
};
EOL
fi

echo "เคลียร์ node_modules เพื่อติดตั้งใหม่..."
rm -rf node_modules
rm -f package-lock.json

echo "ติดตั้ง dependencies ใหม่..."
npm install

echo "ติดตั้ง TailwindCSS dependencies ใหม่..."
npm install -D tailwindcss postcss autoprefixer @tailwindcss/forms @tailwindcss/typography

echo "กำลังทดสอบการ build..."
npm run build

if [ $? -eq 0 ]; then
    echo "การแก้ไขสำเร็จ! คุณสามารถรัน npm run dev ได้แล้ว"
else
    echo "ยังมีปัญหาในการ build กรุณาตรวจสอบข้อความข้างต้น"
    echo "ลองเคลียร์ cache ของ vite:"
    echo "rm -rf node_modules/.vite"
fi

chmod +x fix-tailwind.sh
