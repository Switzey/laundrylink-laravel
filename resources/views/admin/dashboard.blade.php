<x-layouts.app title="Admin Dashboard - LaundryLink">
    <section class="border-b border-neutral-200 bg-white">
        <div class="container-shell py-12">
            <p class="text-sm font-bold uppercase tracking-[0.22em] text-teal-700">Admin dashboard</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-neutral-950">Platform overview</h1>
            <p class="mt-4 max-w-2xl text-neutral-600">Monitor users, cleaners, orders, and pending cleaner approvals from the seeded MVP dataset.</p>
        </div>
    </section>

    <section class="container-shell space-y-10 py-10">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card label="Total users" :value="$stats['total_users']" />
            <x-stat-card label="Total cleaners" :value="$stats['total_cleaners']" />
            <x-stat-card label="Total orders" :value="$stats['total_orders']" />
            <x-stat-card label="Pending approvals" :value="$stats['pending_cleaners']" />
        </div>

        <div class="grid gap-8 xl:grid-cols-2">
            <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">Pending cleaner approvals</h2>
                </div>
                <div class="divide-y divide-neutral-200">
                    @forelse ($pendingCleaners as $cleaner)
                        <div class="p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-black text-neutral-950">{{ $cleaner->business_name }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $cleaner->city }} - {{ $cleaner->phone }}</p>
                                </div>
                                <button type="button" class="rounded-md bg-teal-700 px-3 py-2 text-xs font-bold text-white hover:bg-teal-800">Approve</button>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-neutral-600">No cleaners are waiting for approval.</div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-lg border border-neutral-200 bg-white shadow-sm">
                <div class="border-b border-neutral-200 p-5">
                    <h2 class="text-xl font-black text-neutral-950">Recent orders</h2>
                </div>
                <div class="divide-y divide-neutral-200">
                    @forelse ($recentOrders as $order)
                        <div class="p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-black text-neutral-950">Order #{{ str_pad((string) $order->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    <p class="mt-1 text-sm text-neutral-600">{{ $order->customer?->name }} with {{ $order->cleaner?->business_name }}</p>
                                </div>
                                <x-status-badge :status="$order->status" />
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-neutral-600">No recent orders yet.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </section>
</x-layouts.app>
