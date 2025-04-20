<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="mb-6">
                        <form action="{{ route('delivery-orders.index') }}" method="GET" class="flex flex-wrap gap-4">
                            <div class="flex-1">
                                <x-input-label for="search" :value="__('ค้นหา')" />
                                <x-text-input id="search" name="search" type="text" class="w-full" placeholder="ค้นหาตามเลขที่ใบส่งสินค้า หรือชื่อลูกค้า" value="{{ request('search') }}" />
                            </div>

                            <div class="w-40">
                                <x-input-label for="status" :value="__('สถานะ')" />
                                <select id="status" name="status" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="all">ทั้งหมด</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>ส่งมอบแล้ว</option>
                                    <option value="partial_delivered" {{ request('status') == 'partial_delivered' ? 'selected' : '' }}>ส่งมอบบางส่วน</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                            </div>

                            <div class="w-40">
                                <x-input-label for="date_from" :value="__('ตั้งแต่วันที่')" />
                                <x-text-input id="date_from" name="date_from" type="date" class="w-full" value="{{ request('date_from') }}" />
                            </div>

                            <div class="w-40">
                                <x-input-label for="date_to" :value="__('ถึงวันที่')" />
                                <x-text-input id="date_to" name="date_to" type="date" class="w-full" value="{{ request('date_to') }}" />
                            </div>

                            <div class="w-auto">
                                <x-input-label class="invisible" for="filter_button" :value="__('กรอง')" />
                                <x-primary-button class="h-10">{{ __('กรอง') }}</x-primary-button>
                            </div>

                            @if(request('search') || request('status') || request('date_from') || request('date_to'))
                                <div class="flex items-end">
                                    <a href="{{ route('delivery-orders.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                                        {{ __('ล้างตัวกรอง') }}
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>

                    <!-- Delivery Orders Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white" id="deliveryOrderTable">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">
                                        เลขที่
                                        <span class="text-xs ml-1">(เรียงจากล่าสุด)</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </th>
                                    <th class="py-2 px-4 border-b text-left">วันที่ส่งสินค้า</th>
                                    <th class="py-2 px-4 border-b text-left">ลูกค้า</th>
                                    <th class="py-2 px-4 border-b text-left">เลขที่ใบสั่งขาย</th>
                                    <th class="py-2 px-4 border-b text-left">สถานะ</th>
                                    <th class="py-2 px-4 border-b text-left">เลขพัสดุ</th>
                                    <th class="py-2 px-4 border-b text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deliveryOrders as $deliveryOrder)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('delivery-orders.show', $deliveryOrder) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                {{ $deliveryOrder->delivery_number }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ $deliveryOrder->delivery_date->format('d/m/Y') }}</td>
                                        <td class="py-2 px-4 border-b">{{ $deliveryOrder->customer->name }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('orders.show', $deliveryOrder->order) }}" class="text-blue-600 hover:text-blue-900">
                                                {{ $deliveryOrder->order->order_number }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 bg-{{ $deliveryOrder->statusColor }}-100 text-{{ $deliveryOrder->statusColor }}-800 rounded-md text-xs">
                                                {{ $deliveryOrder->statusText }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ $deliveryOrder->tracking_number ?? '-' }}</td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('delivery-orders.show', $deliveryOrder) }}" class="text-blue-600 hover:text-blue-900" title="ดูรายละเอียด">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                @if(!in_array($deliveryOrder->delivery_status, ['delivered', 'cancelled']))
                                                    <a href="{{ route('delivery-orders.edit', $deliveryOrder) }}" class="text-yellow-600 hover:text-yellow-900" title="แก้ไข">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if(in_array($deliveryOrder->delivery_status, ['pending', 'cancelled']))
                                                    <button type="button" onclick="confirmDelete('{{ $deliveryOrder->id }}')" class="text-red-600 hover:text-red-900" title="ลบ">
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
                                        <td colspan="7" class="py-6 text-center text-gray-500">ไม่พบข้อมูลใบส่งสินค้า</td>
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
    
    <!-- หมายเหตุสำหรับนักพัฒนา -->
    <!--
    การเรียงลำดับควรทำที่ Controller จะดีกว่า:
    
    public function index(Request $request)
    {
        $query = DeliveryOrder::with(['customer', 'order']);
        
        // ตัวกรองต่างๆ...
        
        // เพิ่มการเรียงลำดับเริ่มต้นตามเลขที่จากมากไปน้อย
        $query->orderByDesc('delivery_number');
        
        $deliveryOrders = $query->paginate(15);
        
        return view('delivery_orders.index', compact('deliveryOrders'));
    }
    -->
</x-app-layout>
