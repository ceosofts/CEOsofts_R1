<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสนอราคาเลขที่ {{ $quotation->quotation_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* ปรับปรุง CSS ให้เหมือนกับ "ดูตัวอย่าง" */
        body {
            font-family: 'Sarabun', sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            border: 1px solid #ddd;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .address {
            margin-bottom: 20px;
        }
        
        .divider {
            border-bottom: 2px solid #000;
            margin: 20px 0;
        }
        
        .section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            width: 100%;
            padding: 0;
        }
        
        .left {
            width: 48%;
        }
        
        .right {
            width: 48%;
            text-align: right;
            padding-right: 0; /* ลบ padding ขวา */
        }
        
        /* แก้ไขตารางให้เหมือนกับ "ดูตัวอย่าง" */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 8px 12px;
            color: black;
        }
        
        th {
            background-color: #f2f2f2;
            color: black;
            font-weight: bold;
            text-align: left;
        }
        
        .amount-summary {
            margin-left: auto;
            width: 300px;
        }
        
        .row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }
        
        .row span:first-child {
            width: 60%;
        }
        
        .row span:last-child {
            width: 38%;
            text-align: right;
            padding-right: 5px;
        }
        
        .total-row {
            font-weight: bold;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
        }
        
        .total-row span:first-child {
            width: 60%;
        }
        
        .total-row span:last-child {
            width: 38%;
            text-align: right;
            padding-right: 5px;
        }
        
        .notes {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 4px;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        
        .signature {
            text-align: center;
            width: 40%;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 70%;
            margin: 50px auto 0;
            padding-top: 10px;
        }
        
        .toolbar {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 100;
            display: flex;
            gap: 10px;
        }
        
        .action-button {
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-family: 'Sarabun', sans-serif;
        }
        
        .print-button {
            background-color: #4299E1; /* สีฟ้า */
        }
        
        .return-button {
            background-color: #718096; /* สีเทา */
        }
        
        .action-button:hover {
            opacity: 0.9;
        }
        
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 20px;
            }
            
            .container {
                margin: 0;
                padding: 10px;
                border: none;
                box-shadow: none;
                max-width: 100%;
            }
            
            .toolbar {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="action-button print-button" onclick="window.print();">พิมพ์เอกสาร</button>
        <button class="action-button return-button" onclick="window.history.back();">กลับไปหน้าก่อนหน้า</button>
    </div>
    
    <div class="container">
        @if(isset($error))
        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 4px;">
            <strong>เกิดข้อผิดพลาด:</strong> {{ $error }}
        </div>
        @endif
        
        <div class="header">
            <div class="title">{{ $company->name ?? 'บริษัท ซีอีโอซอฟต์ จำกัด' }}</div>
            <div class="address">{{ $company->address ?? '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพฯ 10110' }}</div>
            <div>โทร: {{ $company->phone ?? '02-123-4567' }} | อีเมล: {{ $company->email ?? 'info@ceosofts.com' }}</div>
        </div>

        <div class="divider"></div>
        <div class="title" style="text-align: center;">ใบเสนอราคา</div>
        <div class="divider"></div>

        <div class="section">
            <div class="left">
                <strong>ลูกค้า:</strong> {{ $quotation->customer->name }}<br>
                {{ $quotation->customer->address }}<br>
                โทร: {{ $quotation->customer->phone }}<br>
                อีเมล: {{ $quotation->customer->email }}
            </div>
            <div class="right">
                <strong>เลขที่:</strong> {{ $quotation->quotation_number }}<br>
                <strong>วันที่:</strong> {{ $quotation->issue_date->format('d/m/Y') }}<br>
                <strong>วันที่หมดอายุ:</strong> {{ $quotation->expiry_date->format('d/m/Y') }}<br>
                <strong>อ้างอิง:</strong> {{ $quotation->reference_number ?: '-' }}<br>
                <!-- เพิ่มพนักงานขายในส่วน PDF view -->
                <strong>พนักงานขาย:</strong>
                @if($quotation->sales_person_id && $salesPerson = \App\Models\Employee::find($quotation->sales_person_id))
                    {{ $salesPerson->employee_code }} - {{ $salesPerson->first_name }} {{ $salesPerson->last_name }}
                @else
                    -
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ลำดับ</th>
                    <th style="width: 15%;">รหัสสินค้า</th>
                    <th style="width: 25%;">รายการ</th>
                    <th style="width: 10%;">จำนวน</th>
                    <th style="width: 15%;">ราคาต่อหน่วย</th>
                    <th style="width: 15%;">ส่วนลด</th>
                    <th style="width: 15%;">จำนวนเงิน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quotation->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product->code ?? '-' }}</td>
                    <td>{{ $item->description }}</td>
                    <td style="text-align: right;">{{ number_format($item->quantity, 2) }} {{ $item->unit->name ?? '' }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">
                        @if($item->discount_percentage > 0)
                            {{ number_format($item->discount_percentage, 2) }}%
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: right;">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">ไม่มีรายการสินค้า</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="amount-summary">
            <div class="row">
                <span>ยอดรวมก่อนภาษี</span>
                <span>{{ number_format($quotation->subtotal, 2) }}</span>
            </div>
            @if($quotation->discount_amount > 0)
            <div class="row">
                <span>ส่วนลด
                    @if($quotation->discount_type == 'percentage')
                    ({{ $quotation->discount_amount }}%)
                    @endif
                </span>
                <span>{{ number_format($quotation->discount_amount, 2) }}</span>
            </div>
            @endif
            @if($quotation->tax_amount > 0)
            <div class="row">
                <span>ภาษีมูลค่าเพิ่ม ({{ $quotation->tax_rate }}%)</span>
                <span>{{ number_format($quotation->tax_amount, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between py-3 font-bold">
                <span class="total-label" style="text-align: left; padding-left: 5mm;">ยอดรวมทั้งสิ้น</span>
                <span class="total-amount">{{ number_format($quotation->total_amount, 2) }}</span>
            </div>
        </div>

        @if($quotation->notes)
        <div class="notes">
            <strong>หมายเหตุ:</strong>
            <p>{{ $quotation->notes }}</p>
        </div>
        @endif

        <div class="signatures">
            <div class="signature">
                <div class="signature-line">ผู้เสนอราคา</div>
            </div>
            <div class="signature">
                <div class="signature-line">ผู้มีอำนาจลงนาม</div>
            </div>
        </div>
    </div>
    
    <script>
        // เพิ่ม Event listener สำหรับการพิมพ์
        document.addEventListener('DOMContentLoaded', function() {
            // ตั้งชื่อเอกสารก่อนพิมพ์เสมอ
            window.addEventListener('beforeprint', function() {
                document.title = 'ใบเสนอราคาเลขที่ {{ $quotation->quotation_number }}';
            });

            // เพิ่ม event listener สำหรับปุ่มพิมพ์
            const printButton = document.querySelector('.print-button');
            if (printButton) {
                printButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.title = 'ใบเสนอราคาเลขที่ {{ $quotation->quotation_number }}';
                    window.print();
                });
            }
        });
    </script>
</body>
</html>
