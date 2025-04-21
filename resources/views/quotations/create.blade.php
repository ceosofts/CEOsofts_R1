<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('สร้างใบเสนอราคาใหม่') }}
            </h2>
            <div>
                <a href="{{ route('quotations.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปรายการใบเสนอราคา') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <form id="quotationForm" action="{{ route('quotations.store') }}" method="POST">
                @csrf

                <!-- ข้อมูลทั่วไปของใบเสนอราคา -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">ข้อมูลเอกสาร</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label for="quotation_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เลขที่เอกสาร <span class="text-red-600">*</span></label>
                                <input type="text" name="quotation_number" id="quotation_number" value="{{ old('quotation_number', $nextNumber) }}" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label for="reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">เลขที่อ้างอิง</label>
                                <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ลูกค้า <span class="text-red-600">*</span></label>
                                <select name="customer_id" id="customer_id" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- เลือกลูกค้า --</option>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">วันที่ออกเอกสาร <span class="text-red-600">*</span></label>
                                <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', date('Y-m-d')) }}" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">วันที่หมดอายุ <span class="text-red-600">*</span></label>
                                <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', date('Y-m-d', strtotime('+30 days'))) }}" required
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>

                            <div>
                                <label for="shipping_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">วิธีการจัดส่ง</label>
                                <input type="text" name="shipping_method" id="shipping_method" value="{{ old('shipping_method') }}"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">หมายเหตุ</label>
                            <textarea name="notes" id="notes" rows="3"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- รายการสินค้า -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">รายการสินค้า</h3>
                            <button type="button" id="add-product" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
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
                                        <th class="py-2 px-4 border-b text-left">สินค้า</th>
                                        <th class="py-2 px-4 border-b text-right">จำนวน</th>
                                        <th class="py-2 px-4 border-b text-right">หน่วย</th>
                                        <th class="py-2 px-4 border-b text-right">ราคาต่อหน่วย</th>
                                        <th class="py-2 px-4 border-b text-right">ส่วนลด (%)</th>
                                        <th class="py-2 px-4 border-b text-right">รวม</th>
                                        <th class="py-2 px-4 border-b text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="product-list">
                                    <!-- รายการสินค้าจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="no-products" class="mt-4 text-center text-gray-500 dark:text-gray-400 py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="mt-2">ยังไม่มีรายการสินค้า กรุณาคลิกปุ่ม "เพิ่มรายการ"</p>
                        </div>
                    </div>
                </div>

                <!-- ส่วนลดและภาษี -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">ส่วนลดและภาษี</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ประเภทส่วนลด</label>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <input type="radio" name="discount_type" id="discount_type_fixed" value="fixed" {{ old('discount_type', 'fixed') == 'fixed' ? 'checked' : '' }}
                                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600">
                                        <label for="discount_type_fixed" class="ml-2 text-sm text-gray-700 dark:text-gray-300">จำนวนเงิน</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="discount_type" id="discount_type_percentage" value="percentage" {{ old('discount_type') == 'percentage' ? 'checked' : '' }}
                                            class="rounded-full border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-800 dark:border-gray-600">
                                        <label for="discount_type_percentage" class="ml-2 text-sm text-gray-700 dark:text-gray-300">เปอร์เซ็นต์</label>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label for="discount_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">จำนวนส่วนลด</label>
                                <input type="number" name="discount_amount" id="discount_amount" value="{{ old('discount_amount', 0) }}" min="0" step="0.01"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label for="tax_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">อัตราภาษีมูลค่าเพิ่ม (%)</label>
                            <input type="number" name="tax_rate" id="tax_rate" value="{{ old('tax_rate', 7) }}" min="0" max="100" step="0.01"
                                class="block w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- สรุปยอด -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">สรุปยอด</h3>
                        
                        <div class="flex justify-end">
                            <div class="w-full md:w-1/2 lg:w-1/3">
                                <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">ยอดรวมก่อนภาษี</span>
                                    <span id="subtotal-display">0.00</span>
                                </div>
                                <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">ส่วนลด</span>
                                    <span id="discount-display">0.00</span>
                                </div>
                                <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                    <span class="text-gray-600 dark:text-gray-400">ภาษีมูลค่าเพิ่ม (7%)</span>
                                    <span id="tax-display">0.00</span>
                                </div>
                                <div class="flex justify-between py-3 font-bold">
                                    <span>ยอดรวมทั้งสิ้น</span>
                                    <span id="total-display" class="text-lg">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ปุ่มบันทึก -->
                <div class="flex justify-end">
                    <button type="button" onclick="window.history.back()" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-md mr-4 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        ยกเลิก
                    </button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        บันทึกใบเสนอราคา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Template สำหรับแถวสินค้า -->
    <template id="product-row-template">
        <tr class="product-row hover:bg-gray-50 dark:hover:bg-gray-600">
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <select name="products[INDEX][product_id]" class="product-select block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <option value="">-- เลือกสินค้า --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-unit="{{ $product->unit_id }}" data-code="{{ $product->code }}">
                        {{ $product->code ? "[$product->code] " : "" }}{{ $product->name }}
                    </option>
                    @endforeach
                </select>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <input type="text" name="products[INDEX][code]" class="product-code-display block w-full bg-gray-100 rounded-md border-gray-300 shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white text-center" readonly>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <input type="number" name="products[INDEX][quantity]" class="quantity-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right" value="1" min="0.01" step="0.01" required>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <select name="products[INDEX][unit_id]" class="unit-select block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <input type="number" name="products[INDEX][unit_price]" class="price-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right" value="0.00" min="0" step="0.01" required>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <input type="number" name="products[INDEX][discount_percentage]" class="discount-input block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-right" value="0" min="0" max="100" step="0.01">
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700">
                <span class="subtotal-display block text-right">0.00</span>
            </td>
            <td class="py-2 px-4 border-b dark:border-gray-700 text-center">
                <button type="button" class="delete-product text-red-600 hover:text-red-800">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let productIndex = 0;
            const productList = document.getElementById('product-list');
            const noProductsMessage = document.getElementById('no-products');
            const addProductButton = document.getElementById('add-product');
            const productRowTemplate = document.getElementById('product-row-template');

            // เพิ่มรายการสินค้า
            addProductButton.addEventListener('click', function() {
                addProductRow();
                updateNoProductsMessage();
                calculateTotals();
            });

            // ลบรายการสินค้า
            productList.addEventListener('click', function(e) {
                if (e.target.closest('.delete-product')) {
                    e.target.closest('.product-row').remove();
                    updateNoProductsMessage();
                    calculateTotals();
                }
            });

            // อัพเดตราคาเมื่อเลือกสินค้า
            productList.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select')) {
                    const row = e.target.closest('.product-row');
                    const option = e.target.options[e.target.selectedIndex];
                    const priceInput = row.querySelector('.price-input');
                    const unitSelect = row.querySelector('.unit-select');
                    const productCodeDisplay = row.querySelector('.product-code-display');
                    const price = option.getAttribute('data-price');
                    const unitId = option.getAttribute('data-unit');
                    const productCode = option.getAttribute('data-code') || '';
                    
                    if (price) {
                        priceInput.value = price;
                    }
                    
                    if (unitId) {
                        unitSelect.value = unitId;
                    }
                    
                    // เพิ่มการแสดงรหัสสินค้า
                    productCodeDisplay.value = productCode;
                    
                    calculateRowTotal(row);
                    calculateTotals();
                }
            });

            // คำนวณราคารวมเมื่อเปลี่ยนจำนวน, ราคา, หรือส่วนลด
            productList.addEventListener('input', function(e) {
                if (e.target.classList.contains('quantity-input') || 
                    e.target.classList.contains('price-input') || 
                    e.target.classList.contains('discount-input')) {
                    const row = e.target.closest('.product-row');
                    calculateRowTotal(row);
                    calculateTotals();
                }
            });

            // อัพเดตการคำนวณเมื่อเปลี่ยนส่วนลดหรืออัตราภาษี
            document.getElementById('discount_amount').addEventListener('input', calculateTotals);
            document.getElementById('discount_type_fixed').addEventListener('change', calculateTotals);
            document.getElementById('discount_type_percentage').addEventListener('change', calculateTotals);
            document.getElementById('tax_rate').addEventListener('input', calculateTotals);

            // เพิ่มแถวสินค้า
            function addProductRow() {
                const template = productRowTemplate.innerHTML;
                const newRow = template.replace(/INDEX/g, productIndex++);
                productList.insertAdjacentHTML('beforeend', newRow);
            }

            // คำนวณยอดรวมของแต่ละแถว
            function calculateRowTotal(row) {
                const quantityInput = row.querySelector('.quantity-input');
                const priceInput = row.querySelector('.price-input');
                const discountInput = row.querySelector('.discount-input');
                const subtotalDisplay = row.querySelector('.subtotal-display');
                
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const discount = parseFloat(discountInput.value) || 0;
                
                const subtotal = quantity * price * (1 - discount / 100);
                subtotalDisplay.textContent = subtotal.toFixed(2);
            }

            // คำนวณยอดรวมทั้งหมด
            function calculateTotals() {
                let subtotal = 0;
                document.querySelectorAll('.product-row').forEach(row => {
                    const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
                    
                    const rowSubtotal = quantity * price * (1 - discount / 100);
                    subtotal += rowSubtotal;
                });
                
                const discountType = document.querySelector('input[name="discount_type"]:checked').value;
                const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
                let totalDiscount = 0;
                
                if (discountType === 'percentage') {
                    totalDiscount = subtotal * (discountAmount / 100);
                } else {
                    totalDiscount = discountAmount;
                }
                
                const afterDiscount = subtotal - totalDiscount;
                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = afterDiscount * (taxRate / 100);
                const totalAmount = afterDiscount + taxAmount;
                
                document.getElementById('subtotal-display').textContent = subtotal.toFixed(2);
                document.getElementById('discount-display').textContent = totalDiscount.toFixed(2);
                document.getElementById('tax-display').textContent = taxAmount.toFixed(2);
                document.getElementById('total-display').textContent = totalAmount.toFixed(2);
            }

            // แสดง/ซ่อนข้อความ "ไม่มีรายการสินค้า"
            function updateNoProductsMessage() {
                if (document.querySelectorAll('.product-row').length > 0) {
                    noProductsMessage.style.display = 'none';
                } else {
                    noProductsMessage.style.display = 'block';
                }
            }

            // เพิ่มรายการสินค้าอย่างน้อย 1 รายการเมื่อโหลดหน้า
            addProductRow();
            updateNoProductsMessage();

            // เพิ่มการตรวจสอบและสร้าง products_json ก่อนส่งฟอร์ม
            document.getElementById('quotationForm').addEventListener('submit', function(e) {
                e.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ
                
                const productRows = document.querySelectorAll('.product-row');
                
                if (productRows.length === 0) {
                    alert('กรุณาเพิ่มอย่างน้อย 1 รายการสินค้า');
                    return;
                }
                
                // เตรียมข้อมูลสินค้าทั้งหมด - แก้ไขการเลือก elements ให้ตรงกับโครงสร้างฟอร์ม
                const products = [];
                
                productRows.forEach(function(row) {
                    const productSelect = row.querySelector('.product-select');
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    
                    const product = {
                        product_id: productSelect.value,
                        product_code: selectedOption.getAttribute('data-code') || '',
                        quantity: row.querySelector('.quantity-input').value,
                        unit_id: row.querySelector('.unit-select').value,
                        unit_price: row.querySelector('.price-input').value,
                        discount_percentage: row.querySelector('.discount-input').value || 0
                    };
                    
                    products.push(product);
                });
                
                console.log('Products data prepared:', products); // เพิ่ม log เพื่อตรวจสอบ
                
                // ลบ input เก่าออกเพื่อป้องกันการซ้ำซ้อน
                const oldInput = this.querySelector('input[name="products_json"]');
                if (oldInput) oldInput.remove();
                
                // สร้าง input hidden เพื่อเก็บข้อมูล products_json
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'products_json';
                input.value = JSON.stringify(products);
                
                // เพิ่ม input เข้าไปในฟอร์ม
                this.appendChild(input);
                
                console.log('Form submitting with products_json:', input.value);
                
                // ส่งฟอร์ม
                this.submit();
            });
        });
    </script>
</x-app-layout>
