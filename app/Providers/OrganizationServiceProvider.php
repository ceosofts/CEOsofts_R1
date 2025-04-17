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
        // ตรวจสอบว่ามี logic ที่ apply global scope หรือ filter company_id หรือไม่
        //
    }
}
