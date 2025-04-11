<?php

namespace App\Http\Livewire\Department;

use App\Domains\Organization\Actions\CreateDepartmentAction;
use App\Models\Department;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use App\Models\Company;

class DepartmentForm extends Component
{
    use AuthorizesRequests;
    
    public Department $department;
    public bool $isEdit = false;
    
    public $parent_id = null;
    public $parentDepartments = [];
    
    protected $rules = [
        'department.name' => 'required|string|max:255',
        'department.code' => 'nullable|string|max:50',
        'department.description' => 'nullable|string|max:1000',
        'department.is_active' => 'boolean',
        'parent_id' => 'nullable|exists:departments,id',
    ];
    
    public function mount(Department $department = null)
    {
        $this->department = $department ?? new Department();
        $this->isEdit = $this->department->exists;
        
        if ($this->isEdit) {
            $this->parent_id = $this->department->parent_id;
        }
        
        // โหลดแผนกทั้งหมดสำหรับตัวเลือกแผนกหลัก
        // (ไม่รวมแผนกปัจจุบัน + แผนกลูกของมัน)
        $this->loadParentDepartments();
    }
    
    public function loadParentDepartments()
    {
        // ถ้าเป็นการแก้ไข เราต้องไม่แสดงตัวเอง และแผนกลูกของตัวเอง
        $excludedIds = $this->isEdit ? $this->getExcludedDepartmentIds() : [];
        
        $this->parentDepartments = Department::query()
            ->when(count($excludedIds) > 0, function($query) use ($excludedIds) {
                $query->whereNotIn('id', $excludedIds);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);
    }
    
    /**
     * กรณีที่เป็นการแก้ไข ต้องไม่แสดงแผนกตัวเองและแผนกลูกทั้งหมด 
     * เพื่อป้องกัน circular reference
     */
    protected function getExcludedDepartmentIds(): array
    {
        $excludedIds = [$this->department->id]; // เริ่มด้วยตัวเอง
        
        // เพิ่มแผนกลูกทั้งหมดเข้าไป (รวมถึงลูกของลูก)
        $this->addChildDepartmentIds($this->department->id, $excludedIds);
        
        return $excludedIds;
    }
    
    protected function addChildDepartmentIds(int $parentId, array &$ids)
    {
        $childIds = Department::where('parent_id', $parentId)->pluck('id')->toArray();
        
        foreach ($childIds as $childId) {
            $ids[] = $childId;
            $this->addChildDepartmentIds($childId, $ids);
        }
    }
    
    public function save()
    {
        $this->validate();
        
        if ($this->isEdit) {
            $this->authorize('update', $this->department);
        } else {
            $this->authorize('create', Department::class);
        }
        
        $this->department->parent_id = $this->parent_id;
        
        if (!$this->isEdit) {
            // ถ้าเป็นการสร้างใหม่ ใช้ Action
            $action = new CreateDepartmentAction();
            $department = $action->execute([
                'name' => $this->department->name,
                'code' => $this->department->code,
                'description' => $this->department->description,
                'is_active' => $this->department->is_active,
                'parent_id' => $this->parent_id,
            ]);
            
            session()->flash('message', 'เพิ่มแผนกใหม่สำเร็จ');
            return redirect()->route('department.show', $department);
        } else {
            // ถ้าเป็นการแก้ไข ใช้วิธีปกติ
            $this->department->save();
            
            session()->flash('message', 'แก้ไขข้อมูลแผนกสำเร็จ');
            return redirect()->route('department.show', $this->department);
        }
    }
    
    public function render()
    {
        return view('livewire.department.department-form');
    }
}
