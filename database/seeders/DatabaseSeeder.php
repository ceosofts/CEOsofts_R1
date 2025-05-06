<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            CompanySeeder::class,
            BranchOfficeSeeder::class,
            DepartmentSeeder::class,
            PositionSeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
            EmployeeSeeder::class,
            LeaveTypeSeeder::class,
            WorkShiftSeeder::class,
            EmployeeWorkShiftSeeder::class,
            CustomerSeeder::class,
            UnitSeeder::class, // ให้ UnitSeeder ทำงานก่อน ProductSeeder
            TaxSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class, // ProductSeeder เรียกใช้หลังจาก UnitSeeder
            StockMovementSeeder::class,
            QuotationSeeder::class,
            QuotationItemSeeder::class, // เพิ่มบรรทัดนี้
            OrderSeeder::class,
            InvoiceSeeder::class,
            InvoiceItemSeeder::class, // Adding InvoiceItemSeeder after InvoiceSeeder
            ReceiptSeeder::class,
            ReceiptItemSeeder::class,
            DocumentTemplateSeeder::class,
            GeneratedDocumentSeeder::class,
            DocumentSendingSeeder::class,
            SettingSeeder::class,
            ScheduledEventSeeder::class,
            TranslationSeeder::class,
            FileAttachmentSeeder::class,
            ActivityLogSeeder::class,
            DeliveryOrderSeeder::class, // เพิ่มการเรียกใช้ DeliveryOrderSeeder
        ]);
    }
}
