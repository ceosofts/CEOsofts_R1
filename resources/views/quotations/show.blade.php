<!-- เพิ่มการอ้างอิงไฟล์ CSS และ JavaScript ที่แยกออกมา -->
<x-app-layout>
    <x-slot name="header">
        <!-- เพิ่ม styles สำหรับ modal -->
        <style>
            /* Modal Styles */
            #preview-modal, #rejection-modal {
                display: flex;
                position: fixed;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 50;
                background-color: rgba(0, 0, 0, 0.5);
                align-items: center;
                justify-content: center;
            }

            #preview-modal.modal-hidden, #rejection-modal.modal-hidden {
                display: none;
            }

            #preview-modal .modal-content {
                background-color: white;
                border-radius: 0.5rem;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                padding: 3rem 4rem;
                width: 100%;
                max-width: 80rem;
                max-height: 90vh;
                overflow-y: auto;
            }
            
            /* Print Styles */
            @media print {
                body {
                    background-color: white;
                    margin: 0;
                    padding: 0;
                }
                
                .print-section {
                    margin: 0;
                    padding: 20px;
                }
                
                .no-print {
                    display: none !important;
                }
            }
        </style>
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
                
                <!-- ปุ่ม Print -->
                <a href="{{ route('quotations.print', $quotation) }}" target="_blank" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    {{ __('พิมพ์') }}
                </a>
                
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-3">ข้อมูลทั่วไป</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">เลขที่</p>
                                    <p class="font-medium">{{ $quotation->quotation_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">วันที่</p>
                                    <p class="font-medium">{{ $quotation->issue_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">วันที่หมดอายุ</p>
                                    <p class="font-medium">{{ $quotation->expiry_date->format('d/m/Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">สถานะ</p>
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
                                    <p class="text-sm text-gray-600">พนักงานขาย</p>
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">รายการสินค้า</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ลำดับ</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รหัส</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รายการ</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวน</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">หน่วยสินค้า</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ราคาต่อหน่วย</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ส่วนลด</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">รวม</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($quotation->items as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $index + 1 }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->product->code ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal">
                                        <div class="text-sm font-medium text-gray-900">{{ $item->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="text-sm text-gray-900">{{ $item->unit->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900">{{ number_format($item->unit_price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @if($item->discount_percentage > 0)
                                            <div class="text-sm text-gray-900">{{ number_format($item->discount_percentage, 2) }}% ({{ number_format($item->discount_amount, 2) }})</div>
                                        @else
                                            <div class="text-sm text-gray-500">-</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="text-sm text-gray-900">{{ number_format($item->subtotal, 2) }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-end">
                        <div class="w-full md:w-1/2 lg:w-1/3">
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">ยอดรวมก่อนภาษี</span>
                                <span>{{ number_format($quotation->subtotal, 2) }}</span>
                            </div>
                            @if($quotation->discount_amount > 0)
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">
                                    ส่วนลด
                                    @if($quotation->discount_type == 'percentage')
                                    ({{ $quotation->discount_amount }}%)
                                    @endif
                                </span>
                                <span>{{ number_format($quotation->discount_amount, 2) }}</span>
                            </div>
                            @endif
                            @if($quotation->tax_amount > 0)
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg">
            <h3 class="text-lg font-semibold mb-4 text-gray-900">ปฏิเสธใบเสนอราคา</h3>
            <form action="{{ route('quotations.reject', $quotation) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-1">สาเหตุการปฏิเสธ</label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="3" required
                              class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
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

    <!-- เพิ่ม JavaScript สำหรับการพิมพ์ -->
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
            
            // ปุ่ม "พิมพ์" (Print)
            const printButton = document.getElementById('print-button');
            if (printButton) {
                printButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    // แสดง preview modal ก่อน
                    previewModal.classList.remove('modal-hidden');
                    
                    // เซ็ต CSS สำหรับการพิมพ์เฉพาะ preview content
                    const printStyles = document.createElement('style');
                    printStyles.id = 'print-styles';
                    printStyles.innerHTML = `
                        @media print {
                            body * {
                                visibility: hidden;
                            }
                            #preview-content, #preview-content * {
                                visibility: visible;
                            }
                            #preview-content {
                                position: absolute;
                                left: 0;
                                top: 0;
                                width: 100%;
                                padding: 0 40px;
                            }
                            .no-print {
                                display: none !important;
                            }
                        }
                    `;
                    
                    // เพิ่ม style สำหรับพิมพ์
                    document.head.appendChild(printStyles);
                    
                    // ตั้งค่าชื่อเอกสาร
                    const title = 'ใบเสนอราคาเลขที่ {{ $quotation->quotation_number }}';
                    document.title = title;
                    
                    // รอให้ modal แสดงผลเต็มที่ก่อนพิมพ์
                    setTimeout(() => {
                        window.print();
                        
                        // ลบ style เฉพาะการพิมพ์
                        document.getElementById('print-styles').remove();
                        
                        // ฟังก์ชั่นสำหรับถูกเรียกหลังจากพิมพ์เสร็จ
                        window.onafterprint = function() {
                            // ไม่ต้องปิด modal ถ้าผู้ใช้ต้องการดูต่อ
                            window.onafterprint = null;
                        };
                    }, 500);
                });
            }
        });
    </script>
</x-app-layout>
