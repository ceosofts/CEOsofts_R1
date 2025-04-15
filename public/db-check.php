<?php
/**
 * Database Check Script
 * This file is for directly testing database connection without Laravel framework.
 */

// Show all errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

$dbFile = __DIR__ . '/../database/ceosofts_db_R1.sqlite';

echo '<html><head><title>Database Direct Check</title>';
echo '<style>body{font-family:Arial,sans-serif;line-height:1.6;padding:20px;} 
.success{color:green;font-weight:bold} .error{color:red;font-weight:bold}</style>';
echo '</head><body>';
echo '<h1>Database Direct Check</h1>';

// Check if file exists
echo '<h2>SQLite File Check</h2>';
if (file_exists($dbFile)) {
    echo "<p class='success'>SQLite file exists at: {$dbFile}</p>";
    echo "<p>File size: " . round(filesize($dbFile) / 1048576, 2) . " MB</p>";
    echo "<p>File permissions: " . substr(sprintf('%o', fileperms($dbFile)), -4) . "</p>";
    echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($dbFile)) . "</p>";
} else {
    echo "<p class='error'>SQLite file does not exist at: {$dbFile}</p>";
}

// Try to connect using PDO directly
echo '<h2>PDO Connection Test</h2>';
try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p class='success'>Successfully connected to SQLite database!</p>";
    
    // Check if tables exist
    $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    
    echo '<h3>Tables in Database:</h3>';
    echo '<ul>';
    $tableCount = 0;
    while ($table = $tables->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>{$table['name']}</li>";
        $tableCount++;
    }
    echo '</ul>';
    
    if ($tableCount === 0) {
        echo "<p class='error'>No tables found in the database.</p>";
    } else {
        echo "<p class='success'>Found {$tableCount} tables in the database.</p>";
        
        // Check some important tables
        $checkTables = ['users', 'employees', 'companies', 'departments', 'positions'];
        echo '<h3>Important Tables Check:</h3>';
        echo '<ul>';
        
        foreach ($checkTables as $tableName) {
            try {
                $count = $pdo->query("SELECT COUNT(*) FROM {$tableName}")->fetchColumn();
                echo "<li>{$tableName}: <span class='success'>Available</span> ({$count} records)</li>";
            } catch (PDOException $e) {
                echo "<li>{$tableName}: <span class='error'>Not available</span> - {$e->getMessage()}</li>";
            }
        }
        
        echo '</ul>';
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>Failed to connect: {$e->getMessage()}</p>";
}

echo '<h2>Actions</h2>';
echo '<ul>';
echo '<li><a href="/">Return to home page</a></li>';
echo '<li><a href="/system-check">Go to system check</a></li>';
echo '<li><a href="/employees">Go to employees page</a></li>';
echo '</ul>';

echo '</body></html>';
