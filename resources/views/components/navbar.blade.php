<header class="bg-white shadow-sm z-10">
    <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6 lg:px-8 py-4">
        <!-- Mobile menu button-->
        <button 
            type="button" 
            class="md:hidden text-secondary-500 hover:text-secondary-900 focus:outline-none"
            x-data="{}"
            x-on:click="$dispatch('toggle-sidebar')"
        >
            <span class="sr-only">เปิดเมนู</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
        
        <!-- Logo for mobile view -->
        <div class="flex md:hidden items-center">
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="/img/logo-sm.svg" alt="CEOsofts logo" class="h-8 w-auto">
            </a>
        </div>

        <!-- Search -->
        <div class="flex-1 max-w-md hidden md:block">
            <form class="w-full">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-secondary-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" class="bg-secondary-50 border border-secondary-300 text-secondary-900 text-sm rounded-md block w-full pl-10 p-2 focus:ring-primary-500 focus:border-primary-500" placeholder="ค้นหา...">
                </div>
            </form>
        </div>

        <!-- Right side nav items -->
        <div class="flex items-center">
            <!-- Company selector dropdown -->
            <div class="mr-4">
                @if(auth()->check() && session('current_company_id'))
                    <x-dropdown>
                        <x-slot name="trigger">
                            <button class="flex items-center text-sm font-medium text-secondary-700 hover:text-secondary-900">
                                <span>{{ \App\Models\Company::find(session('current_company_id'))->name }}</span>
                                <svg class="ml-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @foreach(auth()->user()->companies as $company)
                                <a href="{{ route('dashboard.switch-company', $company) }}" 
                                   class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100 {{ session('current_company_id') == $company->id ? 'bg-primary-50 text-primary-700' : '' }}">
                                    {{ $company->name }}
                                </a>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                @endif
            </div>

            <!-- User dropdown -->
            <div>
                <x-dropdown>
                    <x-slot name="trigger">
                        <button class="flex items-center text-sm font-medium text-secondary-700 hover:text-secondary-900">
                            <img class="h-8 w-8 rounded-full object-cover" src="{{ auth()->user()->profile_photo_url ?? '/img/undraw_profile.svg' }}" alt="{{ auth()->user()->name }}">
                            <span class="ml-2 hidden md:block">{{ auth()->user()->name }}</span>
                            <svg class="ml-1 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100">
                            โปรไฟล์
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-100">
                                ออกจากระบบ
                            </button>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>
</header>
