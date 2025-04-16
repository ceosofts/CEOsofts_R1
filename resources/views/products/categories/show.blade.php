<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-3xl text-blue-800">
                {{ __('รายละเอียดหมวดหมู่สินค้า') }}
            </h2>
            <div>
                <a href="{{ route('product-categories.edit', $productCategory) }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-500 border border-transparent rounded-md shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 mr-2">
                   <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    แก้ไข
                </a>
                <a href="{{ route('product-categories.index') }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    กลับ
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
            @endif
            
            <!-- แสดงข้อมูลหลักของหมวดหมู่ -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลหมวดหมู่สินค้า</h3>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">รหัสหมวดหมู่:</p>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $productCategory->formatted_code }}</p>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">หมวดหมู่:</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $productCategory->code ?: '-' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">ชื่อหมวดหมู่:</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $productCategory->name }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">รายละเอียด:</p>
                                <p class="mt-1 text-sm text-gray-900">{{ $productCategory->description ?: 'ไม่มีข้อมูล' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">สถานะ:</p>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $productCategory->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $productCategory->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">ข้อมูลเพิ่มเติม</h3>
                            
                            <!-- แสดงบริษัทที่อ้างอิง - แสดงชื่อบริษัทชัดเจนขึ้น -->
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">บริษัท:</p>
                                <p class="mt-1 text-sm text-gray-900 font-medium">
                                    @if($productCategory->company_id && $productCategory->company)
                                        {{ $productCategory->company->company_name ?? $productCategory->company->name ?? 'ชื่อบริษัทไม่พบ' }}
                                        <!-- <span class="text-xs text-gray-500">(รหัส: {{ $productCategory->company_id }})</span> -->
                                    @else
                                        ไม่ระบุบริษัท
                                    @endif
                                </p>
                            </div>
                            
                            <!-- แสดงหมวดหมู่แม่ ถ้ามี -->
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">หมวดหมู่หลัก:</p>
                                <p class="mt-1 text-sm text-gray-900">
                                    @if($productCategory->parent)
                                        <a href="{{ route('product-categories.show', $productCategory->parent->id) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $productCategory->parent->name }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            
                            <!-- ข้อมูล metadata -->
                            @if($productCategory->metadata)
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">Metadata:</p>
                                <div class="mt-1 p-2 bg-gray-50 rounded text-xs font-mono overflow-x-auto">
                                    @php
                                        $metadata = is_string($productCategory->metadata) ? json_decode($productCategory->metadata) : $productCategory->metadata;
                                    @endphp
                                    <pre>{{ json_encode($metadata, JSON_PRETTY_PRINT) }}</pre>
                                </div>
                            </div>
                            @endif
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">วันที่สร้าง:</p>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $productCategory->created_at ? $productCategory->created_at->format('d/m/Y H:i:s') : 'ไม่มีข้อมูล' }}
                                </p>
                            </div>
                            
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500">วันที่แก้ไขล่าสุด:</p>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $productCategory->updated_at ? $productCategory->updated_at->format('d/m/Y H:i:s') : 'ไม่มีข้อมูล' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- แสดงหมวดหมู่ย่อย (ถ้ามี) -->
            @if($productCategory->children && $productCategory->children->count() > 0)
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">หมวดหมู่ย่อย ({{ $productCategory->children->count() }} รายการ)</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="py-2 px-4 border-b text-left">รหัส</th>
                                    <th class="py-2 px-4 border-b text-left">ชื่อหมวดหมู่</th>
                                    <th class="py-2 px-4 border-b text-center">สถานะ</th>
                                    <th class="py-2 px-4 border-b text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($productCategory->children as $child)
                                    <tr class="hover:bg-gray-50 border-b">
                                        <td class="py-2 px-4">{{ $child->formatted_code }}</td>
                                        <td class="py-2 px-4">{{ $child->name }}</td>
                                        <td class="py-2 px-4 text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $child->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $child->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 text-center">
                                            <a href="{{ route('product-categories.show', $child->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">ดูรายละเอียด</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- แสดงรายการสินค้าในหมวดหมู่นี้ (ถ้ามี) -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        สินค้าในหมวดหมู่นี้และหมวดหมู่ย่อย ({{ $products->count() }} รายการ จาก {{ $products->pluck('company_id')->unique()->count() }} บริษัท)
                    </h3>
                    
                    @if($products && $products->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-100">
                                        <th class="py-2 px-4 border-b text-left">รหัสสินค้า</th>
                                        <th class="py-2 px-4 border-b text-left">ชื่อสินค้า</th>
                                        <th class="py-2 px-4 border-b text-left">บริษัท</th>
                                        <th class="py-2 px-4 border-b text-left">หมวดหมู่</th>
                                        <th class="py-2 px-4 border-b text-right">ราคา</th>
                                        <th class="py-2 px-4 border-b text-center">สถานะ</th>
                                        <th class="py-2 px-4 border-b text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $currentCompanyId = null;
                                    @endphp
                                    
                                    @foreach($products as $product)
                                        @if($currentCompanyId !== $product->company_id)
                                            @php 
                                                $currentCompanyId = $product->company_id;
                                                $companyName = $product->company ? ($product->company->company_name ?? $product->company->name ?? 'ไม่ระบุชื่อ') : 'ไม่ระบุบริษัท';
                                            @endphp
                                            <tr class="bg-blue-50">
                                                <td colspan="7" class="py-1 px-4 font-medium text-blue-800">
                                                    {{ $companyName }}
                                                    @if($product->company_id)
                                                        <span class="text-xs text-gray-500">(รหัส: {{ $product->company_id }})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        
                                        <tr class="hover:bg-gray-50 border-b">
                                            <td class="py-2 px-4">{{ $product->code }}</td>
                                            <td class="py-2 px-4">{{ $product->name }}</td>
                                            <td class="py-2 px-4">
                                                @if($product->company_id && $product->company)
                                                    {{ $product->company->company_name ?? $product->company->name ?? 'ชื่อบริษัทไม่พบ' }}
                                                @else
                                                    ไม่ระบุบริษัท
                                                @endif
                                            </td>
                                            <td class="py-2 px-4">
                                                @if($product->product_category_id != $productCategory->id && $product->productCategory)
                                                    <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 rounded">
                                                        {{ $product->productCategory->name ?? 'หมวดหมู่ย่อย' }}
                                                    </span>
                                                @else
                                                    <span class="text-xs">หมวดหมู่หลัก</span>
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 text-right">{{ number_format($product->selling_price, 2) }}</td>
                                            <td class="py-2 px-4 text-center">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $product->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 text-center">
                                                @if(Route::has('products.show'))
                                                    <a href="{{ route('products.show', $product->id) }}" class="text-indigo-600 hover:text-indigo-900">ดูรายละเอียด</a>
                                                @else
                                                    <span class="text-gray-400">ดูรายละเอียด</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">ไม่พบสินค้าในหมวดหมู่นี้</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
