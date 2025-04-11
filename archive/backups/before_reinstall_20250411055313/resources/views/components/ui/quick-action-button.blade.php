@props([
    'route' => null,
    'href' => null,
    'icon' => null,
    'label' => '',
    'color' => 'primary', // primary, secondary, success, danger, warning, info
])

@php
    $url = $route ? route($route) : ($href ?? '#');
    
    $colorClasses = [
        'primary' => 'bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-300',
        'secondary' => 'bg-gray-50 text-gray-700 hover:bg-gray-100 border-gray-300',
        'success' => 'bg-green-50 text-green-700 hover:bg-green-100 border-green-300',
        'danger' => 'bg-red-50 text-red-700 hover:bg-red-100 border-red-300',
        'warning' => 'bg-yellow-50 text-yellow-700 hover:bg-yellow-100 border-yellow-300',
        'info' => 'bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border-indigo-300',
    ][$color] ?? 'bg-blue-50 text-blue-700 hover:bg-blue-100 border-blue-300';
@endphp

<a 
    href="{{ $url }}" 
    {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center p-4 rounded-lg border transition-colors duration-200 ' . $colorClasses]) }}
>
    @if($icon)
        <x-dynamic-component :component="'heroicon-o-'.$icon" class="h-6 w-6 mb-2" />
    @endif
    
    <span class="text-sm font-medium text-center">{{ $label }}</span>
</a>
