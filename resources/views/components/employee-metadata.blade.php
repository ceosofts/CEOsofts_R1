@props(['metadata'])

@if(is_array($metadata) && count($metadata) > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach($metadata as $key => $value)
        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900 transition-colors">
            <h4 class="font-medium text-lg text-blue-600 dark:text-blue-300">{{ ucfirst(str_replace('_', ' ', $key)) }}</h4>
            <p class="mt-2">{{ $value }}</p>
        </div>
        @endforeach
    </div>
@else
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
        <p class="text-gray-500 dark:text-gray-400">ไม่มีข้อมูลเพิ่มเติม</p>
    </div>
@endif
