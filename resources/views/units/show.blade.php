<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-3xl text-blue-800">รายละเอียดหน่วยนับ</h2>
            <a href="{{ route('units.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 text-sm font-semibold">← กลับ</a>
        </div>
    </x-slot>
    <div class="py-8">
        <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 shadow rounded-lg p-8">
            <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">ชื่อ</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $unit->name }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">รหัส</dt>
                    <dd class="font-mono text-xs text-gray-900 dark:text-gray-100">{{ $unit->code }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">สัญลักษณ์</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $unit->symbol ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">บริษัท</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $unit->company->name ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">หน่วยฐาน</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $unit->baseUnit->name ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">อัตราแปลง</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $unit->conversion_factor }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">ประเภท</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $unit->type }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">หมวดหมู่</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $unit->category ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">รายละเอียด</dt>
                    <dd class="text-gray-900 dark:text-gray-100">{{ $unit->description ?? '-' }}</dd>
                </div>
                <div class="py-3 flex justify-between">
                    <dt class="font-medium text-gray-700 dark:text-gray-300">สถานะ</dt>
                    <dd>
                        <span class="inline-block px-2 py-0.5 rounded text-xs font-semibold {{ $unit->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $unit->is_active ? 'ใช้งาน' : 'ไม่ใช้งาน' }}
                        </span>
                    </dd>
                </div>
            </dl>
            <div class="mt-6 flex justify-end">
                <a href="{{ route('units.edit', $unit) }}" class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-sm font-semibold mr-2">แก้ไข</a>
                <form action="{{ route('units.destroy', $unit) }}" method="POST" onsubmit="return confirm('ลบหน่วยนับนี้?');" class="inline">
                    @csrf @method('DELETE')
                    <button class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm font-semibold" type="submit">ลบ</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
