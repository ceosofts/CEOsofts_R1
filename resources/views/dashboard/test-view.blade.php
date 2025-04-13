<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CEOsofts R1') }}</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            line-height: 1.5;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            color: #333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>CEOsofts R1 Dashboard</h1>
        <p>นี่คือหน้าแดชบอร์ดอย่างง่าย ใช้สำหรับทดสอบการแสดงผล</p>

        @if(isset($stats))
        <div style="margin-top: 2rem;">
            <h2>สถิติระบบ</h2>
            <ul>
                <li>บริษัท: {{ $stats['companies'] ?? 0 }}</li>
                <li>แผนก: {{ $stats['departments'] ?? 0 }}</li>
                <li>ตำแหน่ง: {{ $stats['positions'] ?? 0 }}</li>
                <li>พนักงาน: {{ $stats['employees'] ?? 0 }}</li>
            </ul>
        </div>
        @else
        <p>ไม่มีข้อมูลสถิติ</p>
        @endif
    </div>
</body>

</html>