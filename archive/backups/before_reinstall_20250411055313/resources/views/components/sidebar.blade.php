<div
    x-data="{ open: true }"
    x-init="if (window.innerWidth < 768) open = false"
    @resize.window="open = window.innerWidth >= 768"
    :class="{ 'w-64': open, 'w-16': !open }"
    class="bg-primary-800 dark:bg-gray-800 text-white transition-width duration-300 flex flex-col"
>
    <!-- Logo -->
    <div class="flex items-center px-4 py-5 h-16 border-b border-primary-700 dark:border-gray-700">
        <a href="{{ route('dashboard') }}" class="flex items-center">
            @if (open)
                <img src="{{ asset('img/logo.svg') }}" alt="{{ config('app.name') }}" class="h-8">
            @else
                <img src="{{ asset('img/logo-sm.svg') }}" alt="{{ config('app.name') }}" class="h-8">
            @endif
        </a>
        <button
            @click="open = !open"
            class="ml-auto md:ml-4 p-1 rounded-full bg-primary-700 dark:bg-gray-700 hover:bg-primary-600 dark:hover:bg-gray-600 focus:outline-none"
        >
            <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <svg x-show="!open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 pt-5 pb-4 overflow-y-auto">
        <ul class="space-y-1 px-2">
            <!-- Dashboard -->
            <x-sidebar.nav-item route="dashboard" icon="home">
                <span x-show="open">แดชบอร์ด</span>
            </x-sidebar.nav-item>
            
            <!-- Organization Menu -->
            <x-sidebar.dropdown icon="building-office" text="องค์กร">
                <x-sidebar.nav-item route="companies.index" icon="building">
                    <span x-show="open">บริษัท</span>
                </x-sidebar.nav-item>
                <x-sidebar.nav-item route="departments.index" icon="user-group">
                    <span x-show="open">แผนก</span>
                </x-sidebar.nav-item>
                <x-sidebar.nav-item route="positions.index" icon="briefcase">
                    <span x-show="open">ตำแหน่ง</span>
                </x-sidebar.nav-item>
            </x-sidebar.dropdown>
            
            <!-- HR Menu -->
            <x-sidebar.dropdown icon="users" text="บุคลากร">
                <x-sidebar.nav-item route="employees.index" icon="user">
                    <span x-show="open">พนักงาน</span>
                </x-sidebar.nav-item>
                <x-sidebar.nav-item route="leaves.index" icon="calendar">
                    <span x-show="open">การลา</span>
                </x-sidebar.nav-item>
            </x-sidebar.dropdown>
            
            <!-- Sales Menu -->
            <x-sidebar.dropdown icon="shopping-cart" text="การขาย">
                <x-sidebar.nav-item route="customers.index" icon="user-circle">
                    <span x-show="open">ลูกค้า</span>
                </x-sidebar.nav-item>
                <x-sidebar.nav-item route="quotations.index" icon="document-text">
                    <span x-show="open">ใบเสนอราคา</span>
                </x-sidebar.nav-item>
                <x-sidebar.nav-item route="orders.index" icon="clipboard-document-list">
                    <span x-show="open">คำสั่งซื้อ</span>
                </x-sidebar.nav-item>
                <x-sidebar.nav-item route="invoices.index" icon="receipt-percent">
                    <span x-show="open">ใบแจ้งหนี้</span>
                </x-sidebar.nav-item>
            </x-sidebar.dropdown>
            
            <!-- Inventory -->
            <x-sidebar.dropdown icon="cube" text="สินค้า/คลัง">
                <x-sidebar.nav-item route="products.index" icon="archive-box">
                    <span x-show="open">สินค้า</span>
                </x-sidebar.nav-item>
                <x-sidebar.nav-item route="stock.index" icon="truck">
                    <span x-show="open">คลังสินค้า</span>
                </x-sidebar.nav-item>
            </x-sidebar.dropdown>
            
            <!-- Settings -->
            <x-sidebar.nav-item route="settings.index" icon="cog">
                <span x-show="open">การตั้งค่า</span>
            </x-sidebar.nav-item>
        </ul>
    </nav>
    
    <!-- User Menu -->
    <div class="border-t border-primary-700 dark:border-gray-700 p-4">
        <div x-data="{ openUserMenu: false }" class="relative">
            <button @click="openUserMenu = !openUserMenu" class="flex items-center w-full text-left">
                <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="h-8 w-8 rounded-full">
                <div x-show="open" class="ml-3">
                    <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-primary-300 dark:text-gray-400">{{ Auth::user()->email }}</p>
                </div>
                <svg x-show="open" class="ml-auto h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
            
            <div
                x-show="openUserMenu"
                @click.away="openUserMenu = false"
                class="absolute bottom-0 left-0 mb-12 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5"
            >
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    บัญชีผู้ใช้
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        ออกจากระบบ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
