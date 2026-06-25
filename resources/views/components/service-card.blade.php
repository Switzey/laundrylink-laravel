@props(['service', 'withQuantity' => false])

<div class="rounded-lg border border-neutral-200 bg-white p-4 shadow-sm">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h4 class="font-bold text-neutral-950">{{ $service->name }}</h4>
            <p class="mt-1 text-sm leading-6 text-neutral-600">{{ $service->description }}</p>
            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.18em] text-neutral-500">{{ str_replace('_', ' ', $service->unit) }}</p>
        </div>
        <div class="shrink-0 text-left sm:text-right">
            <p class="text-lg font-black text-teal-800">NGN {{ number_format((float) $service->price, 2) }}</p>
            @if ($withQuantity)
                <label class="mt-3 block text-xs font-semibold uppercase tracking-[0.16em] text-neutral-500" for="service-{{ $service->id }}">
                    Qty
                </label>
                <input id="service-{{ $service->id }}" name="services[{{ $service->id }}]" type="number" value="{{ old("services.{$service->id}", 0) }}" min="0" class="mt-1 w-24 rounded-md border border-neutral-300 px-3 py-2 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
            @endif
        </div>
    </div>
</div>
