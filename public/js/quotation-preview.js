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
                /* ปรับปรุง CSS ให้เหมือนกับ "ดูตัวอย่าง" */
                body {
                    font-family: 'Sarabun', sans-serif;
                    margin: 30px;
                    padding: 0;
                    background-color: #fff;
                    color: #000;
                    line-height: 1.6;
                }
                
                .container {
                    max-width: 1000px;
                    margin: 0 auto;
                }
                
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .font-bold { font-weight: bold; }
                
                h1 { font-size: 24px; font-weight: bold; margin-bottom: 5px; }
                h2 { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
                h3 { font-size: 18px; }
                h4 { font-size: 16px; }
                
                p { margin: 5px 0; }
                
                .mb-6 { margin-bottom: 25px; }
                
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 25px;
                }
                
                th, td {
                    border: 1px solid #333;
                    padding: 8px 12px;
                    color: #000;
                }
                
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                
                td {
                    vertical-align: top;
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
                
                .border-b-2 {
                    border-bottom: 2px solid #000;
                    margin: 20px 0;
                    padding-bottom: 10px;
                }
                
                .flex { 
                    display: flex; 
                    justify-content: space-between;
                }
                
                .justify-end { 
                    justify-content: flex-end; 
                }
                
                .w-1\\/3 { 
                    width: 33.333333%; 
                }
                
                .border-b {
                    border-bottom: 1px solid #ddd;
                    padding: 5px 0;
                    display: flex;
                    justify-content: space-between;
                }
                
                .row, .total-row {
                    display: flex;
                    justify-content: space-between;
                    min-width: 100%;
                }
                
                .row span:first-child, .total-row span:first-child {
                    width: 60%;
                }
                
                .row span:last-child, .total-row span:last-child {
                    width: 38%;
                    text-align: right;
                    padding-right: 5px;
                }
                
                .total-row {
                    font-weight: bold;
                    padding: 8px 0;
                }
                
                .flex { display: flex; }
                
                .justify-end { justify-content: flex-end; }
                
                .w-1\\/3 { width: 33.333333%; }
                
                .border-b {
                    border-bottom: 1px solid #ddd;
                    padding: 5px 0;
                }
                
                .py-2 { padding-top: 8px; padding-bottom: 8px; }
                .py-3 { padding-top: 12px; padding-bottom: 12px; }
                .p-3 { padding: 12px; }
                
                .border { border: 1px solid #ddd; }
                .rounded { border-radius: 4px; }
                
                .mt-12 { margin-top: 48px; }
                
                .border-t { border-top: 1px solid #000; }
                .pt-2 { padding-top: 8px; }
                
                .inline-block { display: inline-block; }
                
                .w-48 { width: 180px; }
                
                @media print {
                    body {
                        margin: 0;
                        padding: 20px;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                ${printContent}
            </div>
            <script>
                window.onload = function() {
                    var summaryRows = document.querySelectorAll('.row, .total-row');
                    summaryRows.forEach(function(row) {
                        if (row.children.length >= 2) {
                            var valueSpan = row.children[row.children.length - 1];
                            valueSpan.style.textAlign = 'right';
                            valueSpan.style.paddingRight = '5px';
                        }
                    });
                    
                    setTimeout(function() {
                        window.print();
                    }, 800);
                };
            </script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
}
