@props(['type' => null])

@php
    $classes = [
        'order_created' => 'bg-teal-100 text-teal-800',
        'order_accepted' => 'bg-sky-100 text-sky-800',
        'order_status_updated' => 'bg-indigo-100 text-indigo-800',
        'payment_successful' => 'bg-emerald-100 text-emerald-800',
        'payment_failed' => 'bg-rose-100 text-rose-800',
        'cleaner_approved' => 'bg-amber-100 text-amber-800',
        'review_received' => 'bg-purple-100 text-purple-800',
        'general' => 'bg-neutral-100 text-neutral-800',
    ][$type ?? 'general'] ?? 'bg-neutral-100 text-neutral-800';
@endphp

<span class="inline-flex rounded-full px-3 py-1 text-xs font-bold {{ $classes }}">
    {{ str($type ?? 'general')->replace('_', ' ')->title() }}
</span>
