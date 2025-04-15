<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComingSoonController extends Controller
{
    public function index(Request $request, $feature = null)
    {
        $featureName = $feature ?? $request->query('feature', 'unknown');
        $featureNames = [
            'branch-offices' => 'การจัดการสาขา',
            'work-shifts' => 'กะการทำงาน',
            'leave-types' => 'ประเภทการลา',
            'leaves' => 'การลา',
            'attendances' => 'การลงเวลาทำงาน',
            'delivery-notes' => 'ใบส่งของ',
            'invoices' => 'ใบแจ้งหนี้',
            'receipts' => 'ใบเสร็จรับเงิน',
            'products' => 'สินค้า',
            'product-categories' => 'หมวดหมู่สินค้า',
            'units' => 'หน่วยนับ',
            'stock-movements' => 'การเคลื่อนไหวสินค้า',
            'settings' => 'ตั้งค่าระบบ',
            'users' => 'ผู้ใช้งาน',
            'roles' => 'บทบาท',
            'permissions' => 'สิทธิ์การใช้งาน',
            'activity-logs' => 'ประวัติการใช้งาน',
            'profile' => 'โปรไฟล์',
        ];

        $displayName = $featureNames[$featureName] ?? ucfirst(str_replace('-', ' ', $featureName));

        return view('coming-soon', [
            'feature' => $featureName,
            'displayName' => $displayName,
        ]);
    }
}
