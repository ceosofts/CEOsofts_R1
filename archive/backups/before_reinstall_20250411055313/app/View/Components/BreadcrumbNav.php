<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class BreadcrumbNav extends Component
{
    /**
     * รายการ Breadcrumb
     *
     * @var array
     */
    public array $items;

    /**
     * Create a new component instance.
     *
     * @param array|\Illuminate\Support\Collection $items
     */
    public function __construct($items = [])
    {
        // แปลง Collection เป็น array ถ้าจำเป็น
        if ($items instanceof Collection) {
            $items = $items->toArray();
        }

        $this->items = $this->formatItems($items);
    }

    /**
     * จัดรูปแบบรายการให้เป็นมาตรฐาน
     *
     * @param array $items
     * @return array
     */
    protected function formatItems(array $items): array
    {
        return array_map(function($item) {
            // หากเป็น string จะถือว่ามีแค่ label ไม่มี url
            if (is_string($item)) {
                return ['label' => $item, 'url' => null];
            }
            
            // ตรวจสอบว่ามี key ที่จำเป็นหรือไม่
            return [
                'label' => Arr::get($item, 'label', ''),
                'url' => Arr::get($item, 'url'),
            ];
        }, $items);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('organization.partials.breadcrumb-nav');
    }
}
