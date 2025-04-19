<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Context Configuration
    |--------------------------------------------------------------------------
    |
    | ไฟล์นี้เก็บค่าตั้งค่าที่เกี่ยวข้องกับ company context ในระบบ
    |
    */

    // บริษัทที่เลือกปัจจุบัน
    'id' => 1,

    // ระบุค่าเริ่มต้นสำหรับบริษัท
    'default_id' => 1,

    // ชื่อคอลัมน์ company_id ในฐานข้อมูล
    'column' => 'company_id',

    // รายการ models ที่ใช้ company scope
    'models' => [
        'App\Models\Department',
        'App\Models\Employee',
        'App\Models\Customer',
        'App\Models\Order',
        'App\Models\OrderItem',
        'App\Models\Quotation',
        'App\Models\QuotationItem',
        'App\Models\Product',
    ],

    // แบบฟิลเตอร์แบบเข้มงวด (ถ้าไม่มี company_id ให้แสดงข้อผิดพลาด)
    'strict' => false,
];
