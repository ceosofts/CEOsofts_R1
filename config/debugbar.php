<?php

return [
    /*
     |--------------------------------------------------------------------------
     | Debugbar Settings
     |--------------------------------------------------------------------------
     */

    // เปิดใช้งานเฉพาะโหมด local (ปิดใน production)
    'enabled' => env('DEBUGBAR_ENABLED', env('APP_ENV') === 'local'),
    
    // บันทึกข้อมูลใน storage น้อยลง
    'storage' => [
        'enabled'    => env('DEBUGBAR_STORE_ENABLED', false),
        'driver'     => 'file', // redis, file, pdo
        'path'       => storage_path('debugbar'), // สำหรับ file driver
        'connection' => null,   // สำหรับ redis/pdo
        // เก็บไฟล์ไม่เกิน 1 วัน
        'maximum_lifetime' => 60 * 60 * 24, // 1 วัน
    ],

    // ลดการเก็บข้อมูล
    'collectors' => [
        'phpinfo'         => false,  // Requires zend_extension
        'messages'        => true,
        'time'            => true,
        'memory'          => true,
        'exceptions'      => true,
        'log'             => true,
        'db'              => true,
        'views'           => true,
        'route'           => true,
        'auth'            => false,
        'gate'            => false,
        'session'         => true,
        'symfony_request' => true,
        'mail'            => true,
        'laravel'         => false,
        'events'          => false,
        'default_request' => false,
        'logs'            => false,
        'files'           => false,
        'config'          => false,
        'cache'           => false,
        'models'          => false,
        'livewire'        => true,
    ],
    
    // จำกัดขนาดข้อมูลที่เก็บ
    'options' => [
        'auth' => [
            'show_name' => true,
        ],
        'db' => [
            'with_params'       => true,
            'backtrace'         => false,
            'backtrace_exclude_paths' => ['vendor'],
            'timeline'          => false,
            'duration_background' => true,
            'explain' => [
                'enabled' => false,
                'types' => ['SELECT'],
            ],
            'hints'             => false,
            'show_copy'         => false,
        ],
        'mail' => [
            'full_log' => false
        ],
        'views' => [
            'timeline' => false,
            'data' => false,
        ],
        'route' => [
            'label' => true
        ],
        'logs' => [
            'file' => null
        ],
    ],

    'inject' => true,
];
