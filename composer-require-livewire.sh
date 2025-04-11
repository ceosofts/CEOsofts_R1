#!/bin/bash

echo "กำลังติดตั้ง Livewire ลงใน CEOsofts_R1..."
composer require livewire/livewire

if [ $? -eq 0 ]; then
    echo "ติดตั้ง Livewire สำเร็จ!"
    
    echo "กำลังเพิ่ม service provider..."
    
    if [ -f "app/Providers/AppServiceProvider.php" ]; then
        # สำรองไฟล์ก่อนแก้ไข
        cp app/Providers/AppServiceProvider.php app/Providers/AppServiceProvider.php.bak
        
        # ตรวจสอบว่ามี Livewire::component อยู่แล้วหรือไม่
        if ! grep -q "Livewire::component" app/Providers/AppServiceProvider.php; then
            # เพิ่มการนำเข้า Livewire Facade
            sed -i '' '/^namespace App\\Providers;/a\
\
use Livewire\\Livewire;' app/Providers/AppServiceProvider.php
            
            # เพิ่มการลงทะเบียน Livewire component ใน boot method
            sed -i '' '/public function boot()/a\
    {\
        // ลงทะเบียน Livewire components\
        $this->registerLivewireComponents();\
' app/Providers/AppServiceProvider.php
            
            # แทนที่เมธอด boot เดิม
            sed -i '' 's/public function boot().*{/public function boot()/g' app/Providers/AppServiceProvider.php
            
            # เพิ่มเมธอดใหม่
            cat >> app/Providers/AppServiceProvider.php << 'EOL'
    
    /**
     * Register Livewire components.
     *
     * @return void
     */
    protected function registerLivewireComponents()
    {
        // Organization
        Livewire::component('organization.department-tree', \App\Http\Livewire\Organization\DepartmentTree::class);
        Livewire::component('organization.company-stats', \App\Http\Livewire\Organization\CompanyStats::class);
        Livewire::component('organization.org-chart', \App\Http\Livewire\Organization\OrgChart::class);
        
        // Components
        Livewire::component('components.counter', \App\Http\Livewire\Components\Counter::class);
        Livewire::component('components.search', \App\Http\Livewire\Components\Search::class);
    }
EOL
        fi
    fi
    
    # สร้างไดเรกทอรีสำหรับ Livewire components
    mkdir -p app/Http/Livewire/Components
    mkdir -p app/Http/Livewire/Organization
    mkdir -p resources/views/livewire/components
    mkdir -p resources/views/livewire/organization
    
    echo "กำลังสร้าง Livewire components พื้นฐาน..."
    
    # สร้าง Counter component
    cat > app/Http/Livewire/Components/Counter.php << 'EOL'
<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;

class Counter extends Component
{
    public int $count = 0;
    
    public function increment()
    {
        $this->count++;
    }
    
    public function decrement()
    {
        $this->count--;
    }
    
    public function render()
    {
        return view('livewire.components.counter');
    }
}
EOL
    
    # สร้าง Counter view
    cat > resources/views/livewire/components/counter.blade.php << 'EOL'
<div>
    <div class="flex items-center">
        <button 
            wire:click="decrement" 
            class="px-4 py-2 bg-red-500 text-white rounded-l"
        >-</button>
        
        <span class="px-4 py-2 bg-gray-200">{{ $count }}</span>
        
        <button 
            wire:click="increment" 
            class="px-4 py-2 bg-green-500 text-white rounded-r"
        >+</button>
    </div>
</div>
EOL

    # สร้าง Organization DepartmentTree component
    cat > app/Http/Livewire/Organization/DepartmentTree.php << 'EOL'
<?php

namespace App\Http\Livewire\Organization;

use App\Models\Department;
use Livewire\Component;

class DepartmentTree extends Component
{
    public $departments = [];
    public $companyId;

    public function mount($companyId = null)
    {
        $this->companyId = $companyId;
        $this->loadDepartments();
    }

    public function loadDepartments()
    {
        // โหลดแผนกระดับบนสุด (ไม่มี parent)
        $query = Department::with('children')
            ->whereNull('parent_id');
            
        if ($this->companyId) {
            $query->where('company_id', $this->companyId);
        }
        
        $this->departments = $query->get();
    }

    public function render()
    {
        return view('livewire.organization.department-tree');
    }
}
EOL

    # สร้าง Department Tree view
    cat > resources/views/livewire/organization/department-tree.blade.php << 'EOL'
<div>
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-lg font-medium mb-4">โครงสร้างแผนก</h3>
        
        @if(count($departments) > 0)
            <ul class="space-y-2">
                @foreach($departments as $department)
                    @include('livewire.organization.partials.department-node', ['department' => $department])
                @endforeach
            </ul>
        @else
            <div class="text-gray-500 py-4 text-center">
                ไม่พบข้อมูลแผนก
            </div>
        @endif
    </div>
</div>
EOL

    # สร้างไฟล์ partial สำหรับ Department node
    mkdir -p resources/views/livewire/organization/partials
    cat > resources/views/livewire/organization/partials/department-node.blade.php << 'EOL'
<li>
    <div class="flex items-center p-2 hover:bg-gray-50 rounded-md">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
        </svg>
        <span class="font-medium">{{ $department->name }}</span>
        
        @if($department->code)
            <span class="text-xs text-gray-500 ml-2">({{ $department->code }})</span>
        @endif
    </div>

    @if($department->children && $department->children->count() > 0)
        <ul class="pl-6 mt-1 space-y-2">
            @foreach($department->children as $child)
                @include('livewire.organization.partials.department-node', ['department' => $child])
            @endforeach
        </ul>
    @endif
</li>
EOL

    echo "กำลังสร้าง test page..."
    mkdir -p resources/views/test
    cat > resources/views/test/livewire.blade.php << 'EOL'
<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-semibold mb-6">Livewire Test Components</h1>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-lg font-medium mb-4">Counter Component</h2>
                            @livewire('components.counter')
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h2 class="text-lg font-medium mb-4">Department Tree</h2>
                            @livewire('organization.department-tree')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
EOL

    echo "เพิ่ม Route สำหรับทดสอบ Livewire..."
    
    # เพิ่ม route ใหม่
    cat >> routes/web.php << 'EOL'

// Test routes
Route::get('/test/livewire', function () {
    return view('test.livewire');
})->name('test.livewire');
EOL

    echo "เคลียร์ cache..."
    php artisan optimize:clear
    
    echo ""
    echo "คุณสามารถทดสอบ Livewire ได้ที่ http://localhost:8000/test/livewire"
    echo ""
    echo "ถ้าคุณต้องการสร้าง Livewire component ใหม่ ให้ทำตามนี้:"
    echo "1. สร้างไฟล์ PHP ใน app/Http/Livewire/[Folder]/[ComponentName].php"
    echo "2. สร้าง View ใน resources/views/livewire/[folder]/[component-name].blade.php"
    echo "3. ลงทะเบียน Component ใน AppServiceProvider::registerLivewireComponents()"
    echo "4. ใช้ด้วย @livewire('[folder].[component-name]') ในหน้าเว็บ"
else
    echo "การติดตั้ง Livewire ล้มเหลว โปรดตรวจสอบข้อผิดพลาด"
fi

chmod +x composer-require-livewire.sh
