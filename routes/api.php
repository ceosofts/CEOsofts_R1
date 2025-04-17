<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrganizationStructureController;

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
