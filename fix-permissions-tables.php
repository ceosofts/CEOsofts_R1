<?php

/**
 * สคริปต์สำหรับสร้างตาราง Permissions และตารางที่เกี่ยวข้อง
 */

echo "เริ่มการสร้างตาราง Permissions และตารางที่เกี่ยวข้อง...\n";

// เชื่อมต่อกับฐานข้อมูล SQLite
$dbPath = __DIR__ . '/database/database.sqlite';
$pdo = new PDO('sqlite:' . $dbPath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ฟังก์ชันสำหรับตรวจสอบว่าตารางมีอยู่แล้วหรือไม่
function tableExists(PDO $pdo, string $tableName): bool
{
    $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='$tableName'");
    return (bool) $stmt->fetchColumn();
}

// 1. สร้างตาราง permissions
if (!tableExists($pdo, 'permissions')) {
    echo "กำลังสร้างตาราง permissions...\n";
    $pdo->exec("
        CREATE TABLE permissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL,
            group VARCHAR(255) NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        )
    ");
    $pdo->exec("CREATE UNIQUE INDEX permissions_name_guard_name_unique ON permissions (name, guard_name)");
    echo "✅ ตาราง permissions ถูกสร้างเรียบร้อยแล้ว\n";
} else {
    echo "⚠️ ตาราง permissions มีอยู่แล้ว\n";
}

// 2. สร้างตาราง roles
if (!tableExists($pdo, 'roles')) {
    echo "กำลังสร้างตาราง roles...\n";
    $pdo->exec("
        CREATE TABLE roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            guard_name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            is_system BOOLEAN DEFAULT 0,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        )
    ");
    $pdo->exec("CREATE UNIQUE INDEX roles_name_guard_name_unique ON roles (name, guard_name)");
    echo "✅ ตาราง roles ถูกสร้างเรียบร้อยแล้ว\n";
} else {
    echo "⚠️ ตาราง roles มีอยู่แล้ว\n";
}

// 3. สร้างตาราง role_has_permissions
if (!tableExists($pdo, 'role_has_permissions')) {
    echo "กำลังสร้างตาราง role_has_permissions...\n";
    $pdo->exec("
        CREATE TABLE role_has_permissions (
            permission_id BIGINT UNSIGNED NOT NULL,
            role_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (permission_id, role_id)
        )
    ");
    echo "✅ ตาราง role_has_permissions ถูกสร้างเรียบร้อยแล้ว\n";
} else {
    echo "⚠️ ตาราง role_has_permissions มีอยู่แล้ว\n";
}

// 4. สร้างตาราง model_has_roles
if (!tableExists($pdo, 'model_has_roles')) {
    echo "กำลังสร้างตาราง model_has_roles...\n";
    $pdo->exec("
        CREATE TABLE model_has_roles (
            role_id BIGINT UNSIGNED NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (role_id, model_id, model_type)
        )
    ");
    echo "✅ ตาราง model_has_roles ถูกสร้างเรียบร้อยแล้ว\n";
} else {
    echo "⚠️ ตาราง model_has_roles มีอยู่แล้ว\n";
}

// 5. สร้างตาราง model_has_permissions
if (!tableExists($pdo, 'model_has_permissions')) {
    echo "กำลังสร้างตาราง model_has_permissions...\n";
    $pdo->exec("
        CREATE TABLE model_has_permissions (
            permission_id BIGINT UNSIGNED NOT NULL,
            model_type VARCHAR(255) NOT NULL,
            model_id BIGINT UNSIGNED NOT NULL,
            PRIMARY KEY (permission_id, model_id, model_type)
        )
    ");
    echo "✅ ตาราง model_has_permissions ถูกสร้างเรียบร้อยแล้ว\n";
} else {
    echo "⚠️ ตาราง model_has_permissions มีอยู่แล้ว\n";
}

echo "\n===== การสร้างตาราง Permissions และตารางที่เกี่ยวข้องเสร็จสมบูรณ์ =====\n";
echo "กรุณาลองรันคำสั่ง: php artisan db:seed อีกครั้ง\n";
