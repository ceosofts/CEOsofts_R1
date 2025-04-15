<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-extrabold text-4xl text-blue-800">
                    เพิ่มสินค้า/บริการใหม่
                </h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:border-gray-600">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    กลับ
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('error') || $errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') ?? 'กรุณาตรวจสอบข้อมูลที่กรอก' }}</span>
                    @if($errors->any())
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif

            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- ข้อมูลพื้นฐาน -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                </svg>
                                ข้อมูลพื้นฐาน
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">ประเภท <span class="text-red-600">*</span></label>
                                        <div class="mt-2 flex items-center space-x-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="type" value="product" {{ old('type', 'product') == 'product' ? 'checked' : '' }} class="form-radio text-blue-600" required>
                                                <span class="ml-2">สินค้า</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="type" value="service" {{ old('type') == 'service' ? 'checked' : '' }} class="form-radio text-blue-600">
                                                <span class="ml-2">บริการ</span>
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">รหัสสินค้า</label>
                                        <div class="mt-1 p-2 bg-gray-100 dark:bg-gray-700 rounded-md text-gray-600 dark:text-gray-300 text-sm">
                                            ระบบจะสร้างรหัสสินค้าให้อัตโนมัติหลังจากบันทึก
                                        </div>
                                    </div>

                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ชื่อสินค้า/บริการ <span class="text-red-600">*</span></label>
                                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>

                                    <div>
                                        <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300">บาร์โค้ด/รหัสสินค้าภายนอก</label>
                                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>

                                    <div>
                                        <label for="product_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หมวดหมู่ <span class="text-red-600">*</span></label>
                                        <select name="product_category_id" id="product_category_id" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            <option value="">-- เลือกหมวดหมู่ --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('product_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">สถานะ <span class="text-red-600">*</span></label>
                                        <select name="status" id="status" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>ใช้งาน</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รายละเอียดสินค้า</label>
                                        <textarea name="description" id="description" rows="3" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- รูปภาพและราคา -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                รูปภาพและราคา
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">รูปภาพสินค้า</label>
                                        <input type="file" name="image" id="image" accept="image/*" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        <p class="mt-1 text-xs text-gray-500">รองรับไฟล์ JPG, PNG ขนาดไม่เกิน 1MB</p>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ราคาขาย (บาท) <span class="text-red-600">*</span></label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price', '0.00') }}" required class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="cost" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ต้นทุน (บาท)</label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" step="0.01" min="0" name="cost" id="cost" value="{{ old('cost', '0.00') }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- เพิ่มฟิลด์ราคาเพิ่มเติม -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="list_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ราคาแนะนำ (บาท)</label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" step="0.01" min="0" name="list_price" id="list_price" value="{{ old('list_price') }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="wholesale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ราคาขายส่ง (บาท)</label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" step="0.01" min="0" name="wholesale_price" id="wholesale_price" value="{{ old('wholesale_price') }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-3 gap-4">
                                        <div>
                                            <label for="special_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ราคาพิเศษ (บาท)</label>
                                            <div class="mt-1 relative rounded-md shadow-sm">
                                                <input type="number" step="0.01" min="0" name="special_price" id="special_price" value="{{ old('special_price') }}" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-3 pr-12 sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500 sm:text-sm">บาท</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label for="special_price_start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันที่เริ่มราคาพิเศษ</label>
                                            <input type="date" name="special_price_start_date" id="special_price_start_date" value="{{ old('special_price_start_date') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        </div>
                                        
                                        <div>
                                            <label for="special_price_end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันที่สิ้นสุดราคาพิเศษ</label>
                                            <input type="date" name="special_price_end_date" id="special_price_end_date" value="{{ old('special_price_end_date') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label for="unit_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หน่วยนับ <span class="text-red-600">*</span></label>
                                        <select name="unit_id" id="unit_id" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            <option value="">-- เลือกหน่วยนับ --</option>
                                            @foreach($units as $unit)
                                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- เพิ่มฟิลด์การตั้งค่าสินค้าพิเศษ -->
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                        <h4 class="text-sm font-medium mb-2 text-indigo-600 dark:text-indigo-400">การตั้งค่าสินค้าพิเศษ</h4>
                                        <div class="grid grid-cols-3 gap-4">
                                            <div class="flex items-center">
                                                <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <label for="is_featured" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">สินค้าแนะนำ</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" name="is_bestseller" id="is_bestseller" value="1" {{ old('is_bestseller') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <label for="is_bestseller" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">สินค้าขายดี</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="checkbox" name="is_new" id="is_new" value="1" {{ old('is_new') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <label for="is_new" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">สินค้าใหม่</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="stock-fields">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">จำนวนคงเหลือ</label>
                                                <input type="number" min="0" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', '0') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            </div>
                                            
                                            <div>
                                                <label for="min_stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">จำนวนขั้นต่ำ</label>
                                                <input type="number" min="0" name="min_stock" id="min_stock" value="{{ old('min_stock', '0') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            </div>
                                        </div>

                                        <!-- เพิ่มฟิลด์สต็อคเพิ่มเติม -->
                                        <div class="mt-4">
                                            <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ตำแหน่งในคลัง</label>
                                            <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="เช่น ชั้น A3, แถว 5" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        </div>

                                        <div class="grid grid-cols-2 gap-4 mt-4">
                                            <div>
                                                <label for="inventory_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">สถานะคลัง</label>
                                                <select name="inventory_status" id="inventory_status" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                    <option value="in_stock" {{ old('inventory_status') == 'in_stock' ? 'selected' : '' }}>มีสินค้า</option>
                                                    <option value="out_of_stock" {{ old('inventory_status') == 'out_of_stock' ? 'selected' : '' }}>สินค้าหมด</option>
                                                    <option value="low_stock" {{ old('inventory_status') == 'low_stock' ? 'selected' : '' }}>สินค้าใกล้หมด</option>
                                                </select>
                                            </div>
                                            
                                            <div class="flex items-center mt-6">
                                                <input type="checkbox" name="allow_backorder" id="allow_backorder" value="1" {{ old('allow_backorder') ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                <label for="allow_backorder" class="ml-2 block text-sm text-gray-900 dark:text-gray-100">อนุญาตให้สั่งเกินสต็อค</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- คุณสมบัติสินค้า (เพิ่มเติม) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                คุณสมบัติสินค้า
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="warranty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">การรับประกัน</label>
                                        <input type="text" name="warranty" id="warranty" value="{{ old('warranty') }}" placeholder="เช่น 1 ปี, 6 เดือน" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>

                                    <div>
                                        <label for="condition" class="block text-sm font-medium text-gray-700 dark:text-gray-300">สภาพสินค้า</label>
                                        <select name="condition" id="condition" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            <option value="new" {{ old('condition', 'new') == 'new' ? 'selected' : '' }}>สินค้าใหม่</option>
                                            <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>สินค้ามือสอง</option>
                                            <option value="refurbished" {{ old('condition') == 'refurbished' ? 'selected' : '' }}>สินค้ารีเฟอร์บิช</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="available_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันที่เริ่มจำหน่าย</label>
                                        <input type="date" name="available_from" id="available_from" value="{{ old('available_from') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>

                                    <div>
                                        <label for="available_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">วันที่สิ้นสุดจำหน่าย</label>
                                        <input type="date" name="available_to" id="available_to" value="{{ old('available_to') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                    </div>
                                </div>

                                <div>
                                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">แท็ก (คั่นด้วยเครื่องหมายคอมม่า ,)</label>
                                    <input type="text" name="tags" id="tags" value="{{ old('tags') }}" placeholder="เช่น โปรโมชั่น, สินค้าขายดี" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                </div>

                                <div>
                                    <label for="tax_class" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ประเภทภาษี</label>
                                    <select name="tax_class" id="tax_class" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                        <option value="" {{ old('tax_class') == '' ? 'selected' : '' }}>ไม่ระบุ</option>
                                        <option value="standard" {{ old('tax_class') == 'standard' ? 'selected' : '' }}>ภาษีมาตรฐาน (7%)</option>
                                        <option value="reduced" {{ old('tax_class') == 'reduced' ? 'selected' : '' }}>ภาษีลดหย่อน</option>
                                        <option value="zero" {{ old('tax_class') == 'zero' ? 'selected' : '' }}>ไม่มีภาษี (0%)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- คุณลักษณะพิเศษ (Metadata) -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                คุณลักษณะพิเศษ (Metadata)
                            </h3>
                            
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-4">
                                <!-- สี -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">สีสินค้า</label>
                                        <div class="flex flex-wrap gap-2" id="color-container">
                                            <div class="flex items-center space-x-2">
                                                <input type="text" name="metadata[color][]" placeholder="เช่น Black, White, Blue" class="focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                                <button type="button" class="text-blue-500 hover:text-blue-700" onclick="addColorField()">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">เพิ่มสีหลายรายการได้โดยคลิกปุ่ม + </p>
                                    </div>

                                    <!-- สำหรับอิเล็กทรอนิกส์ -->
                                    <div id="electronics-fields">
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="metadata_os" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ระบบปฏิบัติการ (OS)</label>
                                                <input type="text" name="metadata[os]" id="metadata_os" value="{{ old('metadata.os') }}" placeholder="เช่น Android 13, iOS 16, Windows 11" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            </div>
                                            
                                            <div>
                                                <label for="metadata_warranty" class="block text-sm font-medium text-gray-700 dark:text-gray-300">การรับประกันเพิ่มเติม</label>
                                                <input type="text" name="metadata[warranty]" id="metadata_warranty" value="{{ old('metadata.warranty') }}" placeholder="เช่น 1 year international warranty" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ส่วนข้อมูลขนาดและน้ำหนัก - ปรับปรุงให้เหมือนกับหน้าแก้ไข -->
                <div class="product-stock bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6" id="dimensionBlock" style="display:none;">
                    <div class="p-6 text-gray-900 dark:text-gray-100 border-2 border-blue-200 dark:border-blue-800 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2 text-blue-700 dark:text-blue-300">ข้อมูลขนาดและน้ำหนัก</h3>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mb-4">กรุณากรอกข้อมูลขนาดและน้ำหนักเพื่อแสดงรายละเอียดสินค้าที่สมบูรณ์</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label for="weight" class="block text-sm font-medium text-gray-700 dark:text-gray-300">น้ำหนัก</label>
                                <div class="mt-1 flex rounded-md shadow-sm">
                                    <input type="number" name="weight" id="weight" value="{{ old('weight') }}" step="0.01" min="0" class="block w-full border-r-0 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 0.5">
                                    <select name="weight_unit" class="border-l-0 border-gray-300 rounded-r-md bg-gray-50 text-gray-500 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-gray-300">
                                        <option value="kg" {{ old('weight_unit') == 'kg' ? 'selected' : '' }}>กก.</option>
                                        <option value="g" {{ old('weight_unit') == 'g' ? 'selected' : '' }}>กรัม</option>
                                        <option value="lb" {{ old('weight_unit') == 'lb' ? 'selected' : '' }}>ปอนด์</option>
                                    </select>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">น้ำหนักสินค้าสุทธิ ไม่รวมบรรจุภัณฑ์</p>
                            </div>
                            
                            <div>
                                <label for="length" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ความยาว</label>
                                <input type="number" name="length" id="length" value="{{ old('length') }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 30">
                            </div>
                            
                            <div>
                                <label for="width" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ความกว้าง</label>
                                <input type="number" name="width" id="width" value="{{ old('width') }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 20">
                            </div>
                            
                            <div>
                                <label for="height" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ความสูง</label>
                                <input type="number" name="height" id="height" value="{{ old('height') }}" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 10">
                            </div>
                            
                            <div>
                                <label for="dimension_unit" class="block text-sm font-medium text-gray-700 dark:text-gray-300">หน่วย (ขนาด)</label>
                                <select name="dimension_unit" id="dimension_unit" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700">
                                    <option value="cm" {{ old('dimension_unit') == 'cm' ? 'selected' : '' }}>เซนติเมตร</option>
                                    <option value="m" {{ old('dimension_unit') == 'm' ? 'selected' : '' }}>เมตร</option>
                                    <option value="mm" {{ old('dimension_unit') == 'mm' ? 'selected' : '' }}>มิลลิเมตร</option>
                                    <option value="in" {{ old('dimension_unit') == 'in' ? 'selected' : '' }}>นิ้ว</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- เพิ่มฟีลด์เมตาดาต้าสำหรับขนาดโดยรวม -->
                        <div class="mt-4">
                            <label for="metadata_size" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ขนาดโดยรวม (ถ้ามี)</label>
                            <input type="text" name="metadata[size]" id="metadata_size" value="{{ old('metadata.size') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 dark:text-white dark:bg-gray-800 dark:border-gray-700" placeholder="เช่น 30 x 40 x 15 ซม.">
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

                <!-- ปุ่มบันทึก -->
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:border-gray-600">
                        ยกเลิก
                    </a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        บันทึกข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // เพิ่มฟิลด์เพิ่มเติม
            document.getElementById('add-field').addEventListener('click', function() {
                const template = document.getElementById('additional-field-template');
                const newField = template.cloneNode(true);
                newField.classList.remove('hidden');
                newField.removeAttribute('id');
                
                const additionalFields = document.getElementById('additional-fields');
                additionalFields.insertBefore(newField, template);
                
                // เพิ่ม event listener สำหรับปุ่มลบ
                newField.querySelector('.remove-field').addEventListener('click', function() {
                    newField.remove();
                });
            });
            
            // เพิ่ม event listener สำหรับปุ่มลบที่มีอยู่แล้ว
            document.querySelectorAll('.remove-field').forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('.additional-field-row');
                    if (row && !row.classList.contains('hidden')) {
                        row.remove();
                    }
                });
            });
            
            // แสดง/ซ่อนฟิลด์สำหรับสินค้า/บริการ
            const productTypeRadios = document.querySelectorAll('input[name="type"]');
            const stockFields = document.getElementById('stock-fields');
            
            function toggleStockFields() {
                const selectedType = document.querySelector('input[name="type"]:checked').value;
                if (selectedType === 'service') {
                    stockFields.style.display = 'none';
                } else {
                    stockFields.style.display = 'block';
                }
            }
            
            // เรียกใช้ฟังก์ชันครั้งแรกเพื่อตั้งค่าเริ่มต้น
            toggleStockFields();
            
            // เพิ่ม event listeners สำหรับการเปลี่ยนประเภท
            productTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleStockFields);
            });

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

        // เพิ่มฟิลด์สี
        window.addColorField = function() {
            const container = document.getElementById('color-container');
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2';
            div.innerHTML = `
                <input type="text" name="metadata[color][]" placeholder="เช่น Red, Green" class="focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md">
                <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.remove()">
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            container.appendChild(div);
        }
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
