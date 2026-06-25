@props(['available'])

@php
    $classes = $available ? 'bg-teal-100 text-teal-800' : 'bg-rose-100 text-rose-800';
@endphp

<span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $classes }}">
    {{ $available ? 'Available' : 'Unavailable' }}
</span>
