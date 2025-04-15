<!DOCTYPE html>
<html>
<head>
    <title>Employees - Simple Page</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .card { border: 1px solid #ddd; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>รายการพนักงาน</h1>
            <p>นี่คือหน้าแสดงรายการพนักงานแบบเรียบง่าย</p>
            
            @if(isset($employees) && count($employees) > 0)
                <h3>พนักงานทั้งหมด {{ count($employees) }} คน</h3>
                <ul>
                @foreach($employees as $employee)
                    <li>{{ $employee->first_name }} {{ $employee->last_name }}</li>
                @endforeach
                </ul>
            @else
                <p>ไม่พบข้อมูลพนักงาน</p>
            @endif
            
            <p><a href="{{ route('employees.create') }}">เพิ่มพนักงานใหม่</a></p>
            <p><a href="{{ url('/') }}">กลับหน้าหลัก</a></p>
        </div>
    </div>
</body>
</html>
