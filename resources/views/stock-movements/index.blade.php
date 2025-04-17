<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('การเคลื่อนไหวสินค้า') }}
            </h2>
            <a href="{{ route('stock-movements.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                {{ __('เพิ่มรายการ') }}
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <!-- ส่วนค้นหา -->
                <div class="p-4 border-b border-gray-200">
                    <form method="GET" action="{{ route('stock-movements.index') }}" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <label for="search" class="sr-only">ค้นหา</label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="ค้นหาตามรหัสสินค้า หรือชื่อ..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div class="flex-1">
                            <select name="type" id="type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">-- ทุกประเภท --</option>
                                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>รับเข้า</option>
                                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>เบิกออก</option>
                                <option value="adjust" {{ request('type') == 'adjust' ? 'selected' : '' }}>ปรับยอด</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div class="flex-1">
                            <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                ค้นหา
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-6 bg-white border-b border-gray-200">
                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200">
                            <thead class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="py-3 px-4 text-left border-b">วันที่</th>
                                    <th class="py-3 px-4 text-left border-b">รหัสสินค้า</th>
                                    <th class="py-3 px-4 text-left border-b">ชื่อสินค้า</th>
                                    <th class="py-3 px-4 text-left border-b">ประเภท</th>
                                    <th class="py-3 px-4 text-left border-b">จำนวน</th>
                                    <th class="py-3 px-4 text-left border-b">หน่วย</th>
                                    <th class="py-3 px-4 text-left border-b">อ้างอิง</th>
                                    <th class="py-3 px-4 text-center border-b">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 text-sm text-gray-700">
                                @forelse ($stockMovements as $movement)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4">{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}</td>
                                        <td class="py-2 px-4">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded">
                                                {{ $movement->product->code }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4">{{ $movement->product->name }}</td>
                                        <td class="py-2 px-4">
                                            @if ($movement->movement_type == 'in')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">รับเข้า</span>
                                            @elseif ($movement->movement_type == 'out')
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-red-100 text-red-800">เบิกออก</span>
                                            @else
                                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-yellow-100 text-yellow-800">ปรับยอด</span>
                                            @endif
                                        </td>
                                        <td class="py-2 px-4 text-right font-medium">
                                            {{ number_format(abs($movement->quantity), 2) }}
                                        </td>
                                        <td class="py-2 px-4">{{ $movement->unit->name }}</td>
                                        <td class="py-2 px-4">
                                            @if ($movement->reference_type || $movement->reference_id)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                                    {{ $movement->reference_type }} {{ $movement->reference_id }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="py-2 px-4">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('stock-movements.show', $movement) }}" class="text-blue-600 hover:text-blue-900" title="ดูรายละเอียด">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                <a href="{{ route('stock-movements.edit', $movement) }}" class="text-indigo-600 hover:text-indigo-900" title="แก้ไข">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('stock-movements.destroy', $movement) }}" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบรายการนี้?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="ลบ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="py-6 px-4 text-center text-gray-500">ไม่พบข้อมูลการเคลื่อนไหวสินค้า</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $stockMovements->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
