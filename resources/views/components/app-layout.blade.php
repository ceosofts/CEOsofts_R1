<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('img/ceo_logo9.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex-grow">
            {{ $slot }}
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 py-4">
                    <div>
                        <h3 class="font-heading text-lg font-semibold mb-4">เกี่ยวกับเรา</h3>
                        <p class="text-gray-600">
                            CEOsofts พัฒนาโซลูชันทางเทคโนโลยีที่ตอบโจทย์ธุรกิจของคุณ
                        </p>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-semibold mb-4">บริการ</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">พัฒนาเว็บไซต์</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">แอปพลิเคชันมือถือ</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">ที่ปรึกษาธุรกิจ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-semibold mb-4">ลิงก์</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">หน้าหลัก</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">เกี่ยวกับเรา</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">บริการ</a></li>
                            <li><a href="#" class="text-gray-600 hover:text-primary-600">ติดต่อ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-heading text-lg font-semibold mb-4">ติดต่อ</h3>
                        <ul class="space-y-2">
                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                </svg>
                                <span class="text-gray-600">02-123-4567</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-primary-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                </svg>
                                <span class="text-gray-600">info@ceosoft.com</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="pt-8 mt-8 border-t border-gray-200 text-center text-gray-500">
                    &copy; {{ date('Y') }} CEOsofts R1. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
