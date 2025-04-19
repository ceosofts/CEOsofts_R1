<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // เพิ่มค่า company_id เริ่มต้นใน container
        $this->app->singleton('company_id', function ($app) {
            return session('company_id') ?? config('company.default_id', 1);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // กำหนด company_id เริ่มต้นถ้าไม่มี
        $this->app->booted(function () {
            if (Auth::check() && !session()->has('company_id')) {
                $user = Auth::user();
                $isAdmin = $user->hasRole(['superadmin', 'admin']);
                
                // ดึงบริษัททั้งหมดที่ผู้ใช้มีสิทธิ์เข้าถึง
                $companies = $isAdmin ? Company::all() : $user->companies;
                
                if ($companies->isNotEmpty()) {
                    $companyId = $companies->first()->id;
                    session(['company_id' => $companyId]);
                    config(['company.id' => $companyId]);
                }
            }
        });
    }
}
