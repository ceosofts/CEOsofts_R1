<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>ใบเสนอราคาเลขที่ {{ $quotation->quotation_number }}</title>
    <style>
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: normal;
            src: url("{{ storage_path('fonts/THSarabunNew.ttf') }}") format('truetype');
        }
        @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ storage_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
        }
        body {
            font-family: 'THSarabunNew', sans-serif;
            font-size: 16px;
            line-height: 1.5;
            margin: 0;
            padding: 1cm;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mb-6 { margin-bottom: 20px; }
        .mt-12 { margin-top: 40px; }
        h1 { font-size: 24px; }
        h2 { font-size: 20px; }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 8px; 
        }
        th { 
            background-color: #f2f2f2; 
        }
        .grid {
            display: flex;
            flex-wrap: wrap;
        }
        .grid-cols-2 {
            display: flex;
            justify-content: space-between;
        }
        .grid-cols-2 > div {
            width: 48%;
        }
        .border-b {
            border-bottom: 1px solid #ddd;
            padding: 5px 0;
        }
        .font-bold {
            font-weight: bold;
        }
        .signature-line {
            border-top: 1px solid #ddd;
            width: 160px;
            margin: 40px auto 0;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- ข้อมูลบริษัท -->
    <div class="text-center mb-6">
        <h1>{{ $company->name ?? 'บริษัท ซีอีโอซอฟต์ จำกัด' }}</h1>
        <p>{{ $company->address ?? '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110' }}</p>
        <p>โทร: {{ $company->phone ?? '02-123-4567' }}, อีเมล: {{ $company->email ?? 'info@ceosofts.com' }}</p>
    </div>

    <div style="border-bottom: 2px solid #000; margin-bottom: 20px;">
        <h2 class="text-center">ใบเสนอราคา</h2>
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
        </div>
    </div>

    <!-- รายการสินค้า -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">ลำดับ</th>
                <th style="width: 15%;">รหัส</th>
                <th style="width: 25%;">รายการ</th>
                <th style="width: 10%;" class="text-right">จำนวน</th>
                <th style="width: 15%;" class="text-right">ราคาต่อหน่วย</th>
                <th style="width: 15%;" class="text-right">ส่วนลด</th>
                <th style="width: 15%;" class="text-right">จำนวนเงิน</th>
            </tr>
        </thead>
        <tbody>
            @forelse($quotation->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->product->code ?? '' }}</td>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ number_format($item->quantity, 2) }} {{ $item->unit->name ?? '' }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">
                    @if($item->discount_percentage > 0)
                        {{ number_format($item->discount_percentage, 2) }}%
                    @else
                        -
                    @endif
                </td>
                <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">ไม่มีรายการสินค้า</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- สรุปยอดรวม -->
    <div style="margin-left: auto; width: 300px;">
        <div class="border-b" style="display: flex; justify-content: space-between;">
            <span>ยอดรวมก่อนภาษี</span>
            <span>{{ number_format($quotation->subtotal, 2) }}</span>
        </div>
        @if($quotation->discount_amount > 0)
        <div class="border-b" style="display: flex; justify-content: space-between;">
            <span>ส่วนลด
                @if($quotation->discount_type == 'percentage')
                ({{ $quotation->discount_amount }}%)
                @endif
            </span>
            <span>{{ number_format($quotation->discount_amount, 2) }}</span>
        </div>
        @endif
        @if($quotation->tax_amount > 0)
        <div class="border-b" style="display: flex; justify-content: space-between;">
            <span>ภาษีมูลค่าเพิ่ม ({{ $quotation->tax_rate }}%)</span>
            <span>{{ number_format($quotation->tax_amount, 2) }}</span>
        </div>
        @endif
        <div style="display: flex; justify-content: space-between; font-weight: bold; padding: 5px 0;">
            <span>ยอดรวมทั้งสิ้น</span>
            <span>{{ number_format($quotation->total_amount, 2) }}</span>
        </div>
    </div>

    <!-- หมายเหตุ -->
    @if($quotation->notes)
    <div class="mb-6">
        <h4 style="font-weight: bold; margin-bottom: 5px;">หมายเหตุ</h4>
        <p style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;">{{ $quotation->notes }}</p>
    </div>
    @endif

    <!-- ส่วนลงนาม -->
    <div class="grid-cols-2 mt-12">
        <div class="text-center">
            <div class="signature-line">
                <p>ผู้เสนอราคา</p>
            </div>
        </div>
        <div class="text-center">
            <div class="signature-line">
                <p>ล๔กค้าผู้มีอำนาจลงนาม</p>
            </div>
        </div>
    </div>
</body>
</html>
