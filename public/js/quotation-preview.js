/**
 * Quotation Preview and Print Functions
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('Quotation Preview JS Loaded');
    
    // ระบุองค์ประกอบที่เกี่ยวข้อง
    const previewButton = document.getElementById('preview-button');
    const previewModal = document.getElementById('preview-modal');
    const closePreview = document.getElementById('close-preview');
    
    // เช็คว่าอยู่ในหน้าที่มีปุ่มแสดงตัวอย่างหรือไม่
    if (previewButton) {
        console.log('Preview button found in document');
        
        // แสดงตัวอย่างใบเสนอราคา
        previewButton.addEventListener('click', function() {
            console.log('Preview button clicked');
            previewModal.classList.remove('modal-hidden'); 
        });
    }
    
    // ปิดการแสดงตัวอย่าง
    if (closePreview) {
        closePreview.addEventListener('click', function() {
            console.log('Close preview clicked');
            previewModal.classList.add('modal-hidden');
        });
    }
    
    // *** สำคัญ: ไม่ผูกปุ่มพิมพ์กับฟังก์ชันที่นี่ ***
    // การจัดการปุ่มพิมพ์จะทำใน show.blade.php เพื่อหลีกเลี่ยงการทำงานซ้ำซ้อน
});

/**
 * ฟังก์ชันสำหรับพิมพ์ใบเสนอราคาในรูปแบบที่กำหนด
 * @param {string} title - ชื่อเอกสารที่จะแสดงในหัวเพจ
 */
function printQuotation(title) {
    const quotationTitle = title || 'ใบเสนอราคา'; // ใช้ค่าเริ่มต้นถ้าไม่มีการส่ง title มา
    const printContent = document.getElementById('preview-content').innerHTML;
    
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${quotationTitle}</title>
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
                    width: 48%;
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
                
                /* แถวยอดรวมทั้งสิ้น */
                .total-row {
                    font-weight: bold;
                    padding: 2mm 0;
                    margin-top: 1mm;
                }
                
                /* สไตล์สำหรับแถวยอดรวมทั้งสิ้น - ใช้ได้กับทั้งตารางและ div */
                .summary-table tr:last-child td:first-child,
                div.summary-row > div:first-child,
                .total-label {
                    text-align: left !important;
                    padding-left: 5mm !important;
                }
                
                .summary-table tr:last-child td:last-child,
                div.summary-row > div:last-child,
                .total-amount {
                    text-align: right !important;
                }
                
                /* สำหรับยอดรวมทั้งสิ้นที่อยู่ใน div */
                .flex.justify-between:last-child {
                    display: flex;
                    justify-content: space-between;
                    padding: 2mm 0;
                }
                
                /* กรณีเป็น <p> หรือ element อื่นๆ ที่มีคำว่า "ยอดรวมทั้งสิ้น" */
                p:contains("ยอดรวมทั้งสิ้น"), 
                div:contains("ยอดรวมทั้งสิ้น"),
                span:contains("ยอดรวมทั้งสิ้น") {
                    text-align: left !important;
                    padding-left: 5mm !important;
                }
                
                /* แก้ไขเพิ่มเติมสำหรับกรณีใช้คลาส flex */
                .flex.justify-between:last-child > :first-child {
                    margin-right: auto;
                    padding-left: 5mm !important;
                    text-align: left;
                }
                
                .flex.justify-between:last-child > :last-child {
                    text-align: right;
                }
                
                .total-row .total-label {
                    text-align: left;
                    padding-left: 2.5mm;
                }
                
                .total-row .total-amount {
                    text-align: right;
                    padding-right: 2.5mm;
                }
                
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
                }

                /* สไตล์เพิ่มเติมเพื่อจัดการปัญหาการจัดวาง */
                .summary-table tr:last-child td:first-child,
                div.summary-row > div:first-child,
                .total-label,
                .total-row span:first-child,
                div[style*="font-weight: bold"] > span:first-child,
                .flex.justify-between.py-3.font-bold > span:first-child {
                    text-align: left !important;
                    padding-left: 5mm !important;
                }
                
                .flex.justify-between.py-3.font-bold,
                div[style*="font-weight: bold; padding"],
                .total-row {
                    display: flex !important;
                    justify-content: space-between !important;
                    padding: 2mm 0 !important;
                }
                
                /* สำหรับปรับแต่ง CSS ที่ถูกใช้งานในทุกเทมเพลต */
                span:contains("ยอดรวมทั้งสิ้น"),
                *:contains("ยอดรวมทั้งสิ้น") {
                    text-align: left !important;
                    padding-left: 5mm !important;
                }

                /* สไตล์เพิ่มเติมเพื่อจัดการแนวการจัดวางให้ตรงกับรายการด้านบน */
                .border-b span:first-child,
                .row span:first-child,
                .total-row span:first-child,
                .flex.justify-between > span:first-child,
                div[style*="justify-content: space-between"] > span:first-child,
                .flex.justify-between.py-3.font-bold > span:first-child {
                    text-align: left !important;
                    padding-left: 0 !important; /* ลบ padding ซ้ายทั้งหมด */
                }
                
                /* ทำให้แถวทุกแถวในส่วนยอดรวมมีรูปแบบเดียวกัน */
                .amount-summary .row,
                .amount-summary .total-row,
                .w-1\\/3 .border-b,
                .w-1\\/3 .flex.justify-between,
                .flex.justify-between.py-3.font-bold {
                    display: flex;
                    justify-content: space-between;
                    width: 100%;
                    box-sizing: border-box;
                }
                
                /* ทำให้ข้อความ "ยอดรวมทั้งสิ้น" อยู่ในตำแหน่งเดียวกับข้อความอื่นๆ */
                span:contains("ยอดรวมทั้งสิ้น"),
                *:contains("ยอดรวมทั้งสิ้น") {
                    text-align: left !สำคัญ;
                    padding-left: 0 !สำคัญ;
                }
                
                /* แก้ไขเพิ่มเติมเพื่อให้แน่ใจว่าการจัดวางถูกต้อง */
                .total-label,
                .total-row .total-label {
                    text-align: left !สำคัญ;
                    padding-left: 0 !สำคัญ;
                }
                
                /* สไตล์สำหรับแสดงพนักงานขาย */
                .sales-person-info {
                    margin-top: 2mm;
                    font-weight: normal;
                }
                
                .bold-label {
                    font-weight: bold;
                }
            </style>
        </head>
        <body>
            <div class="container">
                ${printContent}
            </div>
            <script>
                window.onload = function() {
                    document.title = "${quotationTitle}"; // กำหนดชื่อเอกสารอีกครั้งหลังจากโหลดเสร็จ
                    // พิมพ์หลังจากปรับขนาด
                    setTimeout(function() {
                        window.print();
                        setTimeout(function() {
                            // ปิดหน้าต่างหลังจากพิมพ์เสร็จ (หรือยกเลิก)
                            window.close();
                        }, 1000);
                    }, 500);
                };
            </script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
}
