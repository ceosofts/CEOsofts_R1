<?php

/**
 * สคริปต์สร้างไฟล์ config/app.php ในเครื่องแบบ local override
 */

echo "Create Local Overriding Config\n";
echo "============================\n\n";

$configDir = __DIR__ . '/config';
if (!is_dir($configDir)) {
    mkdir($configDir, 0755, true);
    echo "✅ สร้างไดเรกทอรี config\n";
}

// สร้างไฟล์ config/app.local.php สำหรับ override การตั้งค่า
$localConfigPath = $configDir . '/app.local.php';
$localConfig = <<<'EOT'
<?php

return [
    // Override only what's needed
    'cipher' => 'aes-256-cbc',
];
EOT;

file_put_contents($localConfigPath, $localConfig);
echo "✅ สร้างไฟล์ config/app.local.php สำหรับ override การตั้งค่าเฉพาะที่\n";

// แก้ไข bootstrap/app.php เพื่อให้โหลดไฟล์ local config
$bootstrapPath = __DIR__ . '/bootstrap/app.php';
if (file_exists($bootstrapPath)) {
    $bootstrapContent = file_get_contents($bootstrapPath);
    
    // ตรวจสอบว่ามีการโหลด app.local.php อยู่แล้วหรือไม่
    if (strpos($bootstrapContent, 'app.local.php') === false) {
        // หาตำแหน่งที่เหมาะสมสำหรับการเพิ่มโค้ด
        $returnPos = strpos($bootstrapContent, 'return $app;');
        if ($returnPos !== false) {
            // เพิ่มโค้ดก่อนบรรทัด return $app;
            $modifiedContent = substr_replace($bootstrapContent, 
                <<<'EOT'
/*
|--------------------------------------------------------------------------
| Load Local Configuration Overrides
|--------------------------------------------------------------------------
|
| Here we will load any local configuration that should override the main
| configuration files. This is useful for development environments.
|
*/
if (file_exists(__DIR__.'/../config/app.local.php')) {
    $app->configure('app');
    $app->make('config')->set('app', array_merge(
        $app->make('config')->get('app', []),
        require __DIR__.'/../config/app.local.php'
    ));
}

EOT, $returnPos, 0);
            
            file_put_contents($bootstrapPath, $modifiedContent);
            echo "✅ อัปเดตไฟล์ bootstrap/app.php เพื่อโหลด local config\n";
        } else {
            echo "❌ ไม่พบตำแหน่งที่เหมาะสมในไฟล์ bootstrap/app.php\n";
        }
    } else {
        echo "ℹ️ bootstrap/app.php มีการโหลด local config อยู่แล้ว\n";
    }
} else {
    echo "❌ ไม่พบไฟล์ bootstrap/app.php\n";
}

// สร้างไฟล์ config/app.php มาตรฐาน (แบบสมบูรณ์) ถ้ายังไม่มี
$appConfigPath = $configDir . '/app.php';
if (!file_exists($appConfigPath)) {
    $appConfig = <<<'EOT'
<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application. This value is used when the
    | framework needs to place the application's name in a notification or
    | any other location as required by the application or its packages.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | your application so that it is used when running Artisan tasks.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. We have gone
    | ahead and set this to a sensible default for you out of the box.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by the translation service provider. You are free to set this value
    | to any of the locales which will be supported by the application.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | The fallback locale determines the locale to use when the current one
    | is not available. You may change the value to correspond to any of
    | the language folders that are provided through your application.
    |
    */

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Faker Locale
    |--------------------------------------------------------------------------
    |
    | This locale will be used by the Faker PHP library when generating fake
    | data for your database seeds. For example, this will be used to get
    | localized telephone numbers, street address information and more.
    |
    */

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is used by the Illuminate encrypter service and should be set
    | to a random, 32 character string, otherwise these encrypted strings
    | will not be safe. Please do this before deploying an application!
    |
    */

    'key' => env('APP_KEY'),

    'cipher' => 'aes-256-cbc',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        // App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,

    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [],

];
EOT;
    file_put_contents($appConfigPath, $appConfig);
    echo "✅ สร้างไฟล์ config/app.php มาตรฐาน\n";
} else {
    echo "ℹ️ ไฟล์ config/app.php มีอยู่แล้ว\n";
}

// เคลียร์แคช
echo "\nกำลังเคลียร์แคช...\n";
passthru('php artisan config:clear');
passthru('php artisan cache:clear');

echo "\n============================\n";
echo "การสร้าง local config override เสร็จสมบูรณ์\n";
echo "โปรดรีสตาร์ท PHP server:\n";
echo "php artisan serve\n";
