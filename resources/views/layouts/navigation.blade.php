<nav class="bg-primary-800 border-b border-primary-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('img/ceo_logo9.ico') }}" alt="CEOsofts Logo" class="h-8 w-auto mr-2">
                        <span class="text-white font-heading font-bold text-xl">CEOsofts R1</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('home') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        หน้าหลัก
                    </a>
                    <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        บริการ
                    </a>
                    <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        เกี่ยวกับเรา
                    </a>
                    <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        ติดต่อเรา
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <div class="relative">
                    <x-button type="accent" class="inline-flex items-center">
                        เข้าสู่ระบบ
                    </x-button>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-primary-700 focus:outline-none focus:bg-primary-700 focus:text-gray-200 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile menu, show/hide based on menu state. -->
    <div class="sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-accent-400 text-accent-400 bg-primary-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                หน้าหลัก
            </a>
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300 text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                บริการ
            </a>
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300 text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                เกี่ยวกับเรา
            </a>
            <a href="#" class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300 text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ติดต่อเรา
            </a>
        </div>
    </div>
</nav>
