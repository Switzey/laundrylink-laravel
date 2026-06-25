@props(['active'])

@php
    $classes = $active ? 'bg-teal-100 text-teal-800' : 'bg-neutral-100 text-neutral-700';
@endphp

<span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $classes }}">
    {{ $active ? 'Active' : 'Inactive' }}
</span>
