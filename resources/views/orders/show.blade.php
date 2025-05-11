<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-3xl text-blue-800">
                {{ __('ใบสั่งขาย') }} #{{ $order->order_number }}
            </h2>
            <div class="flex space-x-2">
                <!-- เปลี่ยนปุ่ม "กลับไปรายการ" เป็นสีเทา -->
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 border border-gray-500 rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปรายการ') }}
                </a>
                
                <!-- ปุ่ม Print -->
                <a href="{{ route('orders.print', $order) }}" target="_blank" onclick="return openPrintWindow(event)" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    {{ __('พิมพ์') }}
                </a>
                
                {{-- ปุ่มแก้ไข --}}
                @if(!in_array($order->status, ['shipped', 'delivered', 'cancelled']))
                <a href="{{ route('orders.edit', $order) }}" class="hidden หinline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('แก้ไข') }}
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif
            
            <!-- สถานะและการดำเนินการ -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="mb-4 sm:mb-0">
                            <h3 class="text-lg font-medium">สถานะ: 
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    @if($order->status == 'draft')
                                        ร่าง
                                    @elseif($order->status == 'confirmed')
                                        ยืนยันแล้ว
                                    @elseif($order->status == 'processing')
                                        กำลังดำเนินการ
                                    @elseif($order->status == 'shipped')
                                        จัดส่งแล้ว
                                    @elseif($order->status == 'delivered')
                                        ส่งมอบแล้ว
                                    @elseif($order->status == 'cancelled')
                                        ยกเลิก
                                    @else
                                        {{ $order->statusText }}
                                    @endif
                                </span>
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                สร้างโดย: {{ $order->creator->name ?? 'ไม่ระบุ' }} เมื่อ {{ $order->created_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            @if($order->status == 'draft')
                                <form action="{{ route('orders.confirm', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        ยืนยันใบสั่งขาย
                                    </button>
                                </form>
                            @endif
                            
                            @if($order->status == 'confirmed')
                                <form action="{{ route('orders.process', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-yellow-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                        </svg>
                                        เริ่มดำเนินการ
                                    </button>
                                </form>
                            @endif
                            
                            @if(in_array($order->status, ['confirmed', 'processing']))
                                @if($order->deliveryOrders()->count() > 0)
                                <button type="button" onclick="showShipModal()" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-purple-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    จัดส่งสินค้า
                                </button>
                                @else
                                <a href="{{ route('delivery-orders.create', ['order_id' => $order->id]) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-purple-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    สร้างใบส่งสินค้า
                                </a>
                                @endif
                            @endif
                            
                            @if($order->status == 'shipped')
                                <form action="{{ route('orders.deliver', $order) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-green-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        ส่งมอบเรียบร้อย
                                    </button>
                                </form>
                            @endif
                            
                            @if(!in_array($order->status, ['shipped', 'delivered', 'cancelled']))
                                <button type="button" onclick="showCancelModal()" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    ยกเลิกใบสั่งขาย
                                </button>
                            @endif

                            @if(in_array($order->status, ['draft', 'cancelled']))
                                <button type="button" onclick="confirmDelete()" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    ลบใบสั่งขาย
                                </button>
                                
                                <form id="delete-form" action="{{ route('orders.destroy', $order) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลพื้นฐาน -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-3">ข้อมูลใบสั่งขาย</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">เลขที่ใบสั่งขาย</p>
                                <p class="font-medium">{{ $order->order_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">วันที่สั่งซื้อ</p>
                                <p class="font-medium">{{ $order->order_date->format('d/m/Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">กำหนดส่งมอบ</p>
                                <p class="font-medium">{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">เลขที่ใบสั่งซื้อลูกค้า</p>
                                <p class="font-medium">{{ $order->customer_po_number ?: '-' }}</p>
                            </div>
                            @if($order->quotation)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">อ้างอิงใบเสนอราคา</p>
                                <p class="font-medium">
                                    <a href="{{ route('quotations.show', $order->quotation) }}" class="text-blue-600 hover:underline dark:text-blue-400">
                                        {{ $order->quotation->quotation_number }}
                                    </a>
                                </p>
                            </div>
                            @endif
                            <div class="col-span-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">พนักงานขาย</p>
                                <p class="font-medium">
                                    @if($order->sales_person_id && $salesPerson = \App\Models\Employee::find($order->sales_person_id))
                                        {{ $salesPerson->employee_code }} - {{ $salesPerson->first_name }} {{ $salesPerson->last_name }}
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">เงื่อนไขการชำระเงิน</p>
                                <p class="font-medium">{{ $order->payment_terms ?: '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-3">ข้อมูลลูกค้า</h3>
                        <p class="font-medium">{{ $order->customer->name }}</p>
                        <p>{{ $order->customer->address }}</p>
                        <p>โทร: {{ $order->customer->phone }}</p>
                        <p>อีเมล: {{ $order->customer->email }}</p>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลการจัดส่ง -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-3">ข้อมูลการจัดส่ง</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">ที่อยู่จัดส่ง</p>
                            <p>{{ $order->shipping_address ?? $order->customer->address ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">วิธีการจัดส่ง</p>
                            <p>{{ $order->shipping_method ?: '-' }}</p>
                            
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 mb-1">ค่าขนส่ง</p>
                            <p>{{ $order->shipping_cost > 0 ? number_format($order->shipping_cost, 2) . ' บาท' : '-' }}</p>
                            
                            @if($order->status == 'shipped' || $order->status == 'delivered')
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 mb-1">เลขพัสดุ</p>
                                <p>{{ $order->tracking_number ?: '-' }}</p>
                                
                                @if($order->shipping_notes)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-3 mb-1">บันทึกการจัดส่ง</p>
                                    <p>{{ $order->shipping_notes }}</p>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- รายการสินค้า -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">รายการสินค้า</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">รหัสสินค้า</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">รายการ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">จำนวน</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">หน่วย</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ราคาต่อหน่วย</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">จำนวนเงิน</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($order->items as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $index + 1 }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            @php
                                                $productCode = null;
                                                if($order->quotation) {
                                                    foreach($order->quotation->items as $quotationItem) {
                                                        if($quotationItem->product_id == $item->product_id) {
                                                            if($quotationItem->product && ($quotationItem->product->code || $quotationItem->product->sku)) {
                                                                $productCode = $quotationItem->product->code ?? $quotationItem->product->sku;
                                                                break;
                                                            }
                                                        }
                                                    }
                                                }
                                                
                                                if(!$productCode && $item->product) {
                                                    $productCode = $item->product->code ?? $item->product->sku ?? '-';
                                                }
                                            @endphp
                                            {{ $productCode ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->quantity, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $item->unit->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->unit_price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->total, 2) }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- สรุปยอด -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/2 lg:w-1/3">
                            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">ยอดรวมก่อนภาษี</span>
                                <span>{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            @if($order->discount_amount > 0)
                            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">
                                    ส่วนลด
                                    @if($order->discount_type == 'percentage')
                                    ({{ $order->discount_amount }}%)
                                    @endif
                                </span>
                                <span>{{ number_format($order->discount_value, 2) }}</span>
                            </div>
                            @endif
                            @if($order->tax_rate > 0)
                            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">
                                    ภาษีมูลค่าเพิ่ม ({{ $order->tax_rate }}%)
                                </span>
                                <span>{{ number_format($order->tax_amount, 2) }}</span>
                            </div>
                            @endif
                            @if($order->shipping_cost > 0)
                            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">ค่าขนส่ง</span>
                                <span>{{ number_format($order->shipping_cost, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between py-3 font-bold">
                                <span>ยอดรวมทั้งสิ้น</span>
                                <span class="text-lg">{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if($order->notes)
            <!-- หมายเหตุ -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-2">หมายเหตุ</h3>
                    <p class="p-3 bg-gray-50 dark:bg-gray-700 rounded">{{ $order->notes }}</p>
                </div>
            </div>
            @endif

            <!-- ประวัติการดำเนินการ -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-3">ประวัติการดำเนินการ</h3>
                    <div class="overflow-hidden">
                        <div class="border-l-4 border-gray-200 dark:border-gray-700 ml-3 pl-8 relative">
                            <!-- สร้างใบสั่งขาย -->
                            <div class="mb-8 relative">
                                <span class="w-4 h-4 bg-blue-500 rounded-full absolute -left-10 top-0"></span>
                                <div class="text-sm">
                                    <p class="font-semibold">สร้างใบสั่งขาย</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->creator->name ?? 'ไม่ระบุ' }}</p>
                                </div>
                            </div>

                            <!-- ยืนยันใบสั่งขาย -->
                            @if($order->confirmed_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-blue-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">ยืนยันใบสั่งขาย</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->confirmed_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->confirmedBy->name ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- กำลังดำเนินการ -->
                            @if($order->processed_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-yellow-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">กำลังดำเนินการ</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->processed_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->processedBy->name ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- จัดส่งสินค้า -->
                            @if($order->shipped_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-purple-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">จัดส่งสินค้า</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->shippedBy->name ?? 'ไม่ระบุ' }}</p>
                                        @if($order->tracking_number)
                                            <p class="text-gray-600 dark:text-gray-400">เลขพัสดุ: {{ $order->tracking_number }}</p>
                                        @endif
                                        @if($order->shipping_notes)
                                            <p class="text-gray-600 dark:text-gray-400">หมายเหตุการจัดส่ง: {{ $order->shipping_notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- ส่งมอบสินค้า -->
                            @if($order->delivered_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-green-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">ส่งมอบเรียบร้อย</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->deliveredBy->name ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- ยกเลิกใบสั่งขาย -->
                            @if($order->cancelled_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-red-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">ยกเลิกใบสั่งขาย</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->cancelledBy->name ?? 'ไม่ระบุ' }}</p>
                                        @if($order->cancellation_reason)
                                            <p class="text-gray-600 dark:text-gray-400">สาเหตุการยกเลิก: {{ $order->cancellation_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- เพิ่มส่วนแสดงใบส่งสินค้าที่เกี่ยวข้อง -->
            @if($order->deliveryOrders->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">ใบส่งสินค้าที่เกี่ยวข้อง</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">เลขที่ใบส่งสินค้า</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">วันที่</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">สถานะ</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">เลขพัสดุ</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ดำเนินการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($order->deliveryOrders as $deliveryOrder)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $deliveryOrder->delivery_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $deliveryOrder->delivery_date ? $deliveryOrder->delivery_date->format('d/m/Y') : '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($deliveryOrder->delivery_status === 'pending')
                                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">รอดำเนินการ</span>
                                        @elseif($deliveryOrder->delivery_status === 'processing')
                                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200">กำลังดำเนินการ</span>
                                        @elseif($deliveryOrder->delivery_status === 'shipped')
                                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200">จัดส่งแล้ว</span>
                                        @elseif($deliveryOrder->delivery_status === 'delivered')
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-200">ส่งมอบแล้ว</span>
                                        @elseif($deliveryOrder->delivery_status === 'cancelled')
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-200">ยกเลิก</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ $deliveryOrder->delivery_status }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $deliveryOrder->tracking_number ?: '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('delivery-orders.show', $deliveryOrder) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">รายละเอียด</a>
                                        <a href="{{ route('delivery-orders.print', $deliveryOrder) }}" target="_blank" class="ml-3 text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-200">พิมพ์</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- ประวัติสถานะของใบสั่งขาย -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-3">ประวัติสถานะของใบสั่งขาย</h3>
                    <div class="overflow-hidden">
                        <div class="border-l-4 border-gray-200 dark:border-gray-700 ml-3 pl-8 relative">
                            <!-- สถานะร่าง -->
                            <div class="mb-8 relative">
                                <span class="w-4 h-4 bg-gray-300 rounded-full absolute -left-10 top-0"></span>
                                <div class="text-sm">
                                    <p class="font-semibold">สถานะใบสั่งขาย</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->creator->name ?? 'ไม่ระบุ' }}</p>
                                </div>
                            </div>

                            <!-- ยืนยันใบสั่งขาย -->
                            @if($order->confirmed_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-blue-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">ยืนยันใบสั่งขาย</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->confirmed_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->confirmedBy->name ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- กำลังดำเนินการ -->
                            @if($order->processed_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-yellow-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">กำลังดำเนินการ</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->processed_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->processedBy->name ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- จัดส่งสินค้า -->
                            @if($order->shipped_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-purple-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">จัดส่งสินค้า</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->shippedBy->name ?? 'ไม่ระบุ' }}</p>
                                        @if($order->tracking_number)
                                            <p class="text-gray-600 dark:text-gray-400">เลขพัสดุ: {{ $order->tracking_number }}</p>
                                        @endif
                                        @if($order->shipping_notes)
                                            <p class="text-gray-600 dark:text-gray-400">หมายเหตุการจัดส่ง: {{ $order->shipping_notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- ส่งมอบสินค้า -->
                            @if($order->delivered_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-green-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">ส่งมอบเรียบร้อย</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->deliveredBy->name ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- ยกเลิกใบสั่งขาย -->
                            @if($order->cancelled_at)
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-red-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">ยกเลิกใบสั่งขาย</p>
                                        <p class="text-gray-600 dark:text-gray-400">{{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600 dark:text-gray-400">โดย: {{ $order->cancelledBy->name ?? 'ไม่ระบุ' }}</p>
                                        @if($order->cancellation_reason)
                                            <p class="text-gray-600 dark:text-gray-400">สาเหตุการยกเลิก: {{ $order->cancellation_reason }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal จัดส่งสินค้า -->
    <div id="shipModal" class="modal-hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">จัดส่งสินค้า - ขั้นตอนที่ 2</h3>
            
            <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-3 rounded dark:bg-blue-900 dark:border-blue-700 dark:text-blue-200">
                <div class="text-sm text-blue-800 dark:text-blue-200">
                    <strong>ใบส่งสินค้าถูกสร้างแล้ว</strong> - ตอนนี้คุณสามารถบันทึกข้อมูลการจัดส่งได้
                </div>
            </div>
            
            <form action="{{ route('orders.ship', $order) }}" method="POST" id="shipForm">
                @csrf
                
                <div class="mb-4">
                    <label for="tracking_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เลขพัสดุ</label>
                    <input type="text" id="tracking_number" name="tracking_number" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                
                <div class="mb-4">
                    <label for="shipping_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">หมายเหตุการจัดส่ง</label>
                    <textarea id="shipping_notes" name="shipping_notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                </div>
                
                <!-- เพิ่ม checkbox สำหรับเปิดหน้าจัดส่งสินค้าอัตโนมัติ -->
                <!-- <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" id="redirect_to_delivery" name="redirect_to_delivery" value="1" checked class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">เปิดหน้าสร้างใบส่งสินค้าอัตโนมัติ</span>
                    </label>
                </div> -->
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeShipModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded-md text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500">
                        ยกเลิก
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 rounded-md text-white hover:bg-purple-700">
                        บันทึกการจัดส่ง
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal ยกเลิกใบสั่งขาย -->
    <div id="cancelModal" class="modal-hidden">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4 text-gray-900 dark:text-gray-100">ยกเลิกใบสั่งขาย</h3>
            
            <form action="{{ route('orders.cancel', $order) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สาเหตุการยกเลิก <span class="text-red-600">*</span></label>
                    <textarea id="cancellation_reason" name="cancellation_reason" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required></textarea>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 rounded-md text-gray-800 dark:text-gray-200 hover:bg-gray-400 dark:hover:bg-gray-500">
                        ยกเลิก
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 rounded-md text-white hover:bg-red-700">
                        ยืนยันการยกเลิก
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- เพิ่มหรือแก้ไข CSS เพื่อให้แน่ใจว่า modal แสดงผลได้อย่างถูกต้อง -->
    <style>
        /* ปรับแต่งการแสดงผล Modal */
        #shipModal, #cancelModal {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        #shipModal.modal-hidden, #cancelModal.modal-hidden {
            display: none;
        }
        
        @media (prefers-color-scheme: dark) {
            .bg-white {
                background-color: #1f2937;
                color: #f3f4f6;
            }
        }
    </style>

    <!-- เพิ่ม JavaScript สำหรับปุ่มดูตัวอย่างและพิมพ์ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ฟังก์ชันแสดง Modal จัดส่งสินค้า
            window.showShipModal = function() {
                document.getElementById('shipModal').classList.remove('modal-hidden');
            };
            
            // ฟังก์ชันซ่อน Modal จัดส่งสินค้า
            window.closeShipModal = function() {
                document.getElementById('shipModal').classList.add('modal-hidden');
            };
            
            // ฟังก์ชันแสดง Modal ยกเลิกใบสั่งขาย
            window.showCancelModal = function() {
                document.getElementById('cancelModal').classList.remove('modal-hidden');
            };
            
            // ฟังก์ชันซ่อน Modal ยกเลิกใบสั่งขาย
            window.closeCancelModal = function() {
                document.getElementById('cancelModal').classList.add('modal-hidden');
            };
            
            // ฟังก์ชันยืนยันการลบใบสั่งขาย
            window.confirmDelete = function() {
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบใบสั่งขายนี้?')) {
                    document.getElementById('delete-form').submit();
                }
            };
            
            // เพิ่มการจัดการกับฟอร์มการจัดส่งสินค้า
            const shipForm = document.getElementById('shipForm');
            if (shipForm) {
                shipForm.addEventListener('submit', function(e) {
                    // ใส่ input hidden เพื่อบอก controller
                    const noRedirectInput = document.createElement('input');
                    noRedirectInput.type = 'hidden';
                    noRedirectInput.name = 'no_redirect';
                    noRedirectInput.value = '1';
                    shipForm.appendChild(noRedirectInput);
                });
            }
            
            // ซ่อน modal เมื่อกดปุ่ม Escape หรือคลิกพื้นหลัง
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    document.getElementById('shipModal')?.classList.add('modal-hidden');
                    document.getElementById('cancelModal')?.classList.add('modal-hidden');
                }
            });
            
            document.getElementById('shipModal')?.addEventListener('click', function(event) {
                if (event.target === this) {
                    this.classList.add('modal-hidden');
                }
            });
            
            document.getElementById('cancelModal')?.addEventListener('click', function(event) {
                if (event.target === this) {
                    this.classList.add('modal-hidden');
                }
            });
        });
        
        // Function to handle print window opening
        function openPrintWindow(event) {
            // ถ้ากดที่ icon ให้ไปหา element a ที่เป็น parent
            var target = event.target.closest('a');
            if (!target) return true;
            
            // เปิดในแท็บใหม่ปกติด้วย _blank
            // ไม่ต้องป้องกัน popup blocker แล้วเพราะเป็นแท็บปกติ ไม่ใช่หน้าต่างป๊อปอัพ
            return true; // ให้ทำงานปกติตาม target="_blank"
        }
    </script>
</x-app-layout>
