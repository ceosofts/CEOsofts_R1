<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('สร้างใบสั่งขาย') }}{{ $quotation ? ' จากใบเสนอราคา #'.$quotation->quotation_number : '' }}
            </h2>
            <a href="{{ route('orders.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700">
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

                    <!-- แสดงข้อผิดพลาดทั้งหมดที่ด้านบนของฟอร์ม -->
                    @if (session('error_message'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">{{ session('error_message') }}</p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-bold">กรุณาตรวจสอบข้อมูล:</p>
                            <ul class="mt-1 ml-4 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- ส่วนของการเลือกใบเสนอราคา -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0 text-blue-400">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3 w-full">
                                <p class="text-sm text-blue-800 mb-2">
                                    เลือกใบเสนอราคาที่อนุมัติแล้วเพื่อสร้างใบสั่งขาย
                                </p>
                                <div class="flex space-x-2">
                                    <select id="approved_quotation_selector" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- เลือกใบเสนอราคาที่อนุมัติแล้ว --</option>
                                        @foreach($approvedQuotations as $apQuote)
                                        <option value="{{ $apQuote->id }}" data-customer="{{ $apQuote->customer_id }}">
                                            {{ $apQuote->quotation_number }} - {{ $apQuote->customer->name ?? 'ไม่ระบุชื่อลูกค้า' }} ({{ number_format($apQuote->total_amount, 2) }} บาท)
                                        </option>
                                        @endforeach
                                    </select>
                                    <button type="button" id="loadQuotationBtn" class="px-4 py-2 bg-blue-600 rounded-md text-white hover:bg-blue-700">
                                        <span>โหลดข้อมูล</span>
                                    </button>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p>หรือ <a href="{{ route('quotations.index') }}?status=approved" class="text-blue-600 hover:underline">ดูรายการใบเสนอราคาทั้งหมดที่อนุมัติแล้ว</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="{{ route('orders.store') }}" method="POST" id="orderCreateForm">
                        @csrf

                        @if($quotation)
                            <input type="hidden" name="quotation_id" id="quotation_id" value="{{ $quotation->id }}">
                        @else
                            <input type="hidden" name="quotation_id" id="quotation_id" value="">
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- ข้อมูลลูกค้าและเลขที่เอกสาร -->
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="customer_id" :value="__('ลูกค้า')" class="required" />
                                    <select id="customer_id" name="customer_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                        <option value="">เลือกลูกค้า</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" @if(old('customer_id', $quotation ? $quotation->customer_id ?? null : null) == $customer->id) selected @endif>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($quotation)
                                        <input type="hidden" name="customer_id" value="{{ $quotation->customer_id }}">
                                    @endif
                                    @error('customer_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="order_number" :value="__('เลขที่ใบสั่งขาย')" class="required" />
                                    <x-text-input id="order_number" name="order_number" type="text" class="w-full" :value="old('order_number', $orderNumber)" required />
                                    @error('order_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="customer_po_number" :value="__('เลขที่ใบสั่งซื้อจากลูกค้า')" />
                                    <x-text-input id="customer_po_number" name="customer_po_number" type="text" class="w-full" :value="old('customer_po_number')" />
                                    @error('customer_po_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- วันที่เอกสารและกำหนดส่ง -->
                            <div>
                                <div class="mb-4">
                                    <x-input-label for="order_date" :value="__('วันที่สั่งซื้อ')" class="required" />
                                    <x-text-input id="order_date" name="order_date" type="date" class="w-full" :value="old('order_date', date('Y-m-d'))" required />
                                    @error('order_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <x-input-label for="delivery_date" :value="__('กำหนดส่งมอบ')" />
                                    <x-text-input id="delivery_date" name="delivery_date" type="date" class="w-full" :value="old('delivery_date')" />
                                    @error('delivery_date')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <!-- ปรับช่องพนักงานขายให้ดึงค่าจากใบเสนอราคา -->
                                <div class="mb-4">
                                    <x-input-label for="sales_person_id" :value="__('พนักงานขาย')" />
                                    <select id="sales_person_id" name="sales_person_id" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="">-- เลือกพนักงานขาย --</option>
                                        @foreach(\App\Models\Employee::where('company_id', session('company_id'))->orderBy('first_name')->get() as $employee)
                                        <option value="{{ $employee->id }}" {{ old('sales_person_id', $quotation ? $quotation->sales_person_id : null) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->employee_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('sales_person_id')
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
                                    <x-input-label for="shipping_address" :value="__('ที่อยู่จัดส่ง')" />
                                    <textarea id="shipping_address" name="shipping_address" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('shipping_address', $quotation->shipping_address ?? ($quotation ? $quotation->customer->address : '')) }}</textarea>
                                    @error('shipping_address')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <div class="mb-4">
                                        <x-input-label for="shipping_method" :value="__('วิธีการจัดส่ง')" />
                                        <x-text-input id="shipping_method" name="shipping_method" type="text" class="w-full" :value="old('shipping_method', $quotation->shipping_method ?? '')" />
                                        @error('shipping_method')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <div>
                                        <x-input-label for="shipping_cost" :value="__('ค่าขนส่ง')" />
                                        <x-text-input id="shipping_cost" name="shipping_cost" type="number" step="0.01" min="0" class="w-full" :value="old('shipping_cost', $quotation->shipping_cost ?? 0)" />
                                        @error('shipping_cost')
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
                                <button type="button" id="addProductBtn" class="hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                    เพิ่มสินค้า
                                </button>
                            </div>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white" id="productsTable">
                                    <thead class="bg-gray-100">
                                        <tr>
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
                                        @if($quotation && $quotation->items->count() > 0)
                                            @foreach($quotation->items as $index => $item)
                                                <tr class="product-row">
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
                                                    <td class="py-2 px-4 border-b">
                                                        <input type="number" name="products[{{ $index }}][quantity]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="1" step="any" value="{{ $item->quantity }}" required>
                                                    </td>
                                                    <td class="py-2 px-4 border-b text-center">
                                                        <span class="product-unit">
                                                            {{ $item->unit ? $item->unit->name : ($item->product && $item->product->unit ? $item->product->unit->name : '-') }}
                                                        </span>
                                                        <input type="hidden" name="products[{{ $index }}][unit_id]" class="unit-id" value="{{ $item->unit_id ?? ($item->product ? $item->product->unit_id : '') }}">
                                                    </td>
                                                    <td class="py-2 px-4 border-b">
                                                        <input type="number" name="products[{{ $index }}][unit_price]" class="unit-price w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" step="0.01" value="{{ $item->unit_price }}" required>
                                                    </td>
                                                    <td class="py-2 px-4 border-b">
                                                        <input type="text" class="subtotal w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="{{ number_format($item->quantity * $item->unit_price, 2) }}" readonly>
                                                    </td>
                                                    <td class="py-2 px-4 border-b text-center">
                                                        <button type="button" class="remove-product text-red-600 hover:text-red-900">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr class="product-row">
                                                <td class="py-2 px-4 border-b text-center">1</td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="product-code">-</span>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <select name="products[0][id]" class="product-select w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
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
                                                    <input type="number" name="products[0][quantity]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="1" step="any" value="1" required>
                                                </td>
                                                <td class="py-2 px-4 border-b text-center">
                                                    <span class="product-unit">-</span>
                                                    <input type="hidden" name="products[0][unit_id]" class="unit-id" value="">
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <input type="number" name="products[0][unit_price]" class="unit-price w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" step="0.01" value="0.00" required>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <input type="text" class="subtotal w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="0.00" readonly>
                                                </td>
                                                <td class="py-2 px-4 border-b text-center">
                                                    <button type="button" class="remove-product text-red-600 hover:text-red-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50">
                                            <td colspan="6" class="py-2 px-4 text-right font-medium">รวมเป็นเงิน:</td>
                                            <td class="py-2 px-4 text-right">
                                                <input type="text" id="subtotalDisplay" class="w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="{{ $quotation ? number_format($quotation->subtotal, 2) : '0.00' }}" readonly>
                                                <input type="hidden" name="subtotal" id="subtotal" value="{{ $quotation ? $quotation->subtotal : '0' }}">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="py-2 px-4 text-right font-medium">
                                                <div class="flex justify-end items-center">
                                                    <span class="mr-2">ส่วนลด:</span>
                                                    <select name="discount_type" id="discount_type" class="mr-2 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" style="width:100px;">
                                                        <option value="fixed" @if(old('discount_type', $quotation ? $quotation->discount_type : 'fixed') == 'fixed') selected @endif>บาท</option>
                                                        <option value="percentage" @if(old('discount_type', $quotation ? $quotation->discount_type : '') == 'percentage') selected @endif>%</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="py-2 px-4 text-right">
                                                <input type="number" name="discount_amount" id="discount_amount" class="w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" step="0.01" value="{{ old('discount_amount', $quotation ? $quotation->discount_amount : '0') }}">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" class="py-2 px-4 text-right font-medium">
                                                <div class="flex justify-end items-center">
                                                    <span>ภาษีมูลค่าเพิ่ม:</span>
                                                    <input type="number" name="tax_rate" id="tax_rate" class="ml-2 w-24 text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" max="100" step="0.01" value="{{ old('tax_rate', $quotation ? $quotation->tax_rate : '7') }}">
                                                    <span class="ml-1">%</span>
                                                </div>
                                            </td>
                                            <td class="py-2 px-4 text-right">
                                                <input type="text" id="tax_amount_display" class="w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="{{ $quotation ? number_format($quotation->tax_amount, 2) : '0.00' }}" readonly>
                                                <input type="hidden" name="tax_amount" id="tax_amount" value="{{ $quotation ? $quotation->tax_amount : '0' }}">
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-blue-50">
                                            <td colspan="6" class="py-2 px-4 text-right font-bold">จำนวนเงินรวมทั้งสิ้น:</td>
                                            <td class="py-2 px-4 text-right">
                                                <input type="text" id="total_amount_display" class="w-full text-right font-bold bg-blue-50 border-blue-200 rounded-md shadow-sm text-blue-800" value="{{ $quotation ? number_format($quotation->total_amount, 2) : '0.00' }}" readonly>
                                                <input type="hidden" name="total_amount" id="total_amount" value="{{ $quotation ? $quotation->total_amount : '0' }}">
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <!-- เงื่อนไขและหมายเหตุ -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <x-input-label for="payment_terms" :value="__('เงื่อนไขการชำระเงิน')" />
                                <textarea id="payment_terms" name="payment_terms" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('payment_terms', $quotation ? $quotation->payment_terms : '') }}</textarea>
                                @error('payment_terms')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <x-input-label for="notes" :value="__('หมายเหตุ')" />
                                <textarea id="notes" name="notes" rows="3" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('notes', $quotation ? $quotation->notes : '') }}</textarea>
                                @error('notes')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- สถานะ -->
                        <div class="mb-6">
                            <x-input-label for="status" :value="__('สถานะ')" class="required" />
                            <select id="status" name="status" class="w-full md:w-1/4 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="draft" @if(old('status') == 'draft') selected @endif>ร่าง</option>
                                <option value="confirmed" @if(old('status') == 'confirmed') selected @endif>ยืนยันแล้ว</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-6 text-right">
                            <button type="button" onclick="history.back()" class="px-4 py-2 bg-gray-300 rounded-md text-gray-800 mr-2">
                                ยกเลิก
                            </button>
                            <button type="submit" id="submitBtn" class="px-4 py-2 bg-blue-600 rounded-md text-white">
                                <span class="normal-state">บันทึกใบสั่งขาย</span>
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
                            const productsList = document.getElementById('productsList');
                            const addProductBtn = document.getElementById('addProductBtn');
                            
                            // preload units list for JS (unit_id => unit_name)
                            window.unitsList = @json(\App\Models\Unit::pluck('name', 'id'));

                            // เพิ่มรายการสินค้า
                            addProductBtn.addEventListener('click', function() {
                                const rowCount = productsList.querySelectorAll('tr.product-row').length;
                                const newRow = document.createElement('tr');
                                newRow.classList.add('product-row');
                                
                                newRow.innerHTML = `
                                    <td class="py-2 px-4 border-b text-center">${rowCount + 1}</td>
                                    <td class="py-2 px-4 border-b">
                                        <span class="product-code">-</span>
                                    </td>
                                    <td class="py-2 px-4 border-b">
                                        <select name="products[${rowCount}][id]" class="product-select w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                            <option value="">เลือกสินค้า</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-code="{{ $product->code ?? $product->sku ?? '-' }}" data-unit-id="{{ $product->unit_id }}" data-unit-name="{{ $product->unit ? $product->unit->name : '' }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="py-2 px-4 border-b">
                                        <input type="number" name="products[${rowCount}][quantity]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="1" step="any" value="1" required>
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <span class="product-unit">-</span>
                                        <input type="hidden" name="products[${rowCount}][unit_id]" class="unit-id" value="">
                                    </td>
                                    <td class="py-2 px-4 border-b">
                                        <input type="number" name="products[${rowCount}][unit_price]" class="unit-price w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" step="0.01" value="0.00" required>
                                    </td>
                                    <td class="py-2 px-4 border-b">
                                        <input type="text" class="subtotal w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="0.00" readonly>
                                    </td>
                                    <td class="py-2 px-4 border-b text-center">
                                        <button type="button" class="remove-product text-red-600 hover:text-red-900">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                `;
                                
                                productsList.appendChild(newRow);
                                
                                // กำหนด event listeners สำหรับแถวใหม่
                                initializeRowEvents(newRow);
                                
                                // คำนวณยอดรวมใหม่
                                calculateTotals();
                            });
                            
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
                                }
                            });
                            
                            // Initialize row events สำหรับแถวที่มีอยู่แล้ว
                            document.querySelectorAll('.product-row').forEach(row => {
                                initializeRowEvents(row);
                            });
                            
                            // Event listeners สำหรับการคำนวณยอดรวม
                            document.getElementById('discount_type').addEventListener('change', calculateTotals);
                            document.getElementById('discount_amount').addEventListener('input', calculateTotals);
                            document.getElementById('tax_rate').addEventListener('input', calculateTotals);
                            document.getElementById('shipping_cost').addEventListener('input', calculateTotals);
                            
                            // คำนวณยอดรวมครั้งแรก
                            calculateTotals();
                            
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
                            
                            // จัดลำดับรายการสินค้าใหม่
                            function reindexProductRows() {
                                const rows = productsList.querySelectorAll('.product-row');
                                rows.forEach((row, index) => {
                                    // อัพเดทคอลัมน์ลำดับ
                                    const seqCell = row.querySelector('td:first-child');
                                    if (seqCell) seqCell.textContent = index + 1;
                                    row.querySelectorAll('[name^="products["]').forEach(input => {
                                        const name = input.getAttribute('name');
                                        const newName = name.replace(/products\[\d+\]/, `products[${index}]`);
                                        input.setAttribute('name', newName);
                                    });
                                });
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

                            // เพิ่ม event listener สำหรับปุ่มโหลดข้อมูลใบเสนอราคา
                            document.getElementById('loadQuotationBtn').addEventListener('click', function() {
                                const quotationSelect = document.getElementById('approved_quotation_selector');
                                const quotationId = quotationSelect.value;
                                
                                if (!quotationId) {
                                    alert('กรุณาเลือกใบเสนอราคาก่อน');
                                    return;
                                }
                                
                                // แสดงสถานะกำลังโหลด
                                this.innerHTML = '<span class="inline-block animate-spin mr-1">⟳</span> กำลังโหลด...';
                                this.disabled = true;
                                
                                // ทำ AJAX request เพื่อดึงข้อมูลใบเสนอราคา
                                fetch(`/quotations/${quotationId}/get-data`)
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error(`HTTP error ${response.status}`);
                                        }
                                        return response.json();
                                    })
                                    .then(data => {
                                        // เซ็ตค่า quotation_id ในฟอร์ม
                                        document.getElementById('quotation_id').value = data.id;
                                        
                                        // เลือกลูกค้า
                                        const customerSelect = document.getElementById('customer_id');
                                        customerSelect.value = data.customer_id;
                                        
                                        // เซ็ตที่อยู่จัดส่ง
                                        document.getElementById('shipping_address').value = data.shipping_address || data.customer.address || '';
                                        
                                        // เซ็ตข้อมูลอื่นๆ
                                        document.getElementById('shipping_method').value = data.shipping_method || '';
                                        document.getElementById('shipping_cost').value = data.shipping_cost || 0;
                                        document.getElementById('payment_terms').value = data.payment_terms || '';
                                        document.getElementById('notes').value = data.notes || '';
                                        document.getElementById('discount_type').value = data.discount_type || 'fixed';
                                        document.getElementById('discount_amount').value = data.discount_amount || 0;
                                        document.getElementById('tax_rate').value = data.tax_rate || 7;
                                        
                                        // เซ็ตค่าพนักงานขาย
                                        if (data.sales_person_id) {
                                            const salesPersonSelect = document.getElementById('sales_person_id');
                                            salesPersonSelect.value = data.sales_person_id;
                                        }
                                        
                                        // ล้างรายการสินค้าเก่า
                                        const productsList = document.getElementById('productsList');
                                        while (productsList.firstChild) {
                                            productsList.removeChild(productsList.firstChild);
                                        }
                                        
                                        // เพิ่มรายการสินค้าจากใบเสนอราคา (ดึงหน่วยจากใบเสนอราคา)
                                        data.items.forEach((item, index) => {
                                            const newRow = document.createElement('tr');
                                            newRow.classList.add('product-row');

                                            // ดึงข้อมูลหน่วยจากใบเสนอราคา (unit_id/unit_name)
                                            let unitId = '';
                                            let unitName = '-';
                                            if (item.unit_id) {
                                                unitId = item.unit_id;
                                                // พยายามใช้ unit_name จาก QuotationItem->unit->name หรือจาก unitsList
                                                if (item.unit && item.unit.name) {
                                                    unitName = item.unit.name;
                                                } else if (window.unitsList && window.unitsList[unitId]) {
                                                    unitName = window.unitsList[unitId];
                                                }
                                            }

                                            newRow.innerHTML = `
                                                <td class="py-2 px-4 border-b text-center">${index + 1}</td>
                                                <td class="py-2 px-4 border-b">
                                                    <span class="product-code">${item.product ? (item.product.code || item.product.sku || '-') : '-'}</span>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <select name="products[${index}][id]" class="product-select w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                                        <option value="">เลือกสินค้า</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-code="{{ $product->code ?? $product->sku ?? '-' }}" data-unit-id="{{ $product->unit_id }}" data-unit-name="{{ $product->unit ? $product->unit->name : '' }}" ${item.product_id == {{ $product->id }} ? 'selected' : ''}>{{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <input type="number" name="products[${index}][quantity]" class="quantity w-full text-center border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="1" step="any" value="${item.quantity}" required>
                                                </td>
                                                <td class="py-2 px-4 border-b text-center">
                                                    <span class="product-unit">${unitName}</span>
                                                    <input type="hidden" name="products[${index}][unit_id]" class="unit-id" value="${unitId}">
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <input type="number" name="products[${index}][unit_price]" class="unit-price w-full text-right border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" min="0" step="0.01" value="${item.unit_price}" required>
                                                </td>
                                                <td class="py-2 px-4 border-b">
                                                    <input type="text" class="subtotal w-full text-right bg-gray-50 border-gray-300 rounded-md shadow-sm" value="${(item.quantity * item.unit_price).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}" readonly>
                                                </td>
                                                <td class="py-2 px-4 border-b text-center">
                                                    <button type="button" class="remove-product text-red-600 hover:text-red-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            `;

                                            productsList.appendChild(newRow);
                                            initializeRowEvents(newRow);

                                            // เลือกสินค้าในรายการ
                                            const productSelect = newRow.querySelector('.product-select');
                                            Array.from(productSelect.options).forEach(option => {
                                                if (option.value == item.product_id) {
                                                    option.selected = true;
                                                }
                                            });
                                        });
                                        
                                        // คำนวณยอดรวมใหม่
                                        calculateTotals();
                                        
                                        // อัพเดทปุ่ม
                                        this.innerHTML = '<span>โหลดข้อมูล</span>';
                                        this.disabled = false;
                                        
                                        // แจ้งเตือนสำเร็จ
                                        alert('โหลดข้อมูลใบเสนอราคาสำเร็จ');
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message);
                                        
                                        // คืนสถานะปุ่ม
                                        this.innerHTML = '<span>โหลดข้อมูล</span>';
                                        this.disabled = false;
                                    });

                                // ขอเลขที่ใบสั่งขายใหม่จากเซิร์ฟเวอร์ (ผ่าน AJAX)
                                fetch('/orders/generate-order-number')
                                    .then(res => res.json())
                                    .then(json => {
                                        if (json.order_number) {
                                            document.getElementById('order_number').value = json.order_number;
                                        }
                                    });
                            });
                            
                            // JavaScript สำหรับป้องกันการส่งฟอร์มซ้ำ
                            document.getElementById('orderCreateForm').addEventListener('submit', function(e) {
                                // ตรวจสอบฟิลด์สำคัญเพิ่มเติม
                                const customerId = document.getElementById('customer_id').value;
                                if (!customerId) {
                                    e.preventDefault(); // ป้องกันการส่งฟอร์ม
                                    alert('กรุณาเลือกลูกค้า');
                                    return false;
                                }
                                
                                // แสดง Loading State
                                document.querySelector('#submitBtn .normal-state').classList.add('hidden');
                                document.querySelector('#submitBtn .loading-state').classList.remove('hidden');
                                
                                // ปิดปุ่ม Submit
                                document.getElementById('submitBtn').disabled = true;
                            });

                            // ขอเลขที่ใบสั่งขายใหม่ทุกครั้งที่เปิดหน้า หรือหลังโหลดใบเสนอราคา
                            function refreshOrderNumber() {
                                fetch('/orders/generate-order-number')
                                    .then(res => res.json())
                                    .then(json => {
                                        if (json.order_number) {
                                            document.getElementById('order_number').value = json.order_number;
                                        }
                                    });
                            }

                            // เรียกทันทีเมื่อโหลดหน้า (ป้องกันเลขซ้ำ)
                            refreshOrderNumber();

                            document.getElementById('loadQuotationBtn').addEventListener('click', function() {
                                // ...existing code...
                                fetch(`/quotations/${quotationId}/get-data`)
                                    .then(response => {
                                        // ...existing code...
                                    })
                                    .then(data => {
                                        // ...existing code...
                                        // ขอเลขที่ใบสั่งขายใหม่หลังโหลดใบเสนอราคา
                                        refreshOrderNumber();
                                        // ...existing code...
                                    })
                                    // ...existing code...
                            });
                        });
                    </script>

                    {{-- คำแนะนำสำหรับการตรวจสอบข้อมูลใน tinker --}}
                    {{-- 
                        คุณสามารถตรวจสอบได้ว่า OrderItem ถูกสร้างมาจาก QuotationItem และมีข้อมูล unit_id หรือไม่ ด้วยการใช้ tinker ดังนี้:

                        1. เปิด tinker:
                            php artisan tinker

                        2. ค้นหา Order ที่ต้องการ (เช่น order ล่าสุด):
                            $order = \App\Models\Order::latest()->first();

                        3. ดูรายการสินค้าใน order:
                            $order->items->map(function($item) {
                                return [
                                    'product_id' => $item->product_id,
                                    'product_code' => $item->product ? $item->product->code : null,
                                    'quantity' => $item->quantity,
                                    'unit_id' => $item->unit_id,
                                    'unit_name' => $item->unit ? $item->unit->name : null,
                                ];
                            });

                        4. ถ้าต้องการดู QuotationItem ที่เกี่ยวข้อง (ถ้ามีการอ้างอิง quotation):
                            $order->quotation ? $order->quotation->items->map(function($item) {
                                return [
                                    'product_id' => $item->product_id,
                                    'quantity' => $item->quantity,
                                    'unit_id' => $item->unit_id,
                                    'unit_name' => $item->unit ? $item->unit->name : null,
                                ];
                            }) : null;

                        5. ตรวจสอบว่าข้อมูล unit_id/unit_name ถูกบันทึกมาจากใบเสนอราคาหรือไม่
                    --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>