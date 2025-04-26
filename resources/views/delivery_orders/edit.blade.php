<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-3xl text-blue-800 dark:text-blue-300">
                {{ __('แก้ไขใบส่งสินค้า') }}: {{ $deliveryOrder->delivery_number }}
            </h2>
            <a href="{{ route('delivery-orders.show', $deliveryOrder) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('กลับ') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
                    <p class="font-bold">เกิดข้อผิดพลาด</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 dark:bg-red-900 dark:border-red-700 dark:text-red-200" role="alert">
                    <p class="font-bold">กรุณาแก้ไขข้อผิดพลาดต่อไปนี้:</p>
                    <ul class="list-disc pl-5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- เพิ่มแสดงข้อมูลของใบสั่งขายที่เกี่ยวข้อง -->
            <div class="mb-6 bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-200">
                <h3 class="text-blue-700 font-medium mb-2 dark:text-blue-300">ข้อมูลใบสั่งขาย</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">เลขที่ใบสั่งขาย</p>
                        <p class="font-medium">
                            <a href="{{ route('orders.show', $deliveryOrder->order) }}" class="text-blue-600 hover:underline dark:text-blue-400 dark:hover:text-blue-300">
                                {{ $deliveryOrder->order->order_number }}
                            </a>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">สถานะใบสั่งขาย</p>
                        <p>
                            @php
                                // กำหนด status classes เพื่อแก้ปัญหา CSS conflict
                                $statusClasses = [
                                    'confirmed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                                    'processing' => 'bg-blue-500 text-white dark:bg-blue-700 dark:text-white',
                                    'delivered' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
                                    'shipped' => 'bg-indigo-500 text-white dark:bg-indigo-700 dark:text-white',
                                    'partial_delivered' => 'bg-amber-200 text-amber-900 dark:bg-amber-900 dark:text-amber-200',
                                    'cancelled' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200',
                                    'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
                                    'draft' => 'bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                ];
                                
                                // ใช้ status ปัจจุบันหรือค่าเริ่มต้น
                                $currentStatus = $deliveryOrder->order->status ?? 'pending';
                                $statusClass = $statusClasses[$currentStatus] ?? $statusClasses['pending'];
                                
                                $statusMap = [
                                    'confirmed' => 'ยืนยันแล้ว',
                                    'processing' => 'กำลังดำเนินการ',
                                    'delivered' => 'ส่งมอบแล้ว',
                                    'shipped' => 'จัดส่งแล้ว',
                                    'partial_delivered' => 'ส่งมอบบางส่วน',
                                    'cancelled' => 'ยกเลิก',
                                    'pending' => 'รอดำเนินการ',
                                    'draft' => 'ร่าง'
                                ];
                                $statusText = $statusMap[$currentStatus] ?? ucfirst($currentStatus);
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">กำหนดส่งมอบ</p>
                        <p class="font-medium dark:text-gray-200">{{ $deliveryOrder->order->delivery_date ? $deliveryOrder->order->delivery_date->format('d/m/Y') : '-' }}</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('delivery-orders.update', $deliveryOrder) }}" method="POST" id="deliveryOrderEditForm">
                @csrf
                @method('PUT')

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- ข้อมูลลูกค้าและเลขที่เอกสาร -->
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="order_id" :value="__('ใบสั่งขาย')" class="required dark:text-gray-300" />
                                    <select id="order_id" name="order_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" disabled>
                                        <option value="{{ $deliveryOrder->order_id }}">{{ $deliveryOrder->order->order_number }} - {{ $deliveryOrder->order->customer->name }}</option>
                                    </select>
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="delivery_number" :value="__('เลขที่ใบส่งสินค้า')" class="required dark:text-gray-300" />
                                    <x-text-input id="delivery_number" name="delivery_number" type="text" class="w-full bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" :value="old('delivery_number', $deliveryOrder->delivery_number)" readonly />
                                    @error('delivery_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="customer_name" :value="__('ลูกค้า')" class="required dark:text-gray-300" />
                                    <x-text-input id="customer_name" type="text" class="w-full bg-gray-100 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400" value="{{ $deliveryOrder->customer->name }}" readonly />
                                </div>
                            </div>

                            <!-- วันที่เอกสารและสถานะ -->
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="delivery_date" :value="__('วันที่ส่งสินค้า')" class="required dark:text-gray-300" />
                                    <x-text-input id="delivery_date" name="delivery_date" type="date" class="w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white" :value="old('delivery_date', $deliveryOrder->delivery_date->format('Y-m-d'))" required />
                                    @error('delivery_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="delivery_status" :value="__('สถานะการจัดส่ง')" class="required dark:text-gray-300" />
                                    <select id="delivery_status" name="delivery_status" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                        <option value="pending" {{ old('delivery_status', $deliveryOrder->delivery_status) == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                        <option value="processing" {{ old('delivery_status', $deliveryOrder->delivery_status) == 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                        <option value="shipped" {{ old('delivery_status', $deliveryOrder->delivery_status) == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                                        <option value="delivered" {{ old('delivery_status', $deliveryOrder->delivery_status) == 'delivered' ? 'selected' : '' }}>ส่งมอบแล้ว</option>
                                        <option value="partial_delivered" {{ old('delivery_status', $deliveryOrder->delivery_status) == 'partial_delivered' ? 'selected' : '' }}>ส่งมอบบางส่วน</option>
                                        <option value="cancelled" {{ old('delivery_status', $deliveryOrder->delivery_status) == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                    </select>
                                    @error('delivery_status')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ข้อมูลการจัดส่ง -->
                        <div class="border rounded-lg p-4 mb-6 dark:border-gray-700">
                            <h3 class="font-medium text-gray-700 mb-2 dark:text-gray-300">ข้อมูลการจัดส่ง</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-4">
                                        <x-input-label for="shipping_address" :value="__('ชื่อผู้ติดต่อและที่อยู่จัดส่ง')" class="required dark:text-gray-300" />
                                        <textarea id="shipping_address" name="shipping_address" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>{{ old('shipping_address', $deliveryOrder->shipping_address) }}</textarea>
                                        @error('shipping_address','shipping_contact')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                </div>
                                
                                <div>
                                    <div class="mb-4">
                                        <x-input-label for="shipping_method" :value="__('วิธีการจัดส่ง')" class="dark:text-gray-300" />
                                        <x-text-input id="shipping_method" name="shipping_method" type="text" class="w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white" :value="old('shipping_method', $deliveryOrder->shipping_method)" />
                                        @error('shipping_method')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <x-input-label for="tracking_number" :value="__('เลขพัสดุ')" class="dark:text-gray-300" />
                                        <x-text-input id="tracking_number" name="tracking_number" type="text" class="w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white" :value="old('tracking_number', $deliveryOrder->tracking_number)" />
                                        @error('tracking_number')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- รายการสินค้า -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-medium text-gray-700 dark:text-gray-300">รายการสินค้า</h3>
                                <div class="flex items-center space-x-2">
                                    <button type="button" id="showOrderItemsBtn" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        ดูรายการในใบสั่งขาย
                                    </button>
                                    <button type="button" id="addProductBtn" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        เพิ่มสินค้า
                                    </button>
                                </div>
                            </div>
                            
                            <!-- รายการสินค้าในใบสั่งขาย (ซ่อนไว้) -->
                            <div id="orderItemsPanel" class="mb-4 bg-gray-50 p-3 rounded-lg border border-gray-200 hidden dark:bg-gray-700 dark:border-gray-600">
                                <h4 class="text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">รายการสินค้าในใบสั่งขาย</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white border dark:bg-gray-800 dark:border-gray-700">
                                        <thead class="bg-gray-100 dark:bg-gray-700">
                                            <tr>
                                                <th class="py-2 px-3 border-b text-left text-xs dark:text-gray-300 dark:border-gray-600">รหัสสินค้า</th>
                                                <th class="py-2 px-3 border-b text-left text-xs dark:text-gray-300 dark:border-gray-600">รายการ</th>
                                                <th class="py-2 px-3 border-b text-center text-xs dark:text-gray-300 dark:border-gray-600">จำนวน</th>
                                                <th class="py-2 px-3 border-b text-center text-xs dark:text-gray-300 dark:border-gray-600">หน่วย</th>
                                                <th class="py-2 px-3 border-b text-center text-xs dark:text-gray-300 dark:border-gray-600">เพิ่ม</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($deliveryOrder->order->items as $orderItem)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                    <td class="py-1 px-3 border-b text-xs dark:text-gray-300 dark:border-gray-600">{{ $orderItem->product->sku ?? '-' }}</td>
                                                    <td class="py-1 px-3 border-b text-xs dark:text-gray-300 dark:border-gray-600">{{ $orderItem->description }}</td>
                                                    <td class="py-1 px-3 border-b text-center text-xs dark:text-gray-300 dark:border-gray-600">{{ $orderItem->quantity }}</td>
                                                    <td class="py-1 px-3 border-b text-center text-xs dark:text-gray-300 dark:border-gray-600">{{ $orderItem->unit_name ?? '' }}</td>
                                                    <td class="py-1 px-3 border-b text-center dark:text-gray-300 dark:border-gray-600">
                                                        <button type="button" class="add-from-order text-blue-600 hover:text-blue-800 text-xs dark:text-blue-400 dark:hover:text-blue-300"
                                                            data-product-id="{{ $orderItem->product_id }}"
                                                            data-description="{{ $orderItem->description }}"
                                                            data-quantity="{{ $orderItem->quantity }}"
                                                            data-unit="{{ $orderItem->unit_name ?? '' }}">
                                                            เพิ่ม
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2 text-right">
                                    <button type="button" id="closeOrderItemsBtn" class="text-xs text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">ปิด</button>
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-800" id="productsTable">
                                    <thead class="bg-gray-100 dark:bg-gray-700">
                                        <tr>
                                            <th class="py-2 px-4 border-b text-center dark:text-gray-300 dark:border-gray-600">ลำดับ</th>
                                            <th class="py-2 px-4 border-b text-left dark:text-gray-300 dark:border-gray-600" style="width: 120px;">รหัสสินค้า</th>
                                            <th class="py-2 px-4 border-b text-left dark:text-gray-300 dark:border-gray-600" >รายการ</th>
                                            <th class="py-2 px-4 border-b text-center dark:text-gray-300 dark:border-gray-600" style="width: 120px;">จำนวนในใบสั่งขาย</th>
                                            <th class="py-2 px-4 border-b text-center dark:text-gray-300 dark:border-gray-600" style="width: 120px;">จำนวนที่ส่ง</th>
                                            <th class="py-2 px-4 border-b text-right dark:text-gray-300 dark:border-gray-600" style="width: 100px;">หน่วย</th>
                                            <th class="py-2 px-4 border-b text-center dark:text-gray-300 dark:border-gray-600" style="width: 150px;">สถานะ</th>
                                            <th class="py-2 px-4 border-b text-left dark:text-gray-300 dark:border-gray-600" style="width: 150px;">หมายเหตุ</th>
                                            <th class="py-2 px-4 border-b text-center dark:text-gray-300 dark:border-gray-600" style="width: 80px;">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsList">
                                        @foreach($deliveryOrder->deliveryOrderItems as $index => $item)
                                            @php
                                                // หาข้อมูลรายการสินค้าในใบสั่งขายที่ตรงกัน
                                                $matchingOrderItem = $deliveryOrder->order->items->first(function($orderItem) use ($item) {
                                                    return $orderItem->product_id == $item->product_id;
                                                });
                                                $orderItemQuantity = $matchingOrderItem ? $matchingOrderItem->quantity : 0;
                                            @endphp
                                            <tr class="product-row dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <input type="hidden" name="item_id[{{ $index }}]" value="{{ $item->id }}">
                                                
                                                <td class="py-2 px-4 border-b text-center dark:text-gray-300 dark:border-gray-600">{{ $index + 1 }}</td>
                                                <td class="py-2 px-4 border-b dark:text-gray-300 dark:border-gray-600">
                                                    {{ $item->product->code ?? $item->product->sku ?? '-' }}
                                                    <input type="hidden" name="product_id[{{ $index }}]" value="{{ $item->product_id }}">
                                                </td>
                                                <td class="py-2 px-4 border-b dark:border-gray-600">
                                                    <input type="text" name="description[{{ $index }}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="{{ $item->description }}" required>
                                                </td>
                                                <td class="py-2 px-4 border-b text-center bg-gray-50 dark:bg-gray-600 dark:text-gray-300 dark:border-gray-700">
                                                    {{ number_format($orderItemQuantity) }}
                                                </td>
                                                <td class="py-2 px-4 border-b text-center dark:border-gray-600">
                                                    <input type="number" name="quantity[{{ $index }}]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="0.01" step="any" value="{{ $item->quantity }}" required>
                                                </td>
                                                <td class="py-2 px-4 border-b text-right dark:border-gray-600">
                                                    <input type="text" name="unit[{{ $index }}]" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="{{ $item->unit }}" required>
                                                </td>
                                                <td class="py-2 px-4 border-b text-center dark:border-gray-600">
                                                    <select name="status[{{ $index }}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                                        <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                                        <option value="delivered" {{ $item->status == 'delivered' ? 'selected' : '' }}>ส่งมอบแล้ว</option>
                                                        <option value="partial" {{ $item->status == 'partial' ? 'selected' : '' }}>ส่งมอบบางส่วน</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b dark:border-gray-600">
                                                    <input type="text" name="item_notes[{{ $index }}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" value="{{ $item->notes }}">
                                                </td>
                                                <td class="py-2 px-4 border-b text-center dark:border-gray-600">
                                                    <button type="button" class="remove-product text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                    <input type="hidden" name="delete_items[]" class="delete-marker" disabled>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- หมายเหตุ -->
                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('หมายเหตุ')" class="dark:text-gray-300" />
                            <textarea id="notes" name="notes" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $deliveryOrder->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ผู้อนุมัติ - แก้เป็นใช้ผู้ใช้งานปัจจุบัน -->
                        <div class="mb-6">
                            <x-input-label for="approver_info" :value="__('ผู้อนุมัติ')" class="dark:text-gray-300" />
                            <div class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                {{ auth()->user()->name }}
                                @if($deliveryOrder->notes && strpos($deliveryOrder->notes, 'ผู้อนุมัติ:') !== false)
                                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">
                                        (ข้อมูลการอนุมัติเดิมถูกบันทึกไว้ในหมายเหตุ)
                                    </p>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1 dark:text-gray-400">ข้อมูลผู้อนุมัติจะถูกบันทึกในหมายเหตุ</p>
                        </div>

                        <div class="mt-6 text-right">
                            <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-300 rounded-md text-gray-800 mr-2 hover:bg-gray-400 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                                ยกเลิก
                            </button>
                            <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 rounded-md text-white hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-600">
                                <span class="normal-state">บันทึกการแก้ไข</span>
                                <span class="loading-state hidden">
                                    <svg class="animate-spin h-5 w-5 text-white inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    กำลังบันทึก...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsList = document.getElementById('productsList');
            const addProductBtn = document.getElementById('addProductBtn');
            const showOrderItemsBtn = document.getElementById('showOrderItemsBtn');
            const closeOrderItemsBtn = document.getElementById('closeOrderItemsBtn');
            const orderItemsPanel = document.getElementById('orderItemsPanel');
            
            // แสดง/ซ่อน รายการสินค้าในใบสั่งขาย
            showOrderItemsBtn.addEventListener('click', function() {
                orderItemsPanel.classList.remove('hidden');
            });
            
            closeOrderItemsBtn.addEventListener('click', function() {
                orderItemsPanel.classList.add('hidden');
            });
            
            // เพิ่มรายการสินค้าจากใบสั่งขาย
            document.querySelectorAll('.add-from-order').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    const description = this.getAttribute('data-description');
                    const quantity = this.getAttribute('data-quantity');
                    const unit = this.getAttribute('data-unit');
                    
                    // ตรวจสอบว่ามีสินค้านี้ในรายการแล้วหรือไม่
                    const existingItems = document.querySelectorAll('input[name^="product_id"]');
                    let productExists = false;
                    
                    existingItems.forEach(item => {
                        if (item.value == productId) {
                            productExists = true;
                            alert('สินค้านี้มีในรายการแล้ว กรุณาแก้ไขจำนวนในรายการที่มีอยู่แทน');
                        }
                    });
                    
                    if (!productExists) {
                        addNewProductRow(productId, description, quantity, unit);
                    }
                    
                    // ซ่อนรายการสินค้าในใบสั่งขาย
                    orderItemsPanel.classList.add('hidden');
                });
            });
            
            // เพิ่มรายการสินค้า
            addProductBtn.addEventListener('click', function() {
                // แสดงรายการสินค้าในใบสั่งขาย
                orderItemsPanel.classList.remove('hidden');
            });
            
            // ฟังก์ชันเพิ่มรายการสินค้าใหม่
            function addNewProductRow(productId, description, quantity, unit) {
                const newIndex = 'new_' + Math.floor(Math.random() * 10000); // สร้าง index ที่ไม่ซ้ำ
                const newRow = document.createElement('tr');
                newRow.classList.add('product-row', 'new-item');
                
                // ดึงข้อมูล SKU จาก element ที่มี data-product-id ตรงกับ productId
                const productRow = document.querySelector(`[data-product-id="${productId}"]`).closest('tr');
                const sku = productRow.cells[0].textContent.trim(); // ดึง SKU จาก cell แรกของแถวที่มีข้อมูลสินค้า
                
                newRow.innerHTML = `
                    <td class="py-2 px-4 border-b text-center">${document.querySelectorAll('.product-row').length + 1}</td>
                    <td class="py-2 px-4 border-b">
                        <input type="hidden" name="new_product_id[${newIndex}]" value="${productId}">
                        ${sku}<!-- แสดง SKU ของสินค้า -->
                    </td>
                    <td class="py-2 px-4 border-b">
                        <input type="text" name="new_description[${newIndex}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="${description}" required>
                    </td>
                    <td class="py-2 px-4 border-b text-center bg-gray-50">
                        ${quantity}
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        <input type="number" name="new_quantity[${newIndex}]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0.01" step="any" value="${quantity}" required>
                    </td>
                    <td class="py-2 px-4 border-b text-right">
                        <input type="text" name="new_unit[${newIndex}]" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="${unit}" required>
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        <select name="new_status[${newIndex}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="pending">รอดำเนินการ</option>
                            <option value="delivered">ส่งมอบแล้ว</option>
                            <option value="partial">ส่งมอบบางส่วน</option>
                        </select>
                    </td>
                    <td class="py-2 px-4 border-b">
                        <input type="text" name="new_item_notes[${newIndex}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </td>
                    <td class="py-2 px-4 border-b text-center">
                        <button type="button" class="remove-new-product text-red-600 hover:text-red-900">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                `;
                
                productsList.appendChild(newRow);
            }
            
            // Event delegation สำหรับปุ่มลบสินค้าเดิม
            productsList.addEventListener('click', function(e) {
                if (e.target.closest('.remove-product')) {
                    const row = e.target.closest('.product-row');
                    const deleteMarker = row.querySelector('.delete-marker');
                    
                    row.classList.add('bg-red-50');
                    deleteMarker.disabled = false;
                    deleteMarker.value = row.querySelector('input[name^="item_id"]').value;
                    
                    // ปิดรายการนี้
                    row.querySelectorAll('input, select').forEach(input => {
                        if (!input.classList.contains('delete-marker')) {
                            input.disabled = true;
                        }
                    });
                    
                    // เปลี่ยนปุ่มลบเป็นปุ่มคืนค่า
                    const removeButton = e.target.closest('.remove-product');
                    removeButton.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                        </svg>
                    `;
                    removeButton.classList.remove('remove-product');
                    removeButton.classList.add('restore-product', 'text-green-600', 'hover:text-green-900');
                }
                
                if (e.target.closest('.restore-product')) {
                    const row = e.target.closest('.product-row');
                    const deleteMarker = row.querySelector('.delete-marker');
                    
                    row.classList.remove('bg-red-50');
                    deleteMarker.disabled = true;
                    deleteMarker.value = '';
                    
                    // เปิดใช้งานรายการนี้
                    row.querySelectorAll('input, select').forEach(input => {
                        if (!input.classList.contains('delete-marker')) {
                            input.disabled = false;
                        }
                    });
                    
                    // เปลี่ยนปุ่มคืนค่าเป็นปุ่มลบ
                    const restoreButton = e.target.closest('.restore-product');
                    restoreButton.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    `;
                    restoreButton.classList.remove('restore-product', 'text-green-600', 'hover:text-green-900');
                    restoreButton.classList.add('remove-product', 'text-red-600', 'hover:text-red-900');
                }
                
                // ลบรายการใหม่
                if (e.target.closest('.remove-new-product')) {
                    const row = e.target.closest('.product-row');
                    row.remove();
                }
            });
            
            // ป้องกันการส่งฟอร์มซ้ำ
            document.getElementById('deliveryOrderEditForm').addEventListener('submit', function() {
                // แสดง Loading State
                document.querySelector('#submitBtn .normal-state').classList.add('hidden');
                document.querySelector('#submitBtn .loading-state').classList.remove('hidden');
                
                // ปิดปุ่ม Submit
                document.getElementById('submitBtn').disabled = true;
            });
            
            // เปรียบเทียบจำนวนสินค้า
            document.querySelectorAll('.quantity').forEach(input => {
                input.addEventListener('input', function() {
                    const row = this.closest('tr');
                    const orderQuantityCell = row.cells[2];
                    const orderQuantity = parseFloat(orderQuantityCell.textContent.replace(/[^0-9.-]+/g, '')) || 0;
                    const inputQuantity = parseFloat(this.value) || 0;
                    
                    // แสดงสีเมื่อจำนวนไม่ตรงกัน
                    if (inputQuantity > orderQuantity) {
                        this.classList.add('bg-yellow-50', 'border-yellow-300');
                        this.classList.remove('bg-green-50', 'border-green-300', 'bg-red-50', 'border-red-300');
                    } else if (inputQuantity < orderQuantity) {
                        this.classList.add('bg-red-50', 'border-red-300');
                        this.classList.remove('bg-green-50', 'border-green-300', 'bg-yellow-50', 'border-yellow-300');
                    } else {
                        this.classList.add('bg-green-50', 'border-green-300');
                        this.classList.remove('bg-yellow-50', 'border-yellow-300', 'bg-red-50', 'border-red-300');
                    }
                });
                
                // ทำการเปรียบเทียบครั้งแรก
                input.dispatchEvent(new Event('input'));
            });
        });
    </script>
</x-app-layout>
