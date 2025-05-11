<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบสั่งขายเลขที่ {{ $order->order_number }}</title>
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
            margin: 20px auto;
            padding: 20px 30px;
            border: 1px solid #ddd;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 5px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 0 15px;
        }
        
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 18px;
            font-weight: bold;
            margin: 12px 0;
        }
        
        .header p {
            margin: 4px 0;
            font-size: 12px;
        }
        
        .divider {
            border-bottom: 1px solid #000;
            margin: 15px 0;
        }
        
        .section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            width: 100%;
            padding: 0;
            font-size: 12px;
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
            font-size: 11px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 4px 8px;
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
            width: 280px;
            font-size: 11px;
        }
        
        .row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px solid #ddd;
        }
        
        .row span:first-child {
            width: 62%;
        }
        
        .row span:last-child {
            width: 36%;
            text-align: right;
            padding-right: 3px;
        }
        
        .mt-6 {
            margin-top: 1.2rem;
        }
        
        .mb-2 {
            margin-bottom: 0.5rem;
        }
        
        .font-semibold {
            font-weight: 600;
        }
        
        .p-3 {
            padding: 0.5rem;
        }
        
        .border {
            border: 1px solid #e2e8f0;
        }
        
        .rounded {
            border-radius: 0.25rem;
        }
        
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }
        
        .signature {
            text-align: center;
            width: 40%;
            font-size: 12px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            width: 70%;
            margin: 35px auto 0;
            padding-top: 6px;
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
            padding: 8px 16px;
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

        .text-2xl {
            font-size: 1.1rem;
        }
        
        .font-bold {
            font-weight: 700;
        }
        
        .my-4 {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }
        
        .mt-12 {
            margin-top: 1.5rem;
        }
        
        .text-lg {
            font-size: 0.9rem;
        }
        
        .font-semibold {
            font-weight: 600;
        }
        
        .mb-3 {
            margin-bottom: 0.5rem;
        }
        
        .title {
            font-size: 16px;
            font-weight: bold;
        }
        
        .address, .notes p {
            font-size: 11px;
            line-height: 1.3;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 2px solid #000;
            font-weight: bold;
        }
        
        .total-row span:first-child {
            width: 62%;
        }
        
        .total-row span:last-child {
            width: 36%;
            text-align: right;
            padding-right: 3px;
        }
        
        @media print {
            body {
                background-color: white;
                margin: 0;
                padding: 0;
                font-size: 12px;
                line-height: 1.4;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            
            .container {
                margin: 0 auto;
                padding: 5mm;
                border: none;
                box-shadow: none;
                max-width: 100%;
                width: 180mm; /* ลดความกว้างลงเพื่อให้มีระยะห่างจากขอบ */
            }
            
            table {
                font-size: 10px; /* ลดขนาดตัวอักษรลงเล็กน้อยเพื่อให้ข้อมูลพอดีกับตาราง */
                width: 100%;
                table-layout: fixed;
                page-break-inside: avoid; /* ป้องกันการตัดตารางระหว่างหน้า */
                margin-bottom: 10px;
            }
            
            th, td {
                padding: 3px 6px; /* ลดขนาด padding ลงเพื่อให้ตารางกระชับขึ้น */
                overflow: hidden;
                word-wrap: break-word;
                max-width: 100%;
            }
            
            th {
                background-color: #f2f2f2 !important; /* บังคับให้สีพื้นหลังแสดงผล */
            }
            
            /* ปรับขนาดความกว้างคอลัมน์ให้เหมาะสมยิ่งขึ้น */
            th:nth-child(1), td:nth-child(1) { width: 5%; } /* ลำดับ */
            th:nth-child(2), td:nth-child(2) { width: 15%; } /* รหัสสินค้า */
            th:nth-child(3), td:nth-child(3) { width: 35%; } /* รายการ */
            th:nth-child(4), td:nth-child(4) { width: 15%; } /* จำนวน */
            th:nth-child(5), td:nth-child(5) { width: 15%; } /* ราคาต่อหน่วย */
            th:nth-child(6), td:nth-child(6) { width: 15%; } /* จำนวนเงิน */
            
            .section {
                font-size: 12px;
                margin-bottom: 10px;
            }
            
            .title {
                font-size: 16px;
            }
            
            .address, .notes p {
                font-size: 10px;
            }
            
            .amount-summary {
                font-size: 10px;
                width: 250px; /* ลดความกว้างลงเล็กน้อย */
            }
            
            .signatures {
                margin-top: 25px;
            }
            
            .signature-line {
                margin-top: 25px;
            }
            
            .toolbar {
                display: none !important;
            }
            
            /* ป้องกันการตัดหน้าในที่ไม่ต้องการ */
            .header, .signatures {
                page-break-inside: avoid;
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
            <div style="font-size: 10px;">
                โทร: {{ $company->phone ?? config('company.phone', '') }} | 
                อีเมล: {{ $company->email ?? config('company.email', '') }}
                @if($company && $company->tax_id)
                | เลขประจำตัวผู้เสียภาษี: {{ $company->tax_id }}
                @endif
            </div>
        </div>

        <div class="title" style="text-align: center; font-size: 16px;">ใบสั่งขาย</div>

        <div class="section">
            <div class="left">
                <strong>ลูกค้า:</strong> {{ $order->customer->name }}<br>
                <span style="font-size: 10px;">{{ $order->customer->address }}</span><br>
                <span style="font-size: 10px;">โทร: {{ $order->customer->phone }}</span><br>
                <span style="font-size: 10px;">อีเมล: {{ $order->customer->email }}</span>
            </div>
            <div class="right">
                <strong>เลขที่:</strong> {{ $order->order_number }}<br>
                <strong>วันที่:</strong> {{ $order->order_date->format('d/m/Y') }}<br>
                <strong>กำหนดส่งมอบ:</strong> {{ $order->delivery_date ? $order->delivery_date->format('d/m/Y') : '-' }}<br>
                @if($order->customer_po_number)
                <strong>เลขที่ใบสั่งซื้อลูกค้า:</strong> {{ $order->customer_po_number }}<br>
                @endif
                @if($order->quotation)
                <strong>อ้างอิงใบเสนอราคา:</strong> {{ $order->quotation->quotation_number }}<br>
                @endif
                <strong>พนักงานขาย:</strong>
                @if($order->sales_person_id && $salesPerson = \App\Models\Employee::find($order->sales_person_id))
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
                    <th style="width: 35%;">รายการ</th>
                    <th style="width: 15%; text-align: right;">จำนวน</th>
                    <th style="width: 15%; text-align: right;">ราคาต่อหน่วย</th>
                    <th style="width: 15%; text-align: right;">จำนวนเงิน</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        @php
                            $productCode = null;
                            if($order->quotation) {
                                foreach($order->quotation->items as $quotationItem) {
                                    if($quotationItem->product_id == $item->product_id) {
                                        if($quotationItem->product && ($quotationItem->product->code || $quotationItem->product->sku)) {
                                            $productCode = $quotationItem->product->code ?? $quotationItem->product->sku;
                                            break;
                                        }
                                    }
                                }
                            }
                            
                            if(!$productCode && $item->product) {
                                $productCode = $item->product->code ?? $item->product->sku ?? '-';
                            }
                        @endphp
                        {{ $productCode ?? '-' }}
                    </td>
                    <td>{{ $item->description }}</td>
                    <td style="text-align: right;">{{ number_format($item->quantity, 2) }} {{ $item->unit->name ?? '-' }}</td>
                    <td style="text-align: right;">{{ number_format($item->unit_price, 2) }}</td>
                    <td style="text-align: right;">{{ number_format($item->total, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center;">ไม่มีรายการสินค้า</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="display: flex; justify-content: space-between;">
            <div style="width: 55%;">
                @if($order->notes)
                <div class="notes">
                    <strong>หมายเหตุ:</strong>
                    <p style="margin: 2px 0;">{{ $order->notes }}</p>
                </div>
                @endif
            </div>
            
            <div class="amount-summary">
                <div class="row">
                    <span>ยอดรวมก่อนภาษี</span>
                    <span>{{ number_format($order->subtotal, 2) }}</span>
                </div>
                
                @if($order->discount_amount > 0)
                <div class="row">
                    <span>ส่วนลด
                        @if($order->discount_type == 'percentage')
                        ({{ $order->discount_amount }}%)
                        @endif
                    </span>
                    <span>{{ number_format($order->discount_value, 2) }}</span>
                </div>
                @endif
                
                @if($order->tax_rate > 0)
                <div class="row">
                    <span>ภาษีมูลค่าเพิ่ม ({{ $order->tax_rate }}%)</span>
                    <span>{{ number_format($order->tax_amount, 2) }}</span>
                </div>
                @endif
                
                @if($order->shipping_cost > 0)
                <div class="row">
                    <span>ค่าขนส่ง</span>
                    <span>{{ number_format($order->shipping_cost, 2) }}</span>
                </div>
                @endif
                
                <div class="total-row">
                    <span>ยอดรวมทั้งสิ้น</span>
                    <span>{{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="signatures">
            <div class="signature">
                <div class="signature-line">ผู้สั่งซื้อ</div>
            </div>
            
            <div class="signature">
                <div class="signature-line">ผู้มีอำนาจลงนาม</div>
            </div>
        </div>
    </div>
    
    <script>
        // เพิ่ม Event listener สำหรับการพิมพ์
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Document loaded, setting up print functionality');
            
            // ป้องกันการพิมพ์ซ้ำซ้อน
            let isPrinting = false;
            
            // ฟังก์ชันสำหรับการพิมพ์
            const printDocument = function() {
                console.log('Print function called');
                if (isPrinting) {
                    console.log('Already printing, ignoring request');
                    return; // ป้องกันการเรียกซ้ำ
                }
                
                isPrinting = true;
                console.log('Setting up print parameters');
                
                try {
                    // ตั้งชื่อเอกสาร
                    document.title = 'ใบสั่งขายเลขที่ {{ $order->order_number }}';
                    
                    // ปรับขนาดตัวอักษรตามความจำเป็น
                    const tableRows = document.querySelectorAll('table tbody tr');
                    console.log('Table rows found:', tableRows.length);
                    
                    if (tableRows.length > 15) {
                        // ถ้ามีรายการมาก ลดขนาดตัวอักษรลงอีก
                        document.querySelector('table').style.fontSize = '9px';
                        document.querySelectorAll('th, td').forEach(cell => {
                            cell.style.padding = '2px 4px';
                        });
                    } else if (tableRows.length > 10) {
                        // ถ้ามีรายการปานกลาง
                        document.querySelector('table').style.fontSize = '10px';
                        document.querySelectorAll('th, td').forEach(cell => {
                            cell.style.padding = '3px 5px';
                        });
                    }
                    
                    // ตรวจสอบความกว้างของตาราง
                    const tableWidth = document.querySelector('table').offsetWidth;
                    const containerWidth = document.querySelector('.container').offsetWidth;
                    console.log('Table width:', tableWidth, 'Container width:', containerWidth);
                    
                    // ถ้าตารางกว้างเกินไป ปรับแต่งเพิ่มเติม
                    if (tableWidth > containerWidth * 0.95) {
                        document.querySelector('table').style.fontSize = '9px';
                        document.querySelectorAll('table th, table td').forEach(cell => {
                            cell.style.padding = '2px 3px';
                        });
                    }
                } catch (err) {
                    console.error('Error during print preparation:', err);
                }
                
                // เรียกคำสั่งพิมพ์
                console.log('Initiating print command...');
                setTimeout(function() {
                    try {
                        window.print();
                        console.log('Print command executed');
                    } catch (e) {
                        console.error('Error during print:', e);
                    }
                    
                    // รอให้การพิมพ์เสร็จสิ้น
                    setTimeout(function() {
                        isPrinting = false;
                        console.log('Print process completed');
                    }, 1000);
                }, 200);
            };
            
            // ตั้งค่า event listener สำหรับปุ่มพิมพ์
            const printButton = document.querySelector('.print-button');
            if (printButton) {
                console.log('Print button found, attaching event listener');
                // ลบ onclick attribute ที่มีอยู่เดิม
                printButton.removeAttribute('onclick');
                
                // ใช้ addEventListener แทน
                printButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    console.log('Print button clicked');
                    printDocument();
                });
            } else {
                console.warn('Print button not found in the document');
            }
            
            // เมื่อหน้าต่างสั่งพิมพ์ถูกเรียก
            window.addEventListener('beforeprint', function() {
                console.log('beforeprint event triggered');
                document.title = 'ใบสั่งขายเลขที่ {{ $order->order_number }}';
            });
            
            // เพิ่มตัวแสดงการโหลด
            console.log('Print page fully initialized');

            // โค้ดเพิ่มเติมเพื่อให้สามารถพิมพ์ได้ทันทีเมื่อโหลดหน้า
            // หากมีการเปิดหน้านี้โดยตรงจากปุ่มพิมพ์
            if (window.opener && !window.location.href.includes('noprint')) {
                console.log('Auto-print mode detected from new tab');
                // รอให้หน้าโหลดเสร็จสมบูรณ์ก่อนพิมพ์
                setTimeout(printDocument, 1000);
            } else if(!window.opener && !window.location.href.includes('noprint')) {
                // กรณีเปิดในแท็บใหม่โดยไม่มี opener
                console.log('New tab print mode detected');
                setTimeout(printDocument, 1000);
            }
        });
    </script>
</body>
</html>
