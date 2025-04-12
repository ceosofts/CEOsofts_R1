<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
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

// Route สำหรับบริษัท
Route::resource('companies', CompanyController::class);

// ทดสอบเส้นทาง
Route::get('/test-companies', function () {
    return "Companies test route is working!";
});
