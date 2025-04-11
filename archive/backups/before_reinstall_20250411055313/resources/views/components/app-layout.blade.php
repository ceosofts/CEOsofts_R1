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

    @livewireStyles
    @stack('scripts')
</body>
</html>
