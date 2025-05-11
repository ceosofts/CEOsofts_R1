<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('แก้ไขใบสั่งขาย') }}: {{ $order->order_number }}
            </h2>
            <div>
                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปหน้ารายละเอียด') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
                    <p class="mt-2">{{ session('error') }}</p>
                </div>
            @endif

            <!-- แสดงข้อผิดพลาดทั้งหมดที่ด้านบนของฟอร์ม -->
            @if (session('error_message'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
                    <p class="mt-2">{{ session('error_message') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
                    <ul class="mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(config('app.debug') && session('error_debug'))
                <div class="bg-gray-100 border-l-4 border-gray-500 text-gray-700 p-4 mb-4">
                    <p class="font-bold">Debug Info:</p>
                    <pre class="text-xs overflow-auto">{{ session('error_debug') }}</pre>
                </div>
            @endif

            <form action="{{ route('orders.update', $order) }}" method="POST" id="orderEditForm">
                @csrf
                @method('PUT')

                @if($order->quotation_id)
                    <input type="hidden" name="quotation_id" id="quotation_id" value="{{ $order->quotation_id }}">
                @endif

                <!-- ข้อมูลทั่วไปของใบสั่งขาย -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">ข้อมูลเอกสาร</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="order_number" class="block text-sm font-medium text-gray-700 mb-1">เลขที่ใบสั่งขาย <span class="text-red-600">*</span></label>
                                <input type="text" name="order_number" id="order_number" value="{{ old('order_number', $order->order_number) }}" required 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @error('order_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_po_number" class="block text-sm font-medium text-gray-700 mb-1">เลขที่ใบสั่งซื้อจากลูกค้า</label>
                                <input type="text" name="customer_po_number" id="customer_po_number" value="{{ old('customer_po_number', $order->customer_po_number) }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @error('customer_po_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">ลูกค้า <span class="text-red-600">*</span></label>
                                <select id="customer_id" name="customer_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required>
                                    <option value="">-- เลือกลูกค้า --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" @if(old('customer_id', $order->customer_id) == $customer->id) selected @endif>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="order_date" class="block text-sm font-medium text-gray-700 mb-1">วันที่สั่งซื้อ <span class="text-red-600">*</span></label>
                                <input type="date" name="order_date" id="order_date" value="{{ old('order_date', $order->order_date->format('Y-m-d')) }}" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @error('order_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-1">กำหนดส่งมอบ</label>
                                <input type="date" name="delivery_date" id="delivery_date" value="{{ old('delivery_date', $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                @error('delivery_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="sales_person_id" class="block text-sm font-medium text-gray-700 mb-1">พนักงานขาย</label>
                                <select id="sales_person_id" name="sales_person_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">-- เลือกพนักงานขาย --</option>
                                    @foreach(\App\Models\Employee::where('company_id', session('company_id'))->orderBy('first_name')->get() as $employee)
                                    <option value="{{ $employee->id }}" {{ old('sales_person_id', $order->sales_person_id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->employee_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('sales_person_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ</label>
                            <textarea id="notes" name="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('notes', $order->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลการจัดส่ง -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">ข้อมูลการจัดส่ง</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">ที่อยู่จัดส่ง</label>
                                <textarea id="shipping_address" name="shipping_address" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                                @error('shipping_address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <label for="shipping_method" class="block text-sm font-medium text-gray-700 mb-1">วิธีการจัดส่ง</label>
                                    <input type="text" name="shipping_method" id="shipping_method" value="{{ old('shipping_method', $order->shipping_method) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    @error('shipping_method')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="shipping_cost" class="block text-sm font-medium text-gray-700 mb-1">ค่าขนส่ง</label>
                                    <input type="number" name="shipping_cost" id="shipping_cost" step="0.01" min="0" value="{{ old('shipping_cost', $order->shipping_cost) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    @error('shipping_cost')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- รายการสินค้า -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">รายการสินค้า</h3>
                            <button type="button" id="addProductBtn" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                เพิ่มรายการ
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr class="bg-gray-100 text-gray-700">
                                        <th class="py-2 px-4 border-b text-center" style="width: 50px;">ลำดับ</th>
                                        <th class="py-2 px-4 border-b text-left">รหัสสินค้า</th>
                                        <th class="py-2 px-4 border-b text-left">สินค้า</th>
                                        <th class="py-2 px-4 border-b text-center" style="width: 120px;">จำนวน</th>
                                        <th class="py-2 px-4 border-b text-center" style="width: 80px;">หน่วย</th>
                                        <th class="py-2 px-4 border-b text-right" style="width: 150px;">ราคาต่อหน่วย</th>
                                        <th class="py-2 px-4 border-b text-right" style="width: 150px;">จำนวนเงิน</th>
                                        <th class="py-2 px-4 border-b text-center" style="width: 80px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="productsList">
                                    @foreach($order->items as $index => $item)
                                        <tr class="product-row hover:bg-gray-50">
                                            <td class="py-2 px-4 border-b text-center">{{ $index + 1 }}</td>
                                            <td class="py-2 px-4 border-b">
                                                <span class="product-code">{{ $item->product->code ?? $item->product->sku ?? '-' }}</span>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <select name="products[{{ $index }}][id]" class="product-select w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                                    <option value="">เลือกสินค้า</option>
                                                    @foreach($products as $product)
                                                        <option value="{{ $product->id }}" 
                                                            data-price="{{ $product->price }}"
                                                            data-code="{{ $product->code ?? $product->sku ?? '-' }}"
                                                            data-unit-id="{{ $product->unit_id }}"
                                                            data-unit-name="{{ $product->unit ? $product->unit->name : '' }}"
                                                            @if($item->product_id == $product->id) selected @endif>
                                                            {{ $product->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="py-2 px-4 border-b text-center">
                                                <input type="number" name="products[{{ $index }}][quantity]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="1" step="any" value="{{ $item->quantity }}" required>
                                            </td>
                                            <td class="py-2 px-4 border-b text-center">
                                                <span class="product-unit">{{ $item->unit->name ?? '-' }}</span>
                                                <input type="hidden" name="products[{{ $index }}][unit_id]" class="unit-id" value="{{ $item->unit_id }}">
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <input type="number" name="products[{{ $index }}][unit_price]" class="unit-price w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" step="0.01" value="{{ $item->unit_price }}" required>
                                            </td>
                                            <td class="py-2 px-4 border-b">
                                                <input type="text" class="subtotal w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="{{ number_format($item->quantity * $item->unit_price, 2) }}" readonly>
                                            </td>
                                            <td class="py-2 px-4 border-b text-center">
                                                <button type="button" class="remove-product text-red-600 hover:text-red-900">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50">
                                        <td colspan="6" class="py-2 px-4 text-right font-medium text-gray-700">รวมเป็นเงิน:</td>
                                        <td class="py-2 px-4 text-right">
                                            <input type="text" id="subtotalDisplay" class="w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="{{ number_format($order->subtotal, 2) }}" readonly>
                                            <input type="hidden" name="subtotal" id="subtotal" value="{{ $order->subtotal }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="py-2 px-4 text-right font-medium text-gray-700">
                                            <div class="flex justify-end items-center">
                                                <span class="mr-2">ส่วนลด:</span>
                                                <select name="discount_type" id="discount_type" class="mr-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" style="width:100px;">
                                                    <option value="fixed" @if(old('discount_type', $order->discount_type) == 'fixed') selected @endif>บาท</option>
                                                    <option value="percentage" @if(old('discount_type', $order->discount_type) == 'percentage') selected @endif>%</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 text-right">
                                            <input type="number" name="discount_amount" id="discount_amount" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" step="0.01" value="{{ old('discount_amount', $order->discount_type == 'percentage' ? $order->discount_amount : $order->discount_value) }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="py-2 px-4 text-right font-medium text-gray-700">
                                            <div class="flex justify-end items-center">
                                                <span>ภาษีมูลค่าเพิ่ม:</span>
                                                <input type="number" name="tax_rate" id="tax_rate" class="ml-2 w-24 text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" max="100" step="0.01" value="{{ old('tax_rate', $order->tax_rate) }}">
                                                <span class="ml-1">%</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 text-right">
                                            <input type="text" id="tax_amount_display" class="w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="{{ number_format($order->tax_amount, 2) }}" readonly>
                                            <input type="hidden" name="tax_amount" id="tax_amount" value="{{ $order->tax_amount }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-blue-50">
                                        <td colspan="6" class="py-2 px-4 text-right font-bold text-gray-700">จำนวนเงินรวมทั้งสิ้น:</td>
                                        <td class="py-2 px-4 text-right">
                                            <input type="text" id="total_amount_display" class="w-full text-right font-bold bg-blue-50 border-blue-200 rounded-md shadow-sm text-blue-800" value="{{ number_format($order->total_amount, 2) }}" readonly>
                                            <input type="hidden" name="total_amount" id="total_amount" value="{{ $order->total_amount }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- เงื่อนไขและหมายเหตุ -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">เงื่อนไขการชำระเงิน</h3>
                        
                        <div>
                            <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-1">เงื่อนไขการชำระเงิน</label>
                            <textarea id="payment_terms" name="payment_terms" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">{{ old('payment_terms', $order->payment_terms) }}</textarea>
                            @error('payment_terms')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- สถานะ -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold mb-4">สถานะใบสั่งขาย</h3>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">สถานะ <span class="text-red-600">*</span></label>
                            <select id="status" name="status" class="w-full md:w-1/4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="draft" @if(old('status', $order->status) == 'draft') selected @endif>ร่าง</option>
                                <option value="confirmed" @if(old('status', $order->status) == 'confirmed') selected @endif>ยืนยันแล้ว</option>
                                <option value="processing" @if(old('status', $order->status) == 'processing') selected @endif>กำลังดำเนินการ</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <div class="flex justify-end">
                    <button type="button" onclick="window.history.back()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md mr-4 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        ยกเลิก
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
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
            </form>
        </div>
    </div>

    <!-- Template สำหรับแถวสินค้า -->
    <template id="product-row-template">
        <tr class="product-row hover:bg-gray-50">
            <td class="py-2 px-4 border-b text-center">ROW_NUMBER</td>
            <td class="py-2 px-4 border-b">
                <span class="product-code">-</span>
            </td>
            <td class="py-2 px-4 border-b">
                <select name="products[INDEX][id]" class="product-select w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">เลือกสินค้า</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" 
                            data-price="{{ $product->price }}"
                            data-code="{{ $product->code ?? $product->sku ?? '-' }}"
                            data-unit-id="{{ $product->unit_id }}"
                            data-unit-name="{{ $product->unit ? $product->unit->name : '' }}">
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="py-2 px-4 border-b">
                <input type="number" name="products[INDEX][quantity]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="1" step="any" value="1" required>
            </td>
            <td class="py-2 px-4 border-b text-center">
                <span class="product-unit">-</span>
                <input type="hidden" name="products[INDEX][unit_id]" class="unit-id" value="">
            </td>
            <td class="py-2 px-4 border-b">
                <input type="number" name="products[INDEX][unit_price]" class="unit-price w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" step="0.01" value="0.00" required>
            </td>
            <td class="py-2 px-4 border-b">
                <input type="text" class="subtotal w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="0.00" readonly>
            </td>
            <td class="py-2 px-4 border-b text-center">
                <button type="button" class="remove-product text-red-600 hover:text-red-900">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productsList = document.getElementById('productsList');
            const addProductBtn = document.getElementById('addProductBtn');
            const productRowTemplate = document.getElementById('product-row-template');
            let productIndex = {{ count($order->items) }};
            
            // preload units list for JS (unit_id => unit_name)
            window.unitsList = @json(\App\Models\Unit::pluck('name', 'id'));

            // เพิ่มรายการสินค้า
            addProductBtn.addEventListener('click', function() {
                addProductRow();
                updateNoProductsMessage();
                calculateTotals();
            });

            // ฟังก์ชันเพิ่มแถวสินค้า
            function addProductRow() {
                const template = productRowTemplate.innerHTML;
                const nextRowNumber = document.querySelectorAll('.product-row').length + 1;
                // แทนที่ ROW_NUMBER ด้วย nextRowNumber
                const newRow = template.replace(/INDEX/g, productIndex++).replace('ROW_NUMBER', nextRowNumber);
                productsList.insertAdjacentHTML('beforeend', newRow);
                
                // กำหนด event listeners สำหรับแถวใหม่
                initializeRowEvents(productsList.lastElementChild);
            }
            
            // Event delegation สำหรับปุ่มลบสินค้า
            productsList.addEventListener('click', function(e) {
                if (e.target.closest('.remove-product')) {
                    const row = e.target.closest('.product-row');
                    
                    // ถ้ามีมากกว่า 1 รายการสินค้า จึงลบได้
                    if (productsList.querySelectorAll('.product-row').length > 1) {
                        row.remove();
                        reindexProductRows();
                        calculateTotals();
                    } else {
                        alert('ต้องมีอย่างน้อย 1 รายการสินค้า');
                    }
                    
                    updateNoProductsMessage();
                }
            });
            
            // Initialize row events สำหรับแถวที่มีอยู่แล้ว
            function initializeExistingRows() {
                document.querySelectorAll('.product-row').forEach(row => {
                    initializeRowEvents(row);
                });
            }
            
            // จัดลำดับรายการสินค้าใหม่
            function reindexProductRows() {
                const rows = productsList.querySelectorAll('.product-row');
                rows.forEach((row, index) => {
                    // อัพเดทคอลัมน์ลำดับ (เริ่มที่ 1)
                    row.querySelector('td:first-child').textContent = index + 1;
                    
                    row.querySelectorAll('[name^="products["]').forEach(input => {
                        const name = input.getAttribute('name');
                        const newName = name.replace(/products\[\d+\]/, `products[${index}]`);
                        input.setAttribute('name', newName);
                    });
                });
            }
            
            // ฟังก์ชันกำหนด event listeners ของแต่ละแถวสินค้า
            function initializeRowEvents(row) {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity');
                const unitPriceInput = row.querySelector('.unit-price');
                
                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price') || 0;
                    const code = selectedOption.getAttribute('data-code') || '-';
                    const unitId = selectedOption.getAttribute('data-unit-id') || '';
                    const unitName = selectedOption.getAttribute('data-unit-name') || '-';
                    
                    unitPriceInput.value = parseFloat(price).toFixed(2);
                    row.querySelector('.product-code').textContent = code;
                    row.querySelector('.unit-id').value = unitId;
                    row.querySelector('.product-unit').textContent = unitName;
                    
                    updateRowTotal(row);
                    calculateTotals();
                });
                
                quantityInput.addEventListener('input', function() {
                    updateRowTotal(row);
                    calculateTotals();
                });
                
                unitPriceInput.addEventListener('input', function() {
                    updateRowTotal(row);
                    calculateTotals();
                });
            }
            
            // อัพเดทยอดรวมของแต่ละรายการ
            function updateRowTotal(row) {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const subtotal = quantity * unitPrice;
                row.querySelector('.subtotal').value = subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
            
            // แสดง/ซ่อนข้อความ "ไม่มีรายการสินค้า"
            function updateNoProductsMessage() {
                // Do nothing if the element doesn't exist
            }
            
            // คำนวณยอดรวมทั้งหมด
            function calculateTotals() {
                // คำนวณยอดรวมก่อนหักส่วนลด
                let subtotal = 0;
                document.querySelectorAll('.product-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                    subtotal += quantity * unitPrice;
                });
                
                // แสดงยอดรวมก่อนหักส่วนลด
                document.getElementById('subtotalDisplay').value = subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                document.getElementById('subtotal').value = subtotal.toFixed(2);
                
                // คำนวณส่วนลด
                const discountType = document.getElementById('discount_type').value;
                const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
                let discountValue = 0;
                
                if (discountType === 'percentage') {
                    discountValue = subtotal * (discountAmount / 100);
                } else {
                    discountValue = discountAmount;
                }
                
                // คำนวณราคาสุทธิหลังหักส่วนลด
                const netTotal = subtotal - discountValue;
                
                // คำนวณภาษี
                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = netTotal * (taxRate / 100);
                document.getElementById('tax_amount_display').value = taxAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                document.getElementById('tax_amount').value = taxAmount.toFixed(2);
                
                // คำนวณยอดรวมทั้งหมด
                const shippingCost = parseFloat(document.getElementById('shipping_cost').value) || 0;
                const totalAmount = netTotal + taxAmount + shippingCost;
                
                document.getElementById('total_amount_display').value = totalAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                document.getElementById('total_amount').value = totalAmount.toFixed(2);
            }

            // Event listeners สำหรับการคำนวณยอดรวม
            document.getElementById('discount_type').addEventListener('change', calculateTotals);
            document.getElementById('discount_amount').addEventListener('input', calculateTotals);
            document.getElementById('tax_rate').addEventListener('input', calculateTotals);
            document.getElementById('shipping_cost').addEventListener('input', calculateTotals);
            
            // JavaScript สำหรับป้องกันการส่งฟอร์มซ้ำ
            document.getElementById('orderEditForm').addEventListener('submit', function(e) {
                // ตรวจสอบฟิลด์สำคัญเพิ่มเติม
                const customerId = document.getElementById('customer_id').value;
                if (!customerId) {
                    e.preventDefault(); // ป้องกันการส่งฟอร์ม
                    alert('กรุณาเลือกลูกค้า');
                    return false;
                }
                
                // ตรวจสอบว่ามีรายการสินค้าหรือไม่
                if (document.querySelectorAll('.product-row').length === 0) {
                    e.preventDefault();
                    alert('กรุณาเพิ่มอย่างน้อย 1 รายการสินค้า');
                    return false;
                }
                
                // แสดง Loading State
                document.querySelector('#submitBtn .normal-state').classList.add('hidden');
                document.querySelector('#submitBtn .loading-state').classList.remove('hidden');
                
                // ปิดปุ่ม Submit
                document.getElementById('submitBtn').disabled = true;
            });
            
            // คำนวณยอดรวมครั้งแรก
            calculateTotals();
            
            // กำหนด event listeners สำหรับแถวที่มีอยู่แล้ว
            initializeExistingRows();
        });
    </script>
</x-app-layout>