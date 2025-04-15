<li class="ml-{{ $level * 6 }}">
    <div class="flex items-center group p-2 hover:bg-gray-50 rounded-lg border-l-4 {{ $level == 0 ? 'border-blue-500' : ($level == 1 ? 'border-green-500' : 'border-amber-500') }}">
        <div class="flex-1">
            <div class="font-medium text-gray-900">{{ $department->name }}</div>
            @if($department->manager)
                <div class="text-sm text-gray-500 mt-1">
                    <span class="font-medium">หัวหน้าแผนก:</span> {{ $department->manager->full_name }}
                </div>
            @endif
        </div>
        <div class="text-right text-sm text-gray-500">
            <span>{{ $department->positions->count() }} ตำแหน่ง</span>
            <span class="mx-2">|</span>
            <span>{{ $department->employees->count() }} พนักงาน</span>
        </div>
    </div>
    
    <!-- แสดงตำแหน่งในแผนก -->
    @if($department->positions->isNotEmpty())
        <div class="mt-1 ml-3 pl-3 border-l-2 border-gray-200">
            <p class="text-sm font-medium text-gray-500 mt-1">ตำแหน่ง:</p>
            <ul class="mt-1 space-y-1">
                @foreach($department->positions as $position)
                    <li>
                        <div class="text-sm">
                            <span class="font-medium">{{ $position->name }}</span>
                            @php
                                $positionEmployeeCount = \App\Models\Employee::where('department_id', $department->id)
                                    ->where('position_id', $position->id)
                                    ->count();
                            @endphp
                            @if($positionEmployeeCount > 0)
                                <span class="text-gray-500">({{ $positionEmployeeCount }} คน)</span>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <!-- แสดงแผนกย่อย (recursive) -->
    @if($department->childDepartments->isNotEmpty())
        <ul class="mt-2 space-y-2">
            @foreach($department->childDepartments as $childDepartment)
                @include('organization.structure.partials.department-tree-item', [
                    'department' => $childDepartment,
                    'level' => $level + 1,
                ])
            @endforeach
        </ul>
    @endif
</li>
