<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // บังคับให้ใช้ SQLite กับ path ที่ถูกต้อง
        $sqlitePath = database_path('ceosofts_db_R1.sqlite');

        // บันทึกค่าเพื่อ debug
        Log::info("SQLite path: " . $sqlitePath);
        Log::info("Current DB connection: " . config('database.default'));

        // กำหนดค่า database connection
        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', $sqlitePath);
    }
}
