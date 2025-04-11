<?php
require __DIR__.'/vendor/autoload.php';

$app = new Illuminate\Foundation\Application(__DIR__);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

// กำหนดค่า cipher และ key โดยตรง
$app['config']->set('app.cipher', 'aes-256-cbc');
$app['config']->set('app.key', getenv('APP_KEY'));

return $app;
