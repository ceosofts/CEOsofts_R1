<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Encryption\SimpleEncrypter;

class SimpleEncryptionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // ลงทะเบียน simple-encrypter
        $this->app->singleton('simple-encrypter', function ($app) {
            return new SimpleEncrypter();
        });
        
        // ลงทะเบียนแทนที่ encrypter เดิมเพื่อความเข้ากันได้
        if (!$this->app->bound('encrypter')) {
            $this->app->singleton('encrypter', function ($app) {
                return $app->make('simple-encrypter');
            });
        }
    }
    
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}