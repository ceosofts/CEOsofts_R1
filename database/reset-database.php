<?php

// สคริปต์สำหรับรีเซ็ตและสร้างโครงสร้างฐานข้อมูลใหม่
// รันด้วยคำสั่ง: php database/reset-database.php

$dbPath = __DIR__ . '/database.sqlite';

// ลบและสร้างไฟล์ฐานข้อมูล SQLite ใหม่
if (file_exists($dbPath)) {
    unlink($dbPath);
}
touch($dbPath);

// เชื่อมต่อกับฐานข้อมูล
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// สร้างตารางพื้นฐาน
$tables = [
    // ตาราง migrations
    "CREATE TABLE migrations (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        migration VARCHAR NOT NULL,
        batch INTEGER NOT NULL
    )",

    // ตาราง cache
    "CREATE TABLE cache (
        key VARCHAR PRIMARY KEY,
        value TEXT NOT NULL,
        expiration INTEGER NOT NULL
    )",

    // ตาราง companies
    "CREATE TABLE companies (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        ulid VARCHAR NOT NULL,
        code VARCHAR NULL,
        name VARCHAR NOT NULL,
        tax_id VARCHAR NULL,
        address TEXT NULL,
        phone VARCHAR NULL,
        email VARCHAR NULL,
        website VARCHAR NULL,
        logo VARCHAR NULL,
        is_active BOOLEAN NOT NULL DEFAULT 1,
        status VARCHAR NULL,
        settings TEXT NULL,
        metadata TEXT NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL,
        deleted_at DATETIME NULL,
        uuid VARCHAR NOT NULL
    )"
];

// สร้างตาราง
foreach ($tables as $sql) {
    $pdo->exec($sql);
}

// สร้าง index
$pdo->exec("CREATE UNIQUE INDEX companies_ulid_unique ON companies (ulid)");
$pdo->exec("CREATE UNIQUE INDEX companies_uuid_unique ON companies (uuid)");

echo "ฐานข้อมูลถูกรีเซ็ตและสร้างตารางพื้นฐานใหม่แล้ว\n";
