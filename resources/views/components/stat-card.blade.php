@props(['label', 'value', 'caption' => null])

<div class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
    <p class="text-sm font-medium text-neutral-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-black text-neutral-950">{{ $value }}</p>
    @if ($caption)
        <p class="mt-2 text-sm text-neutral-600">{{ $caption }}</p>
    @endif
</div>
