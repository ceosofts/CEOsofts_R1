<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('สร้างใบส่งสินค้า') }}
            </h2>
            <a href="{{ route('delivery-orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('กลับ') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">กรุณาตรวจสอบข้อมูล:</p>
                            <ul class="mt-1 ml-4 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- ส่วนของการเลือกใบสั่งขาย -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0 text-blue-400">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3 w-full">
                                <p class="text-sm text-blue-800 mb-2">
                                    เลือกใบสั่งขายที่อนุมัติแล้วเพื่อสร้างใบส่งสินค้า
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    <select id="order_id_selector" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- เลือกใบสั่งขายที่อนุมัติแล้ว --</option>
                                        @foreach($orders as $order)
                                            @if(in_array($order->status, ['confirmed', 'processing']))
                                                <option value="{{ $order->id }}" 
                                                    {{ $selectedOrder && $selectedOrder->id == $order->id ? 'selected' : '' }}
                                                    data-status="{{ $order->status }}" 
                                                    data-delivery-date="{{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '' }}">
                                                    {{ $order->order_number }} - {{ $order->customer->name ?? 'ไม่ระบุชื่อลูกค้า' }} 
                                                    ({{ $order->statusText }} - {{ number_format($order->total_amount, 2) }} บาท)
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" id="loadOrderBtn" class="px-4 py-2 bg-blue-600 rounded-md text-white hover:bg-blue-700 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <span>โหลดข้อมูล</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('delivery-orders.store') }}" method="POST" id="deliveryOrderForm">
                        @csrf

                        <input type="hidden" name="order_id" id="order_id" value="{{ $selectedOrder ? $selectedOrder->id : '' }}">
                        <input type="hidden" name="customer_id" id="customer_id" value="{{ $selectedOrder ? $selectedOrder->customer_id : '' }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- ข้อมูลลูกค้าและเลขที่เอกสาร -->
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="customer_name" :value="__('ลูกค้า')" class="required" />
                                    <x-text-input id="customer_name" type="text" class="w-full bg-gray-100" value="{{ $selectedOrder ? $selectedOrder->customer->name : '' }}" readonly />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="delivery_number" :value="__('เลขที่ใบส่งสินค้า')" class="required" />
                                    <x-text-input id="delivery_number" name="delivery_number" type="text" class="w-full" :value="old('delivery_number')" required />
                                    @error('delivery_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="order_number" :value="__('เลขที่ใบสั่งขาย')" class="required" />
                                    <x-text-input id="order_number" type="text" class="w-full bg-gray-100" value="{{ $selectedOrder ? $selectedOrder->order_number : '' }}" readonly />
                                </div>
                                
                                <!-- เพิ่มข้อมูลสถานะ -->
                                <div class="mb-4">
                                    <x-input-label for="order_status" :value="__('สถานะใบสั่งขาย')" />
                                    <div class="flex items-center mt-1">
                                        <span id="order_status_badge" class="px-2 py-1 rounded text-xs font-medium {{ $selectedOrder ? 'bg-'.$selectedOrder->statusColor.'-100 text-'.$selectedOrder->statusColor.'-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $selectedOrder ? $selectedOrder->statusText : '-' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- วันที่เอกสาร -->
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="delivery_date" :value="__('วันที่ส่งสินค้า')" class="required" />
                                    <x-text-input id="delivery_date" name="delivery_date" type="date" class="w-full" :value="old('delivery_date', $selectedOrder && $selectedOrder->delivery_date ? $selectedOrder->delivery_date->format('Y-m-d') : date('Y-m-d'))" required />
                                    @error('delivery_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="expected_delivery_date" :value="__('กำหนดส่งมอบจากใบสั่งขาย')" />
                                    <x-text-input id="expected_delivery_date" type="date" class="w-full bg-gray-100" value="{{ $selectedOrder && $selectedOrder->delivery_date ? $selectedOrder->delivery_date->format('Y-m-d') : '' }}" readonly />
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="delivery_status" :value="__('สถานะการจัดส่ง')" class="required" />
                                    <select id="delivery_status" name="delivery_status" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="pending" {{ old('delivery_status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                        <option value="processing" {{ old('delivery_status') == 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                        <option value="shipped" {{ old('delivery_status') == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                                        <option value="delivered" {{ old('delivery_status') == 'delivered' ? 'selected' : '' }}>ส่งมอบแล้ว</option>
                                        <option value="partial_delivered" {{ old('delivery_status') == 'partial_delivered' ? 'selected' : '' }}>ส่งมอบบางส่วน</option>
                                    </select>
                                    @error('delivery_status')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ข้อมูลการจัดส่ง -->
                        <div class="border rounded-lg p-4 mb-6">
                            <h3 class="font-medium text-gray-700 mb-2">ข้อมูลการจัดส่ง</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <div class="mb-4">
                                        <x-input-label for="shipping_address" :value="__('ที่อยู่จัดส่ง')" class="required" />
                                        <textarea id="shipping_address" name="shipping_address" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('shipping_address', $selectedOrder ? $selectedOrder->shipping_address : '') }}</textarea>
                                        @error('shipping_address')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <x-input-label for="shipping_contact" :value="__('ผู้ติดต่อ')" class="required" />
                                        <x-text-input id="shipping_contact" name="shipping_contact" type="text" class="w-full" :value="old('shipping_contact')" required />
                                        @error('shipping_contact')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div>
                                    <div class="mb-4">
                                        <x-input-label for="shipping_method" :value="__('วิธีการจัดส่ง')" />
                                        <x-text-input id="shipping_method" name="shipping_method" type="text" class="w-full" :value="old('shipping_method', $selectedOrder ? $selectedOrder->shipping_method : '')" />
                                        @error('shipping_method')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <x-input-label for="tracking_number" :value="__('เลขพัสดุ')" />
                                        <x-text-input id="tracking_number" name="tracking_number" type="text" class="w-full" :value="old('tracking_number')" />
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
                                <h3 class="font-medium text-gray-700">รายการสินค้า</h3>
                                <div id="loading-indicator" class="hidden text-blue-600">
                                    <svg class="animate-spin h-5 w-5 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    กำลังโหลดข้อมูลสินค้า...
                                </div>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white" id="productsTable">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="py-2 px-4 border-b text-left">รหัสสินค้า</th>
                                            <th class="py-2 px-4 border-b text-left">รายการ</th>
                                            <th class="py-2 px-4 border-b text-center" style="width: 120px;">จำนวนตามใบสั่งซื้อ</th>
                                            <th class="py-2 px-4 border-b text-center" style="width: 120px;">จำนวนที่ส่ง</th>
                                            <th class="py-2 px-4 border-b text-right" style="width: 100px;">หน่วย</th>
                                            <th class="py-2 px-4 border-b text-center" style="width: 150px;">สถานะ</th>
                                            <th class="py-2 px-4 border-b text-center">หมายเหตุ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productsList">
                                        @if($selectedOrder && $selectedOrder->items->count() > 0)
                                            @foreach($selectedOrder->items as $index => $item)
                                                <tr class="product-row">
                                                    <input type="hidden" name="product_id[{{ $index }}]" value="{{ $item->product_id }}">
                                                    <input type="hidden" name="order_item_id[{{ $index }}]" value="{{ $item->id }}">
                                                    
                                                    <td class="py-2 px-4 border-b">
                                                        {{ $item->product->sku ?? '-' }}
                                                    </td>
                                                    <td class="py-2 px-4 border-b">
                                                        <input type="text" name="description[{{ $index }}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ $item->description }}" required>
                                                    </td>
                                                    <td class="py-2 px-4 border-b text-center bg-gray-50">
                                                        {{ $item->quantity }} {{ $item->unit_name ?? '' }}
                                                    </td>
                                                    <td class="py-2 px-4 border-b text-center">
                                                        <input type="number" name="quantity[{{ $index }}]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0.01" step="any" value="{{ $item->quantity }}" required>
                                                    </td>
                                                    <td class="py-2 px-4 border-b text-right">
                                                        <input type="text" name="unit[{{ $index }}]" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="{{ $item->unit_name ?? '' }}" required>
                                                    </td>
                                                    <td class="py-2 px-4 border-b text-center">
                                                        <select name="status[{{ $index }}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                            <option value="pending">รอดำเนินการ</option>
                                                            <option value="delivered">ส่งมอบแล้ว</option>
                                                            <option value="partial">ส่งมอบบางส่วน</option>
                                                        </select>
                                                    </td>
                                                    <td class="py-2 px-4 border-b">
                                                        <input type="text" name="item_notes[{{ $index }}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr id="no-products-row">
                                                <td colspan="7" class="py-4 text-center text-gray-500">
                                                    กรุณาเลือกใบสั่งขายที่อนุมัติแล้วเพื่อดูรายการสินค้า
                                                </td>
                                            @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- หมายเหตุ -->
                        <div class="mb-6">
                            <x-input-label for="notes" :value="__('หมายเหตุ')" />
                            <textarea id="notes" name="notes" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ผู้อนุมัติ - แก้เป็นใช้ผู้ใช้งานปัจจุบัน -->
                        <div class="mb-6">
                            <x-input-label for="approved_by" :value="__('ผู้อนุมัติ')" />
                            <div class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                                <input type="hidden" name="approved_by" value="{{ auth()->id() }}">
                                {{ auth()->user()->name }}
                            </div>
                            <p class="text-xs text-gray-500 mt-1">ใช้ผู้ใช้งานปัจจุบันเป็นผู้อนุมัติโดยอัตโนมัติ</p>
                            @error('approved_by')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 text-right">
                            <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-300 rounded-md text-gray-800 mr-2">
                                ยกเลิก
                            </button>
                            <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 rounded-md text-white">
                                <span class="normal-state">บันทึกใบส่งสินค้า</span>
                                <span class="loading-state hidden">
                                    <svg class="animate-spin h-5 w-5 text-white inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    กำลังบันทึก...
                                </span>
                            </button>
                        </div>
                    </form>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const orderIdSelector = document.getElementById('order_id_selector');
                            const loadOrderBtn = document.getElementById('loadOrderBtn');
                            const orderIdInput = document.getElementById('order_id');
                            const customerIdInput = document.getElementById('customer_id');
                            const customerNameInput = document.getElementById('customer_name');
                            const orderNumberInput = document.getElementById('order_number');
                            const shippingAddressInput = document.getElementById('shipping_address');
                            const shippingMethodInput = document.getElementById('shipping_method');
                            const expectedDeliveryDateInput = document.getElementById('expected_delivery_date');
                            const orderStatusBadge = document.getElementById('order_status_badge');
                            const productsList = document.getElementById('productsList');
                            const loadingIndicator = document.getElementById('loading-indicator');
                            
                            // เมื่อเลือกใบสั่งขายจาก dropdown
                            orderIdSelector.addEventListener('change', function() {
                                // อัพเดทวันที่ส่งมอบตามที่ระบุในใบสั่งขาย (ถ้ามี)
                                const selectedOption = this.options[this.selectedIndex];
                                if (selectedOption.value) {
                                    const deliveryDate = selectedOption.getAttribute('data-delivery-date');
                                    if (deliveryDate) {
                                        expectedDeliveryDateInput.value = deliveryDate;
                                        
                                        // ถ้ายังไม่ได้กำหนดวันส่งสินค้า ให้ใช้วันที่จากใบสั่งขาย
                                        if (!document.getElementById('delivery_date').value) {
                                            document.getElementById('delivery_date').value = deliveryDate;
                                        }
                                    }
                                    
                                    // อัพเดทแสดงสถานะใบสั่งขาย
                                    const status = selectedOption.getAttribute('data-status');
                                    let statusText = '';
                                    let statusColor = '';
                                    
                                    switch (status) {
                                        case 'confirmed':
                                            statusText = 'ยืนยันแล้ว';
                                            statusColor = 'green';
                                            break;
                                        case 'processing':
                                            statusText = 'กำลังดำเนินการ';
                                            statusColor = 'blue';
                                            break;
                                        default:
                                            statusText = status;
                                            statusColor = 'gray';
                                    }
                                    
                                    orderStatusBadge.textContent = statusText;
                                    orderStatusBadge.className = `px-2 py-1 rounded text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800`;
                                }
                            });
                            
                            // โหลดข้อมูลใบสั่งขาย
                            loadOrderBtn.addEventListener('click', function() {
                                const orderId = orderIdSelector.value;
                                
                                if (!orderId) {
                                    alert('กรุณาเลือกใบสั่งขาย');
                                    return;
                                }
                                
                                console.log('กำลังโหลดข้อมูล Order ID:', orderId);
                                
                                // แสดงสถานะกำลังโหลด
                                this.disabled = true;
                                loadingIndicator.classList.remove('hidden');
                                const originalButtonText = this.innerHTML;
                                this.innerHTML = '<svg class="animate-spin h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> กำลังโหลด...';
                                
                                // ใช้ URL ใหม่ที่แก้ไขแล้ว
                                const apiUrl = `/api/order-products/${orderId}`;
                                
                                // ดึงข้อมูลจาก API
                                fetch(apiUrl, {
                                    method: 'GET',
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    credentials: 'same-origin'
                                })
                                .then(response => {
                                    console.log('API Response Status:', response.status);
                                    
                                    if (!response.ok) {
                                        return response.json()
                                            .then(errorData => {
                                                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
                                            })
                                            .catch(e => {
                                                throw new Error(`HTTP error! status: ${response.status}`);
                                            });
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('API Response Data:', data);
                                    
                                    // เซ็ตค่าต่างๆ จากข้อมูลที่ได้รับ
                                    orderIdInput.value = data.order.id;
                                    customerIdInput.value = data.order.customer_id;
                                    customerNameInput.value = data.customer.name;
                                    orderNumberInput.value = data.order.order_number;
                                    
                                    // กำหนดข้อมูลการจัดส่ง
                                    shippingAddressInput.value = data.order.shipping_address || (data.customer.address || '');
                                    shippingMethodInput.value = data.order.shipping_method || '';
                                    
                                    // กำหนดวันที่ส่ง
                                    if (data.order.delivery_date) {
                                        expectedDeliveryDateInput.value = data.order.delivery_date;
                                        if (!document.getElementById('delivery_date').value) {
                                            document.getElementById('delivery_date').value = data.order.delivery_date;
                                        }
                                    }
                                    
                                    // อัพเดทแสดงสถานะใบสั่งขาย
                                    updateOrderStatusBadge(data.order.status);
                                    
                                    // ล้างรายการสินค้าเก่า
                                    while (productsList.firstChild) {
                                        productsList.removeChild(productsList.firstChild);
                                    }
                                    
                                    // เพิ่มรายการสินค้าใหม่
                                    if (data.items && data.items.length > 0) {
                                        data.items.forEach((item, index) => {
                                            const newRow = document.createElement('tr');
                                            newRow.classList.add('product-row');
                                            
                                            newRow.innerHTML = `
                                                <input type="hidden" name="product_id[${index}]" value="${item.product_id}">
                                                <input type="hidden" name="order_item_id[${index}]" value="${item.id}">
                                                
                                                <td class="py-2 px-4 border-b">
                                                    ${item.product && item.product.sku ? item.product.sku : '-'}
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <input type="text" name="description[${index}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="${item.description || ''}" required>
                                                </td>
                                                <td class="py-2 px-4 border-b text-center bg-gray-50">
                                                    ${item.quantity} ${item.unit_name || ''}
                                                </td>
                                                <td class="py-2 px-4 border-b text-center">
                                                    <input type="number" name="quantity[${index}]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0.01" step="any" value="${item.quantity}" required>
                                                </td>
                                                <td class="py-2 px-4 border-b text-right">
                                                    <input type="text" name="unit[${index}]" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="${item.unit_name || ''}" required>
                                                </td>
                                                <td class="py-2 px-4 border-b text-center">
                                                    <select name="status[${index}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                        <option value="pending">รอดำเนินการ</option>
                                                        <option value="delivered">ส่งมอบแล้ว</option>
                                                        <option value="partial">ส่งมอบบางส่วน</option>
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <input type="text" name="item_notes[${index}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                </td>
                                            `;
                                            
                                            productsList.appendChild(newRow);
                                        });
                                    } else {
                                        showNoProductsMessage();
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message);
                                })
                                .finally(() => {
                                    // คืนสถานะปุ่มเมื่อเสร็จสิ้น
                                    this.disabled = false;
                                    this.innerHTML = originalButtonText;
                                    loadingIndicator.classList.add('hidden');
                                });
                            });
                            
                            // ฟังก์ชันเพิ่มรายการสินค้า
                            function addProductItems(items) {
                                items.forEach((item, index) => {
                                    const newRow = document.createElement('tr');
                                    newRow.classList.add('product-row');
                                    
                                    newRow.innerHTML = `
                                        <input type="hidden" name="product_id[${index}]" value="${item.product_id}">
                                        <input type="hidden" name="order_item_id[${index}]" value="${item.id}">
                                        
                                        <td class="py-2 px-4 border-b">
                                            ${item.product && item.product.sku ? item.product.sku : '-'}
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <input type="text" name="description[${index}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="${item.description || ''}" required>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center bg-gray-50">
                                            ${item.quantity} ${item.unit_name || ''}
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <input type="number" name="quantity[${index}]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0.01" step="any" value="${item.quantity}" required>
                                        </td>
                                        <td class="py-2 px-4 border-b text-right">
                                            <input type="text" name="unit[${index}]" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" value="${item.unit_name || ''}" required>
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <select name="status[${index}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                                <option value="pending">รอดำเนินการ</option>
                                                <option value="delivered">ส่งมอบแล้ว</option>
                                                <option value="partial">ส่งมอบบางส่วน</option>
                                            </select>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            <input type="text" name="item_notes[${index}]" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        </td>
                                    `;
                                    
                                    productsList.appendChild(newRow);
                                });
                            }
                            
                            // ฟังก์ชันแสดงข้อความไม่มีสินค้า
                            function showNoProductsMessage() {
                                const emptyRow = document.createElement('tr');
                                emptyRow.setAttribute('id', 'no-products-row');
                                emptyRow.innerHTML = `
                                    <td colspan="7" class="py-4 text-center text-gray-500">
                                        ไม่พบรายการสินค้าในใบสั่งขายนี้
                                    </td>
                                `;
                                productsList.appendChild(emptyRow);
                            }
                            
                            // ฟังก์ชันอัพเดทสถานะใบสั่งขาย
                            function updateOrderStatusBadge(status) {
                                let statusText = '';
                                let statusColor = '';
                                
                                switch (status) {
                                    case 'confirmed':
                                        statusText = 'ยืนยันแล้ว';
                                        statusColor = 'green';
                                        break;
                                    case 'processing':
                                        statusText = 'กำลังดำเนินการ';
                                        statusColor = 'blue';
                                        break;
                                    default:
                                        statusText = status;
                                        statusColor = 'gray';
                                }
                                
                                orderStatusBadge.textContent = statusText;
                                orderStatusBadge.className = `px-2 py-1 rounded text-xs font-medium bg-${statusColor}-100 text-${statusColor}-800`;
                            }
                            
                            // ป้องกันการส่งฟอร์มซ้ำและตรวจสอบข้อมูล
                            document.getElementById('deliveryOrderForm').addEventListener('submit', function(e) {
                                const orderId = document.getElementById('order_id').value;
                                if (!orderId) {
                                    e.preventDefault();
                                    alert('กรุณาเลือกใบสั่งขายก่อนบันทึก');
                                    return false;
                                }
                                
                                // ตรวจสอบว่ามีรายการสินค้าหรือไม่
                                const productRows = document.querySelectorAll('.product-row');
                                if (productRows.length === 0) {
                                    e.preventDefault();
                                    alert('กรุณาโหลดข้อมูลสินค้าก่อนบันทึก');
                                    return false;
                                }
                                
                                // แสดง Loading State
                                document.querySelector('#submitBtn .normal-state').classList.add('hidden');
                                document.querySelector('#submitBtn .loading-state').classList.remove('hidden');
                                
                                // ปิดปุ่ม Submit
                                document.getElementById('submitBtn').disabled = true;
                            });
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
