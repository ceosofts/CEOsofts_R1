<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-3xl text-blue-800">แก้ไขหน่วยนับ</h2>
            <a href="{{ route('units.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-semibold">← กลับ</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow rounded-lg p-8">
            @if($errors->any())
                <div class="mb-4">
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="text-sm pl-4 list-disc">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            <form method="POST" action="{{ route('units.update', $unit) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">บริษัท</label>
                        <select name="company_id" class="w-full rounded border-gray-300 text-sm">
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" {{ $unit->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ชื่อ</label>
                        <input type="text" name="name" class="w-full rounded border-gray-300 text-sm" value="{{ $unit->name }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">รหัส</label>
                        <input type="text" name="code" class="w-full rounded border-gray-300 text-sm" value="{{ old('code', $unit->code) }}" placeholder="{{ $nextCode }}">
                        <div class="text-xs text-gray-400 mt-1">ตัวอย่างรหัสถัดไป: {{ $nextCode }}</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">สัญลักษณ์</label>
                        <input type="text" name="symbol" class="w-full rounded border-gray-300 text-sm" value="{{ $unit->symbol }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">หน่วยฐาน</label>
                        <select name="base_unit_id" class="w-full rounded border-gray-300 text-sm">
                            <option value="">-- ไม่มี --</option>
                            @foreach($baseUnits as $base)
                                <option value="{{ $base->id }}" {{ $unit->base_unit_id == $base->id ? 'selected' : '' }}>{{ $base->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">อัตราแปลง</label>
                        <input type="number" step="0.0001" name="conversion_factor" class="w-full rounded border-gray-300 text-sm" value="{{ $unit->conversion_factor }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ประเภท</label>
                        <input type="text" name="type" class="w-full rounded border-gray-300 text-sm" value="{{ $unit->type }}" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">หมวดหมู่</label>
                        <input type="text" name="category" class="w-full rounded border-gray-300 text-sm" value="{{ $unit->category }}">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">รายละเอียด</label>
                        <input type="text" name="description" class="w-full rounded border-gray-300 text-sm" value="{{ $unit->description }}">
                    </div>
                    <div class="flex gap-4 items-center">
                        <label class="inline-flex items-center text-sm">
                            <input type="checkbox" name="is_active" {{ $unit->is_active ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2">ใช้งาน</span>
                        </label>
                        <label class="inline-flex items-center text-sm">
                            <input type="checkbox" name="is_default" {{ $unit->is_default ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2">หน่วยหลัก</span>
                        </label>
                        <label class="inline-flex items-center text-sm">
                            <input type="checkbox" name="is_system" {{ $unit->is_system ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2">หน่วยระบบ</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-semibold">บันทึก</button>
                    <a href="{{ route('units.index') }}" class="ml-2 px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-semibold">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
