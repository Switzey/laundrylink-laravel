@props(['cleaner'])

<article class="flex h-full flex-col justify-between rounded-lg border border-neutral-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="space-y-4">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-teal-700">{{ $cleaner->city }}</p>
                <h3 class="mt-1 text-xl font-bold text-neutral-950">{{ $cleaner->business_name }}</h3>
            </div>
            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">
                {{ number_format((float) $cleaner->rating, 1) }}/5
            </span>
        </div>

        <p class="text-sm leading-6 text-neutral-600">{{ $cleaner->description }}</p>

        <dl class="grid grid-cols-2 gap-3 text-sm">
            <div class="rounded-md bg-neutral-50 p-3">
                <dt class="text-neutral-500">Turnaround</dt>
                <dd class="font-semibold text-neutral-900">{{ $cleaner->turnaround_time ?? 'Flexible' }}</dd>
            </div>
            <div class="rounded-md bg-neutral-50 p-3">
                <dt class="text-neutral-500">Services</dt>
                <dd class="font-semibold text-neutral-900">{{ $cleaner->services_count ?? $cleaner->services->count() }}</dd>
            </div>
        </dl>
    </div>

    <a href="{{ route('cleaners.show', $cleaner) }}" class="mt-6 inline-flex items-center justify-center rounded-md bg-teal-700 px-4 py-2 text-sm font-bold text-white hover:bg-teal-800">
        View Services
    </a>
</article>
