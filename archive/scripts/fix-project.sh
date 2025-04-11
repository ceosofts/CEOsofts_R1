#!/bin/bash

# ตั้งค่าสี
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}     CEOsofts R1 Project Fix Utility     ${NC}"
echo -e "${BLUE}================================================${NC}"

# ฟังก์ชันติดตั้ง Livewire
install_livewire() {
    echo -e "${YELLOW}กำลังติดตั้ง Livewire...${NC}"
    composer require livewire/livewire

    if [ $? -eq 0 ]; then
        echo -e "${GREEN}ติดตั้ง Livewire สำเร็จ!${NC}"
        # ทดสอบคำสั่ง make:livewire
        echo -e "${YELLOW}ทดสอบคำสั่ง make:livewire...${NC}"
        php artisan make:livewire TestComponent --force
    else
        echo -e "${RED}ไม่สามารถติดตั้ง Livewire ได้${NC}"
    fi
}

# ฟังก์ชันแก้ไขปัญหา Tailwind CSS
fix_tailwind() {
    echo -e "${YELLOW}กำลังตรวจสอบและแก้ไขปัญหา Tailwind CSS...${NC}"
    
    # ตรวจสอบว่ามีไฟล์ tailwind.config.js หรือไม่
    if [ ! -f "tailwind.config.js" ]; then
        echo -e "${RED}ไม่พบไฟล์ tailwind.config.js${NC}"
        echo -e "${YELLOW}กำลังสร้างไฟล์ tailwind.config.js...${NC}"
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
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms, typography],
};
EOL
    else
        echo -e "${GREEN}พบไฟล์ tailwind.config.js แล้ว${NC}"
    fi

    # แก้ไขไฟล์ resources/css/app.css
    echo -e "${YELLOW}กำลังตรวจสอบไฟล์ resources/css/app.css...${NC}"
    if grep -q "@tailwind base" "resources/css/app.css"; then
        echo -e "${GREEN}ไฟล์ resources/css/app.css มีการนำเข้า @tailwind แล้ว${NC}"
    else
        echo -e "${YELLOW}กำลังแก้ไขไฟล์ resources/css/app.css...${NC}"
        cat > resources/css/app.css << 'EOL'
@tailwind base;
@tailwind components;
@tailwind utilities;
EOL
        echo -e "${GREEN}แก้ไขไฟล์ resources/css/app.css เรียบร้อยแล้ว${NC}"
    fi
}

# ฟังก์ชันสร้าง app-layout component
create_app_layout() {
    echo -e "${YELLOW}กำลังสร้าง app-layout component...${NC}"
    
    # สร้างไดเร็กทอรี (ถ้ายังไม่มี)
    mkdir -p resources/views/components

    # สร้าง app-layout component
    cat > resources/views/components/app-layout.blade.php << 'EOL'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        @if (View::exists('components.sidebar'))
            <x-sidebar />
        @endif

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Navbar -->
            @if (View::exists('components.navbar'))
                <x-navbar />
            @endif

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
EOL

    # สร้าง AppLayout class
    mkdir -p app/View/Components
    cat > app/View/Components/AppLayout.php << 'EOL'
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.app-layout');
    }
}
EOL

    echo -e "${GREEN}สร้าง app-layout component เรียบร้อยแล้ว${NC}"
}

# ฟังก์ชันแก้ไขปัญหา migrations
fix_migrations() {
    echo -e "${YELLOW}กำลังแก้ไขปัญหา migrations...${NC}"
    
    # เพิ่ม check เงื่อนไขในไฟล์ migration ของ create_companies_table
    if [ -f "database/migrations/2024_08_01_000001_create_companies_table.php" ]; then
        echo -e "${YELLOW}กำลังแก้ไขไฟล์ migration create_companies_table.php...${NC}"
        
        # สำรองไฟล์
        cp database/migrations/2024_08_01_000001_create_companies_table.php database/migrations/2024_08_01_000001_create_companies_table.php.bak
        
        # แก้ไขไฟล์
        sed -i '' -e 's/Schema::create('\''companies'\'', function (Blueprint $table) {/if (!Schema::hasTable('\''companies'\'')) {\n            Schema::create('\''companies'\'', function (Blueprint $table) {/' database/migrations/2024_08_01_000001_create_companies_table.php
        sed -i '' -e 's/});/});\n        }/' database/migrations/2024_08_01_000001_create_companies_table.php
        
        echo -e "${GREEN}แก้ไขไฟล์ migration create_companies_table.php เรียบร้อยแล้ว${NC}"
    else
        echo -e "${RED}ไม่พบไฟล์ migration create_companies_table.php${NC}"
    fi
    
    # รันคำสั่งสำหรับข้าม migration ที่มีปัญหา
    echo -e "${YELLOW}กำลังทดสอบข้าม migration ที่มีปัญหา...${NC}"
    php artisan migrate:status
    
    echo -e "${YELLOW}คุณสามารถรันคำสั่ง 'php artisan migrate --path=vendor/laravel/telescope/database/migrations' เพื่อรัน migration เฉพาะของ Telescope${NC}"
}

# แสดงเมนูตัวเลือก
echo -e "${GREEN}โปรดเลือกการดำเนินการ:${NC}"
echo "1) ติดตั้ง Livewire (สำหรับแก้ไขปัญหา make:livewire ไม่ทำงาน)"
echo "2) แก้ไขปัญหา Tailwind CSS"
echo "3) สร้าง app-layout component (สำหรับแก้ไขปัญหา view:cache)"
echo "4) แก้ไขปัญหา migrations ที่ซ้ำซ้อน"
echo "5) รันทุกการแก้ไขข้างต้น"
echo "q) ออกจากสคริปต์"

# รับข้อมูลจากผู้ใช้
read -p "> เลือกตัวเลข (1-5) หรือ q เพื่อออก: " choice

# ดำเนินการตามตัวเลือก
case $choice in
    1)
        install_livewire
        ;;
    2)
        fix_tailwind
        ;;
    3)
        create_app_layout
        ;;
    4)
        fix_migrations
        ;;
    5)
        install_livewire
        fix_tailwind
        create_app_layout
        fix_migrations
        ;;
    q)
        echo "ออกจากสคริปต์"
        exit 0
        ;;
    *)
        echo -e "${RED}ตัวเลือกไม่ถูกต้อง${NC}"
        ;;
esac

echo -e "\n${GREEN}เสร็จสิ้นการทำงาน${NC}"
chmod +x ./fix-project.sh
