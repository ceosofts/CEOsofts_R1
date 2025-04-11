<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Organization\CompanyController;
use App\Http\Controllers\Organization\DepartmentController;
use App\Http\Controllers\Organization\PositionController;

/*
|--------------------------------------------------------------------------
| Organization Domain Routes
|--------------------------------------------------------------------------
|
| These routes are related to organization management, including companies,
| departments, and positions. All routes in this file are protected by the
| 'auth' and 'ensure.company.access' middleware.
|
*/

// Companies routes (admin only)
Route::middleware(['can:manage-companies'])->group(function () {
    Route::resource('companies', CompanyController::class);
});

// Department routes
Route::resource('departments', DepartmentController::class);

// Position routes
Route::resource('positions', PositionController::class);

// Department structure viewer
Route::get('departments/organization-structure', [DepartmentController::class, 'organizationStructure'])
    ->name('departments.organization-structure');

// Department import/export
Route::get('departments/export', [DepartmentController::class, 'export'])->name('departments.export');
Route::post('departments/import', [DepartmentController::class, 'import'])->name('departments.import');

// Position import/export 
Route::get('positions/export', [PositionController::class, 'export'])->name('positions.export');
Route::post('positions/import', [PositionController::class, 'import'])->name('positions.import');
