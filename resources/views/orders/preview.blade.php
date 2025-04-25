<!-- Modal สำหรับดูตัวอย่างใบสั่งขาย -->
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
                <h1 class="text-xl font-bold">{{ $order->company->name ?? 'บริษัท ซีอีโอซอฟต์ จำกัด' }}</h1>
                <p>{{ $order->company->address ?? '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110' }}</p>
                <p>โทร: {{ $order->company->phone ?? '02-123-4567' }}, อีเมล: {{ $order->company->email ?? 'info@ceosofts.com' }}</p>
            </div>

            <div class="border-b-2 border-gray-800 mb-6">
                <h2 class="text-center text-2xl font-bold">ใบสั่งขาย</h2>
            </div>

            <!-- ข้อมูลลูกค้าและเลขที่เอกสาร -->
            <div class="grid-cols-2 mb-6">
                <div>
                    <p><strong>ลูกค้า:</strong> {{ $order->customer->name }}</p>
                    <p>{{ $order->customer->address }}</p>
                    <p>โทร: {{ $order->customer->phone }}</p>
                    <p>อีเมล: {{ $order->customer->email }}</p>
                </div>
                <div class="text-right">
                    <p><strong>เลขที่:</strong> {{ $order->order_number }}</p>
                    <p><strong>วันที่:</strong> {{ $order->order_date->format('d/m/Y') }}</p>
                    <p><strong>วันที่จัดส่ง:</strong> {{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</p>
                    <p><strong>อ้างอิง:</strong> {{ $order->customer_po_number ?: '-' }}</p>
                    <!-- แสดงพนักงานขาย -->
                    <p><strong>พนักงานขาย:</strong> 
                        @if($order->sales_person_id && $salesPerson = \App\Models\Employee::find($order->sales_person_id))
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
                        <th class="py-2 px-4 border text-right">จำนวนเงิน</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $index => $item)
                    <tr>
                        <td class="py-2 px-4 border">{{ $index + 1 }}</td>
                        <td class="py-2 px-4 border">
                            {{ $item->product->code ?? $item->product->sku ?? '-' }}
                        </td>
                        <td class="py-2 px-4 border">{{ $item->description ?? $item->product->name }}</td>
                        <td class="py-2 px-4 border text-right">{{ number_format($item->quantity, 2) }} {{ $item->unit->name ?? '' }}</td>
                        <td class="py-2 px-4 border text-right">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="py-2 px-4 border text-right">{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-2 px-4 border text-center">ไม่มีรายการสินค้า</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- สรุปยอดรวม -->
            <div class="flex justify-end mb-6">
                <div class="w-1/3">
                    <div class="flex justify-between py-2 border-b">
                        <span>ยอดรวมก่อนภาษี</span>
                        <span>{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between py-2 border-b">
                        <span>ส่วนลด
                            @if($order->discount_type == 'percentage')
                            ({{ $order->discount_amount }}%)
                            @endif
                        </span>
                        <span>{{ number_format($order->discount_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($order->tax_amount > 0)
                    <div class="flex justify-between py-2 border-b">
                        <span>ภาษีมูลค่าเพิ่ม ({{ $order->tax_rate }}%)</span>
                        <span>{{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    @endif
                    @if($order->shipping_cost > 0)
                    <div class="flex justify-between py-2 border-b">
                        <span>ค่าขนส่ง</span>
                        <span>{{ number_format($order->shipping_cost, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between py-3 font-bold">
                        <span>ยอดรวมทั้งสิ้น</span>
                        <span>{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- หมายเหตุ -->
            @if($order->notes)
            <div class="mb-6">
                <h4 class="font-semibold mb-2">หมายเหตุ</h4>
                <p class="p-3 border rounded">{{ $order->notes }}</p>
            </div>
            @endif

            <!-- ส่วนลงนาม -->
            <div class="grid grid-cols-2 gap-6 mt-12">
                <div class="text-center">
                    <div class="border-t border-gray-400 pt-2 mt-12 inline-block w-48">
                        <p>ผู้สั่งซื้อ</p>
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
