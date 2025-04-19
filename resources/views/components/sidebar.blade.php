<aside
    class="bg-secondary-800 text-white w-64 flex-shrink-0 overflow-y-auto fixed h-full md:sticky md:top-0 transition-transform duration-300 transform"
    x-data="{ open: true }"
    x-on:toggle-sidebar.window="open = !open"
    :class="{'translate-x-0': open, '-translate-x-full': !open, 'md:translate-x-0': true}">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-16 bg-secondary-900">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="/img/logo.svg" alt="CEOsofts logo" class="h-8 w-auto">
                <span class="ml-2 text-lg font-semibold">CEOsofts R1</span>
            </a>
        </div>

        <!-- Company Info -->
        @if(auth()->check() && session('current_company_id'))
        <div class="bg-secondary-900/50 px-4 py-3">
            <p class="text-xs text-secondary-400 uppercase">บริษัท</p>
            <p class="font-medium truncate">{{ \App\Models\Company::find(session('current_company_id'))->name }}</p>
        </div>
        @endif

        <!-- Navigation -->
        <nav class="flex-1 px-2 py-4 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="{{ request()->routeIs('dashboard') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                แดชบอร์ด
            </a>

            <!-- Organization Management -->
            <div x-data="{ open: {{ request()->routeIs('companies.*') || request()->routeIs('departments.*') || request()->routeIs('positions.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm text-secondary-300 hover:bg-secondary-700 rounded-md">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        จัดการองค์กร
                    </div>
                    <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <svg x-show="open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('companies.index') }}" class="{{ request()->routeIs('companies.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        บริษัท
                    </a>
                    <a href="#" class="{{ request()->routeIs('departments.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        แผนก
                    </a>
                    <a href="#" class="{{ request()->routeIs('positions.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        ตำแหน่ง
                    </a>
                </div>
            </div>

            <!-- Human Resources -->
            <div x-data="{ open: {{ request()->routeIs('employees.*') || request()->routeIs('work-shifts.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm text-secondary-300 hover:bg-secondary-700 rounded-md">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        ทรัพยากรบุคคล
                    </div>
                    <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <svg x-show="open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-6 mt-1 space-y-1">
                    <a href="#" class="{{ request()->routeIs('employees.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        พนักงาน
                    </a>
                    <a href="#" class="{{ request()->routeIs('work-shifts.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        กะการทำงาน
                    </a>
                </div>
            </div>

            <!-- Sales -->
            <div x-data="{ open: {{ request()->routeIs('customers.*') || request()->routeIs('quotations.*') || request()->routeIs('orders.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-4 py-2 text-sm text-secondary-300 hover:bg-secondary-700 rounded-md">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        ขาย
                    </div>
                    <svg x-show="!open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <svg x-show="open" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" class="pl-6 mt-1 space-y-1">
                    <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        ลูกค้า
                    </a>
                    <a href="{{ route('quotations.index') }}" class="{{ request()->routeIs('quotations.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        ใบเสนอราคา
                    </a>
                    <a href="{{ route('orders.index') }}" class="{{ request()->routeIs('orders.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        ใบสั่งขาย
                    </a>
                    <a href="{{ route('delivery-orders.index') }}" class="{{ request()->routeIs('delivery-orders.*') ? 'bg-secondary-900 text-white' : 'text-secondary-300 hover:bg-secondary-700' }} flex items-center px-4 py-2 text-sm rounded-md">
                        ใบส่งสินค้า
                    </a>
                </div>
            </div>
        </nav>

        <!-- Help Link -->
        <div class="border-t border-secondary-700 p-4">
            <a href="#" class="flex items-center text-sm text-secondary-400 hover:text-white transition-colors duration-150">
                <svg class="mr-3 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                ช่วยเหลือ
            </a>
        </div>
    </div>
</aside>