<?php

/**
 * Script para arreglar archivos principales de Laravel 12
 * Corrige bootstrap/app.php, artisan y public/index.php para asegurar compatibilidad
 */

echo "===== Fixing Laravel 12 Core Files =====\n\n";

// 1. Crear copia de seguridad de archivos
$backupDir = __DIR__ . '/backups/laravel12_fix_' . date('YmdHis');
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "✅ Created backup directory: $backupDir\n";
}

$filesToBackup = [
    'bootstrap/app.php',
    'artisan',
    'public/index.php'
];

foreach ($filesToBackup as $file) {
    $fullPath = __DIR__ . '/' . $file;
    if (file_exists($fullPath)) {
        copy($fullPath, $backupDir . '/' . basename($file));
        echo "✅ Backed up $file\n";
    }
}

// 2. Fix bootstrap/app.php
echo "\n1. Fixing bootstrap/app.php...\n";
$bootstrapDir = __DIR__ . '/bootstrap';
if (!is_dir($bootstrapDir)) {
    mkdir($bootstrapDir, 0755, true);
}

$appContent = <<<'EOT'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    });
EOT;

file_put_contents(__DIR__ . '/bootstrap/app.php', $appContent);
echo "✅ Fixed bootstrap/app.php\n";

// 3. Fix artisan file
echo "\n2. Fixing artisan file...\n";
$artisanContent = <<<'EOT'
#!/usr/bin/env php
<?php

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| When we run the console application, the current CLI command will be
| executed in this console and the response sent back to a terminal
| or another output device for the developers. Here goes nothing!
|
*/

$status = (require_once __DIR__.'/bootstrap/app.php')
    ->handleCommand(
        capture: fn ($app) => $app->make(Illuminate\Contracts\Console\Kernel::class)
    );

exit($status);
EOT;

file_put_contents(__DIR__ . '/artisan', $artisanContent);
chmod(__DIR__ . '/artisan', 0755); // Make it executable
echo "✅ Fixed artisan file\n";

// 4. Fix public/index.php
echo "\n3. Fixing public/index.php...\n";
$publicDir = __DIR__ . '/public';
if (!is_dir($publicDir)) {
    mkdir($publicDir, 0755, true);
}

$indexContent = <<<'EOT'
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

(require_once __DIR__.'/../bootstrap/app.php')
    ->handleRequest(Request::capture());
EOT;

file_put_contents(__DIR__ . '/public/index.php', $indexContent);
echo "✅ Fixed public/index.php\n";

// 5. Create routes directory and files if they don't exist
echo "\n4. Setting up routes...\n";
$routesDir = __DIR__ . '/routes';
if (!is_dir($routesDir)) {
    mkdir($routesDir, 0755, true);
}

// Create web.php
if (!file_exists($routesDir . '/web.php')) {
    $webContent = <<<'EOT'
<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return 'Laravel 12 is working! 🎉 APP_KEY: ' . config('app.key');
});
EOT;
    file_put_contents($routesDir . '/web.php', $webContent);
    echo "✅ Created routes/web.php\n";
}

// Create console.php
if (!file_exists($routesDir . '/console.php')) {
    $consoleContent = <<<'EOT'
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
EOT;
    file_put_contents($routesDir . '/console.php', $consoleContent);
    echo "✅ Created routes/console.php\n";
}

// 6. Create storage structure if needed
echo "\n5. Setting up storage directory...\n";
$storageDirs = [
    'app',
    'framework/cache',
    'framework/sessions',
    'framework/views',
    'logs',
];

foreach ($storageDirs as $dir) {
    $path = __DIR__ . '/storage/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "✅ Created $path\n";
    }
}

echo "\n===== Laravel 12 Core Files Fixed =====\n";
echo "You can now run:\n";
echo "php artisan serve --port=8020\n\n";
echo "If you want to check if basic encryption is working:\n";
echo "php artisan tinker\n";
echo "Then in tinker, try: encrypt('test');\n";
