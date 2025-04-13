<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'CEOsofts R1') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ซ่อนตัวอักษร C ที่อาจถูกสร้างจาก pseudo-elements หรือคอมโพเนนต์อื่นๆ */
        body::before {
            display: none !important;
        }

        .c-letter,
        .c-character,
        [data-content="C"],
        [class*="c-"] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-900 via-primary-800 to-purple-900 min-h-screen flex flex-col items-center justify-center py-8">
    <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl overflow-hidden sm:rounded-xl border border-gray-100">
        <div class="mb-6 flex justify-center">
            <div class="flex flex-col items-center">
                <img src="{{ asset('img/ceo_logo9.ico') }}" alt="CEOsofts Logo" class="h-20 w-20 mb-2">
                <h1 class="text-3xl font-bold text-center text-primary-700 mb-2">CEOsofts R1</h1>
                <div class="w-16 h-1 bg-accent-500 rounded-full mb-3"></div>
            </div>
        </div>

        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">{{ __('ลืมรหัสผ่าน') }}</h2>

        <p class="mb-4 text-sm text-gray-600 text-center">
            {{ __('กรุณากรอกที่อยู่อีเมลของคุณ เราจะส่งลิงก์สำหรับรีเซ็ตรหัสผ่านให้คุณทางอีเมล') }}
        </p>

        <!-- Session Status -->
        @if (session('status'))
        <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
            {{ session('status') }}
        </div>
        @endif

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-5">
                <x-label for="email" :value="__('อีเมล')" class="text-gray-700 font-semibold mb-1" />

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                    </div>
                    <x-input id="email" class="block mt-1 w-full pl-10 rounded-lg border-gray-300 focus:border-accent-500 focus:ring focus:ring-accent-400 focus:ring-opacity-50 shadow-sm"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus />
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('กลับไปยังหน้าเข้าสู่ระบบ') }}
                </a>

                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-accent-500 to-accent-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-accent-600 hover:to-accent-700 active:from-accent-700 active:to-accent-800 focus:outline-none focus:border-accent-700 focus:ring focus:ring-accent-200 disabled:opacity-25 transition transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    {{ __('ส่งลิงก์รีเซ็ตรหัสผ่าน') }}
                </button>
            </div>
        </form>
    </div>

    <div class="w-full sm:max-w-md px-6 py-4 mt-6 text-center">
        <a href="{{ route('home') }}" class="inline-flex items-center text-sm text-white hover:text-accent-300 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            {{ __('กลับไปยังหน้าหลัก') }}
        </a>
    </div>
</body>

</html>