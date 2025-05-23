<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('สร้างใบแจ้งหนี้') }}{{ $order ? ' จากใบสั่งขาย #'.$order->order_number : '' }}
            </h2>
            <div>
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปรายการใบแจ้งหนี้') }}
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

            <form action="{{ route('invoices.store') }}" method="POST" id="invoiceCreateForm">
                @csrf

                @if($order)
                    <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">
                @else
                    <input type="hidden" name="order_id" id="order_id" value="">
                @endif

                <!-- ส่วนของการเลือกใบสั่งขาย -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">เลือกใบสั่งขาย</h3>
                        
                        <div class="bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0 text-blue-400 dark:text-blue-300">
                                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3 w-full">
                                    <p class="text-sm text-blue-800 dark:text-blue-300 mb-2">
                                        เลือกใบสั่งขายที่ต้องการสร้างใบแจ้งหนี้
                                    </p>
                                    <div class="flex flex-col md:flex-row md:space-x-2 space-y-2 md:space-y-0">
                                        <select id="order_selector" class="w-full md:flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                            <option value="">-- เลือกใบสั่งขาย --</option>
                                            @php
                                                $companyId = session('company_id') ?? session('current_company_id') ?? 1;
                                                $orders = \App\Models\Order::with('customer')
                                                    ->where('company_id', $companyId)
                                                    ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                                                    ->orderBy('order_number', 'desc')
                                                    ->get();
                                            @endphp
                                            @foreach($orders as $orderItem)
                                            <option value="{{ $orderItem->id }}" data-customer="{{ $orderItem->customer_id }}" {{ $order && $order->id == $orderItem->id ? 'selected' : '' }}>
                                                {{ $orderItem->order_number }} - {{ $orderItem->customer->name ?? 'ไม่ระบุชื่อลูกค้า' }} ({{ number_format($orderItem->total_amount, 2) }} บาท)
                                            </option>
                                            @endforeach
                                        </select>
                                        <button type="button" id="loadOrderBtn" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-blue-700 dark:hover:bg-blue-600">
                                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            <span>โหลดข้อมูล</span>
                                        </button>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                        <p>หรือ <a href="{{ route('orders.index') }}?status=confirmed" class="text-blue-600 hover:underline dark:text-blue-400">ดูรายการใบสั่งขายทั้งหมด</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลทั่วไปของใบแจ้งหนี้ -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">ข้อมูลเอกสาร</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="invoice_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เลขที่ใบแจ้งหนี้ <span class="text-red-600">*</span></label>
                                <input type="text" name="invoice_number" id="invoice_number" value="{{ old('invoice_number', $invoiceNumber) }}" required 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('invoice_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เลขที่อ้างอิง</label>
                                <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('reference_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ลูกค้า <span class="text-red-600">*</span></label>
                                <select id="customer_id" name="customer_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                    <option value="">-- เลือกลูกค้า --</option>
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" @if(old('customer_id', $order ? $order->customer_id : null) == $customer->id) selected @endif>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($order)
                                    <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                                @endif
                                @error('customer_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="invoice_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">วันที่ใบแจ้งหนี้ <span class="text-red-600">*</span></label>
                                <input type="date" name="invoice_date" id="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('invoice_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">วันครบกำหนดชำระ</label>
                                <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('due_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="sales_person_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">พนักงานขาย</label>
                                <select id="sales_person_id" name="sales_person_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- เลือกพนักงานขาย --</option>
                                    @php
                                        $companyId = session('company_id') ?? session('current_company_id') ?? 1;
                                        $employees = \App\Models\Employee::where('company_id', $companyId)->orderBy('first_name')->get();
                                    @endphp
                                    @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ old('sales_person_id', $order ? $order->sales_person_id : null) == $employee->id ? 'selected' : '' }}>
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
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">หมายเหตุ</label>
                            <textarea id="notes" name="notes" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $order ? $order->notes : '') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ข้อมูลการจัดส่ง -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">ข้อมูลการจัดส่ง</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ที่อยู่จัดส่ง</label>
                                <textarea id="shipping_address" name="shipping_address" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('shipping_address', $order ? $order->shipping_address : '') }}</textarea>
                                @error('shipping_address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <div class="mb-4">
                                    <label for="shipping_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">วิธีการจัดส่ง</label>
                                    <input type="text" name="shipping_method" id="shipping_method" value="{{ old('shipping_method', $order ? $order->shipping_method : '') }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('shipping_method')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="shipping_cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ค่าขนส่ง</label>
                                    <input type="number" name="shipping_cost" id="shipping_cost" step="0.01" min="0" value="{{ old('shipping_cost', $order ? $order->shipping_cost : 0) }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('shipping_cost')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- รายการสินค้า -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">รายการสินค้า</h3>
                            <button type="button" id="addProductBtn" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 dark:bg-green-700 dark:hover:bg-green-600">
                                <svg class="-ml-1 mr-2 h-5 w-5 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                เพิ่มรายการ
                            </button>
                        </div>
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white dark:bg-gray-700">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-center" style="width: 50px;">ลำดับ</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-left">รหัสสินค้า</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-left">สินค้า</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-center" style="width: 120px;">จำนวน</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-center" style="width: 80px;">หน่วย</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-right" style="width: 150px;">ราคาต่อหน่วย</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-right" style="width: 150px;">จำนวนเงิน</th>
                                        <th class="py-2 px-4 border-b dark:border-gray-600 text-center" style="width: 80px;">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="productsList">
                                    <!-- รายการสินค้าจะถูกเพิ่มที่นี่ด้วย JavaScript หรือจากใบสั่งขาย -->
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-50 dark:bg-gray-600">
                                        <td colspan="6" class="py-2 px-4 text-right font-medium text-gray-700 dark:text-gray-300">รวมเป็นเงิน:</td>
                                        <td class="py-2 px-4 text-right">
                                            <input type="text" id="subtotalDisplay" class="w-full text-right bg-gray-50 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded-md shadow-sm dark:text-gray-300" value="{{ $order ? number_format($order->subtotal, 2) : '0.00' }}" readonly>
                                            <input type="hidden" name="subtotal" id="subtotal" value="{{ $order ? $order->subtotal : '0' }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="py-2 px-4 text-right font-medium text-gray-700 dark:text-gray-300">
                                            <div class="flex justify-end items-center">
                                                <span class="mr-2">ส่วนลด:</span>
                                                <select name="discount_type" id="discount_type" class="mr-2 border-gray-300 dark:border-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" style="width:100px;">
                                                    <option value="fixed" @if(old('discount_type', $order ? $order->discount_type : 'fixed') == 'fixed') selected @endif>บาท</option>
                                                    <option value="percentage" @if(old('discount_type', $order ? $order->discount_type : '') == 'percentage') selected @endif>%</option>
                                                </select>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 text-right">
                                            <input type="number" name="discount_amount" id="discount_amount" class="w-full text-right border-gray-300 dark:border-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" min="0" step="0.01" value="{{ old('discount_amount', $order ? $order->discount_amount : '0') }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="6" class="py-2 px-4 text-right font-medium text-gray-700 dark:text-gray-300">
                                            <div class="flex justify-end items-center">
                                                <span>ภาษีมูลค่าเพิ่ม:</span>
                                                <input type="number" name="tax_rate" id="tax_rate" class="ml-2 w-24 text-right border-gray-300 dark:border-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" min="0" max="100" step="0.01" value="{{ old('tax_rate', $order ? $order->tax_rate : '7') }}">
                                                <span class="ml-1">%</span>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 text-right">
                                            <input type="text" id="tax_amount_display" class="w-full text-right bg-gray-50 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded-md shadow-sm dark:text-gray-300" value="{{ $order ? number_format($order->tax_amount, 2) : '0.00' }}" readonly>
                                            <input type="hidden" name="tax_amount" id="tax_amount" value="{{ $order ? $order->tax_amount : '0' }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr class="bg-blue-50 dark:bg-blue-900/30">
                                        <td colspan="6" class="py-2 px-4 text-right font-bold text-gray-700 dark:text-gray-300">จำนวนเงินรวมทั้งสิ้น:</td>
                                        <td class="py-2 px-4 text-right">
                                            <input type="text" id="total_amount_display" class="w-full text-right font-bold bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800 rounded-md shadow-sm text-blue-800 dark:text-blue-200" value="{{ $order ? number_format($order->total_amount, 2) : '0.00' }}" readonly>
                                            <input type="hidden" name="total_amount" id="total_amount" value="{{ $order ? $order->total_amount : '0' }}">
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        
                        <div id="no-products" class="mt-4 text-center text-gray-500 dark:text-gray-400 py-8 hidden">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-2">ยังไม่มีรายการสินค้า กรุณาคลิกปุ่ม "เพิ่มรายการ"</p>
                        </div>
                    </div>
                </div>

                <!-- เงื่อนไขการชำระเงิน -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">เงื่อนไขการชำระเงิน</h3>
                        
                        <div>
                            <label for="payment_terms" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เงื่อนไขการชำระเงิน</label>
                            <textarea id="payment_terms" name="payment_terms" rows="3" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('payment_terms', $order ? $order->payment_terms : 'ชำระภายใน 30 วัน') }}</textarea>
                            @error('payment_terms')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- สถานะ -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">สถานะใบแจ้งหนี้</h3>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ <span class="text-red-600">*</span></label>
                            <select id="status" name="status" class="w-full md:w-1/4 border-gray-300 dark:border-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                                <option value="draft" @if(old('status') == 'draft') selected @endif>ร่าง</option>
                                <option value="issued" @if(old('status') == 'issued') selected @endif>ออกใบแจ้งหนี้</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <div class="flex justify-end">
                    <button type="button" onclick="window.history.back()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md mr-4 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600">
                        ยกเลิก
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:bg-blue-700 dark:hover:bg-blue-600">
                        <span class="normal-state">บันทึกใบแจ้งหนี้</span>
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
        <tr class="product-row hover:bg-gray-50 dark:hover:bg-gray-600">
            <td class="py-2 px-4 border-b dark:border-gray-700 text-center">ROW_NUMBER</td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <span class="product-code">-</span>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <select name="products[INDEX][product_id]" class="product-select w-full border-gray-300 dark:border-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" required>
                    <option value="">เลือกสินค้า</option>
                    @php
                        $companyId = session('company_id') ?? session('current_company_id') ?? 1;
                        $products = \App\Models\Product::where('company_id', $companyId)
                            ->where('is_active', true)
                            ->orderBy('name')
                            ->get();
                    @endphp
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
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <input type="number" name="products[INDEX][quantity]" class="quantity w-full text-center border-gray-300 dark:border-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" min="1" step="any" value="1" required>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700 text-center">
                <span class="product-unit">-</span>
                <input type="hidden" name="products[INDEX][unit_id]" class="unit-id" value="">
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <input type="number" name="products[INDEX][unit_price]" class="unit-price w-full text-right border-gray-300 dark:border-gray-500 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:bg-gray-700 dark:text-white" min="0" step="0.01" value="0.00" required>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <input type="text" class="subtotal w-full text-right bg-gray-50 dark:bg-gray-600 border-gray-300 dark:border-gray-500 rounded-md shadow-sm dark:text-gray-300" value="0.00" readonly>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700 text-center">
                <button type="button" class="remove-product text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
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
            const noProductsMessage = document.getElementById('no-products');
            let productIndex = 0;
            
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
            
            // แสดง/ซ่อนข้อความ "ไม่มีรายการสินค้า"
            function updateNoProductsMessage() {
                if (document.querySelectorAll('.product-row').length > 0) {
                    noProductsMessage.classList.add('hidden');
                } else {
                    noProductsMessage.classList.remove('hidden');
                }
            }

            // Event listeners สำหรับการคำนวณยอดรวม
            document.getElementById('discount_type').addEventListener('change', calculateTotals);
            document.getElementById('discount_amount').addEventListener('input', calculateTotals);
            document.getElementById('tax_rate').addEventListener('input', calculateTotals);
            document.getElementById('shipping_cost').addEventListener('input', calculateTotals);

            // เพิ่ม event listener สำหรับปุ่มโหลดข้อมูลใบสั่งขาย
            document.getElementById('loadOrderBtn').addEventListener('click', function() {
                const orderSelector = document.getElementById('order_selector');
                const orderId = orderSelector.value;
                
                if (!orderId) {
                    alert('กรุณาเลือกใบสั่งขายก่อน');
                    return;
                }
                
                // แสดงสถานะกำลังโหลด
                this.innerHTML = '<span class="inline-block animate-spin mr-1">⟳</span> กำลังโหลด...';
                this.disabled = true;
                
                // ทำ AJAX request เพื่อดึงข้อมูลใบสั่งขาย
                fetch(`/orders/${orderId}/get-data`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Set order_id in hidden field
                        document.getElementById('order_id').value = data.id;
                        
                        // เลือกลูกค้า
                        const customerSelect = document.getElementById('customer_id');
                        customerSelect.value = data.customer_id;
                        
                        // เซ็ตที่อยู่จัดส่ง
                        document.getElementById('shipping_address').value = data.shipping_address || data.customer.address || '';
                        
                        // เซ็ตข้อมูลอื่นๆ
                        document.getElementById('shipping_method').value = data.shipping_method || '';
                        document.getElementById('shipping_cost').value = data.shipping_cost || 0;
                        document.getElementById('payment_terms').value = data.payment_terms || 'ชำระเงินภายใน 30 วัน';
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
                        while (productsList.firstChild) {
                            productsList.removeChild(productsList.firstChild);
                        }
                        
                        // รีเซ็ตค่า index
                        productIndex = 0;
                        
                        // เพิ่มรายการสินค้าจากใบสั่งขาย
                        if (data.items && data.items.length > 0) {
                            data.items.forEach((item, idx) => {
                                const template = productRowTemplate.innerHTML;
                                const newIndex = productIndex++;
                                let newRow = template.replace(/INDEX/g, newIndex).replace('ROW_NUMBER', idx + 1);
                                
                                productsList.insertAdjacentHTML('beforeend', newRow);
                                const row = productsList.lastElementChild;
                                
                                // ดึงข้อมูลหน่วยจากใบสั่งขาย
                                let unitId = '';
                                let unitName = '-';
                                if (item.unit_id) {
                                    unitId = item.unit_id;
                                    if (item.unit && item.unit.name) {
                                        unitName = item.unit.name;
                                    } else if (window.unitsList && window.unitsList[unitId]) {
                                        unitName = window.unitsList[unitId];
                                    }
                                }
                                
                                // เลือกสินค้าในรายการ
                                const productSelect = row.querySelector('.product-select');
                                Array.from(productSelect.options).forEach(option => {
                                    if (option.value == item.product_id) {
                                        option.selected = true;
                                    }
                                });
                                
                                // แก้ไขส่วนนี้: เพิ่มการกำหนดค่า product_id ให้กับ input ชื่อ products[INDEX][id]
                                // หาทุก input ที่มีชื่อขึ้นต้นด้วย products[INDEX] และมีคำว่า id ต่อท้าย
                                const productIdField = row.querySelector(`[name="products[${newIndex}][id]"]`);
                                if (productIdField) {
                                    productIdField.value = item.product_id;
                                } else {
                                    // ถ้าไม่พบ field นี้ ให้สร้างใหม่
                                    const hiddenInput = document.createElement('input');
                                    hiddenInput.type = 'hidden';
                                    hiddenInput.name = `products[${newIndex}][id]`;
                                    hiddenInput.value = item.product_id;
                                    row.appendChild(hiddenInput);
                                }
                                
                                // กำหนดค่าอื่นๆ
                                row.querySelector('.quantity').value = item.quantity;
                                row.querySelector('.unit-price').value = item.unit_price;
                                row.querySelector('.unit-id').value = unitId;
                                row.querySelector('.product-unit').textContent = unitName;
                                row.querySelector('.product-code').textContent = item.product ? (item.product.code || item.product.sku || '-') : '-';
                                
                                // คำนวณยอดรวม
                                updateRowTotal(row);
                                initializeRowEvents(row);
                            });
                        } else {
                            // ถ้าไม่มีรายการสินค้าในใบสั่งขาย
                            addProductRow();
                        }
                        
                        // อัพเดทสถานะการแสดงข้อความ "ไม่มีรายการสินค้า"
                        updateNoProductsMessage();
                        
                        // คำนวณยอดรวมใหม่
                        calculateTotals();
                        
                        // อัพเดทปุ่ม
                        this.innerHTML = '<svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg><span>โหลดข้อมูล</span>';
                        this.disabled = false;
                        
                        // แจ้งเตือนสำเร็จ
                        alert('โหลดข้อมูลใบสั่งขายสำเร็จ');
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('เกิดข้อผิดพลาดในการโหลดข้อมูล: ' + error.message);
                        
                        // คืนสถานะปุ่ม
                        this.innerHTML = '<svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg><span>โหลดข้อมูล</span>';
                        this.disabled = false;
                    });

                // ขอเลขที่ใบแจ้งหนี้ใหม่จากเซิร์ฟเวอร์ (ผ่าน AJAX)
                refreshInvoiceNumber();
            });
            
            // JavaScript สำหรับป้องกันการส่งฟอร์มซ้ำ
            document.getElementById('invoiceCreateForm').addEventListener('submit', function(e) {
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

            // ขอเลขที่ใบแจ้งหนี้ใหม่ทุกครั้งที่เปิดหน้า หรือหลังโหลดใบสั่งขาย
            function refreshInvoiceNumber() {
                fetch('/invoices/generate-invoice-number')
                    .then(res => res.json())
                    .then(json => {
                        if (json.invoice_number) {
                            document.getElementById('invoice_number').value = json.invoice_number;
                        }
                    });
            }

            // เรียกทันทีเมื่อโหลดหน้า (ป้องกันเลขซ้ำ)
            refreshInvoiceNumber();

            // เพิ่มรายการสินค้าเริ่มต้น (หากไม่มีการโหลดจากใบสั่งขาย)
            if (!document.querySelector('.product-row')) {
                addProductRow();
            }
            
            // ตรวจสอบและอัพเดทสถานะการแสดง "ไม่มีรายการสินค้า"
            updateNoProductsMessage();
            
            // คำนวณยอดรวมครั้งแรก
            calculateTotals();
            
            // กำหนด event listeners สำหรับแถวที่มีอยู่แล้ว
            initializeExistingRows();
        });
    </script>
</x-app-layout>