<?php
// นำเข้า Facade และ Model ที่จำเป็น
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employee System Debug') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">System Status</h3>
                    
                    <div class="mb-4">
                        <h4 class="font-semibold">Routes:</h4>
                        <ul>
                            <li>Employees Index: <a href="{{ route('employees.index') }}" class="text-blue-600 hover:underline">{{ route('employees.index') }}</a></li>
                            <li>Employees Create: <a href="{{ route('employees.create') }}" class="text-blue-600 hover:underline">{{ route('employees.create') }}</a></li>
                            <li>Test Controller: <a href="{{ route('test.employee.controller') }}" class="text-blue-600 hover:underline">{{ route('test.employee.controller') }}</a></li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="font-semibold">Database Status:</h4>
                        <ul>
                            @php
                            try {
                                $connected = DB::connection()->getPdo() ? true : false;
                                $dbName = DB::connection()->getDatabaseName();
                            } catch (\Exception $e) {
                                $connected = false;
                                $dbName = '';
                                $error = $e->getMessage();
                            }
                            
                            try {
                                $employeeCount = Employee::count();
                                $companyCount = Company::count();
                                $departmentCount = Department::count();
                                $positionCount = Position::count();
                            } catch (\Exception $e) {
                                $employeeCount = 0;
                                $companyCount = 0;
                                $departmentCount = 0;
                                $positionCount = 0;
                            }
                            @endphp
                            
                            <li>Database Connection: 
                                @if($connected)
                                <span class="text-green-600">Connected to {{ $dbName }}</span>
                                @else
                                <span class="text-red-600">Not Connected: {{ $error ?? 'Unknown error' }}</span>
                                @endif
                            </li>
                            <li>Employees Count: <span class="font-bold">{{ $employeeCount }}</span></li>
                            <li>Companies Count: <span class="font-bold">{{ $companyCount }}</span></li>
                            <li>Departments Count: <span class="font-bold">{{ $departmentCount }}</span></li>
                            <li>Positions Count: <span class="font-bold">{{ $positionCount }}</span></li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="font-semibold">File System Check:</h4>
                        <ul>
                            <li>View exists (index): <span class="{{ view()->exists('organization.employees.index') ? 'text-green-600' : 'text-red-600' }}">{{ view()->exists('organization.employees.index') ? 'Yes' : 'No' }}</span></li>
                            <li>View exists (create): <span class="{{ view()->exists('organization.employees.create') ? 'text-green-600' : 'text-red-600' }}">{{ view()->exists('organization.employees.create') ? 'Yes' : 'No' }}</span></li>
                            <li>View exists (edit): <span class="{{ view()->exists('organization.employees.edit') ? 'text-green-600' : 'text-red-600' }}">{{ view()->exists('organization.employees.edit') ? 'Yes' : 'No' }}</span></li>
                            <li>View exists (show): <span class="{{ view()->exists('organization.employees.show') ? 'text-green-600' : 'text-red-600' }}">{{ view()->exists('organization.employees.show') ? 'Yes' : 'No' }}</span></li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="font-semibold">System Information:</h4>
                        <ul>
                            <li>Laravel Version: {{ app()->version() }}</li>
                            <li>PHP Version: {{ phpversion() }}</li>
                            <li>Environment: {{ app()->environment() }}</li>
                        </ul>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="font-semibold">Actions:</h4>
                        <div class="mt-2 flex gap-2">
                            <a href="{{ url('employees') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Go to Employees</a>
                            <a href="{{ url('/') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-700">Back to Home</a>
                            <a href="{{ url('/system-check') }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">System Check</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
