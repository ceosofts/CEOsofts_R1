@props(['type' => 'button', 'color' => 'gray'])

@php
// กำหนดสีต่างๆ ตาม color prop
$colorClasses = [
'gray' => 'bg-gray-500 hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 border-gray-500',
'blue' => 'bg-blue-500 hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 border-blue-500',
'red' => 'bg-red-500 hover:bg-red-600 focus:bg-red-600 active:bg-red-700 border-red-500',
'green' => 'bg-green-500 hover:bg-green-600 focus:bg-green-600 active:bg-green-700 border-green-500',
'yellow' => 'bg-yellow-500 hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 border-yellow-500',
][$color] ?? 'bg-gray-500 hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 border-gray-500';
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'inline-flex items-center px-4 py-2 ' . $colorClasses . ' border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>