<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} - @yield('title', 'Dashboard')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <!-- Additional Styles -->
    @stack('styles')
</head>
<body class="h-full font-sans antialiased bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <div class="flex h-full">
        <!-- Sidebar -->
        <x-sidebar />
        
        <!-- Main Content Area -->
        <div class="flex flex-col flex-1 overflow-hidden">
            <!-- Navbar -->
            <x-navbar />
            
            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
                <!-- Flash Messages -->
                <x-flash-messages />
                
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="mb-6">
                        <div class="max-w-7xl mx-auto">
                            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
                                {{ $header }}
                            </h1>
                        </div>
                    </header>
                @endif
                
                <!-- Main Content -->
                {{ $slot }}
            </main>
            
            <!-- Footer -->
            <x-footer />
        </div>
    </div>
    
    <!-- Modals Container -->
    <div id="modal-container"></div>
    
    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- Additional Scripts -->
    @stack('scripts')
</body>
</html>
