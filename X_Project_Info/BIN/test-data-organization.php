<?php

// สคริปต์สำหรับเพิ่มข้อมูลตัวอย่างโครงสร้างองค์กร
// รันด้วยคำสั่ง: php test-data-organization.php

require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Support\Facades\Log;

// เลือกบริษัทที่ต้องการเพิ่มข้อมูล
$company = Company::find(1);

if (!$company) {
    echo "ไม่พบบริษัทที่ ID = 1\n";
    exit(1);
}

echo "เริ่มเพิ่มข้อมูลโครงสร้างองค์กรให้กับบริษัท " . $company->name . " (ID: " . $company->id . ")\n";

try {
    // สร้างแผนกหลัก
    $mainDepartment = Department::firstOrCreate(
        ['name' => 'แผนกบริหาร', 'company_id' => $company->id],
        ['description' => 'แผนกบริหารระดับสูง', 'is_active' => true]
    );

    // สร้างแผนกย่อย
    $childDepartments = [
        ['name' => 'แผนกการเงิน', 'description' => 'ดูแลด้านการเงินและบัญชี'],
        ['name' => 'แผนกบุคคล', 'description' => 'ดูแลด้านทรัพยากรบุคคล'],
        ['name' => 'แผนกขาย', 'description' => 'ดูแลด้านการขายและการตลาด'],
    ];

    foreach ($childDepartments as $deptData) {
        $dept = Department::firstOrCreate(
            ['name' => $deptData['name'], 'company_id' => $company->id],
            [
                'description' => $deptData['description'],
                'is_active' => true,
                'parent_id' => $mainDepartment->id
            ]
        );
        echo "- สร้างแผนก: " . $dept->name . "\n";
        
        // สร้างตำแหน่งในแผนก
        $positions = [];
        
        if ($dept->name === 'แผนกการเงิน') {
            $positions = ['ผู้จัดการฝ่ายการเงิน', 'พนักงานบัญชี', 'พนักงานการเงิน'];
        } elseif ($dept->name === 'แผนกบุคคล') {
            $positions = ['ผู้จัดการฝ่ายบุคคล', 'เจ้าหน้าที่สรรหาบุคลากร', 'เจ้าหน้าที่ฝึกอบรม'];
        } elseif ($dept->name === 'แผนกขาย') {
            $positions = ['ผู้จัดการฝ่ายขาย', 'พนักงานขาย', 'พนักงานการตลาด'];
        }
        
        foreach ($positions as $posTitle) {
            $position = Position::firstOrCreate(
                ['name' => $posTitle, 'company_id' => $company->id],
                ['description' => 'ตำแหน่งใน' . $dept->name, 'is_active' => true]
            );
            
            // เพิ่มความสัมพันธ์ระหว่างแผนกและตำแหน่ง
            if (!$dept->positions()->where('position_id', $position->id)->exists()) {
                $dept->positions()->attach($position->id);
                echo "  - เพิ่มตำแหน่ง: " . $position->name . " ให้กับแผนก " . $dept->name . "\n";
            }
        }
    }

    echo "เพิ่มข้อมูลโครงสร้างองค์กรเรียบร้อยแล้ว\n";
} catch (\Exception $e) {
    echo "เกิดข้อผิดพลาด: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
