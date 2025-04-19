@php
use Illuminate\Support\Facades\Auth;
@endphp
<nav x-data="{ open: false }" class="bg-blue-800 border-b border-blue-700">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('img/ceo_logo9.ico') }}" alt="CEOsofts Logo" class="h-8 w-auto mr-2">
                        <!-- <span class="text-white font-heading font-bold text-xl">CEOsofts R1</span> -->
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ml-10 sm:flex sm:items-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center px-2 py-2 border-b-2 {{ request()->routeIs('home') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        Home
                    </a>

                    @auth
                    <!-- รวม Dashboard เป็นเมนูเดียว -->
                    <a href="{{ route('executive.dashboard') }}" class="inline-flex items-center px-2 py-2 border-b-2 {{ request()->routeIs('dashboard') || request()->routeIs('executive.*') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        DashBoard
                    </a>

                    <!-- เมนูหลัก -->
                    <!-- ฝ่ายขาย (Sales) Dropdown -->
                    <div class="relative inline-flex items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                                    <span>ฝ่ายขาย</span>
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('customers.index')">
                                    ลูกค้า
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('quotations.index')">
                                    ใบเสนอราคา
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('orders.index')">
                                    ใบสั่งขาย
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('delivery-orders.index')">
                                    ใบส่งของ
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'invoices']) }}">
                                    ใบแจ้งหนี้
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'receipts']) }}">
                                    ใบเสร็จรับเงิน
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- ฝ่ายจัดซื้อ (Purchasing) Dropdown - เพิ่มใหม่ -->
                    <div class="relative inline-flex items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                                    <span>ฝ่ายจัดซื้อ</span>
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'suppliers']) }}">
                                    ผู้จำหน่าย
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'purchase-requisitions']) }}">
                                    คำขอซื้อ
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'purchase-orders']) }}">
                                    ใบสั่งซื้อ
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'goods-receipt']) }}">
                                    การรับสินค้า
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'supplier-invoices']) }}">
                                    ใบแจ้งหนี้จากผู้จำหน่าย
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'payments']) }}">
                                    การจ่ายเงิน
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'supplier-evaluation']) }}">
                                    การประเมินผู้จำหน่าย
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'purchasing-reports']) }}">
                                    รายงานการจัดซื้อ
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- สินค้า (Inventory) Dropdown -->
                    <div class="relative inline-flex items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                                    <span>สินค้า</span>
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('products.index')">
                                    สินค้าและบริการ
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('product-categories.index')">
                                    หมวดหมู่สินค้า
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('units.index') }}">
                                    หน่วยนับ
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('stock-movements.index') }}">
                                    การเคลื่อนไหวสินค้า
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    

                    <!-- ฝ่ายคลัง (Inventory) - Phase 4 -->
                    <div class="relative inline-flex items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                                    <span>ฝ่ายคลัง</span>
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="'/coming-soon/warehouses'">
                                    {{ __('ฝ่ายคลัง') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/goods-receipt'">
                                    {{ __('การรับสินค้า') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/goods-issue'">
                                    {{ __('การเบิกจ่ายสินค้า') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/stock-transfer'">
                                    {{ __('การโอนย้ายสินค้า') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/stock-count'">
                                    {{ __('การตรวจนับสินค้า') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/stock-adjustment'">
                                    {{ __('การปรับปรุงสินค้า') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- ฝ่ายบัญชีและการเงิน(Finance & Accounting) - Phase 5 -->
                    <div class="relative inline-flex items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                                    <span>ฝ่ายบัญชีและการเงิน</span>
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="'/coming-soon/general-ledger'">
                                    {{ __('บัญชีแยกประเภท') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/account-payable'">
                                    {{ __('บัญชีเจ้าหนี้') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/account-receivable'">
                                    {{ __('บัญชีลูกหนี้') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/cash-flow'">
                                    {{ __('รายรับ-รายจ่าย') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/tax-management'">
                                    {{ __('การจัดการภาษี') }}
                                </x-dropdown-link>
                                <x-dropdown-link :href="'/coming-soon/financial-reports'">
                                    {{ __('รายงานทางการเงิน') }}
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>


                    <!-- ทรัพยากรฝ่ายบุคคล (HR) Dropdown -->
                    <div class="relative inline-flex items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                                    <span>ฝ่ายบุคคล</span>
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('employees.index')">
                                    พนักงาน
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'work-shifts']) }}">
                                    กะการทำงาน
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'leave-types']) }}">
                                    ประเภทการลา
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'leaves']) }}">
                                    การลา
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'attendances']) }}">
                                    การลงเวลา
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'time-attendance']) }}">
                                    การลงเวลาทำงาน
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'leave-management']) }}">
                                    การจัดการวันลา
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'payroll']) }}">
                                    เงินเดือนและค่าตอบแทน
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'performance-evaluation']) }}">
                                    การประเมินผลงาน
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'training']) }}">
                                    การฝึกอบรมและพัฒนา
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>


                    <!-- องค์กร (Organization) Dropdown -->
                    <div class="relative inline-flex items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                                    <span>องค์กร</span>
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('companies.index')">
                                    บริษัท
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('branch-offices.index')">
                                    สาขา
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('departments.index')">
                                    แผนก
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('positions.index')">
                                    ตำแหน่ง
                                </x-dropdown-link>
                                <x-dropdown-link :href="route('organization.structure.index')">
                                    โครงสร้างองค์กร
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    
            <!-- ตั้งค่า (Settings) - ยุบรวมกับระบบ -->
                    <div class="relative inline-flex items-center">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-2 py-2 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                                    <span>ตั้งค่า</span>
                                    <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <!-- รายการจากเมนูตั้งค่าเดิม -->
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'settings']) }}">
                                    ตั้งค่าระบบ
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'users']) }}">
                                    ผู้ใช้งาน
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'roles']) }}">
                                    บทบาท
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'permissions']) }}">
                                    สิทธิ์การใช้งาน
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'activity-logs']) }}">
                                    ประวัติการใช้งาน
                                </x-dropdown-link>
                                
                                <!-- รายการจากเมนูระบบ -->
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'system-integration']) }}">
                                    การเชื่อมต่อระบบภายนอก
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'custom-reports']) }}">
                                    รายงานแบบกำหนดเอง
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'alert-system']) }}">
                                    ระบบแจ้งเตือนอัตโนมัติ
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'mobile-app']) }}">
                                    Mobile Application
                                </x-dropdown-link>
                                <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'data-import-export']) }}">
                                    นำเข้า/ส่งออกข้อมูล
                                </x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>



                    @else
                    <a href="{{ route('about') }}" class="inline-flex items-center px-2 py-2 border-b-2 {{ request()->routeIs('about') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        เกี่ยวกับเรา
                    </a>
                    @endauth
                </div>
            </div>


                    

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center">
                @auth
                <div class="relative">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center px-2 py-2 text-sm font-medium text-white hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ml-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('dashboard')">
                                DashBoard
                            </x-dropdown-link>
                            
                            <x-dropdown-link href="{{ route('coming-soon', ['feature' => 'profile']) }}">
                                โปรไฟล์
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                    ออกจากระบบ
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
                @else
                <div class="relative">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-accent-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-accent-600 active:bg-accent-700 focus:outline-none focus:border-accent-700 focus:ring focus:ring-accent-200 disabled:opacity-25 transition">
                        เข้าสู่ระบบ
                    </a>
                </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-blue-700 focus:outline-none focus:bg-blue-700 focus:text-gray-200 transition duration-150 ease-in-out">
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
            <a href="{{ route('home') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('home') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                Home
            </a>

            @auth
            <!-- รวม Dashboard เป็นเมนูเดียวสำหรับ Mobile -->
            <a href="{{ route('executive.dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') || request()->routeIs('executive.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                Dashboard
            </a>

            <div class="px-4 py-2 text-white font-medium border-l-4 border-transparent">องค์กร</div>
            <a href="{{ route('companies.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('companies.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                บริษัท
            </a>
            <a href="{{ route('departments.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('departments.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                แผนก
            </a>
            <a href="{{ route('positions.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('positions.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ตำแหน่ง
            </a>
            
            <div class="px-4 py-2 text-white font-medium border-l-4 border-transparent">ฝ่ายบุคคล</div>
            <a href="{{ route('employees.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('employees.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                พนักงาน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'work-shifts']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('work-shifts') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                กะการทำงาน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'leave-types']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('leave-types') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ประเภทการลา
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'leaves']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('leaves') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การลา
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'attendances']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('attendances') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การลงเวลา
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'time-attendance']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('time-attendance') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การลงเวลาทำงาน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'leave-management']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('leave-management') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การจัดการวันลา
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'payroll']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('payroll') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                เงินเดือนและค่าตอบแทน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'performance-evaluation']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('performance-evaluation') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การประเมินผลงาน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'training']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('training') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การฝึกอบรมและพัฒนา
            </a>
            
            <div class="px-4 py-2 text-white font-medium border-l-4 border-transparent">ฝ่ายขาย</div>
            <a href="{{ route('customers.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('customers.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ลูกค้า
            </a>
            <a href="{{ route('quotations.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('quotations.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ใบเสนอราคา
            </a>
            <a href="{{ route('orders.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('orders.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ใบสั่งขาย
            </a>
            
            <div class="px-4 py-2 text-white font-medium border-l-4 border-transparent">สินค้า</div>
            <a href="{{ route('products.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('products.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                สินค้าและบริการ
            </a>
            <a href="{{ route('product-categories.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('product-categories.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                หมวดหมู่สินค้า
            </a>
            <a href="{{ route('units.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('units.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                หน่วยนับ
            </a>
            <a href="{{ route('stock-movements.index') }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('stock-movements.*') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การเคลื่อนไหวสินค้า
            </a>

            <!-- ตั้งค่าและระบบ (รวมแล้ว) สำหรับ Mobile -->
            <div class="px-4 py-2 text-white font-medium border-l-4 border-transparent">ตั้งค่า</div>
            <a href="{{ route('coming-soon', ['feature' => 'settings']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('settings') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ตั้งค่าระบบ
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'users']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('users') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ผู้ใช้งาน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'roles']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('roles') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                บทบาท
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'permissions']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('permissions') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                สิทธิ์การใช้งาน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'activity-logs']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('activity-logs') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ประวัติการใช้งาน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'system-integration']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('system-integration') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การเชื่อมต่อระบบภายนอก
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'custom-reports']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('custom-reports') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                รายงานแบบกำหนดเอง
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'alert-system']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('alert-system') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ระบบแจ้งเตือนอัตโนมัติ
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'mobile-app']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('mobile-app') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                Mobile Application
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'data-import-export']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('data-import-export') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                นำเข้า/ส่งออกข้อมูล
            </a>

            <!-- เพิ่มเมนูฝ่ายจัดซื้อสำหรับ mobile view -->
            <div class="px-4 py-2 text-white font-medium border-l-4 border-transparent">ฝ่ายจัดซื้อ</div>
            <a href="{{ route('coming-soon', ['feature' => 'suppliers']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('suppliers') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ผู้จำหน่าย
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'purchase-requisitions']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('purchase-requisitions') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                คำขอซื้อ
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'purchase-orders']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('purchase-orders') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ใบสั่งซื้อ
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'goods-receipt']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('goods-receipt') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การรับสินค้า
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'supplier-invoices']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('supplier-invoices') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ใบแจ้งหนี้จากผู้จำหน่าย
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'payments']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('payments') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การจ่ายเงิน
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'supplier-evaluation']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('supplier-evaluation') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                การประเมินผู้จำหน่าย
            </a>
            <a href="{{ route('coming-soon', ['feature' => 'purchasing-reports']) }}" class="block pl-6 pr-4 py-2 border-l-4 {{ request()->routeIs('purchasing-reports') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                รายงานการจัดซื้อ
            </a>
            @else
            <a href="{{ route('about') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('about') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                เกี่ยวกับเรา
            </a>

            <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('login') ? 'border-accent-400 text-accent-400 bg-blue-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-blue-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                เข้าสู่ระบบ
            </a>
            @endauth
        </div>
    </div>
</nav>