@php
use Illuminate\Support\Facades\Auth;
@endphp
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

                    @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('dashboard') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        แผงควบคุม
                    </a>

                    <a href="{{ route('customers.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('customers.*') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        ลูกค้า
                    </a>

                    <a href="{{ route('quotations.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('quotations.*') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        ใบเสนอราคา
                    </a>

                    <a href="{{ route('orders.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('orders.*') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        ใบสั่งขาย
                    </a>
                    @else
                    <a href="{{ route('about') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('about') ? 'border-accent-400' : 'border-transparent' }} text-sm font-medium leading-5 text-white hover:text-gray-200 hover:border-accent-300 focus:outline-none focus:text-gray-200 focus:border-accent-400 transition duration-150 ease-in-out">
                        เกี่ยวกับเรา
                    </a>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                @auth
                <div class="relative ml-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-white hover:text-gray-200 focus:outline-none transition duration-150 ease-in-out">
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
                                แผงควบคุม
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

            @auth
            <a href="{{ route('dashboard') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('dashboard') ? 'border-accent-400 text-accent-400 bg-primary-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                แผงควบคุม
            </a>

            <a href="{{ route('customers.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('customers.*') ? 'border-accent-400 text-accent-400 bg-primary-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ลูกค้า
            </a>

            <a href="{{ route('quotations.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('quotations.*') ? 'border-accent-400 text-accent-400 bg-primary-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ใบเสนอราคา
            </a>

            <a href="{{ route('orders.index') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('orders.*') ? 'border-accent-400 text-accent-400 bg-primary-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                ใบสั่งขาย
            </a>
            @else
            <a href="{{ route('about') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('about') ? 'border-accent-400 text-accent-400 bg-primary-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                เกี่ยวกับเรา
            </a>

            <a href="{{ route('login') }}" class="block pl-3 pr-4 py-2 border-l-4 {{ request()->routeIs('login') ? 'border-accent-400 text-accent-400 bg-primary-900' : 'border-transparent text-gray-200 hover:text-white hover:bg-primary-700 hover:border-accent-300' }} text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                เข้าสู่ระบบ
            </a>
            @endauth
        </div>
    </div>
</nav>