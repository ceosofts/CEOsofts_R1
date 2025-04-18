<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('รายการใบสั่งขาย') }}
            </h2>
            <a href="{{ route('orders.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                {{ __('สร้างใบสั่งขายใหม่') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <!-- Filters -->
                    <div class="mb-6">
                        <form action="{{ route('orders.index') }}" method="GET" class="flex flex-wrap gap-4">
                            <div class="flex-1">
                                <x-input-label for="search" :value="__('ค้นหา')" />
                                <x-text-input id="search" name="search" type="text" class="w-full" placeholder="ค้นหาตามเลขที่ใบสั่งขาย เลขที่ PO หรือ ชื่อลูกค้า" value="{{ request('search') }}" />
                            </div>

                            <div class="w-40">
                                <x-input-label for="status" :value="__('สถานะ')" />
                                <select id="status" name="status" class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">ทั้งหมด</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>ร่าง</option>
                                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>ยืนยันแล้ว</option>
                                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>กำลังดำเนินการ</option>
                                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>จัดส่งแล้ว</option>
                                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>ส่งมอบแล้ว</option>
                                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                </select>
                            </div>

                            <div class="w-40">
                                <x-input-label for="from_date" :value="__('ตั้งแต่วันที่')" />
                                <x-text-input id="from_date" name="from_date" type="date" class="w-full" value="{{ request('from_date') }}" />
                            </div>

                            <div class="w-40">
                                <x-input-label for="to_date" :value="__('ถึงวันที่')" />
                                <x-text-input id="to_date" name="to_date" type="date" class="w-full" value="{{ request('to_date') }}" />
                            </div>

                            <div class="w-auto">
                                <x-input-label class="invisible" for="filter_button" :value="__('กรอง')" />
                                <x-primary-button class="h-10">{{ __('กรอง') }}</x-primary-button>
                            </div>

                            @if(request('search') || request('status') || request('from_date') || request('to_date'))
                                <div class="flex items-end">
                                    <a href="{{ route('orders.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                                        {{ __('ล้างตัวกรอง') }}
                                    </a>
                                </div>
                            @endif
                        </form>
                    </div>

                    <!-- Orders Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="py-2 px-4 border-b text-left">เลขที่</th>
                                    <th class="py-2 px-4 border-b text-left">วันที่สั่งซื้อ</th>
                                    <th class="py-2 px-4 border-b text-left">ลูกค้า</th>
                                    <th class="py-2 px-4 border-b text-left">มูลค่ารวม</th>
                                    <th class="py-2 px-4 border-b text-left">สถานะ</th>
                                    <th class="py-2 px-4 border-b text-left">กำหนดส่งมอบ</th>
                                    <th class="py-2 px-4 border-b text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-2 px-4 border-b">
                                            <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                                {{ $order->order_number }}
                                            </a>
                                        </td>
                                        <td class="py-2 px-4 border-b">{{ $order->order_date->format('d/m/Y') }}</td>
                                        <td class="py-2 px-4 border-b">{{ $order->customer->name }}</td>
                                        <td class="py-2 px-4 border-b">{{ number_format($order->total_amount, 2) }}</td>
                                        <td class="py-2 px-4 border-b">
                                            <span class="px-2 py-1 bg-{{ $order->statusColor }}-100 text-{{ $order->statusColor }}-800 rounded-md text-xs">
                                                {{ $order->statusText }}
                                            </span>
                                        </td>
                                        <td class="py-2 px-4 border-b">
                                            {{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}
                                        </td>
                                        <td class="py-2 px-4 border-b text-center">
                                            <div class="flex justify-center space-x-2">
                                                <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:text-blue-900" title="ดูรายละเอียด">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>

                                                @if(!in_array($order->status, ['shipped', 'delivered', 'cancelled']))
                                                    <a href="{{ route('orders.edit', $order) }}" class="text-yellow-600 hover:text-yellow-900" title="แก้ไข">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                @endif

                                                @if(in_array($order->status, ['draft', 'cancelled']))
                                                    <button type="button" onclick="confirmDelete('{{ $order->id }}')" class="text-red-600 hover:text-red-900" title="ลบ">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                    
                                                    <form id="delete-form-{{ $order->id }}" action="{{ route('orders.destroy', $order) }}" method="POST" class="hidden">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-6 text-center text-gray-500">ไม่พบข้อมูลใบสั่งขาย</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(orderId) {
            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบใบสั่งขายนี้?')) {
                document.getElementById('delete-form-' + orderId).submit();
            }
        }
    </script>
</x-app-layout>