# โครงสร้างสำหรับรองรับการทำงานกับ PDF

## 1. โครงสร้างฐานข้อมูลเพิ่มเติม

### 1.1 ตาราง document_templates

| Column      | Type            | Properties                  | Description                                |
| ----------- | --------------- | --------------------------- | ------------------------------------------ |
| id          | bigint unsigned | primary key, auto-increment |                                            |
| company_id  | bigint unsigned | foreign key -> companies.id | onDelete: cascade                          |
| name        | varchar(255)    | not null                    | ชื่อเทมเพลต                                |
| type        | varchar(50)     | not null                    | invoice, receipt, quotation, order, etc.   |
| layout      | json            | not null                    | เก็บโครงสร้าง layout ของเอกสาร             |
| header      | json            | nullable                    | เก็บข้อมูล header ของเอกสาร                |
| footer      | json            | nullable                    | เก็บข้อมูล footer ของเอกสาร                |
| css         | text            | nullable                    | CSS สำหรับ styling เอกสาร                  |
| orientation | varchar(10)     | default: 'portrait'         | portrait, landscape                        |
| paper_size  | varchar(10)     | default: 'a4'               | a4, letter, legal                          |
| is_default  | boolean         | default: false              | กำหนดเป็นเทมเพลตเริ่มต้นสำหรับประเภทเอกสาร |
| is_active   | boolean         | default: true               |                                            |
| created_by  | bigint unsigned | foreign key -> users.id     | onDelete: restrict                         |
| metadata    | json            | nullable                    |                                            |
| created_at  | timestamp       |                             |                                            |
| updated_at  | timestamp       |                             |                                            |
| deleted_at  | timestamp       | nullable                    |                                            |

**Indexes**: company_id, type, is_default, is_active, created_by  
**Unique**: [company_id, name, type]

### 1.2 ตาราง generated_documents

| Column         | Type            | Properties                           | Description                              |
| -------------- | --------------- | ------------------------------------ | ---------------------------------------- |
| id             | bigint unsigned | primary key, auto-increment          |                                          |
| company_id     | bigint unsigned | foreign key -> companies.id          | onDelete: cascade                        |
| document_type  | varchar(50)     | not null                             | invoice, receipt, quotation, order, etc. |
| document_id    | bigint unsigned | not null                             | ID ของเอกสารต้นฉบับ                      |
| template_id    | bigint unsigned | foreign key -> document_templates.id | onDelete: set null                       |
| filename       | varchar(255)    | not null                             | ชื่อไฟล์ที่จัดเก็บ                       |
| disk           | varchar(50)     | default: 'local'                     | disk ที่จัดเก็บ (local, s3, etc.)        |
| path           | varchar(255)    | not null                             | path ที่จัดเก็บบน disk                   |
| is_signed      | boolean         | default: false                       | มีการเซ็นเอกสารหรือไม่                   |
| signature_data | json            | nullable                             | ข้อมูลลายเซ็น (ถ้ามี)                    |
| created_by     | bigint unsigned | foreign key -> users.id              | onDelete: restrict                       |
| metadata       | json            | nullable                             |                                          |
| created_at     | timestamp       |                                      |                                          |
| updated_at     | timestamp       |                                      |                                          |

**Indexes**: company_id, document_type, [document_type, document_id], template_id, created_by  
**Unique**: [document_type, document_id, created_at] (เพื่อให้สามารถสร้างใหม่ได้หลายครั้ง)

### 1.3 ตาราง document_sendings

| Column                | Type            | Properties                            | Description                      |
| --------------------- | --------------- | ------------------------------------- | -------------------------------- |
| id                    | bigint unsigned | primary key, auto-increment           |                                  |
| company_id            | bigint unsigned | foreign key -> companies.id           | onDelete: cascade                |
| generated_document_id | bigint unsigned | foreign key -> generated_documents.id | onDelete: cascade                |
| recipient_email       | varchar(255)    | not null                              |                                  |
| recipient_name        | varchar(255)    | not null                              |                                  |
| subject               | varchar(255)    | not null                              |                                  |
| message               | text            | nullable                              |                                  |
| status                | varchar(20)     | default: 'pending'                    | pending, sent, delivered, failed |
| sent_at               | timestamp       | nullable                              |                                  |
| error                 | text            | nullable                              | กรณีส่งไม่สำเร็จ                 |
| sent_by               | bigint unsigned | foreign key -> users.id               | onDelete: set null               |
| metadata              | json            | nullable                              |                                  |
| created_at            | timestamp       |                                       |                                  |
| updated_at            | timestamp       |                                       |                                  |

**Indexes**: company_id, generated_document_id, recipient_email, status, sent_at, sent_by

### 1.4 เพิ่มฟิลด์ในตารางที่มีอยู่

#### ตาราง quotations, orders, invoices, receipts

```php
$table->foreignId('last_generated_document_id')->nullable()->constrained('generated_documents')->nullOnDelete();
$table->timestamp('last_pdf_generated_at')->nullable();
$table->boolean('needs_pdf_regeneration')->default(false);
```

## 2. โครงสร้างไฟล์สำหรับ PDF Generator

```
app/
├── Domain/
│   └── DocumentGeneration/
│       ├── Models/
│       │   ├── DocumentTemplate.php
│       │   ├── GeneratedDocument.php
│       │   └── DocumentSending.php
│       ├── Services/
│       │   ├── Contracts/
│       │   │   ├── PdfGeneratorInterface.php
│       │   │   └── DocumentStorageInterface.php
│       │   └── Implementations/
│       │       ├── DomPdfGenerator.php
│       │       ├── SnappyPdfGenerator.php
│       │       └── S3DocumentStorage.php
│       ├── DTOs/
│       │   ├── DocumentRenderData.php
│       │   └── TemplateData.php
│       ├── Events/
│       │   ├── DocumentGenerated.php
│       │   └── DocumentSent.php
│       └── Exceptions/
│           └── PdfGenerationException.php
├── Infrastructure/
│   └── Services/
│       └── Pdf/
│           ├── DomPdfAdapter.php
│           ├── SnappyPdfAdapter.php
│           └── WkHtmlToPdfAdapter.php
└── UI/
    └── Web/
        └── Controllers/
            └── DocumentGeneration/
                ├── TemplateController.php
                └── DocumentGenerationController.php
```

## 3. การจัดการเทมเพลต PDF

### 3.1 ประเภทเทมเพลตเอกสาร

-   ใบเสนอราคา (Quotation Templates)
-   ใบสั่งซื้อ (Order Templates)
-   ใบแจ้งหนี้ (Invoice Templates)
-   ใบเสร็จรับเงิน (Receipt Templates)
-   ใบส่งสินค้า (Delivery Note Templates)
-   รายงาน (Report Templates)

### 3.2 องค์ประกอบของเทมเพลต

-   Header: โลโก้บริษัท, ข้อมูลบริษัท, ชื่อเอกสาร
-   Body: รายละเอียดเอกสาร, ตารางสินค้า/บริการ
-   Summary: รวมยอด, ภาษี, ส่วนลด
-   Footer: เงื่อนไขการชำระเงิน, ข้อความท้ายเอกสาร, ลายเซ็น
-   QR Code: สำหรับระบบชำระเงินหรือตรวจสอบเอกสาร

## 4. การรองรับลายเซ็นดิจิทัล

### 4.1 ประเภทลายเซ็น

-   ลายเซ็นรูปภาพ: อัพโหลดรูปภาพลายเซ็น
-   ลายเซ็นแบบวาด: ใช้ canvas เพื่อวาดลายเซ็น
-   ลายเซ็นดิจิทัลที่มีการรับรอง (Digital Signature): ใช้ private key

### 4.2 ตำแหน่งลายเซ็นในเอกสาร

-   กำหนดตำแหน่งลายเซ็นได้ (X, Y coordinates)
-   รองรับหลายลายเซ็นในเอกสารเดียว (เช่น ผู้อนุมัติ, ผู้รับสินค้า)

## 5. การใช้งานและตัวอย่าง

### 5.1 การสร้าง PDF

```php
// ตัวอย่างการใช้งาน
$pdfService = app(PdfGeneratorInterface::class);
$document = Invoice::find($id);
$template = DocumentTemplate::where('type', 'invoice')->where('is_default', true)->first();

$pdf = $pdfService->generate($document, $template);
$storedPath = $pdfService->store($pdf, 'invoices', "INV-{$document->invoice_number}.pdf");

// บันทึกข้อมูลการสร้างเอกสาร
$generatedDoc = new GeneratedDocument([
    'company_id' => $document->company_id,
    'document_type' => 'invoice',
    'document_id' => $document->id,
    'template_id' => $template->id,
    'filename' => "INV-{$document->invoice_number}.pdf",
    'disk' => 'local',
    'path' => $storedPath,
]);
$generatedDoc->save();
```

### 5.2 การส่ง PDF ทางอีเมล

```php
// ตัวอย่างการส่งเอกสารทางอีเมล
$emailService = app(EmailService::class);
$document = Invoice::find($id);
$generatedDoc = $document->lastGeneratedDocument;

$emailService->sendDocument($generatedDoc, [
    'to' => $document->customer->email,
    'subject' => "Invoice #{$document->invoice_number}",
    'message' => "Please find attached invoice #{$document->invoice_number}",
    'cc' => config('company.accounting_email')
]);

// บันทึกการส่งเอกสาร
$sending = new DocumentSending([
    'company_id' => $document->company_id,
    'generated_document_id' => $generatedDoc->id,
    'recipient_email' => $document->customer->email,
    'recipient_name' => $document->customer->name,
    'subject' => "Invoice #{$document->invoice_number}",
    'message' => "Please find attached invoice #{$document->invoice_number}",
    'status' => 'sent',
    'sent_at' => now(),
    'sent_by' => auth()->id()
]);
$sending->save();
```
