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

        <h2 class="text-2xl font-semibold text-center text-gray-700 mb-6">{{ __('รีเซ็ตรหัสผ่าน') }}</h2>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">

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
                        :value="old('email', $email)"
                        required
                        autofocus />
                </div>
            </div>

            <!-- Password -->
            <div class="mb-5">
                <x-label for="password" :value="__('รหัสผ่านใหม่')" class="text-gray-700 font-semibold mb-1" />

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-input id="password" class="block mt-1 w-full pl-10 rounded-lg border-gray-300 focus:border-accent-500 focus:ring focus:ring-accent-400 focus:ring-opacity-50 shadow-sm"
                        type="password"
                        name="password"
                        required />
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-5">
                <x-label for="password_confirmation" :value="__('ยืนยันรหัสผ่านใหม่')" class="text-gray-700 font-semibold mb-1" />

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <x-input id="password_confirmation" class="block mt-1 w-full pl-10 rounded-lg border-gray-300 focus:border-accent-500 focus:ring focus:ring-accent-400 focus:ring-opacity-50 shadow-sm"
                        type="password"
                        name="password_confirmation"
                        required />
                </div>
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-accent-500 to-accent-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-accent-600 hover:to-accent-700 active:from-accent-700 active:to-accent-800 focus:outline-none focus:border-accent-700 focus:ring focus:ring-accent-200 disabled:opacity-25 transition transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    {{ __('รีเซ็ตรหัสผ่าน') }}
                </button>
            </div>
        </form>
    </div>
</body>

</html>