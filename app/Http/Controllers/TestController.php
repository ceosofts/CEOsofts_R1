<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\BranchOffice;

class TestController extends Controller
{
    /**
     * Test method for employees
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function testEmployees()
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Test Employees endpoint is working',
            'timestamp' => now(),
            'data' => [
                'total_employees' => Employee::count(),
                'available_models' => [
                    'Employee', 'Company', 'Department', 'Position', 'BranchOffice'
                ]
            ]
        ]);
    }
}
