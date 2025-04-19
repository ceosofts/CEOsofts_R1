<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        // ตรวจสอบว่า Telescope ถูกเปิดใช้งานหรือไม่
        if (config('telescope.enabled', false) === false) {
            return 'sqlite'; // กำหนดให้ใช้ connection ปัจจุบัน
        }
        return config('telescope.storage.database.connection');
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ตรวจสอบว่า Telescope ต้องการถูกเปิดใช้งานหรือไม่
        if (config('telescope.enabled', false) === false) {
            echo "Skipping Telescope migrations (telescope.enabled=false).\n";
            return;
        }
        
        // ถ้าใช้ SQLite ก็ข้ามไป
        if (config('database.default') === 'sqlite') {
            echo "Skipping Telescope migrations for SQLite database.\n";
            return;
        }

        // โค้ดเดิมที่สร้างตาราง (จะไม่ทำงานถ้าเข้าเงื่อนไขข้างบน)
        $schema = Schema::connection($this->getConnection());

        $schema->create('telescope_entries', function (Blueprint $table) {
            $table->bigIncrements('sequence');
            $table->uuid('uuid');
            $table->uuid('batch_id');
            $table->string('family_hash')->nullable();
            $table->boolean('should_display_on_index')->default(true);
            $table->string('type', 20);
            $table->longText('content');
            $table->dateTime('created_at')->nullable();

            $table->unique('uuid');
            $table->index('batch_id');
            $table->index('family_hash');
            $table->index('created_at');
            $table->index(['type', 'should_display_on_index']);
        });

        $schema->create('telescope_entries_tags', function (Blueprint $table) {
            $table->uuid('entry_uuid');
            $table->string('tag');

            $table->primary(['entry_uuid', 'tag']);
            $table->index('tag');

            $table->foreign('entry_uuid')
                ->references('uuid')
                ->on('telescope_entries')
                ->onDelete('cascade');
        });

        $schema->create('telescope_monitoring', function (Blueprint $table) {
            $table->string('tag')->primary();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ตรวจสอบว่า Telescope ถูกเปิดใช้งานหรือไม่
        if (config('telescope.enabled', false) === false) {
            return;
        }
        
        // ถ้าใช้ SQLite ก็ข้ามไป
        if (config('database.default') === 'sqlite') {
            return;
        }

        $schema = Schema::connection($this->getConnection());

        $schema->dropIfExists('telescope_entries_tags');
        $schema->dropIfExists('telescope_entries');
        $schema->dropIfExists('telescope_monitoring');
    }
};
