<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('รายละเอียดการเคลื่อนไหวสินค้า') }}
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">รหัสสินค้า:</h3>
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded">
                                    {{ $stockMovement->product->code }}
                                </span>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">ชื่อสินค้า:</h3>
                                <p class="text-gray-600">{{ $stockMovement->product->name }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">ประเภทการเคลื่อนไหว:</h3>
                                <p>
                                    @if ($stockMovement->movement_type == 'in')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-green-100 text-green-800">รับเข้า</span>
                                    @elseif ($stockMovement->movement_type == 'out')
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-red-100 text-red-800">เบิกออก</span>
                                    @else
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-yellow-100 text-yellow-800">ปรับยอด</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">วันที่:</h3>
                                <p class="text-gray-600">{{ \Carbon\Carbon::parse($stockMovement->movement_date)->format('d/m/Y') }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">จำนวน:</h3>
                                <p class="text-gray-600 font-medium">{{ number_format(abs($stockMovement->quantity), 2) }} {{ $stockMovement->unit->name }}</p>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded">
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">ประเภทอ้างอิง:</h3>
                                <p class="text-gray-600">{{ $stockMovement->reference_type ?? '-' }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">รหัสอ้างอิง:</h3>
                                <p class="text-gray-600">{{ $stockMovement->reference_id ?? '-' }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">หมายเหตุ:</h3>
                                <p class="text-gray-600">{{ $stockMovement->note ?? '-' }}</p>
                            </div>
                            
                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">ผู้บันทึก:</h3>
                                <p class="text-gray-600">{{ $stockMovement->createdBy?->name ?? '-' }}</p>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-sm font-semibold text-gray-700 mb-1">วันที่บันทึก:</h3>
                                <p class="text-gray-600">{{ $stockMovement->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            
                            @if ($stockMovement->updated_by)
                                <div class="mb-4">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-1">ผู้แก้ไขล่าสุด:</h3>
                                    <p class="text-gray-600">{{ $stockMovement->updatedBy?->name ?? '-' }}</p>
                                </div>

                                <div class="mb-4">
                                    <h3 class="text-sm font-semibold text-gray-700 mb-1">วันที่แก้ไขล่าสุด:</h3>
                                    <p class="text-gray-600">{{ $stockMovement->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="{{ route('stock-movements.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:border-gray-400 focus:ring ring-gray-200 disabled:opacity-25 transition ease-in-out duration-150 mr-3">
                            {{ __('กลับ') }}
                        </a>
                        <a href="{{ route('stock-movements.edit', $stockMovement) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            {{ __('แก้ไข') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
