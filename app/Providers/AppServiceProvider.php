<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

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
        Schema::defaultStringLength(191);

        // ถ้าอยู่ในโหมด local ให้แสดง error ทั้งหมด
        if (app()->environment('local')) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        }
        
        // บันทึก SQL query ที่ผิดพลาดเพื่อช่วยในการ debug
        DB::listen(function ($query) {
            if ($query->time > 1000) { // บันทึกเฉพาะ query ที่ใช้เวลามากกว่า 1 วินาที
                Log::channel('daily')->info('Slow Query', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms'
                ]);
            }
        });
        
        // จับ query exception และบันทึกลง log
        DB::getEventDispatcher()->listen('illuminate.query.failed', function ($sql, $bindings, $error) {
            Log::error('SQL Error', [
                'sql' => $sql,
                'bindings' => $bindings,
                'error' => $error
            ]);
        });
    }
}
