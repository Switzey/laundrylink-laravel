<x-layouts.app title="Admin Reports - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell flex flex-col gap-4 py-12 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Admin reports</p>
                <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Platform reporting</h1>
                <p class="mt-4 max-w-2xl text-neutral-600">Review order health, payment status, cleaner performance, and recent exceptions.</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="rounded-md border border-neutral-300 bg-white px-4 py-2 text-sm font-bold text-neutral-800 hover:border-teal-700 hover:text-teal-800">Back to Dashboard</a>
        </div>
    </section>

    <section class="container-shell space-y-8 py-10">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total reviews" :value="$totalReviews" />
            <x-stat-card label="Average rating" :value="number_format((float) $averageRating, 1).'/5'" />
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Orders by status</h2>
                <div class="mt-5 divide-y divide-neutral-200">
                    @forelse ($ordersByStatus as $status => $count)
                        <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <x-status-badge :status="$status" />
                            <span class="text-lg font-black text-neutral-950">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No orders have been created yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Payments by status</h2>
                <div class="mt-5 divide-y divide-neutral-200">
                    @forelse ($paymentsByStatus as $status => $count)
                        <div class="flex items-center justify-between gap-4 py-3 first:pt-0 last:pb-0">
                            <x-payment-status-badge :status="$status" />
                            <span class="text-lg font-black text-neutral-950">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No payments have been started yet.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="grid gap-8 xl:grid-cols-3">
            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Top cleaners by orders</h2>
                <div class="mt-5 divide-y divide-neutral-200">
                    @forelse ($topCleanersByOrders as $cleaner)
                        <div class="py-3 first:pt-0 last:pb-0">
                            <p class="font-bold text-neutral-950">{{ $cleaner->business_name }}</p>
                            <p class="mt-1 text-sm text-neutral-600">{{ $cleaner->orders_count }} orders</p>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No cleaner order data yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Top cleaners by revenue</h2>
                <div class="mt-5 divide-y divide-neutral-200">
                    @forelse ($topCleanersByRevenue as $cleaner)
                        <div class="py-3 first:pt-0 last:pb-0">
                            <p class="font-bold text-neutral-950">{{ $cleaner->business_name }}</p>
                            <p class="mt-1 text-sm text-neutral-600">NGN {{ number_format((float) $cleaner->paid_revenue, 2) }} paid revenue</p>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No paid revenue yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white p-5 shadow-sm">
                <h2 class="text-xl font-black text-neutral-950">Most reviewed cleaners</h2>
                <div class="mt-5 divide-y divide-neutral-200">
                    @forelse ($mostReviewedCleaners as $cleaner)
                        <div class="py-3 first:pt-0 last:pb-0">
                            <p class="font-bold text-neutral-950">{{ $cleaner->business_name }}</p>
                            <div class="mt-1"><x-rating-stars :rating="$cleaner->rating" :count="$cleaner->reviews_count" /></div>
                        </div>
                    @empty
                        <p class="text-sm text-neutral-600">No cleaner reviews yet.</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
            <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">Recent paid orders</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[680px] text-left text-sm">
                        <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                            <tr>
                                <th class="px-5 py-3">Order</th>
                                <th class="px-5 py-3">Cleaner</th>
                                <th class="px-5 py-3">Customer</th>
                                <th class="px-5 py-3">Total</th>
                                <th class="px-5 py-3">Paid</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse ($recentPaidOrders as $order)
                                <tr>
                                    <td class="px-5 py-4"><a href="{{ route('orders.show', $order) }}" class="font-bold text-neutral-950 hover:text-teal-800">#{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</a></td>
                                    <td class="px-5 py-4 text-neutral-600">{{ $order->cleaner?->business_name }}</td>
                                    <td class="px-5 py-4 text-neutral-600">{{ $order->customer?->name ?? 'Guest' }}</td>
                                    <td class="px-5 py-4 font-semibold">NGN {{ number_format((float) $order->total, 2) }}</td>
                                    <td class="px-5 py-4 text-neutral-600">{{ $order->paid_at?->format('M j, Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-8 text-neutral-600">No paid orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">Recent cancelled orders</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-sm">
                        <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                            <tr>
                                <th class="px-5 py-3">Order</th>
                                <th class="px-5 py-3">Cleaner</th>
                                <th class="px-5 py-3">Customer</th>
                                <th class="px-5 py-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-neutral-200">
                            @forelse ($recentCancelledOrders as $order)
                                <tr>
                                    <td class="px-5 py-4"><a href="{{ route('orders.show', $order) }}" class="font-bold text-neutral-950 hover:text-teal-800">#{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</a></td>
                                    <td class="px-5 py-4 text-neutral-600">{{ $order->cleaner?->business_name }}</td>
                                    <td class="px-5 py-4 text-neutral-600">{{ $order->customer?->name ?? 'Guest' }}</td>
                                    <td class="px-5 py-4 text-neutral-600">{{ $order->updated_at->format('M j, Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-5 py-8 text-neutral-600">No cancelled orders yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </section>
</x-layouts.app>
