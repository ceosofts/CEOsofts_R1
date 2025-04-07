@props(['route' => '', 'icon' => ''])

@php
    $isActive = request()->routeIs($route);
    $activeClass = $isActive ? 'bg-primary-700 dark:bg-gray-700 text-white' : 'text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white';
@endphp

<li>
    <a href="{{ $route ? route($route) : '#' }}" class="{{ $activeClass }} group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors">
        @if ($icon)
            <x-dynamic-component :component="'heroicon-o-' . $icon" class="mr-3 h-5 w-5" />
        @endif
        {{ $slot }}
    </a>
</li>
