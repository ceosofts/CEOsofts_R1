@props([
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'disabled' => false,
    'icon' => null,
    'iconPosition' => 'left',
    'rounded' => false,
])

@php
    $variantClasses = [
        'primary' => 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500 text-white',
        'secondary' => 'bg-gray-200 hover:bg-gray-300 focus:ring-gray-400 text-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200',
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500 text-white',
        'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500 text-white',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 focus:ring-yellow-400 text-white',
        'info' => 'bg-blue-500 hover:bg-blue-600 focus:ring-blue-400 text-white',
        'light' => 'bg-gray-100 hover:bg-gray-200 focus:ring-gray-300 text-gray-800 dark:bg-gray-800 dark:hover:bg-gray-700 dark:text-gray-200',
        'dark' => 'bg-gray-800 hover:bg-gray-900 focus:ring-gray-700 text-white',
        'outline-primary' => 'bg-transparent hover:bg-primary-50 text-primary-600 border border-primary-600 hover:border-primary-700 hover:text-primary-700 focus:ring-primary-500 dark:hover:bg-gray-800',
        'outline-secondary' => 'bg-transparent hover:bg-gray-50 text-gray-700 border border-gray-300 hover:border-gray-400 hover:text-gray-800 focus:ring-gray-400 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-800',
    ][$variant];
    
    $sizeClasses = [
        'xs' => 'px-2 py-1 text-xs',
        'sm' => 'px-2 py-1 text-sm',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-5 py-2 text-base',
        'xl' => 'px-6 py-3 text-base',
    ][$size];
    
    $roundedClass = $rounded ? 'rounded-full' : 'rounded-md';
    $disabledClass = $disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
@endphp

<button 
    type="{{ $type }}" 
    {{ $disabled ? 'disabled' : '' }} 
    {{ $attributes->merge(['class' => "{$variantClasses} {$sizeClasses} {$roundedClass} {$disabledClass} inline-flex items-center justify-center font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors duration-200"]) }}
>
    @if ($icon && $iconPosition === 'left')
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5 mr-2 -ml-1" />
    @endif
    
    {{ $slot }}
    
    @if ($icon && $iconPosition === 'right')
        <x-dynamic-component :component="'heroicon-o-' . $icon" class="w-5 h-5 ml-2 -mr-1" />
    @endif
</button>
