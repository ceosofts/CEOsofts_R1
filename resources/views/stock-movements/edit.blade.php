<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('แก้ไขการเคลื่อนไหวสินค้า') }}
            </h2>
            <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                {{ __('กลับ') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('stock-movements.update', $stockMovement) }}">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- ประเภทการเคลื่อนไหว -->
                            <div>
                                <x-input-label for="movement_type" :value="__('ประเภทการเคลื่อนไหว')" class="font-medium text-sm text-gray-700" />
                                <select id="movement_type" name="movement_type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    @foreach ($movementTypes as $value => $label)
                                        <option value="{{ $value }}" {{ $stockMovement->movement_type == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('movement_type')" class="mt-2" />
                            </div>

                            <!-- วันที่ -->
                            <div>
                                <x-input-label for="movement_date" :value="__('วันที่')" class="font-medium text-sm text-gray-700" />
                                <x-text-input id="movement_date" type="date" name="movement_date" :value="old('movement_date', $stockMovement->movement_date->format('Y-m-d'))" required class="mt-1 block w-full text-sm" />
                                <x-input-error :messages="$errors->get('movement_date')" class="mt-2" />
                            </div>

                            <!-- สินค้า -->
                            <div>
                                <x-input-label for="product_id" :value="__('สินค้า')" class="font-medium text-sm text-gray-700" />
                                <select id="product_id" name="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="">กรุณาเลือกสินค้า</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}" {{ $stockMovement->product_id == $product->id ? 'selected' : '' }} data-stock="{{ $product->stock_quantity }}" data-unit="{{ $product->unit_id }}">
                                            <span class="font-mono">{{ $product->code }}</span> - {{ $product->name }} (คงเหลือ: {{ number_format($product->stock_quantity, 2) }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('product_id')" class="mt-2" />
                            </div>

                            <!-- จำนวน -->
                            <div>
                                <x-input-label for="quantity" :value="__('จำนวน')" class="font-medium text-sm text-gray-700" />
                                <x-text-input id="quantity" type="number" step="0.01" min="0.01" name="quantity" :value="old('quantity', abs($stockMovement->quantity))" required class="mt-1 block w-full text-sm" />
                                <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                            </div>

                            <!-- หน่วยนับ -->
                            <div>
                                <x-input-label for="unit_id" :value="__('หน่วยนับ')" class="font-medium text-sm text-gray-700" />
                                <select id="unit_id" name="unit_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                                    <option value="">กรุณาเลือกหน่วยนับ</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}" {{ $stockMovement->unit_id == $unit->id ? 'selected' : '' }}>
                                            {{ $unit->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('unit_id')" class="mt-2" />
                            </div>

                            <!-- ประเภทอ้างอิง -->
                            <div>
                                <x-input-label for="reference_type" :value="__('ประเภทอ้างอิง (ถ้ามี)')" class="font-medium text-sm text-gray-700" />
                                <x-text-input id="reference_type" type="text" name="reference_type" :value="old('reference_type', $stockMovement->reference_type)" class="mt-1 block w-full text-sm" />
                                <x-input-error :messages="$errors->get('reference_type')" class="mt-2" />
                            </div>

                            <!-- รหัสอ้างอิง -->
                            <div>
                                <x-input-label for="reference_id" :value="__('รหัสอ้างอิง (ถ้ามี)')" class="font-medium text-sm text-gray-700" />
                                <x-text-input id="reference_id" type="text" name="reference_id" :value="old('reference_id', $stockMovement->reference_id)" class="mt-1 block w-full text-sm" />
                                <x-input-error :messages="$errors->get('reference_id')" class="mt-2" />
                            </div>

                            <!-- หมายเหตุ -->
                            <div class="md:col-span-2">
                                <x-input-label for="note" :value="__('หมายเหตุ')" class="font-medium text-sm text-gray-700" />
                                <textarea id="note" name="note" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">{{ old('note', $stockMovement->note) }}</textarea>
                                <x-input-error :messages="$errors->get('note')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                                {{ __('ยกเลิก') }}
                            </a>
                            <x-primary-button>
                                {{ __('อัปเดต') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const movementType = document.getElementById('movement_type');
            const quantityInput = document.getElementById('quantity');
            const productSelect = document.getElementById('product_id');
            const unitSelect = document.getElementById('unit_id');

            // เปลี่ยน label ตามประเภทการเคลื่อนไหว
            movementType.addEventListener('change', function() {
                const selectedType = this.value;
                if (selectedType === 'in') {
                    document.querySelector('label[for="quantity"]').textContent = 'จำนวนที่รับเข้า';
                } else if (selectedType === 'out') {
                    document.querySelector('label[for="quantity"]').textContent = 'จำนวนที่เบิกออก';
                } else {
                    document.querySelector('label[for="quantity"]').textContent = 'จำนวนที่ปรับเป็น';
                }
            });
            
            // ตั้งค่าหน่วยนับอัตโนมัติเมื่อเลือกสินค้า
            productSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption) {
                    const unitId = selectedOption.dataset.unit;
                    if (unitId) {
                        unitSelect.value = unitId;
                    }
                }
            });

            // ทริกเกอร์การเปลี่ยนแปลงเริ่มต้น
            movementType.dispatchEvent(new Event('change'));
        });
    </script>
</x-app-layout>
