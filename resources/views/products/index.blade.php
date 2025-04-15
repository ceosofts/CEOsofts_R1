<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                สินค้าและบริการ
            </h2>
            <div>
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    เพิ่มสินค้า/บริการ
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error') || isset($error))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') ?? $error }}</span>
                </div>
            @endif

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ค้นหาและกรองข้อมูล</h3>
                    
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Search Box -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ค้นหา</label>
                                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="รหัสสินค้า, ชื่อสินค้า, บาร์โค้ด" 
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            
                            <!-- Category Filter -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">หมวดหมู่</label>
                                <select name="category" id="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สถานะ</label>
                                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>ใช้งาน</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>ไม่ใช้งาน</option>
                                </select>
                            </div>
                            
                            <!-- Type Filter -->
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ประเภท</label>
                                <select name="type" id="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">-- ทั้งหมด --</option>
                                    <option value="0" {{ request('type') == '0' ? 'selected' : '' }}>สินค้า</option>
                                    <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>บริการ</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('products.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-2 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:hover:bg-gray-600">
                                รีเซ็ต
                            </a>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Products Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">รายการสินค้าและบริการ</h3>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400 mr-2">เรียงตาม:</span>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => 'asc']) }}"
                                    class="{{ (!request()->has('sort') && !request()->has('direction')) || (request('sort') == 'id' && request('direction') == 'asc') ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    ID ↑
                                </a>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'id', 'direction' => 'desc']) }}"
                                    class="{{ request('sort') == 'id' && request('direction') == 'desc' ? 'text-blue-600 font-medium' : 'text-gray-600' }} text-sm mx-1">
                                    ID ↓
                                </a>
                            </div>
                        </div>

                        @if($products->count() > 0)
                            <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-200">
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">รหัส</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">ชื่อสินค้า/บริการ</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-left">หมวดหมู่</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-right">ราคา</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">สต็อก</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">ประเภท</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">สถานะ</th>
                                        <th class="py-2 px-4 border-b border-gray-300 dark:border-gray-600 text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr class="hover:bg-gray-200 dark:hover:bg-gray-600 border-b border-gray-200 dark:border-gray-700">
                                            <td class="py-2 px-4">{{ $product->code }}</td>
                                            <td class="py-2 px-4">
                                                <div class="flex items-center">
                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 object-cover rounded-md mr-2">
                                                    @else
                                                        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md flex items-center justify-center mr-2">
                                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                            </svg>
                                                        </div>
                                                    @endif
                                                    {{ $product->name }}
                                                </div>
                                            </td>
                                            <td class="py-2 px-4">{{ $product->category->name ?? 'ไม่ระบุหมวดหมู่' }}</td>
                                            <td class="py-2 px-4 text-right">{{ number_format($product->price, 2) }}</td>
                                            <td class="py-2 px-4 text-center">
                                                @if($product->is_service)
                                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                                @else
                                                    @if($product->stock_quantity <= $product->min_stock)
                                                        <span class="text-red-600 dark:text-red-400 font-medium">{{ $product->stock_quantity }}</span>
                                                    @else
                                                        {{ $product->stock_quantity }}
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="py-2 px-4 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                                                    {{ $product->is_service ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' }}">
                                                    {{ $product->is_service ? 'บริการ' : 'สินค้า' }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 text-center">
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full 
                                                    {{ $product->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                    {{ $product->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4 text-center">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('products.show', $product) }}" class="text-blue-500 hover:text-blue-700">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                    <a href="{{ route('products.edit', $product) }}" class="text-yellow-500 hover:text-yellow-700">
                                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสินค้านี้?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $products->withQueryString()->links() }}
                            </div>
                        @else
                            <div class="text-center py-10">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">ไม่พบข้อมูลสินค้า</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">เริ่มต้นโดยการเพิ่มสินค้าหรือบริการใหม่</p>
                                
                                @if(config('app.debug') && isset($debugInfo))
                                <div class="mt-4 p-4 bg-yellow-50 text-yellow-800 rounded-md border border-yellow-200 max-w-3xl mx-auto text-left">
                                    <h4 class="font-semibold mb-2">ข้อมูลสำหรับการ Debug:</h4>
                                    <ul class="list-disc pl-5 space-y-1 text-sm">
                                        <li>จำนวนสินค้าทั้งหมดในระบบ: {{ $debugInfo['total_products'] }}</li>
                                        <li>Company ID ของผู้ใช้ปัจจุบัน: {{ $debugInfo['company_id'] }}</li>
                                        <li>จำนวนหมวดหมู่สินค้า: {{ $debugInfo['categories_count'] }}</li>
                                        <li>SQL Query: <code class="bg-gray-100 px-1 rounded">{{ $debugInfo['query_sql'] }}</code></li>
                                        <li>Query Bindings: <code class="bg-gray-100 px-1 rounded">{{ json_encode($debugInfo['query_bindings']) }}</code></li>
                                    </ul>
                                    <p class="mt-2 text-sm">ควรตรวจสอบว่ามีสินค้าที่ถูกสร้างสำหรับ Company ID {{ $debugInfo['company_id'] }} หรือไม่</p>
                                    <div class="mt-2">
                                        <a href="{{ route('debug.products') }}" target="_blank" class="text-blue-600 hover:underline">ตรวจสอบข้อมูลสินค้าทั้งหมดเพิ่มเติม</a>
                                    </div>
                                </div>
                                @endif
                                
                                <div class="mt-6">
                                    <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        เพิ่มสินค้า/บริการ
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
