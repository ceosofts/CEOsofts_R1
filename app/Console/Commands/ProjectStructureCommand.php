<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class ProjectStructureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:structure 
                            {path? : เส้นทางที่ต้องการแสดงโครงสร้าง (ค่าเริ่มต้นคือ root ของโปรเจกต์)} 
                            {--depth=3 : ความลึกสูงสุดในการแสดงโฟลเดอร์}
                            {--o|output= : ไฟล์ที่ต้องการบันทึกผลลัพธ์}
                            {--e|exclude=* : โฟลเดอร์ที่ต้องการข้าม เช่น vendor,node_modules}
                            {--f|format=text : รูปแบบผลลัพธ์ (text, markdown, json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'แสดงโครงสร้างโปรเจกต์ Laravel';

    /**
     * โฟลเดอร์ที่ควรข้ามโดยค่าเริ่มต้น
     */
    protected $defaultExclude = [
        'vendor', 'node_modules', '.git', 'storage/logs',
        'storage/framework', 'bootstrap/cache', '.idea', '.vscode'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $basePath = $this->argument('path') ?? base_path();
        $depth = $this->option('depth');
        $excludeFolders = $this->option('exclude') ?: $this->defaultExclude;
        $format = $this->option('format');
        $output = $this->option('output');

        $this->info("กำลังสร้างโครงสร้างโปรเจกต์จาก: {$basePath}");
        $this->info("ความลึกสูงสุด: {$depth} ระดับ");
        $this->info("ข้ามโฟลเดอร์: " . implode(', ', $excludeFolders));

        // สร้างโครงสร้างไฟล์
        $structure = $this->generateStructure($basePath, $depth, $excludeFolders);

        // แสดงผลลัพธ์ตามรูปแบบที่เลือก
        $this->displayStructure($structure, $format);

        // บันทึกผลลัพธ์ถ้าระบุไฟล์
        if ($output) {
            $this->saveStructure($structure, $format, $output);
        }

        return Command::SUCCESS;
    }

    /**
     * สร้างโครงสร้างไฟล์และโฟลเดอร์
     */
    protected function generateStructure($basePath, $maxDepth, $excludeFolders, $currentDepth = 0)
    {
        if ($currentDepth > $maxDepth) {
            return [];
        }

        $result = [];
        $relativePath = $this->getRelativePath($basePath);

        // อ่านไฟล์และโฟลเดอร์
        $items = File::files($basePath);
        $directories = File::directories($basePath);

        // เพิ่มไฟล์ในโฟลเดอร์ปัจจุบัน
        foreach ($items as $item) {
            $result[] = [
                'type' => 'file',
                'name' => $item->getFilename(),
                'path' => $this->getRelativePath($item->getPathname()),
                'size' => $this->formatSize($item->getSize()),
            ];
        }

        // สำรวจโฟลเดอร์ย่อย
        foreach ($directories as $directory) {
            $dirName = basename($directory);
            
            // ข้ามโฟลเดอร์ที่อยู่ในรายการยกเว้น
            if (in_array($dirName, $excludeFolders)) {
                continue;
            }

            $subItems = $this->generateStructure($directory, $maxDepth, $excludeFolders, $currentDepth + 1);
            
            $result[] = [
                'type' => 'directory',
                'name' => $dirName,
                'path' => $this->getRelativePath($directory),
                'children' => $subItems,
            ];
        }

        return $result;
    }

    /**
     * แสดงผลโครงสร้างตามรูปแบบที่กำหนด
     */
    protected function displayStructure($structure, $format)
    {
        switch ($format) {
            case 'json':
                $this->line(json_encode($structure, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                break;
                
            case 'markdown':
                $this->line($this->formatAsMarkdown($structure));
                break;
                
            case 'text':
            default:
                $this->line($this->formatAsText($structure));
                break;
        }
    }

    /**
     * บันทึกผลลัพธ์ลงไฟล์
     */
    protected function saveStructure($structure, $format, $outputPath)
    {
        $content = '';
        
        switch ($format) {
            case 'json':
                $content = json_encode($structure, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                break;
                
            case 'markdown':
                $content = $this->formatAsMarkdown($structure);
                break;
                
            case 'text':
            default:
                $content = $this->formatAsText($structure);
                break;
        }
        
        File::put($outputPath, $content);
        $this->info("บันทึกโครงสร้างโปรเจกต์ไปยัง: {$outputPath}");
    }

    /**
     * แปลงขนาดไฟล์เป็นรูปแบบที่อ่านง่าย
     */
    protected function formatSize($sizeInBytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $size = $sizeInBytes;
        $unitIndex = 0;
        
        while ($size > 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }
        
        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * แสดงเส้นทางเทียบกับโปรเจกต์
     */
    protected function getRelativePath($path)
    {
        return str_replace(base_path() . '/', '', $path);
    }

    /**
     * แสดงผลในรูปแบบข้อความ
     */
    protected function formatAsText($structure, $prefix = '')
    {
        $output = '';
        
        foreach ($structure as $index => $item) {
            $isLast = $index === array_key_last($structure);
            $marker = $isLast ? '└── ' : '├── ';
            $newPrefix = $isLast ? $prefix . '    ' : $prefix . '│   ';
            
            if ($item['type'] === 'file') {
                $output .= $prefix . $marker . $item['name'] . ' (' . $item['size'] . ")\n";
            } else {
                $output .= $prefix . $marker . $item['name'] . "/\n";
                
                if (!empty($item['children'])) {
                    $output .= $this->formatAsText($item['children'], $newPrefix);
                }
            }
        }
        
        return $output;
    }

    /**
     * แสดงผลในรูปแบบ Markdown
     */
    protected function formatAsMarkdown($structure, $level = 1)
    {
        $output = '';
        
        foreach ($structure as $item) {
            $indent = str_repeat('#', $level) . ' ';
            
            if ($item['type'] === 'file') {
                $output .= $indent . '📄 ' . $item['name'] . ' (' . $item['size'] . ")\n";
            } else {
                $output .= $indent . '📁 ' . $item['name'] . "/\n";
                
                if (!empty($item['children'])) {
                    $output .= $this->formatAsMarkdown($item['children'], $level + 1);
                }
            }
        }
        
        return $output;
    }
}
