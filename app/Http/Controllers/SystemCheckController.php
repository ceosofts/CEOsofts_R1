<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\File;

class SystemCheckController extends Controller
{
    /**
     * Check system status and show detailed information
     *
     * @return \Illuminate\Http\Response
     */
    public function checkSystem()
    {
        // Show all PHP errors
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        
        try {
            // Test DB connection
            $dbConnected = false;
            $dbName = '';
            $dbError = '';
            try {
                $dbConnected = DB::connection()->getPdo() ? true : false;
                $dbName = DB::connection()->getDatabaseName();
            } catch (\Exception $e) {
                $dbError = $e->getMessage();
            }
            
            // Check if tables exist using direct SQL query instead of getDoctrineSchemaManager
            $tablesExist = false;
            $tablesList = [];
            $tablesError = '';
            try {
                if (DB::connection()->getDriverName() === 'sqlite') {
                    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
                    $tablesList = collect($tables)->pluck('name')->toArray();
                } else {
                    $tables = DB::select('SHOW TABLES');
                    $tablesList = array_map('current', json_decode(json_encode($tables), true));
                }
                
                $tablesExist = !empty($tablesList);
            } catch (\Exception $e) {
                $tablesError = $e->getMessage();
            }
            
            // Count records in key tables
            $employeeCount = 0;
            $companyCount = 0;
            $departmentCount = 0;
            $positionCount = 0;
            try {
                $employeeCount = DB::table('employees')->count();
                $companyCount = DB::table('companies')->count();
                $departmentCount = DB::table('departments')->count();
                $positionCount = DB::table('positions')->count();
            } catch (\Exception $e) {
                // Silently fail if tables don't exist
            }
            
            // Check view paths
            $viewPaths = View::getFinder()->getPaths();
            
            // Check if specific view files exist
            $employeeIndexExists = File::exists(resource_path('views/organization/employees/index.blade.php'));
            $employeeCreateExists = File::exists(resource_path('views/organization/employees/create.blade.php'));
            $debugEmployeesExists = File::exists(resource_path('views/debug/employee-status.blade.php'));
            
            // Check directory permissions
            $storageWritable = is_writable(storage_path());
            $viewsWritable = is_writable(resource_path('views'));
            
            // Output as raw HTML for maximum compatibility
            $output = "<!DOCTYPE html>
            <html>
            <head>
                <title>System Check Report</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
                    h1, h2 { color: #333; }
                    .success { color: green; font-weight: bold; }
                    .error { color: red; font-weight: bold; }
                    .warning { color: orange; font-weight: bold; }
                    .section { margin-bottom: 30px; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
                    .code { font-family: monospace; background: #f5f5f5; padding: 10px; border: 1px solid #ddd; overflow: auto; }
                    ul { margin-top: 5px; }
                    table { width: 100%; border-collapse: collapse; }
                    table, th, td { border: 1px solid #ddd; }
                    th, td { padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <h1>CEOsofts R1 System Check Report</h1>
                <p>Generated at: " . date('Y-m-d H:i:s') . "</p>
                
                <div class='section'>
                    <h2>Database Connection</h2>
                    <ul>
                        <li>Status: " . ($dbConnected ? "<span class='success'>Connected</span>" : "<span class='error'>Failed</span>") . "</li>
                        <li>Database: " . htmlspecialchars($dbName) . "</li>
                        " . ($dbError ? "<li>Error: <span class='error'>" . htmlspecialchars($dbError) . "</span></li>" : "") . "
                    </ul>
                </div>
                
                <div class='section'>
                    <h2>Database Tables</h2>
                    <ul>
                        <li>Tables Found: " . ($tablesExist ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "</li>
                        <li>Total Tables: " . count($tablesList) . "</li>
                        " . ($tablesError ? "<li>Error: <span class='error'>" . htmlspecialchars($tablesError) . "</span></li>" : "") . "
                    </ul>
                    
                    <h3>Table List</h3>
                    <div class='code'>" . implode(", ", $tablesList) . "</div>
                    
                    <h3>Record Counts</h3>
                    <table>
                        <tr>
                            <th>Table</th>
                            <th>Count</th>
                        </tr>
                        <tr>
                            <td>employees</td>
                            <td>" . $employeeCount . "</td>
                        </tr>
                        <tr>
                            <td>companies</td>
                            <td>" . $companyCount . "</td>
                        </tr>
                        <tr>
                            <td>departments</td>
                            <td>" . $departmentCount . "</td>
                        </tr>
                        <tr>
                            <td>positions</td>
                            <td>" . $positionCount . "</td>
                        </tr>
                    </table>
                </div>
                
                <div class='section'>
                    <h2>View Files</h2>
                    <ul>
                        <li>Views Directory: <span class='" . ($viewsWritable ? "success" : "error") . "'>" . ($viewsWritable ? "Writable" : "Not Writable") . "</span></li>
                        <li>employees/index.blade.php: " . ($employeeIndexExists ? "<span class='success'>Exists</span>" : "<span class='error'>Missing</span>") . "</li>
                        <li>employees/create.blade.php: " . ($employeeCreateExists ? "<span class='success'>Exists</span>" : "<span class='error'>Missing</span>") . "</li>
                        <li>debug/employee-status.blade.php: " . ($debugEmployeesExists ? "<span class='success'>Exists</span>" : "<span class='error'>Missing</span>") . "</li>
                    </ul>
                </div>
                
                <div class='section'>
                    <h2>View Paths</h2>
                    <div class='code'>" . implode("<br>", $viewPaths) . "</div>
                </div>
                
                <div class='section'>
                    <h2>Directory Permissions</h2>
                    <ul>
                        <li>Storage Directory: <span class='" . ($storageWritable ? "success" : "error") . "'>" . ($storageWritable ? "Writable" : "Not Writable") . "</span></li>
                    </ul>
                </div>
                
                <div class='section'>
                    <h2>Environment</h2>
                    <ul>
                        <li>Laravel Version: " . app()->version() . "</li>
                        <li>PHP Version: " . phpversion() . "</li>
                        <li>Environment: " . app()->environment() . "</li>
                    </ul>
                </div>
                
                <div class='section'>
                    <h2>Actions</h2>
                    <ul>
                        <li><a href='" . url('/test-employee-view') . "'>Test Employee View</a></li>
                        <li><a href='" . url('/debug/employees') . "'>Debug Employees</a></li>
                        <li><a href='" . url('/employees') . "'>Employees Page</a></li>
                        <li><a href='" . url('/') . "'>Home</a></li>
                    </ul>
                </div>
                
                <div class='section'>
                    <h2>Laravel Artisan Commands</h2>
                    <p>Run these commands in terminal to fix common issues:</p>
                    <div class='code'>
                    php artisan cache:clear<br>
                    php artisan view:clear<br>
                    php artisan route:clear<br>
                    php artisan optimize:clear<br>
                    php artisan storage:link
                    </div>
                </div>
            </body>
            </html>";
            
            return response($output);
            
        } catch (\Exception $e) {
            // Handle any unexpected errors
            return response("Error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine(), 500);
        }
    }
}
