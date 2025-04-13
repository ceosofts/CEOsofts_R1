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

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-accent-500 to-accent-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:from-accent-600 hover:to-accent-700 active:from-accent-700 active:to-accent-800 focus:outline-none focus:border-accent-700 focus:ring focus:ring-accent-200 disabled:opacity-25 transition transform hover:-translate-y-0.5']) }}>
    {{ $slot }}
</button>