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
                                
                                <!-- เพิ่มฟิลด์พนักงานขาย -->
                                <div class="mb-4">
                                    <label for="sales_person_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">พนักงานขาย</label>
                                    <select id="sales_person_id" name="sales_person_id" class="block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white @error('sales_person_id') border-red-500 @enderror">
                                        <option value="">-- เลือกพนักงานขาย --</option>
                                        @foreach(\App\Models\Employee::where('company_id', session('company_id'))->orderBy('first_name')->get() as $employee)
                                        <option value="{{ $employee->id }}" {{ old('sales_person_id', $quotation->sales_person_id) == $employee->id ? 'selected' : '' }}>
                                            {{ $employee->employee_code }} - {{ $employee->first_name }} {{ $employee->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('sales_person_id')
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

                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white dark:bg-gray-700">
                                    <thead>
                                        <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                            <th class="py-2 px-4 border-b text-center">ลำดับ</th>
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
                                        <!-- รายการสินค้าจะถูกโหลดผ่าน JavaScript -->
                                    </tbody>
                                </table>
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
            <td class="py-2 px-4 text-center item-sequence">
                <!-- ลำดับจะถูกใส่ผ่าน JavaScript -->
            </td>
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
                <select name="products[INDEX].unit_id" class="unit-select block w-full rounded-md shadow-sm focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <option value="">-- เลือกหน่วย --</option>
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
            const productList = document.getElementById('product-list');
            const productRowTemplate = document.getElementById('productRowTemplate').content;
            
            // เพิ่มแถวสินค้าที่มีอยู่แล้ว
            const existingItems = @json($quotation->items()->with('unit', 'product')->get());
            console.log('Existing items with relationships:', existingItems);
            
            if (existingItems && existingItems.length > 0) {
                existingItems.forEach(item => {
                    addProductRow(item);
                });
                updateTotals();
            } else {
                console.log('No existing items found or items array is empty');
            }
            
            // ปุ่มเพิ่มแถวสินค้า
            document.getElementById('addProductRow').addEventListener('click', function() {
                addProductRow();
                updateSequenceNumbers(); // เรียกใช้ฟังก์ชันอัพเดทเลขลำดับ
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

            // Event delegation for product list changes
            document.getElementById('product-list').addEventListener('change', function(event) {
                const target = event.target;
                const row = target.closest('.product-row');
                
                if (!row) return;
                
                if (target.classList.contains('product-select')) {
                    const selectedOption = target.options[target.selectedIndex];
                    if (selectedOption) {
                        const price = selectedOption.dataset.price || 0;
                        const code = selectedOption.dataset.code || '';
                        row.querySelector('.product-code').value = code;
                        row.querySelector('.unit-price').value = price;
                        calculateRowTotal(row);
                    }
                } else if (target.classList.contains('quantity') || 
                          target.classList.contains('unit-price') || 
                          target.classList.contains('discount-percentage')) {
                    calculateRowTotal(row);
                }
            });

            // Event delegation for removing products
            document.getElementById('product-list').addEventListener('click', function(event) {
                if (event.target.closest('.remove-row')) {
                    const row = event.target.closest('.product-row');
                    row.remove();
                    updateSequenceNumbers();
                    updateTotals();
                }
            });

            // ฟังก์ชันเพิ่มแถวสินค้า
            function addProductRow(existingItem = null) {
                const row = document.importNode(productRowTemplate, true);
                const index = rowIndex++;
                
                // อัปเดต index ในชื่อฟิลด์
                row.querySelectorAll('[name^="products[INDEX]"]').forEach(field => {
                    field.name = field.name.replace('INDEX', index);
                });

                // ถ้ามีข้อมูลอยู่แล้ว ให้เติมข้อมูลเข้าไป
                if (existingItem) {
                    // กำหนดค่าสินค้า
                    const productSelect = row.querySelector('.product-select');
                    let foundProduct = false;
                    for (let i = 0; i < productSelect.options.length; i++) {
                        if (productSelect.options[i].value === String(existingItem.product_id)) {
                            productSelect.selectedIndex = i;
                            foundProduct = true;
                            break;
                        }
                    }
                    if (!foundProduct && existingItem.product) {
                        let newOption = new Option(
                            `${existingItem.product.code ? '[' + existingItem.product.code + '] ' : ''}${existingItem.product.name}`,
                            existingItem.product.id
                        );
                        productSelect.add(newOption);
                        productSelect.value = existingItem.product.id;
                    }

                    // กำหนดรหัสสินค้า
                    row.querySelector('.product-code').value = existingItem.product_code || '';
                    // กำหนดจำนวน
                    row.querySelector('.quantity').value = existingItem.quantity;
                    // กำหนดหน่วย
                    const unitSelect = row.querySelector('.unit-select');
                    let foundUnit = false;
                    for (let i = 0; i < unitSelect.options.length; i++) {
                        if (unitSelect.options[i].value === String(existingItem.unit_id)) {
                            unitSelect.selectedIndex = i;
                            foundUnit = true;
                            break;
                        }
                    }
                    if (!foundUnit) {
                        let unitName = '';
                        if (existingItem.unit && existingItem.unit.name) {
                            unitName = existingItem.unit.name;
                        } else {
                            unitName = 'หน่วย #' + existingItem.unit_id;
                        }
                        let newOption = new Option(unitName, existingItem.unit_id);
                        unitSelect.add(newOption);
                        unitSelect.value = existingItem.unit_id;
                    }
                    // กำหนดราคาต่อหน่วย
                    row.querySelector('.unit-price').value = existingItem.unit_price;
                    // กำหนดส่วนลด
                    row.querySelector('.discount-percentage').value = existingItem.discount_percentage || 0;
                    // คำนวณยอดรวมของแถว
                    const subtotal = existingItem.quantity * existingItem.unit_price * (1 - existingItem.discount_percentage / 100);
                    row.querySelector('.subtotal').value = subtotal.toFixed(2);
                    // กำหนด id สำหรับรายการที่มีอยู่แล้ว
                    const itemIdInput = document.createElement('input');
                    itemIdInput.type = 'hidden';
                    itemIdInput.name = `products[${index}].id`;
                    itemIdInput.value = existingItem.id;
                    row.querySelector('td:first-child').appendChild(itemIdInput);
                }
                
                // อัปเดตลำดับ
                row.querySelector('.item-sequence').textContent = productList.children.length + 1;
                
                // เพิ่มแถวลงใน DOM
                productList.appendChild(row);

                // คำนวณยอดรวมหากไม่มีข้อมูลอยู่แล้ว
                if (!existingItem) {
                    calculateRowTotal(productList.lastElementChild);
                    updateTotals();
                }
            }

            // คำนวณยอดรวมของแถว
            function calculateRowTotal(row) {
                const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
                const discountPercentage = parseFloat(row.querySelector('.discount-percentage').value) || 0;
                
                const subtotal = quantity * unitPrice * (1 - discountPercentage / 100);
                row.querySelector('.subtotal').value = subtotal.toFixed(2);
                
                updateTotals();
            }

            // อัปเดตเลขลำดับ
            function updateSequenceNumbers() {
                const rows = document.querySelectorAll('#product-list .product-row');
                rows.forEach((row, index) => {
                    row.querySelector('.item-sequence').textContent = index + 1;
                });
            }

            // อัปเดตยอดรวมทั้งหมด
            function updateTotals() {
                // คำนวณยอดรวมก่อนหักส่วนลด
                let subtotal = 0;
                document.querySelectorAll('#product-list .product-row').forEach(row => {
                    subtotal += parseFloat(row.querySelector('.subtotal').value) || 0;
                });
                
                // หาประเภทและมูลค่าของส่วนลด
                const discountType = document.querySelector('input[name="discount_type"]:checked').value;
                const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
                
                // คำนวณส่วนลด
                let discount = 0;
                if (discountType === 'percentage') {
                    discount = subtotal * (discountAmount / 100);
                } else {
                    discount = discountAmount;
                }
                
                // คำนวณยอดหลังหักส่วนลด
                const afterDiscount = subtotal - discount;
                
                // คำนวณภาษีมูลค่าเพิ่ม
                const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;
                const taxAmount = afterDiscount * (taxRate / 100);
                
                // คำนวณยอดรวมทั้งสิ้น
                const totalAmount = afterDiscount + taxAmount;
                
                // อัปเดตการแสดงผล
                document.getElementById('subtotal').textContent = subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                document.getElementById('discount').textContent = discount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                document.getElementById('tax').textContent = taxAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                document.getElementById('total').textContent = totalAmount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                
                // อัปเดตค่าภาษีที่แสดง
                const taxLabel = document.querySelector('.text-sm.font-medium:nth-child(1)');
                if (taxLabel) {
                    taxLabel.textContent = `ภาษีมูลค่าเพิ่ม (${taxRate}%):`;
                }
            }

            // เพิ่มฟังก์ชันสำหรับการส่งฟอร์ม
            document.getElementById('quotationForm').addEventListener('submit', function(e) {
                e.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ
                
                try {
                    // รวบรวมข้อมูลรายการสินค้าทั้งหมด
                    const products = [];
                    const rows = document.querySelectorAll('#product-list .product-row');
                    
                    rows.forEach(row => {
                        const productId = row.querySelector('.product-select').value;
                        
                        if (!productId) {
                            throw new Error('กรุณาเลือกสินค้าให้ครบทุกรายการ');
                        }
                        
                        const product = {
                            product_id: productId,
                            quantity: parseFloat(row.querySelector('.quantity').value) || 0,
                            unit_id: row.querySelector('.unit-select').value,
                            unit_price: parseFloat(row.querySelector('.unit-price').value) || 0,
                            discount_percentage: parseFloat(row.querySelector('.discount-percentage').value) || 0
                        };
                        
                        // เพิ่ม ID หากเป็นรายการที่มีอยู่แล้ว
                        const itemIdInput = row.querySelector('input[name$=".id"]');
                        if (itemIdInput) {
                            product.id = itemIdInput.value;
                        }
                        
                        products.push(product);
                    });
                    
                    if (products.length === 0) {
                        throw new Error('กรุณาเพิ่มอย่างน้อย 1 รายการสินค้า');
                    }
                    
                    // สร้าง hidden input สำหรับ products_json
                    let productsJsonInput = document.getElementById('products_json');
                    if (!productsJsonInput) {
                        productsJsonInput = document.createElement('input');
                        productsJsonInput.type = 'hidden';
                        productsJsonInput.id = 'products_json';
                        productsJsonInput.name = 'products_json';
                        this.appendChild(productsJsonInput);
                    }
                    
                    // แปลงข้อมูลเป็น JSON และเก็บลงใน hidden input
                    productsJsonInput.value = JSON.stringify(products);
                    console.log('Products JSON:', productsJsonInput.value);
                    
                    // ส่งฟอร์ม
                    this.submit();
                    
                } catch (error) {
                    alert(error.message);
                    console.error(error);
                }
            });
        });
    </script>

</x-app-layout>
