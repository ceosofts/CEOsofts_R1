<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบแจ้งหนี้เลขที่ {{ $invoice->invoice_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* ปรับปรุง CSS ให้เหมาะกับกระดาษ A4 */
        @page {
            size: A4;
            margin: 10mm;
        }
        body {
            font-family: 'Sarabun', sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
            font-size: 12px;
        }
        
        .container {
            max-width: 210mm;
            margin: 10px auto;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .address {
            margin-bottom: 10px;
            font-size: 11px;
        }
        
        .divider {
            border-bottom: 1px solid #000;
            margin: 10px 0;
        }
        
        .section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            width: 100%;
            padding: 0;
        }
        
        .left {
            width: 48%;
        }
        
        .right {
            width: 48%;
            text-align: right;
            padding-right: 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            box-sizing: border-box;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 5px 8px;
            color: black;
            font-size: 11px;
        }
        
        th {
            background-color: #f2f2f2;
            color: black;
            font-weight: bold;
            text-align: left;
        }
        
        .amount-summary {
            margin-left: auto;
            width: 250px;
        }
        
        .row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
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
            padding: 5px 0;
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
            margin-top: 10px;
            padding: 8px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            border-radius: 4px;
            font-size: 11px;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        
        .signature {
            text-align: center;
            width: 30%;
            font-size: 11px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 30px auto 0;
            padding-top: 5px;
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
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
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
                padding: 0;
            }
            
            .container {
                margin: 0;
                padding: 5px;
                border: none;
                box-shadow: none;
                max-width: 100%;
            }
            
            .toolbar {
                display: none !important;
            }

            /* ลดระยะห่างระหว่างส่วนต่างๆ เพื่อประหยัดพื้นที่ */
            .divider {
                margin: 5px 0;
            }

            .section {
                margin-bottom: 10px;
            }

            .signatures {
                margin-top: 15px;
            }

            .signature-line {
                margin: 20px auto 0;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="action-button print-button">พิมพ์เอกสาร</button>
        <button class="action-button return-button" onclick="window.history.back();">กลับไปหน้าก่อนหน้า</button>
    </div>
    
    <div class="container">
        <div class="header">
            <div class="title">{{ $company->company_name ?? $company->name ?? config('company.name', '') }}</div>
            <div class="address">{{ $company->address ?? config('company.address', '') }}</div>
            <div style="font-size: 11px;">
                โทร: {{ $company->phone ?? config('company.phone', '') }} | 
                อีเมล: {{ $company->email ?? config('company.email', '') }} |
                เลขประจำตัวผู้เสียภาษี: {{ $company->tax_id ?? config('company.tax_id', '') }}
            </div>
        </div>

        <div class="divider"></div>
        <div class="title" style="text-align: center; font-size: 18px;">ใบแจ้งหนี้</div>
        <div class="divider"></div>

        <div class="section">
            <div class="left">
                <strong>ลูกค้า:</strong> {{ $invoice->customer->name }}<br>
                <span style="font-size: 11px;">{{ $invoice->customer->address }}</span><br>
                <span style="font-size: 11px;">โทร: {{ $invoice->customer->phone }}</span><br>
                <span style="font-size: 11px;">อีเมล: {{ $invoice->customer->email }}</span>
            </div>
            <div class="right">
                <strong>เลขที่:</strong> {{ $invoice->invoice_number }}<br>
                <strong>วันที่:</strong> {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y') }}<br>
                <strong>วันครบกำหนดชำระ:</strong> {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d/m/Y') : '-' }}<br>
                <strong>อ้างอิง:</strong> {{ $invoice->reference_number ?: '-' }}<br>
                <strong>พนักงานขาย:</strong>
                @if(isset($invoice->salesPerson))
                    {{ $invoice->salesPerson->employee_code ?? '' }} - {{ $invoice->salesPerson->first_name }} {{ $invoice->salesPerson->last_name }}
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
                    <th style="width: 30%;">รายการ</th>
                    <th style="width: 10%; text-align: right;">จำนวน</th>
                    <th style="width: 10%; text-align: center;">หน่วย</th>
                    <th style="width: 15%; text-align: right;">ราคาต่อหน่วย</th>
                    <th style="width: 15%; text-align: right;">จำนวนเงิน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product ? ($item->product->code ?? $item->product->sku ?? '-') : '-' }}</td>
                    <td>{{ $item->product ? $item->product->name : $item->description }}</td>
                    <td style="text-align: right;">{{ number_format($item->quantity, 2) }}</td>
                    <td style="text-align: center;">{{ $item->unit }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center;">ไม่มีรายการสินค้า</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between;">
            <div style="width: 55%;">
                @if($invoice->payment_terms || $invoice->notes || $invoice->shipping_address)
                <div class="notes">
                    @if($invoice->payment_terms)
                    <strong>เงื่อนไขการชำระเงิน:</strong>
                    <p style="margin: 3px 0;">{{ $invoice->payment_terms }}</p>
                    @endif
                    
                    @if($invoice->notes)
                    <strong>หมายเหตุ:</strong>
                    <p style="margin: 3px 0;">{{ $invoice->notes }}</p>
                    @endif
                    
                    @if($invoice->shipping_address)
                    <strong>ที่อยู่จัดส่ง:</strong>
                    <p style="margin: 3px 0;">{{ $invoice->shipping_address }}</p>
                    @endif
                </div>
                @endif
            </div>
            
            <div class="amount-summary">
                <div class="row">
                    <span>ยอดรวมก่อนภาษี</span>
                    <span>{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                
                @if($invoice->discount_amount > 0)
                <div class="row">
                    <span>ส่วนลด
                        @if($invoice->discount_type == 'percentage')
                        ({{ $invoice->discount_amount }}%)
                        @endif
                    </span>
                    <span>{{ number_format($invoice->discount_value, 2) }}</span>
                </div>
                @endif
                
                @if($invoice->shipping_cost > 0)
                <div class="row">
                    <span>ค่าขนส่ง</span>
                    <span>{{ number_format($invoice->shipping_cost, 2) }}</span>
                </div>
                @endif
                
                @if($invoice->tax_rate > 0)
                <div class="row">
                    <span>ภาษีมูลค่าเพิ่ม ({{ $invoice->tax_rate }}%)</span>
                    <span>{{ number_format($invoice->tax_amount, 2) }}</span>
                </div>
                @endif
                
                <div class="total-row">
                    <span class="total-label" style="text-align: left; padding-left: 5mm;">ยอดรวมทั้งสิ้น</span>
                    <span class="total-amount">{{ number_format($invoice->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="signatures">
            <div class="signature">
                <div class="signature-line">ลงชื่อผู้รับสินค้า</div>
                <div style="margin-top: 5px;">วันที่: ______/______/______</div>
            </div>
            
            <div class="signature">
                <div class="signature-line">ลงชื่อผู้ส่งสินค้า</div>
                <div style="margin-top: 5px;">วันที่: ______/______/______</div>
            </div>
            
            <div class="signature">
                <div class="signature-line">ลงชื่อผู้มีอำนาจลงนาม</div>
                <div style="margin-top: 5px;">วันที่: ______/______/______</div>
            </div>
        </div>
    </div>
    
    <script>
        // เพิ่ม Event listener สำหรับการพิมพ์
        document.addEventListener('DOMContentLoaded', function() {
            // ตั้งชื่อเอกสารก่อนพิมพ์เสมอ
            window.addEventListener('beforeprint', function() {
                document.title = 'ใบแจ้งหนี้เลขที่ {{ $invoice->invoice_number }}';
            });

            // เพิ่ม event listener สำหรับปุ่มพิมพ์
            const printButton = document.querySelector('.print-button');
            if (printButton) {
                printButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.title = 'ใบแจ้งหนี้เลขที่ {{ $invoice->invoice_number }}';
                    window.print();
                });
            }
        });
    </script>
</body>
</html>