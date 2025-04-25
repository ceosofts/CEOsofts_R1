<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationStructureController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QuotationApiController;
use App\Http\Controllers\DeliveryOrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// API สำหรับจัดการใบเสนอราคา
Route::get('/quotations/{quotation}/products', [QuotationApiController::class, 'getQuotationProducts']);

// เพิ่ม API route สำหรับดึงข้อมูลสินค้าในใบสั่งขาย
// ใช้ web middleware แทน auth:sanctum เพื่อให้ใช้ session auth และ CSRF protection
Route::middleware('web')->group(function () {
    Route::get('/orders/{order}/products', [OrderController::class, 'getOrderProducts']);
});

// เพิ่ม API endpoint สำหรับดึงข้อมูลสินค้าในใบสั่งขาย (แบบไม่ใช้ Route Model Binding)
Route::get('/orders/{orderId}/products', [OrderController::class, 'getOrderProducts'])
    ->middleware('web');

// ย้าย API endpoint มาที่นี่แทนเพื่อใช้ middleware api
Route::get('/organization/{company}/data', [OrganizationStructureController::class, 'getOrganizationData'])
    ->name('api.organization.data');

// API สำหรับสร้างรหัสพนักงาน
Route::get('/generate-employee-code/{companyId}', function($companyId) {
    $code = \App\Models\Employee::generateEmployeeCode($companyId);
    return response()->json(['code' => $code]);
});

// API สำหรับดึงรหัสสาขาถัดไป
Route::get('/branch-offices/next-code/{companyId}', function ($companyId) {
    $nextCode = \App\Models\BranchOffice::generateBranchCode($companyId);
    return response()->json(['code' => $nextCode]);
});

// API สำหรับดึงข้อมูลลูกค้า
Route::get('/customers/{customer}', function (App\Models\Customer $customer) {
    return response()->json([
        'id' => $customer->id,
        'name' => $customer->name,
        'address' => $customer->address,
        'phone' => $customer->phone,
        'email' => $customer->email,
    ]);
});

// แก้ไขเส้นทาง API โดยไม่ใช้ middleware web และกำหนดชื่อที่ชัดเจน
Route::get('orders/{id}/products', [OrderController::class, 'getOrderProducts'])->name('api.orders.products');

// เพิ่ม route สำหรับสร้างเลขที่ใบส่งสินค้าอัตโนมัติ
Route::get('/generate-delivery-number', [DeliveryOrderController::class, 'generateDeliveryNumber']);
