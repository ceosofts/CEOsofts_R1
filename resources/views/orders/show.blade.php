<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                รายละเอียดใบสั่งขาย: {{ $order->order_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    กลับไปรายการ
                </a>
                
                @if(!in_array($order->status, ['shipped', 'delivered', 'cancelled']))
                    <a href="{{ route('orders.edit', $order) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-yellow-600">
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
                                <span class="px-3 py-1 rounded-full text-sm font-semibold bg-{{ $order->statusColor }}-100 text-{{ $order->statusColor }}-800">
                                    {{ $order->statusText }}
                                </span>
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
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
                                <button type="button" onclick="showShipModal()" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-purple-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                                    </svg>
                                    จัดส่งสินค้า
                                </button>
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

                    <!-- ข้อมูลพื้นฐาน -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-medium mb-3 border-b pb-1">ข้อมูลใบสั่งขาย</h3>
                            <table class="w-full">
                                <tr>
                                    <td class="py-1 text-gray-600 w-1/3">เลขที่ใบสั่งขาย:</td>
                                    <td class="py-1 font-medium">{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">เลขที่ใบสั่งซื้อจากลูกค้า:</td>
                                    <td class="py-1">{{ $order->customer_po_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">วันที่สั่งซื้อ:</td>
                                    <td class="py-1">{{ $order->order_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">กำหนดส่งมอบ:</td>
                                    <td class="py-1">{{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</td>
                                </tr>
                                @if($order->quotation)
                                <tr>
                                    <td class="py-1 text-gray-600">อ้างอิงจากใบเสนอราคา:</td>
                                    <td class="py-1">
                                        <a href="{{ route('quotations.show', $order->quotation_id) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $order->quotation->quotation_number }}
                                        </a>
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="py-1 text-gray-600">เงื่อนไขการชำระเงิน:</td>
                                    <td class="py-1">{{ $order->payment_terms ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">หมายเหตุ:</td>
                                    <td class="py-1">{{ $order->notes ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium mb-3 border-b pb-1">ข้อมูลลูกค้า</h3>
                            <table class="w-full">
                                <tr>
                                    <td class="py-1 text-gray-600 w-1/3">ชื่อลูกค้า:</td>
                                    <td class="py-1 font-medium">{{ $order->customer->name }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">อีเมล:</td>
                                    <td class="py-1">{{ $order->customer->email }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">เบอร์โทรศัพท์:</td>
                                    <td class="py-1">{{ $order->customer->phone }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">ที่อยู่จัดส่ง:</td>
                                    <td class="py-1">{{ $order->shipping_address ?? $order->customer->address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">วิธีการจัดส่ง:</td>
                                    <td class="py-1">{{ $order->shipping_method ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">ค่าขนส่ง:</td>
                                    <td class="py-1">{{ $order->shipping_cost ? number_format($order->shipping_cost, 2) : '0.00' }}</td>
                                </tr>
                                @if($order->status == 'shipped' || $order->status == 'delivered')
                                <tr>
                                    <td class="py-1 text-gray-600">เลขพัสดุ:</td>
                                    <td class="py-1">{{ $order->tracking_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-600">หมายเหตุการจัดส่ง:</td>
                                    <td class="py-1">{{ $order->shipping_notes ?? '-' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- เพิ่มฟิลด์ใหม่เข้าไปในหน้ารายละเอียดใบสั่งขาย -->

                    <div class="mb-4">
                        <h2 class="text-xl font-semibold">ข้อมูลใบสั่งขาย</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            <div>
                                <p><span class="font-medium">เลขที่ใบสั่งขาย:</span> {{ $order->order_number }}</p>
                                <p><span class="font-medium">เลขที่ PO ลูกค้า:</span> {{ $order->customer_po_number ?? '-' }}</p>
                                <p><span class="font-medium">ลูกค้า:</span> {{ $order->customer->name }}</p>
                                <p><span class="font-medium">วันที่สั่งซื้อ:</span> {{ $order->order_date->format('d/m/Y') }}</p>
                                <p><span class="font-medium">วันที่จัดส่ง:</span> {{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</p>
                            </div>
                            <div>
                                <p><span class="font-medium">สถานะ:</span> <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></p>
                                <p><span class="font-medium">เงื่อนไขการชำระเงิน:</span> {{ $order->payment_terms ?? '-' }}</p>
                                <p><span class="font-medium">การจัดส่ง:</span> {{ $order->shipping_method ?? '-' }}</p>
                                <p><span class="font-medium">ที่อยู่จัดส่ง:</span> {{ $order->shipping_address ?? $order->customer->address }}</p>
                                <p><span class="font-medium">ค่าจัดส่ง:</span> {{ number_format($order->shipping_cost, 2) }} บาท</p>
                                
                                @if($order->status == 'shipped' || $order->status == 'delivered')
                                    <p><span class="font-medium">เลขติดตามพัสดุ:</span> {{ $order->tracking_number ?? '-' }}</p>
                                    <p><span class="font-medium">หมายเหตุการจัดส่ง:</span> {{ $order->shipping_notes ?? '-' }}</p>
                                @endif
                                
                                @if($order->status == 'cancelled')
                                    <p><span class="font-medium">เหตุผลที่ยกเลิก:</span> {{ $order->cancellation_reason ?? '-' }}</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <p><span class="font-medium">หมายเหตุ:</span> {{ $order->notes ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- รายการสินค้า -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-3 border-b pb-1">รายการสินค้า</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="py-2 px-4 border-b text-left">รหัสสินค้า</th>
                                        <th class="py-2 px-4 border-b text-left">รายการ</th>
                                        <th class="py-2 px-4 border-b text-center">จำนวน</th>
                                        <th class="py-2 px-4 border-b text-right">หน่วย</th>
                                        <th class="py-2 px-4 border-b text-right">ราคาต่อหน่วย</th>
                                        <th class="py-2 px-4 border-b text-right">จำนวนเงิน</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b">{{ $item->product->sku ?? '-' }}</td>
                                            <td class="py-2 px-4 border-b">{{ $item->description }}</td>
                                            <td class="py-2 px-4 border-b text-center">{{ number_format($item->quantity) }}</td>
                                            <td class="py-2 px-4 border-b text-right">{{ $item->unit->name ?? '-' }}</td>
                                            <td class="py-2 px-4 border-b text-right">{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="py-2 px-4 border-b text-right">{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="5" class="py-2 px-4 text-right font-medium">รวมเป็นเงิน:</td>
                                        <td class="py-2 px-4 text-right">{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    @if($order->discount_amount > 0)
                                        <tr>
                                            <td colspan="5" class="py-2 px-4 text-right font-medium">
                                                ส่วนลด{{ $order->discount_type == 'percentage' ? ' '. $order->discount_amount . '%' : '' }}:
                                            </td>
                                            <td class="py-2 px-4 text-right">{{ number_format($order->discount_value, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if($order->tax_rate > 0)
                                        <tr>
                                            <td colspan="5" class="py-2 px-4 text-right font-medium">
                                                ภาษีมูลค่าเพิ่ม {{ $order->tax_rate }}%:
                                            </td>
                                            <td class="py-2 px-4 text-right">{{ number_format($order->tax_amount, 2) }}</td>
                                        </tr>
                                    @endif
                                    @if($order->shipping_cost > 0)
                                        <tr>
                                            <td colspan="5" class="py-2 px-4 text-right font-medium">ค่าขนส่ง:</td>
                                            <td class="py-2 px-4 text-right">{{ number_format($order->shipping_cost, 2) }}</td>
                                        </tr>
                                    @endif
                                    <tr class="bg-blue-50">
                                        <td colspan="5" class="py-2 px-4 text-right font-bold">จำนวนเงินรวมทั้งสิ้น:</td>
                                        <td class="py-2 px-4 text-right font-bold">{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- ประวัติการดำเนินการ -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium mb-3 border-b pb-1">ประวัติการดำเนินการ</h3>
                        <div class="overflow-hidden">
                            <div class="border-l-4 border-gray-200 ml-3 pl-8 relative">
                                <!-- สร้างใบสั่งขาย -->
                                <div class="mb-8 relative">
                                    <span class="w-4 h-4 bg-blue-500 rounded-full absolute -left-10 top-0"></span>
                                    <div class="text-sm">
                                        <p class="font-semibold">สร้างใบสั่งขาย</p>
                                        <p class="text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                        <p class="text-gray-600">โดย: {{ $order->creator->name ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                </div>

                                <!-- ยืนยันใบสั่งขาย -->
                                @if($order->confirmed_at)
                                    <div class="mb-8 relative">
                                        <span class="w-4 h-4 bg-blue-500 rounded-full absolute -left-10 top-0"></span>
                                        <div class="text-sm">
                                            <p class="font-semibold">ยืนยันใบสั่งขาย</p>
                                            <p class="text-gray-600">{{ $order->confirmed_at->format('d/m/Y H:i') }}</p>
                                            <p class="text-gray-600">โดย: {{ $order->confirmedBy->name ?? 'ไม่ระบุ' }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- กำลังดำเนินการ -->
                                @if($order->processed_at)
                                    <div class="mb-8 relative">
                                        <span class="w-4 h-4 bg-yellow-500 rounded-full absolute -left-10 top-0"></span>
                                        <div class="text-sm">
                                            <p class="font-semibold">กำลังดำเนินการ</p>
                                            <p class="text-gray-600">{{ $order->processed_at->format('d/m/Y H:i') }}</p>
                                            <p class="text-gray-600">โดย: {{ $order->processedBy->name ?? 'ไม่ระบุ' }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- จัดส่งสินค้า -->
                                @if($order->shipped_at)
                                    <div class="mb-8 relative">
                                        <span class="w-4 h-4 bg-purple-500 rounded-full absolute -left-10 top-0"></span>
                                        <div class="text-sm">
                                            <p class="font-semibold">จัดส่งสินค้า</p>
                                            <p class="text-gray-600">{{ $order->shipped_at->format('d/m/Y H:i') }}</p>
                                            <p class="text-gray-600">โดย: {{ $order->shippedBy->name ?? 'ไม่ระบุ' }}</p>
                                            @if($order->tracking_number)
                                                <p class="text-gray-600">เลขพัสดุ: {{ $order->tracking_number }}</p>
                                            @endif
                                            @if($order->shipping_notes)
                                                <p class="text-gray-600">หมายเหตุการจัดส่ง: {{ $order->shipping_notes }}</p>
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
                                            <p class="text-gray-600">{{ $order->delivered_at->format('d/m/Y H:i') }}</p>
                                            <p class="text-gray-600">โดย: {{ $order->deliveredBy->name ?? 'ไม่ระบุ' }}</p>
                                        </div>
                                    </div>
                                @endif

                                <!-- ยกเลิกใบสั่งขาย -->
                                @if($order->cancelled_at)
                                    <div class="mb-8 relative">
                                        <span class="w-4 h-4 bg-red-500 rounded-full absolute -left-10 top-0"></span>
                                        <div class="text-sm">
                                            <p class="font-semibold">ยกเลิกใบสั่งขาย</p>
                                            <p class="text-gray-600">{{ $order->cancelled_at->format('d/m/Y H:i') }}</p>
                                            <p class="text-gray-600">โดย: {{ $order->cancelledBy->name ?? 'ไม่ระบุ' }}</p>
                                            @if($order->cancellation_reason)
                                                <p class="text-gray-600">สาเหตุการยกเลิก: {{ $order->cancellation_reason }}</p>
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
    </div>

    <!-- Modal จัดส่งสินค้า -->
    <div id="shipModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">จัดส่งสินค้า</h3>
            
            <form action="{{ route('orders.ship', $order) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <x-input-label for="tracking_number" :value="__('เลขพัสดุ')" />
                    <x-text-input id="tracking_number" name="tracking_number" type="text" class="w-full" />
                </div>
                
                <div class="mb-4">
                    <x-input-label for="shipping_notes" :value="__('หมายเหตุการจัดส่ง')" />
                    <textarea id="shipping_notes" name="shipping_notes" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeShipModal()" class="px-4 py-2 bg-gray-300 rounded-md text-gray-800 hover:bg-gray-400">
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
    <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-bold mb-4">ยกเลิกใบสั่งขาย</h3>
            
            <form action="{{ route('orders.cancel', $order) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <x-input-label for="cancellation_reason" :value="__('สาเหตุการยกเลิก')" class="required" />
                    <textarea id="cancellation_reason" name="cancellation_reason" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeCancelModal()" class="px-4 py-2 bg-gray-300 rounded-md text-gray-800 hover:bg-gray-400">
                        ยกเลิก
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 rounded-md text-white hover:bg-red-700">
                        ยืนยันการยกเลิก
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal จัดส่งสินค้า
        function showShipModal() {
            document.getElementById('shipModal').classList.remove('hidden');
        }
        
        function closeShipModal() {
            document.getElementById('shipModal').classList.add('hidden');
        }
        
        // Modal ยกเลิกใบสั่งขาย
        function showCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }
        
        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
        }
        
        // ยืนยันการลบใบสั่งขาย
        function confirmDelete() {
            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบใบสั่งขายนี้?')) {
                document.getElementById('delete-form').submit();
            }
        }
    </script>
</x-app-layout>
