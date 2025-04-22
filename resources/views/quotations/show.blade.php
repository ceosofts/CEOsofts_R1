<!-- เพิ่มการอ้างอิงไฟล์ CSS และ JavaScript ที่แยกออกมา -->
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-3xl text-blue-800">
                {{ __('ใบเสนอราคา') }} #{{ $quotation->quotation_number }}
            </h2>
            <div class="flex space-x-2">
                <!-- เปลี่ยนปุ่ม "กลับไปรายการ" เป็นสีเทา -->
                <a href="{{ route('quotations.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-500 border border-gray-500 rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปรายการ') }}
                </a>
                
                <!-- ปุ่ม Preview -->
                <button id="preview-button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    {{ __('ดูตัวอย่าง') }}
                </button>
                
                <!-- ปุ่ม Print -->
                <button id="print-button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    {{ __('พิมพ์') }}
                </button>
                
                <!-- ปรับเปลี่ยนข้อความจาก "ดาวน์โหลด PDF" เป็น "Expo PDF" -->
                <!-- <a href="{{ route('quotations.pdf', $quotation) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md shadow-sm hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    {{ __('Expo PDF') }}
                </a> -->
                
                @if($quotation->status == 'draft')
                <a href="{{ route('quotations.edit', $quotation) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md shadow-sm hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('แก้ไข') }}
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- ส่วนเนื้อหาหลัก -->
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

            <!-- ข้อมูลหลักใบเสนอราคา -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">ข้อมูลทั่วไป</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">เลขที่</p>
                                    <p class="font-medium">{{ $quotation->quotation_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">วันที่</p>
                                    <p class="font-medium">{{ $quotation->issue_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">วันที่หมดอายุ</p>
                                    <p class="font-medium">{{ $quotation->expiry_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">สถานะ</p>
                                    <p>
                                        @if($quotation->status == 'draft')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-gray-100 text-gray-800">กำลังเสนอลูกค้า</span>
                                        @elseif($quotation->status == 'approved')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-green-100 text-green-800">อนุมัติแล้ว</span>
                                        @elseif($quotation->status == 'rejected')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold leading-5 rounded-full bg-red-100 text-red-800">ปฏิเสธแล้ว</span>
                                        @endif
                                    </p>
                                </div>
                                <!-- เพิ่มการแสดงพนักงานขาย -->
                                <div class="col-span-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">พนักงานขาย</p>
                                    <p class="font-medium">
                                        @if($quotation->sales_person_id && $salesPerson = \App\Models\Employee::find($quotation->sales_person_id))
                                            {{ $salesPerson->employee_code }} - {{ $salesPerson->first_name }} {{ $salesPerson->last_name }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-semibold mb-3">ข้อมูลลูกค้า</h3>
                            <p class="font-medium">{{ $quotation->customer->name }}</p>
                            <p>{{ $quotation->customer->address }}</p>
                            <p>โทร: {{ $quotation->customer->phone }}</p>
                            <p>อีเมล: {{ $quotation->customer->email }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- รายการสินค้า -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">รายการสินค้า</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">รหัส</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">รายการ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">จำนวน</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ราคาต่อหน่วย</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ส่วนลด</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">รวม</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($quotation->items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $item->product->code ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->quantity, 2) }} {{ $item->unit->name ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->unit_price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @if($item->discount_percentage > 0)
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->discount_percentage, 2) }}% ({{ number_format($item->discount_amount, 2) }})</div>
                                        @else
                                            <div class="text-sm text-gray-500 dark:text-gray-400">-</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($item->subtotal, 2) }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                        ไม่พบรายการสินค้า
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- สรุปยอด -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/2 lg:w-1/3">
                            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">ยอดรวมก่อนภาษี</span>
                                <span>{{ number_format($quotation->subtotal, 2) }}</span>
                            </div>
                            @if($quotation->discount_amount > 0)
                            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">
                                    ส่วนลด
                                    @if($quotation->discount_type == 'percentage')
                                    ({{ $quotation->discount_amount }}%)
                                    @endif
                                </span>
                                <span>{{ number_format($quotation->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            @if($quotation->tax_amount > 0)
                            <div class="flex justify-between py-2 border-b dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">
                                    ภาษีมูลค่าเพิ่ม ({{ $quotation->tax_rate }}%)
                                </span>
                                <span>{{ number_format($quotation->tax_amount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between py-3 font-bold">
                                <span>ยอดรวมทั้งสิ้น</span>
                                <span class="text-lg">{{ number_format($quotation->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ปุ่มอนุมัติ/ปฏิเสธ -->
            @if($quotation->status == 'draft')
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-center space-x-4">
                        <form action="{{ route('quotations.approve', $quotation) }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                อนุมัติใบเสนอราคา
                            </button>
                        </form>
                        
                        <button type="button" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                                onclick="document.getElementById('rejection-modal').classList.remove('modal-hidden')">
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            ปฏิเสธใบเสนอราคา
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal สำหรับการปฏิเสธ -->
    @if($quotation->status == 'draft')
    <div id="rejection-modal" class="modal-hidden"> <!-- เปลี่ยนจาก hidden เป็น modal-hidden -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">ปฏิเสธใบเสนอราคา</h3>
            <form action="{{ route('quotations.reject', $quotation) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สาเหตุการปฏิเสธ</label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                              placeholder="กรุณาระบุสาเหตุการปฏิเสธใบเสนอราคา"></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            onclick="document.getElementById('rejection-modal').classList.add('modal-hidden')">
                        ยกเลิก
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        ยืนยันการปฏิเสธ
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal สำหรับการ Preview -->
    <div id="preview-modal" class="modal-hidden">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">ตัวอย่างก่อนพิมพ์</h3>
                <button type="button" id="close-preview" class="text-gray-400 hover:text-gray-500">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div id="preview-content" class="print-section bg-white text-black">
                <!-- ข้อมูลบริษัท -->
                <div class="text-center mb-6">
                    <h1 class="text-xl font-bold">{{ $company->name ?? 'บริษัท ซีอีโอซอฟต์ จำกัด' }}</h1>
                    <p>{{ $company->address ?? '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110' }}</p>
                    <p>โทร: {{ $company->phone ?? '02-123-4567' }}, อีเมล: {{ $company->email ?? 'info@ceosofts.com' }}</p>
                </div>

                <div class="border-b-2 border-gray-800 mb-6">
                    <h2 class="text-center text-2xl font-bold">ใบเสนอราคา</h2>
                </div>

                <!-- ข้อมูลใบเสนอราคาและลูกค้า -->
                <div class="grid-cols-2 mb-6">
                    <div>
                        <p><strong>ลูกค้า:</strong> {{ $quotation->customer->name }}</p>
                        <p>{{ $quotation->customer->address }}</p>
                        <p>โทร: {{ $quotation->customer->phone }}</p>
                        <p>อีเมล: {{ $quotation->customer->email }}</p>
                    </div>
                    <div class="text-right">
                        <p><strong>เลขที่:</strong> {{ $quotation->quotation_number }}</p>
                        <p><strong>วันที่:</strong> {{ $quotation->issue_date->format('d/m/Y') }}</p>
                        <p><strong>วันที่หมดอายุ:</strong> {{ $quotation->expiry_date->format('d/m/Y') }}</p>
                        <p><strong>อ้างอิง:</strong> {{ $quotation->reference_number ?: '-' }}</p>
                        <!-- เพิ่มแสดงพนักงานขายในส่วน preview -->
                        <p><strong>พนักงานขาย:</strong> 
                            @if($quotation->sales_person_id && $salesPerson = \App\Models\Employee::find($quotation->sales_person_id))
                                {{ $salesPerson->employee_code }} - {{ $salesPerson->first_name }} {{ $salesPerson->last_name }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>

                <!-- รายการสินค้า -->
                <table class="min-w-full border border-gray-300 mb-6">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="py-2 px-4 border text-left">ลำดับ</th>
                            <th class="py-2 px-4 border text-left">รหัสสินค้า</th>
                            <th class="py-2 px-4 border text-left">รายการ</th>
                            <th class="py-2 px-4 border text-right">จำนวน</th>
                            <th class="py-2 px-4 border text-right">ราคาต่อหน่วย</th>
                            <th class="py-2 px-4 border text-right">ส่วนลด</th>
                            <th class="py-2 px-4 border text-right">จำนวนเงิน</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quotation->items as $index => $item)
                        <tr>
                            <td class="py-2 px-4 border">{{ $index + 1 }}</td>
                            <td class="py-2 px-4 border">{{ $item->product->code ?? '-' }}</td>
                            <td class="py-2 px-4 border">{{ $item->description }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($item->quantity, 2) }} {{ $item->unit->name ?? '' }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-2 px-4 border text-right">
                                @if($item->discount_percentage > 0)
                                    {{ number_format($item->discount_percentage, 2) }}% 
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-2 px-4 border text-right">{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-2 px-4 border text-center">ไม่มีรายการสินค้า</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- สรุปยอดรวม -->
                <div class="flex justify-end mb-6">
                    <div class="w-1/3">
                        <div class="flex justify-between py-2 border-b">
                            <span>ยอดรวมก่อนภาษี</span>
                            <span>{{ number_format($quotation->subtotal, 2) }}</span>
                        </div>
                        @if($quotation->discount_amount > 0)
                        <div class="flex justify-between py-2 border-b">
                            <span>ส่วนลด
                                @if($quotation->discount_type == 'percentage')
                                ({{ $quotation->discount_amount }}%)
                                @endif
                            </span>
                            <span>{{ number_format($quotation->discount_amount, 2) }}</span>
                        </div>
                        @endif
                        @if($quotation->tax_amount > 0)
                        <div class="flex justify-between py-2 border-b">
                            <span>ภาษีมูลค่าเพิ่ม ({{ $quotation->tax_rate }}%)</span>
                            <span>{{ number_format($quotation->tax_amount, 2) }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between py-3 font-bold">
                            <span>ยอดรวมทั้งสิ้น</span>
                            <span>{{ number_format($quotation->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- หมายเหตุ -->
                @if($quotation->notes)
                <div class="mb-6">
                    <h4 class="font-semibold mb-2">หมายเหตุ</h4>
                    <p class="p-3 border rounded">{{ $quotation->notes }}</p>
                </div>
                @endif

                <!-- ส่วนลงนาม -->
                <div class="grid grid-cols-2 gap-6 mt-12">
                    <div class="text-center">
                        <div class="border-t border-gray-400 pt-2 mt-12 inline-block w-48">
                            <p>ผู้เสนอราคา</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="border-t border-gray-400 pt-2 mt-12 inline-block w-48">
                            <p>ผู้มีอำนาจลงนาม</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- เพิ่มหรือแก้ไข CSS เพื่อให้แน่ใจว่า modal แสดงผลได้อย่างถูกต้อง -->
    <style>
        /* ปรับแต่งการแสดงผล Modal โดยไม่ใช้คลาส Tailwind */
        #preview-modal {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 50;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        #preview-modal.modal-hidden {
            display: none;
        }
        
        /* เพิ่ม CSS สำหรับ modal-content เพื่อปรับความกว้าง */
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            width: 90%;
            max-width: 1000px; /* เพิ่มความกว้างสูงสุด */
            max-height: 90vh;
            overflow-y: auto;
        }
        
        /* ปรับขอบกระดาษในส่วน preview content */
        #preview-content {
            padding: 25px 40px; /* เพิ่มความกว้างของขอบด้านข้าง */
            background-color: white;
            margin: 0 auto;
        }
        
        /* ปรับปรุงส่วนแสดงข้อมูลฝั่งขวา */
        .grid-cols-2 {
            display: flex;
            justify-content: space-between;
            width: 100%;
            padding: 0;
            margin: 0 0 20px 0;
        }
        
        .grid-cols-2 > div {
            width: 48%;
        }
        
        /* เพิ่ม CSS สำหรับ modal การปฏิเสธ */
        #rejection-modal {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        #rejection-modal.modal-hidden {
            display: none;
        }
    </style>

    <!-- นำเข้า CSS และ JavaScript ที่แยกออกมา -->
    <link rel="stylesheet" href="{{ asset('css/quotation-preview.css') }}">
    <script src="{{ asset('js/quotation-preview.js') }}"></script>
    
    <!-- เพิ่ม JavaScript สำหรับปุ่มดูตัวอย่างและพิมพ์ -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ควบคุมปุ่ม "ดูตัวอย่าง" (Preview)
            const previewButton = document.getElementById('preview-button');
            const previewModal = document.getElementById('preview-modal');
            const closePreview = document.getElementById('close-preview');
            
            if (previewButton) {
                previewButton.addEventListener('click', function() {
                    previewModal.classList.remove('modal-hidden');
                });
            }
            
            if (closePreview) {
                closePreview.addEventListener('click', function() {
                    previewModal.classList.add('modal-hidden');
                });
            }
            
            // ปุ่ม "พิมพ์" (Print) - ใช้ฟังก์ชัน printQuotation() เพื่อได้รูปแบบที่สวยงาม
            const printButton = document.getElementById('print-button');
            if (printButton) {
                // ลบ event listener เดิมทั้งหมดก่อน (ป้องกันซ้ำซ้อน)
                printButton.replaceWith(printButton.cloneNode(true));
                const newPrintButton = document.getElementById('print-button');
                newPrintButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    const title = 'ใบเสนอราคาเลขที่ {{ $quotation->quotation_number }}';
                    document.title = title; // ตั้งชื่อเอกสารในหน้าปัจจุบัน
                    // ส่ง title เป็น parameter ไปยังฟังก์ชัน printQuotation
                    printQuotation(title);
                });
            }

            // ซ่อน modal เมื่อกดปุ่ม Escape
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    previewModal.classList.add('modal-hidden');
                }
            });
            
            // ซ่อน modal เมื่อคลิกพื้นหลัง
            previewModal.addEventListener('click', function(event) {
                if (event.target === previewModal) {
                    previewModal.classList.add('modal-hidden');
                }
            });
            
            // เพิ่ม event listener สำหรับการพิมพ์เพื่อกำหนดชื่อเอกสาร
            window.addEventListener('beforeprint', function() {
                document.title = 'ใบเสนอราคาเลขที่ {{ $quotation->quotation_number }}';
            });
        });
    </script>
    
    <!-- เพิ่ม CSS สำหรับการพิมพ์ -->
    <style>
        @media print {
            /* ซ่อน modal preview และปุ่มที่ไม่ต้องการพิมพ์ */
            #preview-modal, #preview-modal *, .toolbar, #print-button, #preview-button {
                display: none !important;
            }
            /* ให้เนื้อหาใบเสนอราคาหลักแสดงผลเต็มที่ */
            body * {
                visibility: visible;
            }
        }
    </style>
</x-app-layout>
