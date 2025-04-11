<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbFixSchemaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:fix-schema {table? : ตารางที่ต้องการแก้ไข (ถ้าไม่ระบุจะตรวจสอบทุกตาราง)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ตรวจสอบและแก้ไขโครงสร้างตารางในฐานข้อมูล';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tableName = $this->argument('table');
        
        if ($tableName) {
            // ตรวจสอบและแก้ไขเฉพาะตารางที่ระบุ
            $this->fixTable($tableName);
        } else {
            // ตรวจสอบทุกตาราง
            $this->fixAllTables();
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * แก้ไขตารางทั้งหมด
     */
    protected function fixAllTables()
    {
        $this->info('กำลังตรวจสอบทุกตารางในฐานข้อมูล...');
        
        $tables = $this->getTables();
        
        if (empty($tables)) {
            $this->info('ไม่พบตารางในฐานข้อมูล');
            return;
        }
        
        $this->info('พบทั้งหมด ' . count($tables) . ' ตาราง');
        
        foreach ($tables as $table) {
            $this->fixTable($table);
        }
    }
    
    /**
     * ดึงรายชื่อตารางทั้งหมดในฐานข้อมูล
     */
    protected function getTables()
    {
        $connection = config('database.default');
        
        if ($connection === 'sqlite') {
            $query = "SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'";
            return array_column(DB::select($query), 'name');
        } else {
            return Schema::getConnection()->getDoctrineSchemaManager()->listTableNames();
        }
    }
    
    /**
     * แก้ไขตารางเดี่ยว
     */
    protected function fixTable($table)
    {
        $this->info("กำลังตรวจสอบตาราง: {$table}");
        
        if (!Schema::hasTable($table)) {
            $this->error("ไม่พบตาราง {$table} ในฐานข้อมูล");
            return;
        }
        
        // รายการปัญหาที่ต้องตรวจสอบ
        $this->checkIndexes($table);
        $this->checkNullableColumns($table);
        $this->checkForeignKeys($table);
        
        $this->info("เสร็จสิ้นการตรวจสอบตาราง {$table}");
        $this->newLine();
    }
    
    /**
     * ตรวจสอบ indexes ของตาราง
     */
    protected function checkIndexes($table)
    {
        $this->line("  <fg=yellow>• ตรวจสอบ indexes...</>");
        
        // ตรวจสอบคอลัมน์ที่ควรมี index แต่ยังไม่มี
        $columnsToIndex = [
            'company_id', 'user_id', 'customer_id', 'product_id', 'order_id',
            'invoice_id', 'quotation_id', 'department_id', 'position_id', 'employee_id',
            'email', 'slug', 'uuid'
        ];
        
        $columns = Schema::getColumnListing($table);
        $indexes = $this->getTableIndexes($table);
        
        $columnsToAddIndex = [];
        
        foreach ($columns as $column) {
            if (in_array($column, $columnsToIndex)) {
                // ตรวจสอบว่าคอลัมน์นี้มี index หรือยัง
                $hasIndex = false;
                
                foreach ($indexes as $index) {
                    if ($index['column_name'] === $column) {
                        $hasIndex = true;
                        break;
                    }
                }
                
                if (!$hasIndex) {
                    $columnsToAddIndex[] = $column;
                }
            }
        }
        
        if (!empty($columnsToAddIndex)) {
            $this->warn("    พบคอลัมน์ที่ควรมี index แต่ยังไม่มี: " . implode(', ', $columnsToAddIndex));
            
            if ($this->confirm('ต้องการเพิ่ม indexes ให้คอลัมน์เหล่านี้หรือไม่?')) {
                Schema::table($table, function ($table) use ($columnsToAddIndex) {
                    foreach ($columnsToAddIndex as $column) {
                        $table->index($column);
                        $this->line("    <fg=green>✓ เพิ่ม index ให้คอลัมน์ {$column}</>");
                    }
                });
            }
        } else {
            $this->line("    <fg=green>✓ ไม่พบปัญหาเกี่ยวกับ indexes</>");
        }
    }
    
    /**
     * ตรวจสอบคอลัมน์ที่ควรเป็น nullable
     */
    protected function checkNullableColumns($table)
    {
        $this->line("  <fg=yellow>• ตรวจสอบ nullable columns...</>");
        
        // คอลัมน์ที่มักจะต้องเป็น nullable
        $nullableColumns = [
            'description', 'notes', 'details', 'remark', 'comment',
            'deleted_at', 'metadata', 'settings', 'options'
        ];
        
        $columns = Schema::getColumnListing($table);
        $columnsToFix = [];
        
        foreach ($columns as $column) {
            if (in_array($column, $nullableColumns)) {
                $columnInfo = $this->getColumnInfo($table, $column);
                
                if ($columnInfo && !$columnInfo['nullable']) {
                    $columnsToFix[] = $column;
                }
            }
        }
        
        if (!empty($columnsToFix)) {
            $this->warn("    พบคอลัมน์ที่ควรเป็น nullable แต่ยังไม่ได้ตั้งค่า: " . implode(', ', $columnsToFix));
            
            if ($this->confirm('ต้องการปรับคอลัมน์เหล่านี้ให้เป็น nullable หรือไม่?')) {
                Schema::table($table, function ($table) use ($columnsToFix) {
                    foreach ($columnsToFix as $column) {
                        $table->string($column)->nullable()->change();
                        $this->line("    <fg=green>✓ ปรับคอลัมน์ {$column} เป็น nullable</>");
                    }
                });
            }
        } else {
            $this->line("    <fg=green>✓ ไม่พบปัญหาเกี่ยวกับ nullable columns</>");
        }
    }
    
    /**
     * ตรวจสอบ foreign keys
     */
    protected function checkForeignKeys($table)
    {
        $this->line("  <fg=yellow>• ตรวจสอบ foreign keys...</>");
        
        // ตรวจสอบคอลัมน์ที่น่าจะเป็น foreign key
        $potentialForeignKeys = [
            'company_id' => 'companies',
            'user_id' => 'users',
            'customer_id' => 'customers',
            'product_id' => 'products',
            'order_id' => 'orders',
            'department_id' => 'departments',
            'position_id' => 'positions',
            'employee_id' => 'employees',
        ];
        
        $columns = Schema::getColumnListing($table);
        $foreignKeys = $this->getTableForeignKeys($table);
        $columnsToAddFK = [];
        
        foreach ($columns as $column) {
            if (array_key_exists($column, $potentialForeignKeys)) {
                $referencedTable = $potentialForeignKeys[$column];
                
                // ตรวจสอบว่าตารางที่อ้างอิงมีอยู่จริงหรือไม่
                if (!Schema::hasTable($referencedTable)) {
                    continue;
                }
                
                // ตรวจสอบว่ามี foreign key แล้วหรือยัง
                $hasForeignKey = false;
                
                foreach ($foreignKeys as $foreignKey) {
                    if ($foreignKey['column_name'] === $column) {
                        $hasForeignKey = true;
                        break;
                    }
                }
                
                if (!$hasForeignKey) {
                    $columnsToAddFK[$column] = $referencedTable;
                }
            }
        }
        
        if (!empty($columnsToAddFK)) {
            $this->warn("    พบคอลัมน์ที่น่าจะเป็น foreign key แต่ยังไม่ได้กำหนด: " . implode(', ', array_keys($columnsToAddFK)));
            
            foreach ($columnsToAddFK as $column => $referencedTable) {
                if ($this->confirm("ต้องการเพิ่ม foreign key ให้คอลัมน์ {$column} อ้างอิงไปยังตาราง {$referencedTable} หรือไม่?")) {
                    try {
                        Schema::table($table, function ($table) use ($column, $referencedTable) {
                            $table->foreign($column)
                                ->references('id')
                                ->on($referencedTable)
                                ->onDelete('cascade');
                            $this->line("    <fg=green>✓ เพิ่ม foreign key ให้คอลัมน์ {$column} อ้างอิงไปยัง {$referencedTable}.id</>");
                        });
                    } catch (\Exception $e) {
                        $this->error("    เกิดข้อผิดพลาด: " . $e->getMessage());
                    }
                }
            }
        } else {
            $this->line("    <fg=green>✓ ไม่พบปัญหาเกี่ยวกับ foreign keys</>");
        }
    }
    
    /**
     * ดึงข้อมูล indexes ของตาราง
     */
    protected function getTableIndexes($table)
    {
        $connection = config('database.default');
        
        if ($connection === 'sqlite') {
            $query = "PRAGMA index_list('{$table}')";
            $indexes = DB::select($query);
            
            $result = [];
            foreach ($indexes as $index) {
                $indexInfo = DB::select("PRAGMA index_info('{$index->name}')");
                if (!empty($indexInfo)) {
                    $result[] = [
                        'name' => $index->name,
                        'column_name' => $indexInfo[0]->name,
                        'unique' => $index->unique == 1
                    ];
                }
            }
            
            return $result;
        } else {
            // สำหรับ MySQL
            $query = "SHOW INDEX FROM {$table}";
            $indexes = DB::select($query);
            
            $result = [];
            foreach ($indexes as $index) {
                $result[] = [
                    'name' => $index->Key_name,
                    'column_name' => $index->Column_name,
                    'unique' => $index->Non_unique == 0
                ];
            }
            
            return $result;
        }
    }
    
    /**
     * ดึงข้อมูล foreign keys ของตาราง
     */
    protected function getTableForeignKeys($table)
    {
        $connection = config('database.default');
        
        if ($connection === 'sqlite') {
            $query = "PRAGMA foreign_key_list('{$table}')";
            $foreignKeys = DB::select($query);
            
            $result = [];
            foreach ($foreignKeys as $fk) {
                $result[] = [
                    'column_name' => $fk->from,
                    'referenced_table' => $fk->table,
                    'referenced_column' => $fk->to,
                ];
            }
            
            return $result;
        } else {
            // สำหรับ MySQL
            $query = "SELECT 
                        COLUMN_NAME as column_name, 
                        REFERENCED_TABLE_NAME as referenced_table,
                        REFERENCED_COLUMN_NAME as referenced_column
                      FROM 
                        INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                      WHERE 
                        TABLE_SCHEMA = DATABASE() AND
                        TABLE_NAME = '{$table}' AND
                        REFERENCED_TABLE_NAME IS NOT NULL";
            
            return DB::select($query);
        }
    }
    
    /**
     * ดึงข้อมูลของคอลัมน์
     */
    protected function getColumnInfo($table, $column)
    {
        $connection = config('database.default');
        
        if ($connection === 'sqlite') {
            $query = "PRAGMA table_info('{$table}')";
            $columns = DB::select($query);
            
            foreach ($columns as $col) {
                if ($col->name === $column) {
                    return [
                        'name' => $col->name,
                        'type' => $col->type,
                        'nullable' => $col->notnull == 0,
                        'default' => $col->dflt_value
                    ];
                }
            }
            
            return null;
        } else {
            // สำหรับ MySQL
            $query = "SHOW COLUMNS FROM {$table} WHERE Field = '{$column}'";
            $columns = DB::select($query);
            
            if (!empty($columns)) {
                $col = $columns[0];
                return [
                    'name' => $col->Field,
                    'type' => $col->Type,
                    'nullable' => $col->Null === 'YES',
                    'default' => $col->Default
                ];
            }
            
            return null;
        }
    }
}
