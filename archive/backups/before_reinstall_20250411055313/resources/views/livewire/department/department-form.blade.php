<div>
    <form wire:submit.prevent="save" class="space-y-6">
        <div>
            <x-label for="name" value="ชื่อแผนก" />
            <x-input wire:model="department.name" id="name" type="text" class="mt-1 block w-full" placeholder="ระบุชื่อแผนก" autofocus />
            <x-input-error for="department.name" class="mt-2" />
        </div>

        <div>
            <x-label for="code" value="รหัสแผนก" />
            <x-input wire:model="department.code" id="code" type="text" class="mt-1 block w-full" placeholder="ระบุรหัสแผนก (ถ้ามี)" />
            <x-input-error for="department.code" class="mt-2" />
        </div>

        <div>
            <x-label for="parent_id" value="แผนกหลัก" />
            <x-select wire:model="parent_id" id="parent_id" class="mt-1 block w-full">
                <option value="">-- ไม่มีแผนกหลัก --</option>
                @foreach ($parentDepartments as $parentDept)
                    <option value="{{ $parentDept->id }}">{{ $parentDept->name }}</option>
                @endforeach
            </x-select>
            <x-input-error for="parent_id" class="mt-2" />
        </div>

        <div>
            <x-label for="description" value="รายละเอียด" />
            <x-textarea wire:model="department.description" id="description" rows="4" class="mt-1 block w-full" placeholder="ระบุรายละเอียดแผนก (ถ้ามี)"></x-textarea>
            <x-input-error for="department.description" class="mt-2" />
        </div>

        <div class="flex items-center">
            <x-checkbox wire:model="department.is_active" id="is_active" />
            <x-label for="is_active" value="เปิดใช้งาน" class="ml-2" />
            <x-input-error for="department.is_active" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-button type="button" variant="secondary" href="{{ route('department.index') }}" class="mr-2">
                ยกเลิก
            </x-button>

            <x-button type="submit" variant="primary">
                {{ $isEdit ? 'อัปเดต' : 'บันทึก' }}
            </x-button>
        </div>
    </form>
</div>
