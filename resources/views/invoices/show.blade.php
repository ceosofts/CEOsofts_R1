<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-extrabold text-4xl text-blue-800">
                {{ __('ใบแจ้งหนี้') }} #{{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('กลับไปรายการใบแจ้งหนี้') }}
                </a>
                <a href="{{ route('invoices.edit', $invoice) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('แก้ไข') }}
                </a>
                <a href="{{ route('invoices.print', $invoice) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                    </svg>
                    {{ __('พิมพ์') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">สำเร็จ!</strong>
                    <p class="mt-2">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">เกิดข้อผิดพลาด!</strong>
                    <p class="mt-2">{{ session('error') }}</p>
                </div>
            @endif

            <!-- ถ้ามีคำถามว่าต้องการ Continue to iterate หรือไม่ -->
            @if(session('ask_continue') && session('continue_order_id'))
                <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
                    <div class="flex">
                        <div class="py-1">
                            <svg class="h-6 w-6 text-blue-500 mr-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold">ต้องการสร้างใบแจ้งหนี้เพิ่มเติมจากใบสั่งขายนี้หรือไม่?</p>
                            <p class="text-sm mt-2">คุณสามารถสร้างใบแจ้งหนี้เพิ่มเติมจากใบสั่งขายเดิมได้ในกรณีที่ต้องการแยกใบแจ้งหนี้</p>
                            <div class="mt-4 flex space-x-3">
                                <a href="{{ route('invoices.create', ['order_id' => session('continue_order_id'), 'continue_iteration' => 1]) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    ใช่ สร้างใบแจ้งหนี้เพิ่มเติม
                                </a>
                                <a href="{{ route('invoices.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    ไม่ ไปที่รายการใบแจ้งหนี้
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- ข้อมูลทั่วไปของใบแจ้งหนี้ -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลเอกสาร</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">เลขที่ใบแจ้งหนี้</p>
                            <p class="mt-1 text-lg font-bold">{{ $invoice->invoice_number }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">เลขที่อ้างอิง</p>
                            <p class="mt-1">{{ $invoice->reference_number ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ลูกค้า</p>
                            <p class="mt-1">{{ $invoice->customer->name ?? '-' }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">วันที่ใบแจ้งหนี้</p>
                            <p class="mt-1">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">วันครบกำหนดชำระ</p>
                            <p class="mt-1">{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-' }}</p>
                        </div>
                        
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">พนักงานขาย</p>
                            <p class="mt-1">{{ $invoice->salesPerson->first_name ?? '' }} {{ $invoice->salesPerson->last_name ?? '' }}</p>
                        </div>

                        <div class="col-span-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">หมายเหตุ</p>
                            <p class="mt-1">{{ $invoice->notes ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลการจัดส่ง -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ข้อมูลการจัดส่ง</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ที่อยู่จัดส่ง</p>
                            <p class="mt-1 whitespace-pre-wrap">{{ $invoice->shipping_address ?? '-' }}</p>
                        </div>
                        
                        <div>
                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">วิธีการจัดส่ง</p>
                                <p class="mt-1">{{ $invoice->shipping_method ?? '-' }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">ค่าขนส่ง</p>
                                <p class="mt-1">{{ number_format($invoice->shipping_cost, 2) }} บาท</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- รายการสินค้า -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">รายการสินค้า</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-600 text-gray-700 dark:text-gray-300">
                                    <th class="py-2 px-4 border-b dark:border-gray-600 text-center" style="width: 50px;">ลำดับ</th>
                                    <th class="py-2 px-4 border-b dark:border-gray-600 text-left">รหัสสินค้า</th>
                                    <th class="py-2 px-4 border-b dark:border-gray-600 text-left">สินค้า</th>
                                    <th class="py-2 px-4 border-b dark:border-gray-600 text-center" style="width: 120px;">จำนวน</th>
                                    <th class="py-2 px-4 border-b dark:border-gray-600 text-center" style="width: 80px;">หน่วย</th>
                                    <th class="py-2 px-4 border-b dark:border-gray-600 text-right" style="width: 150px;">ราคาต่อหน่วย</th>
                                    <th class="py-2 px-4 border-b dark:border-gray-600 text-right" style="width: 150px;">จำนวนเงิน</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $index => $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-center">{{ $index + 1 }}</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600">{{ $item->product->product_code ?? '-' }}</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600">{{ $item->product->name ?? $item->description }}</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-center">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-center">{{ $item->unit }}</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right">{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-600">
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right font-medium" colspan="6">รวมเป็นเงิน</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right font-bold">{{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-600">
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right font-medium" colspan="6">ค่าขนส่ง</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right">{{ number_format($invoice->shipping_cost, 2) }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-600">
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right font-medium" colspan="6">ส่วนลด</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right">{{ number_format($invoice->discount_amount, 2) }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-600">
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right font-medium" colspan="6">ภาษีมูลค่าเพิ่ม {{ $invoice->tax_rate }}%</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right">{{ number_format($invoice->tax_amount, 2) }}</td>
                                </tr>
                                <tr class="bg-gray-50 dark:bg-gray-600">
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right font-medium text-lg" colspan="6">ยอดรวมทั้งสิ้น</td>
                                    <td class="py-2 px-4 border-b dark:border-gray-600 text-right font-bold text-lg">{{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ข้อมูลใบสั่งขายที่เกี่ยวข้อง -->
            @if($invoice->order)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">ใบสั่งขายที่เกี่ยวข้อง</h3>
                    
                    <div class="flex items-center">
                        <span class="text-gray-500 dark:text-gray-400">ใบสั่งขายเลขที่:</span>
                        <a href="{{ route('orders.show', $invoice->order) }}" class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                            {{ $invoice->order->order_number }}
                        </a>
                        <span class="ml-4 px-2 py-1 text-xs font-semibold rounded"
                            :class="{
                                'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900': '{{ $invoice->order->status }}' === 'delivered' || '{{ $invoice->order->status }}' === 'completed',
                                'bg-blue-100 text-blue-800 dark:bg-blue-200 dark:text-blue-900': '{{ $invoice->order->status }}' === 'processing',
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-200 dark:text-yellow-900': '{{ $invoice->order->status }}' === 'pending',
                                'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900': '{{ $invoice->order->status }}' === 'cancelled',
                                'bg-gray-100 text-gray-800 dark:bg-gray-200 dark:text-gray-900': !['delivered', 'completed', 'processing', 'pending', 'cancelled'].includes('{{ $invoice->order->status }}')
                            }">
                            {{ ucfirst($invoice->order->status) }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- สถานะการชำระเงิน -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">สถานะการชำระเงิน</h3>
                    
                    <div class="flex items-center">
                        <span class="text-gray-500 dark:text-gray-400">สถานะการชำระเงิน:</span>
                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded"
                            :class="{
                                'bg-green-100 text-green-800 dark:bg-green-200 dark:text-green-900': '{{ $invoice->payment_status }}' === 'paid',
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-200 dark:text-yellow-900': '{{ $invoice->payment_status }}' === 'partial',
                                'bg-red-100 text-red-800 dark:bg-red-200 dark:text-red-900': '{{ $invoice->payment_status }}' === 'unpaid' || '{{ $invoice->payment_status }}' === ''
                            }">
                            @if($invoice->payment_status == 'paid')
                                ชำระแล้ว
                            @elseif($invoice->payment_status == 'partial')
                                ชำระบางส่วน
                            @else
                                ยังไม่ได้ชำระ
                            @endif
                        </span>
                    </div>
                    
                    <div class="mt-4">
                        <!-- ปุ่มนี้จะถูกเปิดใช้งานเมื่อระบบการชำระเงินพร้อมใช้งาน -->
                        <button type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 opacity-50 cursor-not-allowed" disabled>
                            <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z" />
                            </svg>
                            บันทึกการชำระเงิน
                        </button>
                        <p class="text-sm text-gray-500 mt-2">ระบบบันทึกการชำระเงินอยู่ระหว่างการพัฒนา</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>