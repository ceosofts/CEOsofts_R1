<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DepartmentController; // เพิ่มบรรทัดนี้
use App\Models\Company;
use App\Http\Controllers\Auth\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// หน้าหลักใช้ HomeController แทน Closure
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

// เพิ่ม Route สำหรับการเลือกบริษัท
Route::get('/dashboard/switch-company/{company}', function (\App\Models\Company $company) {
    session(['current_company_id' => $company->id]);
    return redirect()->route('dashboard');
})->middleware(['auth'])->name('dashboard.switch-company');

// ลบ route ซ้ำซ้อนและใช้เพียง controller เดียว
Route::resource('companies', CompaniesController::class);

// เพิ่มเส้นทางสำหรับลูกค้า
Route::resource('customers', App\Http\Controllers\CustomerController::class);

// เพิ่มเส้นทางสำหรับใบเสนอราคา
Route::resource('quotations', App\Http\Controllers\QuotationController::class);

// เพิ่มเส้นทางสำหรับใบสั่งขาย
Route::resource('orders', App\Http\Controllers\OrderController::class);

// ทดสอบเส้นทางเพื่อ debug
Route::get('/test-companies', function () {
    return "Companies test route is working!";
});

// ปรับปรุง Debug Route เพื่อบังคับใช้ SQLite Connection
Route::get('/debug-companies', function () {
    // กำหนด path เต็มสำหรับ SQLite
    $sqlitePath = database_path('ceosofts_db_R1.sqlite');

    // บังคับใช้ SQLite connection
    config(['database.default' => 'sqlite']);
    config(['database.connections.sqlite.database' => $sqlitePath]);

    // ล้างการเชื่อมต่อเดิม
    DB::purge('sqlite');

    try {
        // ดึงข้อมูลผ่าน Model โดยตรง
        $companies = \App\Models\Company::all();

        // ดึงข้อมูลโดยตรงจาก DB
        $companiesFromDB = DB::connection()->table('companies')->get();

        return response()->json([
            'model_count' => $companies->count(),
            'model_data' => $companies->toArray(),
            'db_count' => $companiesFromDB->count(),
            'db_data' => json_decode(json_encode($companiesFromDB), true),
            'connection_name' => DB::connection()->getName(),
            'database_path' => DB::connection()->getDatabaseName(),
            'sqlite_path' => $sqlitePath,
            'file_exists' => file_exists($sqlitePath),
            'env_connection' => env('DB_CONNECTION')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'sqlite_path' => $sqlitePath,
            'file_exists' => file_exists($sqlitePath),
        ], 500);
    }
});

// เพิ่ม debug route ที่มีข้อมูลเฉพาะการค้นหา scopes
Route::get('/debug-company-scopes', function () {
    $companyClass = new \ReflectionClass(Company::class);
    $traits = $companyClass->getTraits();

    $hasCompanyScopeTrait = false;
    foreach ($traits as $trait) {
        if (strpos($trait->getName(), 'CompanyScope') !== false) {
            $hasCompanyScopeTrait = true;
            break;
        }
    }

    $bootMethod = $companyClass->hasMethod('boot') ? $companyClass->getMethod('boot')->getFileName() . ':' . $companyClass->getMethod('boot')->getStartLine() : null;

    return response()->json([
        'class' => Company::class,
        'traits' => array_keys($traits),
        'has_company_scope_trait' => $hasCompanyScopeTrait,
        'boot_method_location' => $bootMethod,
        'global_scopes' => Company::$globalScopes ?? [],
        'has_soft_deletes' => in_array('Illuminate\Database\Eloquent\SoftDeletes', array_keys($traits)),
    ]);
});

// เพิ่ม debug routes
Route::get('/debug/companies', [DebugController::class, 'companies']);

// เพิ่มเส้นทางสำหรับการตรวจสอบการเชื่อมต่อฐานข้อมูล
Route::get('/debug-db-connection', function () {
    try {
        DB::connection()->getPdo();
        return "เชื่อมต่อฐานข้อมูลสำเร็จ: " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้: " . $e->getMessage();
    }
});

// เพิ่ม route สำหรับทดสอบข้อมูลบริษัท
Route::get('/debug/company/{id}', function ($id) {
    $company = \App\Models\Company::with(['departments', 'positions', 'employees'])->find($id);

    if (!$company) {
        return response()->json(['error' => 'บริษัทไม่พบ'], 404);
    }

    return response()->json([
        'company' => $company,
        'has_departments' => $company->departments()->exists(),
        'has_positions' => $company->positions()->exists(),
        'has_employees' => $company->employees()->exists(),
        'departments_count' => $company->departments()->count(),
        'positions_count' => $company->positions()->count(),
        'employees_count' => $company->employees()->count(),
    ]);
});

// เพิ่ม Debug Routes
Route::get('/debug/company/list', [App\Http\Controllers\DebugCompanyController::class, 'list']);
Route::get('/debug/company/show/{id}', [App\Http\Controllers\DebugCompanyController::class, 'show']);
Route::get('/debug/company/relationships/{id}', [App\Http\Controllers\DebugCompanyController::class, 'testRelationships']);

// เพิ่ม Route ตรวจสอบข้อมูลบริษัทแบบง่าย
Route::get('/basic-company/{id}', function ($id) {
    try {
        $company = \App\Models\Company::findOrFail($id);
        echo "<h1>ข้อมูลบริษัท</h1>";
        echo "<pre>";
        print_r($company->toArray());
        echo "</pre>";
        return "หากคุณเห็นข้อความนี้ แสดงว่าสามารถเข้าถึงข้อมูลบริษัทได้";
    } catch (\Exception $e) {
        return "เกิดข้อผิดพลาด: " . $e->getMessage();
    }
});

// Password Reset Routes
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotPasswordForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetPasswordForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->middleware('guest')
    ->name('password.update');

// เส้นทางสำหรับระบบจัดการแผนก
Route::middleware(['auth'])->group(function () {
    Route::resource('departments', DepartmentController::class);
});

// นำเข้าเส้นทาง Authentication จากไฟล์ auth.php
require __DIR__ . '/auth.php';
