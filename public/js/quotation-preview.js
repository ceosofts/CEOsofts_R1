/**
 * Quotation Preview and Print Functions
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Quotation Preview JS Loaded');
    
    // ระบุองค์ประกอบที่เกี่ยวข้อง
    const previewButton = document.getElementById('preview-button');
    const printButton = document.getElementById('print-button');
    const previewModal = document.getElementById('preview-modal');
    const closePreview = document.getElementById('close-preview');
    
    // เช็คว่าอยู่ในหน้าที่มีปุ่มแสดงตัวอย่างหรือไม่
    if (previewButton) {
        console.log('Preview button found in document');
        
        // แสดงตัวอย่างใบเสนอราคา
        previewButton.onclick = function() {
            console.log('Preview button clicked');
            previewModal.classList.remove('modal-hidden'); // แก้ไขจาก hidden เป็น modal-hidden
        };
    }
    
    // ปิดการแสดงตัวอย่าง
    if (closePreview) {
        closePreview.onclick = function() {
            console.log('Close preview clicked');
            previewModal.classList.add('modal-hidden'); // แก้ไขจาก hidden เป็น modal-hidden
        };
    }
    
    // พิมพ์ใบเสนอราคา
    if (printButton) {
        console.log('Print button found in document');
        
        printButton.onclick = function() {
            console.log('Print button clicked');
            printQuotation();
        };
    }
});

/**
 * ฟังก์ชันสั่งพิมพ์ใบเสนอราคา
 */
function printQuotation() {
    const printContent = document.getElementById('preview-content').innerHTML;
    
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>พิมพ์ใบเสนอราคา</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;700&display=swap" rel="stylesheet">
            <style>
                /* ตั้งค่า A4 อย่างเข้มงวด */
                @page {
                    size: 21cm 29.7cm;
                    margin: 15mm 10mm 10mm 10mm; /* เพิ่มขอบบนเป็น 15mm */
                }
                
                html, body {
                    width: 210mm;
                    height: 297mm;
                    margin: 0;
                    padding: 0;
                    font-family: 'Sarabun', sans-serif;
                    font-size: 10pt;
                    line-height: 1.4;
                    color: #000;
                }
                
                .container {
                    width: 96%;
                    max-width: 195mm;
                    box-sizing: border-box;
                    padding: 15mm 0 0 0; /* เพิ่ม padding-top จาก 8mm เป็น 15mm */
                    margin: 0 auto;
                }
                
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .font-bold { font-weight: bold; }
                
                /* ปรับขนาดหัวข้อให้ใหญ่ขึ้น */
                h1 { font-size: 16pt; font-weight: bold; margin: 2mm 0; }
                h2 { font-size: 14pt; font-weight: bold; margin: 2mm 0; }
                h3 { font-size: 12pt; margin: 1.5mm 0; }
                h4 { font-size: 11pt; margin: 1.5mm 0; }
                
                p { margin: 2mm 0; }
                
                .mb-6 { margin-bottom: 5mm; }
                
                /* เพิ่มระยะห่างส่วนหัวเอกสาร */
                .text-center.mb-6 {
                    margin-top: 5mm; /* เพิ่มระยะห่างด้านบนของส่วนหัว */
                    margin-bottom: 8mm; /* เพิ่มระยะห่างด้านล่างของส่วนหัว */
                }
                
                /* ปรับตารางให้มีระยะห่างมากขึ้น */
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 5mm;
                    font-size: 10pt;
                    table-layout: fixed; /* ป้องกันตารางขยายเกินขอบ */
                }
                
                th, td {
                    border: 0.5pt solid #333;
                    padding: 2mm 2.5mm; /* ลด padding เล็กน้อยเพื่อป้องกันล้น */
                    word-wrap: break-word; /* ให้ข้อความยาวตัดบรรทัดอัตโนมัติ */
                }
                
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                
                /* ปรับ layout ให้แน่นขึ้น */
                .grid-cols-2 {
                    display: flex;
                    justify-content: space-between;
                    width: 100%;
                    margin: 0 0 4mm 0; /* เพิ่ม margin ด้านล่าง */
                    padding: 0;
                    box-sizing: border-box;
                }

                .grid-cols-2 > div {
                    width: 48%; /* เพิ่มจาก 46% เป็น 48% */
                }
                
                .border-b-2 {
                    border-bottom: 0.8pt solid #000;
                    margin: 3mm 0;
                    padding-bottom: 2mm;
                }
                
                /* ปรับขนาดตารางและองค์ประกอบอื่นๆ */
                .flex { 
                    display: flex;
                    justify-content: flex-end;
                }
                .w-1\\/3 { 
                    width: 35%; 
                }
                
                /* ปรับปรุงการแสดงผลยอดรวม */
                .border-b {
                    border-bottom: 0.8pt solid #ddd;
                    padding: 1mm 0;
                    display: flex;
                    justify-content: space-between;
                }
                
                /* แยกข้อความและตัวเลขให้มีระยะห่างที่เหมาะสม */
                .row, .total-row {
                    display: flex;
                    justify-content: space-between;
                    width: 100%;
                    margin-bottom: 1mm;
                    position: relative;
                }
                
                .row span:first-child, .total-row span:first-child {
                    width: 60%;
                    text-align: left;
                    margin-right: 5mm; /* เพิ่มระยะห่างขวา */
                    white-space: nowrap; /* ป้องกันการตัดบรรทัด */
                }
                
                .row span:last-child, .total-row span:last-child {
                    width: 40%;
                    text-align: right;
                    font-variant-numeric: tabular-nums; /* ทำให้ตัวเลขเรียงตรงกัน */
                    padding-left: 10mm; /* เพิ่มระยะห่างซ้าย */
                }
                
                /* เพิ่ม spacer ระหว่างข้อความและตัวเลข */
                .row::after, .total-row::after {
                    content: "..........................";
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    transform: translateY(-50%);
                    color: transparent;
                    letter-spacing: 2px;
                    overflow: hidden;
                    pointer-events: none;
                }
                
                /* ปรับแต่งแถวยอดรวมทั้งสิ้นเป็นพิเศษ */
                .total-row {
                    font-weight: bold;
                    padding: 2mm 0;
                    margin-top: 1mm;
                    position: relative; /* ต้องใส่เพื่อให้ ::after ทำงานได้ */
                    display: flex;
                    justify-content: space-between;
                    width: 100%;
                }
                
                .total-row span:first-child {
                    display: inline-block;
                    width: 40% !important;
                    text-align: left;
                    margin-right: 0;
                    white-space: nowrap;
                }
                
                .total-row span:last-child {
                    display: inline-block;
                    width: 30% !important;
                    text-align: right;
                    padding-left: 0;
                }
                
                .border-b {
                    border-bottom: 0.8pt solid #ddd;
                    padding: 1mm 0;
                }
                
                .py-2 { padding-top: 2mm; padding-bottom: 2mm; }
                .py-3 { padding-top: 2.5mm; padding-bottom: 2.5mm; }
                .p-3 { padding: 2.5mm; }
                
                .border { border: 0.8pt solid #ddd; }
                .rounded { border-radius: 2mm; }
                
                .mt-12 { margin-top: 12mm; }
                
                .border-t { border-top: 0.8pt solid #000; }
                .pt-2 { padding-top: 2mm; }
                
                .inline-block { display: inline-block; }
                .w-48 { width: 35mm; } /* เพิ่มความกว้างลายเซ็น */
                
                /* ควบคุมการแบ่งหน้าอย่างเข้มงวด */
                @media print {
                    /* ลดขนาดองค์ประกอบเมื่อพิมพ์แต่ยังคงให้อ่านง่าย */
                    html, body {
                        width: 210mm;
                        height: 297mm;
                        margin: 0;
                        padding: 0;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                    
                    .container {
                        width: 100%;
                        max-width: none;
                        margin: 0 auto;
                        padding: 20mm 5mm 0 5mm; /* เพิ่ม padding-top เป็น 20mm */
                    }
                    
                    /* ป้องกันการตัดแบ่งองค์ประกอบสำคัญ */
                    table, tr, thead, tbody { page-break-inside: avoid; }
                    
                    /* ปรับขนาดตารางให้เหมาะสมไม่ล้นหน้า */
                    table {
                        max-height: 180mm;
                        font-size: 9pt;
                    }

                    /* ปรับลายเซ็น */
                    .mt-12 { margin-top: 8mm; }
                    
                    /* ปรับขนาดหัวข้อ */
                    h1 { font-size: 14pt; }
                    h2 { font-size: 12pt; }
                    
                    /* ปรับปรุงการแสดงผลยอดรวมเมื่อพิมพ์ */
                    .row span:first-child, .total-row span:first-child {
                        width: 60%;
                        margin-right: 5mm;
                        white-space: nowrap;
                    }
                    
                    .row span:last-child, .total-row span:last-child {
                        width: 40%;
                        text-align: right;
                        padding-left: 10mm;
                    }
                    
                    /* แก้ไขแถวยอดรวมทั้งสิ้นโดยเฉพาะเมื่อพิมพ์ */
                    .total-row::after {
                        content: "................................";
                        position: absolute;
                        left: 40%;
                        top: 50%;
                        transform: translateY(-50%);
                        color: transparent;
                        letter-spacing: 3px;
                        overflow: hidden;
                        pointer-events: none;
                    }
                }
                
                /* แยกตัวหนังสือและตัวเลขให้ชัดเจน */
                .row, .total-row {
                    display: flex;
                    justify-content: space-between;
                    width: 100%;
                    margin-bottom: 1mm;
                }
                
                .row span:first-child, .total-row span:first-child {
                    width: 70%; /* เพิ่มความกว้างส่วนข้อความ */
                    text-align: left;
                    padding-right: 10mm; /* เพิ่ม padding ระหว่างข้อความและตัวเลข */
                }
                
                .row span:last-child, .total-row span:last-child {
                    width: 30%; /* เพิ่มความกว้างส่วนตัวเลข */
                    text-align: right;
                    font-variant-numeric: tabular-nums; /* ทำให้ตัวเลขเรียงตรงกัน */
                }
                
                .total-row {
                    font-weight: bold;
                    padding: 2mm 0;
                    margin-top: 1mm;
                }
                
                /* แก้ไขปัญหาเรื่องระยะห่างของยอดรวมทั้งสิ้น */
                .total-row, .flex.justify-between.py-3.font-bold {
                    position: relative !important;
                    display: flex !important;
                    justify-content: space-between !important;
                    padding: 3mm 0 !important;
                    font-weight: bold !important;
                    width: 100% !important;
                    box-sizing: border-box !important;
                }
                
                .total-row span:first-child, 
                .flex.justify-between.py-3.font-bold span:first-child {
                    display: inline-block !important;
                    width: 70% !important;
                    text-align: left !important;
                    padding-right: 0 !important;
                    margin-right: 0 !important;
                    white-space: nowrap !important;
                }
                
                .total-row span:last-child,
                .flex.justify-between.py-3.font-bold span:last-child {
                    display: inline-block !important;
                    width: 30% !important;
                    text-align: right !important;
                    padding-left: 0 !important;
                    box-sizing: border-box !important;
                }

                /* จัดวางตำแหน่งสม่ำเสมอ */
                .w-1\\/3 .row,
                .w-1\\/3 .total-row,
                .w-full .row,
                .w-full .total-row,
                .amount-summary .row,
                .amount-summary .total-row {
                    width: 100% !important;
                    display: flex !important;
                    box-sizing: border-box !important;
                }

                /* สร้างความสม่ำเสมอระหว่างแถว */
                .row span:first-child,
                .total-row span:first-child {
                    width: 70% !important;
                }

                .row span:last-child,
                .total-row span:last-child {
                    width: 30% !important;
                }
            </style>
        </head>
        <body>
            <div class="container">
                ${printContent}
            </div>
            <script>
                window.onload = function() {
                    // รอให้โค้ดทำงานและรูปภาพโหลดเสร็จก่อนวัดขนาด
                    setTimeout(function() {
                        // วัดความสูงของเนื้อหา
                        const contentHeight = document.body.scrollHeight;
                        const viewportHeight = window.innerHeight;
                        
                        // กำหนดเกณฑ์ขนาดเอกสารสูงสุด (มากกว่านี้จะเล็กลง)
                        const maxAllowedHeight = viewportHeight * 0.97;
                        
                        // ถ้าเนื้อหาสูงเกินไป ปรับลดขนาดแบบอัตโนมัติ
                        if (contentHeight > maxAllowedHeight) {
                            console.log('Content too tall: ' + contentHeight + 'px (max: ' + maxAllowedHeight + 'px)');
                            
                            // คำนวณอัตราส่วน แต่ไม่ปรับลดมากเกินไป (อย่างน้อย 85%)
                            const scale = Math.max(0.85, maxAllowedHeight / contentHeight);
                            
                            // ปรับขนาดด้วย transform: scale()
                            document.body.style.transform = \`scale(\${scale})\`;
                            document.body.style.transformOrigin = 'top center';
                            document.body.style.width = (100 / scale) + '%';
                            document.body.style.marginBottom = '50px';
                        } else {
                            console.log('Content height OK: ' + contentHeight + 'px');
                        }
                        
                        // พิมพ์หลังจากปรับขนาด
                        setTimeout(function() {
                            window.print();
                        }, 500);
                    }, 500);
                    
                    // ปรับแต่งการแสดงผลยอดรวมเพิ่มเติม
                    document.querySelectorAll('.row, .total-row').forEach(function(row) {
                        // ตรวจสอบว่ามี spans สองตัวหรือไม่ (ข้อความและตัวเลข)
                        if (row.children.length >= 2) {
                            // จัดรูปแบบตัวเลขให้ชิดขวา
                            const valueSpan = row.children[row.children.length - 1];
                            valueSpan.style.textAlign = 'right';
                        }
                    });
                    
                    // แก้ไขปัญหาการแสดงผลยอดรวมทั้งสิ้นโดยตรงด้วย JavaScript
                    const fixTotalRow = function() {
                        // หารายการยอดรวมทั้งสิ้นทั้งหมด
                        const totalRows = document.querySelectorAll('.total-row, .flex.justify-between.py-3.font-bold');
                        
                        totalRows.forEach(function(row) {
                            // เพิ่มระยะห่างด้วยการปรับ style โดยตรง
                            row.style.display = 'flex';
                            row.style.justifyContent = 'space-between';
                            row.style.width = '100%';
                            row.style.padding = '3mm 0';
                            row.style.fontWeight = 'bold';
                            
                            // ปรับแต่งข้อความด้านซ้าย
                            if (row.firstElementChild) {
                                row.firstElementChild.style.display = 'inline-block';
                                row.firstElementChild.style.width = '50%';
                                row.firstElementChild.style.textAlign = 'left';
                                row.firstElementChild.style.paddingRight = '30mm';
                                row.firstElementChild.style.whiteSpace = 'nowrap';
                                
                                // ตรวจสอบหากเป็น "ยอดรวมทั้งสิ้น" จะทำการแทรกช่องว่างพิเศษ
                                if (row.firstElementChild.textContent.includes('ยอดรวมทั้งสิ้น')) {
                                    row.firstElementChild.innerHTML = 'ยอดรวมทั้งสิ้น&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                                }
                            }
                            
                            // ปรับแต่งข้อความด้านขวา (ตัวเลข)
                            if (row.lastElementChild) {
                                row.lastElementChild.style.display = 'inline-block';
                                row.lastElementChild.style.width = '50%';
                                row.lastElementChild.style.textAlign = 'right';
                            }
                        });
                    };
                    
                    // เรียกใช้ฟังก์ชันแก้ไข
                    fixTotalRow();
                    
                    // รอเวลาเล็กน้อยแล้วเรียกใช้อีกครั้งเพื่อความแน่ใจ
                    setTimeout(fixTotalRow, 100);
                    
                    // พิมพ์หลังจากปรับขนาด
                    setTimeout(function() {
                        window.print();
                    }, 500);
                };
            </script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
}
