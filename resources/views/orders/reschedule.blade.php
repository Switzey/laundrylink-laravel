<x-layouts.app title="Reschedule Order #{{ $order->id }} - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Order schedule</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Reschedule order #{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Update pickup and delivery timing for {{ $order->cleaner?->business_name }}.</p>
        </div>
    </section>

    <section class="container-shell grid gap-8 py-10 lg:grid-cols-[1fr_340px]">
        <form method="POST" action="{{ route('orders.reschedule.update', $order) }}" class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            @csrf
            @method('PATCH')

            @error('schedule')
                <div class="mb-5 rounded-md bg-rose-50 p-4 text-sm font-semibold text-rose-900">{{ $message }}</div>
            @enderror

            <div class="grid gap-4 md:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-bold text-neutral-700">Pickup date</span>
                    <input name="pickup_date" type="date" value="{{ old('pickup_date', $order->pickup_date?->toDateString()) }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    @error('pickup_date') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-neutral-700">Pickup time window</span>
                    <select name="pickup_time_window" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        <option value="">Choose a time</option>
                        @foreach ($timeWindows as $window)
                            <option value="{{ $window }}" @selected(old('pickup_time_window', $order->pickup_time_window) === $window)>{{ $window }}</option>
                        @endforeach
                    </select>
                    @error('pickup_time_window') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-neutral-700">Delivery date</span>
                    <input name="delivery_date" type="date" value="{{ old('delivery_date', $order->delivery_date?->toDateString()) }}" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                    @error('delivery_date') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-neutral-700">Delivery time window</span>
                    <select name="delivery_time_window" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">
                        <option value="">Choose a time if known</option>
                        @foreach ($timeWindows as $window)
                            <option value="{{ $window }}" @selected(old('delivery_time_window', $order->delivery_time_window) === $window)>{{ $window }}</option>
                        @endforeach
                    </select>
                    @error('delivery_time_window') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <label class="block">
                    <span class="text-sm font-bold text-neutral-700">Pickup notes</span>
                    <textarea name="pickup_notes" rows="4" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('pickup_notes', $order->pickup_notes) }}</textarea>
                    @error('pickup_notes') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>

                <label class="block">
                    <span class="text-sm font-bold text-neutral-700">Delivery notes</span>
                    <textarea name="delivery_notes" rows="4" class="mt-2 w-full rounded-md border border-neutral-300 px-3 py-3 text-sm focus:border-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-100">{{ old('delivery_notes', $order->delivery_notes) }}</textarea>
                    @error('delivery_notes') <span class="mt-2 block text-sm font-semibold text-rose-700">{{ $message }}</span> @enderror
                </label>
            </div>

            <div class="mt-6 flex flex-wrap gap-3 border-t border-neutral-200 pt-5">
                <button type="submit" class="rounded-md bg-teal-700 px-5 py-3 text-sm font-bold text-white hover:bg-teal-800">Update Schedule</button>
                <a href="{{ route('orders.show', $order) }}" class="rounded-md border border-neutral-300 px-5 py-3 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Back to Order</a>
            </div>
        </form>

        <aside class="h-fit rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
            <h2 class="text-xl font-black text-neutral-950">Current route</h2>
            <dl class="mt-5 space-y-4 text-sm">
                <div>
                    <dt class="text-neutral-500">Pickup</dt>
                    <dd class="font-bold text-neutral-950">{{ $order->pickup_address }}</dd>
                    <dd class="mt-1 text-neutral-600">{{ $order->pickup_date?->format('M j, Y') ?? 'Not set' }}{{ $order->pickup_time_window ? ' - '.$order->pickup_time_window : '' }}</dd>
                </div>
                <div>
                    <dt class="text-neutral-500">Delivery</dt>
                    <dd class="font-bold text-neutral-950">{{ $order->delivery_address }}</dd>
                    <dd class="mt-1 text-neutral-600">{{ $order->delivery_date?->format('M j, Y') ?? 'Not set' }}{{ $order->delivery_time_window ? ' - '.$order->delivery_time_window : '' }}</dd>
                </div>
                <div>
                    <dt class="text-neutral-500">Status</dt>
                    <dd class="mt-1"><x-status-badge :status="$order->status" /></dd>
                </div>
            </dl>
        </aside>
    </section>
</x-layouts.app>
