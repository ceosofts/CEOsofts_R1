<?php

/**
 * สคริปต์สร้างตารางสำหรับ Spatie Permission Package อย่างเด็ดขาด
 */

echo "เริ่มการสร้างตารางสำหรับ Spatie Permission Package...\n";

// เชื่อมต่อกับฐานข้อมูล
$dbPath = __DIR__ . '/database/database.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 1. สร้างตาราง permissions
echo "กำลังสร้างตาราง permissions...\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS permissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        guard_name VARCHAR(255) NOT NULL,
        group VARCHAR(255) NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL
    )
");

// สร้าง unique index
try {
    $pdo->exec("CREATE UNIQUE INDEX permissions_name_guard_name_unique ON permissions (name, guard_name)");
    echo "✅ สร้าง index permissions_name_guard_name_unique สำเร็จ\n";
} catch (PDOException $e) {
    echo "⚠️ index permissions_name_guard_name_unique อาจมีอยู่แล้ว\n";
}

// 2. สร้างตาราง roles
echo "กำลังสร้างตาราง roles...\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS roles (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        guard_name VARCHAR(255) NOT NULL,
        description TEXT NULL,
        is_system BOOLEAN DEFAULT 0,
        created_at DATETIME NULL,
        updated_at DATETIME NULL
    )
");

// สร้าง unique index
try {
    $pdo->exec("CREATE UNIQUE INDEX roles_name_guard_name_unique ON roles (name, guard_name)");
    echo "✅ สร้าง index roles_name_guard_name_unique สำเร็จ\n";
} catch (PDOException $e) {
    echo "⚠️ index roles_name_guard_name_unique อาจมีอยู่แล้ว\n";
}

// 3. สร้างตาราง role_has_permissions
echo "กำลังสร้างตาราง role_has_permissions...\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS role_has_permissions (
        permission_id BIGINT UNSIGNED NOT NULL,
        role_id BIGINT UNSIGNED NOT NULL,
        PRIMARY KEY (permission_id, role_id)
    )
");

// 4. สร้างตาราง model_has_roles
echo "กำลังสร้างตาราง model_has_roles...\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS model_has_roles (
        role_id BIGINT UNSIGNED NOT NULL,
        model_type VARCHAR(255) NOT NULL,
        model_id BIGINT UNSIGNED NOT NULL,
        PRIMARY KEY (role_id, model_id, model_type)
    )
");

// 5. สร้างตาราง model_has_permissions
echo "กำลังสร้างตาราง model_has_permissions...\n";
$pdo->exec("
    CREATE TABLE IF NOT EXISTS model_has_permissions (
        permission_id BIGINT UNSIGNED NOT NULL,
        model_type VARCHAR(255) NOT NULL,
        model_id BIGINT UNSIGNED NOT NULL,
        PRIMARY KEY (permission_id, model_id, model_type)
    )
");

// 6. ตรวจสอบว่าตารางทั้งหมดถูกสร้างแล้ว
echo "\nกำลังตรวจสอบตารางที่สร้าง...\n";
$tables = ['permissions', 'roles', 'role_has_permissions', 'model_has_roles', 'model_has_permissions'];

foreach ($tables as $table) {
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$table'");
    $exists = $stmt->fetchColumn();

    if ($exists) {
        echo "✅ ตาราง $table สร้างเรียบร้อยแล้ว\n";
    } else {
        echo "❌ ตาราง $table ยังไม่ถูกสร้าง!\n";
    }
}

// 7. บันทึกลงในตาราง migrations เพื่อให้ Laravel ทราบว่าได้ทำการ migrate แล้ว
echo "\nกำลังบันทึกข้อมูลลงในตาราง migrations...\n";
$migrationNames = [
    '2024_08_01_000018_create_roles_and_permissions_tables'
];

$stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?) ON CONFLICT (migration) DO NOTHING");
$batch = $pdo->query("SELECT MAX(batch) FROM migrations")->fetchColumn() + 1;

foreach ($migrationNames as $migration) {
    $stmt->execute([$migration, $batch]);
    echo "✅ บันทึก migration '$migration' สำเร็จ\n";
}

echo "\n===== สร้างตาราง Permissions เสร็จสมบูรณ์ =====\n";
echo "กรุณาลองรันคำสั่ง: php artisan db:seed อีกครั้ง\n";
