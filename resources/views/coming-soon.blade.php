<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $displayName }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col items-center justify-center text-center space-y-6 py-12">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h1 class="text-3xl font-bold">กำลังพัฒนา</h1>
                        <p class="text-xl">คุณสมบัติ "{{ $displayName }}" กำลังอยู่ในระหว่างการพัฒนา</p>
                        <p class="text-gray-500 max-w-md">
                            ขอบคุณสำหรับความสนใจ เรากำลังทำงานอย่างหนักเพื่อพัฒนาคุณสมบัตินี้ให้พร้อมใช้งาน
                            โปรดติดตามการอัปเดทในเร็วๆ นี้
                        </p>
                        <a href="{{ route('home') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                            กลับไปยังหน้าหลัก
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
