<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('รายละเอียดสินค้า/บริการ') }}
            </h2>
            <div>
                <a href="{{ route('products.edit', $product) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 transition mr-2">
                    {{ __('แก้ไขข้อมูล') }}
                </a>
                <a href="{{ route('products.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition">
                    {{ __('กลับไปรายการสินค้า') }}
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
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-gray-500 ml-1 md:ml-2">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(app()->environment('local'))
            <div id="debugInfo" class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg" style="display: none;">
                <div class="flex justify-between">
                    <h3 class="font-bold">Debug Information:</h3>
                    <button onclick="document.getElementById('debugInfo').style.display='none';" class="text-yellow-800 hover:text-yellow-900">
                        <span>×</span>
                    </button>
                </div>
                <p>Product ID: {{ $product->id ?? 'ไม่มีข้อมูล' }}</p>
                <p>Product Code: {{ $product->code ?? 'ไม่มีข้อมูล' }}</p>
                <p>UUID: {{ $product->uuid ?? 'ไม่มีข้อมูล' }}</p>
            </div>

            <div class="mb-4">
                <button onclick="document.getElementById('debugInfo').style.display='block';" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    แสดงข้อมูล Debug
                </button>
            </div>
            @endif

            <!-- ส่วนแสดงรูปภาพและข้อมูลทั่วไป -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- รูปภาพสินค้า -->
                        <div class="md:col-span-1">
                            <div class="border dark:border-gray-700 rounded-lg p-4 text-center">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="mx-auto h-40 object-cover">
                                @else
                                    <div class="h-40 w-40 bg-gray-200 dark:bg-gray-700 flex items-center justify-center rounded-lg mx-auto">
                                        <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="mt-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                                        {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $product->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                    </span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                                        {{ $product->is_service ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $product->is_service ? 'บริการ' : 'สินค้า' }}
                                    </span>
                                </div>
                                <div class="mt-4">
                                    <h3 class="font-medium text-lg">{{ $product->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->code }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ข้อมูลทั่วไป -->
                        <div class="md:col-span-2">
                            <div class="border dark:border-gray-700 rounded-lg p-4">
                                <h3 class="text-lg font-semibold mb-4">ข้อมูลทั่วไป</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">รหัสสินค้า</p>
                                        <p class="font-medium">{{ $product->code ?: 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">บาร์โค้ด</p>
                                        <p class="font-medium">{{ $product->barcode ?: 'ไม่ระบุ' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">หมวดหมู่</p>
                                        <p class="font-medium">{{ $product->category->name ?? 'ไม่ระบุ' }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">หน่วยนับ</p>
                                        <p class="font-medium">{{ $product->unitRelation->name ?? $product->unit ?? 'ไม่ระบุ' }}</p>
                                    </div>

                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">ราคาขาย</p>
                                        <p class="font-medium text-blue-700 dark:text-blue-400">{{ number_format($product->price, 2) }} บาท</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">ต้นทุน</p>
                                        <p class="font-medium">{{ number_format($product->cost, 2) }} บาท</p>
                                    </div>
                                    
                                    @if(!$product->is_service)
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">จำนวนในคลัง</p>
                                        <p class="font-medium {{ $product->stock_quantity <= $product->min_stock ? 'text-red-600 dark:text-red-400' : '' }}">
                                            {{ number_format($product->stock_quantity) }} {{ $product->unitRelation->name ?? $product->unit ?? '' }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">สต๊อกขั้นต่ำ</p>
                                        <p class="font-medium">{{ number_format($product->min_stock) }} {{ $product->unitRelation->name ?? $product->unit ?? '' }}</p>
                                    </div>
                                    @endif

                                    <!-- เพิ่มการแสดงข้อมูลการรับประกันถ้ามี -->
                                    @php
                                        $metadata = is_array($product->metadata) ? $product->metadata : json_decode($product->metadata, true);
                                        if (is_string($metadata)) {
                                            $metadata = json_decode($metadata, true);
                                        }
                                    @endphp

                                    @if(isset($metadata['warranty']))
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">การรับประกัน</p>
                                        <p class="font-medium">{{ $metadata['warranty'] }}</p>
                                    </div>
                                    @endif

                                    @if($product->warranty)
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">การรับประกัน</p>
                                        <p class="font-medium">{{ $product->warranty }}</p>
                                    </div>
                                    @endif

                                    @if($product->condition && $product->condition != 'new')
                                    <div>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">สภาพสินค้า</p>
                                        <p class="font-medium">
                                            @if($product->condition == 'used')
                                                สินค้ามือสอง
                                            @elseif($product->condition == 'refurbished')
                                                สินค้ารีเฟอร์บิช
                                            @else
                                                {{ $product->condition }}
                                            @endif
                                        </p>
                                    </div>
                                    @endif
                                </div>

                                @if(!$product->is_service && $product->location)
                                <div class="mt-4">
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">ตำแหน่งในคลัง</p>
                                    <p class="font-medium">{{ $product->location }}</p>
                                </div>
                                @endif
                                
                                <!-- เพิ่มการแสดงสีถ้ามีในข้อมูล metadata -->
                                @if(isset($metadata['color']) && is_array($metadata['color']))
                                <div class="mt-4">
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">สีที่มีจำหน่าย</p>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        @foreach($metadata['color'] as $color)
                                            <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                {{ $color }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ข้อมูลขนาดและน้ำหนัก - แก้ไขปัญหาการแสดงผล -->
            @if(!$product->is_service)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">ข้อมูลขนาดและน้ำหนัก</h3>
                        <!-- เพิ่มปุ่มแก้ไขข้อมูลขนาดและน้ำหนัก -->
                        <a href="{{ route('products.edit', $product) }}#dimensionBlock" class="px-2 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                            แก้ไขข้อมูลขนาด
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">น้ำหนัก</p>
                            <p class="font-medium">
                                @if($product->weight)
                                    {{ number_format($product->weight, 2) }} {{ $product->weight_unit ?? 'kg' }}
                                @else
                                    <span class="text-gray-400">ไม่มีข้อมูล</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ความยาว</p>
                            <p class="font-medium">
                                @if($product->length)
                                    {{ number_format($product->length, 2) }} {{ $product->dimension_unit ?? 'cm' }}
                                @else
                                    <span class="text-gray-400">ไม่มีข้อมูล</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ความกว้าง</p>
                            <p class="font-medium">
                                @if($product->width)
                                    {{ number_format($product->width, 2) }} {{ $product->dimension_unit ?? 'cm' }}
                                @else
                                    <span class="text-gray-400">ไม่มีข้อมูล</span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ความสูง</p>
                            <p class="font-medium">
                                @if($product->height)
                                    {{ number_format($product->height, 2) }} {{ $product->dimension_unit ?? 'cm' }}
                                @else
                                    <span class="text-gray-400">ไม่มีข้อมูล</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <!-- เพิ่มแสดงข้อมูลจากเมตาดาต้าถ้าเกี่ยวข้องกับขนาด -->
                    @php
                        if (!isset($metadata)) {
                            $metadata = is_array($product->metadata) ? $product->metadata : json_decode($product->metadata, true);
                            if (is_string($metadata)) {
                                $metadata = json_decode($metadata, true);
                            }
                        }
                    @endphp
                    
                    @if(isset($metadata['size']))
                    <div class="mt-4">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">ขนาดโดยรวม</p>
                        <p class="font-medium">{{ $metadata['size'] }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- รายละเอียดและคำอธิบาย -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">รายละเอียดสินค้า</h3>

                    @if($product->description)
                    <div class="mb-6">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">คำอธิบาย</p>
                        <p class="font-medium mt-1">{{ $product->description }}</p>
                    </div>
                    @endif

                    <!-- ข้อมูลราคา - เพิ่มการแสดงข้อมูลราคาเพิ่มเติม -->
                    <h4 class="text-md font-semibold mb-2 text-indigo-600 dark:text-indigo-400">ข้อมูลราคา</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ราคาขายปกติ</p>
                            <p class="font-medium">{{ number_format($product->price, 2) }} บาท</p>
                        </div>
                        
                        @if($product->cost)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ต้นทุน</p>
                            <p class="font-medium">{{ number_format($product->cost, 2) }} บาท</p>
                        </div>
                        @endif

                        @if($product->list_price)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ราคาแนะนำ</p>
                            <p class="font-medium">{{ number_format($product->list_price, 2) }} บาท</p>
                        </div>
                        @endif

                        @if($product->wholesale_price)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ราคาขายส่ง</p>
                            <p class="font-medium">{{ number_format($product->wholesale_price, 2) }} บาท</p>
                        </div>
                        @endif
                        
                        @if($product->special_price)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ราคาพิเศษ</p>
                            <p class="font-medium text-red-600 dark:text-red-400">{{ number_format($product->special_price, 2) }} บาท</p>
                        </div>
                        @endif

                        @if($product->tax_class)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ประเภทภาษี</p>
                            <p class="font-medium">{{ $product->tax_class }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <!-- ระยะเวลาราคาพิเศษ -->
                    @if($product->special_price_start_date || $product->special_price_end_date)
                    <div class="mb-4">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">ระยะเวลาราคาพิเศษ</p>
                        <p class="font-medium">
                            @if($product->special_price_start_date && $product->special_price_end_date)
                                {{ \Carbon\Carbon::parse($product->special_price_start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($product->special_price_end_date)->format('d/m/Y') }}
                            @elseif($product->special_price_start_date)
                                เริ่มตั้งแต่ {{ \Carbon\Carbon::parse($product->special_price_start_date)->format('d/m/Y') }}
                            @elseif($product->special_price_end_date)
                                ถึงวันที่ {{ \Carbon\Carbon::parse($product->special_price_end_date)->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <!-- ระยะเวลาจำหน่าย -->
                    @if($product->available_from || $product->available_to)
                    <div class="mb-4">
                        <p class="text-gray-500 dark:text-gray-400 text-sm">ระยะเวลาจำหน่าย</p>
                        <p class="font-medium">
                            @if($product->available_from && $product->available_to)
                                {{ \Carbon\Carbon::parse($product->available_from)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($product->available_to)->format('d/m/Y') }}
                            @elseif($product->available_from)
                                เริ่มจำหน่าย {{ \Carbon\Carbon::parse($product->available_from)->format('d/m/Y') }}
                            @elseif($product->available_to)
                                จำหน่ายถึง {{ \Carbon\Carbon::parse($product->available_to)->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <!-- แท็กและป้ายกำกับ -->
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($product->is_featured)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-amber-100 text-amber-800">
                                แนะนำ
                            </span>
                        @endif
                        @if($product->is_bestseller)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-orange-100 text-orange-800">
                                ขายดี
                            </span>
                        @endif
                        @if($product->is_new)
                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-blue-100 text-blue-800">
                                สินค้าใหม่
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- ข้อมูลสต็อคและคลังสินค้า - เพิ่มการแสดงข้อมูลสต็อคเพิ่มเติม -->
            @if(!$product->is_service)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลสต็อคและคลังสินค้า</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">จำนวนในคลังปัจจุบัน</p>
                            <p class="font-medium {{ $product->stock_quantity <= $product->min_stock ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ number_format($product->stock_quantity) }} {{ $product->unitRelation->name ?? $product->unit ?? '' }}
                            </p>
                        </div>
                        
                        @if($product->current_stock != $product->stock_quantity)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">สต็อคปัจจุบันในระบบ</p>
                            <p class="font-medium">{{ number_format($product->current_stock) }} {{ $product->unitRelation->name ?? $product->unit ?? '' }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">จำนวนขั้นต่ำ</p>
                            <p class="font-medium">{{ number_format($product->min_stock) }} {{ $product->unitRelation->name ?? $product->unit ?? '' }}</p>
                        </div>
                        
                        @if($product->max_stock)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">จำนวนสูงสุด</p>
                            <p class="font-medium">{{ number_format($product->max_stock) }} {{ $product->unitRelation->name ?? $product->unit ?? '' }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">สถานะคลัง</p>
                            <p class="font-medium">
                                @if($product->inventory_status == 'in_stock')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-green-100 text-green-800">
                                        มีสินค้า
                                    </span>
                                @elseif($product->inventory_status == 'out_of_stock')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-red-100 text-red-800">
                                        สินค้าหมด
                                    </span>
                                @elseif($product->inventory_status == 'low_stock')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-yellow-100 text-yellow-800">
                                        สินค้าใกล้หมด
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-gray-100 text-gray-800">
                                        {{ $product->inventory_status }}
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">อนุญาตให้สั่งเกินสต็อค</p>
                            <p class="font-medium">
                                @if($product->allow_backorder)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-blue-100 text-blue-800">
                                        อนุญาต
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-gray-100 text-gray-800">
                                        ไม่อนุญาต
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        @if($product->location)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ตำแหน่งในคลัง</p>
                            <p class="font-medium">{{ $product->location }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <!-- สถานะสต็อค -->
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            @if($product->min_stock > 0)
                                @php
                                    $stockPercentage = min(100, ($product->stock_quantity / ($product->max_stock ?? ($product->min_stock * 3))) * 100);
                                    $stockColor = $product->stock_quantity <= $product->min_stock ? 'bg-red-600' : 
                                        ($product->stock_quantity <= $product->min_stock * 2 ? 'bg-yellow-400' : 'bg-green-600');
                                @endphp
                                <div class="{{ $stockColor }} h-2.5 rounded-full" style="width: {{ $stockPercentage }}%"></div>
                            @endif
                        </div>
                        <div class="flex justify-between text-xs mt-1">
                            <span>0</span>
                            @if($product->min_stock > 0)
                                <span class="text-red-600">{{ $product->min_stock }} (ขั้นต่ำ)</span>
                            @endif
                            @if($product->max_stock)
                                <span>{{ $product->max_stock }} (สูงสุด)</span>
                            @else
                                <span>{{ $product->min_stock * 3 }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- ข้อมูลเพิ่มเติม -->
            @if($product->metadata || $product->attributes || $product->tags)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลเพิ่มเติม</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- คุณสมบัติและข้อมูล -->
                        @if($product->metadata)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">ข้อมูลเมตา (Metadata)</p>
                            @php
                            $metadata = is_array($product->metadata) ? $product->metadata : json_decode($product->metadata, true);
                            @endphp
                            @if(is_array($metadata) && count($metadata) > 0)
                            <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">คุณสมบัติ</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ค่า</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($metadata as $key => $value)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if(is_array($value))
                                                    {{ implode(', ', $value) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-gray-500 dark:text-gray-400">ไม่สามารถแปลงข้อมูลเมตาได้</p>
                            @endif
                        </div>
                        @endif

                        <!-- แอตทริบิวต์ -->
                        @if($product->attributes)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">แอตทริบิวต์ (Attributes)</p>
                            @php
                            $attributes = is_array($product->attributes) ? $product->attributes : json_decode($product->attributes, true);
                            @endphp
                            @if(is_array($attributes) && count($attributes) > 0)
                            <div class="bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-md overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">แอตทริบิวต์</th>
                                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ค่า</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-700 divide-y divide-gray-200 dark:divide-gray-600">
                                        @foreach($attributes as $key => $value)
                                        <tr>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $key)) }}</td>
                                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                @if(is_array($value))
                                                    {{ implode(', ', $value) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-gray-500 dark:text-gray-400">ไม่สามารถแปลงข้อมูลแอตทริบิวต์ได้</p>
                            @endif
                        </div>
                        @endif

                        <!-- แท็ก -->
                        @if($product->tags)
                        <div class="mt-4">
                            <p class="text-gray-500 dark:text-gray-400 text-sm mb-2">แท็ก (Tags)</p>
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                @php
                                $tags = is_array($product->tags) ? $product->tags : json_decode($product->tags, true);
                                @endphp
                                @if(is_array($tags))
                                    @foreach($tags as $tag)
                                        <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- รายละเอียดคุณสมบัติพิเศษ -->
            @php
                // Prepare metadata once
                if (!isset($metadata)) {
                    $metadata = is_array($product->metadata) ? $product->metadata : json_decode($product->metadata, true);
                    if (is_string($metadata)) {
                        $metadata = json_decode($metadata, true);
                    }
                }
                $hasSpecialFeatures = isset($metadata['features']) || isset($metadata['os']) || isset($metadata['energy_rating']) || isset($metadata['resolution']) || isset($metadata['refresh_rate']) || isset($metadata['inputs']);
            @endphp

            @if($hasSpecialFeatures)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">คุณสมบัติพิเศษ</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @if(isset($metadata['os']))
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ระบบปฏิบัติการ</p>
                            <p class="font-medium">{{ $metadata['os'] }}</p>
                        </div>
                        @endif
                        
                        @if(isset($metadata['energy_rating']))
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ประสิทธิภาพพลังงาน</p>
                            <p class="font-medium">{{ $metadata['energy_rating'] }}</p>
                        </div>
                        @endif
                        
                        @if(isset($metadata['resolution']))
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">ความละเอียดหน้าจอ</p>
                            <p class="font-medium">{{ $metadata['resolution'] }}</p>
                        </div>
                        @endif
                        
                        @if(isset($metadata['refresh_rate']))
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">อัตรารีเฟรช</p>
                            <p class="font-medium">{{ $metadata['refresh_rate'] }}</p>
                        </div>
                        @endif
                        
                        @if(isset($metadata['features']) && is_array($metadata['features']))
                        <div class="mt-4">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">คุณสมบัติเด่น</p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($metadata['features'] as $feature)
                                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        
                        @if(isset($metadata['inputs']) && is_array($metadata['inputs']))
                        <div class="mt-4">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">พอร์ตเชื่อมต่อ</p>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($metadata['inputs'] as $input)
                                    <span class="inline-flex px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        {{ $input }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- ข้อมูลระบบ -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลระบบ</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">วันที่สร้าง</p>
                            <p class="font-medium">{{ isset($product->created_at) ? $product->created_at->format('d/m/Y H:i:s') : '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">วันที่อัปเดต</p>
                            <p class="font-medium">{{ isset($product->updated_at) ? $product->updated_at->format('d/m/Y H:i:s') : '-' }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 dark:text-gray-400 text-sm">วันที่ลบ (ถ้ามี)</p>
                            <p class="font-medium">{{ isset($product->deleted_at) ? $product->deleted_at->format('d/m/Y H:i:s') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลเพิ่มเติมอื่นๆ - ส่วนนี้จะซ่อนข้อมูลเทคนิคเชิงลึก -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลเพิ่มเติมอื่นๆ</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- ส่วนที่ยังคงเดิม -->
                        <div>
                            <h4 class="font-medium mb-2 text-indigo-600 dark:text-indigo-400">ข้อมูลสต็อค</h4>
                            @if(!$product->is_service)
                                <div class="mb-2">
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">สต็อคปัจจุบัน</p>
                                    <p class="font-medium">{{ number_format($product->stock_quantity) }} {{ $product->unitRelation->name ?? $product->unit ?? '' }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 dark:text-gray-400 text-sm">สถานะคลัง</p>
                                    <p class="font-medium">
                                        @if($product->stock_quantity > $product->min_stock)
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-green-100 text-green-800">
                                                พร้อมจำหน่าย
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-red-100 text-red-800">
                                                สินค้าใกล้หมด
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            @else
                                <p class="text-gray-500">ไม่มีข้อมูลสต็อค (บริการ)</p>
                            @endif
                        </div>

                        <div>
                            <h4 class="font-medium mb-2 text-indigo-600 dark:text-indigo-400">ข้อมูลสินค้าพิเศษ</h4>
                            @if($product->is_featured || $product->is_bestseller || $product->is_new)
                                <ul class="list-disc list-inside">
                                    @if($product->is_featured)
                                        <li>สินค้าแนะนำ</li>
                                    @endif
                                    @if($product->is_bestseller)
                                        <li>สินค้าขายดี</li>
                                    @endif
                                    @if($product->is_new)
                                        <li>สินค้าใหม่</li>
                                    @endif
                                </ul>
                            @else
                                <p class="text-gray-500">ไม่มีข้อมูลพิเศษ</p>
                            @endif
                        </div>

                        <div>
                            <h4 class="font-medium mb-2 text-indigo-600 dark:text-indigo-400">ข้อมูลเทคนิค</h4>
                            <details class="border border-gray-200 dark:border-gray-700 rounded-md">
                                <summary class="px-4 py-2 cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                                    คลิกเพื่อดูข้อมูลเทคนิคเชิงลึก
                                </summary>
                                <div class="p-4">
                                    @if(!empty($metadata))
                                        <ul class="space-y-2">
                                            @foreach($metadata as $key => $value)
                                                @if(!in_array($key, ['color', 'warranty', 'os', 'features', 'size', 'energy_rating', 'resolution', 'refresh_rate', 'inputs']))
                                                    <li class="text-sm">
                                                        <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span> 
                                                        @if(is_array($value))
                                                            {{ implode(', ', $value) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-gray-500">ไม่มีข้อมูลเทคนิคเพิ่มเติม</p>
                                    @endif

                                    <!-- แสดงข้อมูลการอ้างอิงอื่นๆ -->
                                    <div class="mt-2">
                                        @if($product->sku)
                                            <div class="text-sm mt-1">
                                                <span class="font-medium">SKU:</span> {{ $product->sku }}
                                            </div>
                                        @endif
                                        @if(isset($metadata['compatible_models']) && is_array($metadata['compatible_models']))
                                            <div class="text-sm mt-1">
                                                <span class="font-medium">รุ่นที่ใช้งานร่วมกันได้:</span> {{ implode(', ', $metadata['compatible_models']) }}
                                            </div>
                                        @endif
                                        @if(isset($metadata['material']))
                                            <div class="text-sm mt-1">
                                                <span class="font-medium">วัสดุ:</span> {{ $metadata['material'] }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </details>
                        </div>
                    </div>
                    
                    <!-- ปุ่มดำเนินการ -->
                    <div class="mt-6 flex justify-end">
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้า/บริการนี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition">
                                {{ __('ลบสินค้า/บริการนี้') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
