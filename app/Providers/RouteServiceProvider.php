<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log; // เพิ่มการ import Log facade

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // โหลด Domain Routes
            $this->mapDomainRoutes();
        });
    }

    /**
     * ทำการโหลด routes จากโฟลเดอร์ domains
     */
    protected function mapDomainRoutes(): void
    {
        $domainRoutesPath = base_path('routes/domains');
        $domainRoutes = glob("{$domainRoutesPath}/*.php");

        Log::debug("Attempting to load domain routes from: {$domainRoutesPath}");
        Log::debug("Found domain route files: " . json_encode(array_map('basename', $domainRoutes)));

        if (empty($domainRoutes)) {
            Log::warning("No domain route files found at {$domainRoutesPath}");
        }

        foreach ($domainRoutes as $file) {
            Log::debug("Loading domain route file: {$file}");

            try {
                Route::middleware('web')
                    ->group($file);

                Log::debug("Successfully loaded route file: {$file}");
            } catch (\Throwable $e) {
                Log::error("Error loading route file {$file}: " . $e->getMessage());
            }
        }
    }
}
