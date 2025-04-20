@props(['status' => 'pending'])

@php
    $statusClasses = [
        'pending' => 'bg-amber-100 text-amber-800',
        'processing' => 'bg-blue-100 text-blue-800', 
        'shipped' => 'bg-emerald-100 text-emerald-800',
        'delivered' => 'bg-teal-100 text-teal-800', 
        'returned' => 'bg-rose-100 text-rose-800',
        'cancelled' => 'bg-slate-100 text-slate-800',
    ];
    
    $class = $statusClasses[$status] ?? $statusClasses['pending'];
@endphp

<span {{ $attributes->merge(['class' => "px-2 py-1 text-sm rounded-full $class"]) }}>
    {{ ucfirst($status) }}
</span>
