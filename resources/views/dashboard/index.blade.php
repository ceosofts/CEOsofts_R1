<x-app-layout>
    <x-slot name="header">
        แดชบอร์ด
    </x-slot>
    
    <x-slot name="breadcrumbs">
        ['แดชบอร์ด' => route('dashboard')]
    </x-slot>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Departments Stats -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary-600">แผนก</p>
                    <p class="text-2xl font-semibold text-secondary-900">{{ $stats['departments'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-primary-50 text-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Employees Stats -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary-600">พนักงาน</p>
                    <p class="text-2xl font-semibold text-secondary-900">{{ $stats['employees'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-primary-50 text-primary-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Active Employees Stats -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-secondary-600">พนักงานที่กำลังทำงาน</p>
                    <p class="text-2xl font-semibold text-secondary-900">{{ $stats['activeEmployees'] }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-50 text-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Department Structure -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-secondary-900 mb-4">โครงสร้างแผนก</h3>
                
                @if($departmentStructure->isEmpty())
                    <p class="text-secondary-500 py-4">ยังไม่มีแผนกในระบบ</p>
                @else
                    <div class="space-y-2">
                        @foreach($departmentStructure as $department)
                            <x-tree-view :data="$department" />
                        @endforeach
                    </div>
                @endif
                
                <div class="mt-4 pt-3 border-t border-secondary-200">
                    <a href="{{ route('departments.index') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                        ดูแผนกทั้งหมด
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-secondary-900 mb-4">กิจกรรมล่าสุด</h3>
                
                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="border-l-4 border-primary-400 pl-3 py-1">
                            <p class="text-secondary-900">{{ $activity['description'] }}</p>
                            <p class="text-xs text-secondary-500">
                                โดย {{ $activity['user'] }} เมื่อ {{ $activity['created_at']->diffForHumans() }}
                            </p>
                        </div>
                    @empty
                        <p class="text-secondary-500 py-2">ไม่พบกิจกรรมล่าสุด</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
