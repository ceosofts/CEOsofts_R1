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
            RoleAndPermissionSeeder::class, // ต้องรันก่อน AdminUserSeeder
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
            UnitSeeder::class,    // เพิ่ม UnitSeeder
            TaxSeeder::class,     // เพิ่ม TaxSeeder
            ProductCategorySeeder::class,  // เพิ่ม ProductCategorySeeder ก่อน ProductSeeder
            ProductSeeder::class,  // ย้าย ProductSeeder มาหลัง UnitSeeder และ TaxSeeder
            StockMovementSeeder::class, // เพิ่ม StockMovementSeeder หลัง ProductSeeder
            QuotationSeeder::class,
            OrderSeeder::class,
            InvoiceSeeder::class,
            ReceiptSeeder::class, // เพิ่ม ReceiptSeeder
            ReceiptItemSeeder::class, // เพิ่ม ReceiptItemSeeder หลัง ReceiptSeeder
            DocumentTemplateSeeder::class,
            GeneratedDocumentSeeder::class,
            DocumentSendingSeeder::class, // เพิ่ม DocumentSendingSeeder
            SettingSeeder::class,          // เพิ่ม SettingSeeder
            ScheduledEventSeeder::class,    // เพิ่ม ScheduledEventSeeder
            TranslationSeeder::class,     // เพิ่ม TranslationSeeder
            FileAttachmentSeeder::class,  // เพิ่ม FileAttachmentSeeder
            ActivityLogSeeder::class,
        ]);
    }
}
