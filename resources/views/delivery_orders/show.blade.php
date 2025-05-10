<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                รายละเอียดใบส่งสินค้า: {{ $deliveryOrder->delivery_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('delivery-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    กลับไปรายการ
                </a>
                
                <!-- เพิ่มปุ่มดูตัวอย่าง -->
                <button id="preview-button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    ดูตัวอย่าง
                </button>
                
                <!-- เพิ่มปุ่มพิมพ์ -->
                <button id="print-button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    พิมพ์
                </button>
                
                @if(!in_array($deliveryOrder->delivery_status, ['delivered', 'cancelled']))
                    <a href="{{ route('delivery-orders.edit', $deliveryOrder) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-yellow-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        แก้ไข
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @php
                        // กำหนด status classes สำหรับแสดงสถานะต่างๆ
                        $statusClasses = [
                            // สถานะของใบส่งสินค้า
                            'pending' => 'bg-amber-100 text-amber-800',
                            'processing' => 'bg-blue-500 text-white',
                            'shipped' => 'bg-emerald-100 text-emerald-800',
                            'delivered' => 'bg-teal-100 text-teal-800',
                            'returned' => 'bg-rose-100 text-rose-800',
                            'cancelled' => 'bg-slate-100 text-slate-800',
                            'partial_delivered' => 'bg-amber-200 text-amber-900',
                            
                            // สถานะของรายการในใบส่งสินค้า
                            'partial' => 'bg-amber-200 text-amber-900',
                        ];
                        
                        // ฟังก์ชั่นสำหรับดึงคลาสที่เหมาะสมสำหรับแต่ละสถานะ
                        // เปลี่ยนเป็นรับพารามิเตอร์ $classes แทนการใช้ global
                        function getStatusClass($status, $classes) {
                            // ถ้า status เป็น null หรือค่าว่าง ให้ใช้ค่าเริ่มต้น 'pending'
                            if ($status === null || $status === '') {
                                return $classes['pending'] ?? 'bg-gray-100 text-gray-800';
                            }
                            
                            // ตรวจสอบว่ามีคลาสที่ตรงกับสถานะหรือไม่
                            if (isset($classes[$status])) {
                                return $classes[$status];
                            }
                            
                            // ถ้าไม่มีคลาสที่ตรงกับสถานะ ให้ใช้ค่าเริ่มต้น
                            return $classes['pending'] ?? 'bg-gray-100 text-gray-800';
                        }
                        
                        // ใช้ status ปัจจุบันหรือค่าเริ่มต้น (กันไม่ให้เป็น null)
                        $currentStatus = $deliveryOrder->delivery_status ?? 'pending';
                        // ส่งอาเรย์ $statusClasses เป็นพารามิเตอร์
                        $statusClass = getStatusClass($currentStatus, $statusClasses);
                    @endphp

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
                    
                    <!-- สถานะและการดำเนินการ -->
                    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="mb-4 sm:mb-0">
                            <h3 class="text-lg font-medium">สถานะ: 
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-{{ $deliveryOrder->statusColor }}-100 text-{{ $deliveryOrder->statusColor }}-800">
                                    {{ $deliveryOrder->statusText }}
                                </span>
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                สร้างโดย: {{ $deliveryOrder->creator->name ?? 'ไม่ระบุ' }} เมื่อ {{ $deliveryOrder->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            @if($deliveryOrder->delivery_status == 'pending')
                                <form action="{{ route('delivery-orders.update', $deliveryOrder) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="delivery_status" value="processing">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                        เริ่มดำเนินการ
                                    </button>
                                </form>
                            @endif
                            
                            @if($deliveryOrder->delivery_status == 'processing')
                                <form action="{{ route('delivery-orders.update', $deliveryOrder) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="delivery_status" value="shipped">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-purple-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                        </svg>
                                        จัดส่งสินค้า
                                    </button>
                                </form>
                            @endif
                            
                            @if($deliveryOrder->delivery_status == 'shipped')
                                <form action="{{ route('delivery-orders.update', $deliveryOrder) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="delivery_status" value="delivered">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-green-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        ส่งมอบเรียบร้อย
                                    </button>
                                </form>
                            @endif
                            
                            @if(!in_array($deliveryOrder->delivery_status, ['delivered', 'cancelled']))
                                <form action="{{ route('delivery-orders.update', $deliveryOrder) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="delivery_status" value="cancelled">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        ยกเลิกการจัดส่ง
                                    </button>
                                </form>
                            @endif

                            @if(in_array($deliveryOrder->delivery_status, ['pending', 'cancelled']))
                                <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    ลบใบส่งสินค้า
                                </button>
                                
                                <form id="delete-form" action="{{ route('delivery-orders.destroy', $deliveryOrder) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>

                    <!-- ข้อมูลพื้นฐาน -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium mb-3 border-b pb-1">ข้อมูลใบส่งสินค้า</h3>
                            <table class="w-full">
                                <tr>
                                    <td class="py-1 text-gray-600 w-1/3">เลขที่ใบส่งสินค้า:</td>
                                    <td class="py-1 font-medium">{{ $deliveryOrder->delivery_number }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">อ้างอิงจากใบสั่งขาย:</td>
                                    <td class="py-1">
                                        @if($deliveryOrder->order)
                                            <a href="{{ route('orders.show', $deliveryOrder->order) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $deliveryOrder->order->order_number }}
                                            </a>
                                        @else
                                            <span class="text-red-500">ไม่พบข้อมูลอ้างอิง</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">วันที่จัดส่ง:</td>
                                    <td class="py-1">{{ $deliveryOrder->delivery_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">เลขพัสดุ:</td>
                                    <td class="py-1">{{ $deliveryOrder->tracking_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">วิธีการจัดส่ง:</td>
                                    <td class="py-1">{{ $deliveryOrder->shipping_method ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">ผู้อนุมัติ:</td>
                                    <td class="py-1">
                                        @if($deliveryOrder->approver)
                                            {{ $deliveryOrder->approver->name }} 
                                            @if($deliveryOrder->approved_at)
                                                <span class="text-green-600 text-xs">({{ $deliveryOrder->approved_at->format('d/m/Y H:i') }})</span>
                                            @endif
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">หมายเหตุ:</td>
                                    <td class="py-1">{{ $deliveryOrder->notes ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium mb-3 border-b pb-1">ข้อมูลลูกค้า</h3>
                            <table class="w-full">
                                <tr>
                                    <td class="py-1 text-gray-600 w-1/3">ชื่อลูกค้า:</td>
                                    <td class="py-1 font-medium">
                                        @if($deliveryOrder->customer)
                                            {{ $deliveryOrder->customer->name }}
                                        @else
                                            <span class="text-red-500">ไม่พบข้อมูลลูกค้า</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">อีเมล:</td>
                                    <td class="py-1">{{ $deliveryOrder->customer->email ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">เบอร์โทรศัพท์:</td>
                                    <td class="py-1">{{ $deliveryOrder->customer->phone ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">ชื่อผู้ติดต่อและที่อยู่จัดส่ง:</td>
                                    <td class="py-1">{{ $deliveryOrder->delivery_address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- รายการสินค้า -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-3 border-b pb-1">รายการสินค้า</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 border-b text-center">ลำดับ</th>
                                        <th class="py-2 px-4 border-b text-left">รหัสสินค้า</th>
                                        <th class="py-2 px-4 border-b text-left">รายการ</th>
                                        <th class="py-2 px-4 border-b text-center">จำนวน</th>
                                        <th class="py-2 px-4 border-b text-right">หน่วย</th>
                                        <th class="py-2 px-4 border-b text-center">สถานะ</th>
                                        <th class="py-2 px-4 border-b text-right">หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deliveryOrder->items as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b text-center">{{ $loop->iteration }}</td>

                                            <td class="py-2 px-4 border-b">
                                                {{ $item->product->code ?? $item->product->sku ?? '-' }}<!-- แสดงรหัสสินค้า (code หรือ sku) -->
                                            </td>

                                            <td class="py-2 px-4 border-b">{{ $item->description }}</td>
                                            <td class="py-2 px-4 border-b text-center">{{ number_format($item->quantity) }}</td>
                                            <td class="py-2 px-4 border-b text-right">{{ $item->unit }}</td>
                                            <td class="py-2 px-4 border-b text-center">
                                                <span class="px-2 py-1 rounded-full text-xs font-semibold {{ getStatusClass($item->status, $statusClasses) }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 border-b text-right">{{ $item->notes ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-4 text-center text-gray-500">ไม่พบรายการสินค้า</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- ข้อมูลการติดตามและทรานแซ็คชั่น -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-3 border-b pb-1">ข้อมูลการจัดการ</h3>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium mb-2">ข้อมูลการสร้าง</h4>
                                    <p class="text-sm text-gray-600">
                                        สร้างโดย: {{ $deliveryOrder->creator->name ?? 'ไม่ระบุ' }}<br>
                                        วันที่สร้าง: {{ $deliveryOrder->created_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            <div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="font-medium mb-2">บริษัท</h4>
                                    <p class="text-sm text-gray-600">
                                        @if($deliveryOrder->company)
                                            {{ $deliveryOrder->company->name }}
                                        @else
                                            ไม่พบข้อมูลบริษัท
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- แก้ไขส่วนที่มีการชนกันของ CSS classes (บริเวณบรรทัด 262-265) --}}
                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900">สถานะการส่งสินค้า</h3>
                        <div class="mt-2">
                            <span class="px-2 py-1 text-sm rounded-full {{ $statusClass }}">
                                {{ ucfirst($currentStatus) }}
                            </span>
                        </div>
                    </div>

                    {{-- ส่วนอื่นๆ ที่อาจมีการใช้คลาสเหล่านี้ควรได้รับการแก้ไขเช่นกัน --}}
                    {{-- เช่น ส่วนของการแสดงประวัติการเปลี่ยนสถานะ (ถ้ามี) --}}
                    @if(isset($deliveryOrder->status_history) && count($deliveryOrder->status_history) > 0)
                        <div class="mt-6">
                            <h4 class="font-medium text-gray-700">ประวัติสถานะ</h4>
                            <ul class="mt-2 space-y-2">
                                @foreach($deliveryOrder->status_history as $history)
                                    <li class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs rounded-full {{ getStatusClass($history->status, $statusClasses) }}">
                                            {{ ucfirst($history->status) }}
                                        </span>
                                        <span class="text-sm text-gray-500">{{ $history->created_at->format('d/m/Y H:i') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- นำเข้าส่วน Modal Preview -->
    @include('delivery_orders.preview', ['deliveryOrder' => $deliveryOrder, 'statusClasses' => $statusClasses])

    <!-- เรียกใช้ไฟล์ CSS สำหรับการแสดงตัวอย่างก่อนพิมพ์ -->
    <link rel="stylesheet" href="{{ asset('css/quotation-preview.css') }}">

    <!-- เรียกใช้ไฟล์ JavaScript สำหรับการแสดงตัวอย่างและพิมพ์ -->
    <script src="{{ asset('js/quotation-preview.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const previewModal = document.getElementById('preview-modal');
            
            // ปุ่มดูตัวอย่าง
            document.getElementById('preview-button').addEventListener('click', function() {
                previewModal.classList.remove('modal-hidden');
                previewModal.classList.add('flex');
            });
            
            // ปุ่มปิดตัวอย่าง
            document.getElementById('close-preview').addEventListener('click', function() {
                previewModal.classList.add('modal-hidden');
                previewModal.classList.remove('flex');
            });
            
            // ปิด modal เมื่อคลิกนอกพื้นที่
            previewModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('modal-hidden');
                    this.classList.remove('flex');
                }
            });
            
            // Escape key สำหรับปิด modal
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !previewModal.classList.contains('modal-hidden')) {
                    previewModal.classList.add('modal-hidden');
                    previewModal.classList.remove('flex');
                }
            });
            
            // ปุ่มพิมพ์ - แก้ไขใหม่
            document.getElementById('print-button').addEventListener('click', function() {
                // แสดงสถานะกำลังโหลด
                this.disabled = true;
                const originalHtml = this.innerHTML;
                this.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    กำลังโหลด...
                `;
                
                // เปิด URL โดยตรงในหน้าต่างใหม่แทนการใช้ fetch API
                const printUrl = "{{ route('delivery-orders.print', $deliveryOrder) }}";
                const printWindow = window.open(printUrl, '_blank');
                
                if (!printWindow) {
                    alert('โปรดอนุญาตให้เว็บไซต์เปิดป๊อปอัพหน้าต่างใหม่');
                }
                
                // คืนสถานะปุ่ม
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = originalHtml;
                }, 1000);
            });
        });

        // เพิ่มฟังก์ชันสำหรับการลบใบส่งสินค้า
        function confirmDelete() {
            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบใบส่งสินค้านี้?')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>

    <style>
        /* สไตล์สำหรับ Modal Preview กรณีที่ไฟล์ CSS ภายนอกไม่ทำงาน */
        #preview-modal.modal-hidden {
            display: none;
        }
        
        #preview-content {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            font-family: 'Sarabun', sans-serif;
        }
        
        .grid-cols-2 {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin: 0 0 20px 0;
            padding: 0;
        }
        
        .grid-cols-2 > div {
            width: 48%;
        }
    </style>
</x-app-layout>
