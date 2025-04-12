@props(['disabled' => false])

<input
    type="radio"
    {{ $disabled ? 'disabled' : '' }}
    {!! $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600']) !!}>