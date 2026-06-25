@props(['status'])

@php
    $classes = [
        'unpaid' => 'bg-neutral-100 text-neutral-800',
        'pending' => 'bg-sky-100 text-sky-800',
        'paid' => 'bg-teal-100 text-teal-800',
        'failed' => 'bg-rose-100 text-rose-800',
    ][$status] ?? 'bg-neutral-100 text-neutral-800';
@endphp

<span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $classes }}">
    {{ str($status)->replace('_', ' ')->title() }}
</span>
