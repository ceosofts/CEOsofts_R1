<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800 dark:text-blue-300">
                {{ __('รายการใบส่งสินค้า') }}
            </h2>
            <a href="{{ route('delivery-orders.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('สร้างใบส่งสินค้าใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('delivery-orders.index') }}" method="GET">
                        <!-- Grid layout แบบแถวเดียว -->
                        <div class="flex flex-wrap items-end gap-3">
                            <!-- ช่อง "ค้นหา" -->
                            <div class="w-full md:w-64 lg:w-70">
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ค้นหา</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                                        class="block w-full pl-10 pr-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="เลขที่ใบส่งสินค้า, เลขที่ใบสั่งขาย">
                                </div>
                            </div>

                            <!-- ช่อง "ลูกค้า" -->
                            <div class="w-full md:w-40 lg:w-48">
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ลูกค้า</label>
                                <select name="customer_id" id="customer_id"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach(\App\Models\Customer::where('company_id', session('company_id'))->orderBy('name')->get() as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- ช่อง "สถานะ" -->
                            <div class="w-full md:w-40 lg:w-48">
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                <select id="status" name="status"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>ส่งมอบแล้ว</option>
                                    <option value="partial_delivered" {{ request('status') == 'partial_delivered' ? 'selected' : '' }}>ส่งมอบบางส่วน</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                            </div>

                            <!-- วันที่ (ย่อให้สั้นลง) -->
                            <div class="w-full md:w-44 lg:w-60">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ช่วงวันที่</label>
                                <div class="flex space-x-1">
                                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                                        class="block w-1/2 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                                        class="block w-1/2 py-2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>

                            <!-- ปุ่มค้นหาและรีเซ็ต -->
                            <div class="flex space-x-2">
                                <button type="submit" class="inline-flex items-center px-4 h-10 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    ค้นหา
                                </button>
                                <a href="{{ route('delivery-orders.index') }}" class="inline-flex items-center px-4 h-10 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                    <svg class="w-4 h-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                    </svg>
                                    รีเซ็ต
                                </a>
                            </div>
                        

                        <!-- For debugging purposes - hidden by default -->
                        @if(config('app.debug'))
                        <div class="mt-2 text-right">
                            <button type="button" id="toggleDebug" class="text-xs text-gray-500 dark:text-gray-400 hover:underline">Debug</button>
                        </div>
                        <div id="debugInfo" class="hidden mt-2 p-3 bg-gray-100 dark:bg-gray-700 text-xs rounded">
                            <p class="font-semibold mb-1">Debug: Search Parameters</p>
                            <ul>
                                <li>Search: {{ request('search') ?? 'null' }}</li>
                                <li>Status: {{ request('status') ?? 'null' }}</li>
                                <li>Date From: {{ request('date_from') ?? 'null' }}</li>
                                <li>Date To: {{ request('date_to') ?? 'null' }}</li>
                                <li>Current URL: {{ url()->full() }}</li>
                            </ul>
                        </div>

                        </div>

                        <script>
                            document.getElementById('toggleDebug').addEventListener('click', function() {
                                const debugInfo = document.getElementById('debugInfo');
                                const isHidden = debugInfo.classList.contains('hidden');
                                debugInfo.classList.toggle('hidden');
                                this.textContent = isHidden ? 'Hide Debug Info' : 'Debug';
                            });
                        </script>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Delivery Orders Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600" id="deliveryOrderTable">
                            <thead class="bg-gray-100 dark:bg-gray-600">
                                <tr>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-gray-700 dark:text-gray-200">
                                        เลขที่
                                        <span class="text-xs ml-1">(เรียงจากล่าสุด)</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-gray-700 dark:text-gray-200">วันที่ส่งสินค้า</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-gray-700 dark:text-gray-200">ลูกค้า</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-gray-700 dark:text-gray-200">เลขที่ใบสั่งขาย</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-gray-700 dark:text-gray-200">สถานะ</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left text-gray-700 dark:text-gray-200">เลขพัสดุ</th>
                                    <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center text-gray-700 dark:text-gray-200">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveryOrders as $deliveryOrder)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                        <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">
                                            <a href="{{ route('delivery-orders.show', $deliveryOrder) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                                                {{ $deliveryOrder->delivery_number }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">{{ $deliveryOrder->delivery_date->format('d/m/Y') }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">{{ $deliveryOrder->customer->name }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">
                                            <a href="{{ route('orders.show', $deliveryOrder->order) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                                                {{ $deliveryOrder->order->order_number }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">
                                            <span class="px-2 py-1 bg-{{ $deliveryOrder->statusColor }}-100 text-{{ $deliveryOrder->statusColor }}-800 dark:bg-{{ $deliveryOrder->statusColor }}-900 dark:text-{{ $deliveryOrder->statusColor }}-200 rounded-md text-xs">
                                                {{ $deliveryOrder->statusText }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700">{{ $deliveryOrder->tracking_number ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b border-gray-200 dark:border-gray-700 text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('delivery-orders.show', $deliveryOrder) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300" title="ดูรายละเอียด">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                @if(!in_array($deliveryOrder->delivery_status, ['delivered', 'cancelled']))
                                                    <a href="{{ route('delivery-orders.edit', $deliveryOrder) }}" class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-900 dark:hover:text-yellow-300" title="แก้ไข">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if(in_array($deliveryOrder->delivery_status, ['pending', 'cancelled']))
                                                    <button type="button" onclick="confirmDelete('{{ $deliveryOrder->id }}')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300" title="ลบ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                    
                                                    <form id="delete-form-{{ $deliveryOrder->id }}" action="{{ route('delivery-orders.destroy', $deliveryOrder) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-gray-500 dark:text-gray-400">ไม่พบข้อมูลใบส่งสินค้า</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $deliveryOrders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(deliveryOrderId) {
            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบใบส่งสินค้านี้?')) {
                document.getElementById('delete-form-' + deliveryOrderId).submit();
            }
        }

        // เรียงลำดับรายการตามเลขที่จากมากไปหาน้อยเมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('deliveryOrderTable');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // ข้ามการเรียงถ้าไม่มีข้อมูล
            if (rows.length === 1 && rows[0].querySelector('td[colspan]')) {
                return;
            }
            
            rows.sort((a, b) => {
                const aNumber = a.querySelector('td:first-child a')?.textContent.trim() || '';
                const bNumber = b.querySelector('td:first-child a')?.textContent.trim() || '';
                return bNumber.localeCompare(aNumber, undefined, {numeric: true}); // เรียงจากมากไปน้อย
            });
            
            // ล้างตารางและเพิ่มแถวที่เรียงแล้ว
            rows.forEach(row => tbody.appendChild(row));
        });
    </script>
</x-app-layout>
