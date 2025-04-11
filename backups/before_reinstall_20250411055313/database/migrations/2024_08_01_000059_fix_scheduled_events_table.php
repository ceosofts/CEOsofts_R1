<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ตรวจสอบโครงสร้างตาราง scheduled_events
        $columns = Schema::getColumnListing('scheduled_events');
        $requiredColumns = ['name', 'type', 'description', 'schedule', 'is_enabled', 'last_run', 'next_run'];

        foreach ($requiredColumns as $column) {
            if (!in_array($column, $columns)) {
                Schema::table('scheduled_events', function (Blueprint $table) use ($column) {
                    // เพิ่มคอลัมน์ตามประเภท
                    switch ($column) {
                        case 'name':
                            $table->string('name')->after('company_id');
                            break;
                        case 'type':
                            $table->string('type')->after('name');
                            break;
                        case 'description':
                            $table->string('description')->nullable()->after('type');
                            break;
                        case 'schedule':
                            $table->string('schedule')->after('description');
                            break;
                        case 'is_enabled':
                            $table->boolean('is_enabled')->default(true)->after('timezone');
                            break;
                        case 'last_run':
                            $table->timestamp('last_run')->nullable()->after('is_enabled');
                            break;
                        case 'next_run':
                            $table->timestamp('next_run')->nullable()->after('last_run');
                            break;
                    }
                });
                
                // ใช้ Log แทน command->info
                Log::info("Added missing column '{$column}' to scheduled_events table.");
            }
        }
        
        // เพิ่ม indexes ถ้ายังไม่มี
        $this->addIndexesIfNotExists();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ไม่ต้องทำอะไรใน down เพราะเราไม่ต้องการลบคอลัมน์
    }
    
    /**
     * เพิ่ม indexes ถ้ายังไม่มี
     */
    private function addIndexesIfNotExists()
    {
        // ตรวจสอบว่ามี indexes หรือไม่
        $indexes = [];
        try {
            $indexes = DB::select("SHOW INDEXES FROM scheduled_events");
        } catch (\Exception $e) {
            // ถ้าไม่สามารถดูข้อมูล indexes ได้ ให้สันนิษฐานว่ายังไม่มี
            Log::error("Could not check indexes: " . $e->getMessage());
            return;
        }
        
        // แปลงผลลัพธ์เป็น array ของชื่อ indexes
        $indexNames = [];
        foreach ($indexes as $index) {
            $indexNames[] = $index->Key_name;
        }
        
        // เพิ่ม indexes ถ้ายังไม่มี
        if (!in_array('scheduled_events_company_id_type_index', $indexNames)) {
            Schema::table('scheduled_events', function (Blueprint $table) {
                $table->index(['company_id', 'type']);
            });
        }
        
        if (!in_array('scheduled_events_is_enabled_next_run_index', $indexNames)) {
            Schema::table('scheduled_events', function (Blueprint $table) {
                $table->index(['is_enabled', 'next_run']);
            });
        }
    }
};
