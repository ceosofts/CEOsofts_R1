<!DOCTYPE html>
<html>
<head>
    <title>ใบส่งสินค้าเลขที่ {{ $deliveryOrder->delivery_number }}</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: 'Sarabun', sans-serif;
            margin: 0;
            padding: 15px;
            font-size: 10.5pt;
            line-height: 1.1;
        }
        .print-content {
            max-width: 210mm;
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #333;
            padding: 5px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
            font-size: 10pt;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 8px;
            margin-top: 0;
        }
        h2 {
            font-size: 18px;
            margin-bottom: 10px;
            margin-top: 5px;
        }
        p {
            margin: 3px 0;
        }
        .company-header {
            text-align: center;
            margin-bottom: 10px;
        }
        .document-header {
            border-bottom: 2px solid #000;
            margin-bottom: 10px;
            padding-bottom: 5px;
            text-align: center;
        }
        .party-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .party-info > div {
            width: 48%;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        .signature-box {
            text-align: center;
            width: 40%;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 70%;
            margin: 40px auto 0;
            padding-top: 5px;
        }
        .notes-box {
            margin-top: 15px;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        .notes-box p {
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="print-content">
        <!-- ข้อมูลบริษัท -->
        <div class="company-header">
            <h1>{{ $deliveryOrder->company->name ?? 'บริษัท ซีอีโอซอฟต์ จำกัด' }}</h1>
            <p>{{ $deliveryOrder->company->address ?? '123 ถนนสุขุมวิท แขวงคลองตันเหนือ เขตวัฒนา กรุงเทพฯ 10110' }}</p>
            <p>โทร: {{ $deliveryOrder->company->phone ?? '02-123-4567' }}, อีเมล: {{ $deliveryOrder->company->email ?? 'info@ceosofts.com' }}</p>
        </div>

        <div class="document-header">
            <h2>ใบส่งสินค้า</h2>
        </div>

        <!-- ข้อมูลลูกค้าและเลขที่เอกสาร -->
        <div class="party-info">
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
        <table>
            <thead>
                <tr>
                    <th style="text-align: center; width: 40px;">ลำดับ</th>
                    <th style="text-align: left; width: 80px;">รหัสสินค้า</th>
                    <th style="text-align: left;">รายการ</th>
                    <th style="text-align: right; width: 60px;">จำนวน</th>
                    <th style="text-align: right; width: 60px;">หน่วย</th>
                    <th style="text-align: center; width: 80px;">สถานะ</th>
                    <th style="text-align: right; width: 100px;">หมายเหตุ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveryOrder->deliveryOrderItems as $index => $item)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>
                        @if($item->product)
                            {{ $item->product->code ?? $item->product->sku ?? '-' }}
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $item->description }}</td>
                    <td style="text-align: right;">{{ number_format($item->quantity) }}</td>
                    <td style="text-align: right;">{{ $item->unit }}</td>
                    <td style="text-align: center;">
                        {{ ucfirst($item->status) }}
                    </td>
                    <td style="text-align: right;">{{ $item->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">ไม่มีรายการสินค้า</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- หมายเหตุ -->
        @if($deliveryOrder->notes)
        <div class="notes-box">
            <strong>หมายเหตุ:</strong>
            <p>{{ $deliveryOrder->notes }}</p>
        </div>
        @endif

        <!-- ส่วนลงนาม -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    ผู้ส่งมอบสินค้า
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    ผู้รับสินค้า
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // พิมพ์อัตโนมัติเมื่อโหลดเสร็จ
        window.onload = function() {
            setTimeout(function() {
                window.print();
                // หลังจากพิมพ์แล้ว รอสักครู่แล้วปิดหน้าต่าง (ถ้าต้องการ)
                // setTimeout(function() { window.close(); }, 500);
            }, 500);
        };
    </script>
</body>
</html>
