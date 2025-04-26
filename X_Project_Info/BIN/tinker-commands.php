
<?php
// คำสั่งสำหรับใช้งานใน Tinker

// 1. แสดงรายการตารางทั้งหมด (สำหรับ SQLite)
// use Illuminate\Support\Facades\DB;
// DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");

// 2. นับจำนวนข้อมูลในแต่ละตาราง
// $tables = collect(DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;"))
//     ->pluck('name')
//     ->filter(fn($table) => !in_array($table, ['migrations', 'sqlite_sequence']));

// $result = $tables->mapWithKeys(function($table) {
//     return [$table => DB::table($table)->count()]; 
// });

// echo "รายการตารางทั้งหมดและจำนวนข้อมูล:\n";
// $result->each(function ($count, $table) {
//     echo "{$table}: {$count} รายการ\n";
// });

// 3. ดูข้อมูลพนักงานในระบบ
// App\Models\Employee::all();

// 4. ดูโครงสร้างตาราง employees
// Schema::getColumnListing('employees');

// 5. ดูข้อมูลบริษัทในระบบ
// App\Models\Company::all();

// 6. ดูความสัมพันธ์ระหว่างบริษัทและพนักงาน
// App\Models\Company::with('employees')->first();
