<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $displayName ?? 'ฟีเจอร์กำลังพัฒนา' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="text-center py-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-24 w-24 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                        </svg>
                        <h3 class="text-3xl font-bold mt-8 mb-4">ฟีเจอร์นี้กำลังพัฒนา</h3>
                        <p class="text-lg text-gray-600 mb-8">ขณะนี้เรากำลังพัฒนาฟีเจอร์ {{ $displayName ?? $feature }} อยู่</p>
                        <p class="text-gray-500">โปรดกลับมาใหม่ในเร็วๆ นี้</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
