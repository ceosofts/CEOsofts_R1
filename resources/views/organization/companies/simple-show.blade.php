<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลบริษัทแบบพื้นฐาน</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        .row {
            display: flex;
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            width: 150px;
        }

        .value {
            flex: 1;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>ข้อมูลบริษัท (แบบพื้นฐาน)</h1>

        <div class="card">
            <div class="row">
                <div class="label">ID:</div>
                <div class="value">{{ $company->id }}</div>
            </div>
            <div class="row">
                <div class="label">ชื่อบริษัท:</div>
                <div class="value">{{ $company->name }}</div>
            </div>
            <div class="row">
                <div class="label">รหัส:</div>
                <div class="value">{{ $company->code ?? 'ไม่ระบุ' }}</div>
            </div>
            <div class="row">
                <div class="label">อีเมล:</div>
                <div class="value">{{ $company->email ?? 'ไม่ระบุ' }}</div>
            </div>
            <div class="row">
                <div class="label">เบอร์โทรศัพท์:</div>
                <div class="value">{{ $company->phone ?? 'ไม่ระบุ' }}</div>
            </div>
            <div class="row">
                <div class="label">สถานะ:</div>
                <div class="value">{{ $company->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</div>
            </div>
            <div class="row">
                <div class="label">วันที่สร้าง:</div>
                <div class="value">{{ $company->created_at }}</div>
            </div>
        </div>

        <a href="{{ route('companies.index') }}" class="back-link">กลับไปยังรายการบริษัท</a>
    </div>
</body>

</html>