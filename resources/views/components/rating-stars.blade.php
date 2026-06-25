@props(['rating' => 0, 'count' => null])

@php
    $ratingValue = (float) $rating;
    $filled = (int) round($ratingValue);
@endphp

<span class="inline-flex items-center gap-2">
    <span class="tracking-wide text-amber-500" aria-label="Rating {{ number_format($ratingValue, 1) }} out of 5">
        @for ($i = 1; $i <= 5; $i++)
            {{ $i <= $filled ? '★' : '☆' }}
        @endfor
    </span>
    <span class="text-sm font-bold text-neutral-800">
        {{ number_format($ratingValue, 1) }}/5
        @if ($count !== null)
            <span class="font-medium text-neutral-500">({{ $count }} {{ (int) $count === 1 ? 'review' : 'reviews' }})</span>
        @endif
    </span>
</span>
