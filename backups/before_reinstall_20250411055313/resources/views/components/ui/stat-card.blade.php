@props([
    'title',
    'value',
    'subtitle' => null,
    'icon' => null,
    'color' => 'blue', // blue, green, red, yellow, indigo, purple, pink, gray
    'href' => null
])

@php
    $baseClasses = 'overflow-hidden rounded-lg shadow';
    $colorClasses = [
        'blue' => 'bg-blue-500',
        'green' => 'bg-green-500',
        'red' => 'bg-red-500',
        'yellow' => 'bg-yellow-500',
        'amber' => 'bg-amber-500',
        'indigo' => 'bg-indigo-500',
        'purple' => 'bg-purple-500',
        'pink' => 'bg-pink-500',
        'gray' => 'bg-gray-500',
    ][$color] ?? 'bg-blue-500';
    
    $wrapperClasses = $href ? 'cursor-pointer transition-transform duration-200 hover:scale-105' : '';
@endphp

<div {{ $attributes->merge(['class' => $wrapperClasses]) }}>
    @if($href)
    <a href="{{ $href }}" class="{{ $baseClasses }}">
    @else
    <div class="{{ $baseClasses }}">
    @endif
        <div class="{{ $colorClasses }} px-4 py-5 sm:p-6">
            <div class="flex items-center">
                @if($icon)
                <div class="flex-shrink-0 bg-white/20 rounded-full p-3 mr-4">
                    <x-dynamic-component :component="'heroicon-o-'.$icon" class="h-6 w-6 text-white" />
                </div>
                @endif
                
                <div>
                    <dt class="text-sm font-medium text-white truncate">
                        {{ $title }}
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-white">
                        {{ $value }}
                    </dd>
                    @if($subtitle)
                    <dd class="mt-1 text-sm font-medium text-white/80">
                        {{ $subtitle }}
                    </dd>
                    @endif
                </div>
            </div>
        </div>
    @if($href)
    </a>
    @else
    </div>
    @endif
</div>
