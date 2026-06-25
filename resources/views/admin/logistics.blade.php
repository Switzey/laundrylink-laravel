<x-layouts.app title="Admin Logistics - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Admin logistics</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Pickup and delivery overview</h1>
                <p class="mt-4 max-w-2xl text-neutral-600">Monitor scheduled pickups and deliveries across cleaners.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="rounded-md border border-neutral-300 px-4 py-2 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Back to Dashboard</a>
        </div>
    </section>

    <section class="container-shell space-y-6 py-10">
        <div class="flex flex-wrap gap-2">
            @foreach ($filters as $option)
                <a href="{{ route('admin.logistics', ['filter' => $option]) }}" class="rounded-full px-4 py-2 text-sm font-bold {{ $filter === $option ? 'bg-teal-700 text-white' : 'bg-white text-neutral-700 hover:bg-teal-50 hover:text-teal-800' }}">
                    {{ str($option)->replace('_', ' ')->title() }}
                </a>
            @endforeach
        </div>

        <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                        <tr>
                            <th class="px-5 py-3">Order</th>
                            <th class="px-5 py-3">Cleaner</th>
                            <th class="px-5 py-3">Customer</th>
                            <th class="px-5 py-3">Pickup</th>
                            <th class="px-5 py-3">Delivery</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Payment</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-5 py-4">
                                    <a href="{{ route('orders.show', $order) }}" class="font-bold text-neutral-950 hover:text-teal-800">#{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</a>
                                </td>
                                <td class="px-5 py-4 text-neutral-600">{{ $order->cleaner?->business_name ?? '-' }}</td>
                                <td class="px-5 py-4 text-neutral-600">{{ $order->customer?->name ?? 'Guest' }}</td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-neutral-900">{{ $order->pickup_date?->format('M j, Y') ?? 'Not set' }}</p>
                                    <p class="mt-1 text-xs text-neutral-500">{{ $order->pickup_time_window ?? 'No window' }}</p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-neutral-900">{{ $order->delivery_date?->format('M j, Y') ?? 'Not set' }}</p>
                                    <p class="mt-1 text-xs text-neutral-500">{{ $order->delivery_time_window ?? 'No window' }}</p>
                                </td>
                                <td class="px-5 py-4"><x-status-badge :status="$order->status" /></td>
                                <td class="px-5 py-4"><x-payment-status-badge :status="$order->payment_status" /></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-neutral-600">No orders match this logistics filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </section>
</x-layouts.app>
