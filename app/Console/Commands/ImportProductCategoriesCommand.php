<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\File;

class ImportProductCategoriesCommand extends Command
{
    protected $signature = 'import:product-categories {path?}';
    protected $description = 'นำเข้าข้อมูลหมวดหมู่สินค้าจากไฟล์ JSON';

    public function handle()
    {
        $path = $this->argument('path') ?: '/Users/iwasbornforthis/Downloads/product_categories_export_20250416_030022.json';
        
        if (!File::exists($path)) {
            $this->error("ไม่พบไฟล์: $path");
            return 1;
        }
        
        $categories = json_decode(File::get($path), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("ไม่สามารถอ่านไฟล์ JSON: " . json_last_error_msg());
            return 1;
        }
        
        $this->info("พบข้อมูล " . count($categories) . " รายการ");
        
        $importCount = 0;
        $skipCount = 0;
        
        $this->withProgressBar($categories, function($category) use (&$importCount, &$skipCount) {
            // ตรวจสอบว่าหมวดหมู่นี้มีอยู่แล้วหรือไม่
            $exists = ProductCategory::where('id', $category['id'])
                ->orWhere(function($query) use ($category) {
                    if (!empty($category['code'])) {
                        return $query->where('company_id', $category['company_id'])
                                     ->where('code', $category['code']);
                    }
                    return $query->where('company_id', $category['company_id'])
                                  ->where('name', $category['name']);
                })
                ->exists();
                
            if ($exists) {
                $skipCount++;
                return;
            }
            
            try {
                ProductCategory::create([
                    'id' => $category['id'],
                    'company_id' => $category['company_id'],
                    'name' => $category['name'],
                    'code' => $category['code'],
                    'description' => $category['description'],
                    'is_active' => $category['is_active'],
                    'parent_id' => $category['parent_id'],
                    'metadata' => $category['metadata'],
                    'slug' => $category['slug'] ?: \Str::slug($category['name']),
                    'level' => $category['level'] ?: 0,
                    'path' => $category['path'] ?: '',
                    'created_at' => $category['created_at'],
                    'updated_at' => $category['updated_at'],
                    'deleted_at' => $category['deleted_at'],
                ]);
                
                $importCount++;
            } catch (\Exception $e) {
                $this->error("\nเกิดข้อผิดพลาด: " . $e->getMessage());
            }
        });
        
        $this->newLine(2);
        $this->info("นำเข้าสำเร็จ: $importCount รายการ");
        $this->info("ข้ามไป: $skipCount รายการ");
        
        return 0;
    }
}
