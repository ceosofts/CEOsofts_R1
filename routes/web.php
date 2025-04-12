<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\Organization\CompanyController;

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
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route สำหรับบริษัท - ลบ middleware 'web' เพราะซ้ำซ้อนและอาจทำให้เกิดปัญหา
// และกำหนดให้เข้าถึงได้โดยไม่ต้อง auth ก่อน
Route::resource('companies', CompanyController::class);

// ทดสอบเส้นทางเพื่อ debug
Route::get('/test-companies', function () {
    return "Companies test route is working!";
});

// เพิ่ม debug route เพื่อตรวจสอบข้อมูล
Route::get('/debug-companies', function () {
    $companies = \App\Models\Company::all();
    return response()->json([
        'count' => $companies->count(),
        'data' => $companies->toArray()
    ]);
});

// เพิ่ม debug routes
Route::get('/debug/companies', [DebugController::class, 'companies']);
