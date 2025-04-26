<!-- Modal Preview -->
<div id="preview-modal" class="modal-hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="modal-content bg-white dark:bg-gray-800 rounded-lg shadow max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                ตัวอย่างใบส่งสินค้า
            </h3>
            <button id="close-preview" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div id="preview-content" class="bg-white p-8 mx-auto" style="max-width: 210mm;">
                <!-- ข้อมูลบริษัท -->
                <div style="text-align: center; margin-bottom: 20px;">
                    <h1 style="font-size: 24px; font-weight: bold;">{{ $deliveryOrder->company->name ?? 'บริษัท ซีอีโอซอฟต์ จำกัด' }}</h1>
                    <p>{{ $deliveryOrder->company->address ?? '123 ถนนสุขุมวิท แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพฯ 10110' }}</p>
                    <p>โทร: {{ $deliveryOrder->company->phone ?? '02-123-4567' }}, อีเมล: {{ $deliveryOrder->company->email ?? 'info@ceosofts.com' }}</p>
                </div>
                
                <div style="border-bottom: 2px solid #000; margin-bottom: 20px;">
                    <h2 style="text-align: center; font-size: 20px; font-weight: bold;">ใบส่งสินค้า</h2>
                </div>
                
                <!-- ข้อมูลลูกค้าและเลขที่เอกสาร -->
                <div class="grid-cols-2">
                    <div>
                        <p><strong>ลูกค้า:</strong> {{ $deliveryOrder->customer->name ?? '-' }}</p>
                        <p><strong>ชื่อผู้ติดต่อและที่อยู่จัดส่ง:</strong></p>
                        <p>{{ $deliveryOrder->shipping_address }}</p>
                        <p><strong>โทร:</strong> {{ $deliveryOrder->customer->phone ?? '-' }}</p>
                        <p><strong>อีเมล:</strong> {{ $deliveryOrder->customer->email ?? '-' }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p><strong>เลขที่:</strong> {{ $deliveryOrder->delivery_number }}</p>
                        <p><strong>วันที่:</strong> {{ optional($deliveryOrder->delivery_date)->format('d/m/Y') ?? date('d/m/Y') }}</p>
                        <p><strong>เลขที่ใบสั่งขาย:</strong> 
                            {{ $deliveryOrder->order ? $deliveryOrder->order->order_number : '-' }}
                        </p>
                        <p><strong>วิธีจัดส่ง:</strong> {{ $deliveryOrder->shipping_method ?? '-' }}</p>
                        <p><strong>เลขพัสดุ:</strong> {{ $deliveryOrder->tracking_number ?? '-' }}</p>
                    </div>
                </div>
                
                <!-- รายการสินค้า -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; margin-top: 20px;">
                    <thead>
                        <tr style="background-color: #f2f2f2;">
                            <th style="border: 1px solid #333; padding: 8px; text-align: left;">ลำดับ</th>
                            <th style="border: 1px solid #333; padding: 8px; text-align: left;">รหัสสินค้า</th>
                            <th style="border: 1px solid #333; padding: 8px; text-align: left;">รายการ</th>
                            <th style="border: 1px solid #333; padding: 8px; text-align: right;">จำนวน</th>
                            <th style="border: 1px solid #333; padding: 8px; text-align: right;">หน่วย</th>
                            <th style="border: 1px solid #333; padding: 8px; text-align: center;">สถานะ</th>
                            <th style="border: 1px solid #333; padding: 8px; text-align: right;">หมายเหตุ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deliveryOrder->deliveryOrderItems as $index => $item)
                        <tr>
                            <td style="border: 1px solid #333; padding: 8px;">{{ $index + 1 }}</td>
                            <td style="border: 1px solid #333; padding: 8px;">
                                @if($item->product)
                                    {{ $item->product->code ?? $item->product->sku ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td style="border: 1px solid #333; padding: 8px;">{{ $item->description }}</td>
                            <td style="border: 1px solid #333; padding: 8px; text-align: right;">{{ number_format($item->quantity) }}</td>
                            <td style="border: 1px solid #333; padding: 8px; text-align: right;">{{ $item->unit }}</td>
                            <td style="border: 1px solid #333; padding: 8px; text-align: center;">
                                @php
                                    $statusClass = isset($statusClasses[$item->status]) ? $statusClasses[$item->status] : 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 py-1 rounded-full text-xs {{ $statusClass }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td style="border: 1px solid #333; padding: 8px; text-align: right;">{{ $item->notes ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="border: 1px solid #333; padding: 8px; text-align: center;">ไม่มีรายการสินค้า</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                
                <!-- หมายเหตุ -->
                @if($deliveryOrder->notes)
                <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; border-radius: 4px;">
                    <strong>หมายเหตุ:</strong>
                    <p>{{ $deliveryOrder->notes }}</p>
                </div>
                @endif
                
                <!-- ส่วนลงนาม -->
                <div style="display: flex; justify-content: space-between; margin-top: 50px;">
                    <div style="text-align: center; width: 40%;">
                        <div style="border-top: 1px solid #000; width: 70%; margin: 50px auto 0; padding-top: 10px;">
                            ผู้ส่งมอบสินค้า
                        </div>
                    </div>
                    <div style="text-align: center; width: 40%;">
                        <div style="border-top: 1px solid #000; width: 70%; margin: 50px auto 0; padding-top: 10px;">
                            ผู้รับสินค้า
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- <div class="px-4 py-3 sm:px-6 border-t border-gray-200 dark:border-gray-700 flex justify-end bg-gray-50 dark:bg-gray-700">
            <button id="print-preview" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                พิมพ์เอกสาร
            </button>
        </div> -->
    </div>
</div>
