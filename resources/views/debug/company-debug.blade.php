<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Company Data</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 20px;
        }

        .card {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .error {
            color: red;
        }

        pre {
            background: #eee;
            padding: 10px;
            overflow: auto;
        }
    </style>
</head>

<body>
    <h1>Debug Company Data</h1>

    @if(isset($company))
    <div class="card">
        <h2>Company Data Found</h2>
        <p><strong>ID:</strong> {{ $company->id ?? 'ไม่มีข้อมูล' }}</p>
        <p><strong>Name:</strong> {{ $company->name ?? 'ไม่มีข้อมูล' }}</p>
        <p><strong>Code:</strong> {{ $company->code ?? 'ไม่มีข้อมูล' }}</p>
        <p><strong>Type:</strong> {{ get_class($company) }}</p>
    </div>

    <div class="card">
        <h2>Raw Company Data</h2>
        <pre>{{ print_r($company->toArray(), true) }}</pre>
    </div>

    <div class="card">
        <h2>Relationships</h2>
        <p><strong>Has Departments Method:</strong> {{ method_exists($company, 'departments') ? 'Yes' : 'No' }}</p>
        <p><strong>Has Positions Method:</strong> {{ method_exists($company, 'positions') ? 'Yes' : 'No' }}</p>
        <p><strong>Has Employees Method:</strong> {{ method_exists($company, 'employees') ? 'Yes' : 'No' }}</p>
        <hr>
        <p><strong>Departments Count:</strong> {{ isset($company->departments) ? $company->departments->count() : 'Not loaded' }}</p>
        <p><strong>Positions Count:</strong> {{ isset($company->positions) ? $company->positions->count() : 'Not loaded' }}</p>
        <p><strong>Employees Count:</strong> {{ isset($company->employees) ? $company->employees->count() : 'Not loaded' }}</p>
    </div>
    @else
    <div class="error">
        <h2>Error: No Company Data</h2>
        <p>The company variable is not defined or is null.</p>
    </div>
    @endif

    <div class="card">
        <h2>Request Information</h2>
        <p><strong>Current Route:</strong> {{ request()->route()->getName() }}</p>
        <p><strong>Current URL:</strong> {{ request()->url() }}</p>
        <p><strong>Request Method:</strong> {{ request()->method() }}</p>
    </div>
</body>

</html>