<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixTranslationsTable extends Command
{
    protected $signature = 'db:fix-translations';

    protected $description = 'Fix issues with translations table and related migrations';

    public function handle()
    {
        $this->info('Starting translations table fix...');

        // 1. ตรวจสอบว่ามีตาราง migrations หรือไม่
        if (!Schema::hasTable('migrations')) {
            $this->error('No migrations table found. Please run migrations first.');
            return 1;
        }

        // 2. แสดงรายการ migrations ที่เกี่ยวข้องกับ translations
        $translationMigrations = DB::table('migrations')
            ->where('migration', 'like', '%translation%')
            ->orderBy('batch')
            ->get();

        $this->info('Found ' . $translationMigrations->count() . ' translation-related migrations.');
        
        $this->table(['Migration', 'Batch'], $translationMigrations->map(function($item) {
            return [
                'migration' => $item->migration,
                'batch' => $item->batch,
            ];
        }));

        // 3. ตรวจสอบว่ามีตาราง translations หรือไม่
        if (!Schema::hasTable('translations')) {
            $this->info('Translations table does not exist. Will create it...');
            $this->call('migrate', ['--path' => 'database/migrations/2024_08_01_000075_setup_translations_table.php']);
            return;
        }

        // 4. ตรวจสอบคอลัมน์ในตาราง translations
        $columns = Schema::getColumnListing('translations');
        $this->info('Translations table columns: ' . implode(', ', $columns));

        // 5. ตรวจสอบว่ามีคอลัมน์ที่จำเป็นหรือไม่
        $requiredColumns = ['company_id', 'locale', 'group', 'key', 'value', 'translatable_type', 'translatable_id'];
        $missingColumns = array_diff($requiredColumns, $columns);

        if (count($missingColumns) > 0) {
            $this->warn('Missing required columns: ' . implode(', ', $missingColumns));
            
            if ($this->confirm('Do you want to recreate the translations table?')) {
                // สำรองข้อมูล
                $this->info('Backing up existing translations data...');
                $this->backupTranslationsData();
                
                // ลบตารางเดิม
                Schema::dropIfExists('translations');
                
                // สร้างตารางใหม่
                $this->info('Creating new translations table...');
                $this->call('migrate', ['--path' => 'database/migrations/2024_08_01_000075_setup_translations_table.php']);
                
                // คืนข้อมูลเดิม (ถ้ามี)
                $this->info('Restoring translations data...');
                $this->restoreTranslationsData();
            }
        } else {
            $this->info('All required columns exist in translations table.');
        }

        // 6. แก้ไขรายการใน migrations table เพื่อป้องกันการรัน migration ซ้ำ
        $this->fixMigrationsTable();

        $this->info('Translations table fix completed!');
    }

    /**
     * สำรองข้อมูลจากตาราง translations
     */
    protected function backupTranslationsData()
    {
        if (Schema::hasTable('translations')) {
            // สร้างตาราง backup ถ้ายังไม่มี
            Schema::dropIfExists('translations_backup');
            Schema::create('translations_backup', function ($table) {
                $table->id();
                $table->text('data');
                $table->timestamps();
            });
            
            // ดึงข้อมูลและเก็บเป็น JSON
            $translations = DB::table('translations')->get();
            foreach ($translations as $translation) {
                DB::table('translations_backup')->insert([
                    'data' => json_encode((array) $translation),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $this->info('Backed up ' . count($translations) . ' translation records.');
        }
    }

    /**
     * คืนข้อมูลจาก backup กลับไปยังตาราง translations
     */
    protected function restoreTranslationsData()
    {
        if (!Schema::hasTable('translations_backup')) {
            $this->warn('No backup data found.');
            return;
        }
        
        $backupData = DB::table('translations_backup')->get();
        $restored = 0;
        
        foreach ($backupData as $record) {
            try {
                $data = json_decode($record->data, true);
                
                // กรองเฉพาะ fields ที่มีอยู่ในตาราง translations
                $insertData = [];
                $columns = Schema::getColumnListing('translations');
                
                foreach ($data as $key => $value) {
                    if (in_array($key, $columns)) {
                        $insertData[$key] = $value;
                    }
                }
                
                // เพิ่มข้อมูล timestamps ถ้าจำเป็น
                if (!isset($insertData['created_at']) && in_array('created_at', $columns)) {
                    $insertData['created_at'] = now();
                }
                
                if (!isset($insertData['updated_at']) && in_array('updated_at', $columns)) {
                    $insertData['updated_at'] = now();
                }
                
                // เพิ่มข้อมูลกลับเข้า translations
                DB::table('translations')->insert($insertData);
                $restored++;
            } catch (\Exception $e) {
                $this->error('Error restoring record: ' . $e->getMessage());
            }
        }
        
        $this->info("Restored $restored translation records.");
    }

    /**
     * แก้ไขรายการใน migrations table
     */
    protected function fixMigrationsTable()
    {
        $migrationsToUpdate = [
            '2024_08_01_000066_create_translations_table',
            '2024_08_01_000067_update_translations_table',
            '2024_08_01_000068_add_key_column_to_translations_table',
            '2024_08_01_000069_add_translatable_fields_to_translations_table',
            '2024_08_01_000070_update_translatable_id_in_translations_table',
            '2024_08_01_000071_add_field_and_deleted_at_to_translations_table',
            '2024_08_01_000072_fix_translations_unique_constraints',
        ];

        $latestBatch = DB::table('migrations')->max('batch') ?: 1;
        
        foreach ($migrationsToUpdate as $migration) {
            DB::table('migrations')->updateOrInsert(
                ['migration' => $migration],
                ['batch' => $latestBatch]
            );
            
            $this->info("Updated migration status for: $migration");
        }
    }
}
