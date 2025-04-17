<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('แก้ไขสินค้า/บริการ') }}
            </h2>
            <div>
                <a href="{{ route('products.show', $product) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    {{ __('กลับไปหน้ารายละเอียด') }}
                </a>
            </div>
        </div>
        <nav class="flex mt-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('products.index') }}" class="text-gray-700 hover:text-gray-900 inline-flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        รายการสินค้า
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('products.show', $product) }}" class="ml-1 md:ml-2 text-gray-700 hover:text-gray-900">{{ $product->name }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 md:ml-2 text-gray-500">แก้ไข</span>
                    </div>
                </li>
            </ol>
        </nav>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
                    <ul class="mt-1 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- ส่วนข้อมูลหลักและรูปภาพ -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- รูปภาพสินค้า -->
                            <div class="md:col-span-1">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold mb-4">รูปภาพสินค้า</h3>
                                    
                                    <div class="mb-4 text-center">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="mx-auto h-40 object-cover mb-2">
                                            <div class="text-sm text-gray-500 mb-2">รูปภาพปัจจุบัน</div>
                                        @else
                                            <div class="h-40 w-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded-lg mx-auto mb-2">
                                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                            <div class="text-sm text-gray-500 mb-2">ไม่มีรูปภาพ</div>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">เปลี่ยนรูปภาพ</label>
                                        <input type="file" name="image" id="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                        <p class="text-xs text-gray-500 mt-1">ขนาดไฟล์สูงสุด: 1MB. รองรับ: jpg, png, gif</p>
                                    </div>
                                </div>

                                <div class="border dark:border-gray-700 rounded-lg p-4 mt-4">
                                    <h3 class="text-lg font-semibold mb-4">ประเภทและสถานะ</h3>
                                    
                                    <div class="flex flex-col space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ประเภท</label>
                                            <div class="flex items-center space-x-4">
                                                <div class="flex items-center">
                                                    <input type="radio" name="is_service" id="type_product" value="0" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ $product->is_service ? '' : 'checked' }}>
                                                    <label for="type_product" class="ml-2 text-sm text-gray-700 dark:text-gray-300">สินค้า</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="radio" name="is_service" id="type_service" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ $product->is_service ? 'checked' : '' }}>
                                                    <label for="type_service" class="ml-2 text-sm text-gray-700 dark:text-gray-300">บริการ</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                            <div class="flex items-center space-x-4">
                                                <div class="flex items-center">
                                                    <input type="radio" name="is_active" id="status_active" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ $product->is_active ? 'checked' : '' }}>
                                                    <label for="status_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">ใช้งาน</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="radio" name="is_active" id="status_inactive" value="0" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ $product->is_active ? '' : 'checked' }}>
                                                    <label for="status_inactive" class="ml-2 text-sm text-gray-700 dark:text-gray-300">ไม่ใช้งาน</label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ความโดดเด่น</label>
                                            <div class="space-y-2">
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="is_featured" id="is_featured" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ $product->is_featured ? 'checked' : '' }}>
                                                    <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">แนะนำ</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="is_bestseller" id="is_bestseller" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ $product->is_bestseller ? 'checked' : '' }}>
                                                    <label for="is_bestseller" class="ml-2 text-sm text-gray-700 dark:text-gray-300">ขายดี</label>
                                                </div>
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="is_new" id="is_new" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500" {{ $product->is_new ? 'checked' : '' }}>
                                                    <label for="is_new" class="ml-2 text-sm text-gray-700 dark:text-gray-300">สินค้าใหม่</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- ข้อมูลสินค้า -->
                            <div class="md:col-span-2">
                                <div class="border dark:border-gray-700 rounded-lg p-4">
                                    <h3 class="text-lg font-semibold mb-4">ข้อมูลสินค้า</h3>
                                    
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อสินค้า <span class="text-red-500">*</span></label>
                                            <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="code" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสสินค้า</label>
                                                <input type="text" name="code" id="code" value="{{ old('code', $product->code) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                            </div>
                                            
                                            <div>
                                                <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">บาร์โค้ด</label>
                                                <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $product->barcode) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                            </div>
                                            
                                            <div>
                                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หมวดหมู่ <span class="text-red-500">*</span></label>
                                                <select name="category_id" id="category_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                                    <option value="">เลือกหมวดหมู่</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            
                                            <div>
                                                <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หน่วยนับ <span class="text-red-500">*</span></label>
                                                <select name="unit_id" id="unit_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                                    <option value="">เลือกหน่วยนับ</option>
                                                    @foreach($units as $unit)
                                                        <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รายละเอียด</label>
                                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">{{ old('description', $product->description) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- ราคาและสต็อก -->
                                <div class="border dark:border-gray-700 rounded-lg p-4 mt-4">
                                    <h3 class="text-lg font-semibold mb-4">ราคาและสต็อก</h3>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ราคาขาย <span class="text-red-500">*</span></label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" name="price" id="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-12 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <span class="text-gray-500">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ต้นทุน</label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" name="cost" id="cost" value="{{ old('cost', $product->cost) }}" step="0.01" min="0" class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-12 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <span class="text-gray-500">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="list_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ราคาแนะนำ</label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" name="list_price" id="list_price" value="{{ old('list_price', $product->list_price) }}" step="0.01" min="0" class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-12 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <span class="text-gray-500">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="wholesale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ราคาขายส่ง</label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" name="wholesale_price" id="wholesale_price" value="{{ old('wholesale_price', $product->wholesale_price) }}" step="0.01" min="0" class="block w-full border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 pr-12 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                    <span class="text-gray-500">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="product-stock" id="stockFields" style="{{ $product->is_service ? 'display:none;' : '' }}">
                                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">จำนวนในคลัง</label>
                                            <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                        </div>
                                        
                                        <div class="product-stock" id="minStockFields" style="{{ $product->is_service ? 'display:none;' : '' }}">
                                            <label for="min_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">จำนวนขั้นต่ำ</label>
                                            <input type="number" name="min_stock" id="min_stock" value="{{ old('min_stock', $product->min_stock) }}" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                        </div>
                                        
                                        <div class="product-stock" id="locationFields" style="{{ $product->is_service ? 'display:none;' : '' }}">
                                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ตำแหน่งในคลัง</label>
                                            <input type="text" name="location" id="location" value="{{ old('location', $product->location) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ส่วนข้อมูลขนาดและน้ำหนัก - เพิ่ม highlight และคำแนะนำเพื่อให้สังเกตเห็นง่าย -->
                <div class="product-stock bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6" id="dimensionBlock" style="{{ $product->is_service ? 'display:none;' : '' }}">
                    <div class="p-6 text-gray-900 dark:text-gray-100 border-2 border-blue-200 dark:border-blue-800 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2 text-blue-700 dark:text-blue-300">ข้อมูลขนาดและน้ำหนัก</h3>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">กรุณากรอกข้อมูลขนาดและน้ำหนักเพื่อแสดงรายละเอียดสินค้าที่สมบูรณ์</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">น้ำหนัก</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="number" name="weight" id="weight" value="{{ old('weight', $product->weight) }}" step="0.01" min="0" class="block w-full border-r-0 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 0.5">
                                    <select name="weight_unit" class="border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="kg" {{ old('weight_unit', $product->weight_unit) == 'kg' ? 'selected' : '' }}>กก.</option>
                                        <option value="g" {{ old('weight_unit', $product->weight_unit) == 'g' ? 'selected' : '' }}>กรัม</option>
                                        <option value="lb" {{ old('weight_unit', $product->weight_unit) == 'lb' ? 'selected' : '' }}>ปอนด์</option>
                                    </select>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">น้ำหนักสินค้าสุทธิ ไม่รวมบรรจุภัณฑ์</p>
                            </div>
                            
                            <div>
                                <label for="length" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ความยาว</label>
                                <input type="number" name="length" id="length" value="{{ old('length', $product->length) }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 30">
                            </div>
                            
                            <div>
                                <label for="width" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ความกว้าง</label>
                                <input type="number" name="width" id="width" value="{{ old('width', $product->width) }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 20">
                            </div>
                            
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ความสูง</label>
                                <input type="number" name="height" id="height" value="{{ old('height', $product->height) }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 10">
                            </div>
                            
                            <div>
                                <label for="dimension_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หน่วย (ขนาด)</label>
                                <select name="dimension_unit" id="dimension_unit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                    <option value="cm" {{ old('dimension_unit', $product->dimension_unit) == 'cm' ? 'selected' : '' }}>เซนติเมตร</option>
                                    <option value="m" {{ old('dimension_unit', $product->dimension_unit) == 'm' ? 'selected' : '' }}>เมตร</option>
                                    <option value="mm" {{ old('dimension_unit', $product->dimension_unit) == 'mm' ? 'selected' : '' }}>มิลลิเมตร</option>
                                    <option value="in" {{ old('dimension_unit', $product->dimension_unit) == 'in' ? 'selected' : '' }}>นิ้ว</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- เพิ่มฟีลด์เมตาดาต้าสำหรับขนาดโดยรวม -->
                        @php
                        $metadata = is_array($product->metadata) ? $product->metadata : json_decode($product->metadata, true);
                        if (is_string($metadata)) {
                            $metadata = json_decode($metadata, true);
                        }
                        if (!is_array($metadata)) {
                            $metadata = [];
                        }
                        $sizeValue = $metadata['size'] ?? '';
                        @endphp

                        <div class="mt-4">
                            <label for="metadata_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ขนาดโดยรวม (ถ้ามี)</label>
                            <input type="text" name="metadata[size]" id="metadata_size" value="{{ old('metadata.size', $sizeValue) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 30 x 40 x 15 ซม.">
                            <p class="text-xs text-gray-500 mt-1">ใช้สำหรับอธิบายขนาดในภาพรวม เช่น "30 x 40 x 15 ซม." หรือ "ขนาด A4"</p>
                        </div>
                        
                        <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 p-2 rounded">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"></path>
                                </svg>
                                <p class="text-xs text-blue-700 dark:text-blue-300">ข้อมูลขนาดและน้ำหนักมีความสำคัญต่อการคำนวณค่าส่ง และทำให้ลูกค้าสามารถตัดสินใจซื้อได้ง่ายขึ้น</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ส่วนข้อมูลเพิ่มเติม -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">ข้อมูลเพิ่มเติม</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="warranty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">การรับประกัน</label>
                                <input type="text" name="warranty" id="warranty" value="{{ old('warranty', $product->warranty) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 1 ปี, 6 เดือน">
                            </div>
                            
                            <div>
                                <label for="condition" class="block text-sm font-medium text-gray-700 dark:text-gray-300">สภาพสินค้า</label>
                                <select name="condition" id="condition" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                    <option value="new" {{ old('condition', $product->condition) == 'new' ? 'selected' : '' }}>สินค้าใหม่</option>
                                    <option value="used" {{ old('condition', $product->condition) == 'used' ? 'selected' : '' }}>สินค้ามือสอง</option>
                                    <option value="refurbished" {{ old('condition', $product->condition) == 'refurbished' ? 'selected' : '' }}>สินค้ารีเฟอร์บิช</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">แท็ก (คั่นด้วย ,)</label>
                                <input type="text" name="tags" id="tags" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : $product->tags) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น โปรโมชั่น, สินค้าขายดี">
                            </div>
                            
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ปุ่มบันทึก -->
                <div class="flex justify-end">
                    <a href="{{ route('products.show', $product) }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition mr-2">
                        {{ __('ยกเลิก') }}
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition">
                        {{ __('บันทึกการเปลี่ยนแปลง') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeProduct = document.getElementById('type_product');
            const typeService = document.getElementById('type_service');
            const stockFields = document.querySelectorAll('.product-stock');
            
            // ฟังก์ชันสำหรับจัดการการแสดง/ซ่อนฟิลด์สต็อก
            function handleProductTypeChange() {
                if (typeService.checked) {
                    stockFields.forEach(field => field.style.display = 'none');
                } else {
                    stockFields.forEach(field => field.style.display = 'block');
                }
            }
            
            // เรียกใช้ฟังก์ชันเมื่อเปลี่ยนประเภท
            typeProduct.addEventListener('change', handleProductTypeChange);
            typeService.addEventListener('change', handleProductTypeChange);
            
            // ตรวจสอบสถานะเริ่มต้น
            handleProductTypeChange();
            
            // เช็คหากมี hash ใน URL ให้เลื่อนไปยังตำแหน่งนั้น
            if (window.location.hash) {
                const element = document.querySelector(window.location.hash);
                if (element) {
                    setTimeout(() => {
                        element.scrollIntoView({ behavior: 'smooth' });
                        element.classList.add('highlight-section');
                        setTimeout(() => element.classList.remove('highlight-section'), 2000);
                    }, 500);
                }
            }
        });
    </script>
    <style>
        .highlight-section {
            animation: highlight 2s;
        }

        @keyframes highlight {
            0% { background-color: rgba(59, 130, 246, 0.2); }
            100% { background-color: transparent; }
        }
    </style>
</x-app-layout>
