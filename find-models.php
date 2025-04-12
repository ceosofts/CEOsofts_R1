<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// ค้นหา Model ทั้งหมดในโปรเจค
$models = [];
$directories = [
    app_path(),
    app_path('Models'),
    // เพิ่มเติมไดเร็กทอรีที่อาจเก็บ Models
];

// ค้นหาไฟล์ที่อาจเป็น Models
foreach ($directories as $directory) {
    if (!is_dir($directory)) continue;

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        if ($file->getExtension() !== 'php') continue;

        $content = file_get_contents($file->getPathname());
        // ตรวจสอบว่าไฟล์นั้นมีการ extends Model หรือไม่
        if (strpos($content, 'extends Model') !== false) {
            // ดึง namespace+classname
            if (
                preg_match('/namespace\s+([^;]+)/i', $content, $nsMatches) &&
                preg_match('/class\s+(\w+)/i', $content, $classMatches)
            ) {
                $fullClassName = $nsMatches[1] . '\\' . $classMatches[1];
                $models[] = [
                    'class' => $fullClassName,
                    'file' => $file->getPathname(),
                ];
            }
        }
    }
}

// แสดงรายการ Models ทั้งหมดที่พบ
echo "Models ทั้งหมดที่พบในโปรเจค:\n";
foreach ($models as $model) {
    echo "- " . $model['class'] . " (" . $model['file'] . ")\n";
}

// ค้นหา WorkShift Model โดยเฉพาะ
echo "\nค้นหา WorkShift Model:\n";
$workShiftModels = array_filter($models, function ($model) {
    return strpos($model['class'], 'WorkShift') !== false;
});

if (count($workShiftModels) > 0) {
    foreach ($workShiftModels as $model) {
        echo "พบ WorkShift Model: " . $model['class'] . " (" . $model['file'] . ")\n";
    }
} else {
    echo "ไม่พบ WorkShift Model\n";
}
