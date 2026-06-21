@props(['status'])

@php
    $classes = [
        'pending' => 'bg-yellow-100 text-yellow-800',
        'accepted' => 'bg-sky-100 text-sky-800',
        'picked_up' => 'bg-indigo-100 text-indigo-800',
        'in_cleaning' => 'bg-purple-100 text-purple-800',
        'ready' => 'bg-emerald-100 text-emerald-800',
        'out_for_delivery' => 'bg-cyan-100 text-cyan-800',
        'completed' => 'bg-teal-100 text-teal-800',
        'cancelled' => 'bg-rose-100 text-rose-800',
    ][$status] ?? 'bg-neutral-100 text-neutral-800';
@endphp

<span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $classes }}">
    {{ str($status)->replace('_', ' ')->title() }}
</span>
