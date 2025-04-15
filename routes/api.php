<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// API สำหรับการเลือกบริษัทและดึงข้อมูลพนักงานด้วย AJAX
Route::middleware('auth:sanctum')->get('/set-company/{company}/get-employees', function (App\Models\Company $company) {
    // เก็บ company ID ใน session
    session(['current_company_id' => $company->id]);
    
    // ดึงข้อมูลพนักงานของบริษัทที่เลือก
    $employees = \App\Models\Employee::where('company_id', $company->id)
        ->with(['department', 'position'])
        ->paginate(10);
    
    // Render HTML
    $html = view('organization.employees._employee_table', [
        'employees' => $employees
    ])->render();
    
    return response()->json([
        'success' => true,
        'html' => $html,
        'company' => $company->only(['id', 'name']),
        'pagination' => [
            'total' => $employees->total(),
            'current_page' => $employees->currentPage(),
            'last_page' => $employees->lastPage(),
        ]
    ]);
});
