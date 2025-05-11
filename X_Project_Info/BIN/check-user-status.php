<?php

require __DIR__ . '/bootstrap/app.php';

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$user = \App\Models\User::where('email', 'superadmin@ceosofts.com')->first();

if ($user) {
    echo "ข้อมูลผู้ใช้:\n";
    echo "- ชื่อ: " . $user->name . "\n";
    echo "- อีเมล: " . $user->email . "\n";
    echo "- สถานะการยืนยันอีเมล: " . ($user->email_verified_at ? 'ยืนยันแล้ว' : 'ยังไม่ยืนยัน') . "\n";
    
    echo "\nสิทธิ์การใช้งาน:\n";
    if (class_exists('\Spatie\Permission\Models\Role')) {
        $roles = $user->getRoleNames()->toArray();
        echo "- บทบาท: " . implode(", ", $roles) . "\n";
        
        echo "- สิทธิ์ทั้งหมด:\n";
        foreach ($user->getAllPermissions() as $permission) {
            echo "  - " . $permission->name . "\n";
        }
    } else {
        echo "- ไม่พบข้อมูลบทบาท (ระบบอาจไม่ได้ใช้ spatie/laravel-permission)\n";
    }
    
    echo "\nข้อมูลบริษัทที่เข้าถึงได้:\n";
    if (method_exists($user, 'companies')) {
        $companies = $user->companies()->get();
        if ($companies->count() > 0) {
            foreach ($companies as $company) {
                echo "- " . $company->name . " (ID: " . $company->id . ")\n";
            }
        } else {
            echo "- มีสิทธิ์เข้าถึงทุกบริษัท (เนื่องจากเป็น superadmin)\n";
        }
    } else {
        echo "- ไม่พบความสัมพันธ์กับบริษัท\n";
    }
} else {
    echo "ไม่พบผู้ใช้ superadmin@ceosofts.com ในระบบ";
}

echo "\n\nคุณสามารถใช้คำสั่ง 'php check-user-status.php' เพื่อรันสคริปต์นี้และดูข้อมูลแบบละเอียด";
