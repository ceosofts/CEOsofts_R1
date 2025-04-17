<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow duration-300']) }}>
    <div class="p-4">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $branchOffice->name }}
                    @if($branchOffice->is_headquarters)
                        <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            สำนักงานใหญ่
                        </span>
                    @endif
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $branchOffice->code }} - {{ $branchOffice->company->name ?? 'ไม่ระบุบริษัท' }}
                </p>
            </div>
            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $branchOffice->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $branchOffice->is_active ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}
            </span>
        </div>

        @if($detailed)
            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">ที่อยู่</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $branchOffice->address }}</p>
                </div>
                
                <div>
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">การติดต่อ</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                        {{ $branchOffice->phone ?? '-' }}<br>
                        {{ $branchOffice->email ?? '-' }}
                    </p>
                </div>

                @if(isset($branchOffice->metadata['region']) || isset($branchOffice->metadata['tax_branch_id']))
                    <div class="sm:col-span-2 border-t dark:border-gray-700 pt-3 mt-2">
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">ข้อมูลเพิ่มเติม</h4>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            @if(isset($branchOffice->metadata['region']))
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">ภูมิภาค:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $branchOffice->metadata['region'] }}</span>
                                </div>
                            @endif

                            @if(isset($branchOffice->metadata['tax_branch_id']))
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">รหัสสาขากรมสรรพากร:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $branchOffice->metadata['tax_branch_id'] }}</span>
                                </div>
                            @endif

                            @if(isset($branchOffice->metadata['opening_date']))
                                <div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">เปิดทำการเมื่อ:</span>
                                    <span class="text-sm text-gray-900 dark:text-white">
                                        {{ \Carbon\Carbon::parse($branchOffice->metadata['opening_date'])->format('d/m/Y') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-4 flex justify-end">
            <a href="{{ route('branch-offices.show', $branchOffice->id) }}" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                {{ $detailed ? 'แก้ไขข้อมูล' : 'ดูข้อมูลเพิ่มเติม' }} &rarr;
            </a>
        </div>
    </div>
</div>
