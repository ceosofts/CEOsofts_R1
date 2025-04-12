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
| departments, positions, and branch offices.
|
*/

// องค์กร: เส้นทางสำหรับการจัดการข้อมูลบริษัท
Route::middleware(['web'])->group(function () {
    // Companies
    Route::resource('companies', CompanyController::class);

    // สร้าง routes เฉพาะเมื่อ class controller มีอยู่จริง
    if (class_exists(DepartmentController::class)) {
        Route::resource('departments', DepartmentController::class);

        // Department structure viewer
        Route::get('departments/organization-structure', [DepartmentController::class, 'organizationStructure'])
            ->name('departments.organization-structure');

        // Department import/export
        Route::get('departments/export', [DepartmentController::class, 'export'])->name('departments.export');
        Route::post('departments/import', [DepartmentController::class, 'import'])->name('departments.import');
    }

    // สร้าง routes เฉพาะเมื่อ class controller มีอยู่จริง
    if (class_exists(PositionController::class)) {
        Route::resource('positions', PositionController::class);

        // Position import/export 
        Route::get('positions/export', [PositionController::class, 'export'])->name('positions.export');
        Route::post('positions/import', [PositionController::class, 'import'])->name('positions.import');
    }
});
