<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            แผงควบคุมสำหรับผู้บริหาร
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Organization KPI Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-gray-500 text-sm uppercase font-medium">บริษัท</h3>
                    <div class="flex items-center mt-2">
                        <div class="p-2 rounded-full bg-blue-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-2xl font-bold">{{ $organizationStats['companies'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-gray-500 text-sm uppercase font-medium">แผนก</h3>
                    <div class="flex items-center mt-2">
                        <div class="p-2 rounded-full bg-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-2xl font-bold">{{ $organizationStats['departments'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-gray-500 text-sm uppercase font-medium">ตำแหน่ง</h3>
                    <div class="flex items-center mt-2">
                        <div class="p-2 rounded-full bg-yellow-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-2xl font-bold">{{ $organizationStats['positions'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-gray-500 text-sm uppercase font-medium">พนักงาน</h3>
                    <div class="flex items-center mt-2">
                        <div class="p-2 rounded-full bg-purple-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-2xl font-bold">{{ $organizationStats['employees'] }}</h2>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-gray-500 text-sm uppercase font-medium">พนักงานปฏิบัติงาน</h3>
                    <div class="flex items-center mt-2">
                        <div class="p-2 rounded-full bg-indigo-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-2xl font-bold">{{ $organizationStats['active_employees'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales KPI Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-gray-500 text-sm uppercase font-medium">ลูกค้าทั้งหมด</h3>
                    <div class="flex items-center mt-2">
                        <div class="p-2 rounded-full bg-blue-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-2xl font-bold">{{ $salesStats['customers'] }}</h2>
                            <p class="text-sm text-gray-500">เดือนนี้ +{{ $salesStats['customers_this_month'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-gray-500 text-sm uppercase font-medium">ใบเสนอราคา</h3>
                    <div class="flex items-center mt-2">
                        <div class="p-2 rounded-full bg-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-2xl font-bold">{{ $salesStats['quotations'] }}</h2>
                            <p class="text-sm text-gray-500">เดือนนี้ +{{ $salesStats['quotations_this_month'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-gray-500 text-sm uppercase font-medium">คำสั่งซื้อ</h3>
                    <div class="flex items-center mt-2">
                        <div class="p-2 rounded-full bg-yellow-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h2 class="text-2xl font-bold">{{ $salesStats['orders'] }}</h2>
                            <p class="text-sm text-gray-500">เดือนนี้ +{{ $salesStats['orders_this_month'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-lg font-medium mb-4">ยอดขายรายเดือน {{ Carbon\Carbon::now()->year }}</h3>
                    <div class="h-64">
                        <canvas id="monthlySalesChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-4">
                    <h3 class="text-lg font-medium mb-4">พนักงานตามแผนก</h3>
                    <div class="h-64">
                        <canvas id="employeesByDepartmentChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Action Cards Row -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <a href="{{ route('organization.structure.index') }}" class="bg-white rounded-lg shadow p-4 hover:bg-blue-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-blue-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium">โครงสร้างองค์กร</h3>
                            <p class="text-sm text-gray-500">ดูแผนผังองค์กรทั้งหมด</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('employees.index') }}" class="bg-white rounded-lg shadow p-4 hover:bg-green-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium">พนักงาน</h3>
                            <p class="text-sm text-gray-500">จัดการข้อมูลพนักงาน</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('orders.index') }}" class="bg-white rounded-lg shadow p-4 hover:bg-yellow-50 transition-colors">
                    <div class="flex items-center">
                        <div class="p-2 rounded-full bg-yellow-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="font-medium">คำสั่งซื้อ</h3>
                            <p class="text-sm text-gray-500">จัดการคำสั่งซื้อทั้งหมด</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    @push('scripts')
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // ข้อมูลสำหรับ chart ยอดขายรายเดือน
        const monthlySalesData = {
            labels: @json($monthlySales['labels']),
            datasets: [{
                label: 'ยอดขาย (บาท)',
                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 2,
                data: @json($monthlySales['data']),
                tension: 0.4,
                fill: true,
            }]
        };
        
        const monthlySalesConfig = {
            type: 'line',
            data: monthlySalesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        };
        
        // ข้อมูลสำหรับ chart พนักงานตามแผนก
        const employeesData = {
            labels: @json($employeesByDepartment['labels']),
            datasets: [{
                label: 'จำนวนพนักงาน',
                backgroundColor: [
                    'rgba(59, 130, 246, 0.5)',
                    'rgba(16, 185, 129, 0.5)',
                    'rgba(245, 158, 11, 0.5)',
                    'rgba(139, 92, 246, 0.5)',
                    'rgba(239, 68, 68, 0.5)',
                    'rgba(14, 165, 233, 0.5)',
                ],
                borderColor: [
                    'rgba(59, 130, 246, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(139, 92, 246, 1)',
                    'rgba(239, 68, 68, 1)',
                    'rgba(14, 165, 233, 1)',
                ],
                borderWidth: 1,
                data: @json($employeesByDepartment['data']),
            }]
        };
        
        const employeesConfig = {
            type: 'doughnut',
            data: employeesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        };

        // สร้าง charts เมื่อโหลดหน้าเสร็จ
        window.addEventListener('DOMContentLoaded', () => {
            const salesCtx = document.getElementById('monthlySalesChart').getContext('2d');
            const employeesCtx = document.getElementById('employeesByDepartmentChart').getContext('2d');
            
            new Chart(salesCtx, monthlySalesConfig);
            new Chart(employeesCtx, employeesConfig);
        });
    </script>
    @endpush
</x-app-layout>
