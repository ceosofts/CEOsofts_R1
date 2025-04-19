<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Dashboard Header -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">แผงควบคุมผู้บริหาร</h1>
                            <p class="text-gray-500 dark:text-gray-400 mt-1">{{ isset($company) ? $company->name : 'ภาพรวมองค์กร' }} - {{ now()->format('d/m/Y') }}</p>
                        </div>
                        
                        <!-- ส่วนเลือกบริษัท -->
                        @if(isset($userCompanies) && $userCompanies->count() > 0)
                            <div class="mt-4 md:mt-0">
                                <form action="{{ route('executive.new-switch-company') }}" method="POST" class="flex items-center bg-gray-50 dark:bg-gray-700 p-2 rounded-lg shadow-sm">
                                    @csrf
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <label for="company_selector" class="mr-2 text-sm font-medium">เลือกบริษัท:</label>
                                    <select name="company_id" id="company_selector" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 bg-white dark:bg-gray-800" onchange="this.form.submit()">
                                        @foreach($userCompanies as $userCompany)
                                            <option value="{{ $userCompany->id }}" {{ session('company_id') == $userCompany->id ? 'selected' : '' }}>
                                                {{ $userCompany->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        @endif
                    </div>

                    @if(session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-4">
                            <div class="flex">
                                <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="font-semibold">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if(isset($error))
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 text-red-700 dark:text-red-200 px-4 py-3 rounded mb-4">
                            <h3 class="font-bold">ข้อผิดพลาด</h3>
                            <p>{{ $error }}</p>
                        </div>
                    @else
                        <!-- Quick Stats Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                            <!-- พนักงาน -->
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md border-l-4 border-blue-500 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">พนักงาน</p>
                                        <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['employees_count'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">พนักงานที่ทำงานอยู่: {{ $stats['active_employees'] }}</p>
                                    </div>
                                    <div class="bg-blue-100 dark:bg-blue-800 p-3 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('employees.index') }}" class="text-sm text-blue-500 dark:text-blue-300 hover:underline">ดูรายละเอียด &rarr;</a>
                                </div>
                            </div>

                            <!-- แผนก -->
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md border-l-4 border-green-500 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">แผนก</p>
                                        <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['departments_count'] }}</p>
                                    </div>
                                    <div class="bg-green-100 dark:bg-green-800 p-3 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-500 dark:text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('departments.index') }}" class="text-sm text-green-500 dark:text-green-300 hover:underline">ดูรายละเอียด &rarr;</a>
                                </div>
                            </div>

                            <!-- ลูกค้า -->
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md border-l-4 border-purple-500 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">ลูกค้า</p>
                                        <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['customers_count'] }}</p>
                                    </div>
                                    <div class="bg-purple-100 dark:bg-purple-800 p-3 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-500 dark:text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('customers.index') }}" class="text-sm text-purple-500 dark:text-purple-300 hover:underline">ดูรายละเอียด &rarr;</a>
                                </div>
                            </div>

                            <!-- คำสั่งซื้อ -->
                            <div class="bg-white dark:bg-gray-700 p-6 rounded-lg shadow-md border-l-4 border-yellow-500 hover:shadow-lg transition-shadow duration-300">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">คำสั่งซื้อ</p>
                                        <p class="text-3xl font-bold text-gray-800 dark:text-white">{{ $stats['orders_count'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">เดือนนี้: {{ $stats['orders_this_month'] }}</p>
                                    </div>
                                    <div class="bg-yellow-100 dark:bg-yellow-800 p-3 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 dark:text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <a href="{{ route('orders.index') }}" class="text-sm text-yellow-500 dark:text-yellow-300 hover:underline">ดูรายละเอียด &rarr;</a>
                                </div>
                            </div>
                        </div>

                        <!-- Chart Section (Optional - ในกรณีที่มี $monthlySales data) -->
                        @isset($monthlySales)
                        <div class="mb-8">
                            <div class="bg-white dark:bg-gray-700 p-4 rounded-lg shadow">
                                <h3 class="text-xl font-bold mb-4">ยอดขายรายเดือน</h3>
                                <div class="h-80">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                        @endisset
                        
                        <!-- Content Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <!-- ออเดอร์ล่าสุด -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden">
                                <div class="p-4 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">ออเดอร์ล่าสุด</h3>
                                    <a href="{{ route('orders.index') }}" class="text-sm text-blue-500 dark:text-blue-300 hover:underline">ดูทั้งหมด</a>
                                </div>
                                
                                <div class="overflow-x-auto">
                                    @if(count($recentOrders) > 0)
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead class="bg-gray-50 dark:bg-gray-600">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">เลขที่</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">ลูกค้า</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">วันที่</th>
                                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">จำนวนเงิน</th>
                                                    <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">สถานะ</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                                @foreach($recentOrders as $order)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $order->customer_name }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y') }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-gray-500 dark:text-gray-300">{{ number_format($order->total_amount, 2) }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-center">
                                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                                @if($order->status == 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif
                                                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif
                                                                @if($order->status == 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif
                                                            ">
                                                                {{ ucfirst($order->status) }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p>ไม่มีข้อมูลออเดอร์</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- ลูกค้าล่าสุด -->
                            <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden">
                                <div class="p-4 border-b border-gray-200 dark:border-gray-600 flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-gray-800 dark:text-white">ลูกค้าล่าสุด</h3>
                                    <a href="{{ route('customers.index') }}" class="text-sm text-blue-500 dark:text-blue-300 hover:underline">ดูทั้งหมด</a>
                                </div>
                                
                                <div class="overflow-x-auto">
                                    @if(count($recentCustomers) > 0)
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead class="bg-gray-50 dark:bg-gray-600">
                                                <tr>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">ชื่อ</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">อีเมล</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">เบอร์โทรศัพท์</th>
                                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">วันที่เพิ่ม</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                                @foreach($recentCustomers as $customer)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $customer->name }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $customer->email }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $customer->phone }}</td>
                                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ \Carbon\Carbon::parse($customer->created_at)->format('d/m/Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 dark:text-gray-500 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p>ไม่มีข้อมูลลูกค้า</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @isset($monthlySales)
    <!-- JavaScript สำหรับสร้างกราฟ -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesChart').getContext('2d');
            const monthlyData = @json($monthlySales);
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: monthlyData.labels,
                    datasets: [{
                        label: 'ยอดขายรายเดือน',
                        data: monthlyData.data,
                        backgroundColor: 'rgba(59, 130, 246, 0.5)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB', minimumFractionDigits: 0 }).format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return new Intl.NumberFormat('th-TH', { style: 'currency', currency: 'THB' }).format(context.raw);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endisset
</x-app-layout>
