<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('แผงควบคุม') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-xl font-bold mb-4">ยินดีต้อนรับเข้าสู่ระบบ CEOsofts R1</h2>
                    <p class="mb-4">คุณได้เข้าสู่ระบบสำเร็จแล้ว</p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                        <!-- Card 1 - Company Stats -->
                        <div class="bg-blue-50 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        บริษัททั้งหมด
                                    </dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                        {{ $companyCount }}
                                    </dd>
                                </dl>
                            </div>
                            <div class="bg-blue-100 px-4 py-4 sm:px-6">
                                <div class="text-sm">
                                    <a href="{{ route('companies.index') }}" class="font-medium text-blue-700 hover:text-blue-900">
                                        ดูทั้งหมด
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card 2 - Department Stats -->
                        <div class="bg-green-50 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">
                                        แผนกทั้งหมด
                                    </dt>
                                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                                        {{ $departmentCount }}
                                    </dd>
                                </dl>
                            </div>
                            <div class="bg-green-100 px-4 py-4 sm:px-6">
                                <div class="text-sm">
                                    <a href="#" class="font-medium text-green-700 hover:text-green-900">
                                        ดูทั้งหมด
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3 - Quick Actions -->
                        <div class="bg-purple-50 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900">การดำเนินการด่วน</h3>
                                <ul class="mt-4 space-y-2">
                                    <li>
                                        <a href="{{ route('customers.index') }}" class="text-purple-700 hover:text-purple-900">
                                            จัดการลูกค้า
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('quotations.index') }}" class="text-purple-700 hover:text-purple-900">
                                            สร้างใบเสนอราคา
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('orders.index') }}" class="text-purple-700 hover:text-purple-900">
                                            จัดการใบสั่งขาย
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>