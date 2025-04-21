<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-3xl text-blue-800">
                {{ __('แก้ไขใบเสนอราคา') }} #{{ $quotation->quotation_number }}
            </h2>
            <div>
                <a href="{{ route('quotations.show', $quotation) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 border border-gray-500 rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปรายละเอียด') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('quotations.update', $quotation) }}" method="POST" id="quotationForm">
                        @csrf
                        @method('PUT')

                        <!-- ข้อมูลหลักใบเสนอราคา -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <div class="mb-4">
                                    <label for="customer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ลูกค้า <span class="text-red-600">*</span></label>
                                    <select id="customer_id" name="customer_id" required class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('customer_id') border-red-500 @enderror">
                                        <option value="">-- เลือกลูกค้า --</option>
                                        @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" {{ old('customer_id', $quotation->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('customer_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="reference_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่อ้างอิง</label>
                                    <input type="text" id="reference_number" name="reference_number" value="{{ old('reference_number', $quotation->reference_number) }}" class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('reference_number') border-red-500 @enderror">
                                    @error('reference_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <div class="mb-4">
                                    <label for="quotation_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">เลขที่ใบเสนอราคา <span class="text-red-600">*</span></label>
                                    <input type="text" id="quotation_number" name="quotation_number" value="{{ $quotation->quotation_number }}" readonly class="block w-full rounded-md shadow-sm bg-gray-100 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="mb-4">
                                        <label for="issue_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันที่ <span class="text-red-600">*</span></label>
                                        <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', $quotation->issue_date->format('Y-m-d')) }}" required class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('issue_date') border-red-500 @enderror">
                                        @error('issue_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วันที่หมดอายุ <span class="text-red-600">*</span></label>
                                        <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $quotation->expiry_date->format('Y-m-d')) }}" required class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('expiry_date') border-red-500 @enderror">
                                        @error('expiry_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- รายการสินค้า -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3 flex justify-between items-center">
                                <span>รายการสินค้า</span>
                                <button type="button" id="addProductRow" class="px-3 py-1 text-sm text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    เพิ่มรายการ
                                </button>
                            </h3>

                            <div class="overflow-x-auto bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <table class="min-w-full" id="productsTable">
                                    <thead>
                                        <tr class="border-b dark:border-gray-600">
                                            <th class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">สินค้า</th>
                                            <th class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20">จำนวน</th>
                                            <th class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">หน่วย</th>
                                            <th class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">ราคา/หน่วย</th>
                                            <th class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">ส่วนลด (%)</th>
                                            <th class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-32">รวม</th>
                                            <th class="py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="productRowsContainer">
                                        <!-- แถวสินค้าจะถูกเพิ่มที่นี่ด้วย JavaScript -->
                                    </tbody>
                                </table>

                                <!-- แสดงเมื่อไม่มีรายการสินค้า -->
                                <div id="noProductsMessage" class="text-center py-4 text-gray-500 dark:text-gray-400 {{ count($quotation->items) > 0 ? 'hidden' : '' }}">
                                    ไม่มีรายการสินค้า กดปุ่ม "เพิ่มรายการ" เพื่อเพิ่มสินค้า
                                </div>
                            </div>
                        </div>
                        
                        <!-- ส่วนลดและภาษี -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">หมายเหตุ</label>
                                    <textarea id="notes" name="notes" rows="4" class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $quotation->notes) }}</textarea>
                                </div>

                                <div class="mb-4">
                                    <label for="shipping_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">วิธีการจัดส่ง</label>
                                    <input type="text" id="shipping_method" name="shipping_method" value="{{ old('shipping_method', $quotation->shipping_method) }}" class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                </div>
                            </div>

                            <div>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <div class="mb-4">
                                        <label for="discount_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ประเภทส่วนลด</label>
                                        <div class="flex space-x-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="discount_type" value="fixed" {{ old('discount_type', $quotation->discount_type) == 'fixed' ? 'checked' : '' }} class="form-radio">
                                                <span class="ml-2">จำนวนเงิน</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="discount_type" value="percentage" {{ old('discount_type', $quotation->discount_type) == 'percentage' ? 'checked' : '' }} class="form-radio">
                                                <span class="ml-2">เปอร์เซ็นต์</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="discount_amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            <span id="discount_label">{{ $quotation->discount_type == 'percentage' ? 'ส่วนลด (%)' : 'ส่วนลด (บาท)' }}</span>
                                        </label>
                                        <input type="number" id="discount_amount" name="discount_amount" value="{{ old('discount_amount', $quotation->discount_amount) }}" min="0" step="0.01" class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <div class="mb-4">
                                        <label for="tax_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ภาษีมูลค่าเพิ่ม (%)</label>
                                        <input type="number" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $quotation->tax_rate) }}" min="0" max="100" step="0.01" class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    </div>

                                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4 mt-4">
                                        <div class="flex justify-between">
                                            <span class="text-sm font-medium">ยอดรวมก่อนภาษี:</span>
                                            <span id="subtotal">{{ number_format($quotation->subtotal, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between mt-2">
                                            <span class="text-sm font-medium">ส่วนลด:</span>
                                            <span id="discount">{{ number_format($quotation->discount_amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between mt-2">
                                            <span class="text-sm font-medium">ภาษีมูลค่าเพิ่ม ({{ $quotation->tax_rate }}%):</span>
                                            <span id="tax">{{ number_format($quotation->tax_amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                            <span class="font-bold">ยอดรวมทั้งสิ้น:</span>
                                            <span id="total" class="font-bold">{{ number_format($quotation->total_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ปุ่ม Submit -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('quotations.show', $quotation) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                {{ __('ยกเลิก') }}
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('บันทึกการแก้ไข') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Template สำหรับแถวสินค้า -->
    <template id="productRowTemplate">
        <tr class="product-row border-b dark:border-gray-600">
            <td class="py-2">
                <select name="products[INDEX].product_id" class="product-select block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <option value="">-- เลือกสินค้า --</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-code="{{ $product->code }}">
                        {{ $product->code ? "[$product->code] " : "" }}{{ $product->name }}
                    </option>
                    @endforeach
                </select>
                <input type="hidden" name="products[INDEX].product_code" class="product-code">
            </td>
            <td class="py-2">
                <input type="number" name="products[INDEX].quantity" class="quantity block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="0.01" step="0.01" value="1" required>
            </td>
            <td class="py-2">
                <select name="products[INDEX].unit_id" class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <option value="">-- หน่วย --</option>
                    @foreach($units as $unit)
                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="py-2">
                <input type="number" name="products[INDEX].unit_price" class="unit-price block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="0" step="0.01" required>
            </td>
            <td class="py-2">
                <input type="number" name="products[INDEX].discount_percentage" class="discount-percentage block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" min="0" max="100" step="0.01" value="0">
            </td>
            <td class="py-2">
                <input type="text" class="subtotal block w-full rounded-md bg-gray-100 border-gray-300 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300" readonly>
            </td>
            <td class="py-2">
                <button type="button" class="remove-row text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let rowIndex = 0;
            const noProductsMessage = document.getElementById('noProductsMessage');
            const productRowsContainer = document.getElementById('productRowsContainer');
            const productRowTemplate = document.getElementById('productRowTemplate').content;
            
            // เพิ่มแถวสินค้าที่มีอยู่แล้ว
            const existingItems = @json($quotation->items);
            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(item => {
                    addProductRow(item);
                });
                updateTotals();
            }
            
            // ปุ่มเพิ่มแถวสินค้า
            document.getElementById('addProductRow').addEventListener('click', function() {
                addProductRow();
                toggleNoProductsMessage();
            });

            // เปลี่ยนฉลากของส่วนลดเมื่อเปลี่ยนประเภทส่วนลด
            document.querySelectorAll('input[name="discount_type"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const label = document.getElementById('discount_label');
                    label.textContent = this.value === 'percentage' ? 'ส่วนลด (%)' : 'ส่วนลด (บาท)';
                    updateTotals();
                });
            });

            // อัปเดตยอดรวมเมื่อมีการเปลี่ยนแปลงส่วนลดหรือภาษี
            document.getElementById('discount_amount').addEventListener('input', updateTotals);
            document.getElementById('tax_rate').addEventListener('input', updateTotals);

            // ฟังก์ชันเพิ่มแถวสินค้า
            function addProductRow(item = null) {
                const clone = document.importNode(productRowTemplate, true);
                
                // แทนที่ INDEX ในชื่อฟิลด์
                const inputs = clone.querySelectorAll('[name*="INDEX"]');
                inputs.forEach(input => {
                    input.name = input.name.replace('INDEX', rowIndex);
                });

                // เพิ่มการฟังก์ชันเมื่อเลือกสินค้า
                const productSelect = clone.querySelector('.product-select');
                productSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = selectedOption.getAttribute('data-price');
                    const productCode = selectedOption.getAttribute('data-code') || '';
                    const row = this.closest('.product-row');
                    const unitPriceInput = row.querySelector('.unit-price');
                    const productCodeInput = row.querySelector('.product-code');
                    
                    if (price) {
                        unitPriceInput.value = price;
                    }
                    
                    // เก็บรหัสสินค้า
                    productCodeInput.value = productCode;
                    
                    calculateRowTotal(row);
                });

                // เพิ่มการคำนวณเมื่อมีการเปลี่ยนค่าในฟิลด์
                const row = clone.querySelector('.product-row');
                const inputsToWatch = ['.quantity', '.unit-price', '.discount-percentage'];
                inputsToWatch.forEach(selector => {
                    const input = row.querySelector(selector);
                    input.addEventListener('input', function() {
                        calculateRowTotal(row);
                    });
                });

                // เพิ่มการลบแถว
                const removeButton = row.querySelector('.remove-row');
                removeButton.addEventListener('click', function() {
                    row.remove();
                    updateTotals();
                    toggleNoProductsMessage();
                });

                // เพิ่มค่าเริ่มต้นจากรายการที่มีอยู่แล้ว
                if (item) {
                    row.querySelector('.product-select').value = item.product_id;
                    row.querySelector('.quantity').value = item.quantity;
                    row.querySelector('select[name$="unit_id"]').value = item.unit_id;
                    row.querySelector('.unit-price').value = item.unit_price;
                    row.querySelector('.discount-percentage').value = item.discount_percentage;
                    calculateRowTotal(row);
                }

                productRowsContainer.appendChild(row);
                rowIndex++;
            }

            // คำนวณยอดรวมของแถว
            function calculateRowTotal(row) {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const discountPercentage = parseFloat(row.querySelector('.discount-percentage').value) || 0;
                
                const total = quantity * unitPrice * (1 - (discountPercentage / 100));
                row.querySelector('.subtotal').value = total.toFixed(2);
                
                updateTotals();
            }

            // อัปเดตยอดรวมทั้งหมด
            function updateTotals() {
                const subtotalElement = document.getElementById('subtotal');
                const discountElement = document.getElementById('discount');
                const taxElement = document.getElementById('tax');
                const totalElement = document.getElementById('total');
                
                let subtotal = 0;
                document.querySelectorAll('.subtotal').forEach(input => {
                    subtotal += parseFloat(input.value) || 0;
                });
                
                const discountType = document.querySelector('input[name="discount_type"]:checked').value;
                let discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
                if (discountType === 'percentage') {
                    discountAmount = subtotal * (discountAmount / 100);
                }
                
                const afterDiscount = subtotal - discountAmount;
                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = afterDiscount * (taxRate / 100);
                const total = afterDiscount + taxAmount;
                
                subtotalElement.textContent = subtotal.toFixed(2);
                discountElement.textContent = discountAmount.toFixed(2);
                taxElement.textContent = taxAmount.toFixed(2);
                totalElement.textContent = total.toFixed(2);
            }

            // แสดง/ซ่อนข้อความ "ไม่มีรายการสินค้า"
            function toggleNoProductsMessage() {
                const hasProducts = productRowsContainer.querySelectorAll('.product-row').length > 0;
                noProductsMessage.classList.toggle('hidden', hasProducts);
            }

            // เตรียมฟอร์มก่อนส่ง - แก้ไขรูปแบบการส่งข้อมูลรายการสินค้า
            document.getElementById('quotationForm').addEventListener('submit', function(e) {
                e.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ
                
                const hasProducts = productRowsContainer.querySelectorAll('.product-row').length > 0;
                if (!hasProducts) {
                    alert('กรุณาเพิ่มอย่างน้อย 1 รายการสินค้า');
                    return;
                }
                
                // เตรียมข้อมูลรายการสินค้าในรูปแบบที่ server สามารถประมวลผลได้
                const productRows = productRowsContainer.querySelectorAll('.product-row');
                const products = [];
                
                productRows.forEach(function(row, index) {
                    const product = {
                        product_id: row.querySelector('.product-select').value,
                        quantity: row.querySelector('.quantity').value,
                        unit_id: row.querySelector('select[name$="unit_id"]').value,
                        unit_price: row.querySelector('.unit-price').value,
                        discount_percentage: row.querySelector('.discount-percentage').value
                    };
                    products.push(product);
                });
                
                // สร้าง hidden input เพื่อส่งข้อมูลรายการสินค้า
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'products_json';
                input.value = JSON.stringify(products);
                this.appendChild(input);
                
                // ส่งฟอร์ม
                this.submit();
            });
        });
    </script>

</x-app-layout>
