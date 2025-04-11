<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Encryption\CustomEncrypter;

class CustomEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // แทนที่ encrypter service ด้วย CustomEncrypter
        $this->app->singleton('encrypter', function ($app) {
            return new CustomEncrypter();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}