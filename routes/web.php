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
use App\Http\Controllers\OrganizationStructureController;
use App\Http\Controllers\ComingSoonController;
use App\Http\Controllers\ExecutiveDashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\BranchOfficeController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\NewExecutiveController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\SystemCheckController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\QuotationApiController;
use App\Http\Controllers\InvoiceController;

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

// เพิ่ม route สำหรับ quotations.get-data (API ดึงข้อมูลใบเสนอราคาแบบ JSON)
Route::get('/quotations/{quotation}/get-data', [\App\Http\Controllers\QuotationApiController::class, 'getData'])
    ->name('quotations.get-data')
    ->middleware(['auth']);

Route::get('/quotations/{quotation}/pdf', [App\Http\Controllers\QuotationController::class, 'viewAsPdf'])->name('quotations.pdf');
Route::get('/quotations/{quotation}/print', [App\Http\Controllers\QuotationController::class, 'printView'])->name('quotations.print');
Route::get('/orders/{order}/print', [App\Http\Controllers\OrderController::class, 'printView'])->name('orders.print');

// เพิ่ม route สำหรับการอนุมัติและปฏิเสธใบเสนอราคา
Route::post('/quotations/{quotation}/approve', [App\Http\Controllers\QuotationController::class, 'approve'])
    ->name('quotations.approve')
    ->middleware(['auth']);
Route::post('/quotations/{quotation}/reject', [App\Http\Controllers\QuotationController::class, 'reject'])
    ->name('quotations.reject')
    ->middleware(['auth']);

// เพิ่มเส้นทางสำหรับใบสั่งขาย
Route::resource('orders', OrderController::class);

// เพิ่ม route สำหรับการจัดส่งสินค้า (orders.ship)
Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])
    ->name('orders.ship')
    ->middleware(['auth']);

// เพิ่ม route สำหรับยืนยันใบสั่งขาย (orders.confirm)
Route::post('/orders/{order}/confirm', [OrderController::class, 'confirm'])
    ->name('orders.confirm')
    ->middleware(['auth']);

// เพิ่ม route สำหรับการยกเลิกใบสั่งขาย (orders.cancel)
Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])
    ->name('orders.cancel')
    ->middleware(['auth']);

// เพิ่ม route สำหรับเริ่มดำเนินการใบสั่งขาย (orders.process)
Route::post('/orders/{order}/process', [OrderController::class, 'process'])
    ->name('orders.process')
    ->middleware(['auth']);

// เพิ่ม route สำหรับการบันทึกว่าส่งมอบแล้ว (orders.deliver)
Route::post('/orders/{order}/deliver', [OrderController::class, 'deliver'])
    ->name('orders.deliver')
    ->middleware(['auth']);

// Route to generate a unique order number
Route::get('/orders/generate-order-number', function() {
    return response()->json(['order_number' => \App\Models\Order::generateOrderNumber()]);
})->middleware(['auth']);

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

// Organization Routes
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
    Route::get('/branch-offices/export', [BranchOfficeController::class, 'export'])->name('branch-offices.export');
    Route::resource('branch-offices', BranchOfficeController::class);
    
    // เพิ่ม routes สำหรับ Delivery Orders
    Route::resource('delivery-orders', DeliveryOrderController::class);
    Route::get('api/orders/{id}/products', [OrderController::class, 'getOrderProducts'])
          ->name('api.orders.products');
    
    // เพิ่ม route สำหรับดึงรายการบริษัทที่ผู้ใช้มีสิทธิ์เข้าถึง
    Route::get('/companies/accessible', [CompanyController::class, 'listAccessibleCompanies'])
        ->name('companies.accessible');
    
    // เพิ่ม route แบบเจาะจงสำหรับ API เรียกข้อมูลใบสั่งขาย (โดยเอาไว้ด้านบนสุดเพื่อป้องกันการ conflict)
    Route::get('/api/order-products/{id}', [App\Http\Controllers\OrderController::class, 'getOrderProducts'])
        ->name('api.orders.products');
});

// เปลี่ยน routes ของ Executive Dashboard ให้ใช้เฉพาะ auth เท่านั้นโดยไม่ต้องใช้ middleware เกี่ยวกับ company
Route::middleware(['auth'])->group(function () {
    Route::get('/executive/dashboard', [ExecutiveDashboardController::class, 'index'])->name('executive.dashboard');
    Route::post('/executive/switch-company', [ExecutiveDashboardController::class, 'switchCompany'])->name('executive.switch-company');
    Route::post('/company/request-access', [CompanyController::class, 'requestAccess'])->name('company.request-access');
});

// เพิ่ม Dashboard แบบใหม่ สำหรับผู้บริหาร
Route::middleware(['auth'])->group(function () {
    Route::get('/executive/new-dashboard', [NewExecutiveController::class, 'dashboard'])
        ->name('executive.new-dashboard');
    Route::post('/executive/new-switch-company', [NewExecutiveController::class, 'switchCompany'])
        ->name('executive.new-switch-company');
});

// API Route สำหรับแผนผังองค์กร
Route::get('/api/organization/{company}/data', [OrganizationStructureController::class, 'getOrganizationData'])
    ->name('api.organization.data');

// Debug Routes
Route::get('/test-employee-view', function() {
    return view('test-employee-view');
})->name('test.employee.view');

Route::get('/debug/employees', function() {
    return view('debug.employee-status');
})->name('debug.employees');

Route::get('/system-check', [SystemCheckController::class, 'checkSystem'])->name('system.check');

// Coming Soon Routes
Route::get('/coming-soon/{feature?}', [ComingSoonController::class, 'index'])->name('coming-soon');

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

// นำเข้าเส้นทาง Authentication จากไฟล์ auth.php
require __DIR__ . '/auth.php';

// ส่วนเกี่ยวกับการจัดการบริษัท สำหรับ admin/superadmin
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
});

// สำหรับ user ปกติในการร้องขอสิทธิ์เข้าถึงบริษัท
Route::post('/company/request-access', [CompanyController::class, 'requestAccess'])
    ->name('company.request-access')
    ->middleware(['auth', 'verified']);

// เพิ่ม route สำหรับการดึงเนื้อหา PDF ใบส่งสินค้า
Route::get('/delivery-orders/{delivery_order}/pdf-content', [DeliveryOrderController::class, 'pdfContent'])
    ->name('delivery-orders.pdf-content');

// รองรับการพิมพ์ใบส่งสินค้า
Route::get('/delivery-orders/{deliveryOrder}/print', [DeliveryOrderController::class, 'print'])->name('delivery-orders.print');

// Debug routes - ตั้งค่า middleware เป็น auth เพื่อความปลอดภัย
Route::middleware('auth')->prefix('debug')->group(function () {
    Route::get('/orders/check-connection', [App\Http\Controllers\OrderDebugController::class, 'checkConnection']);
    Route::get('/orders/fix-company-id', [App\Http\Controllers\OrderDebugController::class, 'fixCompanyId']);
});

// Invoice Routes
Route::get('/invoices/generate-invoice-number', [InvoiceController::class, 'generateInvoiceNumber'])->name('invoices.generate-invoice-number');
Route::get('/orders/{order}/get-data', [InvoiceController::class, 'getOrderData'])->name('invoices.get-order-data');
Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

Route::resource('invoices', InvoiceController::class);

Route::post('/invoices/{invoice}/issue', [InvoiceController::class, 'issue'])->name('invoices.issue');
Route::post('/invoices/{invoice}/mark-as-paid', [InvoiceController::class, 'markAsPaid'])->name('invoices.mark-as-paid');
Route::post('/invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');
