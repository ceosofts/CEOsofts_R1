<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CEOsofts R1') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-secondary-700 antialiased">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('welcome') }}">
                            <img class="h-8 w-auto" src="/img/logo.svg" alt="CEOsofts">
                        </a>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-8">
                    <a href="#features" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">คุณสมบัติ</a>
                    <a href="#modules" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">โมดูล</a>
                    <a href="#testimonials" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">ความคิดเห็น</a>
                    <a href="#contact" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">ติดต่อเรา</a>
                </div>

                <!-- Authentication Links -->
                <div class="hidden sm:ml-6 sm:flex sm:items-center">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            แดชบอร์ด
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-secondary-600 hover:text-primary-600 px-3 py-2 text-sm font-medium">เข้าสู่ระบบ</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md bg-primary-600 text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                ลงทะเบียน
                            </a>
                        @endif
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="-mr-2 flex items-center sm:hidden">
                    <button type="button" @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-secondary-400 hover:text-secondary-500 hover:bg-secondary-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500" x-data="{ open: false }" :aria-expanded="open">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" x-show="!open" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" x-show="open" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state -->
        <div class="sm:hidden" x-data="{ open: false }" x-show="open" style="display: none;">
            <div class="pt-2 pb-3 space-y-1">
                <a href="#features" class="block px-3 py-2 text-base font-medium text-secondary-600 hover:bg-secondary-50 hover:text-primary-600">คุณสมบัติ</a>
                <a href="#modules" class="block px-3 py-2 text-base font-medium text-secondary-600 hover:bg-secondary-50 hover:text-primary-600">โมดูล</a>
                <a href="#testimonials" class="block px-3 py-2 text-base font-medium text-secondary-600 hover:bg-secondary-50 hover:text-primary-600">ความคิดเห็น</a>
                <a href="#contact" class="block px-3 py-2 text-base font-medium text-secondary-600 hover:bg-secondary-50 hover:text-primary-600">ติดต่อเรา</a>
            </div>

            <div class="pt-4 pb-3 border-t border-secondary-200">
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-base font-medium text-secondary-600 hover:bg-secondary-50 hover:text-primary-600">แดชบอร์ด</a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-base font-medium text-secondary-600 hover:bg-secondary-50 hover:text-primary-600">เข้าสู่ระบบ</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-medium text-secondary-600 hover:bg-secondary-50 hover:text-primary-600">ลงทะเบียน</a>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    <footer class="bg-secondary-800 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <img src="/img/logo.svg" alt="CEOsofts" class="h-8 w-auto">
                    <p class="mt-4 text-sm text-secondary-300">
                        CEOsofts R1 - ระบบบริหารจัดการองค์กรครบวงจร สำหรับธุรกิจทุกขนาด ช่วยให้องค์กรของคุณทำงานได้อย่างมีประสิทธิภาพมากขึ้น
                    </p>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase">เกี่ยวกับเรา</h3>
                    <ul role="list" class="mt-4 space-y-4">
                        <li>
                            <a href="#" class="text-sm text-secondary-300 hover:text-white">เกี่ยวกับบริษัท</a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-secondary-300 hover:text-white">บริการของเรา</a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-secondary-300 hover:text-white">ข่าวสาร</a>
                        </li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase">ผลิตภัณฑ์</h3>
                    <ul role="list" class="mt-4 space-y-4">
                        <li>
                            <a href="#" class="text-sm text-secondary-300 hover:text-white">CEOsofts R1</a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-secondary-300 hover:text-white">CEOsofts HR</a>
                        </li>
                        <li>
                            <a href="#" class="text-sm text-secondary-300 hover:text-white">CEOsofts Finance</a>
                        </li>
                    </ul>
                </div>

                <div id="contact">
                    <h3 class="text-sm font-semibold text-white tracking-wider uppercase">ติดต่อเรา</h3>
                    <ul role="list" class="mt-4 space-y-4">
                        <li class="flex">
                            <svg class="flex-shrink-0 h-5 w-5 text-secondary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            <span class="ml-3 text-sm text-secondary-300">02-123-4567</span>
                        </li>
                        <li class="flex">
                            <svg class="flex-shrink-0 h-5 w-5 text-secondary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                            <span class="ml-3 text-sm text-secondary-300">contact@ceosofts.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-secondary-700 md:flex md:items-center md:justify-between">
                <div class="flex space-x-6 md:order-2">
                    <a href="#" class="text-secondary-400 hover:text-secondary-300">
                        <span class="sr-only">Facebook</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </a>

                    <a href="#" class="text-secondary-400 hover:text-secondary-300">
                        <span class="sr-only">Instagram</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465.668.25 1.246.6 1.809 1.161.56.563.902 1.142 1.152 1.809.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427-.25.668-.6 1.246-1.161 1.809-.563.56-1.142.902-1.809 1.152-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465-.668-.25-1.246-.6-1.809-1.161-.56-.563-.902-1.142-1.152-1.809-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427.25-.668.6-1.246 1.161-1.809.563-.56 1.142-.902 1.809-1.152.636-.247 1.363-.416 2.427-.465C9.56 2.013 9.9 2 12 2h.315zm-.191 18c2.282 0 2.969-.147 4.047-.336.466-.09.812-.192 1.125-.344.405-.193.659-.34.943-.622.283-.284.43-.538.622-.943.152-.313.254-.659.344-1.125.186-1.05.196-1.63.196-8.63 0-7.026-.01-7.577-.2-8.634-.09-.466-.192-.812-.344-1.125-.193-.405-.34-.659-.622-.943-.284-.283-.538-.43-.943-.622-.313-.152-.659-.254-1.125-.344-1.05-.186-1.63-.196-8.63-.196-7.026 0-7.577.01-8.634.2-.466.09-.812.192-1.125.344-.405.193-.659.34-.943.622-.283.284-.43.538-.622.943-.152.313-.254.659-.344 1.125-.19 1.06-.196 1.634-.196 8.643 0 7.01.006 7.583.196 8.642.09.466.192.812.344 1.125.193.405.34.659.622.943.284.283.538.43.943.622.313.152.659.254 1.125.344 1.05.196 1.634.185 8.333.185h.31z" clip-rule="evenodd" />
                        </svg>
                    </a>

                    <a href="#" class="text-secondary-400 hover:text-secondary-300">
                        <span class="sr-only">Line</span>
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314" />
                        </svg>
                    </a>
                </div>
                <p class="mt-8 text-sm text-secondary-400 md:mt-0 md:order-1">
                    &copy; 2023 CEOsofts. สงวนลิขสิทธิ์.
                </p>
            </div>
        </div>
    </footer>
</body>
</html>
