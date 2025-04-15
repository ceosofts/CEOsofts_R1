<!DOCTYPE html>
<html>
<head>
    <title>Test Employee Page</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Test Employee Page</h1>
    
    <p>This is a test page to check if blade views are working correctly</p>
    
    <div class="success">If you can see this page, the view engine is working correctly!</div>
    
    <h2>System Information:</h2>
    <ul>
        <li>PHP Version: {{ phpversion() }}</li>
        <li>Laravel Version: {{ app()->version() }}</li>
        <li>Environment: {{ app()->environment() }}</li>
    </ul>
    
    <h2>Available Routes:</h2>
    <ul>
        <li><a href="{{ route('employees.index') }}">Employees Index</a></li>
        <li><a href="{{ route('employees.create') }}">Create New Employee</a></li>
        <li><a href="{{ route('debug.employees') }}">Debug Employees</a></li>
    </ul>

</body>
</html>
