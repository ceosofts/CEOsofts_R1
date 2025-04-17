<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Models\Company;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DebugCompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrganizationStructureController; // เพิ่มบรรทัดนี้
use App\Http\Controllers\ComingSoonController; // เพิ่มบรรทัดนี้
use App\Http\Controllers\ExecutiveDashboardController; // เพิ่มบรรทัดนี้
use App\Http\Controllers\ProductController; // เพิ่มการ import นี้
use App\Http\Controllers\ProductCategoryController; // เพิ่มการ import นี้
use App\Http\Controllers\UnitController; // import เพิ่มเติม
use App\Http\Controllers\StockMovementController; // import เพิ่มเติม
use App\Http\Controllers\BranchOfficeController; // เพิ่ม import สำหรับ BranchOfficeController

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

// แก้ไขเส้นทางสำหรับการเปลี่ยนบริษัท
Route::get('/switch-company/{company}', [App\Http\Controllers\CompanyController::class, 'switchCompany'])
    ->middleware(['auth'])
    ->name('switch.company');

// เพิ่มเส้นทางสำหรับทดสอบการเลือกบริษัท
Route::get('/test-company-switch', function () {
    return view('company-switch-test');
})->middleware(['auth'])->name('test.company.switch');

// ลบ route ซ้ำซ้อนและใช้เพียง controller เดียว
Route::resource('companies', CompaniesController::class);

// เพิ่มเส้นทางสำหรับลูกค้า
Route::resource('customers', CustomerController::class);

// เพิ่มเส้นทางสำหรับใบเสนอราคา
Route::resource('quotations', QuotationController::class);
Route::post('quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'viewPdf'])->name('quotations.pdf');

// เพิ่มเส้นทางสำหรับใบสั่งขาย
Route::resource('orders', OrderController::class);

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
Route::get('/debug/company/list', [DebugCompanyController::class, 'list']);
Route::get('/debug/company/show/{id}', [DebugCompanyController::class, 'show']);
Route::get('/debug/company/relationships/{id}', [DebugCompanyController::class, 'testRelationships']);

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

// Organization Routes - ตัดส่วนที่ซ้ำซ้อนออกแล้วเก็บไว้เฉพาะอันนี้
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Companies
    Route::resource('companies', CompaniesController::class);
    
    // Departments
    Route::resource('departments', DepartmentController::class);
    
    // Positions
    Route::resource('positions', PositionController::class);
    
    // Employees
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::resource('employees', EmployeeController::class);
    
    // Test Employee Controller
    Route::get('/test-employee-controller', [EmployeeController::class, 'testConnection'])
        ->name('test.employee.controller');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Routes สำหรับระบบโครงสร้างองค์กร
    Route::middleware([\App\Http\Middleware\SetDefaultCompany::class])->prefix('organization/structure')->name('organization.structure.')->group(function () {
        Route::get('/', [OrganizationStructureController::class, 'index'])->name('index');
        Route::get('/{company}', [OrganizationStructureController::class, 'show'])->name('show');
        Route::get('/{company}/edit', [OrganizationStructureController::class, 'edit'])->name('edit');
        Route::put('/{company}', [OrganizationStructureController::class, 'update'])->name('update');
        Route::get('/{company}/tree', [OrganizationStructureController::class, 'treeView'])->name('tree');
    });
    
    // Products
    Route::resource('products', ProductController::class);
    Route::get('/products/{product}/stock', [ProductController::class, 'stockHistory'])->name('products.stock');
    Route::get('/product-categories', [ProductController::class, 'categories'])->name('products.categories');
    Route::post('/product-categories', [ProductController::class, 'storeCategory'])->name('products.categories.store');
    
    // Product Categories
    Route::resource('product-categories', ProductCategoryController::class);
    
    // Units
    Route::resource('units', UnitController::class);
    
    // Stock Movements
    Route::resource('stock-movements', StockMovementController::class);
    Route::get('/stock/report', [StockMovementController::class, 'report'])->name('stock.report');
    Route::get('/stock/low-stock', [StockMovementController::class, 'lowStock'])->name('stock.low');
    
    // Customers
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('customers.export');
    Route::get('/customers/{customer}/purchase-history', [CustomerController::class, 'purchaseHistory'])->name('customers.purchase-history');
    
    // Branch Offices Routes
    // Note: Need to import BranchOfficeController at the top of the file
    Route::get('/branch-offices/export', [BranchOfficeController::class, 'export'])
        ->name('branch-offices.export');
    Route::resource('branch-offices', BranchOfficeController::class);

    // Quotation Routes
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/approve', [QuotationController::class, 'approve'])->name('quotations.approve');
    Route::post('quotations/{quotation}/reject', [QuotationController::class, 'reject'])->name('quotations.reject');
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'viewPdf'])->name('quotations.pdf');
});

// เส้นทางสำหรับจัดการหมวดหมู่สินค้า
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('product-categories', \App\Http\Controllers\ProductCategoryController::class);
});

// Executive Dashboard Routes
Route::middleware(['auth', 'verified'])->prefix('executive')->name('executive.')->group(function () {
    Route::get('/dashboard', [ExecutiveDashboardController::class, 'index'])->name('dashboard');
    Route::get('/summary', [ExecutiveDashboardController::class, 'executiveSummary'])->name('summary');
});

// API Route สำหรับแผนผังองค์กร
Route::get('/api/organization/{company}/data', [OrganizationStructureController::class, 'getOrganizationData'])
    ->name('api.organization.data');

// เพิ่ม route สำหรับการทดสอบ
Route::get('/test/employees', [App\Http\Controllers\TestController::class, 'testEmployees']);

// เพิ่มเส้นทางทดสอบ
Route::get('/test-employee-view', function() {
    return view('test-employee-view');
})->name('test.employee.view');

// เพิ่ม Debug Route ไว้ด้านล่าง middleware
Route::get('/debug/employees', function() {
    return view('debug.employee-status');
})->name('debug.employees');

// เพิ่ม route สำหรับตรวจสอบระบบ
Route::get('/system-check', [App\Http\Controllers\SystemCheckController::class, 'checkSystem'])->name('system.check');

// เพิ่ม route สำหรับหน้าฟีเจอร์ที่กำลังพัฒนา
Route::get('/coming-soon/{feature?}', [App\Http\Controllers\ComingSoonController::class, 'index'])
    ->name('coming-soon');

// Coming Soon Routes
Route::get('/coming-soon/{feature}', function($feature) {
    $featureName = str_replace('-', ' ', $feature);
    $viewPath = 'coming-soon.' . $feature;
    
    // ตรวจสอบว่ามีไฟล์ view นี้หรือไม่
    if (view()->exists($viewPath)) {
        return view($viewPath);
    }
    
    // ถ้าไม่มีไฟล์เฉพาะ ให้ใช้ view ทั่วไป
    return view('coming-soon', ['feature' => $featureName]);
});

// เพิ่ม route สำหรับตรวจสอบข้อมูลสินค้า
Route::get('/debug/products', function () {
    $products = \App\Models\Product::all();
    $categories = \App\Models\ProductCategory::all();
    $units = \App\Models\Unit::all();
    
    return response()->json([
        'products_count' => $products->count(),
        'categories_count' => $categories->count(),
        'units_count' => $units->count(),
        'products' => $products->take(5)->toArray(), // แสดงเฉพาะ 5 รายการแรกเพื่อไม่ให้ข้อมูลเยอะเกินไป
        'user' => auth()->check() ? auth()->user()->only(['id', 'name', 'email', 'company_id']) : 'ยังไม่ได้เข้าสู่ระบบ'
    ]);
})->middleware(['auth'])->name('debug.products');

// เพิ่มเส้นทางเฉพาะกิจสำหรับตรวจสอบการดึงข้อมูลใบเสนอราคา (เฉพาะ local environment)
if (app()->environment('local')) {
    Route::get('/debug/quotations-query', function () {
        $query = \App\Models\Quotation::with(['customer', 'creator']);
        $quotations = $query->limit(5)->get();
        
        return response()->json([
            'count' => $quotations->count(),
            'data' => $quotations,
            'db_config' => [
                'connection' => config('database.default'),
                'database' => config('database.connections.' . config('database.default') . '.database')
            ]
        ]);
    })->middleware('auth');
}

// เพิ่มเส้นทางสำหรับสร้างข้อมูลใบเสนอราคาทดสอบ
Route::get('/debug/create-test-quotations', function () {
    if (!app()->environment('local')) {
        return abort(403);
    }

    $companyId = auth()->user()->company_id;
    $customer = \App\Models\Customer::where('company_id', $companyId)->first();

    if (!$customer) {
        return response()->json(['error' => 'ไม่พบข้อมูลลูกค้า'], 404);
    }

    try {
        // สร้างใบเสนอราคาทดสอบ
        $quotation = \App\Models\Quotation::create([
            'company_id' => $companyId,
            'customer_id' => $customer->id,
            'quotation_number' => 'QT-TEST-' . rand(1000, 9999),
            'issue_date' => now(),
            'expiry_date' => now()->addDays(30),
            'status' => 'draft',
            'subtotal' => 10000,
            'discount_amount' => 0,
            'discount_type' => 'fixed',
            'tax_rate' => 7,
            'tax_amount' => 700,
            'total_amount' => 10700,
            'reference_number' => 'REF-TEST-' . rand(1000, 9999),
            'created_by' => auth()->id(),
            'currency' => 'THB',
            'currency_rate' => 1
        ]);

        return response()->json([
            'message' => 'สร้างใบเสนอราคาทดสอบสำเร็จ',
            'quotation' => $quotation
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->middleware('auth');

// เพิ่มเส้นทางใหม่เพื่อตรวจสอบสถานะ session
Route::get('/debug/session', function() {
    return response()->json([
        'company_id' => session('company_id'),
        'auth_user' => Auth::check() ? Auth::user()->only(['id', 'name', 'email', 'company_id']) : null,
        'all_companies' => DB::table('companies')->get(['id', 'name']),
        'all_session_data' => session()->all()
    ]);
})->middleware('auth');

// นำเข้าเส้นทาง Authentication จากไฟล์ auth.php
require __DIR__ . '/auth.php';
