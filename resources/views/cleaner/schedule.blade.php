<x-layouts.app title="Vendor Schedule - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Vendor logistics</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Pickup and delivery schedule</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">See assigned pickups and deliveries grouped by timing, with order and payment status in one place.</p>
        </div>
    </section>

    <section class="container-shell py-10">
        @if (! $cleaner)
            <div class="rounded-lg border border-dashed border-neutral-300 bg-white p-8 text-neutral-600">
                Create your cleaner profile before logistics can be scheduled.
            </div>
        @else
            @php
                $groups = [
                    'Pickup today' => $pickupToday,
                    'Upcoming pickups' => $upcomingPickups,
                    'Deliveries today' => $deliveriesToday,
                    'Upcoming deliveries' => $upcomingDeliveries,
                ];
            @endphp

            <div class="grid gap-8 xl:grid-cols-2">
                @foreach ($groups as $title => $orders)
                    <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                        <div class="border-b border-neutral-200 p-5">
                            <h2 class="text-xl font-black text-neutral-950">{{ $title }}</h2>
                        </div>
                        <div class="divide-y divide-neutral-200">
                            @forelse ($orders as $order)
                                <article class="p-5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div>
                                            <a href="{{ route('orders.show', $order) }}" class="font-black text-neutral-950 hover:text-teal-800">
                                                Order #{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}
                                            </a>
                                            <p class="mt-1 text-sm text-neutral-600">{{ $order->customer?->name ?? 'Guest customer' }} - NGN {{ number_format((float) $order->total, 2) }}</p>
                                        </div>
                                        <div class="flex flex-wrap gap-2 sm:justify-end">
                                            <x-status-badge :status="$order->status" />
                                            <x-payment-status-badge :status="$order->payment_status" />
                                        </div>
                                    </div>

                                    <dl class="mt-4 grid gap-3 text-sm md:grid-cols-2">
                                        <div class="rounded-md bg-neutral-50 p-3">
                                            <dt class="text-neutral-500">Pickup</dt>
                                            <dd class="font-semibold text-neutral-900">{{ $order->pickup_address }}</dd>
                                            <dd class="mt-1 text-neutral-600">{{ $order->pickup_date?->format('M j, Y') ?? 'Not set' }}{{ $order->pickup_time_window ? ' - '.$order->pickup_time_window : '' }}</dd>
                                        </div>
                                        <div class="rounded-md bg-neutral-50 p-3">
                                            <dt class="text-neutral-500">Delivery</dt>
                                            <dd class="font-semibold text-neutral-900">{{ $order->delivery_address }}</dd>
                                            <dd class="mt-1 text-neutral-600">{{ $order->delivery_date?->format('M j, Y') ?? 'Not set' }}{{ $order->delivery_time_window ? ' - '.$order->delivery_time_window : '' }}</dd>
                                        </div>
                                    </dl>
                                </article>
                            @empty
                                <div class="p-8 text-neutral-600">No orders in this schedule group.</div>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
