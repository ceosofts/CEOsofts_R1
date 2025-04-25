<div id="printable-area" style="display: none;">
    <div class="print-content">
        <!-- ข้อมูลบริษัท -->
        <div style="text-align: center; margin-bottom: 20px;">
            <h1 style="font-size: 24px; font-weight: bold;">{{ $order->company->name ?? 'บริษัท ซีอีโอซอฟต์ จำกัด' }}</h1>
            <p>{{ $order->company->address ?? '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110' }}</p>
            <p>โทร: {{ $order->company->phone ?? '02-123-4567' }}, อีเมล: {{ $order->company->email ?? 'info@ceosofts.com' }}</p>
        </div>

        <div style="border-bottom: 2px solid #000; margin-bottom: 20px;">
            <h2 style="text-align: center; font-size: 20px; font-weight: bold;">ใบสั่งขาย</h2>
        </div>

        <!-- ข้อมูลลูกค้าและเลขที่เอกสาร -->
        <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
            <div style="width: 48%;">
                <p><strong>ลูกค้า:</strong> {{ $order->customer->name }}</p>
                <p>{{ $order->customer->address }}</p>
                <p>โทร: {{ $order->customer->phone }}</p>
                <p>อีเมล: {{ $order->customer->email }}</p>
            </div>
            <div style="width: 48%; text-align: right;">
                <p><strong>เลขที่:</strong> {{ $order->order_number }}</p>
                <p><strong>วันที่:</strong> {{ $order->order_date->format('d/m/Y') }}</p>
                <p><strong>วันที่จัดส่ง:</strong> {{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}</p>
                <p><strong>อ้างอิง:</strong> {{ $order->customer_po_number ?: '-' }}</p>
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
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #333; padding: 8px; text-align: left;">ลำดับ</th>
                    <th style="border: 1px solid #333; padding: 8px; text-align: left;">รหัสสินค้า</th>
                    <th style="border: 1px solid #333; padding: 8px; text-align: left;">รายการ</th>
                    <th style="border: 1px solid #333; padding: 8px; text-align: right;">จำนวน</th>
                    <th style="border: 1px solid #333; padding: 8px; text-align: right;">ราคาต่อหน่วย</th>
                    <th style="border: 1px solid #333; padding: 8px; text-align: right;">จำนวนเงิน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $index => $item)
                <tr>
                    <td style="border: 1px solid #333; padding: 8px;">{{ $index + 1 }}</td>
                    <td style="border: 1px solid #333; padding: 8px;">{{ $item->product->code ?? '-' }}</td>
                    <td style="border: 1px solid #333; padding: 8px;">{{ $item->description }}</td>
                    <td style="border: 1px solid #333; padding: 8px; text-align: right;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="border: 1px solid #333; padding: 8px; text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="border: 1px solid #333; padding: 8px; text-align: right;">{{ number_format($item->total, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="border: 1px solid #333; padding: 8px; text-align: center;">ไม่พบรายการสินค้า</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- สรุปยอดเงิน -->
        <div style="display: flex; justify-content: flex-end; margin-bottom: 20px;">
            <div style="width: 300px;">
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding: 5px 0;">
                    <span>ยอดรวมก่อนภาษี</span>
                    <span>{{ number_format($order->subtotal, 2) }}</span>
                </div>
                @if($order->discount_amount > 0)
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding: 5px 0;">
                    <span>ส่วนลด</span>
                    <span>{{ number_format($order->discount_amount, 2) }}</span>
                </div>
                @endif
                @if($order->tax_amount > 0)
                <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #ddd; padding: 5px 0;">
                    <span>ภาษีมูลค่าเพิ่ม ({{ $order->tax_rate }}%)</span>
                    <span>{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; font-weight: bold; padding: 10px 0;">
                    <span>ยอดรวมทั้งสิ้น</span>
                    <span>{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        @if($order->notes)
        <div style="margin-top: 20px; padding: 10px; border: 1px solid #ddd; background-color: #f9f9f9; border-radius: 4px;">
            <strong>หมายเหตุ:</strong>
            <p>{{ $order->notes }}</p>
        </div>
        @endif

        <!-- ส่วนลงนาม -->
        <div style="display: flex; justify-content: space-between; margin-top: 50px;">
            <div style="text-align: center; width: 40%;">
                <div style="border-top: 1px solid #000; width: 70%; margin: 50px auto 0; padding-top: 10px;">
                    ผู้สั่งขาย
                </div>
            </div>
            <div style="text-align: center; width: 40%;">
                <div style="border-top: 1px solid #000; width: 70%; margin: 50px auto 0; padding-top: 10px;">
                    ผู้มีอำนาจลงนาม
                </div>
            </div>
        </div>
    </div>
</div>
