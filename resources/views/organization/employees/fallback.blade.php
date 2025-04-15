<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('พนักงาน (Fallback)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-red-600 font-bold">
                        ไฟล์ view ที่ต้องการไม่พบ แต่ระบบทำงานได้!
                    </div>
                    
                    <div class="mt-4">
                        <p>นี่เป็นหน้าสำรองเพื่อตรวจสอบว่าระบบยังทำงานได้ถูกต้อง แต่ไม่พบไฟล์ view ที่ต้องการ</p>
                        
                        <div class="mt-6">
                            <h3 class="font-bold text-lg">ตรวจสอบ:</h3>
                            <ul class="list-disc pl-6 mt-2">
                                <li>ไฟล์ resources/views/organization/employees/index.blade.php มีอยู่หรือไม่</li>
                                <li>ไฟล์ resources/views/organization/employees/create.blade.php มีอยู่หรือไม่</li>
                                <li>ไฟล์ resources/views/organization/employees/edit.blade.php มีอยู่หรือไม่</li>
                                <li>ไฟล์ resources/views/organization/employees/show.blade.php มีอยู่หรือไม่</li>
                            </ul>
                        </div>
                        
                        <div class="mt-6">
                            <h3 class="font-bold text-lg">ลองเข้า:</h3>
                            <ul class="list-disc pl-6 mt-2">
                                <li><a href="{{ route('test.employee.view') }}" class="text-blue-600 hover:underline">หน้าทดสอบ</a></li>
                                <li><a href="{{ route('debug.employees') }}" class="text-blue-600 hover:underline">หน้า Debug</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
