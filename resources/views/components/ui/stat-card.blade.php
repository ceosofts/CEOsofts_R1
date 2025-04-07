@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => null,
    'color' => 'blue',
    'increase' => null,
    'decrease' => null,
])

@php
    $colors = [
        'blue' => 'bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100',
        'green' => 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100',
        'red' => 'bg-red-100 text-red-800 dark:bg-red-800 dark:text-red-100',
        'yellow' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
        'amber' => 'bg-amber-100 text-amber-800 dark:bg-amber-800 dark:text-amber-100',
        'purple' => 'bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-100',
        'indigo' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-100',
        'pink' => 'bg-pink-100 text-pink-800 dark:bg-pink-800 dark:text-pink-100',
    ];
    
    $colorClass = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 border border-gray-200 dark:border-gray-700">
    <div class="flex items-center">
        @if ($icon)
            <div class="flex-shrink-0">
                <span class="inline-flex items-center justify-center h-12 w-12 rounded-full {{ $colorClass }}">
                    <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-6 h-6" />
                </span>
            </div>
        @endif
        
        <div class="{{ $icon ? 'ml-5' : '' }}">
            <dt class="text-base font-normal text-gray-500 dark:text-gray-400 truncate">
                {{ $title }}
            </dt>
            <dd class="mt-1 flex items-baseline">
                <div class="text-2xl font-semibold text-gray-900 dark:text-white">
                    {{ $value }}
                </div>
                
                @if ($subtitle)
                    <div class="ml-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ $subtitle }}
                    </div>
                @endif
                
                @if ($increase)
                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600 dark:text-green-500">
                        <svg class="self-center flex-shrink-0 h-5 w-5 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">เพิ่มขึ้น</span>
                        {{ $increase }}
                    </div>
                @endif
                
                @if ($decrease)
                    <div class="ml-2 flex items-baseline text-sm font-semibold text-red-600 dark:text-red-500">
                        <svg class="self-center flex-shrink-0 h-5 w-5 text-red-500 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        <span class="sr-only">ลดลง</span>
                        {{ $decrease }}
                    </div>
                @endif
            </dd>
        </div>
    </div>
</div>
