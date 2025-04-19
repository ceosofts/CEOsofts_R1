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
        try {
            // ตรวจสอบว่าเป็น SQLite หรือไม่
            $connection = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);
            $isSQLite = ($connection === 'sqlite');
            
            // ตรวจสอบว่าตาราง translations มีอยู่แล้วหรือไม่
            $tableExists = false;
            if ($isSQLite) {
                $tableExists = !empty(DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name='translations'"));
            } else {
                $tableExists = Schema::hasTable('translations');
            }
            
            // ถ้าตารางไม่มี ให้สร้างใหม่
            if (!$tableExists) {
                if ($isSQLite) {
                    // สำหรับ SQLite: ใช้ "translation_group" แทน "group" เพื่อหลีกเลี่ยง keyword
                    DB::statement('CREATE TABLE translations (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        company_id INTEGER NOT NULL,
                        locale VARCHAR(255) NOT NULL,
                        translation_group VARCHAR(255) NOT NULL,
                        key VARCHAR(255) NOT NULL,
                        field VARCHAR(255) NOT NULL DEFAULT "general",
                        value TEXT NULL,
                        translatable_type VARCHAR(255) NOT NULL DEFAULT "general",
                        translatable_id INTEGER NOT NULL DEFAULT 0,
                        metadata JSON NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        deleted_at TIMESTAMP NULL,
                        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
                    )');
                    
                    // สร้าง indices
                    DB::statement('CREATE INDEX idx_translations_company_id ON translations(company_id)');
                    DB::statement('CREATE INDEX idx_translations_translatable ON translations(translatable_type, translatable_id)');
                    DB::statement('CREATE INDEX idx_translations_locale ON translations(locale)');
                    DB::statement('CREATE INDEX idx_translations_field ON translations(field)');
                    DB::statement('CREATE INDEX idx_translations_company_locale ON translations(company_id, locale)');
                    DB::statement('CREATE INDEX idx_translations_company_group ON translations(company_id, translation_group)');
                    DB::statement('CREATE UNIQUE INDEX translations_company_locale_group_key_unique ON translations(company_id, locale, translation_group, key)');
                    
                    Log::info('สร้างตาราง translations ด้วย raw SQL สำหรับ SQLite เรียบร้อยแล้ว');
                    
                    // กำหนด PRAGMA สำหรับ SQLite เพื่อให้ TranslationSeeder รู้ว่าควรใช้ translation_group
                    DB::statement('PRAGMA user_version = 1');
                } else {
                    // สร้างตารางด้วย Schema Builder ปกติสำหรับฐานข้อมูลอื่น
                    Schema::create('translations', function (Blueprint $table) {
                        $table->id();
                        $table->foreignId('company_id')->constrained()->onDelete('cascade');
                        $table->string('locale');
                        $table->string('group'); // MySQL รองรับคำว่า group
                        $table->string('key');
                        $table->string('field')->default('general');
                        $table->text('value')->nullable();
                        $table->string('translatable_type')->default('general');
                        $table->unsignedBigInteger('translatable_id')->default(0);
                        $table->json('metadata')->nullable();
                        $table->timestamps();
                        $table->softDeletes();

                        // สร้าง indices
                        $table->index('company_id');
                        $table->index(['translatable_type', 'translatable_id']);
                        $table->index('locale');
                        $table->index('field');

                        // สร้าง unique constraint
                        $table->unique(['company_id', 'locale', 'group', 'key'], 'translations_company_locale_group_key_unique');

                        // เพิ่ม index เพิ่มเติม
                        $table->index(['company_id', 'locale'], 'translations_company_locale_index');
                        $table->index(['company_id', 'group'], 'translations_company_group_index');
                    });
                    
                    Log::info('สร้างตาราง translations เรียบร้อยแล้ว');
                }
            } else if ($isSQLite) {
                // กรณีเป็น SQLite และตารางมีอยู่แล้ว ตรวจสอบและปรับปรุงโครงสร้าง
                
                // ตรวจสอบโครงสร้างของตาราง
                $columns = [];
                $columnInfo = DB::select("PRAGMA table_info(translations)");
                
                foreach ($columnInfo as $column) {
                    $columns[] = $column->name;
                }
                
                // เช็คว่ามีการใช้ translation_group หรือ group
                $groupColumnName = in_array('translation_group', $columns) ? 'translation_group' : 'group';
                
                // ถ้าต้องการอัพเดทโครงสร้าง
                $needsUpdate = !in_array('company_id', $columns) || 
                              !in_array($groupColumnName, $columns) || 
                              !in_array('metadata', $columns) || 
                              !in_array('field', $columns) || 
                              !in_array('translatable_type', $columns) ||
                              !in_array('translatable_id', $columns) || 
                              !in_array('deleted_at', $columns);
                
                if ($needsUpdate) {
                    // สำรองข้อมูล
                    $data = [];
                    if (!empty($columns)) {
                        try {
                            $data = DB::select("SELECT * FROM translations");
                        } catch (\Exception $e) {
                            Log::warning('ไม่สามารถดึงข้อมูลจากตาราง translations: ' . $e->getMessage());
                        }
                    }
                    
                    // ลบตารางเดิม
                    DB::statement('DROP TABLE IF EXISTS translations');
                    
                    // สร้างตารางใหม่
                    DB::statement('CREATE TABLE translations (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        company_id INTEGER NOT NULL,
                        locale VARCHAR(255) NOT NULL,
                        translation_group VARCHAR(255) NOT NULL,
                        key VARCHAR(255) NOT NULL,
                        field VARCHAR(255) NOT NULL DEFAULT "general",
                        value TEXT NULL,
                        translatable_type VARCHAR(255) NOT NULL DEFAULT "general",
                        translatable_id INTEGER NOT NULL DEFAULT 0,
                        metadata JSON NULL,
                        created_at TIMESTAMP NULL,
                        updated_at TIMESTAMP NULL,
                        deleted_at TIMESTAMP NULL,
                        FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
                    )');
                    
                    // สร้าง indices
                    DB::statement('CREATE INDEX idx_translations_company_id ON translations(company_id)');
                    DB::statement('CREATE INDEX idx_translations_translatable ON translations(translatable_type, translatable_id)');
                    DB::statement('CREATE INDEX idx_translations_locale ON translations(locale)');
                    DB::statement('CREATE INDEX idx_translations_field ON translations(field)');
                    DB::statement('CREATE INDEX idx_translations_company_locale ON translations(company_id, locale)');
                    DB::statement('CREATE INDEX idx_translations_company_group ON translations(company_id, translation_group)');
                    DB::statement('CREATE UNIQUE INDEX translations_company_locale_group_key_unique ON translations(company_id, locale, translation_group, key)');
                    
                    Log::info('อัพเดทโครงสร้างตาราง translations สำหรับ SQLite เรียบร้อยแล้ว');
                }
            } else {
                // กรณีไม่ใช่ SQLite และตารางมีอยู่แล้ว เพิ่มคอลัมน์ที่อาจหายไป
                if (!Schema::hasColumn('translations', 'field')) {
                    Schema::table('translations', function (Blueprint $table) {
                        $table->string('field')->default('general')->after('key');
                        $table->index('field');
                    });
                }
                
                if (!Schema::hasColumn('translations', 'translatable_type')) {
                    Schema::table('translations', function (Blueprint $table) {
                        $table->string('translatable_type')->default('general')->after('value');
                    });
                }
                
                if (!Schema::hasColumn('translations', 'translatable_id')) {
                    Schema::table('translations', function (Blueprint $table) {
                        $table->unsignedBigInteger('translatable_id')->default(0)->after('translatable_type');
                        $table->index(['translatable_type', 'translatable_id']);
                    });
                }
                
                if (!Schema::hasColumn('translations', 'metadata')) {
                    Schema::table('translations', function (Blueprint $table) {
                        $table->json('metadata')->nullable()->after('translatable_id');
                    });
                }
                
                if (!Schema::hasColumn('translations', 'deleted_at')) {
                    Schema::table('translations', function (Blueprint $table) {
                        $table->softDeletes();
                    });
                }
                
                // เพิ่ม index ที่อาจหายไป
                Schema::table('translations', function (Blueprint $table) {
                    if (!Schema::hasIndex('translations', 'translations_company_locale_index')) {
                        $table->index(['company_id', 'locale'], 'translations_company_locale_index');
                    }
                    
                    if (!Schema::hasIndex('translations', 'translations_company_group_index')) {
                        $table->index(['company_id', 'group'], 'translations_company_group_index');
                    }
                });
                
                Log::info('อัพเดทโครงสร้างตาราง translations เรียบร้อยแล้ว');
            }
            
            // แก้ไขส่วนนี้: ข้ามการ clean up ขณะที่สร้างตาราง เพราะอาจยังไม่มีตาราง
            if (Schema::hasTable('translations')) {
                try {
                    // ใช้ backtick แบบเงื่อนไขตามชนิดของฐานข้อมูล
                    $groupCol = $isSQLite ? 'translation_group' : '`group`';
                    $sql = "DELETE FROM translations WHERE id NOT IN (SELECT MIN(id) FROM translations GROUP BY company_id, locale, {$groupCol}, `key`)";
                    DB::statement($sql);
                    
                    Log::info('ทำความสะอาดข้อมูลซ้ำซ้อนในตาราง translations เรียบร้อยแล้ว');
                } catch (\Exception $e) {
                    Log::warning('ไม่สามารถทำความสะอาดตาราง translations: ' . $e->getMessage());
                }
            }
            
        } catch (\Exception $e) {
            Log::error('เกิดข้อผิดพลาดในการปรับปรุงตาราง translations: ' . $e->getMessage());
            echo "เกิดข้อผิดพลาดในการปรับปรุงตาราง translations: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
