<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class OrganizationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // ตรวจสอบว่ามีการ apply global scope หรือ logic ที่ filter company_id หรือไม่
        //
    }
}
