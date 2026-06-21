<x-layouts.app title="Customer Dashboard - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Customer dashboard</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Track laundry orders</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">A seeded customer view of order totals, active work, completed orders, and recent cleaner activity.</p>
        </div>
    </section>

    <section class="container-shell py-10">
        <div class="grid gap-4 md:grid-cols-3">
            <x-stat-card label="Total orders" :value="$stats['total_orders']" caption="All sample customer orders" />
            <x-stat-card label="Active orders" :value="$stats['active_orders']" caption="Not completed or cancelled" />
            <x-stat-card label="Completed orders" :value="$stats['completed_orders']" caption="Ready for review history" />
        </div>

        <div class="mt-10 rounded-lg border border-neutral-200 bg-white shadow-sm">
            <div class="border-b border-neutral-200 p-5">
                <h2 class="text-xl font-black text-neutral-950">Recent orders</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-sm">
                    <thead class="bg-neutral-50 text-xs uppercase tracking-[0.16em] text-neutral-500">
                        <tr>
                            <th class="px-5 py-3">Cleaner</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-5 py-4 font-bold text-neutral-950">{{ $order->cleaner?->business_name }}</td>
                                <td class="px-5 py-4"><x-status-badge :status="$order->status" /></td>
                                <td class="px-5 py-4 font-semibold">NGN {{ number_format((float) $order->total, 2) }}</td>
                                <td class="px-5 py-4 text-neutral-600">{{ $order->created_at->format('M j, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-neutral-600">No orders yet. Run the seeder to load sample activity.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts.app>
